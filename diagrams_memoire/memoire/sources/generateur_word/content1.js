// Contenu — partie 1 : pages liminaires, introduction, chapitres I et II
const L = require('./lib');
const { P, bullets, numbered, H2, H3, H4, chapter, partPage, T, figure, setChapter, note } = L;
const { Paragraph, TextRun, AlignmentType, TableOfContents, StyleLevel } = L.d;

function remerciements() {
  return [
    L.H1('Remerciements'),
    P("L'aboutissement de ce travail est le fruit d'un engagement personnel soutenu, mais il n'aurait pu se concrétiser sans l'aide, les conseils et le soutien de plusieurs personnes que nous tenons à remercier chaleureusement."),
    P("Nos plus profonds remerciements s'adressent à notre encadrante, **Mme SAIGHI**, dont la rigueur, la disponibilité et les précieux conseils ont été déterminants pour l'élaboration de ce mémoire."),
    P("Nos remerciements vont également à la direction et au personnel de l'Institut National Spécialisé de la Formation Professionnelle pour le cadre, les informations et les ressources mises à notre disposition tout au long de ce projet."),
    P("Nous remercions aussi l'ensemble des enseignants qui ont contribué à notre formation, ainsi que les membres du jury qui ont accepté d'évaluer ce travail."),
    P("Enfin, nous ne saurions terminer sans une pensée pour nos familles et nos amis, dont le soutien inconditionnel et les encouragements constants ont été une force motrice tout au long de ces années."),
  ];
}

function dedicace() {
  return [
    new Paragraph({ children: [], pageBreakBefore: true }),
    L.H1('Dédicace'),
    P("Nous dédions ce travail, fruit de longues heures de labeur et de passion, à ceux qui nous sont les plus chers :"),
    ...bullets([
      "À nos très chers parents, pour leur amour inconditionnel et leurs sacrifices ;",
      "À nos familles, pour leur soutien constant et leur présence réconfortante ;",
      "À nos amis et camarades de promotion, pour leur soutien moral et leurs conseils ;",
      "À tous ceux qui nous ont soutenus, de près ou de loin, dans ce long cheminement.",
    ]),
  ];
}

function listes() {
  const hint = P("*Clic droit sur la liste → « Mettre à jour les champs » si les numéros de page ne s'affichent pas.*", { run: { size: 16, color: '8A8A8A' } });
  return [
    new Paragraph({ children: [], pageBreakBefore: true }),
    L.H1('Table des matières'),
    new TableOfContents('Table des matières', { hyperlink: true, headingStyleRange: '1-3', useAppliedParagraphOutlineLevel: true }),
    new Paragraph({ children: [], pageBreakBefore: true }),
    L.H1('Liste des figures'),
    new TableOfContents('Liste des figures', { hyperlink: true, stylesWithLevels: [new StyleLevel('FigCaption', 1)] }),
    new Paragraph({ children: [], pageBreakBefore: true }),
    L.H1('Liste des tableaux'),
    new TableOfContents('Liste des tableaux', { hyperlink: true, stylesWithLevels: [new StyleLevel('TabCaption', 1)] }),
    hint,
  ];
}

function abreviations() {
  setChapter(0);
  const rows = [
    ['INSFP', 'Institut National Spécialisé de la Formation Professionnelle'],
    ['MFEP', "Ministère de la Formation et de l'Enseignement Professionnels"],
    ['DFEP', "Direction de la Formation et de l'Enseignement Professionnels (de wilaya)"],
    ['API', 'Application Programming Interface (interface de programmation)'],
    ['REST', 'Representational State Transfer'],
    ['HTTP(S)', 'HyperText Transfer Protocol (Secure)'],
    ['JSON', 'JavaScript Object Notation'],
    ['SPA', 'Single Page Application (application à page unique)'],
    ['MVC', 'Modèle – Vue – Contrôleur'],
    ['ORM', 'Object Relational Mapping'],
    ['CRUD', 'Create, Read, Update, Delete'],
    ['UML', 'Unified Modeling Language'],
    ['MOR', 'Modèle Objet Relationnel'],
    ['SGBD', 'Système de Gestion de Bases de Données'],
    ['SQL', 'Structured Query Language'],
    ['CSRF', 'Cross-Site Request Forgery'],
    ['XSS', 'Cross-Site Scripting'],
    ['UI / UX', 'User Interface / User Experience'],
    ['PV', 'Procès-verbal'],
    ['EDT', 'Emploi du temps'],
  ];
  return [
    new Paragraph({ children: [], pageBreakBefore: true }),
    L.H1('Liste des abréviations'),
    L.table(['Abréviation', 'Signification'], rows, [22, 78]),
  ];
}

