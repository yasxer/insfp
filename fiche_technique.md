# Fiche Technique du Projet / البطاقة التقنية للمشروع : INSFP Management System

---

## 🇫🇷 Version Française

### 1. Présentation Générale
* **Nom du projet :** Plateforme de Gestion INSFP (Institut National Spécialisé de la Formation Professionnelle).
* **Type d'application :** Web Application (Single Page Application - SPA).
* **Objectif principal :** Numérisation et gestion intégrale des affaires pédagogiques et administratives de l'institut.
* **Architecture Système :** Architecture découplée entre le Frontend et le Backend, communiquant via une API RESTful.

### 2. Technologies & Environnement (Stack Technique)
* **Backend (Serveur) :** Laravel 10/11 (PHP).
* **Base de données :** MySQL / MariaDB (avec Migrations & Seeders).
* **Authentification & Sécurité :** Laravel Sanctum / JWT.
* **Frontend (Client) :** Vue.js 3 (Composition API).
* **Outil de Build Frontend :** Vite.
* **Framework CSS :** Tailwind CSS.
* **Environnement de développement :** Laragon (Windows), Composer, NPM.

### 3. Modules Principaux (Modélisation)
* **Administration & Pédagogie :** Gestion des sessions de formation (`TrainingSession`), spécialités (`Specialty`), modules (`Module`), emplois du temps (`Schedule`) et congés (`Holiday`).
* **Gestion des Professeurs (`Teacher`) :** Attribution des cours (`Lesson`), création de devoirs (`Homework`), évaluation (`Grade`), et suivi des absences (`Attendance`).
* **Gestion des Stagiaires (`Student`) :** Inscriptions, matricules, accès aux cours et soumission des devoirs (`HomeworkSubmission`).
* **Évaluation & Délibérations (`Deliberation`) :** Calcul automatique et intelligent des moyennes (Note de l'examen × Coefficient / Somme des coefficients), statistiques et validation des résultats (Admis / Ajourné).
* **Communication :** Messagerie interne (`Message`), système de notifications (`Notification`) et partage de fichiers/documents (`Document`).

---

## 🇩🇿 النسخة العربية

### 1. نظرة عامة على المشروع
* **إسم المشروع:** منصة تسيير المعهد المتخصص في التكوين المهني (INSFP).
* **نوع التطبيق:** تطبيق ويب تفاعلي (SPA).
* **الهدف الرئيسي:** رقمنة وتسيير الشؤون البيداغوجية والإدارية للمعهد بطريقة عصرية وفعالة.
* **هندسة النظام:** بنية برمجية مفصولة بالكامل بين واجهة المستخدم (الواجهة الأمامية) والخادم (الواجهة الخلفية) يتواصلان عبر واجهة برمجة تطبيقات (REST API).

### 2. التقنيات وملحقات التطوير
* **الواجهة الخلفية (Backend):** إطار العمل لارافل Laravel 10/11 (PHP).
* **قواعد البيانات:** MySQL / MariaDB (باستخدام نظام Migrations لتوليد الجداول).
* **نظام الحماية والمصادقة:** Laravel Sanctum / JWT لتأمين الاتصالات.
* **الواجهة الأمامية (Frontend):** إطار العمل Vue.js 3 (باستخدام Composition API).
* **مجمّع الحزم (Bundler):** Vite (لضمان سرعة بناء وتحديث الواجهات).
* **تنسيق الواجهة:** Tailwind CSS (لتصميم واجهات متجاوبة).
* **بيئة العمل:** Laragon على نظام Windows، أداة Composer، و أداة NPM.

### 3. الوحدات والخصائص الأساسية
* **الإدارة والبيداغوجيا:** تسيير الدورات التكوينية (`TrainingSession`)، الشعب (`Specialty`)، المواد البيداغوجية (`Module`)، الجداول الزمنية (`Schedule`)، والأيام العطل (`Holiday`).
* **تسيير الأساتذة:** إدارة ملفات الأساتذة (`Teacher`)، السماح برفع الدروس (`Lesson`) والواجبات (`Homework`)، رصد نتائج الامتحانات (`Grade`)، وإدارة حضور وغياب الطلبة (`Attendance`).
* **تسيير الطلبة/المتربصين:** تسجيل الطلبة (`Student`)، توليد أرقام التسجيل، ومتابعة الدروس وحل الواجبات (`HomeworkSubmission`).
* **الامتحانات والمداولات:** نظام مداولات ذكي (`Deliberation`) يقوم بحساب المعدلات آلياً (نقطة كل مادة × معاملها / مجموع المعاملات) مع إمكانية تأكيد النتيجة آلياً أو تعديلها يدوياً (ناجح/مؤجل).
* **التواصل:** نظام مراسلات داخلي (`Message`)، إشعارات للمستخدمين (`Notification`)، ومكتبة لتبادل الوثائق (`Document`).
