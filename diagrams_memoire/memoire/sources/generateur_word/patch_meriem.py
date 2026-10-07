import re
# ---------------------------------------------------------------- étude des documents
p = 'docs_section.js'; s = open(p, encoding='utf-8').read()

def rep(a, b, cnt=1):
    global s
    assert a in s, a[:90]
    s = s.replace(a, b) if cnt == 0 else s.replace(a, b, cnt)

# utilisation PP / PNP / NPP
PNP = {('D7', 'Note de rattrapage'), ('D7', 'Moyenne après rattrapage'), ('D7', 'Remarque'),
       ('D9', 'Note de rattrapage'), ('D9', 'Observation du jury'), ('D9', "Nombre d'absences"),
       ('D9', "Nombre d'absences justifiées"), ('D5', 'Motif')}
NPP = {('D7', "Mention d'absence")}
out, code = [], None
for line in s.split('\n'):
    m = re.search(r"code: '(D\d+)'", line)
    if m:
        code = m.group(1)
    def fix(mm):
        info = mm.group(1).replace("\\'", "'")
        u = 'PNP' if (code, info) in PNP else 'NPP' if (code, info) in NPP else 'PP'
        return mm.group(0)[: mm.group(0).rfind("'", 0, len(mm.group(0)) - 2)] .rsplit("'", 1)[0] + f"'{u}')"
    line = re.sub(r"R\((?:'|\")((?:[^'\"\\]|\\.)*?)(?:'|\"), '[^']*', '[^']*', '(?:PU|PC|NU)'\)", lambda mm: re.sub(r"'(PU|PC|NU)'\)$", f"'{'PNP' if (code, mm.group(1).replace(chr(92)+chr(39), chr(39))) in PNP else 'NPP' if (code, mm.group(1)) in NPP else 'PP'}')", mm.group(0)), line)
    out.append(line)
s = '\n'.join(out)
rep("const head = ['Information', 'Type', 'Taille', 'Utilité'];", "const head = ['Information', 'Type', 'Taille', 'Utilisation'];")
rep("lab('Code du document', f.code), lab('Rôle', f.role),",
    "lab('Code', f.code), lab('Désignation', f.titre), lab('Rôle du document', f.role),")
rep("lab('Récepteur', f.recepteur), lab('Nature', f.nature), lab(\"Nombre d'exemplaires\", f.exemplaires),",
    "lab('Récepteur', f.recepteur), lab('Nature', f.nature), lab(\"Nombre d'exemplaires\", f.exemplaires), lab('Format', f.format || 'A4'),")
rep("par('LES CARACTÉRISTIQUES DU DOCUMENT',", "par('DESCRIPTION DU DOCUMENT',")
a = s.index('function etudeDocuments() {'); b = s.index("  D.forEach((f, i) => {")
s = s[:a] + """function etudeDocuments() {
  const out = [
    H3('3.1 Définition'),
    P("Un document est un support d'information qui permet de collecter, de conserver et de transmettre des données entre les différents acteurs de l'institut. L'étude des documents consiste à recenser les documents utilisés dans le système actuel, à décrire leur rôle et leur circulation, puis à analyser chacune de leurs informations."),
    H3('3.2 Abréviations utilisées'),
    ...T('Liste des abréviations', ['Abréviation', 'Signification'], [
      ['PP', 'Prévue portée : une information qui existe dans le document et qui est remplie.'],
      ['PNP', 'Prévue non portée : une information qui existe dans le document mais qui n\\'est pas renseignée.'],
      ['NPP', 'Non prévue portée : une information qui n\\'existe pas dans le document mais qui y est ajoutée à la main.'],
      ['A / AN / N', 'Alphabétique / alphanumérique / numérique'],
      ['D / H', 'Date / heure'],
    ], [20, 80]),
    H3('3.3 Liste des documents'),
    P("Nous avons recensé onze documents utilisés pour la gestion des stagiaires de la formation professionnelle continue. Ils sont numérotés de D1 à D11 dans l'ordre où ils apparaissent dans le diagramme de flux."),
    ...T('Liste des documents', ['N°', 'Désignation du document'], D.map((f) => [f.code, f.titre]), [15, 85], { align: [AlignmentType.CENTER] }),
    H3("3.4 Fiches d'analyse des documents"),
  ];
""" + s[b:]
rep("out.push(new Paragraph({ heading: L.d.HeadingLevel.HEADING_3, pageBreakBefore: true, children: [new TextRun(`3.${i + 1} Document ${f.code} : ${f.titre}`)] }));",
    "out.push(new Paragraph({ pageBreakBefore: i > 0, keepNext: true, spacing: { before: 120, after: 120 }, children: [new TextRun({ text: `Document ${i + 1} : ${f.titre}`, bold: true, size: 24 })] }));")
