# Scénario de démonstration — INSFP

Réinitialiser les données (à faire avant chaque répétition et avant la soutenance) :

```bash
cd backend
php artisan migrate:fresh --seed
```

Tous les comptes ont le mot de passe **`password`**.

## Situation de départ

- **Session Février 2026** : en cours, fin de semestre. Contrôles 1 et 2 et examens finaux notés et publiés.
- **Session Septembre 2026** : en attente. Elle peut être activée (bandeau « Activer » dans *Sessions*).
- Pour la nouvelle promotion : 4 inscriptions en attente de validation et 10 numéros d'inscription libres.

| Promotion (session d'entrée) | Spécialité | Semestre | Stagiaires | État |
|---|---|---|---|---|
| Septembre 2023 | Systèmes et Réseaux (ASR) | — | 8 | Diplômés |
| Septembre 2024 | Bases de Données (BDD) | S4 | 10 | Délibérés, tous admis |
| Février 2025 | Maintenance (MNT), apprentissage | S3 | 8 | Délibérés, tous admis. Cours 2 jours par semaine |
| Septembre 2025 | Développement Web et Mobile (DWM), groupes G1 et G2 | S2 | 14 | Rattrapage terminé, délibération à faire |
| Septembre 2025 | Systèmes et Réseaux (ASR) | S2 | 12 | **Rattrapage à noter en direct** |
| Février 2026 | Sécurité Informatique (SEC) | S1 | 10 | Un examen à soumettre (Python) |

Pour chaque promotion en formation :
- emploi du temps publié (dimanche → jeudi, sans conflit de formateur ni de salle) ;
- présences sur 7 semaines ;
- supports de cours (PDF téléchargeables) ;
- devoirs : un corrigé et un ouvert jusqu'au 18/10.

## Comptes

| Rôle | Identifiant | Intérêt |
|---|---|---|
| Administration | `admin@insfp.dz` | Nadia Hamidi, directrice des études |
| Formatrice | `leila.saidi@insfp.dz` | Rattrapage ASR à noter (Linux + Windows Server) |
| Formatrice | `lamia.kaci@insfp.dz` | Examen Python (SEC S1) en brouillon, à soumettre |
| Stagiaire | `amine.belkacem@stagiaire.insfp.dz` | DWM : 9,07 → 10,54, admis après rattrapage |
| Stagiaire | `walid.hamidi@stagiaire.insfp.dz` | DWM : ajourné après rattrapage, assiduité 66 % |
| Stagiaire | `yasmine.ouali@stagiaire.insfp.dz` | DWM : meilleure moyenne |
| Stagiaire | `sofiane.lounis@stagiaire.insfp.dz` | ASR : en attente de rattrapage (9,62) |
| Stagiaire | `rania.touati@stagiaire.insfp.dz` | ASR : en attente de rattrapage (8,81), assiduité 63 % |
| Stagiaire | `meriem.rahmani@stagiaire.insfp.dz` | BDD S4 : très bonne stagiaire, déjà délibérée |

- Les autres formateurs suivent le format `prenom.nom@insfp.dz` : karim.benali, samira.haddad, nadia.cherif, farid.khelifi, etc.
- Un stagiaire peut aussi se connecter avec son numéro d'inscription (par exemple `0001225P1647`).

## Déroulé proposé

1. **Landing page** : changer de langue (FR / ع / EN), ouvrir l'assistant IA.
2. **Inscription** : créer un compte avec un numéro libre de la Session Septembre 2026, par exemple `0005226P1647` (DWM, présentiel). L'administration le valide ensuite dans *Stagiaires → Inscriptions en attente*.
3. **Administration** : tableau de bord, sessions, spécialités (programme sur 5 semestres), formateurs, emploi du temps global de la Session Février 2026.
4. **Stagiaire Amine** : emploi du temps, notes, présences, devoirs, cours.
5. **Rattrapage en direct**, avec Leila Saidi :
   - dans *Examens et notes*, ouvrir « Rattrapage — Administration Linux » ;
   - seuls Sofiane et Rania apparaissent : c'est la règle « moyenne < 10 et module < 10 » ;
   - saisir Sofiane **13**, Rania **9**, enregistrer, puis cliquer *Soumettre à l'administration* ;
   - faire de même pour « Rattrapage — Windows Server » : Sofiane **12**, Rania **8**.
6. **Délibérations** (admin) :
   - choisir la session **Septembre 2025**, la spécialité ASR, le semestre 2 ;
   - résultat : Sofiane passe de 9,62 à 10,73, admis après rattrapage ; Rania passe de 8,81 à 9,12, ajournée ;
   - confirmer les moyennes ;
   - pour comparer, montrer aussi DWM, où le rattrapage est déjà terminé.
7. **Activer la Session Septembre 2026** (*Sessions → Activer*), **en dernier** :
   - les stagiaires délibérés admis passent au semestre suivant ;
   - les ajournés arrivent dans *Passages* ;
   - la nouvelle promotion entre en S1.

> ⚠️ Après l'activation, les nouveaux semestres n'ont pas encore d'emploi du temps (il se construit dans *Emplois du temps*). Garder cette étape pour la fin, puis relancer `php artisan migrate:fresh --seed` pour revenir à l'état de départ.
