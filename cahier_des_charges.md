a# Cahier des Charges / دفتر الشروط : INSFP Management System

---

## 🇫🇷 Version Française

### 1. Contexte et Objectifs
L'Institut National Spécialisé de la Formation Professionnelle (INSFP) gère un grand nombre de stagiaires, de professeurs et de filières. Actuellement, la gestion administrative et pédagogique nécessite une numérisation pour optimiser le temps et réduire les erreurs. L'objectif de ce projet est de concevoir et développer une plateforme web complète (SPA) qui centralise toutes les opérations de l'institut.

### 2. Public Cible (Acteurs du Système)
Le système prévoit trois types d'utilisateurs principaux :
1. **Administrateurs :** Le personnel de direction et de la scolarité.
2. **Professeurs (Formateurs) :** L'équipe pédagogique.
3. **Stagiaires (Étudiants) :** Les personnes inscrites aux formations.

### 3. Exigences Fonctionnelles (Besoins Métiers)

#### A. Espace Administration
* **Gestion de la structure :** Création et gestion des sessions de formation, des spécialités, des matières (Modules), et des coefficients.
* **Gestion des Utilisateurs :** Ajout, modification, et attribution des rôles (Professeurs, Stagiaires).
* **Emplois du temps :** Planification et publication des emplois du temps (Schedules) et des congés (Holidays).
* **Délibérations et Examens :** Collecte des notes, calcul automatique des moyennes, statistiques, et édition des fiches de délibérations (Admis/Ajourné).
* **Communication :** Envoi de messages et de notifications ciblées.

#### B. Espace Professeur
* **Espace Pédagogique :** Téléchargement de supports de cours (Lessons), création de devoirs (Homework).
* **Suivi et Évaluation :** Saisie des notes d'examens (Grades), correction des devoirs (Homework Submissions).
* **Assiduité :** Enregistrement des présences et absences des stagiaires (Attendance).

#### C. Espace Stagiaire
* **Consultation Pédagogique :** Accès aux emplois du temps, cours, et consignes de devoirs.
* **Interactions :** Remise des devoirs en ligne (Upload de fichiers).
* **Suivi Scolaire :** Consultation des notes d'examens (une fois publiées) et des messages administratifs.

### 4. Exigences Non Fonctionnelles
* **Sécurité :** Authentification robuste (Token JWT / Sanctum), mots de passe hachés, protection des routes API et vérification des rôles (Role-Based Access Control).
* **Interface Utilisateur (UI/UX) :** Design moderne, ergonomique et "Responsive" (adapté aux smartphones, tablettes et ordinateurs).
* **Performance :** Temps de réponse minimal grâce à une architecture SPA (Single Page Application).

### 5. Contraintes Techniques
* **Backend :** Laravel Framework (PHP).
* **Frontend :** Vue.js 3 avec l'outil de build Vite.
* **Design :** Tailwind CSS.
* **Base de données :** MySQL.

---

## 🇩🇿 النسخة العربية

### 1. سياق المشروع والأهداف
يسير المعهد المتخصص في التكوين المهني (INSFP) عدداً كبيراً من المتربصين، الأساتذة، والشعب للتدريب. الهدف من هذا المشروع هو رقمنة عملية التسيير البيداغوجي والإداري بالكامل عبر بناء منصة ويب مركزية سريعة وحديثة، تهدف إلى ربح الوقت، وتسهيل تداول المعلومات التربوية، وتقليل نسبة الأخطاء في حساب المعدلات وتسجيل الغيابات.

### 2. الفئات المستهدفة (مستخدمو النظام)
يصنف مستخدمو المنصة إلى ثلاثة أدوار رئيسية:
1. **الإدارة (Administrateurs):** مديرية الدراسات وطاقم الإدارة.
2. **الأساتذة/المكونون (Professeurs):** الطاقم البيداغوجي.
3. **المتربصون/الطلبة (Stagiaires):** المسجلون في مختلف دورات التكوين.

### 3. المتطلبات الوظيفية (Besoins Fonctionnels)

#### أ. فضاء الإدارة
* **تسيير الهيكلة المعهد:** إعداد الدورات التكوينية، الشعب (Specialties)، المعاملات، والمواد (Modules).
* **تسيير المستخدمين:** إضافة وتسجيل الطلبة (توليد أرقام التسجيل) والأساتذة.
* **الجداول الزمنية:** إعداد ونشر استعمال الزمن (Emploi du temps) وإدارة العطل.
* **المداولات والامتحانات:** حساب آلي للمعدلات الفردية انطلاقاً من النقاط المدخلة ومعاملات المواد، إصدار نتائج المداولات بشكل فوري وتحديد وضعية الطالب (ناجح/مؤجل).
* **التواصل:** إرسال رسائل أو إشعارات وتعميم الوثائق الإدارية.

#### ب. فضاء الأستاذ
* **المحتوى البيداغوجي:** رفع الدروس (PDF, Word وغيرها)، وإصدار الواجبات (Homeworks).
* **التقييم والمتابعة:** رصد علامات الامتحانات وتصحيح واجبات الطلبة.
* **المواظبة:** التدوين الرقمي لغيابات وحضور الطلبة في كل حصة دراسية.

#### ج. فضاء المتربص
* **المتابعة الدراسية:** الاطلاع على جدول التوقيت، تحميل محتوى الدروس.
* **الواجبات والتفاعل:** تسليم الواجبات رقمياً.
* **النتائج:** الاطلاع على كشوف النقاط وإشعارات المعهد.

### 4. المتطلبات غير الوظيفية (Besoins Non-Fonctionnels)
* **تأمين البيانات (Sécurité):** حماية الاتصال بواسطة (Sanctum/JWT)، تشفير كلمات المرور، وتقييد الصلاحيات كل حسب دوره.
* **تجربة المستخدم (UI/UX):** واجهة مستخدم عصرية وسريعة (تعمل دون إعادة تحميل الصفحة - SPA)، تتجاوب مع كل مقاسات الشاشات (الهواتف، اللوحات، الحواسيب).
* **السرعة والفعالية:** استجابة سريعة للطلبات من خلال معمارية الـ API المعتمدة.

### 5. القيود والتكنولوجيات المعتمدة
* **الواجهة الخلفية (الخادم):** إطار عمل لارافل (Laravel 11/10 - PHP).
* **الواجهة الأمامية (المستخدم):** فيو جي إس (Vue.js 3) مع مجمع حزم (Vite).
* **تصميم الواجهات:** تيلويند (Tailwind CSS).
* **قاعدة البيانات:** ماي إس كيو إل (MySQL).