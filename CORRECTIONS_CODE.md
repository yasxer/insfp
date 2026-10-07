# Corrections à apporter au code — Plateforme INSFP

Cette liste regroupe :

- les **bugs** trouvés en testant l'application et en relisant le code ;
- les **écarts** entre les documents réels de l'institut (D1 à D5, voir le mémoire, chapitre II, section 3) et la base de données ou le code.

Une fois ces points corrigés, l'application, la base de données et le mémoire seront cohérents.

**Priorités :**
- 🔴 bloquant : une fonctionnalité ne marche pas.
- 🟠 important : le résultat est faux par rapport aux règles de l'institut.
- 🟡 amélioration.

---

## A. Bugs

### A1. 🔴 La présence de l'enseignant ne s'enregistre pas
- **Constat** : le frontend envoie `POST /api/teacher/attendance/sessions/{scheduleId}`, mais le backend n'a que `POST /teacher/attendance`, sans `{schedule}`. Or `TeacherAttendanceController::store(Request $request, Schedule $schedule)` attend une séance. On obtient donc une erreur 404 / 405.
- **Fichiers** :
  - `frontend/src/api/endpoints/teacherPortal.js:82`
  - `backend/routes/api.php:106`
- **Correction** : dans `api.php`, remplacer la route par
  ```php
  Route::post('/attendance/sessions/{schedule}', [TeacherAttendanceController::class, 'store']);
  ```
- **Test** : un enseignant fait l'appel d'une séance → les présences apparaissent chez le stagiaire (page Assiduité).

### A2. 🔴 L'application mobile ne peut pas se connecter
- **Constat** : l'app Flutter lit `response['token']` puis envoie `Authorization: Bearer …`. Depuis le passage au cookie httpOnly, `AuthController::login` ne renvoie plus de jeton. Le mobile affiche donc « Invalid response from server ».
- **Fichiers** :
  - `mobile/lib/services/auth_service.dart:54`
  - `mobile/lib/services/api_service.dart:22`
  - `backend/app/Http/Controllers/Api/AuthController.php` (méthode `login`)
- **Correction** : ajouter une route dédiée au mobile, par exemple `POST /api/mobile/login`, qui renvoie un jeton Sanctum :
  ```php
  $token = $user->createToken('mobile')->plainTextToken;
  ```
  Ajouter aussi `POST /api/mobile/logout` qui supprime ce jeton (`currentAccessToken()->delete()`). Mettre à jour `ApiConfig.login` dans l'app. Le web garde le cookie httpOnly.
- **Test** : se connecter depuis l'app Android ou Windows avec un stagiaire approuvé.

### A3. 🔴 Le tableau de bord de l'enseignant affiche 0 partout
- **Constat** : `TeacherController::dashboard()` appelle `$user->teacher()->with('specialty')`, mais le modèle `Teacher` n'a pas de relation `specialty` : une exception est levée et le front affiche les valeurs par défaut (0). Constaté avec `prof.3`, qui a 5 modules mais voit « 0 ».
- **Fichiers** :
  - `backend/app/Http/Controllers/Api/TeacherController.php:32` (même problème dans `profile()`, ligne 113)
  - `backend/app/Models/Teacher.php`
- **Correction** : retirer `with('specialty')`, ou ajouter la relation dans le modèle (et la colonne `specialty_id` si elle est voulue). Même chose pour `canTeachModules` (relation inexistante).
- **Test** : `GET /api/teacher/dashboard` renvoie 200 et `modules_count` = 5 pour `prof.3`.

### A4. 🟠 La déconnexion (Logout) ne fonctionne pas depuis la barre latérale
- **Constat** : au clic sur « Logout » (admin), aucune requête `/api/logout` n'est partie et l'utilisateur est resté connecté. Le code `Sidebar.vue → authStore.logout()` paraît correct. Le bouton est peut-être recouvert : en bas de la barre latérale, l'entrée « Profile » est coupée et chevauche la zone utilisateur.
- **Fichiers** :
  - `frontend/src/components/layout/Sidebar.vue:198` et son template (bas de la barre)
  - `frontend/src/stores/auth.js:115`
