p = 'docs_section.js'; s = open(p, encoding='utf-8').read()

# remarque facultative sous la fiche
a = "    new Table({ width: { size: W, type: WidthType.DXA }, columnWidths: COLS, rows }),\n    P('', { after: 80 }),"
assert a in s
s = s.replace(a, "    new Table({ width: { size: W, type: WidthType.DXA }, columnWidths: COLS, rows }),\n    ...(f.remarque ? [note(f.remarque, 'info')] : [P('', { after: 80 })]),")

# remarques sur D3 (PV après rattrapage) et D4 (liste par groupe)
a = "    code: 'D3_PV_DELIB',"
s = s.replace(a, "    remarque: \"Le PV des délibérations **après rattrapage** a exactement la même forme : seules les lignes des stagiaires ayant passé le rattrapage sont mises à jour (nouvelles moyennes et nouvelle décision). Il n'a donc pas fait l'objet d'une fiche séparée.\",\n" + a)
a = "    code: 'D4_LISTE_ORIENT',"
s = s.replace(a, "    remarque: \"La **liste des stagiaires par groupe** utilisée par la scolarité a la même structure (n°, nom, prénom, spécialité, mode de formation, plus le groupe) ; elle n'a donc pas fait l'objet d'une fiche séparée.\",\n" + a)

new_docs = r'''
  {
    code: 'D6_ATTEST', titre: 'Attestation de stage (شهادة تربص)',
    role: "Attester que le stagiaire suit une formation à l'institut : identité, spécialité, mode de formation, diplôme préparé, période de formation, année et semestre en cours ; document remis au stagiaire à sa demande.",
    emetteur: "Direction de l'institut (service de la scolarité)", recepteur: 'Stagiaire', nature: 'Externe', exemplaires: '01',
    rows: [
      ['Numéro du document', 'AN', '20 car', 'NU', "Généré à l'édition"],
      ['Nom et prénom', 'A', '200 car', 'PU', 'students.last_name, first_name'],
      ['Date de naissance', 'D', '10 car', 'PU', 'students.date_of_birth'],
      ['Lieu de naissance', 'A', '100 car', 'PU', 'Absente de la base (écart 10)'],
      ['Adresse', 'AN', '500 car', 'PU', 'students.address'],
      ["Numéro d'inscription", 'AN', '50 car', 'PU', 'students.registration_number'],
      ['Spécialité', 'A', '255 car', 'PU', 'specialties.name'],
      ['Mode de formation (cours du soir)', 'A', '—', 'PU', 'students.study_mode'],
      ['Diplôme préparé (BTS)', 'A', '100 car', 'PU', 'Absente de la base (écart 5)'],
      ['Période de formation (du … au …)', 'D', '10 car', 'PU', 'sessions.start_date, end_date'],
      ['Année de formation (2025/2026)', 'AN', '9 car', 'PC', 'Calculée (session)'],
      ['Numéro du semestre', 'N', '1 car', 'PU', 'students.current_semester'],
      ['Période du semestre (du … au …)', 'D', '10 car', 'PU', 'Absente de la base (écart 6)'],
      ["Lieu et date d'établissement", 'D', '10 car', 'NU', "Générée à l'édition"],
      ['Établie par (agent)', 'A', '100 car', 'NU', "Utilisateur connecté (administrations)"],
    ],
  },
  {
    code: 'D7_PV_NOTES', titre: 'Document de transfert des notes (وثيقة نقل النقاط)',
    role: "Transmettre au bureau des examens les notes d'un module pour un groupe : contrôles et examen, puis moyennes avant et après rattrapage ; ce document alimente le PV de délibération (document 3).",
    emetteur: 'Enseignant du module', recepteur: 'Bureau des examens', nature: 'Interne', exemplaires: '01 (2 pages)',
    rows: [
      ['Branche (mode et groupe : cours du soir 2)', 'AN', '50 car', 'PU', 'students.study_mode, students.group'],
      ['Spécialité', 'A', '255 car', 'PU', 'specialties.name'],
      ['Matière (module)', 'AN', '255 car', 'PU', 'modules.name'],
      ["Nom de l'enseignant", 'A', '100 car', 'PU', 'teachers.last_name, first_name'],
      ["Grade de l'enseignant (PSFEP)", 'AN', '100 car', 'PU', 'Absente de la base (écart 11)'],
      ['Coefficient', 'N', '3 car', 'PU', 'modules.coefficient'],
      ['Note éliminatoire', 'N', '5 car', 'PU', 'Absente de la base (écart 4)'],
      ['N°', 'N', '3 car', 'NU', 'Calculée'],
      ['Nom, prénom', 'A', '200 car', 'PU', 'students.last_name, first_name'],
      ['Date de naissance', 'D', '10 car', 'PU', 'students.date_of_birth'],
      ['Contrôle 1 (/20), contrôle 2 (/20)', 'N', '5 car', 'PU', "grades.grade (type contrôle)"],
      ['Examen (/40)', 'N', '5 car', 'PU', "grades.grade × 2 (type examen)"],
      ['Mention « ABS » (absent à l\'épreuve)', 'A', '3 car', 'PU', 'Absente de la base (écart 12)'],
      ['Moyenne avant rattrapage', 'N', '5 car', 'PC', 'Calculée (règle R1)'],
      ['Note de rattrapage', 'N', '5 car', 'PU', 'Absente de la base (écart 3)'],
      ['Moyenne après rattrapage', 'N', '5 car', 'PC', 'Calculée'],
      ['Observation', 'AN', '255 car', 'PU', 'Absente de la base (écart 12)'],
      ['Numéro de page (1/2)', 'N', '3 car', 'NU', 'Non retenue'],
    ],
  },
  {
    code: 'D8_ABSENCES', titre: "Feuille d'absences (feuille d'appel)",
    role: "Relever, pour une séance, la présence de chaque stagiaire du groupe (présent, absent, en retard, excusé) ; les feuilles sont remises à la scolarité, qui calcule le nombre d'absences de chaque stagiaire.",
    emetteur: 'Enseignant', recepteur: 'Service de la scolarité', nature: 'Interne', exemplaires: '01 par séance',
    remarque: "Cette feuille ne nous a pas été remise : sa fiche a été établie à partir du modèle de feuille d'appel couramment utilisé dans l'institut (rubriques recueillies auprès des enseignants et de la scolarité).",
    rows: [
      ['Spécialité', 'A', '255 car', 'PU', 'specialties.name'],
      ['Semestre', 'N', '1 car', 'PU', 'schedules.semester'],
      ['Groupe', 'AN', '10 car', 'PU', 'schedules.group'],
      ["Mode d'étude", 'A', '—', 'PU', 'schedules.study_mode'],
      ['Module', 'AN', '255 car', 'PU', 'modules.name'],
      ["Nom de l'enseignant", 'A', '100 car', 'PU', 'teachers.last_name'],
      ['Date de la séance', 'D', '10 car', 'PU', 'attendances.attendance_date'],
      ['Horaire (début – fin)', 'H', '5 car', 'PU', 'schedules.start_time, end_time'],
      ['Salle', 'AN', '50 car', 'PU', 'schedules.classroom'],
      ['N°', 'N', '3 car', 'NU', 'Calculée'],
      ["Numéro d'inscription", 'AN', '50 car', 'PU', 'students.registration_number'],
      ['Nom, prénom', 'A', '200 car', 'PU', 'students.last_name, first_name'],
      ['Présence (P / A / R / E)', 'A', '1 car', 'PU', 'attendances.status'],
      ['Motif / observation', 'AN', '255 car', 'PU', 'attendances.notes'],
      ['Nombre de présents / absents', 'N', '3 car', 'PC', 'Calculée (attendances)'],
      ["Signature de l'enseignant", '—', '—', 'NU', "Non retenue (saisie authentifiée)"],
    ],
  },
'''
a = "\n];\n\nfunction etudeDocuments"
assert a in s
s = s.replace(a, "\n" + new_docs.rstrip('\n') + "\n];\n\nfunction etudeDocuments", 1)

