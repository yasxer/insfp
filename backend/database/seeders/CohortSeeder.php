<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\DemoData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Students of every cohort with their registration numbers, plus the next
 * intake: a few registrations waiting for approval and unused numbers.
 */
class CohortSeeder extends Seeder
{
    private const TYPE_CHARS = ['presential' => 'P', 'cours_soir' => 'C', 'apprentissage' => 'A'];

    private const STREETS = [
        'Cité 200 Logements, Bt 4', 'Rue des Frères Bouadou', 'Cité El Amel, N° 17', 'Lotissement El Wiam, Villa 9',
        'Rue Ahmed Zabana', 'Cité 1er Novembre, Bt C', 'Boulevard de l’ALN, N° 41', 'Cité Ennasr, Bt 2',
        'Rue Larbi Ben M’hidi', 'Cité 320 Logements, Bt 11', 'Rue Didouche Mourad, N° 8', 'Hai El Djamaa',
    ];

    private string $password;
    private array $usedEmails = [];
    private array $sequences = [];
    private int $phone = 0;

    public function run(): void
    {
        $this->password = Hash::make(DemoData::PASSWORD);
        $pool = $this->namePool();

        foreach (DemoData::COHORTS as $key => $cohort) {
            [$month, $year] = $cohort['session'];
            $sessionId = InstitutionSeeder::sessionId($month, $year);
            $specialtyId = InstitutionSeeder::specialtyId($cohort['specialty']);
            $sessionSpecialtyId = $this->sessionSpecialtyId($sessionId, $specialtyId, $cohort['type']);
            $intake = Carbon::create($year, $month, 1);
            $groups = $cohort['groups'] ?? ['G1'];

            // Scenario students first, then ordinary ones
            $names = array_map(fn ($p) => [$p[0], $p[1]], DemoData::PROFILES[$key] ?? []);
            while (count($names) < $cohort['size']) {
                $names[] = array_shift($pool);
            }

            foreach ($names as $n => [$first, $last]) {
                $this->createStudent([
                    'first' => $first, 'last' => $last,
                    'session_id' => $sessionId, 'month' => $month, 'year' => $year,
                    'specialty_id' => $specialtyId, 'session_specialty_id' => $sessionSpecialtyId,
                    'type' => $cohort['type'], 'semester' => $cohort['semester'],
                    'group' => $groups[$n % count($groups)],
                    'graduated' => $cohort['graduated'] ?? false,
                    'approved' => true, 'joined' => $intake,
                ]);
            }
        }

        // ── Next intake: Session Septembre 2026 ──────────────────────
        $nextSessionId = InstitutionSeeder::sessionId(9, 2026);
        $pending = [['DWM', 'presential'], ['DWM', 'presential'], ['ASR', 'presential'], ['BDD', 'presential']];
        foreach ($pending as [$code, $type]) {
            [$first, $last] = array_shift($pool);
            $specialtyId = InstitutionSeeder::specialtyId($code);
            $this->createStudent([
                'first' => $first, 'last' => $last,
                'session_id' => $nextSessionId, 'month' => 9, 'year' => 2026,
                'specialty_id' => $specialtyId,
                'session_specialty_id' => $this->sessionSpecialtyId($nextSessionId, $specialtyId, $type),
                'type' => $type, 'semester' => 1, 'group' => null, 'graduated' => false,
                'approved' => false, 'joined' => Carbon::create(2026, 10, 3 + count($this->usedEmails) % 5, 10),
                'incomplete' => true,
            ]);
        }

        // Numbers issued by the administration and not used yet
        foreach (DemoData::NEXT_INTAKE as [$code, $type]) {
            for ($i = 0; $i < 2; $i++) {
                $this->registrationNumber($nextSessionId, 9, 2026, InstitutionSeeder::specialtyId($code), $type, null);
            }
        }
    }

