p = 'content3.js'; s = open(p, encoding='utf-8').read()
a = "    H3('3.1 Création de la base de données'),"
assert a in s
s = s.replace(a, """    P("Le framework Laravel impose des conventions de nommage en anglais pour les tables et les colonnes. Le tableau suivant établit la correspondance entre les tables du modèle objet relationnel (chapitre III) et celles de l'implémentation."),
    ...T('Correspondance entre le MOR et les tables de la base', ['Table du MOR', "Table de l'implémentation"], [
      ['utilisateur', 'users'], ['stagiaire', 'students'], ['enseignant', 'teachers'], ['administration', 'administrations'],
      ['filiere', 'filieres'], ['specialite', 'specialties'], ['module', 'modules'], ['affectation', 'teacher_module'],
      ['session', 'sessions'], ['semestre_session', 'session_semesters'], ['offre_formation', 'session_specialties'],
      ['numero_inscription', 'registration_numbers'], ['seance', 'schedules'], ['publication_edt', 'schedule_statuses'],
      ['presence', 'attendances'], ['cours', 'lessons'], ['devoir', 'homework'], ['remise_devoir', 'homework_submissions'],
      ['examen', 'exams'], ['note', 'grades'], ['note_rattrapage', 'retake_grades'], ['deliberation', 'deliberations'],
      ['revision_passage', 'advancement_reviews'], ['message', 'messages'], ['lecture_message', 'message_reads'],
      ['notification', 'notifications'], ['document', 'documents'], ['groupe_encadrement', 'encadrement_groups'],
      ['membre_encadrement', 'encadrement_students'], ['rendez_vous', 'encadrement_appointments'],
    ], [50, 50], { size: 17 }),
""" + a)
open(p, 'w', encoding='utf-8').write(s)
print('ok')