# texte d'introduction et tableau des documents
s = s.replace("Nous avons recueilli auprès de l'institut cinq documents réellement utilisés.",
              "Nous avons étudié huit documents du service : sept documents réels recueillis auprès de l'institut et une feuille d'absences établie à partir du modèle couramment utilisé.")
a = "      ['D5', 'Relevé de notes (كشف النقاط)'"
if a in s:
  i = s.index(a); j = s.index('\n', i) + 1
  s = s[:j] + """      ['D6', 'Attestation de stage (شهادة تربص)', 'Attester la formation suivie par le stagiaire', 'Direction → stagiaire', 'Imprimé, signé et cacheté'],
      ['D7', 'Document de transfert des notes (وثيقة نقل النقاط)', "Transmettre les notes d'un module pour un groupe", 'Enseignant → bureau des examens', 'Imprimé, rempli à la main'],
      ['D8', "Feuille d'absences", "Relever la présence des stagiaires à une séance", 'Enseignant → scolarité', 'Papier'],
""" + s[j:]

# titres numérotés dynamiquement
s = s.replace("new TextRun('3.6 Règles de calcul relevées dans les documents')", "new TextRun(`3.${D.length + 1} Règles de calcul relevées dans les documents`)")
s = s.replace("H3('3.7 Synthèse : écarts entre les documents et la base de données'),", "H3(`3.${D.length + 2} Synthèse : écarts entre les documents et la base de données`),")
s = s.replace("Le recoupement des valeurs des documents 3 et 5", "Le recoupement des valeurs des documents 3, 5 et 7")
s = s.replace("['R1 — Moyenne du module', \"(contrôle 1 + contrôle 2 + 2 × examen) / 4 ; l'examen noté sur 20 est reporté sur 40\", 'Doc. 5 : (15 + 15 + 32) / 4 = 15,50'],",
              "['R1 — Moyenne du module', \"(contrôle 1 + contrôle 2 + 2 × examen) / 4 ; l'examen noté sur 20 est reporté sur 40\", 'Doc. 5 : (15 + 15 + 32) / 4 = 15,50 ; doc. 7 : colonnes /20, /20 et /40'],")

# écarts supplémentaires
a = "      ['9', 'Numéro et date du relevé', '5', \"Générés lors de l'édition du relevé en PDF (perspective)\"],"
assert a in s
s = s.replace(a, "      ['9', 'Numéro et date du relevé ou de l\\'attestation', '5, 6', \"Générés lors de l'édition du document en PDF (perspective)\"],\n"
              "      ['10', 'Lieu de naissance du stagiaire non stocké', '6', 'Ajouter students.birth_place'],\n"
              "      ['11', \"Grade de l'enseignant non stocké\", '7', 'Ajouter teachers.grade'],\n"
              "      ['12', \"Absence à une épreuve (ABS) et observation non prévues dans les notes\", '7', 'Ajouter grades.is_absent et grades.remark'],\n"
              "      ['13', \"Session des cours du soir ouverte en octobre (formation du 08/10/2024 au 07/04/2027)\", '6', \"Vérifier auprès de l'institut les mois d'ouverture des sessions du soir\"],")
i = s.index("    note(\"[[À COMPLÉTER : ajouter les autres documents"); j = s.index('\n', i) + 1
s = s[:i] + s[j:]
open(p, 'w', encoding='utf-8').write(s)
print('ok')