function introduction() {
  setChapter(0);
  return [
    L.section('Introduction générale'),
    L.H1('Introduction générale'),
    P("Dans le contexte actuel de transformation numérique, la gestion administrative et pédagogique des établissements de formation représente un enjeu majeur. La numérisation des processus permet d'offrir un meilleur service aux apprenants, d'alléger le travail du personnel et de garantir la traçabilité de l'information."),
    P("L'Institut National Spécialisé de la Formation Professionnelle (INSFP), établissement public relevant du Ministère de la Formation et de l'Enseignement Professionnels (MFEP), forme des techniciens et des techniciens supérieurs dans plusieurs spécialités. Sa gestion quotidienne manipule un volume important d'informations : inscriptions, affectation des enseignants, emplois du temps, assiduité, examens et notes, délibérations de fin de semestre, documents officiels et communication interne."),
    P("Aujourd'hui, une grande partie de ces activités est encore traitée de manière classique (papier, fichiers bureautiques séparés et peu synchronisés), ce qui engendre des pertes de temps, des risques d'erreurs et une difficulté d'accès à l'information."),
    P("C'est dans cette perspective que s'inscrit notre projet de fin d'études : **la conception et la réalisation d'une plateforme web de gestion intégrée de l'INSFP**, destinée à l'administration, aux enseignants et aux stagiaires."),
    P("Ce mémoire est organisé, conformément au plan de l'institut, en trois parties :"),
    ...bullets([
      "**Partie I – Partie théorique** : le chapitre I présente l'organisme d'accueil (étude préalable).",
      "**Partie II – Analyse et conception** : le chapitre II analyse le système existant ; le chapitre III présente le cahier des charges, la méthodologie SCRUM, la modélisation UML, le modèle objet relationnel et la conception des interfaces.",
      "**Partie III – Développement** : le chapitre IV décrit la réalisation (architecture, outils, base de données, interfaces, sécurité, hébergement, tests et recette).",
    ]),
    H2('Présentation du sujet'),
    P("L'INSFP a besoin de centraliser et de numériser sa gestion administrative et pédagogique. Notre sujet consiste à concevoir et réaliser une plateforme qui s'adresse à trois profils d'utilisateurs : **l'administration**, **les enseignants** et **les stagiaires**, ainsi qu'au **visiteur** (futur stagiaire) qui s'inscrit en ligne."),
    H3('1.1 Problématique'),
    P("Dans le secteur de la formation professionnelle, la performance d'un établissement dépend directement de l'efficacité de ses dispositifs de gestion. L'INSFP est ainsi confronté aux questions suivantes :"),
    ...bullets([
      "Comment transformer des processus manuels et répétitifs (inscriptions, emplois du temps, notes, assiduité) en une gestion numérique centralisée et fiable ?",
      "Comment réduire la lenteur des démarches et les saisies multiples (dossiers papier, relevés de notes, feuilles d'appel) ?",
      "Comment faciliter l'accès à l'information (emplois du temps, notes, documents) pour les stagiaires et les enseignants, y compris à distance ?",
      "Comment établir un canal de communication direct et officiel entre l'administration, les enseignants et les stagiaires ?",
    ]),
    H3('1.2 Les objectifs'),
    P("La plateforme web de l'INSFP doit permettre de :"),
    ...bullets([
      "**Numériser et automatiser** les processus : inscription en ligne à l'aide d'un numéro d'inscription, validation des comptes, gestion des sessions (promotions), des spécialités, des modules et des emplois du temps.",
      "**Assurer la traçabilité** : historique des notes, des absences, des documents distribués, des messages et des délibérations.",
      "**Faciliter l'accès à l'information** : chaque stagiaire et chaque enseignant consulte son emploi du temps, ses notes, ses devoirs et ses documents en ligne, depuis un ordinateur, une tablette ou un smartphone.",
      "**Automatiser les calculs pédagogiques** : moyennes pondérées par les coefficients, proposition du résultat de la délibération et passage de semestre.",
      "**Améliorer la communication** : messagerie interne, diffusion ciblée (tous, stagiaires, enseignants, spécialité) et notifications.",
      "**Fournir des indicateurs** : tableaux de bord et statistiques (effectifs par spécialité, enseignants, etc.).",
    ]),
  ];
}