rep("new TextRun(`3.${D.length + 1} Règles de calcul relevées dans les documents`)", "new TextRun('3.5 Règles de calcul relevées dans les documents')")
rep("H3(`3.${D.length + 2} Synthèse : informations nouvelles pour le système`)", "H3('3.6 Synthèse : informations nouvelles pour le système')")
s = s.replace("tcaption(`Fiche description du document ${f.code}`)", "tcaption(`Fiche d'analyse du document ${f.titre.toLowerCase()}`)")
open(p, 'w', encoding='utf-8').write(s)

# ---------------------------------------------------------------- chapitre II : flux, postes, diagnostic
p = 'content1.js'; s = open(p, encoding='utf-8').read()
def rep2(a, b):
    global s
    assert a in s, a[:90]
    s = s.replace(a, b)
rep2("""    P("Le diagramme de flux d'information donne une vue globale du fonctionnement actuel : il montre les **acteurs** du système existant, les **documents** qu'ils échangent et l'ordre de ces échanges, depuis l'ouverture de la session jusqu'à la délivrance des documents au stagiaire. Le tableau ci-dessous présente les signes utilisés."),""",
"""    P("Le flux d'information représente les échanges d'informations entre les acteurs qui interviennent dans la gestion des stagiaires, ainsi qu'avec leur environnement. Un acteur peut être une personne ou une structure. On distingue :"),
    ...bullets([
      "**Le flux interne** : échange d'informations entre les acteurs internes au champ d'étude (services de l'institut, enseignants).",
      "**Le flux externe** : échange d'informations entre un acteur interne et un acteur externe (stagiaire, plateforme takwin.dz, DFEP, CFPA).",
    ]),
    P("Le diagramme montre les **documents** échangés et l'ordre de ces échanges, depuis l'ouverture de la session jusqu'à la délivrance des documents au stagiaire. Le tableau ci-dessous présente les signes utilisés."),""")
a = s.index("    H2('4. Étude des postes de travail'),"); b = s.index("];\n}\n\nmodule.exports")
POSTES = [
    ('P1', 'Directeur', 'Direction', "Diriger l'institut, valider les délibérations et signer les documents officiels", '01',
     [("Réception et transmission des correspondances de la DFEP", 'Selon besoin'), ('Validation des procès-verbaux des délibérations', 'Chaque semestre'),
      ('Signature des relevés de notes et des attestations', 'Quotidienne'), ("Transmission des listes d'orientation et des bilans", 'Selon besoin')]),
    ('P2', "Agent du service de l'information, de l'orientation et de la numérisation", "Sous-direction de l'information, de l'orientation, de la numérisation et de l'insertion professionnelle",
     "Informer et orienter les candidats, gérer les inscriptions", '[[À COMPLÉTER]]',
     [('Publication du calendrier de la session et des fiches des spécialités', 'Chaque session'), ("Accueil, information et inscription des candidats", 'Quotidienne pendant les inscriptions'),
      ('Organisation des journées de sélection et d\'orientation', 'Chaque session'), ('Transmission de la liste des stagiaires admis', 'Chaque session')]),
    ('P3', 'Agent du service de la formation professionnelle continue', "Sous-direction de l'apprentissage et de la formation professionnelle continue",
     'Organiser la formation des stagiaires des cours du soir et suivre leurs résultats', '[[À COMPLÉTER]]',
     [("Élaboration des emplois du temps", 'Chaque semestre'), ('Élaboration des plannings des examens', 'Chaque semestre'),
      ('Collecte des notes et préparation des délibérations', 'Chaque semestre'), ('Établissement des relevés de notes et des attestations', 'Quotidienne')]),
    ('P4', 'Agent du service de la surveillance générale', "Sous-direction de l'information, de l'orientation, de la numérisation et de l'insertion professionnelle",
     'Suivre les absences et la discipline des stagiaires', '[[À COMPLÉTER]]',
     [("Réception des feuilles d'absences", 'Quotidienne'), ("Suivi des absences et des justificatifs", 'Quotidienne'), ("Établissement de l'état des absences", 'Chaque semestre')]),
    ('P5', 'Enseignant', 'Corps des formateurs', 'Assurer les cours, les évaluations et le suivi des stagiaires', '73',
     [('Cours et travaux pratiques', 'Quotidienne'), ("Appel des stagiaires", 'Chaque séance'), ('Contrôles et examens', 'Chaque semestre'),
      ('Transfert des notes', 'Chaque semestre')]),
]
fiches = []
for i, (code, nom, struct, resp, eff, taches) in enumerate(POSTES, 1):
    fiches.append(f"""    ...posteFiche({i}, {code!r}, {nom!r}, {struct!r}, {resp!r}, {eff!r}, {taches!r}),""")
