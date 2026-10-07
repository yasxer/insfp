p = 'content2.js'; s = open(p, encoding='utf-8').read()
a = s.index("    H4('4.6.1 Dictionnaire de données'),")
b = s.index("    H4('4.6.3 Diagramme de classes'),")
s = s[:a] + """    H4('4.6.1 Dictionnaire de données'),
    ...require('./dico_mor').dictionnaire(),
    H4('4.6.2 Règles de gestion'),
    ...require('./dico_mor').regles(),
""" + s[b:]
a = s.index("    H4('4.7.1 Les règles de passage du MOR'),")
b = s.index("    // ------------------------------------------------------------ 5. Conception de l'application")
s = s[:a] + """    H4('4.7.1 Les règles de passage du MOR'),
    ...require('./dico_mor').morRegles(),
    H4('4.7.2 Schéma du MOR'),
    ...require('./dico_mor').mor(),

""" + s[b:]
s = s.replace("L'utilisateur est une classe abstraite spécialisée en stagiaire, enseignant et administration ;",
              "Les classes et les attributs sont issus du dictionnaire de données, donc des documents étudiés (ex. : les classes Filière, SemestreSession et NoteRattrapage). L'utilisateur est une classe abstraite spécialisée en stagiaire, enseignant et administration ;")
# Jira : vocabulaire français
for a2, b2 in [
    ("*Socle technique & base de données*, *Authentification & sécurité*, *Administration & gestion des comptes* et *Front-end & design*",
     "*Socle technique et base de données*, *Authentification et sécurité*, *Administration et gestion des comptes* et *Interface et conception*"),
    ("['Socle technique & base de données',", "['Socle technique et base de données',"),
    ("['Authentification & sécurité',", "['Authentification et sécurité',"),
    ("['Administration & gestion des comptes',", "['Administration et gestion des comptes',"),
    ("['Front-end & design',", "['Interface et conception',"),
    ("Intégration du layout principal (barre latérale, en-tête, tableau de bord)", "Intégration de la mise en page principale : barre latérale, en-tête et tableau de bord"),
    ("Design responsive des formulaires et des tableaux de données", "Adaptation des formulaires et des tableaux à toutes les tailles d'écran"),
    ("Recette globale, correction des bugs UI et validation finale", "Recette globale, correction des anomalies de l'interface et validation finale"),
    ("Gestion des rôles et autorisations (middlewares administration, enseignant, stagiaire)", "Gestion des rôles et des autorisations : administration, enseignant et stagiaire"),
    ("Conception de la base de données MySQL (MCD / MLD) et création des migrations et modèles", "Conception de la base de données MySQL et création des migrations et des modèles"),
    ("Configuration du système de stockage de fichiers (fichiers et documents)", "Configuration du stockage des fichiers et des documents"),
]:
    s = s.replace(a2, b2)
open(p, 'w', encoding='utf-8').write(s)
print('ok')
