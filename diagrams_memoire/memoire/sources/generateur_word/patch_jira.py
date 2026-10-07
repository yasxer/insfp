p = 'content2.js'; s = open(p, encoding='utf-8').read()
a = "    ...figure('memoire/uml/gantt_sprints.png', 'Diagramme de planification des sprints'),\n"
assert a in s
jira = a + """    H4('5. Suivi du projet avec Jira Software'),
    P("Pour organiser et suivre le travail de l'équipe, nous avons utilisé **Jira Software** (Atlassian), l'outil de gestion de projet agile recommandé. Les étapes de mise en place sont présentées ci-dessous."),
    P("**Étape 1 — Création de l'espace de travail.** Après la connexion, la page d'accueil de Jira permet de rechercher ou de créer un espace (projet)."),
    ...figure('memoire/jira/img01.png', "Page d'accueil Jira et espace de travail", { maxW: 560 }),
    P("**Étape 2 — Choix du modèle.** Parmi les modèles proposés (Scrum, Kanban, gestion des services…), nous avons retenu le modèle **Scrum**, adapté à un développement par sprints."),
    ...figure('memoire/jira/img02.png', 'Sélection du modèle de projet dans Jira', { maxW: 560 }),
    ...figure('memoire/jira/img03.png', 'Aperçu du modèle Scrum et validation', { maxW: 560 }),
    P("**Étape 3 — Paramétrage du projet.** Le projet est nommé **« INFSP PROJET »**, avec la clé **IP** qui préfixe tous les tickets (IP-10, IP-11…), en accès privé et géré par l'équipe."),
    ...figure('memoire/jira/img04.png', 'Configuration et paramétrage du projet « INFSP PROJET »', { maxW: 560 }),
    P("**Étape 4 — Initialisation du backlog.** Jira crée l'espace **Backlog** et un premier sprint vide, prêt à recevoir les tickets."),
    ...figure('memoire/jira/img05.png', "Initialisation de l'espace Backlog et du premier sprint", { maxW: 560 }),
    P("**Étape 5 — Structuration en sprints.** Le travail est découpé en quatre sprints thématiques : *Socle technique & base de données*, *Authentification & sécurité*, *Administration & gestion des comptes* et *Front-end & design*."),
    ...figure('memoire/jira/img06.png', 'Structuration du projet en quatre sprints thématiques', { maxW: 560 }),
    P("**Étape 6 — Alimentation du backlog.** Chaque user story est saisie sous forme de ticket, puis affectée à un sprint et à un membre de l'équipe."),
    ...figure('memoire/jira/img07.png', 'Alimentation du backlog et répartition des tickets par sprint', { maxW: 560 }),
    P("**Étape 7 — Suivi de l'avancement.** La vue *Liste* regroupe tous les tickets avec leur responsable, leur priorité et leur état ; à la fin du projet, tous les tickets sont à l'état **Terminé**."),
    ...figure('memoire/jira/img08.png', "Vue globale des tickets et suivi de l'avancement", { maxW: 560 }),
    ...T('Tickets Jira par sprint', ['Sprint', 'Ticket', 'Intitulé', 'Responsable'], [
      ['Socle technique & base de données', 'IP-13', 'Initialisation du projet Laravel', 'LASSEL Abdelmalek'],
      ['', 'IP-14', 'Conception de la base de données MySQL (MCD / MLD) et création des migrations et modèles', 'CHRMAT Khaled'],
      ['', 'IP-15', 'Configuration du système de stockage de fichiers (fichiers et documents)', 'CHRMAT Khaled'],
      ['Authentification & sécurité', 'IP-10', 'Module de connexion, déconnexion et réinitialisation du mot de passe', 'HAOUES Yasser'],
      ['', 'IP-11', 'Gestion des rôles et autorisations (middlewares administration, enseignant, stagiaire)', 'HAOUES Yasser'],
      ['', 'IP-12', 'Espace profil utilisateur et sécurisation des accès', 'HAOUES Yasser'],
      ['Administration & gestion des comptes', 'IP-19', 'Espace administration : validation et activation des nouveaux comptes', 'HAOUES Yasser'],
      ['', 'IP-20', 'Interface de gestion et de modification du profil utilisateur', 'CHRMAT Khaled'],
      ['Front-end & design', 'IP-16', 'Intégration du layout principal (barre latérale, en-tête, tableau de bord)', 'LASSEL Abdelmalek'],
      ['', 'IP-17', 'Design responsive des formulaires et des tableaux de données', 'LASSEL Abdelmalek'],
      ['', 'IP-18', 'Recette globale, correction des bugs UI et validation finale', 'LASSEL Abdelmalek'],
    ], [26, 10, 44, 20], { align: [null, AlignmentType.CENTER] }),
"""
s = s.replace(a, jira)
open(p, 'w', encoding='utf-8').write(s)
print('ok')
