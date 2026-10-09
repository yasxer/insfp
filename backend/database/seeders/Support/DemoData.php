<?php

namespace Database\Seeders\Support;

/**
 * Static description of the demo institute, shared by every demo seeder.
 *
 * Story: the "Session Février 2026" period is ending. Contrôles and examens are
 * graded, the rattrapage is done for DWM and still to be graded for ASR, a few
 * deliberations remain, and "Session Septembre 2026" (new intake, pending
 * registrations) is ready to be activated.
 */
class DemoData
{
    public const PASSWORD = 'password';

    /** Academic year of the active period (Session Février 2026). */
    public const CURRENT_YEAR = '2025-2026';

    /**
     * Sessions (intakes). The active one is the current teaching period; the
     * pending one is the next intake, already past its start date so it can be
     * activated during the demo.
     */
    public const SESSIONS = [
        ['month' => 9, 'year' => 2023, 'status' => 'archived'],
        ['month' => 9, 'year' => 2024, 'status' => 'archived'],
        ['month' => 2, 'year' => 2025, 'status' => 'archived'],
        ['month' => 9, 'year' => 2025, 'status' => 'archived'],
        ['month' => 2, 'year' => 2026, 'status' => 'active'],
        ['month' => 9, 'year' => 2026, 'status' => 'pending'],
    ];

    /** Session-specialty type => student study_mode. */
    public const STUDY_MODES = [
        'presential'    => 'initial',
        'apprentissage' => 'alternance',
        'cours_soir'    => 'continue',
    ];

    /**
     * Teachers: key => [first name, last name, specialization].
     * Emails are prenom.nom@insfp.dz.
     */
    public const TEACHERS = [
        'benali'    => ['Karim', 'Benali', 'Développement informatique'],
        'haddad'    => ['Samira', 'Haddad', 'Développement informatique'],
        'mansouri'  => ['Yacine', 'Mansouri', 'Développement informatique'],
        'cherif'    => ['Nadia', 'Cherif', 'Bases de données'],
        'boudiaf'   => ['Rachid', 'Boudiaf', 'Bases de données'],
        'khelifi'   => ['Farid', 'Khelifi', 'Réseaux et systèmes'],
        'saidi'     => ['Leila', 'Saidi', 'Réseaux et systèmes'],
        'zerrouki'  => ['Mourad', 'Zerrouki', 'Sécurité informatique'],
        'brahimi'   => ['Hakim', 'Brahimi', 'Maintenance informatique'],
        'meziane'   => ['Souad', 'Meziane', 'Enseignement général'],
        'bouzid'    => ['Amel', 'Bouzid', 'Enseignement général'],
        'ferhat'    => ['Djamel', 'Ferhat', 'Enseignement général'],
        'kaci'      => ['Lamia', 'Kaci', 'Développement informatique'],
        'taleb'     => ['Omar', 'Taleb', 'Réseaux et systèmes'],
    ];

