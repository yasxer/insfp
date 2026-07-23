# 📋 TODO LIST - FIXES À FAIRE IMMÉDIATEMENT

**Status:** P0 - DONE! ✅ | P1 - À faire  
**Dernière mise à jour:** 2026-06-19

---

## 🔴 P0 - CRITIQUE (À FIX IMMÉDIATEMENT)

### [x] P0.1 - Fixer le Modèle User - Ajouter first_name & last_name ✅
**Fichier(s):** 
- `backend/database/migrations/XXXX_create_users_table.php`
- `backend/app/Models/User.php`
- `backend/app/Http/Controllers/Api/AuthController.php`

**Tâches:**
- [ ] Créer migration: `php artisan make:migration add_names_to_users_table`
- [ ] Ajouter colonnes `first_name` et `last_name` à la table users
- [ ] Ajouter les champs au fillable du User model
- [ ] Modifier AuthController::register() pour stocker les noms dans User aussi
- [ ] Modifier AuthController::getUserData() pour retourner les noms depuis User
- [ ] Tester que les données sont bien synchronisées

**Temps estimé:** 1-2h

---

### [x] P0.2 - Ajouter Validation à Tous les Endpoints ✅
**Fichier(s):** 
- `backend/app/Http/Requests/` (créer new files)
- Tous les controllers

**Tâches:**
- [ ] Créer `StudentStoreRequest.php` pour créer/modifier étudiant
- [ ] Créer `TeacherStoreRequest.php` pour créer/modifier enseignant
- [ ] Créer `ModuleStoreRequest.php` pour module
- [ ] Ajouter validation dans chaque controller:
  - AdminController::createStudent()
  - AdminController::updateStudent()
  - AdminController::createTeacher()
  - AdminController::updateTeacher()
  - AdminController::createModule()
  - AdminController::updateModule()
  - StudentController::updateProfile()
  - TeacherController::updateProfile()
- [ ] Vérifier que les erreurs de validation sont bien retournées (422)
- [ ] Tester avec des données invalides

**Temps estimé:** 3-4h

---

### [x] P0.3 - Fixer Indentation Routes API ✅
**Fichier:** `backend/routes/api.php`

**Tâches:**
- [ ] Indenter correctement les routes Student (lignes 57-62)
- [ ] Indenter correctement les routes Teacher (lignes 109-115)
- [ ] Vérifier que toutes les routes sont accessibles: `php artisan route:list | grep -E "(student|teacher|admin)"`
- [ ] Tester chaque endpoint avec Postman/Insomnia
- [ ] Vérifier que les middleware sont bien appliqués

**Temps estimé:** 1h

---

### [x] P0.4 - Fixer Auth Store - Gérer tous les rôles dans fetchUser() ✅
**Fichier:** `frontend/src/stores/auth.js:120`

**Tâches:**
- [ ] Modifier fetchUser() pour détecter le rôle
- [ ] Si Student: appeler studentApi.getProfile()
- [ ] Si Teacher: appeler teacherApi.getProfile()
- [ ] Si Admin: appeler adminApi.getProfile()
- [ ] Créer teacherApi et adminApi si nécessaires
- [ ] Tester le refresh pour chaque rôle

**Code à ajouter:**
```javascript
async function fetchUser() {
  if (!token.value) return
  try {
    let data
    if (user.value?.role === 'student') {
      data = await studentApi.getProfile()
    } else if (user.value?.role === 'teacher') {
      data = await teacherApi.getProfile()  // à créer
    } else if (user.value?.role === 'administration') {
      data = await adminApi.getProfile()    // à créer
    } else {
      return
    }
    user.value = data.data || data
  } catch (err) {
    // ... error handling
  }
}
```

**Temps estimé:** 1-2h

---

### [x] P0.5 - Ajouter Interceptors d'Erreur Réseau (Axios) ✅
**Fichier:** `frontend/src/api/axios.js`

**Tâches:**
- [ ] Ajouter interceptor pour réponses 401 (Unauthorized)
- [ ] Ajouter interceptor pour réponses 403 (Forbidden)
- [ ] Ajouter interceptor pour réponses 500 (Server Error)
- [ ] Quand 401: déconnecter l'utilisateur
- [ ] Afficher message d'erreur clair au user
- [ ] Tester avec une requête invalide/expired token

