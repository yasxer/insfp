// Dictionnaire de données, règles de gestion et MOR (en français, issus de l'étude des documents)
const L = require('./lib');
const { P, numbered, H4, T, code, note } = L;
const { AlignmentType } = L.d;
const CEN = AlignmentType.CENTER;
const S = 'Système';

const DICO = [
  ["Nom de l'utilisateur", 'A', '100'],
  ["Prénom de l'utilisateur", 'A', '100'],
  ['Adresse électronique', 'AN', '255'],
  ['Numéro de téléphone', 'AN', '20'],
  ['Mot de passe chiffré', 'AN', '255'],
  ["Rôle de l'utilisateur", 'E', '20'],
  ['Compte approuvé', 'B', '1'],
  ["Numéro d'inscription du stagiaire", 'AN', '20'],
  ['Date de naissance', 'D', '10'],
  ['Lieu de naissance', 'A', '100'],
  ['Adresse du stagiaire', 'AN', '500'],
  ['Mode de formation', 'E', '20'],
  ['Semestre en cours', 'N', '1'],
  ['Groupe du stagiaire', 'AN', '10'],
  ["Nombre d'années d'inscription", 'N', '1'],
  ['Stagiaire diplômé', 'B', '1'],
  ["Année d'obtention du diplôme", 'N', '4'],
  ['Moyenne finale de la formation', 'N', '4,2'],
  ['Stagiaire exclu', 'B', '1'],
  ["Motif de l'exclusion", 'AN', '255'],
  ["Spécialisation de l'enseignant", 'AN', '255'],
  ["Grade de l'enseignant", 'AN', '100'],
  ["Fonction de l'agent administratif", 'AN', '255'],
  ['Code de la filière', 'AN', '3'],
  ['Intitulé de la filière', 'AN', '100'],
  ['Code de la spécialité', 'AN', '10'],
  ['Intitulé de la spécialité', 'AN', '255'],
  ['Définition de la spécialité', 'AN', '1000'],
  ['Missions principales du diplômé', 'AN', '1000'],
  ['Aptitudes requises', 'AN', '255'],
  ['Débouchés', 'AN', '255'],
  ['Niveau de qualification', 'N', '1'],
  ["Niveau d'accès", 'AN', '50'],
  ['Diplôme délivré', 'AN', '100'],
  ['Durée de la formation en mois', 'N', '2'],
  ['Durée de la formation en semestres', 'N', '1'],
  ['Spécialité ouverte', 'B', '1'],
  ['Code du module', 'AN', '10'],
  ['Intitulé du module', 'AN', '255'],
  ['Semestre du module', 'N', '1'],
  ['Coefficient du module', 'N', '3,1'],
  ['Note éliminatoire du module', 'N', '4,2'],
  ['Volume horaire hebdomadaire', 'N', '2'],
  ['Année de formation', 'AN', '9'],
  ["Mois d'ouverture de la session", 'N', '2'],
  ["Année d'ouverture de la session", 'N', '4'],
  ['Date de début de la formation', 'D', '10'],
  ['Date de fin de la formation', 'D', '10'],
  ['État de la session', 'E', '20'],
  ['Numéro du semestre', 'N', '1'],
  ['Date de début du semestre', 'D', '10'],
  ['Date de fin du semestre', 'D', '10'],
  ["Numéro d'inscription généré", 'AN', '20'],
  ['Numéro déjà utilisé', 'B', '1'],
  ['Jour de la séance', 'E', '20'],
  ['Heure de début', 'H', '5'],
  ['Heure de fin', 'H', '5'],
  ['Salle', 'AN', '50'],
  ['Emploi du temps publié', 'B', '1'],
  ['Date de la séance', 'D', '10'],
  ['Statut de présence', 'E', '20'],
  ["Motif de l'absence", 'AN', '255'],
  ['Titre', 'AN', '255'],
  ['Chemin du fichier joint', 'AN', '500'],
  ['Date limite de remise', 'DH', '19'],
  ['Type de remise', 'E', '20'],
  ['Réponse du stagiaire', 'T', '1000'],
  ['Note du devoir', 'N', '4,2'],
  ['État de la remise', 'E', '20'],
  ["Type d'évaluation", 'E', '20'],
  ["Date et heure de l'examen", 'DH', '19'],
  ["Durée de l'examen en minutes", 'N', '3'],
  ["État de l'examen", 'E', '20'],
  ["Note d'un contrôle ou d'un examen", 'N', '4,2'],
  ["Absence à l'épreuve", 'B', '1'],
  ['Remarque sur la note', 'AN', '255'],
  ['Note de rattrapage', 'N', '4,2'],
  ['Phase de la délibération', 'E', '20'],
  ['Moyenne du semestre', 'N', '4,2'],
  ['Décision du jury', 'E', '20'],
  ['Observation du jury', 'AN', '255'],
  ['Date de la délibération', 'D', '10'],
  ['Décision de la révision', 'E', '20'],
  ['Objet du message', 'AN', '255'],
  ['Contenu du message', 'T', '1000'],
  ['Type de destinataire', 'E', '20'],
  ['Date de lecture', 'DH', '19'],
  ['Cible du document', 'E', '20'],
  ['Date de validité du document', 'D', '10'],
];