    private function createStudent(array $s): void
    {
        $now = now();
        $semester = $s['semester'];
        $emailBase = DemoData::slug($s['first']) . '.' . DemoData::slug($s['last']);
        $email = $emailBase . '@stagiaire.insfp.dz';
        for ($k = 2; isset($this->usedEmails[$email]); $k++) {
            $email = "$emailBase$k@stagiaire.insfp.dz";
        }
        $this->usedEmails[$email] = true;

        $number = $this->registrationNumber(
            $s['session_id'], $s['month'], $s['year'], $s['specialty_id'], $s['type'], $s['joined']
        );

        $userId = DB::table('users')->insertGetId([
            'email' => $email, 'first_name' => $s['first'], 'last_name' => $s['last'],
            'phone' => '07' . str_pad((string) (71234567 + 3917 * ++$this->phone), 8, '0', STR_PAD_LEFT),
            'password' => $this->password, 'role' => 'student', 'is_approved' => $s['approved'],
            'created_at' => $s['joined'], 'updated_at' => $now,
        ]);

        $graduated = $s['graduated'];
        DB::table('students')->insert([
            'user_id' => $userId,
            'specialty_id' => $s['specialty_id'],
            'session_specialty_id' => $s['session_specialty_id'],
            'registration_number' => $number,
            'first_name' => $s['first'], 'last_name' => $s['last'],
            // Pending registrations have not completed their profile yet
            'date_of_birth' => empty($s['incomplete'])
                ? Carbon::create($s['year'] - mt_rand(18, 22), mt_rand(1, 12), mt_rand(1, 28))->toDateString()
                : null,
            'address' => empty($s['incomplete']) ? self::STREETS[array_rand(self::STREETS)] . ', Horrimet' : null,
            'study_mode' => DemoData::STUDY_MODES[$s['type']],
            'current_semester' => $semester,
            'group' => $s['group'],
            'years_enrolled' => (int) ceil($semester / 2),
            'is_graduated' => $graduated,
            'graduation_year' => $graduated ? 2026 : null,
            'graduation_semester' => $graduated ? 5 : null,
            'created_at' => $s['joined'], 'updated_at' => $now,
        ]);
    }

    /** NNNN + 1/2 (Février/Septembre) + YY + P/C/A + 16 (wilaya) + 47 (INSFP), as the admin generator does. */
    private function registrationNumber(int $sessionId, int $month, int $year, int $specialtyId, string $type, ?Carbon $usedAt): string
    {
        $suffix = ($month === 2 ? '1' : '2') . substr((string) $year, -2) . self::TYPE_CHARS[$type] . '1647';
        $this->sequences[$suffix] = ($this->sequences[$suffix] ?? 0) + 1;
        $number = str_pad((string) $this->sequences[$suffix], 4, '0', STR_PAD_LEFT) . $suffix;

        DB::table('registration_numbers')->insert([
            'number' => $number, 'is_used' => $usedAt !== null, 'specialty_id' => $specialtyId,
            'session_id' => $sessionId, 'academic_year' => $year . '-' . ($year + 1),
            'used_at' => $usedAt,
            'created_at' => Carbon::create($year, $month, 1)->subMonth(), 'updated_at' => now(),
        ]);

        return $number;
    }

    private function sessionSpecialtyId(int $sessionId, int $specialtyId, string $type): int
    {
        return (int) DB::table('session_specialties')
            ->where(['session_id' => $sessionId, 'specialty_id' => $specialtyId, 'study_type' => $type])
            ->value('id');
    }

    /** Shuffled unique first/last name pairs. */
    private function namePool(): array
    {
        $pairs = [];
        foreach (DemoData::FIRST_NAMES as $f) {
            foreach (DemoData::LAST_NAMES as $l) {
                $pairs[] = [$f, $l];
            }
        }
        shuffle($pairs);

        $pool = [];
        $usedFirst = [];
        $usedLast = [];
        // Prefer pairs whose first and last names were not used yet, for variety
        foreach ($pairs as [$f, $l]) {
            if (isset($usedFirst[$f]) || isset($usedLast[$l])) {
                continue;
            }
            $pool[] = [$f, $l];
            $usedFirst[$f] = $usedLast[$l] = true;
        }
        foreach ($pairs as $p) {
            if (count($pool) >= 120) {
                break;
            }
            if (!in_array($p, $pool, true)) {
                $pool[] = $p;
            }
        }
        return $pool;
    }
}
