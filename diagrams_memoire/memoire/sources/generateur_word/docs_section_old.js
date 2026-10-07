// Étude des documents réels de l'institut (chapitre II) — format « fiche description du document »
const L = require('./lib');
const { P, H3, T, figure, note, tcaption, runs } = L;
const { Paragraph, TextRun, Table, TableRow, TableCell, WidthType, ShadingType, BorderStyle, AlignmentType, VerticalAlign } = L.d;

const W = L.CONTENT_W;
const COLS = [38, 9, 12, 10, 31].map((p) => Math.round((W * p) / 100));
COLS[4] = W - COLS.slice(0, 4).reduce((a, b) => a + b, 0);
const B = { style: BorderStyle.SINGLE, size: 4, color: L.C.grid };
const borders = { top: B, bottom: B, left: B, right: B };
const NONE = { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' };

const cell = (children, o = {}) => new TableCell({
  children, columnSpan: o.span, width: { size: o.w, type: WidthType.DXA },
  shading: o.fill ? { type: ShadingType.CLEAR, color: 'auto', fill: o.fill } : undefined,
  borders: o.borders || borders, verticalAlign: VerticalAlign.CENTER,
  margins: { top: 50, bottom: 50, left: 110, right: 110 },
});
const par = (text, o = {}) => new Paragraph({ children: runs(text, { size: o.size || 20, color: o.color || L.C.text, bold: o.bold }),
  alignment: o.align || AlignmentType.LEFT, spacing: { after: o.after ?? 60, line: 260 } });

// Fiche : { code, titre, role, emetteur, recepteur, nature, exemplaires, rows: [[information, type, taille, utilite, observation]] }
function fiche(num, f) {
  const lab = (k, v) => par(`**${k} :** ${v}`, { size: 19 });
  const caract = [
    par('LES CARACTÉRISTIQUES DU DOCUMENT', { size: 16, bold: true, color: L.C.gold, after: 100 }),
    lab('Code du document', f.code),
    lab('Rôle', f.role),
    lab('Émetteur', f.emetteur),
    lab('Récepteur', f.recepteur),
    lab('Nature', f.nature),
    lab("Nombre d'exemplaires", f.exemplaires),
  ];
  const head = ['Information', 'Type', 'Taille', 'Utilité', 'Observation'];
  const CENTER = [false, true, true, true, false];
  const rows = [
    new TableRow({ cantSplit: true, children: [cell([par(`Fiche description du document : **${f.titre}**`, { size: 21, color: 'FFFFFF' })], { span: 5, w: W, fill: L.C.head })] }),
    new TableRow({ cantSplit: true, children: [cell(caract, { span: 5, w: W, fill: L.C.light })] }),
    new TableRow({ children: head.map((h, i) => cell([par(`**${h}**`, { size: 18, color: L.C.ink, align: CENTER[i] ? AlignmentType.CENTER : AlignmentType.LEFT })], { w: COLS[i], fill: 'D5E9ED' })) }),
    ...f.rows.map((r, k) => new TableRow({ cantSplit: true, children: r.map((v, i) => cell([par(v, {
      size: 18, color: i === 4 && /Absente|Calculée|Non retenue|Hors|Généré/.test(v) ? 'B07A10' : L.C.text,
      align: CENTER[i] ? AlignmentType.CENTER : AlignmentType.LEFT, after: 20 })], {
      w: COLS[i], fill: k % 2 ? 'F3F7FB' : 'FFFFFF' })) })),
  ];
  return [
    tcaption(`Fiche description du document ${f.code}`),
    new Table({ width: { size: W, type: WidthType.DXA }, columnWidths: COLS, rows }),
    ...(f.remarque ? [note(f.remarque, 'info')] : [P('', { after: 80 })]),
  ];
}

const D = [
  {
    img: ['D1_planning_examens.png', 'planning des examens semestriels (cours du soir)', 520],
    code: 'D1_PLAN_EXAM', titre: 'Planning des examens semestriels',
    role: "Programmer les examens de fin de semestre : date, horaire, module et enseignant surveillant pour chaque spécialité, semestre et groupe des cours du soir ; informer les enseignants et les stagiaires.",
    emetteur: 'Service pédagogique', recepteur: 'Enseignants et stagiaires (affichage)', nature: 'Interne', exemplaires: '01 (affiché)',
    rows: [
      ['Journée (date de l\'examen)', 'D', '10 car', 'PU', 'exams.exam_date'],
      ['Horaire (début – fin)', 'H', '5 car', 'PU', 'exams.exam_date, duration_minutes'],
      ['Spécialité (SDWM, SASRI)', 'AN', '50 car', 'PU', 'specialties.code'],
      ['Semestre (S1 … S4)', 'N', '1 car', 'PU', 'exams.semester'],
      ['Groupe (A, B)', 'AN', '10 car', 'PU', 'exams.group'],
      ['Intitulé du module', 'AN', '255 car', 'PU', 'modules.name'],
      ['Nom de l\'enseignant', 'A', '100 car', 'PU', 'teachers.last_name'],
      ['Mode d\'étude (cours du soir)', 'A', '—', 'PU', 'specialties.study_mode'],
      ['Nombre entre parenthèses', 'N', '2 car', 'NU', 'Non retenue'],
    ],
  },
  {
    img: ['D2_emploi_du_temps.png', 'emploi du temps des cours du soir', 560],
    code: 'D2_EDT', titre: 'Emploi du temps',
    role: "Répartir les séances hebdomadaires de chaque spécialité, semestre et groupe : jour, horaire, module, enseignant et salle.",
    emetteur: 'Service pédagogique', recepteur: 'Enseignants et stagiaires', nature: 'Interne', exemplaires: '01 (affiché)',
    rows: [
      ['Journée (samedi … jeudi)', 'A', '10 car', 'PU', 'schedules.day'],
      ['Horaire (début – fin)', 'H', '5 car', 'PU', 'schedules.start_time, end_time'],
      ['Spécialité', 'AN', '50 car', 'PU', 'specialties.code'],
      ['Semestre', 'N', '1 car', 'PU', 'schedules.semester'],
      ['Groupe (G1, G2)', 'AN', '10 car', 'PU', 'schedules.group'],
      ['Code du module (MQ3, MC10…)', 'AN', '50 car', 'PU', 'modules.code'],
      ['Intitulé du module', 'AN', '255 car', 'PU', 'modules.name'],
      ['Nom de l\'enseignant', 'A', '100 car', 'PU', 'teachers.last_name'],
      ['Salle (LABO 03)', 'AN', '50 car', 'PU', 'schedules.classroom'],
      ['Période (été 2024)', 'AN', '9 car', 'PU', 'schedules.academic_year'],
      ['Mention « 1/15 »', 'AN', '4 car', 'NU', 'Non retenue'],
    ],
  },
  {
    img: ['D3_pv_deliberation.png', 'résultats des délibérations avant rattrapage (données personnelles masquées)', 480],
    remarque: "Le PV des délibérations **après rattrapage** a exactement la même forme : seules les lignes des stagiaires ayant passé le rattrapage sont mises à jour (nouvelles moyennes et nouvelle décision). Il n'a donc pas fait l'objet d'une fiche séparée.",
    code: 'D3_PV_DELIB', titre: 'Résultats des délibérations avant rattrapage',
    role: "Présenter, pour un groupe et un semestre, la moyenne de chaque stagiaire dans chaque module, le total, la moyenne générale et la décision (admis, rattrapage, abandon) ; servir de procès-verbal signé par les formateurs et la direction.",
    emetteur: 'Formateurs, professeur principal et responsable pédagogique', recepteur: "Directeur de l'institut", nature: 'Interne', exemplaires: '01',
    rows: [
      ['Spécialité (الفرع)', 'A', '255 car', 'PU', 'specialties.name'],
      ['Semestre (السداسي)', 'N', '1 car', 'PU', 'deliberations.semester'],
      ['Groupe', 'AN', '10 car', 'PU', 'students.group'],
      ['N° d\'ordre', 'N', '3 car', 'NU', 'Calculée'],
      ["Numéro d'inscription", 'AN', '50 car', 'PU', 'students.registration_number'],
      ['Nom', 'A', '100 car', 'PU', 'students.last_name'],
      ['Prénom', 'A', '100 car', 'PU', 'students.first_name'],
      ['Intitulé du module', 'AN', '255 car', 'PU', 'modules.name'],
      ['Coefficient (مع)', 'N', '3 car', 'PU', 'modules.coefficient'],
      ['Note éliminatoire (ن إ)', 'N', '5 car', 'PU', 'Absente de la base (écart 4)'],
      ['Moyenne du module', 'N', '5 car', 'PC', 'Calculée (règle R1)'],
      ['Total (المجموع)', 'N', '6 car', 'PC', 'Calculée (règle R2)'],
      ['Moyenne (المعدل)', 'N', '5 car', 'PU', 'deliberations.average'],
      ['Décision (القرار)', 'A', '15 car', 'PU', 'deliberations.result (écart 2)'],
      ['Nom du formateur de chaque module', 'A', '100 car', 'PU', 'teacher_module'],
    ],
  },
  {
    img: ['D4_liste_nominative.png', 'liste nominative des stagiaires orientés (noms masqués)', 520],
    remarque: "La **liste des stagiaires par groupe** utilisée par la scolarité a la même structure (n°, nom, prénom, spécialité, mode de formation, plus le groupe) ; elle n'a donc pas fait l'objet d'une fiche séparée.",
    code: 'D4_LISTE_ORIENT', titre: 'Liste nominative des stagiaires orientés',
    role: "Transmettre à un centre de formation la liste des stagiaires d'une spécialité et d'un mode de formation qui y sont orientés.",
    emetteur: "Institut (direction)", recepteur: "Centre de formation professionnelle et de l'apprentissage (CFPA)", nature: 'Externe', exemplaires: '02',
    rows: [
      ["Établissement d'accueil", 'AN', '100 car', 'NU', 'Hors périmètre (écart 8)'],
      ['Mode de formation (حضوري)', 'A', '—', 'PU', 'students.study_mode'],
      ['Spécialité', 'A', '255 car', 'PU', 'specialties.name'],
      ['N°', 'N', '3 car', 'NU', 'Calculée'],
      ['Nom (اللقب)', 'A', '100 car', 'PU', 'students.last_name'],
      ['Prénom (الإسم)', 'A', '100 car', 'PU', 'students.first_name'],
    ],
  },
  {
    img: ['D5_releve_notes.png', 'relevé de notes (données personnelles masquées)', 620],
    code: 'D5_RELEVE', titre: 'Relevé de notes',
    role: "Communiquer au stagiaire, pour un semestre, ses notes de contrôle et d'examen, ses moyennes par module, sa moyenne semestrielle, la décision du jury et son nombre d'absences.",
    emetteur: "Direction de l'institut", recepteur: 'Stagiaire', nature: 'Externe', exemplaires: '01',
    rows: [
      ['Numéro du relevé', 'AN', '20 car', 'NU', "Généré à l'édition"],
      ['Nom et prénom', 'A', '200 car', 'PU', 'students.last_name, first_name'],
      ["Numéro d'inscription", 'AN', '50 car', 'PU', 'students.registration_number'],
      ['Spécialité', 'A', '255 car', 'PU', 'specialties.name'],
      ['Niveau', 'N', '1 car', 'PU', 'Absente de la base (écart 5)'],
      ['Diplôme', 'A', '100 car', 'PU', 'Absente de la base (écart 5)'],
      ['Période de formation (du … au …)', 'D', '10 car', 'PU', 'sessions.start_date, end_date'],
      ['Semestre', 'N', '1 car', 'PU', 'deliberations.semester'],
      ['Période du semestre (du … au …)', 'D', '10 car', 'PU', 'Absente de la base (écart 6)'],
      ['Matière (module)', 'AN', '255 car', 'PU', 'modules.name'],
      ['Contrôle 1, contrôle 2 (/20)', 'N', '5 car', 'PU', "grades.grade (type contrôle)"],
      ['Examen final (/40)', 'N', '5 car', 'PU', "grades.grade × 2 (type examen)"],
      ['Moyenne du module avant rattrapage', 'N', '5 car', 'PC', 'Calculée (règle R1)'],
      ['Note de rattrapage', 'N', '5 car', 'PU', 'Absente de la base (écart 3)'],
      ['Moyenne finale du module', 'N', '5 car', 'PC', 'Calculée'],
      ['Coefficient', 'N', '3 car', 'PU', 'modules.coefficient'],
      ['Note éliminatoire', 'N', '5 car', 'PU', 'Absente de la base (écart 4)'],
      ['Total avant / après rattrapage', 'N', '6 car', 'PC', 'Calculée (règle R2)'],
      ['Moyenne du semestre', 'N', '5 car', 'PU', 'deliberations.average'],
      ['Décision du jury', 'A', '15 car', 'PU', 'deliberations.result'],
      ['Observation du jury', 'AN', '255 car', 'PU', 'deliberations.observations'],
      ["Nombre d'absences, dont justifiées", 'N', '3 car', 'PC', 'Calculée (attendances)'],
      ["Lieu et date d'établissement", 'D', '10 car', 'NU', "Générée à l'édition"],
    ],
  },

  {
    code: 'D6_ATTEST', titre: 'Attestation de stage',
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
    code: 'D7_PV_NOTES', titre: 'Document de transfert des notes',
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
];

function etudeDocuments() {
  const out = [
    P("Nous avons étudié huit documents du service : sept documents réels recueillis auprès de l'institut et une feuille d'absences établie à partir du modèle couramment utilisé. Chaque document est présenté par sa **fiche description** : ses caractéristiques, puis la liste de ses informations avec leur type, leur taille et leur utilité. La colonne *Observation* indique l'emplacement de l'information dans la base de données ; ces informations ont servi à construire le dictionnaire de données (chapitre III)."),
    ...T('Légende des fiches description', ['Code', 'Signification'], [
      ['A / AN / N', 'Alphabétique / alphanumérique / numérique'],
      ['D / H', 'Date / heure'],
      ['PU', 'Information permanente et utile : elle est stockée dans la base de données'],
      ['PC', 'Information calculée : elle est obtenue à partir d\'autres données et n\'est pas stockée'],
      ['NU', 'Information non utile au nouveau système (non retenue)'],
    ], [22, 78]),
  ];
  D.forEach((f, i) => {
    out.push(new Paragraph({ heading: L.d.HeadingLevel.HEADING_3, pageBreakBefore: true, children: [new TextRun(`3.${i + 1} Document ${i + 1} : ${f.titre}`)] }));
    out.push(...fiche(i + 1, f));
  });
  out.push(
    new Paragraph({ heading: L.d.HeadingLevel.HEADING_3, pageBreakBefore: true, children: [new TextRun(`3.${D.length + 1} Règles de calcul relevées dans les documents`)] }),
    P("Le recoupement des valeurs des documents 3, 5 et 7 a permis de retrouver les règles de calcul appliquées par l'institut :"),
    ...T('Règles de calcul vérifiées sur les documents', ['Règle', 'Formule', 'Vérification'], [
      ['R1 — Moyenne du module', "(contrôle 1 + contrôle 2 + 2 × examen) / 4 ; l'examen noté sur 20 est reporté sur 40", 'Doc. 5 : (15 + 15 + 32) / 4 = 15,50 ; doc. 7 : colonnes /20, /20 et /40'],
      ['R2 — Moyenne du semestre', 'total = Σ (moyenne du module × coefficient) ; moyenne = total / Σ coefficients', 'Doc. 5 : 439 / 29 = 15,14 ; doc. 3 : 345 / 30 = 11,50'],
      ['R3 — Décision avant rattrapage', 'moyenne ≥ 10 → admis ; moyenne < 10 → rattrapage dans les modules dont la moyenne est < 10 ; aucune note → abandon', 'Doc. 3 : 10,17 → admis malgré un module à 2,00 ; 9,10 → rattrapage'],
      ['R4 — Note éliminatoire', 'chaque module possède une note éliminatoire (0 dans les documents étudiés)', 'Doc. 3 et 5'],
    ], [22, 48, 30]),
    H3(`3.${D.length + 2} Synthèse : écarts entre les documents et la base de données`),
    P("Les informations marquées *absentes* et les règles ci-dessus conduisent aux adaptations suivantes de la base de données et de l'application :"),
    ...T('Écarts relevés et adaptations', ['N°', 'Écart constaté', 'Document', 'Adaptation'], [
      ['1', "La moyenne d'un module est une moyenne simple des notes", '3, 5', 'Appliquer la règle R1 (examen compté double)'],
      ['2', 'Le résultat de la délibération ne prévoit que admis / ajourné', '3', 'Décisions admis, rattrapage, abandon ; délibération avant et après rattrapage'],
      ['3', 'Pas de note de rattrapage ni de moyenne finale par module', '5', 'Ajouter une note de rattrapage par stagiaire et par module'],
      ['4', 'Pas de note éliminatoire', '3, 5', 'Ajouter la note éliminatoire du module'],
      ['5', 'Niveau et diplôme de la spécialité non stockés', '5', 'Ajouter le niveau et le diplôme de la spécialité'],
      ['6', 'Dates de début et de fin du semestre non stockées', '5', 'Ajouter les périodes des semestres de chaque session'],
      ['7', "Lettre du type d'étude du numéro d'inscription : « S » dans les documents, « P / C / A » dans l'application", '3, 5', "Aligner le format du numéro sur celui de l'institut"],
      ['8', "Établissement d'orientation", '4', 'Hors périmètre (perspective)'],
      ['9', 'Numéro et date du relevé ou de l\'attestation', '5, 6', "Générés lors de l'édition du document en PDF (perspective)"],
      ['10', 'Lieu de naissance du stagiaire non stocké', '6', 'Ajouter students.birth_place'],
      ['11', "Grade de l'enseignant non stocké", '7', 'Ajouter teachers.grade'],
      ['12', "Absence à une épreuve (ABS) et observation non prévues dans les notes", '7', 'Ajouter grades.is_absent et grades.remark'],
      ['13', "Session des cours du soir ouverte en octobre (formation du 08/10/2024 au 07/04/2027)", '6', "Vérifier auprès de l'institut les mois d'ouverture des sessions du soir"],
    ], [6, 42, 12, 40], { align: [AlignmentType.CENTER, null, AlignmentType.CENTER] }),
  );
  return out;
}

module.exports = { etudeDocuments };
