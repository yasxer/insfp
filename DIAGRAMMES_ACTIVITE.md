# Diagrammes d'activité — Plateforme INSFP

Les images se trouvent dans `diagrams_memoire/` (`activite_N_*.png` pour Word, `.svg` en vectoriel).
Elles sont générées par `diagrams_memoire/generer_diagrammes_activite.py`. Pour modifier un diagramme, éditez ce script puis relancez-le :

```bash
python diagrams_memoire/generer_diagrammes_activite.py diagrams_memoire "<chemin de chrome-headless-shell.exe>"
```

## Notation UML utilisée

| Élément | Symbole | Rôle |
|---|---|---|
| Nœud initial | ● | Début de l'activité |
| Nœud final d'activité | ◉ | Fin de toute l'activité |
| Nœud final de flot | ⊗ | Fin d'une seule branche (refus, notification) sans arrêter le reste |
| Action | rectangle arrondi | Tâche réalisée par un acteur ou par le système |
| Décision / fusion | ◇ | Choix selon une **garde** `[condition]` ; la fusion réunit les branches (et les boucles) |
| Fourche (fork) | barre noire | Démarre des flots **parallèles** |
| Couloirs (swimlanes) | colonnes | Indiquent **qui** réalise chaque action |
| Cadre `act` | cartouche en haut à gauche | Nom de l'activité |

Règles respectées : une seule entrée par action (les boucles repassent par un nœud de fusion) ; des gardes courtes et exclusives sur chaque sortie de décision ; les contrôles d'autorisation et de validation du backend sont représentés comme des décisions du **Système**.

## Liste des diagrammes

| # | Fichier | Processus | Couloirs |
|---|---|---|---|
| 1 | `activite_1_inscription` | Génération du numéro, inscription, approbation ou rejet | Administration, Stagiaire, Système |
| 2 | `activite_2_authentification` | Connexion par e-mail ou n° d'inscription, compte en attente, complétion du profil | Utilisateur, Système |
| 3 | `activite_3_session_passage_semestre` | Création et activation d'une session, passage automatique de semestre, révisions | Administration, Système |
| 4 | `activite_4_examens_notes` | Création d'un examen (contrôle / examen), notification, soumission, saisie des notes | Enseignant, Système, Stagiaire |
| 5 | `activite_5_deliberation` | Calcul des moyennes pondérées, proposition du résultat, validation | Administration, Système, Stagiaire |
| 6 | `activite_6_emploi_du_temps` | Saisie des séances, détection des conflits, publication | Administration, Système |
| 7 | `activite_7_presence` | Appel d'une séance et enregistrement des présences | Enseignant, Système |
| 8 | `activite_8_devoirs` | Création d'un devoir, remise en ligne, notation | Enseignant, Système, Stagiaire |

## Règles de gestion illustrées

- **Numéro d'inscription** : 4 chiffres de séquence + session (1 = Février, 2 = Septembre) + 2 chiffres de l'année + type d'étude (P/C/A) + code wilaya 16 + code INSFP 47. Un numéro rejeté redevient disponible.
- **Connexion** : un compte non approuvé est refusé ; un stagiaire sans date de naissance ni adresse doit d'abord compléter son profil.
- **Sessions** : seulement en Février ou en Septembre ; durée de 30 mois ; activation possible seulement à partir du mois de début. L'activation archive la session précédente et lance le passage de semestre.
- **Passage de semestre** : admis → semestre suivant (ou diplômé au dernier semestre) ; ajourné → révision manuelle (redoubler, passer, exclure ou reporter) ; aucune délibération → rien ne change.
- **Examens** : deux types seulement (contrôle, examen). Statut : brouillon → soumis → modifié. Seul l'enseignant affecté au module peut créer l'examen ou saisir ses notes, et seulement pour les stagiaires de la spécialité et du semestre de l'examen.
- **Délibération** : moyenne du module = moyenne de ses notes ; moyenne générale = moyennes des modules pondérées par les coefficients ; admis si la moyenne est ≥ 10.
- **Emploi du temps** : refus si l'enseignant ou le groupe a déjà une séance qui chevauche le créneau (même jour, même année académique).
