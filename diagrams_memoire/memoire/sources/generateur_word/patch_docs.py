p = 'content1.js'; s = open(p, encoding='utf-8').read()
a = s.index("    H2('3. Étude des documents'),"); b = s.index("    H2('4. Étude des postes de travail'),")
s = s[:a] + "    H2('3. Étude des documents'),\n    ...require('./docs_section').etudeDocuments(),\n" + s[b:]

old_postes = s[s.index("      ['Agent de scolarité'"):s.index("    ], [22, 55, 23]),")]
s = s.replace(old_postes, """      ['Agent de scolarité', "Réception des dossiers d'inscription, tenue des listes, orientations, établissement des relevés et attestations", 'D4, D5'],
      ['Responsable pédagogique', "Répartition des enseignants, élaboration des emplois du temps et des plannings d'examens", 'D1, D2, D3'],
      ['Enseignant / formateur', 'Cours, appel, contrôles et examens, remise des notes, signature du PV', 'D1, D2, D3'],
      ['Bureau des examens', 'Collecte des notes, calcul des moyennes, préparation des délibérations', 'D3, D5'],
      ['Direction', 'Validation des délibérations, signature des relevés', 'D3, D5'],
""")

a = s.index("    H3(\"1.1 Historique de l'entreprise\"),"); b = s.index("    H3(\"1.2 Missions de l'organisme d'accueil\"),")
s = s[:a] + '''    H3("1.1 Historique de l'entreprise"),
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
''' + s[b:]
open(p, 'w', encoding='utf-8').write(s)

p = 'content2.js'; s = open(p, encoding='utf-8').read()
def repl_line(s, start, new):
    i = s.index(start); j = s.index('\n', i) + 1
    return s[:i] + new + s[j:]
s = repl_line(s, '      "La moyenne d\'un module est la moyenne de ses notes du semestre',
    '      "La moyenne d\'un module est égale à (contrôle 1 + contrôle 2 + 2 × examen) / 4 ; la moyenne du semestre est la somme des moyennes des modules multipliées par leurs coefficients, divisée par la somme des coefficients (règles R1 et R2 de l\'étude des documents).",\n')
s = repl_line(s, '      "Un stagiaire ajourné passe le rattrapage',
    '      "Avant rattrapage, le stagiaire est **admis** si sa moyenne est supérieure ou égale à 10 ; sinon il passe le **rattrapage** uniquement dans les modules dont la moyenne est inférieure à 10 ; un stagiaire sans aucune note est déclaré en **abandon** (règle R3). Le rattrapage n\'est pas un type d\'évaluation.",\n')
old = "**admis** si la moyenne est supérieure ou égale à 10, **ajourné** sinon. Le rattrapage n'est pas un type d'évaluation : il concerne le stagiaire ajourné, uniquement dans les modules où sa note est inférieure à 10."
assert old in s
s = s.replace(old, "**admis** si la moyenne est supérieure ou égale à 10 ; sinon le stagiaire passe le **rattrapage**, uniquement dans les modules dont la moyenne est inférieure à 10. Le rattrapage n'est pas un type d'évaluation.")
old = 'P("Types : **AN** alphanumérique'
assert old in s
s = s.replace(old, 'P("Le dictionnaire de données a été construit à partir des rubriques des documents étudiés (chapitre II, section 3), complétées par les données propres au nouveau système (comptes, mots de passe, états). Types : **AN** alphanumérique')
open(p, 'w', encoding='utf-8').write(s)
print('ok')