- **Correction** : vérifier le chevauchement (menu défilant avec `overflow-y-auto`, zone utilisateur fixe en bas). Vérifier aussi que la requête part avec le cookie XSRF.
- **Test** : clic sur Logout → requête 200, retour à `/login`, puis `/admin/dashboard` redirige vers `/login`.

### A5. 🟠 Boucle infinie dans le graphique du tableau de bord admin
- **Constat** : la console affiche « Maximum recursive updates exceeded in component `<StudentBarChart>` ». Un `watchEffect` modifie une donnée qu'il lit lui-même.
- **Fichier** : `frontend/src/views/admin/components/StudentBarChart.vue:21` et `:28`
- **Correction** : remplacer les `watchEffect` par un `computed` pour les données du graphique, ou par un `watch` ciblé sur les props, sans réécrire la source observée.
- **Test** : plus aucune erreur dans la console sur `/admin/dashboard`.

### A6. 🟡 Heure de fin des séances fausse dans la présence
- **Constat** : `end_time` est calculée comme `start_time + 1 h` au lieu d'utiliser `schedules.end_time`.
- **Fichier** : `backend/app/Http/Controllers/Api/TeacherAttendanceController.php:93` et `:175`
- **Correction** : `Carbon::parse($schedule->end_time)->format('H:i')`.

### A7. 🟡 Données de démonstration non conformes
- **Constat** : les seeders créent des spécialités `DEV`, `DBA`, `CYB`, `RSD`, avec des matricules `REG-2026-x`. Les spécialités réelles sont **SDWM** et **SASRI**, avec des matricules du type `0217124S1647`.
- **Fichiers** : `backend/database/seeders/*.php`
- **Correction** : aligner les seeders sur les vraies spécialités, les vrais modules (voir D2 et D3) et le vrai format de matricule. Les captures du mémoire seront alors réalistes.

---

## B. Écarts avec les documents de l'institut (règles métier)

### B1. 🟠 Calcul de la moyenne d'un module (règle R1)
- **Constat** : la moyenne du module est une moyenne simple des notes. D'après le relevé (D5) :
  ```
  moyenne_module = (contrôle1 + contrôle2 + 2 × examen) / 4
  ```
  Vérification : (15 + 15 + 32) / 4 = 15,50.
- **Fichiers** :
  - `backend/app/Http/Controllers/Api/AdminDeliberationController.php:46-73`
  - `StudentController::grades()` (vers la ligne 461)
- **Correction** : créer un service unique `GradeCalculator` (dans `app/Services`) utilisé partout :
  - moyenne des contrôles `C` = moyenne des notes de type `controle` ;
  - examen `E` = note de type `examen` ;
  - moyenne du module = `(2C + 2E) / 4`, soit `(C1 + C2 + 2E) / 4` avec deux contrôles.
- **Test** : reproduire le relevé D5 (total 439, Σ coefficients 29 → 15,14).

### B2. 🟠 Décision de délibération : admis / rattrapage / abandon (règle R3)
- **Constat** : `deliberations.result` vaut seulement `passed` ou `failed`. Dans le PV (D3) :
  - moyenne ≥ 10 → **admis** (même si un module est < 10) ;
  - moyenne < 10 → **rattrapage**, uniquement dans les modules < 10 ;
  - aucune note → **abandon**.
  
  Il existe aussi un PV **avant** et un PV **après** rattrapage.
- **Fichiers** :
  - migration de `deliberations`
  - `AdminDeliberationController.php:73` et `:101`
  - `SemesterAdvancementService.php`
  - `frontend/src/views/admin/Deliberations.vue`
  - `student/Deliberations.vue`
- **Correction** :
  - migration : `result ENUM('admis','rattrapage','abandon')` et nouvelle colonne `phase ENUM('avant_rattrapage','apres_rattrapage')`. La clé unique devient `(student_id, semester, academic_year, phase)`.
  - après rattrapage : admis si la nouvelle moyenne ≥ 10, sinon la décision passe par la révision (`advancement_reviews`).
  - adapter `SemesterAdvancementService` pour qu'il lise la délibération **après rattrapage** si elle existe.

