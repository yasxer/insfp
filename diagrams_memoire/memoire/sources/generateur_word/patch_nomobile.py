import re

def sub(path, pairs, regex=False):
    s = open(path, encoding='utf-8').read()
    for a, b in pairs:
        if a not in s:
            print('MISSING in', path, ':', a[:70])
        s = s.replace(a, b)
    open(path, 'w', encoding='utf-8').write(s)

sub('build.js', [("line(\"Conception et réalisation d'une plateforme web et mobile\"", "line(\"Conception et réalisation d'une plateforme web\"")])

sub('content1.js', [
    ("**la conception et la réalisation d'une plateforme web et mobile de gestion intégrée de l'INSFP**, composée d'une application web destinée à l'administration, aux enseignants et aux stagiaires, et d'une application mobile destinée aux stagiaires.",
     "**la conception et la réalisation d'une plateforme web de gestion intégrée de l'INSFP**, destinée à l'administration, aux enseignants et aux stagiaires."),
    ("La plateforme web et mobile de l'INSFP doit permettre de :", "La plateforme web de l'INSFP doit permettre de :"),
    ("ses documents depuis le web ou le mobile.", "ses documents en ligne, depuis un ordinateur, une tablette ou un smartphone."),
    ("'Consultation en ligne sur le web et le mobile, à tout moment'", "'Consultation en ligne, à tout moment et depuis tout appareil'"),
])

sub('content2.js', [
    ("      \"**Application mobile** : application installée sur un smartphone ; la nôtre est développée avec Flutter à partir d'un code source unique (Android, iOS, Windows).\",\n", ""),
    ("la couche présentation (web, mobile)", "la couche présentation (application web)"),
    ("(Laravel côté serveur, Vue.js côté web, Flutter côté mobile)", "(Laravel côté serveur, Vue.js côté client)"),
    ("H4('d) Espace stagiaire (web et mobile)')", "H4('d) Espace stagiaire')"),
    ("accès 24 h / 24 depuis le web et le mobile.", "accès 24 h / 24 depuis un navigateur web."),
    ("      \"**Portabilité** : application mobile multiplateforme (Flutter).\",\n",
     "      \"**Portabilité** : interface adaptée aux ordinateurs, aux tablettes et aux smartphones.\",\n"),
    ("séparation backend / frontend / mobile", "séparation entre le serveur et l'interface"),
    ("'Espace enseignant, application mobile et chatbot'", "'Espace enseignant et assistant intelligent'"),
    ("      ['S5', 'Application mobile Flutter', '20/03/2026', '15/04/2026', '27'],\n", ""),
    ("H2(\"5. Conception de l'application (web et mobile)\")", "H2(\"5. Conception de l'application web\")"),
    ("Elle est commune à l'application web et à l'application mobile.", "Elle s'applique à l'ensemble de l'application web."),
    ("la barre latérale se replie sur petit écran ; l'application mobile reprend les mêmes couleurs ;", "la barre latérale se replie sur petit écran ;"),
])
s = open('content2.js', encoding='utf-8').read()
s = re.sub(r"    \.\.\.wf\('wf_14_mobile\.png'.*?\n", '', s)
open('content2.js', 'w', encoding='utf-8').write(s)

sub('content3.js', [
    ("l'application web Vue.js (SPA) et l'application mobile Flutter, qui consomment l'API.", "l'application web Vue.js (SPA), exécutée dans le navigateur, qui consomme l'API."),
    ("ce qui évite d'exposer un jeton au code JavaScript ; l'application mobile utilise un **jeton d'API Sanctum** transmis dans l'en-tête `Authorization`.",
     "ce qui évite d'exposer un jeton au code JavaScript."),
    ("      ['Dart / Flutter', 'Dart 3', 'Application mobile multiplateforme (Dio, Provider)'],\n", ""),
    ("      ['Flutter SDK / Android Studio', \"Compilation et test de l'application mobile\"],\n", ""),
    ("    H4('Application mobile'),\n    note(\"[[À COMPLÉTER : insérer les captures de l'application mobile Flutter (connexion, tableau de bord, emploi du temps, remise d'un devoir)]]\", 'todo'),\n", ""),
    ("H3(\"5.2 Hébergement de l'application web et mobile\")", "H3(\"5.2 Hébergement de l'application web\")"),
    ("      \"**Application mobile** : génération de l'APK / AAB avec `flutter build` et configuration de l'adresse de l'API de production ; diffusion interne ou via le Play Store.\",\n", ""),
    ("      ['Smartphone', 'Application Flutter — Android', '☐ Conforme'],\n      ['Ordinateur', 'Application Flutter — Windows', '☐ Conforme'],\n",
     "      ['Smartphone', 'Chrome Android (affichage adaptatif)', '☐ Conforme'],\n"),
    ("['Consultation (web et mobile)', 'Stagiaires', '', '']", "['Consultation des informations', 'Stagiaires', '', '']"),
    ("reposant sur Laravel, Vue.js, Flutter et MySQL", "reposant sur Laravel, Vue.js et MySQL"),
    ("une plateforme web et mobile de gestion intégrée", "une plateforme web de gestion intégrée"),
    ("Vue.js et Tailwind CSS pour l'interface web, Flutter pour l'application mobile et MySQL pour les données",
     "Vue.js et Tailwind CSS pour l'interface web et MySQL pour les données"),
    ("      \"Ajouter des **notifications push** sur mobile (nouvelle note, changement d'emploi du temps, rappel de devoir).\",\n      \"Étendre l'application mobile à l'**espace enseignant** (appel et saisie des notes).\",\n",
     "      \"Développer une **application mobile** pour les stagiaires et les enseignants, avec des notifications instantanées (nouvelle note, changement d'emploi du temps, rappel de devoir).\",\n"),
    ("      \"Documentation officielle de Flutter et Dart — https://docs.flutter.dev/\",\n", ""),
])
print('done')
