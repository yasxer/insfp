# Cahier des Charges

## 1. Présentation du Projet
Ce document décrit les spécifications générales d'une application intégrée destinée à gérer toutes les activités de l'institut formateur (INSFP). Son but est de fournir une solution numérique complète et centralisée couvrant la gestion administrative, pédagogique, et la communication interne.

## 2. Problématique
Actuellement, la gestion des activités de l'institut (inscriptions, assiduité, plannings, évaluations) se fait souvent de manière classique (sur papier ou fichiers séparés peu synchronisés). 
Cela soulève d'importants défis au quotidien :
* **Perte de temps et lourdeur administrative** : Saisies répétitives et délais longs pour traiter les dossiers et les notes.
* **Accès limité à l'information** : Les étudiants et professeurs ont du mal à consulter rapidement leurs emplois du temps ou leurs notes à distance.
* **Difficulté de communication** : Manque d'un canal direct et officiel pour la communication entre professeurs, administration, et étudiants.
* **Risques d'erreurs** : Dispatching manuel des salles de cours et gestion des données favorisant les chevauchements ou les pertes d'archives.
C’est ainsi qu'a émergé le besoin de développer une plateforme web unifiée accompagnée d'une application mobile, capable de résoudre toutes ces contraintes et de fluidifier l'échange d’informations.

## 3. Objectifs Visés
* Numériser, centraliser et automatiser les processus manuels et fastidieux de l'institut.
* Assurer une meilleure traçabilité (historique des notes, absences, documents).
* Faciliter la communication à travers une interface dédiée (messagerie / alertes).
* Garantir l'accessibilité à l'information n'importe où et n'importe quand grâce à des interfaces web et mobiles adaptées.

## 4. Périmètre du Projet (Scope)
L'application couvrira les modules principaux suivants :
* **Gestion des Utilisateurs** : Inscriptions, authentification (login/mot de passe), et gestion des habilitations (rôles).
* **Gestion de la Pédagogie** : Modules, filières, spécialités et groupes (sections).
* **Gestion des Plannings** : Emplois du temps interactifs des classes, professeurs, et suivi de l’affectation des salles.
* **Gestion des Évaluations** : Saisie et consultation des notes, examens, bulletins de fin de cycle.
* **Espace de Communication** : Messagerie interne ou système de diffusions/notifications (administration vers formateurs/stagiaires).

## 5. Acteurs et Rôles
Les utilisateurs du système se divisent en plusieurs profils :
1. **Administrateur** : Contrôle total, vue globale du système, et gestion de tous les autres comptes.
2. **Professeurs / Formateurs** : Carnet de notes interactif, suivi des présences, et consultation de leurs emplois du temps.
3. **Étudiants / Stagiaires** : Accès personnel pour consulter les emplois du temps, relevés de notes, et réception de notifications importantes (Web et Mobile).
4. **Personnel Administratif** : Gestion courante (scolarité, attestations).

## 6. Fonctionnalités Principales (Exigences Fonctionnelles)
* **Système d'Authentification** : Sécurisé par rôles (JWT / tokens).
* **Tableau de Bord (Dashboard)** : Vue synthétique et statistiques, différente pour un étudiant, un professeur et l'administration.
* **Opérations sur les données (CRUD)** : Ajout, modification, suppression pour le personnel habilité.
* **Génération et Export** : Capacité de générer des PDF (attestations, relevés) ou de faire de l'export/import Excel pour les listes.
* **Notifications Push** : Déclenchement d’alertes lors de l'ajout d'une nouvelle note ou d'une modification d'emploi du temps.

## 7. Technologies Utilisées (Stack Technique)
L'architecture de l'application sera découpée en Micro-services / API pour une meilleure performance et évolutivité :
* **Backend (API + Logique métier)** : **Laravel (PHP)** - Robuste, sécurisé et idéal pour créer une API RESTful solide.
* **Base de Données** : **MySQL** - Pour un stockage relationnel fortement structuré des entités de l'institut.
* **Frontend Web (Administration et Bureautique)** : **Vue.js** (vite.js) couplé à **Tailwind CSS** pour l’UI, assurant des interfaces modernes, réactives (Single Page Application).
* **Application Mobile (Étudiants et Professeurs)** : **Flutter (Dart)** - Pour une compilation native Android / iOS à partir d'un seul code source.

## 8. Plan de Travail (Méthodologie de réalisation)
La réalisation suivra une méthodologie itérative divisée en différents "sprints" (phases) :
* **Phase 1 : Analyse et Conception** 
  * Étude détaillée, modélisation de la base de données (MLD/MCD). 
  * Création des maquettes graphiques (Wireframes/UI).
* **Phase 2 : Développement Backend (Laravel)**
  * Configuration de l'environnement, développement de la base de données et des routes de l'API (Endpoints).
* **Phase 3 : Développement Frontend (Vue.js)**
  * Connexion de l'interface Web à l'API, développement des dashboards administrateur et de la logique côté client.
* **Phase 4 : Développement Mobile (Flutter)**
  * Création des écrans mobile, liaison avec l'authentification et l'API pour les espaces professeurs/stagiaires.
* **Phase 5 : Tests et Validation**
  * Tests d'intégration, vérification de la sécurité, tests de charge.
* **Phase 6 : Déploiement et Formation**
  * Mise en production sur serveur (Hébergement), et rédaction du manuel utilisateur ou session de formation du personnel.

## 9. Livrables Attendus
* Le code source structuré et documenté.
* La base de données complète et peuplée (données de test).
* Un manuel d'utilisation / d'installation clair.
* Plateforme entièrement déployée et fonctionnelle.