function chapitre1() {
  setChapter(1);
  return [
    ...partPage('Partie I', 'Partie théorique', 'Étude préalable'),
    ...chapter('Chapitre I', 'Étude préalable'),
    H2("1. Présentation de l'organisme d'accueil"),
    H3("1.1 Historique de l'entreprise"),
    P("L'**Institut National Spécialisé de la Formation Professionnelle Mohamed Tayeb Boucenna**, situé à **El Mohammadia (Alger)**, est un établissement public de formation placé sous la tutelle du **Ministère de la Formation et de l'Enseignement Professionnels (MFEP)**, par l'intermédiaire de la **Direction de la Formation et de l'Enseignement Professionnels de la wilaya d'Alger**. Il a pour vocation d'assurer la formation professionnelle qualifiante et diplômante de jeunes et d'adultes dans diverses spécialités techniques et tertiaires."),
    note("[[À COMPLÉTER : date et texte de création de l'institut (décret), capacité d'accueil, nombre de stagiaires et d'enseignants]]", 'todo'),
    P("L'institut prépare notamment au **Brevet de Technicien Supérieur (BTS)**, diplôme de **niveau 5** reconnu par l'État. La formation dure **30 mois**, soit cinq semestres de cours suivis d'un stage pratique (par exemple du 25/02/2024 au 24/08/2026 pour une promotion de février). Parmi les spécialités du domaine informatique figurent :"),
    ...bullets([
      "**Informatique, option développeur web et mobile (SDWM)** ;",
      "**Administration et sécurité des réseaux informatiques (SASRI)**.",
    ]),
    P("Les stagiaires sont organisés en **sessions (promotions)** ouvertes en **février** et en **septembre**, répartis par spécialité, semestre et **groupe**, et identifiés par un **numéro d'inscription** unique (ex. 0217124S1647). La formation se déroule selon plusieurs modes :"),
    ...bullets([
      "**Formation résidentielle (présentielle)** : le stagiaire suit sa formation à plein temps à l'institut ;",
      "**Formation par apprentissage (alternance)** : la formation est partagée entre l'institut et une entreprise ;",
      "**Cours du soir** : destinés notamment aux travailleurs qui souhaitent se perfectionner ou se reconvertir.",
    ]),
    P("L'évaluation de chaque module repose sur **deux contrôles** et un **examen semestriel** ; à l'issue de chaque semestre, une **délibération** statue sur le passage du stagiaire (admis, rattrapage ou abandon), puis un **relevé de notes** lui est remis."),
    H3("1.2 Missions de l'organisme d'accueil"),
    ...numbered([
      "**Formation professionnelle qualifiante** : dispenser des formations adaptées aux besoins du marché du travail, dans plusieurs spécialités et selon différents modes.",
      "**Organisation pédagogique** : gérer les spécialités, les modules, l'affectation des enseignants et l'élaboration des emplois du temps.",
      "**Suivi des stagiaires** : assiduité, évaluations (contrôles, examens, devoirs), résultats et délibérations de fin de semestre.",
      "**Délivrance des documents officiels** : attestations, relevés de notes, convocations et pièces administratives.",
      "**Accompagnement et insertion** : préparer les stagiaires à l'intégration dans le monde professionnel.",
    ], 'num'),
    H3("1.3 Organigramme de l'organisme d'accueil"),
    P("L'institut est organisé autour d'une direction appuyée par plusieurs services. La structure simplifiée, dans la limite de notre champ d'étude, est la suivante :"),
    ...figure('memoire/uml/organigramme_insfp.png', "Organigramme simplifié de l'INSFP"),
    note("[[À VÉRIFIER : remplacer par l'organigramme officiel de l'institut (intitulés exacts des services et sous-directions)]]", 'todo'),
    H3("1.4 Organigramme de la structure d'accueil"),
    P("Notre stage s'est déroulé au sein du service chargé de la pédagogie et de la scolarité, qui constitue le cœur métier de la plateforme."),
    ...figure('memoire/uml/organigramme_structure.png', "Organigramme de la structure d'accueil"),
    note("[[À VÉRIFIER : intitulé exact de la structure d'accueil et de ses bureaux]]", 'todo'),
    H3("1.5 Missions de la structure d'accueil"),
    ...bullets([
      "Recevoir et traiter les dossiers d'inscription des stagiaires et tenir à jour leurs dossiers ;",
      "Répartir les stagiaires par spécialité, mode d'étude et groupe ;",
      "Élaborer les emplois du temps et affecter les enseignants aux modules ;",
      "Organiser les contrôles et les examens, collecter les notes et préparer les procès-verbaux ;",
      "Préparer et organiser les délibérations de fin de semestre et suivre le passage des stagiaires ;",
      "Établir les attestations, les relevés de notes et diffuser les informations (affichage, convocations).",
    ]),
    H3('1.6 Moyens informatiques et humains'),
    H4('1.6.1 Moyens matériels et logiciels'),
    ...T('Moyens matériels et logiciels existants', ['Appareil / logiciel', 'Type', 'Observation'], [
      ['Ordinateurs de bureau', 'Postes administratifs', 'Scolarité et service pédagogique'],
      ['Imprimantes', 'Laser', 'Édition des listes, relevés et attestations'],
      ['Serveur', 'Local', "Peut héberger l'application et la base de données"],
      ['Réseau', 'Filaire et Wi-Fi', 'Accès à Internet et au réseau local'],
      ["Système d'exploitation", 'Windows 10 / 11', 'Postes de travail'],
      ['Suite bureautique', 'Microsoft Office', 'Tableaux et documents actuellement utilisés'],
    ], [30, 25, 45]),
    note("[[À COMPLÉTER : quantités et caractéristiques réelles des équipements]]", 'todo'),
    H4('1.6.2 Moyens humains'),
    ...bullets([
      "**Personnel administratif** : inscriptions, scolarité, documents et délibérations ;",
      "**Enseignants / formateurs** : cours, assiduité, évaluations et suivi pédagogique ;",
      "**Responsable informatique** : maintenance du parc et exploitation de la solution numérique ;",
      "**Direction** : supervision et validation des décisions.",
    ]),
  ];
}

