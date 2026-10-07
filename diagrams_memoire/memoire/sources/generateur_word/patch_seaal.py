import re

def edit(p, fn):
    s = open(p, encoding='utf-8').read(); s2 = fn(s)
    open(p, 'w', encoding='utf-8').write(s2)

# 1. tableau des flux : N° + document transmis
def f1(s):
    a = '''    ...T("Description des flux d'information", ['N°', 'Flux', 'Émetteur', 'Récepteur', 'Document'], FLUX, [6, 40, 21, 21, 12],'''
    assert a in s
    i = s.index(a); j = s.index('\n', s.index('\n', i) + 1) + 1
    return s[:i] + """    ...T("Description des flux d'information", ['N° de flux', 'Document transmis'],
      FLUX.map((f) => [f[0], f[4] !== '—' ? `${f[1]} (${f[4]})` : f[1]]), [18, 82], { align: [AlignmentType.CENTER] }),
""" + s[j:]
edit('content1.js', f1)

# 2. dictionnaire : désignation, type, taille
def f2(s):
    s = re.sub(r"(\n  \[(?:\"[^\"]*\"|'[^']*'), '[^']*', '[^']*'), (?:'[^']*'|\"[^\"]*\")\],", r"\1],", s)
    a = "...T('Dictionnaire de données', ['Désignation de la donnée', 'Type', 'Taille', 'Observation'], DICO, [48, 10, 10, 32],"
    assert a in s
    s = s.replace(a, "...T('Dictionnaire de données', ['Désignation de la donnée', 'Type', 'Taille'], DICO, [64, 18, 18],")
    s = s.replace(" (règles R1 et R2)", "").replace(" (règle R3)", "")
    return s
edit('dico_mor.js', f2)

# 3. diagramme de classes complet sur une page A3
def f3(s):
    a = s.index("    P(\"Le diagramme de classes est présenté en deux niveaux")
    b = s.index("    ...figure('memoire/classes/diagramme_classes_enumerations.png'")
    return s[:a] + """    P("Le diagramme de classes ci-dessous présente l'ensemble des classes du système avec leurs attributs, leurs opérations et leurs associations. Les classes et les attributs sont issus du dictionnaire de données, donc des documents étudiés. Les clés étrangères n'y figurent pas : les liens sont exprimés par des associations et leurs multiplicités. L'utilisateur est une classe abstraite spécialisée en stagiaire, enseignant et administration ; les compositions (losange plein) indiquent les parties qui n'existent pas sans leur tout. Pour rester lisible, le diagramme est présenté sur une page au format A3."),
    { __a3: ['memoire/classes/diagramme_classes_complet.png', 'Diagramme de classes de la plateforme'] },
""" + s[b:]
edit('content2.js', f3)

# 4. suppression des parties en trop : règles de calcul, synthèse, correspondance MOR/tables
def f4(s):
    a = s.index("  out.push(\n    new Paragraph({ heading: L.d.HeadingLevel.HEADING_3, pageBreakBefore: true, children: [new TextRun('3.5 Règles")
    b = s.index("  return out;", a)
    return s[:a] + s[b:]
edit('docs_section.js', f4)

def f5(s):
    a = s.index("    P(\"Le framework Laravel impose des conventions de nommage en anglais")
    b = s.index("    H3('3.1 Création de la base de données'),")
    return s[:a] + s[b:]
edit('content3.js', f5)
print('ok')
