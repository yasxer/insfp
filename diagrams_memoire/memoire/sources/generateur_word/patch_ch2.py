p = 'content1.js'; s = open(p, encoding='utf-8').read()
a = s.index("    H2('2. Diagramme de flux (flux du contexte)'),")
b = s.index("    H2('5. Diagnostic (critiques et suggestions)'),")
new = r"""    H2("2. Diagramme de flux d'information"),
    P("Le diagramme de flux d'information donne une vue globale du fonctionnement actuel : il montre les **acteurs** du système existant, les **documents** qu'ils échangent et l'ordre de ces échanges, depuis l'ouverture de la session jusqu'à la délivrance des documents au stagiaire. Le tableau ci-dessous présente les signes utilisés."),
    ...figure('memoire/uml/flux_notation.png', "Signes du diagramme de flux d'information", { maxW: 330 }),
    P("Le **champ d'étude** correspond à la gestion pédagogique des stagiaires de la formation professionnelle continue, assurée par notre structure d'accueil. Les acteurs internes sont les services de l'institut qui interviennent dans ce champ ; les acteurs externes sont la plateforme nationale **takwin.dz**, le stagiaire, la DFEP et le CFPA."),
    ...figure('memoire/uml/flux_information.png', "Diagramme de flux d'information du système existant"),
    ...T("Description des flux d'information", ['N°', 'Flux', 'Émetteur', 'Récepteur', 'Document'], FLUX, [6, 40, 21, 21, 12],
      { align: [AlignmentType.CENTER, null, null, null, AlignmentType.CENTER], size: 17 }),
    P("La lecture du diagramme suit le déroulement réel d'une session : la DFEP fixe le calendrier (flux 1 et 2) ; le service de l'information publie le calendrier et les fiches des spécialités sur takwin.dz, où les candidats s'inscrivent (flux 3 à 7) ; les stagiaires admis sont confiés au service de la formation professionnelle continue (flux 8), qui organise les séances, les absences et les examens (flux 9 à 14), prépare les délibérations validées par la direction (flux 15 à 17) et délivre les documents au stagiaire (flux 18 à 20)."),
    H2('3. Étude des documents'),
    ...require('./docs_section').etudeDocuments(),
    H2('4. Étude des postes de travail'),
    ...T('Postes de travail étudiés', ['Poste', 'Tâches principales', 'Documents manipulés'], [
      ['Directeur', "Réception des correspondances de la DFEP, validation des délibérations, signature des relevés, des attestations et des listes d'orientation", 'D1, D8, D9, D10, D11'],
      ["Agent du service de l'information, de l'orientation et de la numérisation", "Publication du calendrier et des fiches des spécialités, accueil et inscription des candidats, orientation", 'D1, D2, D3'],
      ['Agent du service de la formation professionnelle continue', "Emplois du temps, plannings des examens, collecte des notes, préparation des délibérations, des relevés et des attestations", 'D4, D6, D7, D8, D9, D10'],
      ['Agent du service de la surveillance générale', "Suivi des absences et de la discipline des stagiaires", 'D5'],
      ['Enseignant', "Cours, appel, contrôles et examens, transfert des notes", 'D4, D5, D6, D7'],
    ], [28, 50, 22]),
"""
s = s[:a] + new + s[b:]

flux = """const FLUX = [
  ['1', "Correspondance : prolongation des inscriptions", 'DFEP', 'Direction', 'D1'],
  ['2', 'Correspondance transmise pour application', 'Direction', "Service de l'information", 'D1'],
  ['3', 'Calendrier de la session et fiches des spécialités', "Service de l'information", 'takwin.dz', 'D2, D3'],
  ['4', 'Calendrier de la session et fiches des spécialités', 'takwin.dz', 'Stagiaire', 'D2, D3'],
  ['5', 'Inscription en ligne', 'Stagiaire', 'takwin.dz', '—'],
  ['6', 'Liste des candidats inscrits', 'takwin.dz', "Service de l'information", '—'],
  ['7', "Dossier d'inscription", 'Stagiaire', "Service de l'information", '—'],
  ['8', 'Liste des stagiaires admis et orientés', "Service de l'information", 'Service de la formation continue', '—'],
  ['9', 'Emploi du temps', 'Service de la formation continue', 'Enseignant', 'D4'],
  ['10', 'Emploi du temps affiché', 'Service de la formation continue', 'Stagiaire', 'D4'],
  ['11', "Feuille d'absences", 'Enseignant', 'Service de la surveillance', 'D5'],
  ['12', 'Planning des examens semestriels', 'Service de la formation continue', 'Enseignant', 'D6'],
  ['13', 'Planning des examens affiché', 'Service de la formation continue', 'Stagiaire', 'D6'],
  ['14', 'Document de transfert des notes', 'Enseignant', 'Service de la formation continue', 'D7'],
  ['15', 'Procès-verbal des délibérations à valider', 'Service de la formation continue', 'Direction', 'D8'],
  ['16', 'Procès-verbal des délibérations validé et signé', 'Direction', 'Service de la formation continue', 'D8'],
  ['17', 'État des absences des stagiaires', 'Service de la surveillance', 'Service de la formation continue', '—'],
  ['18', 'Relevé de notes', 'Service de la formation continue', 'Stagiaire', 'D9'],
  ['19', "Demande d'attestation de stage", 'Stagiaire', 'Service de la formation continue', '—'],
  ['20', 'Attestation de stage', 'Service de la formation continue', 'Stagiaire', 'D10'],
  ['21', 'Liste nominative des stagiaires orientés', 'Direction', 'CFPA', 'D11'],
  ['22', 'Bilans et statistiques', 'Direction', 'DFEP', '—'],
];

function chapitre2() {"""
s = s.replace("function chapitre2() {", flux, 1)
open(p, 'w', encoding='utf-8').write(s)
print('ok')