function posteFiche(i, code, nom, struct, resp, eff, taches) {
  const { Table, TableRow, TableCell, WidthType, ShadingType, BorderStyle } = L.d;
  const W = L.CONTENT_W;
  const B = { style: BorderStyle.SINGLE, size: 4, color: L.C.grid };
  const bd = { top: B, bottom: B, left: B, right: B };
  const cell = (children, w, fill, span) => new TableCell({ children, columnSpan: span, width: { size: w, type: WidthType.DXA },
    shading: fill ? { type: ShadingType.CLEAR, color: 'auto', fill } : undefined, borders: bd, margins: { top: 50, bottom: 50, left: 110, right: 110 } });
  const par = (t, o = {}) => new Paragraph({ children: L.runs(t, { size: o.size || 21, color: o.color, bold: o.bold }), spacing: { after: 50 } });
  const c1 = Math.round(W * 0.68), c2 = W - c1;
  return [
    new Paragraph({ keepNext: true, pageBreakBefore: i > 1, spacing: { before: 120, after: 120 }, children: [new TextRun({ text: `Fiche ${i} : Fiche d'étude du poste ${nom.charAt(0).toLowerCase() + nom.slice(1)}`, bold: true })] }),
    new Table({ width: { size: W, type: WidthType.DXA }, columnWidths: [c1, c2], rows: [
      new TableRow({ children: [cell([par(`**Fiche d'étude du poste : ${nom}**`, { color: 'FFFFFF' })], W, L.C.head, 2)] }),
      new TableRow({ children: [cell([par('**CARACTÉRISTIQUES**', { size: 18, color: L.C.gold }), par(`**Code poste :** ${code}`), par(`**Désignation du poste :** ${nom}`),
        par(`**Structure de rattachement :** ${struct}`), par(`**Responsabilité du poste :** ${resp}`), par(`**Effectif :** ${eff}`),
        par('**Moyens matériels :** micro-ordinateur et imprimante')], W, L.C.light, 2)] }),
      new TableRow({ children: [cell([par('**Tâche**')], c1, 'D5E9ED'), cell([par('**Fréquence**')], c2, 'D5E9ED')] }),
      ...taches.map(([t, f], k) => new TableRow({ cantSplit: true, children: [cell([par(t)], c1, k % 2 ? 'F3F7FB' : 'FFFFFF'), cell([par(f)], c2, k % 2 ? 'F3F7FB' : 'FFFFFF')] })),
    ] }),
    L.tcaption(`Fiche d'étude du poste ${nom.charAt(0).toLowerCase() + nom.slice(1)}`),
  ];
}

