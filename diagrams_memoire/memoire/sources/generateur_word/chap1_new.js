// Chapitre I — Étude préalable (informations officielles de l'INSFP Mohamed Tayeb Boucenna)
const L = require('./lib');
const { P, bullets, numbered, H2, H3, H4, chapter, partPage, T, figure, setChapter, note } = L;
const { AlignmentType } = L.d;
const CEN = AlignmentType.CENTER;

function chapitre1() {
  setChapter(1);
  return [
    ...partPage('Partie I', 'Partie théorique', 'Étude préalable'),
    ...chapter('Chapitre I', 'Étude préalable'),
    H2("1. Présentation de l'organisme d'accueil"),
    P("Notre stage s'est déroulé à l'**Institut National Spécialisé de la Formation Professionnelle Mohamed Tayeb Boucenna** (Hussein Dey, Alger), établissement public relevant du **Ministère de la Formation et de l'Enseignement Professionnels (MFEP)**, sous la tutelle de la **Direction de la Formation et de l'Enseignement Professionnels de la wilaya d'Alger (DFEP)**."),

    H3("1.1 Historique de l'entreprise"),
    ...numbered([
      "**1880** : création de l'établissement sous le nom d'« École des Pères Blancs » à El Mohammadia (anciennement Lavigerie), gérée par l'Association culturelle et éducative d'Algérie.",
      "**1976** : intégration au Ministère du Travail et des Affaires sociales en tant que **Centre de Formation Professionnelle et de l'Apprentissage (CFPA)**.",
      "**1990** : en application du décret n° 90-235/236 du 28 juillet 1990, l'établissement devient l'**Institut National de la Formation Professionnelle**.",
      "**1991** : le décret exécutif n° 91-395 fixe le statut-type des instituts nationaux spécialisés de la formation professionnelle (INSFP), dont relève l'institut.",
      "**Février 2016** : sur instruction de la tutelle, l'institut est transféré à **Hussein Dey**, en raison des travaux de construction de la Grande Mosquée d'Alger.",
    ], 'num'),
    ...figure('memoire/insfp/ancien_site.png', "Ancien site de l'institut à El Mohammadia", { maxW: 380 }),
    ...figure('memoire/insfp/site_hussein_dey.png', "L'INSFP Mohamed Tayeb Boucenna à Hussein Dey", { maxW: 440 }),
    P("L'institut a contribué à la formation de nombreuses promotions dans les spécialités de l'électronique industrielle et de la maintenance des équipements informatiques, grâce à une équipe de formateurs qualifiés. Il est situé rue de Tripoli (ex-Lafarge), à l'extrémité de la route nationale n° 5."),
    ...T("Fiche technique de l'institut", ['Rubrique', 'Information'], [
      ['Dénomination', 'Institut National Spécialisé de la Formation Professionnelle Mohamed Tayeb Boucenna'],
      ['Adresse', 'Rue de Tripoli (ex-Lafarge), Hussein Dey — Alger'],
      ["Date d'installation à Hussein Dey", 'Février 2016'],
      ["Code de l'établissement", '1647'],
      ["Capacité d'accueil théorique", '500 places pédagogiques'],
      ['Superficie totale', '24 360,2 m²'],
      ['Téléphone / fax', '044 31 17 77'],
      ['E-mail', 'insfpmohammadia@hotmail.com'],
    ], [32, 68]),
    note("Le code de l'établissement (**1647**) figure à la fin de chaque numéro d'inscription des stagiaires (ex. 0217124S**1647**).", 'info'),
    ...T('Filières et spécialités de l\'institut', ['Filière', 'Spécialité', 'Code'], [
      ['Informatique, numérique et télécommunications (INT)', 'Administration et sécurité des réseaux informatiques', 'INT0705'],
      ['', 'Maintenance des systèmes informatiques', 'INT0704'],
      ['', 'Bases de données', 'INT0703'],
      ['', 'Cybersécurité', 'INT2401'],
      ['', 'Développeur web et mobile', 'INT2201'],
      ['Électricité et électronique (ELE)', 'Électronique industrielle', 'ELE0718'],
      ['', 'Électrotechnique', 'ELE0712'],
      ['', 'Maintenance des équipements informatiques et de bureautique', 'ELE0716'],
      ['', 'Automatismes et régulation', 'ELE0719'],
    ], [38, 47, 15], { align: [null, null, CEN] }),
    H4('Modes de formation'),
    P("Toutes les spécialités préparent au **Brevet de Technicien Supérieur (BTS)**, diplôme d'État de **niveau 5**, en **30 mois**. L'accès est ouvert aux candidats de niveau 3e année secondaire, après un concours de sélection."),
    ...T('Modes de formation et spécialités proposées', ['Mode de formation', 'Principe', 'Spécialités'], [
      ['Formation résidentielle (présentielle)', "À plein temps à l'institut (théorie et travaux pratiques), complétée par un stage en milieu professionnel ; dès 16 ans", 'Électrotechnique, électronique industrielle, administration et sécurité des réseaux, maintenance des systèmes informatiques'],
      ['Formation par apprentissage', "En alternance entre l'institut et une entreprise, sur la base d'un contrat ; de 18 à 35 ans", 'Automatismes et régulation, bases de données, maintenance des systèmes informatiques'],
      ['Cours du soir (formation continue)', "Destinés surtout aux travailleurs souhaitant améliorer leur niveau ou se reconvertir", 'Cybersécurité, bases de données, développeur web et mobile, administration et sécurité des réseaux'],
    ], [22, 40, 38]),
    ...T('Effectifs de la session d\'octobre 2025', ['Spécialité', 'Inscrits', 'Dont filles', 'Intégrés', 'Dont filles'], [
      ['Électrotechnique', '63', '3', '52', '2'],
      ['Électronique industrielle', '44', '2', '31', '1'],
      ['Administration et sécurité des réseaux informatiques', '402', '82', '106', '41'],
      ['Maintenance des systèmes informatiques', '194', '113', '104', '43'],
      ['Cybersécurité', '484', '164', '122', '23'],
    ], [44, 14, 14, 14, 14], { align: [null, CEN, CEN, CEN, CEN] }),

    H3("1.2 Missions de l'organisme d'accueil"),
    ...numbered([
      "Former les stagiaires admis par concours dans les différentes spécialités, selon les périodes fixées par le ministère.",
      "Assurer une formation professionnelle initiale dans tous les modes de formation, sanctionnée par une qualification de **niveau 5 (technicien supérieur)**.",
      "Mettre à la disposition des stagiaires les moyens pédagogiques et des formateurs qualifiés.",
      "Organiser les concours d'accès et les examens.",
      "Placer les apprentis et les stagiaires en stage pratique en milieu professionnel.",
      "Apporter une assistance technique aux entreprises des différents secteurs économiques, à leur demande.",
      "Participer aux travaux d'études et de recherche avec les organismes intéressés.",
    ], 'num2'),

    H3("1.3 Organigramme de l'organisme d'accueil"),
    P("Sous l'autorité de la **directrice**, l'organisation interne de l'institut comprend **quatre sous-directions**, chacune composée de services :"),
    ...figure('memoire/uml/organigramme_insfp.png', "Organigramme de l'INSFP Mohamed Tayeb Boucenna"),
    ...T('Missions de la direction et des sous-directions', ['Structure', 'Principales missions'], [
      ['Directrice', "Coordonne et contrôle l'ensemble des activités administratives, techniques et pédagogiques ; exerce l'autorité hiérarchique sur le personnel, les formateurs et les stagiaires ; établit les plans annuels et les rapports d'activité."],
      ["Sous-direction de l'information, de l'orientation, de la numérisation et de l'insertion professionnelle", "Campagnes d'information sur l'offre de formation ; accueil, orientation et inscription des candidats ; journées de sélection ; numérisation de l'inscription ; suivi psychopédagogique des stagiaires."],
      ['Sous-direction des études et des stages', "Formation initiale en mode résidentiel ; organisation des concours d'accès ; coordination technique et pédagogique ; suivi des formateurs ; gestion des diplômes."],
      ["Sous-direction de l'apprentissage et de la formation professionnelle continue", "Formation par apprentissage ; formation continue par cours du soir ; plans annuels et pluriannuels d'apprentissage ; partenariats avec les entreprises."],
      ["Sous-direction de l'administration et des finances", "Moyens matériels et financiers ; budget ; gestion des ressources humaines ; archives de l'institut."],
    ], [32, 68]),

    H3("1.4 Organigramme de la structure d'accueil"),
    P("Notre stage s'est déroulé au sein de la **Sous-direction de l'apprentissage et de la formation professionnelle continue**, qui gère notamment les stagiaires des **cours du soir**, dont fait partie notre spécialité (développeur web et mobile)."),
    ...figure('memoire/uml/organigramme_structure.png', "Organigramme de la structure d'accueil"),

    H3("1.5 Missions de la structure d'accueil"),
    ...bullets([
      "Assurer la **formation professionnelle continue**, notamment par les **cours du soir**.",
      "Assurer la formation professionnelle initiale par **apprentissage** et préparer les plans annuels et pluriannuels d'apprentissage.",
      "Établir les **contrats d'apprentissage** et répartir les apprentis et les stagiaires par groupe.",
      "Préparer les **emplois du temps** généraux et individuels des formateurs et organiser les réunions pédagogiques.",
      "Organiser les **examens semestriels et finaux** et suivre les résultats.",
      "Développer les **partenariats** avec les secteurs économiques utilisateurs dans les domaines de formation de l'institut.",
    ]),

    H3('1.6 Moyens informatiques et humains'),
    H4('1.6.1 Moyens matériels et logiciels'),
    ...T("Structures pédagogiques et administratives", ['Structure', 'Nombre'], [
      ["Salles de cours", '12'], ['Laboratoires', '06'], ["Salles d'informatique", '10'],
      ['Salle de conférences', '01'], ['Bibliothèque', '01'],
      ['Ailes administratives', '02'], ['Bureaux', '19'],
      ["Bureau d'information, d'orientation et d'aide à l'insertion", '01'],
      ['Salle de réunions', '01'], ['Restaurant', '01'], ['Magasin', '01'],
    ], [75, 25], { align: [null, CEN] }),
    ...figure('memoire/insfp/salle_info.png', "Salle d'informatique de l'institut", { maxW: 380 }),
    ...T('Moyens informatiques et logiciels', ['Équipement / logiciel', 'Utilisation'], [
      ['Ordinateurs des 10 salles d\'informatique', 'Formation des stagiaires'],
      ['Postes des bureaux administratifs', 'Scolarité, pédagogie, comptabilité'],
      ['Imprimantes', 'Listes, relevés de notes, attestations'],
      ['Réseau local et accès Internet', 'Communication, plateforme takwin.dz'],
      ['Windows 10 / 11, Microsoft Office', 'Postes de travail et documents (Word, Excel)'],
    ], [45, 55]),
    H4('1.6.2 Moyens humains'),
    ...T('Personnel administratif', ['Grade', 'Effectif'], [
      ['Administrateur', '01'], ["Attaché principal d'administration", '03'], ["Attaché d'administration", '18'],
      ['Comptable administratif principal', '01'], ['Comptable administratif', '02'],
      ['Secrétaire de direction principale', '01'], ['Secrétaire de direction', '01'],
      ["Assistant ingénieur niveau 1", '02'], ['Ouvrier professionnel hors catégorie', '14'],
      ['Ouvrier professionnel de 1re catégorie', '12'], ["Conseillère d'orientation", '01'], ['Économe', '01'],
    ], [75, 25], { align: [null, CEN] }),
    ...T('Corps enseignant', ['Grade', 'Effectif'], [
      ["Professeur spécialisé de la formation et de l'enseignement professionnels chargé de l'ingénierie pédagogique", '39'],
      ['Professeur spécialisé de 2e grade', '17'],
      ['Professeur spécialisé de 1er grade', '14'],
      ['Professeur de la formation professionnelle', '03'],
    ], [75, 25], { align: [null, CEN] }),
    P("Au total, l'institut compte **57 agents administratifs et de soutien** et **73 formateurs**."),
  ];
}

module.exports = { chapitre1 };
