// Étude des documents de l'institut (chapitre II) — fiches description (Information | Type | Taille | Utilité)
const L = require('./lib');
const { P, H3, T, note, tcaption, runs } = L;
const { Paragraph, TextRun, Table, TableRow, TableCell, WidthType, ShadingType, BorderStyle, AlignmentType, VerticalAlign } = L.d;

const W = L.CONTENT_W;
const COLS = [58, 14, 14, 14].map((p) => Math.round((W * p) / 100));
COLS[3] = W - COLS.slice(0, 3).reduce((a, b) => a + b, 0);
const B = { style: BorderStyle.SINGLE, size: 4, color: L.C.grid };
const borders = { top: B, bottom: B, left: B, right: B };

const cell = (children, o = {}) => new TableCell({
  children, columnSpan: o.span, width: { size: o.w, type: WidthType.DXA },
  shading: o.fill ? { type: ShadingType.CLEAR, color: 'auto', fill: o.fill } : undefined,
  borders, verticalAlign: VerticalAlign.CENTER, margins: { top: 50, bottom: 50, left: 110, right: 110 },
});
const par = (text, o = {}) => new Paragraph({ children: runs(text, { size: o.size || 20, color: o.color || L.C.text, bold: o.bold }),
  alignment: o.align || AlignmentType.LEFT, spacing: { after: o.after ?? 60, line: 260 } });

function fiche(f) {
  const lab = (k, v) => par(`**${k} :** ${v}`, { size: 19 });
  const caract = [
    par('DESCRIPTION DU DOCUMENT', { size: 16, bold: true, color: L.C.gold, after: 100 }),
    lab('Code', f.code), lab('Désignation', f.titre), lab('Rôle du document', f.role), lab('Émetteur', f.emetteur),
    lab('Récepteur', f.recepteur), lab('Nature', f.nature), lab("Nombre d'exemplaires", f.exemplaires), lab('Format', f.format || 'A4'),
  ];
  const head = ['Information', 'Type', 'Taille', 'Utilisation'];
  const CENTER = [false, true, true, true];
  const rows = [
    new TableRow({ cantSplit: true, children: [cell([par(`Fiche description du document : **${f.titre}**`, { size: 21, color: 'FFFFFF' })], { span: 4, w: W, fill: L.C.head })] }),
    new TableRow({ cantSplit: true, children: [cell(caract, { span: 4, w: W, fill: L.C.light })] }),
    new TableRow({ children: head.map((h, i) => cell([par(`**${h}**`, { size: 18, color: L.C.ink, align: CENTER[i] ? AlignmentType.CENTER : AlignmentType.LEFT })], { w: COLS[i], fill: 'D5E9ED' })) }),
    ...f.rows.map((r, k) => new TableRow({ cantSplit: true, children: r.map((v, i) => cell([par(v, {
      size: 18, align: CENTER[i] ? AlignmentType.CENTER : AlignmentType.LEFT, after: 20 })], {
      w: COLS[i], fill: k % 2 ? 'F3F7FB' : 'FFFFFF' })) })),
  ];
  return [
    new Table({ width: { size: W, type: WidthType.DXA }, columnWidths: COLS, rows }),
    tcaption(`Fiche d'analyse du document ${f.titre.charAt(0).toLowerCase() + f.titre.slice(1)}`),
    ...(f.remarque ? [note(f.remarque, 'info')] : [P('', { after: 80 })]),
  ];
}

const R = (info, type, taille, util) => [info, type, taille, util];