    /**
     * Specialties and their 5-semester programme.
     * Module: [code suffix, name, coefficient, weekly slots of 1h30, teacher key, room type]
     * Room types: salle, info, reseau, atelier.
     */
    public static function specialties(): array
    {
        return [
            'DWM' => [
                'name' => 'Développement Web et Mobile',
                'study_mode' => 'initial',
                'description' => 'Conception et réalisation d’applications web et mobiles : interfaces, services, bases de données.',
                'modules' => [
                    1 => [
                        ['ALG', 'Algorithmique et programmation', 4, 3, 'kaci', 'info'],
                        ['ARC', 'Architecture des ordinateurs', 2, 2, 'brahimi', 'salle'],
                        ['BDI', 'Bases de données : fondamentaux', 3, 2, 'cherif', 'info'],
                        ['MAT', 'Mathématiques appliquées', 2, 2, 'meziane', 'salle'],
                        ['ANG', 'Anglais technique 1', 1, 1, 'bouzid', 'salle'],
                    ],
                    2 => [
                        ['POO', 'Programmation orientée objet (Java)', 4, 3, 'haddad', 'info'],
                        ['WEB', 'Développement web : HTML, CSS, JavaScript', 4, 3, 'benali', 'info'],
                        ['SQL', 'Langage SQL et MySQL', 3, 2, 'cherif', 'info'],
                        ['RES', 'Notions de réseaux', 2, 1, 'khelifi', 'reseau'],
                        ['ANG', 'Anglais technique 2', 1, 1, 'bouzid', 'salle'],
                    ],
                    3 => [
                        ['PHP', 'Développement back-end PHP / Laravel', 4, 3, 'benali', 'info'],
                        ['VUE', 'Framework front-end (Vue.js)', 3, 2, 'benali', 'info'],
                        ['MOB', 'Développement mobile (Flutter)', 4, 3, 'mansouri', 'info'],
                        ['UML', 'Génie logiciel et UML', 2, 2, 'haddad', 'salle'],
                        ['EXP', 'Expression et communication', 1, 1, 'ferhat', 'salle'],
                    ],
                    4 => [
                        ['API', 'Services web et API REST', 3, 2, 'benali', 'info'],
                        ['MOA', 'Développement mobile avancé', 4, 3, 'mansouri', 'info'],
                        ['SAW', 'Sécurité des applications web', 3, 2, 'zerrouki', 'info'],
                        ['DEV', 'Git, tests et déploiement', 2, 2, 'haddad', 'info'],
                        ['ENT', 'Entrepreneuriat', 1, 1, 'ferhat', 'salle'],
                    ],
                    5 => [
                        ['STG', 'Stage pratique en entreprise', 4, 0, 'haddad', 'salle'],
                        ['MEM', 'Mémoire de fin d’études', 4, 1, 'benali', 'salle'],
                        ['LEG', 'Législation du travail', 1, 1, 'ferhat', 'salle'],
                    ],
                ],
            ],
            'ASR' => [
                'name' => 'Administration des Systèmes et Réseaux',
                'study_mode' => 'initial',
                'description' => 'Installation, administration et supervision des réseaux et des serveurs d’entreprise.',
                'modules' => [
                    1 => [
                        ['RLC', 'Réseaux locaux et câblage', 4, 3, 'khelifi', 'reseau'],
                        ['SYS', 'Systèmes d’exploitation (Windows)', 3, 2, 'saidi', 'info'],
                        ['ARC', 'Architecture des ordinateurs', 3, 2, 'brahimi', 'salle'],
                        ['MAT', 'Mathématiques appliquées', 2, 2, 'meziane', 'salle'],
                        ['ANG', 'Anglais technique 1', 1, 1, 'bouzid', 'salle'],
                    ],
                    2 => [
                        ['TCP', 'Protocoles TCP/IP et adressage', 4, 3, 'khelifi', 'reseau'],
                        ['LIN', 'Administration Linux', 4, 3, 'saidi', 'info'],
                        ['WSR', 'Windows Server', 3, 2, 'saidi', 'info'],
                        ['ANG', 'Anglais technique 2', 1, 1, 'bouzid', 'salle'],
                        ['EXP', 'Expression écrite et orale', 1, 1, 'ferhat', 'salle'],
                    ],
                    3 => [
                        ['ROU', 'Routage et commutation', 4, 3, 'khelifi', 'reseau'],
                        ['SRV', 'Services réseaux (DNS, DHCP, Web)', 3, 2, 'saidi', 'info'],
                        ['VIR', 'Virtualisation', 3, 2, 'taleb', 'info'],
                        ['SCR', 'Scripts d’administration (Bash, PowerShell)', 2, 2, 'saidi', 'info'],
                        ['ANG', 'Anglais technique 3', 1, 1, 'bouzid', 'salle'],
                    ],
                    4 => [
                        ['SUP', 'Supervision et maintenance réseau', 3, 2, 'taleb', 'reseau'],
                        ['SER', 'Sécurité des réseaux', 4, 3, 'zerrouki', 'reseau'],
                        ['WAN', 'Réseaux étendus et VPN', 3, 2, 'khelifi', 'reseau'],
                        ['CLD', 'Cloud et services d’entreprise', 2, 2, 'taleb', 'info'],
                        ['ENT', 'Législation et entrepreneuriat', 1, 1, 'ferhat', 'salle'],
                    ],
                    5 => [
                        ['STG', 'Stage pratique en entreprise', 4, 0, 'taleb', 'salle'],
                        ['MEM', 'Mémoire de fin d’études', 4, 1, 'khelifi', 'salle'],
                        ['LEG', 'Législation du travail', 1, 1, 'ferhat', 'salle'],
                    ],
                ],
            ],
            'BDD' => [
                'name' => 'Administration des Bases de Données',
                'study_mode' => 'initial',
                'description' => 'Conception, exploitation et optimisation des systèmes de gestion de bases de données.',
                'modules' => [
                    1 => [
                        ['ALG', 'Algorithmique et programmation', 4, 3, 'kaci', 'info'],
                        ['BDI', 'Introduction aux bases de données', 4, 3, 'cherif', 'info'],
                        ['SYS', 'Systèmes d’exploitation', 2, 2, 'saidi', 'info'],
                        ['MAT', 'Mathématiques et statistiques', 2, 1, 'meziane', 'salle'],
                        ['ANG', 'Anglais technique 1', 1, 1, 'bouzid', 'salle'],
                    ],
                    2 => [
                        ['MER', 'Modélisation Merise et UML', 4, 3, 'boudiaf', 'salle'],
                        ['SQL', 'SQL avancé', 4, 3, 'cherif', 'info'],
                        ['PYT', 'Programmation Python', 3, 2, 'kaci', 'info'],
                        ['RES', 'Notions de réseaux', 2, 1, 'khelifi', 'reseau'],
                        ['ANG', 'Anglais technique 2', 1, 1, 'bouzid', 'salle'],
                    ],
                    3 => [
                        ['ORA', 'Administration Oracle', 4, 3, 'cherif', 'info'],
                        ['PLS', 'Programmation PL/SQL', 4, 3, 'cherif', 'info'],
                        ['SGB', 'SGBD MySQL et PostgreSQL', 3, 2, 'boudiaf', 'info'],
                        ['EXP', 'Expression et communication', 1, 1, 'ferhat', 'salle'],
                        ['ANG', 'Anglais technique 3', 1, 1, 'bouzid', 'salle'],
                    ],
                    4 => [
                        ['OPT', 'Optimisation et tuning des requêtes', 4, 3, 'boudiaf', 'info'],
                        ['NSQ', 'Bases NoSQL (MongoDB)', 3, 2, 'boudiaf', 'info'],
                        ['SAU', 'Sauvegarde, restauration et haute disponibilité', 3, 2, 'boudiaf', 'info'],
                        ['DWH', 'Entrepôts de données et BI', 3, 2, 'cherif', 'info'],
                        ['ENT', 'Entrepreneuriat', 1, 1, 'ferhat', 'salle'],
                    ],
                    5 => [
                        ['STG', 'Stage pratique en entreprise', 4, 0, 'boudiaf', 'salle'],
                        ['MEM', 'Mémoire de fin d’études', 4, 1, 'cherif', 'salle'],
                        ['LEG', 'Législation du travail', 1, 1, 'ferhat', 'salle'],
                    ],
                ],
            ],
            'SEC' => [
                'name' => 'Sécurité Informatique',
                'study_mode' => 'initial',
                'description' => 'Protection des systèmes d’information, audit de sécurité et gestion des incidents.',
                'modules' => [
                    1 => [
                        ['FRS', 'Fondamentaux des réseaux', 4, 3, 'khelifi', 'reseau'],
                        ['SYS', 'Systèmes d’exploitation Linux et Windows', 3, 2, 'saidi', 'info'],
                        ['PYT', 'Programmation Python', 3, 2, 'kaci', 'info'],
                        ['ISI', 'Introduction à la sécurité de l’information', 2, 2, 'zerrouki', 'salle'],
                        ['ANG', 'Anglais technique 1', 1, 1, 'bouzid', 'salle'],
                    ],
                    2 => [
                        ['CRY', 'Cryptographie', 4, 3, 'zerrouki', 'salle'],
                        ['SSY', 'Sécurité des systèmes', 4, 3, 'zerrouki', 'info'],
                        ['TCP', 'Protocoles TCP/IP', 3, 2, 'khelifi', 'reseau'],
                        ['MAT', 'Mathématiques discrètes', 2, 2, 'meziane', 'salle'],
                        ['ANG', 'Anglais technique 2', 1, 1, 'bouzid', 'salle'],
                    ],
                    3 => [
                        ['PEN', 'Tests d’intrusion', 4, 3, 'zerrouki', 'reseau'],
                        ['PFW', 'Pare-feu et détection d’intrusion', 4, 3, 'khelifi', 'reseau'],
                        ['SAW', 'Sécurité des applications web', 3, 2, 'zerrouki', 'info'],
                        ['EXP', 'Expression et communication', 1, 1, 'ferhat', 'salle'],
                        ['ANG', 'Anglais technique 3', 1, 1, 'bouzid', 'salle'],
                    ],
                    4 => [
                        ['FOR', 'Investigation numérique (forensic)', 4, 3, 'zerrouki', 'info'],
                        ['GRI', 'Gestion des risques et normes ISO 27001', 3, 2, 'taleb', 'salle'],
                        ['SOC', 'Supervision de la sécurité (SOC)', 3, 2, 'taleb', 'reseau'],
                        ['CLS', 'Sécurité du cloud', 2, 2, 'taleb', 'info'],
                        ['ENT', 'Entrepreneuriat', 1, 1, 'ferhat', 'salle'],
                    ],
                    5 => [
                        ['STG', 'Stage pratique en entreprise', 4, 0, 'zerrouki', 'salle'],
                        ['MEM', 'Mémoire de fin d’études', 4, 1, 'zerrouki', 'salle'],
                        ['LEG', 'Législation du travail', 1, 1, 'ferhat', 'salle'],
                    ],
                ],
            ],
            'MNT' => [
                'name' => 'Maintenance Informatique',
                'study_mode' => 'alternance',
                'description' => 'Diagnostic, dépannage et maintenance du matériel et des parcs informatiques.',
                'modules' => [
                    1 => [
                        ['ELN', 'Électronique de base', 3, 2, 'brahimi', 'atelier'],
                        ['APC', 'Architecture et assemblage des PC', 4, 2, 'brahimi', 'atelier'],
                        ['INS', 'Installation des systèmes', 3, 1, 'saidi', 'info'],
                        ['ANG', 'Anglais technique 1', 1, 1, 'bouzid', 'salle'],
                    ],
                    2 => [
                        ['DIA', 'Diagnostic et dépannage matériel', 4, 2, 'brahimi', 'atelier'],
                        ['PER', 'Périphériques et imprimantes', 3, 2, 'brahimi', 'atelier'],
                        ['RLO', 'Réseaux locaux', 3, 1, 'khelifi', 'reseau'],
                        ['EXP', 'Expression et communication', 1, 1, 'ferhat', 'salle'],
                    ],
                    3 => [
                        ['POR', 'Maintenance des ordinateurs portables', 4, 2, 'brahimi', 'atelier'],
                        ['PAR', 'Gestion de parc informatique', 3, 2, 'brahimi', 'atelier'],
                        ['SAV', 'Sauvegarde et récupération de données', 3, 1, 'brahimi', 'atelier'],
                        ['SPT', 'Sécurité du poste de travail', 2, 1, 'zerrouki', 'info'],
                    ],
                    4 => [
                        ['SRV', 'Maintenance des serveurs', 4, 2, 'brahimi', 'atelier'],
                        ['TEL', 'Téléphonie et équipements mobiles', 3, 2, 'brahimi', 'atelier'],
                        ['HLP', 'Support utilisateurs (helpdesk)', 2, 1, 'taleb', 'salle'],
                        ['ENT', 'Entrepreneuriat', 1, 1, 'ferhat', 'salle'],
                    ],
                    5 => [
                        ['STG', 'Stage pratique en entreprise', 4, 0, 'brahimi', 'salle'],
                        ['MEM', 'Mémoire de fin d’études', 4, 1, 'brahimi', 'salle'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Cohorts: intake session, specialty, study type, size, and the semester they
     * are in during the active period. A cohort past its last semester is graduated.
     */
    public const COHORTS = [
        'ASR23' => ['session' => [9, 2023], 'specialty' => 'ASR', 'type' => 'presential', 'size' => 8, 'semester' => 5, 'graduated' => true],
        'BDD24' => ['session' => [9, 2024], 'specialty' => 'BDD', 'type' => 'presential', 'size' => 10, 'semester' => 4],
        'MNT25' => ['session' => [2, 2025], 'specialty' => 'MNT', 'type' => 'apprentissage', 'size' => 8, 'semester' => 3],
        'DWM25' => ['session' => [9, 2025], 'specialty' => 'DWM', 'type' => 'presential', 'size' => 14, 'semester' => 2, 'groups' => ['G1', 'G2']],
        'ASR25' => ['session' => [9, 2025], 'specialty' => 'ASR', 'type' => 'presential', 'size' => 12, 'semester' => 2],
        'SEC26' => ['session' => [2, 2026], 'specialty' => 'SEC', 'type' => 'presential', 'size' => 10, 'semester' => 1],
    ];

    /** Specialties offered by the pending intake (Session Septembre 2026). */
    public const NEXT_INTAKE = [
        ['DWM', 'presential'], ['ASR', 'presential'], ['BDD', 'presential'],
        ['SEC', 'presential'], ['MNT', 'apprentissage'],
    ];

    /**
     * Scenario students with planned module averages for the current semester
     * (module code suffix => average). Contrôles are average ± 0.5 and the examen
     * equals the average, so the module average is exactly the planned value.
     * 'rattrapage' marks are already published (DWM) — ASR's are graded live.
     */
    public const PROFILES = [
        'DWM25' => [
            // 9.07 before -> retakes POO + SQL -> 10.54 after: admis après rattrapage
            ['Amine', 'Belkacem', 'grades' => ['POO' => 7.5, 'WEB' => 10, 'SQL' => 8, 'RES' => 10.5, 'ANG' => 12],
                'rattrapage' => ['POO' => 14, 'SQL' => 13]],
            // 7.71 before -> retakes 4 modules -> 8.27 after: ajourné
            ['Walid', 'Hamidi', 'grades' => ['POO' => 6.5, 'WEB' => 8, 'SQL' => 7, 'RES' => 9, 'ANG' => 11],
                'rattrapage' => ['POO' => 8, 'WEB' => 9.5, 'SQL' => 7.5, 'RES' => 10], 'absent' => true],
            // 10.68: admis without rattrapage even with POO below 10
            ['Lina', 'Mebarki', 'grades' => ['POO' => 8.5, 'WEB' => 11, 'SQL' => 11.5, 'RES' => 12, 'ANG' => 13]],
            // Top of the class
            ['Yasmine', 'Ouali', 'grades' => ['POO' => 17, 'WEB' => 18, 'SQL' => 16.5, 'RES' => 15, 'ANG' => 17.5]],
        ],
        'ASR25' => [
            // 9.62 -> retakes LIN + WSR (both Leila Saidi): graded live during the demo
            ['Sofiane', 'Lounis', 'grades' => ['TCP' => 10.5, 'LIN' => 8, 'WSR' => 9, 'ANG' => 12, 'EXP' => 12]],
            // 8.81 -> retakes LIN + WSR
            ['Rania', 'Touati', 'grades' => ['TCP' => 10, 'LIN' => 7, 'WSR' => 8.5, 'ANG' => 10, 'EXP' => 11], 'absent' => true],
            // 10.23: admis, TCP below 10 but no rattrapage
            ['Ilyes', 'Guerroudj', 'grades' => ['TCP' => 9, 'LIN' => 10.5, 'WSR' => 10, 'ANG' => 13, 'EXP' => 12]],
        ],
        'BDD24' => [
            ['Meriem', 'Rahmani', 'grades' => ['OPT' => 16, 'NSQ' => 17, 'SAU' => 15.5, 'DWH' => 16, 'ENT' => 15]],
        ],
    ];

    public const FIRST_NAMES = [
        'Mohamed', 'Yacine', 'Karim', 'Sara', 'Imane', 'Nour', 'Mehdi', 'Anis', 'Riad', 'Nassim',
        'Hichem', 'Bilal', 'Oussama', 'Khadidja', 'Asma', 'Chaima', 'Ikram', 'Abdelkader', 'Islam', 'Zakaria',
        'Ayoub', 'Fares', 'Rayan', 'Feriel', 'Wissem', 'Selma', 'Nesrine', 'Lydia', 'Kenza', 'Hadjer',
        'Manel', 'Adel', 'Salim', 'Hamza', 'Yanis', 'Amina', 'Houda', 'Samy', 'Racha', 'Djamila',
        'Massinissa', 'Thiziri', 'Aymen', 'Rym', 'Abderrahmane', 'Nadir', 'Sabrina', 'Lotfi', 'Malak', 'Ismail',
    ];

    public const LAST_NAMES = [
        'Bouzidi', 'Haddadi', 'Mansour', 'Belaid', 'Cherifi', 'Khelil', 'Saadi', 'Djebbar', 'Boudjema', 'Hamdi',
        'Zerrougui', 'Brahmi', 'Amrani', 'Meziani', 'Benamara', 'Ferhati', 'Kaddour', 'Larbi', 'Messaoudi', 'Rahmouni',
        'Talbi', 'Yahiaoui', 'Ziani', 'Bensalem', 'Ait Ali', 'Mebarek', 'Slimani', 'Boukhalfa', 'Chikhi', 'Benyahia',
        'Benmoussa', 'Aouadi', 'Bakhti', 'Chaouch', 'Dahmani', 'Fellah', 'Ghezali', 'Hadjadj', 'Kebir', 'Lahlou',
        'Mokrani', 'Nouri', 'Ouchene', 'Rezig', 'Sahraoui', 'Taibi', 'Zeghdoudi', 'Bourouba', 'Khaldi', 'Remili',
    ];

    public const ROOMS = [
        'salle'   => ['Salle 01', 'Salle 02', 'Salle 03', 'Salle 04', 'Salle 05', 'Salle 06'],
        'info'    => ['Labo Informatique 1', 'Labo Informatique 2', 'Labo Informatique 3'],
        'reseau'  => ['Labo Réseaux 1', 'Labo Réseaux 2'],
        'atelier' => ['Atelier Maintenance'],
    ];

    /** Teaching slots, matching the timetable grids of the platform. */
    public const SLOTS = [
        ['08:00', '09:30'], ['09:30', '11:00'], ['11:00', '12:30'], ['13:00', '14:30'], ['14:30', '16:00'],
    ];

    /** Algerian week, Sunday to Thursday. Apprentices come two days a week. */
    public const DAYS = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'];
    public const APPRENTICE_DAYS = ['sunday', 'monday'];

    /** "2025-2026" for a date, the academic year starting in September. */
    public static function academicYear(\DateTimeInterface $date): string
    {
        $y = (int) $date->format('Y');
        return (int) $date->format('n') >= 9 ? "$y-" . ($y + 1) : ($y - 1) . "-$y";
    }

    /** ASCII e-mail part from a name ("Ait Ali" -> "aitali", "Méziane" -> "meziane"). */
    public static function slug(string $s): string
    {
        $s = strtr($s, ['é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'à' => 'a', 'â' => 'a', 'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'û' => 'u', 'ù' => 'u', 'ç' => 'c']);
        return strtolower(preg_replace('/[^A-Za-z]/', '', $s));
    }
}