const FLUX = [
  ["F1", "Correspondance de la DFEP", "DFEP", "Directrice", "D1"],
  ["F2", "Calendrier de la session", "Agent d'information et d'orientation", "takwin.dz", "D2"],
  ["F3", "Fiche descriptive de la spécialité", "Agent d'information et d'orientation", "takwin.dz", "D3"],
  ["F4", "Liste des candidats inscrits", "takwin.dz", "Agent d'information et d'orientation", "—"],
  ["F5", "Dossier d'inscription", "Stagiaire", "Agent d'information et d'orientation", "—"],
  ["F6", "Liste des stagiaires admis", "Agent d'information et d'orientation", "Agent de la formation continue", "—"],
  ["F7", "Emploi du temps", "Agent de la formation continue", "Enseignant", "D4"],
  ["F8", "Feuille d'absences", "Enseignant", "Agent de surveillance", "D5"],
  ["F9", "Planning des examens semestriels", "Agent de la formation continue", "Enseignant", "D6"],
  ["F10", "Document de transfert des notes", "Enseignant", "Agent de la formation continue", "D7"],
  ["F11", "Procès-verbal des délibérations", "Agent de la formation continue", "Directrice", "D8"],
  ["F11'", "Procès-verbal des délibérations signé", "Direction", "Agent de la formation continue", "D8"],
  ["F12", "État des absences des stagiaires", "Agent de surveillance", "Agent de la formation continue", "—"],
  ["F13", "Relevé de notes", "Agent de la formation continue", "Directrice", "D9"],
  ["F13'", "Relevé de notes signé", "Direction", "Agent de la formation continue", "D9"],
  ["F14", "Demande d'attestation de stage", "Stagiaire", "Agent de la formation continue", "—"],
  ["F15", "Attestation de stage", "Agent de la formation continue", "Directrice", "D10"],
  ["F15'", "Attestation de stage signée", "Direction", "Agent de la formation continue", "D10"],
];