const D = [
  {
    code: 'D1', titre: 'Correspondance de la DFEP',
    role: "Informer les directeurs des établissements de formation de la prolongation de la période d'inscription de la session d'octobre 2026 et des nouvelles dates de sélection, d'annonce des résultats et de rentrée.",
    emetteur: "Direction de la Formation et de l'Enseignement Professionnels de la wilaya d'Alger", recepteur: "Directrice de l'institut (destinataire, comme tous les directeurs des établissements de formation)", nature: 'Externe', exemplaires: '01',
    rows: [
      R('Numéro de la correspondance', 'AN', '30 car', 'PP'), R('Date de la correspondance', 'D', '10 car', 'PP'),
      R('Objet', 'AN', '255 car', 'PP'), R('Références', 'AN', '255 car', 'PP'),
      R('Session concernée', 'AN', '30 car', 'PP'), R('Date de fin des inscriptions', 'D', '10 car', 'PP'),
      R("Période de sélection et d'orientation", 'D', '10 car', 'PP'), R('Date de la rentrée', 'D', '10 car', 'PP'),
      R('Nom du signataire de la correspondance', 'A', '100 car', 'PP'),
    ],
  },
  {
    code: 'D2', titre: 'Calendrier de la session',
    role: "Informer les candidats et les stagiaires des étapes de la session : inscriptions, sélection et orientation, correction et délibérations, annonce des résultats et rentrée officielle.",
    emetteur: "Agent d'information et d'orientation", recepteur: 'Candidats et stagiaires', nature: 'Externe', exemplaires: '01 (affiché et publié en ligne)',
    rows: [
      R('Mois de la session', 'A', '10 car', 'PP'), R('Année de la session', 'N', '4 car', 'PP'),
      R("Numéro de l'étape", 'N', '2 car', 'PP'), R("Intitulé de l'étape", 'AN', '100 car', 'PP'),
      R("Date de début de l'étape", 'D', '10 car', 'PP'), R("Date de fin de l'étape", 'D', '10 car', 'PP'),
      R('Date de la rentrée officielle', 'D', '10 car', 'PP'), R('Adresse de la plateforme', 'AN', '50 car', 'PP'),
    ],
  },
  {
    code: 'D3', titre: 'Fiche descriptive de la spécialité',
    role: "Présenter une spécialité aux candidats : filière, code, mode de formation, niveau, conditions d'accès, durée, diplôme, missions du diplômé, aptitudes requises et débouchés.",
    emetteur: "Agent d'information et d'orientation", recepteur: 'Candidats', nature: 'Externe', exemplaires: '01 par spécialité',
    rows: [
      R('Code de la filière', 'AN', '3 car', 'PP'), R('Intitulé de la filière', 'AN', '100 car', 'PP'),
      R('Intitulé de la spécialité', 'AN', '255 car', 'PP'), R('Code de la spécialité', 'AN', '10 car', 'PP'),
      R('Mode de formation', 'A', '20 car', 'PP'), R('Niveau de qualification', 'N', '1 car', 'PP'),
      R("Niveau d'accès", 'AN', '50 car', 'PP'), R('Durée de la formation', 'N', '2 car', 'PP'),
      R('Diplôme délivré', 'AN', '100 car', 'PP'), R('Définition de la spécialité', 'AN', '1000 car', 'PP'),
      R('Missions principales', 'AN', '1000 car', 'PP'), R('Aptitudes requises', 'AN', '255 car', 'PP'),
      R('Débouchés', 'AN', '255 car', 'PP'),
    ],
  },
  {
    code: 'D4', titre: 'Emploi du temps',
    role: 'Répartir les séances hebdomadaires de chaque spécialité, semestre et groupe : jour, horaire, module, enseignant et salle.',
    emetteur: "Agent de la formation continue", recepteur: 'Enseignants et stagiaires', nature: 'Interne', exemplaires: '01 (affiché)',
    rows: [
      R('Journée', 'A', '10 car', 'PP'), R('Heure de début', 'H', '5 car', 'PP'), R('Heure de fin', 'H', '5 car', 'PP'),
      R('Code de la spécialité', 'AN', '10 car', 'PP'), R('Semestre', 'N', '1 car', 'PP'), R('Groupe', 'AN', '10 car', 'PP'),
      R('Code du module', 'AN', '10 car', 'PP'), R('Intitulé du module', 'AN', '255 car', 'PP'),
      R("Nom de l'enseignant", 'A', '100 car', 'PP'), R('Salle', 'AN', '50 car', 'PP'),
      R('Mode de formation', 'A', '20 car', 'PP'), R('Année de formation', 'AN', '9 car', 'PP'),
    ],
  },
  {
    code: 'D5', titre: "Feuille d'absences",
    role: 'Relever, pour une séance, la présence de chaque stagiaire du groupe : présent, absent, en retard ou excusé.',
    emetteur: 'Enseignant', recepteur: "Agent de surveillance", nature: 'Interne', exemplaires: '01 par séance',
    remarque: "Cette feuille a été établie à partir du modèle de feuille d'appel couramment utilisé dans l'institut, d'après les informations recueillies auprès des enseignants et des agents de surveillance.",
    rows: [
      R('Spécialité', 'A', '255 car', 'PP'), R('Semestre', 'N', '1 car', 'PP'), R('Groupe', 'AN', '10 car', 'PP'),
      R('Mode de formation', 'A', '20 car', 'PP'), R('Intitulé du module', 'AN', '255 car', 'PP'),
      R("Nom de l'enseignant", 'A', '100 car', 'PP'), R('Date de la séance', 'D', '10 car', 'PP'),
      R('Heure de début', 'H', '5 car', 'PP'), R('Heure de fin', 'H', '5 car', 'PP'), R('Salle', 'AN', '50 car', 'PP'),
      R("Numéro d'ordre", 'N', '3 car', 'PP'), R("Numéro d'inscription du stagiaire", 'AN', '20 car', 'PP'),
      R('Nom du stagiaire', 'A', '100 car', 'PP'), R('Prénom du stagiaire', 'A', '100 car', 'PP'), R('Statut de présence', 'A', '10 car', 'PP'),
      R('Motif', 'AN', '255 car', 'PNP'), R('Nombre de présents', 'N', '3 car', 'PP'), R("Nombre d'absents", 'N', '3 car', 'PP'),
      R("Signature de l'enseignant", '—', '—', 'PP'),
    ],
  },
  {
    code: 'D6', titre: 'Planning des examens semestriels',
    role: "Programmer les examens de fin de semestre de chaque spécialité, semestre et groupe : date, horaire, module et enseignant ; informer les enseignants et les stagiaires.",
    emetteur: "Agent de la formation continue", recepteur: 'Enseignants et stagiaires', nature: 'Interne', exemplaires: '01 (affiché)',
    rows: [
      R("Date de l'examen", 'D', '10 car', 'PP'), R('Heure de début', 'H', '5 car', 'PP'), R('Heure de fin', 'H', '5 car', 'PP'),
      R('Code de la spécialité', 'AN', '10 car', 'PP'), R('Semestre', 'N', '1 car', 'PP'), R('Groupe', 'AN', '10 car', 'PP'),
      R('Intitulé du module', 'AN', '255 car', 'PP'), R("Nom de l'enseignant", 'A', '100 car', 'PP'),
      R('Mode de formation', 'A', '20 car', 'PP'), R("Type d'évaluation", 'A', '10 car', 'PP'),
    ],
  },
  {
    code: 'D7', titre: 'Document de transfert des notes',
    role: "Transmettre les notes d'un module pour un groupe : notes des contrôles et de l'examen, moyennes avant et après rattrapage ; ce document alimente le procès-verbal des délibérations.",
    emetteur: 'Enseignant du module', recepteur: "Agent de la formation continue", nature: 'Interne', exemplaires: '01',
    rows: [
      R('Mode de formation', 'A', '20 car', 'PP'), R('Groupe', 'AN', '10 car', 'PP'), R('Spécialité', 'A', '255 car', 'PP'),
      R('Intitulé du module', 'AN', '255 car', 'PP'), R("Nom de l'enseignant", 'A', '100 car', 'PP'),
      R("Grade de l'enseignant", 'AN', '100 car', 'PP'), R('Coefficient', 'N', '3 car', 'PP'),
      R('Note éliminatoire', 'N', '5 car', 'PP'), R("Numéro d'ordre", 'N', '3 car', 'PP'),
      R('Nom du stagiaire', 'A', '100 car', 'PP'), R('Prénom du stagiaire', 'A', '100 car', 'PP'), R('Date de naissance du stagiaire', 'D', '10 car', 'PP'),
      R('Note du contrôle 1', 'N', '5 car', 'PP'), R('Note du contrôle 2', 'N', '5 car', 'PP'), R("Note de l'examen", 'N', '5 car', 'PP'),
      R("Mention d'absence", 'A', '3 car', 'NPP'), R('Moyenne avant rattrapage', 'N', '5 car', 'PP'),
      R('Note de rattrapage', 'N', '5 car', 'PNP'), R('Moyenne après rattrapage', 'N', '5 car', 'PNP'),
      R('Remarque', 'AN', '255 car', 'PNP'),
    ],
  },
  {
    code: 'D8', titre: 'Procès-verbal des délibérations',
    role: "Présenter, pour un groupe et un semestre, la moyenne de chaque stagiaire dans chaque module, le total, la moyenne du semestre et la décision : admis, rattrapage ou abandon.",
    emetteur: "Agent de la formation continue", recepteur: 'Directrice', nature: 'Interne', exemplaires: '01',
    remarque: "Le procès-verbal établi après le rattrapage a la même forme : seules les lignes des stagiaires ayant passé le rattrapage sont mises à jour. Il n'a donc pas fait l'objet d'une fiche séparée.",
    rows: [
      R('Spécialité', 'A', '255 car', 'PP'), R('Semestre', 'N', '1 car', 'PP'), R('Groupe', 'AN', '10 car', 'PP'),
      R('Phase de la délibération', 'A', '20 car', 'PP'), R("Numéro d'ordre", 'N', '3 car', 'PP'),
      R("Numéro d'inscription du stagiaire", 'AN', '20 car', 'PP'), R('Nom du stagiaire', 'A', '100 car', 'PP'), R('Prénom du stagiaire', 'A', '100 car', 'PP'),
      R('Intitulé du module', 'AN', '255 car', 'PP'), R('Coefficient', 'N', '3 car', 'PP'), R('Note éliminatoire', 'N', '5 car', 'PP'),
      R('Moyenne du module', 'N', '5 car', 'PP'), R('Total des points', 'N', '6 car', 'PP'),
      R('Moyenne du semestre', 'N', '5 car', 'PP'), R('Décision', 'A', '15 car', 'PP'),
      R('Nom du formateur du module', 'A', '100 car', 'PP'), R('Signatures', '—', '—', 'PP'),
    ],
  },
  {
    code: 'D9', titre: 'Relevé de notes',
    role: "Communiquer au stagiaire, pour un semestre, ses notes de contrôle et d'examen, ses moyennes, la décision du jury et son nombre d'absences.",
    emetteur: "Agent de la formation continue (document signé par la directrice)", recepteur: 'Stagiaire', nature: 'Externe', exemplaires: '01',
    rows: [
      R('Numéro du relevé', 'AN', '20 car', 'PP'), R('Nom du stagiaire', 'A', '100 car', 'PP'), R('Prénom du stagiaire', 'A', '100 car', 'PP'),
      R("Numéro d'inscription du stagiaire", 'AN', '20 car', 'PP'), R('Spécialité', 'A', '255 car', 'PP'),
      R('Niveau de qualification', 'N', '1 car', 'PP'), R('Diplôme', 'AN', '100 car', 'PP'),
      R('Date de début de la formation', 'D', '10 car', 'PP'), R('Date de fin de la formation', 'D', '10 car', 'PP'),
      R('Numéro du semestre', 'N', '1 car', 'PP'), R('Date de début du semestre', 'D', '10 car', 'PP'), R('Date de fin du semestre', 'D', '10 car', 'PP'),
      R('Intitulé du module', 'AN', '255 car', 'PP'), R('Note du contrôle 1', 'N', '5 car', 'PP'), R('Note du contrôle 2', 'N', '5 car', 'PP'),
      R("Note de l'examen", 'N', '5 car', 'PP'), R('Moyenne avant rattrapage', 'N', '5 car', 'PP'),
      R('Note de rattrapage', 'N', '5 car', 'PNP'), R('Moyenne finale du module', 'N', '5 car', 'PP'),
      R('Coefficient', 'N', '3 car', 'PP'), R('Note éliminatoire', 'N', '5 car', 'PP'), R('Total des points', 'N', '6 car', 'PP'),
      R('Moyenne du semestre', 'N', '5 car', 'PP'), R('Décision du jury', 'A', '15 car', 'PP'), R('Observation du jury', 'AN', '255 car', 'PNP'),
      R("Nombre d'absences", 'N', '3 car', 'PNP'), R("Nombre d'absences justifiées", 'N', '3 car', 'PNP'),
      R("Lieu d'établissement", 'A', '50 car', 'PP'), R("Date d'établissement", 'D', '10 car', 'PP'),
    ],
  },
  {
    code: 'D10', titre: 'Attestation de stage',
    role: "Attester que le stagiaire suit une formation à l'institut : identité, spécialité, mode de formation, diplôme préparé, période de formation, année et semestre en cours.",
    emetteur: "Agent de la formation continue (document signé par la directrice)", recepteur: 'Stagiaire', nature: 'Externe', exemplaires: '01',
    rows: [
      R("Numéro de l'attestation", 'AN', '20 car', 'PP'), R('Nom du stagiaire', 'A', '100 car', 'PP'), R('Prénom du stagiaire', 'A', '100 car', 'PP'),
      R('Date de naissance du stagiaire', 'D', '10 car', 'PP'), R('Lieu de naissance du stagiaire', 'A', '100 car', 'PP'), R('Adresse du stagiaire', 'AN', '500 car', 'PP'),
      R("Numéro d'inscription du stagiaire", 'AN', '20 car', 'PP'), R('Spécialité', 'A', '255 car', 'PP'), R('Mode de formation', 'A', '20 car', 'PP'),
      R('Diplôme préparé', 'AN', '100 car', 'PP'), R('Date de début de la formation', 'D', '10 car', 'PP'),
      R('Date de fin de la formation', 'D', '10 car', 'PP'), R('Année de formation', 'AN', '9 car', 'PP'),
      R('Numéro du semestre', 'N', '1 car', 'PP'), R('Date de début du semestre', 'D', '10 car', 'PP'),
      R('Date de fin du semestre', 'D', '10 car', 'PP'), R("Lieu et date d'établissement", 'AN', '50 car', 'PP'),
      R("Nom de l'agent", 'A', '100 car', 'PP'),
    ],
  },
];

