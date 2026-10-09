<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\Cohorts;
use Database\Seeders\Support\DemoData;
use Database\Seeders\Support\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Course materials (real PDF files), assignments and submissions, documents
 * published by the administration, and messages.
 */
class ContentSeeder extends Seeder
{
    private const FEEDBACK = [
        'Très bon travail, bien structuré.', 'Bon travail, quelques imprécisions à corriger.',
        'Travail correct, soignez davantage la présentation.', 'Démarche juste mais incomplète.',
        'Excellent, rien à redire.', 'Revoir la partie 2, le raisonnement n’est pas abouti.',
    ];

    public function run(): void
    {
        $disk = Storage::disk('public');
        $disk->deleteDirectory('lessons/demo');
        $disk->deleteDirectory('documents/demo');
        $disk->deleteDirectory('homework_submissions/demo');

        foreach (DemoData::COHORTS as $key => $cohort) {
            if (!empty($cohort['graduated'])) {
                continue;
            }
            $modules = Cohorts::modules($cohort['specialty'], $cohort['semester']);
            $this->lessons($modules);
            $this->homework($key, $modules);
        }

        $this->documents();
        $this->messages();
    }

    private function lessons($modules): void
    {
        $chapters = [
            ['Chapitre 1 : notions de base', 'Support de cours du premier chapitre.'],
            ['Série de TD n°1', 'Exercices à préparer avant la séance de travaux dirigés.'],
            ['Chapitre 2 : approfondissement', 'Suite du cours, avec exemples corrigés.'],
        ];
        foreach ($modules as $module) {
            foreach (array_slice($chapters, 0, $module->coefficient >= 3 ? 3 : 2) as $n => [$title, $description]) {
                $path = "lessons/demo/{$module->code}-" . ($n + 1) . '.pdf';
                $content = Pdf::make("{$module->name}", [
                    $title, $description,
                    'Objectifs : comprendre les concepts présentés, savoir les appliquer en travaux pratiques.',
                    'Contenu : définitions, méthodes, exemples commentés et exercices d’application.',
                ]);
                Storage::disk('public')->put($path, $content);
                $date = Carbon::create(2026, 2, 15)->addWeeks($n * 3 + $module->id % 3);
                DB::table('lessons')->insert([
                    'module_id' => $module->id, 'teacher_id' => $module->teacher_id,
                    'title' => $title, 'description' => $description,
                    'file_path' => $path, 'file_name' => DemoData::slug($module->name) . '-' . ($n + 1) . '.pdf',
                    'file_type' => 'application/pdf', 'file_size' => strlen($content),
                    'created_at' => $date, 'updated_at' => $date,
                ]);
            }
        }
    }

    /** One assignment already corrected and one open until next week, on the main modules. */
    private function homework(string $key, $modules): void
    {
        $students = Cohorts::students($key);
        $main = $modules->sortByDesc('coefficient')->take(2)->values();
        $inPerson = DemoData::COHORTS[$key]['type'] === 'apprentissage';

        foreach ($main as $i => $module) {
            $past = $i === 0;
            $due = $past ? Carbon::create(2026, 5, 14, 23, 59) : Carbon::create(2026, 10, 18, 23, 59);
            $homeworkId = DB::table('homework')->insertGetId([
                'teacher_id' => $module->teacher_id, 'module_id' => $module->id,
                'title' => $past ? "Mini-projet : {$module->name}" : "Exercices de révision — {$module->name}",
                'description' => $past
                    ? 'Réaliser le mini-projet décrit en cours et rendre un court rapport (2 à 3 pages) expliquant vos choix.'
                    : 'Traiter les exercices 1 à 4 de la dernière série de TD. Rédigez vos réponses de façon claire et justifiée.',
                'file_path' => null, 'due_date' => $due,
                'submission_type' => $inPerson ? 'in_person' : 'online',
                'created_at' => $due->copy()->subWeeks(2), 'updated_at' => $due->copy()->subWeeks(2),
            ]);

            $rows = [];
            foreach ($students as $n => $student) {
                // Past assignment: most students handed it in; open one: only a few so far
                if ($past ? $n % 7 === 6 : $n % 4 !== 0) {
                    continue;
                }
                $submitted = $due->copy()->subDays(mt_rand(0, 6))->setTime(mt_rand(9, 22), mt_rand(0, 59));
                $graded = $past && $n % 5 !== 4;
                // Two submissions out of three come with a PDF report
                $file = null;
                if ($n % 3 !== 2) {
                    $file = "homework_submissions/demo/hw{$homeworkId}-{$student->id}.pdf";
                    Storage::disk('public')->put($file, Pdf::make("Rapport - {$student->first_name} {$student->last_name}", [
                        "Devoir : {$module->name}",
                        'Introduction : présentation du sujet et des objectifs du travail demandé.',
                        'Réalisation : démarche suivie, choix techniques et difficultés rencontrées.',
                        'Résultats : tests effectués et captures commentées.',
                        'Conclusion : bilan du travail et améliorations possibles.',
                    ]));
                }
                $rows[] = [
                    'homework_id' => $homeworkId, 'student_id' => $student->id,
                    'submission_text' => $file
                        ? "Bonjour,
Veuillez trouver ci-joint mon rapport. J’ai traité toutes les questions demandées.
Cordialement."
                        : "Bonjour,
Voici mes réponses :
1. Analyse du besoin et des données.
2. Proposition de solution et justification.
3. Tests réalisés et résultats obtenus.",
                    'file_path' => $file,
                    'grade' => $graded ? mt_rand(22, 38) / 2 : null,
                    'feedback' => $graded ? self::FEEDBACK[$n % count(self::FEEDBACK)] : null,
                    'status' => $graded ? 'graded' : 'submitted',
                    'submitted_at' => $submitted,
                    'created_at' => $submitted, 'updated_at' => $graded ? $due->copy()->addDays(5) : $submitted,
                ];
            }
            DB::table('homework_submissions')->insert($rows);
        }
    }