function chapitre2() {
  setChapter(2);
  return [
    ...partPage('Partie II', 'Analyse et conception', "Analyse de l'existant — Modélisation et conception du nouveau système"),
    ...chapter('Chapitre II', "Analyse de l'existant"),
    H2('1. Introduction'),
    P("Avant de concevoir la nouvelle solution, il est nécessaire de comprendre le fonctionnement actuel du service : les acteurs, les informations échangées, les documents utilisés et les postes de travail. Cette analyse permet d'identifier les insuffisances du système existant et de proposer des améliorations."),
    H2("2. Diagramme de flux d'information"),
    P("Le flux d'information représente les échanges d'informations entre les acteurs qui interviennent dans la gestion des stagiaires, ainsi qu'avec leur environnement. Un acteur peut être une personne ou une structure. On distingue :"),
    ...bullets([
      "**Le flux interne** : échange d'informations entre les acteurs internes au champ d'étude (directrice, agents de l'institut et enseignants).",
      "**Le flux externe** : échange d'informations entre un acteur interne et un acteur externe (stagiaire, plateforme takwin.dz, DFEP). Les échanges entre deux acteurs externes ne sont pas représentés.",
    ]),
    P("Le diagramme montre les **documents** échangés et l'ordre de ces échanges ; un même document qui circule d'un acteur à un autre garde le même numéro, et un document envoyé pour signature puis retourné signé porte ce numéro marqué d'un prime (ex. : F11 puis F11'), depuis l'ouverture de la session jusqu'à la délivrance des documents au stagiaire. Le tableau ci-dessous présente les signes utilisés."),
    ...figure('memoire/uml/flux_notation.png', "Signes du diagramme de flux d'information", { maxW: 330 }),
    P("Le **champ d'étude** correspond à la gestion pédagogique des stagiaires de la formation professionnelle continue, assurée par notre structure d'accueil. Les acteurs internes sont les postes de travail de l'institut qui interviennent dans ce champ (directrice, agents et enseignants) ; les acteurs externes sont la plateforme nationale **takwin.dz**, le stagiaire et la DFEP."),
    ...figure('memoire/uml/flux_information.png', "Diagramme de flux d'information du système existant"),
    ...T("Description des flux d'information", ['N° de flux', 'Document transmis'],
      FLUX.map((f) => [f[0], f[1]]), [18, 82], { align: [AlignmentType.CENTER] }),
    H2('3. Étude des documents'),
    ...require('./docs_section').etudeDocuments(),
    H2('4. Étude des postes de travail'),
    H3('4.1 Définition'),
    P("Un poste de travail est l'ensemble des tâches confiées à une personne dans une structure. L'étude des postes de travail permet de connaître les acteurs du système actuel, leurs responsabilités, les tâches qu'ils réalisent et leur fréquence."),
    H3('4.2 Liste des postes de travail'),
    P("Les postes de travail retenus sont les acteurs internes du diagramme de flux d'information :"),
    ...T('Liste des postes de travail', ['Code', 'Désignation du poste'], [['P1', 'Directrice'], ['P2', "Agent d'information et d'orientation"], ['P3', "Agent de la formation continue"], ['P4', "Agent de surveillance"], ['P5', 'Enseignant']], [15, 85], { align: [AlignmentType.CENTER] }),
    H3('4.3 Fiches d\'étude des postes'),
    ...posteFiche(1, 'P1', 'Directrice', 'Direction', "Diriger l'institut, valider les délibérations et signer les documents officiels", '01', [['Réception et transmission des correspondances de la DFEP', 'Selon besoin'], ['Validation des procès-verbaux des délibérations', 'Chaque semestre'], ['Signature des relevés de notes et des attestations', 'Quotidienne']]),
    ...posteFiche(2, 'P2', "Agent d'information et d'orientation", "Sous-direction de l'information, de l'orientation, de la numérisation et de l'insertion professionnelle", 'Informer et orienter les candidats, gérer les inscriptions', '02', [['Publication du calendrier de la session et des fiches des spécialités', 'Chaque session'], ['Accueil, information et inscription des candidats', 'Quotidienne pendant les inscriptions'], ["Organisation des journées de sélection et d'orientation", 'Chaque session'], ['Transmission de la liste des stagiaires admis', 'Chaque session']]),
    ...posteFiche(3, 'P3', "Agent de la formation continue", "Sous-direction de l'apprentissage et de la formation professionnelle continue", 'Organiser la formation des stagiaires des cours du soir et suivre leurs résultats', '02', [['Élaboration des emplois du temps', 'Chaque semestre'], ['Élaboration des plannings des examens', 'Chaque semestre'], ['Collecte des notes et préparation des délibérations', 'Chaque semestre'], ['Établissement des relevés de notes et des attestations', 'Quotidienne']]),
    ...posteFiche(4, 'P4', "Agent de surveillance", "Sous-direction de l'information, de l'orientation, de la numérisation et de l'insertion professionnelle", 'Suivre les absences et la discipline des stagiaires', '02', [["Réception des feuilles d'absences", 'Quotidienne'], ['Suivi des absences et des justificatifs', 'Quotidienne'], ["Établissement de l'état des absences", 'Chaque semestre']]),
    ...posteFiche(5, 'P5', 'Enseignant', 'Corps des formateurs', 'Assurer les cours, les évaluations et le suivi des stagiaires', '73', [['Cours et travaux pratiques', 'Quotidienne'], ['Appel des stagiaires', 'Chaque séance'], ['Contrôles et examens', 'Chaque semestre'], ['Transfert des notes', 'Chaque semestre']]),
    H2('5. Diagnostic (critiques et suggestions)'),
    H3('5.1 Critiques'),
    ...bullets([
      "Saisie répétée des mêmes informations (listes, notes) sur plusieurs supports papier et fichiers séparés.",
      "Calcul manuel des moyennes et préparation manuelle des procès-verbaux, source d'erreurs.",
      "Risque de chevauchements dans les emplois du temps (enseignants, groupes, salles).",
      "Accès limité à l'information : emplois du temps, plannings et résultats seulement affichés.",
      "Feuilles d'absences papier difficiles à exploiter pour le suivi de l'assiduité.",
      "Absence d'un canal de communication officiel entre l'administration, les enseignants et les stagiaires.",
      "Archivage papier exposé aux pertes et manque de traçabilité.",
    ]),
    H3('5.2 Suggestions'),
    P("Afin de remédier aux insuffisances constatées, nous proposons :"),
    ...bullets([
      "Une base de données unique partagée par tous les services.",
      "L'inscription en ligne à l'aide d'un numéro d'inscription unique, puis la validation des comptes par l'administration.",
      "Le calcul automatique des moyennes selon les règles de l'institut et la proposition des décisions de la délibération.",
      "La détection automatique des conflits lors de la saisie des séances.",
      "La consultation en ligne des emplois du temps, des notes et des documents, à tout moment.",
      "L'appel numérique par séance et des statistiques d'assiduité.",
      "Une messagerie interne et des notifications ciblées.",
      "Un stockage centralisé et des sauvegardes régulières de la base de données.",
    ]),
  ];
}

module.exports = { remerciements, dedicace, listes, abreviations, introduction, chapitre1, chapitre2 };