s = s[:a] + """    H2('4. Étude des postes de travail'),
    H3('4.1 Définition'),
    P("Un poste de travail est l'ensemble des tâches confiées à une personne dans une structure. L'étude des postes de travail permet de connaître les acteurs du système actuel, leurs responsabilités, les tâches qu'ils réalisent et leur fréquence."),
    H3('4.2 Liste des postes de travail'),
    ...T('Liste des postes de travail', ['Code', 'Désignation du poste'], [""" + ', '.join(f"[{c!r}, {n!r}]" for c, n, *_ in POSTES) + """], [15, 85], { align: [AlignmentType.CENTER] }),
    H3('4.3 Fiches d\\'étude des postes'),
""" + '\n'.join(fiches) + """
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
  """ + s[b:]
# fonction de fiche de poste
s = s.replace("const FLUX = [", """function posteFiche(i, code, nom, struct, resp, eff, taches) {
  const { Table, TableRow, TableCell, WidthType, ShadingType, BorderStyle } = L.d;
  const W = L.CONTENT_W;
  const B = { style: BorderStyle.SINGLE, size: 4, color: L.C.grid };
  const bd = { top: B, bottom: B, left: B, right: B };
  const cell = (children, w, fill, span) => new TableCell({ children, columnSpan: span, width: { size: w, type: WidthType.DXA },
    shading: fill ? { type: ShadingType.CLEAR, color: 'auto', fill } : undefined, borders: bd, margins: { top: 50, bottom: 50, left: 110, right: 110 } });
  const par = (t, o = {}) => new Paragraph({ children: L.runs(t, { size: o.size || 21, color: o.color, bold: o.bold }), spacing: { after: 50 } });
  const c1 = Math.round(W * 0.68), c2 = W - c1;
  return [
    new Paragraph({ keepNext: true, pageBreakBefore: i > 1, spacing: { before: 120, after: 120 }, children: [new TextRun({ text: `Fiche ${i} : Fiche d'étude du poste ${nom.toLowerCase()}`, bold: true })] }),
    new Table({ width: { size: W, type: WidthType.DXA }, columnWidths: [c1, c2], rows: [
      new TableRow({ children: [cell([par(`**Fiche d'étude du poste : ${nom}**`, { color: 'FFFFFF' })], W, L.C.head, 2)] }),
      new TableRow({ children: [cell([par('**CARACTÉRISTIQUES**', { size: 18, color: L.C.gold }), par(`**Code poste :** ${code}`), par(`**Désignation du poste :** ${nom}`),
        par(`**Structure de rattachement :** ${struct}`), par(`**Responsabilité du poste :** ${resp}`), par(`**Effectif :** ${eff}`),
        par('**Moyens matériels :** micro-ordinateur et imprimante')], W, L.C.light, 2)] }),
      new TableRow({ children: [cell([par('**Tâche**')], c1, 'D5E9ED'), cell([par('**Fréquence**')], c2, 'D5E9ED')] }),
      ...taches.map(([t, f], k) => new TableRow({ cantSplit: true, children: [cell([par(t)], c1, k % 2 ? 'F3F7FB' : 'FFFFFF'), cell([par(f)], c2, k % 2 ? 'F3F7FB' : 'FFFFFF')] })),
    ] }),
    L.tcaption(`Fiche d'étude du poste ${nom.toLowerCase()}`),
  ];
}

const FLUX = [""", 1)
s = s.replace("['1', \"Correspondance : prolongation des inscriptions\"", "['F1', \"Correspondance : prolongation des inscriptions\"")
s = re.sub(r"\n  \['(\d+)', ", lambda m: f"\n  ['F{m.group(1)}', ", s)
open(p, 'w', encoding='utf-8').write(s)