    private function documents(): void
    {
        $adminId = DB::table('administrations')->value('id');
        $docs = [
            ['Calendrier des examens — semestre de février 2026', 'Dates des contrôles et des examens finaux par spécialité.', 'Examens', 'all_students', '2026-02-20'],
            ['Planning de la session de rattrapage', 'Rattrapage des modules non validés : septembre et octobre 2026.', 'Examens', 'all_students', '2026-09-10'],
            ['Règlement intérieur de l’institut', 'Assiduité, discipline, évaluations et délibérations.', 'Règlement', 'all_students', '2025-09-01'],
            ['Guide de saisie des notes sur la plateforme', 'Créer une épreuve, saisir les notes, soumettre à l’administration.', 'Guides', 'all_teachers', '2026-02-05'],
            ['Calendrier des conseils de délibération', 'Dates des délibérations semestrielles par spécialité.', 'Pédagogie', 'all_teachers', '2026-06-01'],
            ['Avis d’ouverture — Session Septembre 2026', 'Spécialités ouvertes, modes de formation et pièces du dossier d’inscription.', 'Inscriptions', 'all_students', '2026-08-25'],
        ];
        foreach ($docs as $i => [$title, $description, $category, $target, $date]) {
            $path = 'documents/demo/document-' . ($i + 1) . '.pdf';
            Storage::disk('public')->put($path, Pdf::make($title, [$description, 'Document officiel de l’administration.']));
            DB::table('documents')->insert([
                'administration_id' => $adminId, 'title' => $title, 'description' => $description,
                'category' => $category, 'file_path' => $path,
                'file_name' => DemoData::slug(mb_substr($title, 0, 40)) . '.pdf',
                'is_public' => false, 'target_type' => $target, 'session_id' => null, 'specialty_ids' => null,
                'valid_until' => null, 'created_at' => $date . ' 10:00:00', 'updated_at' => $date . ' 10:00:00',
            ]);
        }
    }

    private function messages(): void
    {
        $adminUserId = DB::table('users')->where('role', 'administration')->value('id');
        $saidi = DB::table('users')->where('email', 'leila.saidi@insfp.dz')->value('id');
        $messages = [
            ['all', null, 'Ouverture de la Session Septembre 2026',
                "La nouvelle session de formation ouvrira prochainement. Les emplois du temps du nouveau semestre seront publiés sur la plateforme dès l’activation de la session.", '2026-09-28 09:00:00'],
            ['students', null, 'Session de rattrapage',
                "Les stagiaires dont la moyenne semestrielle est inférieure à 10 passent le rattrapage des modules non validés. Le planning est disponible dans la rubrique Documents.", '2026-09-10 11:30:00'],
            ['teachers', null, 'Saisie des notes de rattrapage',
                "Merci de saisir les notes de rattrapage et de soumettre les épreuves à l’administration avant la tenue des délibérations.", '2026-10-04 08:45:00'],
            ['students', null, 'Rappel : assiduité',
                "Un taux d’assiduité inférieur à 75 % expose à des sanctions. Consultez votre suivi des présences dans votre espace.", '2026-05-03 10:00:00'],
            ['individual', $saidi, 'Rattrapage ASR — semestre 2',
                "Bonjour Madame Saidi, deux stagiaires d’ASR sont concernés par le rattrapage en Administration Linux et Windows Server. Les épreuves sont créées, il reste à saisir les notes.", '2026-10-06 14:10:00'],
        ];
        foreach ($messages as [$type, $recipient, $subject, $body, $date]) {
            $count = match ($type) {
                'all' => DB::table('users')->where('role', '!=', 'administration')->count(),
                'students' => DB::table('users')->where('role', 'student')->count(),
                'teachers' => DB::table('users')->where('role', 'teacher')->count(),
                default => 1,
            };
            DB::table('messages')->insert([
                'sender_id' => $adminUserId, 'recipient_id' => $recipient, 'subject' => $subject, 'body' => $body,
                'recipient_type' => $type, 'is_read' => false, 'recipient_count' => $count,
                'recipient_filter' => null, 'read_at' => null, 'created_at' => $date, 'updated_at' => $date,
            ]);
        }
    }
}