**Code structure:**
```javascript
apiClient.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Handle unauthorized
    } else if (error.response?.status === 403) {
      // Handle forbidden
    } else if (error.response?.status >= 500) {
      // Handle server error
    }
    return Promise.reject(error)
  }
)
```

**Temps estimé:** 1-2h

---

### [x] P0.6 - Ajouter Transactions DB pour Opérations Critiques ✅
**Fichier:** 
- `backend/app/Http/Controllers/Api/AdminDeliberationController.php`
- `backend/app/Http/Controllers/Api/AdminController.php` (createStudent, etc.)

**Tâches:**
- [ ] Wrapper storeOrUpdate() dans AdminDeliberationController avec DB::transaction()
- [ ] Wrapper createStudent() dans DB::transaction()
- [ ] Wrapper createTeacher() dans DB::transaction()
- [ ] Tester que si une operation échoue, la transaction rollback complètement
- [ ] Ajouter error handling approprié

**Temps estimé:** 1h

---

### [x] P0.7 - Ajouter Rate Limiting ✅
**Fichier:** `backend/app/Http/Kernel.php` et routes

**Tâches:**
- [ ] Ajouter rate limiting sur POST /login (max 5 essais par 15min)
- [ ] Ajouter rate limiting sur POST /register (max 3 par heure)
- [ ] Ajouter rate limiting général sur API (max 60 requests par minute)
- [ ] Tester que les limites fonctionnent

**Code:**
```php
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,15'); // 5 per 15 min
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,60');
```

**Temps estimé:** 1-2h

---

### [x] P0.8 - Ajouter Input Sanitization ✅
**Fichier:** Tous les inputs

**Tâches:**
- [ ] Ajouter `htmlspecialchars()` sur tous les text inputs
- [ ] Utiliser prepared statements pour toutes les queries (déjà fait par Eloquent)
- [ ] Ajouter validation pour file uploads (size, type, mime)
- [ ] Tester avec des inputs malveillants

**Temps estimé:** 2h

---

## 🟡 P1 - MAJEUR (À FIX CETTE SEMAINE)

### [x] P1.1 - Implémenter Pagination Backend ✅
**Fichier:** `backend/app/Http/Controllers/Api/AdminController.php`

**Tâches:**
- [x] Modifier getStudents() pour supporter `?page=1&limit=10` (déjà fait)
- [x] Modifier getTeachers() pour pagination ✅ (fait + frontend `stores/teachers.js` et `TeacherFilters.vue` mis à jour)
- [ ] getModules() — **volontairement non paginé**: cet endpoint n'est utilisé que comme dropdown filtré (specialty_id + semester) dans ScheduleModal/ScheduleEntryModal, jamais comme liste paginée. Pas de page d'admin "Modules" dédiée. À revisiter si une telle liste est créée.
- [x] Retourner `{ data: [], total, current_page, last_page, per_page }`
- [ ] Tester avec différentes valeurs de page

**Code structure:**
```php
public function getStudents(Request $request)
{
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 10);
    
    $students = Student::paginate($limit, ['*'], 'page', $page);
    
    return response()->json($students);
}
```

**Temps estimé:** 2h

---

### [x] P1.2 - Implémenter Pagination Frontend ✅
**Fichier:** 
- `frontend/src/components/admin/students/StudentsList.vue`
- `frontend/src/components/admin/teachers/TeachersList.vue`
- `frontend/src/components/common/PaginationBar.vue` (créé)
- `frontend/src/stores/students.js`, `frontend/src/stores/teachers.js`