# ---------------------------------------------------------------- dictionnaire sans classe
p = 'dico_mor.js'; s = open(p, encoding='utf-8').read()
OBS = {'email': 'Unique', 'telephone': 'Unique', 'mot_de_passe': 'Chiffré', 'role': 'stagiaire / enseignant / administration',
       'num_inscription': 'Unique, se termine par 1647', 'mode_formation': 'résidentiel / apprentissage / cours du soir',
       'semestre_courant': '1 à 5', 'etat_session': 'en attente / active / archivée', 'jour': 'samedi à jeudi',
       'statut_presence': 'présent / absent / en retard / excusé', 'type_evaluation': 'contrôle / examen',
       'etat_examen': 'brouillon / soumis / modifié', 'valeur_note': 'Sur 20', 'note_rattrapage': 'Sur 20', 'note_devoir': 'Sur 20',
       'phase': 'avant / après rattrapage', 'resultat': 'admis / rattrapage / abandon', 'coefficient': 'Supérieur à 0',
       'mois_session': 'Février ou septembre', 'duree_mois': '30 mois', 'niveau_qualification': 'Niveau 5',
       'etat_revision': 'en attente / redoublé / passé / exclu', 'cible': 'enseignants / stagiaires / session / spécialités',
       'type_remise': 'en ligne / en présentiel', 'etat_remise': 'en attente / remise / notée / en retard',
       'type_destinataire': 'tous / stagiaires / enseignants / spécialité / individuel', 'numero': 'Unique', 'moyenne': 'Sur 20'}
a = s.index('const DICO = ['); b = s.index('];', a) + 2
import ast
rows = re.findall(r"\['([a-z_]+)', ((?:'[^']*'|\"[^\"]*\")), '([^']*)', '([^']*)'", s[a:b])
lines = ['const DICO = [']
for codev, des, typ, tai in rows:
    lines.append(f"  [{des}, '{typ}', '{tai}', {OBS.get(codev, '')!r}],")
lines.append('];')
s = s[:a] + '\n'.join(lines) + s[b:]
s = s.replace("""    P("Le dictionnaire de données regroupe les informations **utiles (PU)** relevées dans les fiches description des documents D1 à D11, complétées par les données propres au nouveau système (comptes, états, fichiers). La colonne **Source** indique le ou les documents d'où provient chaque donnée ; « Système » désigne une donnée créée par le nouveau système. Les informations calculées (PC) n'y figurent pas : elles sont obtenues par les opérations des classes. Types : **A** alphabétique, **AN** alphanumérique, **N** numérique, **D** date, **H** heure, **DH** date et heure, **B** booléen, **E** énuméré, **T** texte long."),""",
"""    P("Le dictionnaire de données regroupe les informations relevées dans les fiches d'analyse des documents D1 à D11, complétées par les données propres au nouveau système (comptes, états, fichiers). Les informations calculées (moyennes, totaux, nombres d'absences) n'y figurent pas : elles sont obtenues par les opérations des classes. Types : **A** alphabétique, **AN** alphanumérique, **N** numérique, **D** date, **H** heure, **DH** date et heure, **B** booléen, **E** énuméré, **T** texte long."),""")
s = s.replace("...T('Dictionnaire de données', ['Code', 'Désignation', 'Type', 'Taille', 'Classe', 'Source'], DICO, [19, 27, 7, 7, 22, 18],\n      { size: 16, align: [null, null, CEN, CEN] }),",
              "...T('Dictionnaire de données', ['Désignation de la donnée', 'Type', 'Taille', 'Observation'], DICO, [48, 10, 10, 32],\n      { size: 20, align: [null, CEN, CEN] }),")
open(p, 'w', encoding='utf-8').write(s)
print('ok', len(rows))