function etudeDocuments() {
  const out = [
    H3('3.1 Définition'),
    P("Un document est un support d'information qui permet de collecter, de conserver et de transmettre des données entre les différents acteurs de l'institut. L'étude des documents consiste à recenser les documents utilisés dans le système actuel, à décrire leur rôle et leur circulation, puis à analyser chacune de leurs informations."),
    H3('3.2 Abréviations utilisées'),
    ...T('Liste des abréviations', ['Abréviation', 'Signification'], [
      ['PP', 'Prévue portée : une information qui existe dans le document et qui est remplie.'],
      ['PNP', 'Prévue non portée : une information qui existe dans le document mais qui n\'est pas renseignée.'],
      ['NPP', 'Non prévue portée : une information qui n\'existe pas dans le document mais qui y est ajoutée à la main.'],
      ['A / AN / N', 'Alphabétique / alphanumérique / numérique'],
      ['D / H', 'Date / heure'],
    ], [20, 80]),
    H3('3.3 Liste des documents'),
    P("Nous avons recensé dix documents utilisés pour la gestion des stagiaires de la formation professionnelle continue. Ils sont numérotés de D1 à D10 dans l'ordre où ils apparaissent dans le diagramme de flux."),
    ...T('Liste des documents', ['N°', 'Désignation du document'], D.map((f) => [f.code, f.titre]), [15, 85], { align: [AlignmentType.CENTER] }),
    new Paragraph({ heading: L.d.HeadingLevel.HEADING_3, pageBreakBefore: true, children: [new TextRun("3.4 Fiches d'analyse des documents")] }),
  ];
  D.forEach((f, i) => {
    out.push(new Paragraph({ pageBreakBefore: i > 0, keepNext: true, spacing: { before: 120, after: 120 }, children: [new TextRun({ text: `Document ${i + 1} : ${f.titre}`, bold: true, size: 24 })] }));
    out.push(...fiche(f));
  });
  return out;
}

module.exports = { etudeDocuments, DOCS: D };