function dictionnaire() {
  return [
    P("Le dictionnaire de données regroupe les informations relevées dans les fiches d'analyse des documents D1 à D10, complétées par les données propres au nouveau système (comptes, états, fichiers). Les informations calculées (moyennes, totaux, nombres d'absences) n'y figurent pas : elles sont obtenues par les opérations des classes. Types : **A** alphabétique, **AN** alphanumérique, **N** numérique, **D** date, **H** heure, **DH** date et heure, **B** booléen, **E** énuméré, **T** texte long."),
    ...T('Dictionnaire de données', ['Désignation de la donnée', 'Type', 'Taille'], DICO, [64, 18, 18],
      { size: 20, align: [null, CEN, CEN] }),
  ];
}

function regles() {
  return numbered([
    "Un utilisateur possède un et un seul rôle : administration, enseignant ou stagiaire.",
    "Un compte créé par inscription est **non approuvé** ; il ne peut pas se connecter tant que l'administration ne l'a pas approuvé. Un rejet supprime le compte et libère le numéro d'inscription.",
    "Une filière regroupe une ou plusieurs spécialités ; une spécialité appartient à une seule filière.",
    "Une session ne peut être ouverte qu'en **février** ou en **septembre** ; elle dure 30 mois et comprend cinq semestres datés ; il ne peut exister qu'une session par mois et par année.",
    "Une session est créée « en attente » ; elle ne peut être activée qu'à partir de son mois de début ; activer une session archive la session active précédente.",
    "Une session propose une ou plusieurs spécialités selon un ou plusieurs modes de formation ; la combinaison session, spécialité et mode est unique.",
    "Un numéro d'inscription est généré pour une session non archivée et une spécialité proposée ; il est unique et utilisable une seule fois ; il se termine par le code de l'établissement (1647).",
    "Un stagiaire appartient à une seule spécialité et à une seule offre de formation.",
    "Une spécialité regroupe un ou plusieurs modules ; un module appartient à une seule spécialité et à un seul semestre ; il possède un coefficient et une note éliminatoire.",
    "Un enseignant peut enseigner plusieurs modules et un module peut être enseigné par plusieurs enseignants ; l'affectation est unique par enseignant, module et année de formation.",
    "Une séance ne peut chevaucher ni une autre séance du même enseignant, ni une séance du même groupe.",
    "La présence d'un stagiaire est unique pour une séance et une date.",
    "Une évaluation est un **contrôle** ou un **examen** ; elle est créée en brouillon par un enseignant affecté au module ; une fois soumise, toute modification la fait passer à l'état « modifié ».",
    "Une note est comprise entre 0 et 20 ; un stagiaire absent à une épreuve est marqué absent ; un stagiaire a au plus une note par évaluation.",
    "La moyenne d'un module est égale à (contrôle 1 + contrôle 2 + 2 × examen) / 4 ; la moyenne du semestre est la somme des moyennes des modules multipliées par leurs coefficients, divisée par la somme des coefficients.",
    "Avant rattrapage, le stagiaire est **admis** si sa moyenne est supérieure ou égale à 10 ; sinon il passe le **rattrapage** dans les modules dont la moyenne est inférieure à 10 ; sans aucune note, il est déclaré en **abandon**.",
    "Une délibération est unique par stagiaire, semestre, année de formation et phase (avant ou après rattrapage).",
    "À l'activation d'une session, un stagiaire admis passe au semestre suivant ou est diplômé au dernier semestre ; un stagiaire non admis fait l'objet d'une révision (redoubler, passer, exclure ou reporter).",
    "Un stagiaire ne remet qu'une seule fois par devoir (sa remise peut être mise à jour) ; le fichier joint ne dépasse pas 10 Mo.",
    "Un document peut cibler tous les enseignants, tous les stagiaires, les stagiaires d'une session ou ceux de certaines spécialités d'une session.",
  ], 'num6');
}

function morRegles() {
  return numbered([
    "**Classe** : chaque classe devient une table ; ses attributs deviennent des colonnes ; un identifiant devient la clé primaire.",
    "**Association un-à-plusieurs** : la clé primaire de la table côté « 1 » migre comme clé étrangère dans la table côté « plusieurs » (ex. : module.#id_specialite).",
    "**Association plusieurs-à-plusieurs** : elle devient une table de jointure contenant les deux clés étrangères (ex. : membre_encadrement, document_specialite).",
    "**Classe d'association** : elle devient une table qui porte les deux clés étrangères et ses propres attributs (ex. : affectation avec annee_formation).",
    "**Généralisation** : chaque sous-classe devient une table liée à la table de la super-classe par une clé étrangère unique (stagiaire.#id_utilisateur).",
    "**Composition** : la clé étrangère est assortie d'une suppression en cascade.",
    "**Type énuméré** : il devient une colonne dont les valeurs sont contrôlées.",
    "**Contraintes d'unicité** issues des règles de gestion : index uniques (ex. : deliberation (id_stagiaire, semestre, annee_formation, phase)).",
  ], 'num3');
}