**Tâches:**
- [x] Ajouter état pour page, limit, total (déjà dans `pagination` du store, `per_page` passé de 1000 → 10)
- [x] Créer composant PaginationBar réutilisable
- [x] Implémenter les boutons: Previous, Next, Page numbers
- [x] Fix bug: `fetchStudents(page)` ignorait le paramètre `page` (toujours `page: 1` codé en dur)
- [x] Fix `AssignTeacherModal.vue`: utilisait le state paginé du store pour son dropdown — séparé via `fetchAllTeachers()` (non paginé, per_page=1000) pour ne pas casser la sélection
- [ ] Tester manuellement dans le navigateur (à faire par l'utilisateur)

**Temps estimé:** 3h

---

### [x] P1.3 - Implémenter Search & Filter Backend ✅ (déjà fait)
**Fichier:** `backend/app/Http/Controllers/Api/AdminController.php`

**Tâches:**
- [x] getStudents() accepte déjà `search`, `specialty_id`, `semester`, `group`, `study_mode`, `is_graduated`, `graduation_year`
- [x] getTeachers() a déjà `search` (nom/spécialisation)
- [x] getModules() a déjà `search`, `specialty_id`, `semester`
- [x] Utilise `->where(..., 'like', "%$search%")`
- [ ] Tester les recherches manuellement

**Code structure:**
```php
public function getStudents(Request $request)
{
    $query = Student::query();
    
    if ($request->search) {
        $query->where('first_name', 'like', "%{$request->search}%")
              ->orWhere('last_name', 'like', "%{$request->search}%")
              ->orWhere('registration_number', 'like', "%{$request->search}%");
    }
    
    if ($request->specialty) {
        $query->where('specialty_id', $request->specialty);
    }
    
    return response()->json($query->paginate());
}
```

**Temps estimé:** 2h

---

### [x] P1.4 - Implémenter Search & Filter Frontend ✅ (déjà fait)
**Fichier:** 
- `frontend/src/components/admin/students/StudentFilters.vue`
- `frontend/src/components/admin/teachers/TeacherFilters.vue`

**Tâches:**
- [x] Input de recherche présent (students + teachers)
- [x] Dropdowns spécialité/semestre/groupe/statut présents
- [x] Filtres connectés à fetchStudents()/fetchTeachers()
- [x] Debounce 300ms déjà en place
- [ ] Tester les filtres manuellement

**Temps estimé:** 3h

---

### [x] P1.5 - Implémenter StudentController::dashboard() ✅
**Fichier:** `backend/app/Http/Controllers/Api/StudentController.php`

**Tâches:**
- [x] dashboard() existait déjà avec: modules du semestre, dernières notes (5), présences (all-time, pas juste ce mois), examens à venir (5)
- [x] Ajouté `pending_homeworks` (devoirs avec due_date futur, sans soumission de l'étudiant)
- [x] Ajouté `unread_messages` (réutilise la logique de `MessageController::unreadCount`)
- [x] Frontend `views/student/Dashboard.vue` corrigé: lisait `statistics.pending_tasks` qui n'existait jamais côté backend → mappé sur `pending_homeworks`
- [ ] Tester manuellement avec un compte étudiant ayant des devoirs/messages

**Temps estimé:** 2h

---

### [x] P1.6 - Compléter les Models - Ajouter Relationships ✅ (déjà fait)
**Fichier:** `backend/app/Models/`

**Tâches:**
- [x] Grade.php: `belongsTo(Student)`, `belongsTo(Module)`, `belongsTo(Exam)` — déjà présents
- [x] Deliberation.php: `belongsTo(Student)` présent. `TrainingSession` **n'existe pas** dans ce schéma (ni modèle ni migration) — la session est trackée via les colonnes `semester` + `academic_year` directement sur `deliberations`. Rien à ajouter.
- [x] Attendance.php: `belongsTo(Student)` présent, `belongsTo(Schedule)` (= équivalent de "Lesson", il n'y a pas de modèle `Lesson` séparé dans ce projet) + `belongsTo(Teacher)`
- [x] Homework.php: `belongsTo(Teacher)`, `belongsTo(Module)`, `hasMany(HomeworkSubmission)` — déjà présents
- [x] Message.php: `belongsTo(User, 'sender_id')`, `belongsTo(User, 'recipient_id')` — déjà présents
- [x] Testé avec `php artisan tinker`: toutes les relations (Grade, Deliberation, Attendance, Homework, Message) instanciées et confirmées comme `BelongsTo`/`HasMany` valides, aucune erreur

**Temps estimé:** 1h

---

### [x] P1.7 - Ajouter Validation aux Formulaires Frontend ✅
**Fichier:** Tous les `.vue` avec des formulaires

**Tâches:**
- [x] `vee-validate` + `yup` installés (`frontend/package.json`)
- [x] Composant réutilisable `frontend/src/components/common/FormField.vue` créé (label + input/slot + message d'erreur)
- [x] Schémas Yup centralisés dans `frontend/src/validations/schemas.js`: `loginSchema`, `registerSchema`, `studentSchema`, `specialtySchema` (email format, phone regex algérien `0[5-7][0-9]{8}`, password min 8, required fields, password confirmation match)
- [x] `views/auth/Login.vue` — migré vers `useForm`/`defineField` de vee-validate, erreurs affichées par champ
- [x] `views/auth/Register.vue` — validation Yup au submit (en plus des erreurs serveur 422 déjà gérées), mêmes messages clairs sous chaque champ
- [x] `components/admin/students/StudentForm.vue` — validation Yup au submit + erreurs par champ affichées
- [x] `components/admin/specialties/SpecialtyForm.vue` — n'avait aucune validation avant, ajoutée (name, code, duration_years)
- [x] `components/admin/specialties/ModuleForm.vue` — déjà avait une validation manuelle complète (required + ranges + messages par champ), laissé tel quel, conforme aux exigences
- [x] Build frontend (`npx vite build`) passe sans erreur après tous les changements
- [ ] Tester manuellement avec données invalides dans le navigateur (à faire par l'utilisateur)

**Note:** Pas de formulaire "Add/Edit Teacher" encore implémenté (`TeachersList.vue` → `openAddModal` fait juste `console.log`), donc pas de validation à y ajouter pour l'instant.

**Temps estimé:** 4h

---

### [x] P1.8 - Completer Components Manquants ✅
**Fichier:** `frontend/src/components/` et `frontend/src/views/`

Les noms de fichiers attendus par ce TODO n'existaient pas tels quels — l'implémentation réelle utilise des views au lieu de components séparés. Vérification faite fichier par fichier (fetch API réel + action submit/save réelle, pas de stub):
- [x] Messages: `views/student/Messages.vue`, `views/teacher/Messages.vue`, `MessageComposer.vue` (admin) — complets
- [x] Deliberations: `views/student/Deliberations.vue`, `views/admin/Deliberations.vue` (CRUD complet) — complets
- [x] Homework: `views/teacher/Homeworks.vue`, `HomeworkDetail.vue`, `views/student/Homeworks.vue` — complets (création, soumission, notation)
- [x] Attendance: `views/teacher/MarkAttendance.vue`, `views/student/Attendance.vue` — complets
- [x] Grades: `views/admin/ExamGrades.vue` — complet
- [x] **Trouvé et supprimé**: `components/student/AttendanceTable.vue` était un stub vide (template vide, aucun script) et n'était importé nulle part — dead code supprimé, `views/student/Attendance.vue` gère déjà cette fonctionnalité en interne

**Temps estimé:** 4-5h

---

## 🟢 P2 - MINEUR (À FIX PROCHAINES SEMAINES)

### [x] P2.1 - Ajouter Composant de Notification Global ✅
**Fichier:** `frontend/src/components/common/ToastNotification.vue`

**Tâches:**
- [x] Composant `ToastNotification.vue` créé (4 types: success/error/warning/info, icône + couleur par type, bouton fermer)
- [x] Composant `ToastContainer.vue` créé (position top-right fixe, transition d'entrée/sortie)
- [x] Store `stores/toast.js` créé (Pinia) avec actions `success()`, `error()`, `warning()`, `info()`, auto-dismiss après 4-5s
- [x] `ToastContainer` ajouté dans `App.vue`
- [x] Tous les 32 appels `alert()` remplacés par `toastStore.*()` dans les 14 fichiers concernés (TeachersList, StudentsList, Deliberations, Profile, Schedule, RegistrationGenerator, Exams teacher, StudentDetails, TeacherDetails, SpecialtyDetails, SpecialtyTimetable, FullSessionTimetable, Courses student, Documents student)
- [x] `npx vite build` passe sans erreur
- [ ] Test manuel dans le navigateur (à faire par l'utilisateur)

**Temps estimé:** 2h

---

### [x] P2.2 - Ajouter Loading States Globaux ✅
**Fichier:** `frontend/src/components/common/GlobalLoader.vue`

**Tâches:**
- [x] Composant `GlobalLoader.vue` créé (barre fine animée en haut de la page, type YouTube/GitHub)
- [x] Store `stores/loading.js` créé (compteur `activeRequests`, getter `isLoading`)
- [x] Interceptors ajoutés dans `api/axios.js`: `start()` sur chaque requête, `stop()` sur chaque réponse/erreur
- [x] `GlobalLoader` ajouté dans `App.vue`
- [x] `npx vite build` passe sans erreur

**Temps estimé:** 1-2h

---

### [x] P2.3 - Ajouter Skeleton Loaders ✅
**Fichier:** `frontend/src/components/common/SkeletonLoader.vue`

**Tâches:**
- [x] Composant générique `SkeletonLoader.vue` créé (grille configurable rows/columns, réutilisable hors tableaux)
- [x] `ActiveStudentsTable.vue` — lignes skeleton (au lieu du texte "Loading...")
- [x] `GraduatedStudentsTable.vue` — lignes skeleton
- [x] `TeachersTable.vue` — lignes skeleton
- [x] `npx vite build` passe sans erreur

**Temps estimé:** 2h

---

### [x] P2.4 - Sauvegarder Dark Mode Preference ✅ (bug caché trouvé et corrigé)
**Fichier:** `frontend/src/stores/theme.js`

La persistence localStorage existait déjà des deux côtés, mais il y avait **deux systèmes parallèles non synchronisés**: `composables/useTheme.js` (un `ref()` local recréé à chaque appel — utilisé par `ThemeToggle.vue`, `App.vue`, et les 3 composants de graphiques) et `stores/theme.js` (Pinia, singleton — utilisé seulement par `views/student/Profile.vue`). Résultat: changer le thème depuis la navbar ne mettait pas à jour les couleurs des graphiques en temps réel (il fallait un refresh).

**Tâches:**
- [x] localStorage déjà fonctionnel des deux côtés
- [x] Préférence chargée au démarrage (store Pinia s'auto-initialise)
- [x] **Fix du bug de désynchronisation**: tous les composants migrés vers `stores/theme.js` (seule source de vérité) — `App.vue`, `ThemeToggle.vue`, `TeacherBarChart.vue`, `StudentBarChart.vue`, `DistributionChart.vue` (avec `storeToRefs` pour garder la réactivité)
- [x] `composables/useTheme.js` supprimé (dead code, source du bug)
- [x] `npx vite build` passe sans erreur

**Temps estimé:** 30min (+ debug du bug de sync)

---

### [ ] P2.5 - Normaliser les Timezones
**Fichier:** Tous les fichiers qui utilisent les dates

**Tâches:**
- [ ] Installer `dayjs` ou `date-fns`
- [ ] Convertir tous les timestamps en UTC au backend
- [ ] Convertir au timezone local au frontend lors de l'affichage
- [ ] Tester les dates avec différents timezones

**Temps estimé:** 2h

---

### [ ] P2.6 - Ajouter Indexes Base de Données
**Fichier:** `backend/database/migrations/`

**Tâches:**
- [ ] Créer migration: `php artisan make:migration add_indexes`
- [ ] Ajouter indexes sur:
  - users.email
  - students.registration_number
  - students.specialty_id
  - teachers.specialization
  - modules.specialty_id
  - grades.student_id
  - attendance.student_id
- [ ] Exécuter migration: `php artisan migrate`
- [ ] Vérifier avec `SHOW INDEXES FROM table_name;`

**Temps estimé:** 1h

---

### [ ] P2.7 - Ajouter Soft Deletes
**Fichier:** 
- `backend/app/Models/User.php`
- `backend/app/Models/Student.php`
- Autres models

**Tâches:**
- [ ] Créer migration: `php artisan make:migration add_soft_deletes`
- [ ] Ajouter colonne deleted_at à tables: users, students, teachers
- [ ] Ajouter `use SoftDeletes;` aux models
- [ ] Tester que les deletes sont soft (données toujours en DB)

**Temps estimé:** 1h

---

### [ ] P2.8 - Ajouter Documentation API (Swagger)
**Fichier:** `backend/`

**Tâches:**
- [ ] Installer `l5-swagger`:
  ```bash
  composer require "darkaonline/l5-swagger"
  ```
- [ ] Générer config: `php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`
- [ ] Ajouter commentaires OpenAPI sur les controllers
- [ ] Générer docs: `php artisan l5-swagger:generate`
- [ ] Accéder à http://localhost:8000/api/documentation

**Temps estimé:** 3h

---

### [ ] P2.9 - Ajouter Tests Unitaires
**Fichier:** `backend/tests/`

**Tâches:**
- [ ] Créer test pour AuthController::login()
- [ ] Créer test pour AuthController::register()
- [ ] Créer test pour AdminController::createStudent()
- [ ] Lancer tests: `php artisan test`
- [ ] Viser au moins 80% de code coverage

**Temps estimé:** 4h

---

### [ ] P2.10 - Ajouter Logging
**Fichier:** `backend/config/logging.php` et controllers

**Tâches:**
- [ ] Configurer Monolog (déjà fait par Laravel)
- [ ] Ajouter `Log::info()`, `Log::error()` dans les controllers critiques
- [ ] Ajouter logging pour:
  - Connexions utilisateur
  - Modifications de données
  - Erreurs
- [ ] Vérifier les logs dans `storage/logs/`

**Temps estimé:** 1-2h

---

## 📊 STATISTIQUES

| Catégorie | Count | Status | Temps |
|-----------|-------|--------|-------|
| P0 - Critique | 8 | ✅ DONE | 12-16h |
| P1 - Majeur | 8 | ⏳ À FAIRE | 20-25h |
| P2 - Mineur | 10 | ⏳ À FAIRE | 18-21h |
| **TOTAL** | **26** | **8/26 DONE** | **50-62h** |

---

## 🎯 ORDRE RECOMMANDÉ DE TRAVAIL

**Jour 1-2 (P0 - Critique):**
1. P0.1 - Fixer User Model
2. P0.2 - Validation inputs
3. P0.3 - Routes indentation
4. P0.4 - Auth store rôles
5. P0.5 - Axios interceptors
6. P0.6 - DB Transactions
7. P0.7 - Rate limiting
8. P0.8 - Input sanitization

**Jour 3-5 (P1 - Majeur):**
1. P1.1 & P1.2 - Pagination
2. P1.3 & P1.4 - Search/Filter
3. P1.5 - StudentController dashboard
4. P1.6 - Relationships modèles
5. P1.7 - Validation formulaires frontend
6. P1.8 - Componets manquants

**Jour 6-7 (P2 - Mineur):**
- Sélectionner les P2 les plus importants et les faire

---

## ✅ CHECKLIST FINALE

Avant de déployer:
- [x] Tous les P0 sont fixés ✅
- [ ] Tous les P1 sont fixés
- [ ] Tests unitaires passent
- [ ] Pas de console.error() dans les logs
- [ ] Pas de TODO comments en production
- [ ] Documentation est à jour
- [ ] Changelog est mis à jour
- [ ] Code review complétée

---

## 🎯 RÉSUMÉ P0 - COMPLÉTÉ ✅

**Tous les P0 items sont maintenant fixes:**

✅ **P0.1** - User Model: first_name + last_name (Backend)
✅ **P0.2** - Validation: FormRequests (Backend)
✅ **P0.3** - Routes: Indentation fixée (Backend)
✅ **P0.4** - Auth Store: Handle all roles (Frontend)
✅ **P0.5** - Axios: Error Interceptors (Frontend)
✅ **P0.6** - Transactions: DB Transactions en place (Backend)
✅ **P0.7** - Rate Limiting: Throttle configuré (Backend)
✅ **P0.8** - Sanitization: HTML escaping (Backend)

**Next Phase:** P1 - MAJEUR (Pagination, Search, Dashboard, etc.)

---

**Dernière mise à jour:** 2026-06-19 ✅