### B3. 🟠 Note de rattrapage par module
- **Constat** : le relevé (D5) a une colonne « rattrapage » et une « moyenne finale » par module. Rien n'existe en base. Le rattrapage n'est pas un type d'examen : c'est une note par stagiaire et par module.
- **Correction** : créer la table `retake_grades`, avec l'unicité `(student_id, module_id, semester, academic_year)` :
  ```
  retake_grades(id, student_id, module_id, semester, academic_year, grade, created_at)
  ```
  L'enseignant saisit la note de rattrapage des seuls stagiaires concernés (modules < 10). Moyenne finale du module = note de rattrapage si elle est présente, sinon la moyenne R1. Le règlement de l'institut peut plafonner cette moyenne, à confirmer auprès de l'institut.
- **Écran** : ajouter « Rattrapage » dans l'espace enseignant (liste des stagiaires concernés par module).

### B4. 🟡 Note éliminatoire du module
- **Constat** : chaque module a une note éliminatoire (« ن إ : 0 » dans le PV D3, colonne du relevé D5).
- **Correction** : ajouter la colonne `modules.eliminatory_grade DECIMAL(4,2) DEFAULT 0`, le champ dans le formulaire du module, et une règle dans la délibération : une moyenne de module inférieure à la note éliminatoire empêche l'admission.

### B5. 🟡 Niveau et diplôme de la spécialité
- **Constat** : le relevé (D5) indique « Niveau : 5 » et « Diplôme : BTS ».
- **Correction** : ajouter `specialties.level` (entier) et `specialties.diploma` (chaîne), plus les champs dans `Specialties.vue`.

### B6. 🟡 Dates des semestres
- **Constat** : le relevé (D5) indique la période du semestre (du 05/10/2025 au 04/04/2026). Elle n'est pas stockée.
- **Correction** : créer la table `session_semesters(id, session_id, semester, start_date, end_date)`, remplie à la création de la session (5 semestres de 6 mois) et modifiable par l'admin.

### B7. 🟡 Format du numéro d'inscription
- **Constat** : l'application génère `NNNN + 1/2 + AA + P|C|A + 16 + 47`. Les documents réels portent `…24**S**1647` (lettre **S** pour la formation présentielle).
- **Fichier** : `backend/app/Http/Controllers/Api/AdminController.php:271-276`
- **Correction** : confirmer auprès de l'institut la lettre de chaque mode (S = présentiel ? cours du soir ? apprentissage ?) puis corriger `$typeMap`.

### B8. 🟡 Mode d'étude de l'examen
- **Constat** : le planning (D1) est propre aux **cours du soir**, mais `exams` n'a pas de `study_mode`. On ne peut donc pas distinguer les examens du jour et du soir d'une même spécialité et d'un même semestre.
- **Correction** : ajouter `exams.study_mode` (rempli depuis le module ou le groupe) et filtrer le planning par mode.

### B9. 🟡 Édition des documents officiels (perspective)
- Générer en PDF le **relevé de notes** (D5) et le **PV de délibération** (D3), avec les mêmes rubriques : numéro du relevé, date d'édition, nombre d'absences et nombre d'absences justifiées (calculés depuis `attendances`).

### B10. 🟡 Lieu de naissance du stagiaire
- **Constat** : l'attestation de stage (document 6) indique le lieu de naissance, qui n'est pas stocké.
- **Correction** : ajouter `students.birth_place` (chaîne de 100 caractères), l'ajouter au formulaire d'inscription et à « Compléter le profil ».

### B11. 🟡 Grade de l'enseignant
- **Constat** : le document de transfert des notes (document 7) indique le grade de l'enseignant (PSFEP…).
- **Correction** : ajouter `teachers.grade`, avec le champ correspondant dans la fiche de l'enseignant.