const MOR = [['utilisateur', '!id_utilisateur, nom, prenom, email, telephone, mot_de_passe, role, est_approuve'], ['stagiaire', '!id_stagiaire, #id_utilisateur, #id_specialite, #id_offre, num_inscription, date_naissance, lieu_naissance, adresse, mode_formation, semestre_courant, groupe, annees_inscription, est_diplome, annee_diplome, moyenne_finale, est_exclu, motif_exclusion'], ['enseignant', '!id_enseignant, #id_utilisateur, specialisation, grade'], ['administration', '!id_administration, #id_utilisateur, fonction'], ['filiere', '!id_filiere, code_filiere, intitule_filiere'], ['specialite', '!id_specialite, #id_filiere, code_specialite, intitule_specialite, mode_formation, description, missions, aptitudes_requises, debouches, niveau_qualification, niveau_acces, diplome, duree_mois, duree_semestres, est_active'], ['module', '!id_module, #id_specialite, code_module, intitule_module, semestre_module, coefficient, note_eliminatoire, volume_horaire'], ['affectation', '!id_affectation, #id_enseignant, #id_module, annee_formation'], ['session', '!id_session, mois_session, annee_session, date_debut_formation, date_fin_formation, etat_session'], ['semestre_session', '!id_semestre, #id_session, num_semestre, date_debut_semestre, date_fin_semestre'], ['offre_formation', '!id_offre, #id_session, #id_specialite, mode_formation'], ['numero_inscription', '!id_numero, #id_session, #id_specialite, numero, est_utilise, annee_formation'], ['seance', '!id_seance, #id_session, #id_module, #id_enseignant, #id_specialite, mode_formation, groupe, jour, heure_debut, heure_fin, salle, semestre_module, annee_formation'], ['publication_edt', '!id_publication, #id_session, #id_specialite, semestre_module, mode_formation, groupe, est_publie'], ['presence', '!id_presence, #id_seance, #id_stagiaire, #id_enseignant, date_presence, statut_presence, motif'], ['cours', '!id_cours, #id_module, #id_enseignant, titre, description, chemin_fichier'], ['devoir', '!id_devoir, #id_module, #id_enseignant, titre, description, chemin_fichier, date_limite, type_remise'], ['remise_devoir', '!id_remise, #id_devoir, #id_stagiaire, reponse, chemin_fichier, note_devoir, etat_remise'], ['examen', '!id_examen, #id_module, #id_specialite, #id_enseignant, titre, type_evaluation, etat_examen, date_examen, duree, salle, semestre_module, groupe, mode_formation, annee_formation'], ['note', '!id_note, #id_stagiaire, #id_module, #id_examen, valeur_note, est_absent, remarque, annee_formation'], ['note_rattrapage', '!id_note_rattrapage, #id_stagiaire, #id_module, note_rattrapage, annee_formation'], ['deliberation', '!id_deliberation, #id_stagiaire, semestre_module, annee_formation, phase, moyenne, resultat, observations, date_deliberation'], ['revision_passage', '!id_revision, #id_stagiaire, semestre_module, annee_formation, moyenne, etat_revision'], ['message', '!id_message, #id_expediteur, #id_destinataire, objet, contenu, type_destinataire'], ['lecture_message', '!id_lecture, #id_message, #id_utilisateur, date_lecture'], ['notification', '!id_notification, #id_utilisateur, titre, contenu, date_lecture'], ['document', '!id_document, #id_administration, #id_session, titre, description, chemin_fichier, cible, date_validite'], ['document_specialite', '!#id_document, !#id_specialite'], ['groupe_encadrement', '!id_groupe, #id_enseignant, #id_specialite, titre, description, annee_formation'], ['membre_encadrement', '!#id_groupe, !#id_stagiaire'], ['rendez_vous', '!id_rendez_vous, #id_groupe, date_rendez_vous, ordre_du_jour, etat']];

function mor() {
  const { Paragraph, TextRun } = L.d;
  const lines = MOR.map(([t, cols]) => {
    const runs = [new TextRun({ text: `${t} (`, bold: true })];
    cols.split(', ').forEach((c, i) => {
      if (i) runs.push(new TextRun({ text: ', ' }));
      const pk = c.startsWith('!');
      const name = pk ? c.slice(1) : c;
      runs.push(new TextRun({ text: name, underline: pk ? {} : undefined, bold: pk }));
    });
    runs.push(new TextRun({ text: ')', bold: true }));
    return new Paragraph({ children: runs, numbering: { reference: 'puces', level: 0 }, spacing: { after: 70, line: 280 } });
  });
  return [
    P("Légende : la clé primaire est **soulignée** ; la clé étrangère est précédée de **#**."),
    ...lines,
  ];
}

module.exports = { dictionnaire, regles, morRegles, mor };