### B12. 🟠 Absence à une épreuve (« ABS ») et observation
- **Constat** : dans le document de transfert des notes (document 7), un stagiaire absent à l'épreuve est noté « ABS », ce qui n'est pas un 0. Chaque ligne comporte aussi une observation. `grades` ne prévoit ni l'un ni l'autre.
- **Correction** : ajouter `grades.is_absent` (booléen) et `grades.remark`. Dans `Grading.vue`, prévoir une case « Absent ». Dans le calcul R1, décider (avec l'institut) si « ABS » compte 0 ou rend la moyenne non calculable.

### B13. 🟡 Mois d'ouverture des sessions des cours du soir
- **Constat** : l'attestation (document 6) d'un stagiaire des cours du soir donne une formation du **08/10/2024** au 07/04/2027, alors que l'application n'ouvre des sessions qu'en février et en septembre (`TrainingSession::ALLOWED_MONTHS`).
- **Correction** : vérifier auprès de l'institut si les sessions du soir ouvrent en octobre ; si oui, autoriser ce mois (ou une date de début libre) pour le mode « cours du soir ».

### B14. 🟡 Filière et fiche descriptive de la spécialité
- **Constat** : la fiche descriptive de la spécialité (document D3 du mémoire) donne la filière (code ELE, INT…) et, pour chaque spécialité, le niveau d'accès, la durée en mois, les missions du diplômé, les aptitudes requises et les débouchés.
- **Correction** : créer la table `filieres(id, code, name)` et ajouter `specialties.filiere_id`, `access_level`, `duration_months`, `missions`, `aptitudes`, `career_outlets`. Ces informations s'affichent sur la page publique des spécialités et dans l'assistant intelligent.

### B15. 🟡 Délibération : colonne `phase`
- **Constat** : le modèle du mémoire prévoit une délibération avant rattrapage et une délibération après rattrapage (document D8).
- **Correction** : ajouter `deliberations.phase ENUM('avant_rattrapage','apres_rattrapage')` (voir B2).

> Le mémoire présente désormais le modèle conceptuel en français (classes Filière, SemestreSession, NoteRattrapage…). Le tableau « Correspondance entre le MOR et les tables de la base » (chapitre IV) donne le nom anglais de chaque table : les nouvelles tables à créer sont `filieres`, `session_semesters` et `retake_grades`.

---

## C. Après les corrections

1. Relancer `php artisan migrate`, puis les seeders corrigés (A7).
2. Mettre à jour le mémoire en régénérant les figures et le document :
   - le diagramme de classes (nouveaux attributs `eliminatoryGrade`, `level`, `diploma`, classes `RetakeGrade` et `SessionSemester`, énumération `DeliberationResult` et `phase`) ;
   - le dictionnaire de données et le MOR ;
   - le diagramme d'activité « délibération » (branches admis / rattrapage / abandon) ;
   - les captures d'écran.
   
   Les générateurs sont dans `diagrams_memoire/memoire/sources/`.
3. Cocher les tests du tableau « Cahier de tests fonctionnels » du mémoire (chapitre IV, 6.1).

## Récapitulatif

| # | Priorité | Sujet |
|---|---|---|
| A1 | 🔴 | Route d'enregistrement de la présence |
| A2 | 🔴 | Connexion de l'application mobile (jeton) |
| A3 | 🔴 | Tableau de bord de l'enseignant (relation `specialty`) |
| A4 | 🟠 | Déconnexion depuis la barre latérale |
| A5 | 🟠 | Boucle infinie dans `StudentBarChart` |
| A6 | 🟡 | Heure de fin des séances (présence) |
| A7 | 🟡 | Données de démonstration réalistes |
| B1 | 🟠 | Moyenne du module (C1 + C2 + 2E) / 4 |
| B2 | 🟠 | Décision admis / rattrapage / abandon, avant / après rattrapage |
| B3 | 🟠 | Notes de rattrapage par module |
| B4 | 🟡 | Note éliminatoire |
| B5 | 🟡 | Niveau et diplôme de la spécialité |
| B6 | 🟡 | Dates des semestres |
| B7 | 🟡 | Lettre du mode d'étude dans le matricule |
| B8 | 🟡 | Mode d'étude de l'examen |
| B9 | 🟡 | Relevé et PV en PDF |
| B10 | 🟡 | Lieu de naissance du stagiaire |
| B11 | 🟡 | Grade de l'enseignant |
| B12 | 🟠 | Absence à une épreuve (ABS) et observation |
| B13 | 🟡 | Mois d'ouverture des sessions du soir |
