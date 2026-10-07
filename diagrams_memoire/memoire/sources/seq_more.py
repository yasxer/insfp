"""Diagrammes de séquence supplémentaires (importés par uml.py)."""
from uml import Seq, P3, fig


@fig("seq_generer_numero")
def seq_generer_numero():
    steps = [
        ("call", "u", "ihm", "choisir la session et la spécialité"),
        ("call", "ihm", "api", "POST /api/admin/generate-registration (session, spécialité)"),
        ("call", "api", "bd", "lire la session et l'offre (type d'étude)"),
        ("ret", "bd", "api", "session, offre"),
        ("alt", "session archivée"),
        ("ret", "api", "ihm", "422 « session archivée »"),
        ("ret", "ihm", "u", "afficher l'erreur"),
        ("else", "session en attente ou active"),
        ("self", "api", "api", "composer le suffixe : session + année + type + 16 + 47"),
        ("loop", "jusqu'à 5 tentatives"),
        ("call", "api", "bd", "lire le dernier numéro de même suffixe"),
        ("ret", "bd", "api", "dernier numéro"),
        ("self", "api", "api", "séquence = dernière + 1"),
        ("call", "api", "bd", "créer le numéro (non utilisé)"),
        ("ret", "bd", "api", "ok ou doublon (nouvelle tentative)"),
        ("end",),
        ("ret", "api", "ihm", "201 numéro généré"),
        ("ret", "ihm", "u", "afficher le numéro à remettre"),
        ("end",),
    ]
    return Seq(P3("Administration"), steps, gap=235).build()


@fig("seq_approbation")
def seq_approbation():
    steps = [
        ("call", "u", "ihm", "ouvrir les inscriptions en attente"),
        ("call", "ihm", "api", "GET /api/admin/pending-registrations"),
        ("call", "api", "bd", "stagiaires dont le compte est non approuvé"),
        ("ret", "bd", "api", "liste"),
        ("ret", "api", "ihm", "inscriptions en attente"),
        ("ret", "ihm", "u", "afficher la liste"),
        ("alt", "approuver"),
        ("call", "u", "ihm", "approuver l'inscription"),
        ("call", "ihm", "api", "POST /api/admin/students/{id}/approve"),
        ("call", "api", "bd", "is_approved = vrai"),
        ("ret", "bd", "api", "ok"),
        ("ret", "api", "ihm", "« inscription approuvée »"),
        ("else", "rejeter"),
        ("call", "u", "ihm", "rejeter (motif)"),
        ("call", "ihm", "api", "POST /api/admin/students/{id}/reject"),
        ("call", "api", "bd", "supprimer stagiaire et compte, libérer le numéro (transaction)"),
        ("ret", "bd", "api", "ok"),
        ("ret", "api", "ihm", "« inscription rejetée, numéro disponible »"),
        ("end",),
        ("ret", "ihm", "u", "mettre à jour la liste"),
    ]
    return Seq(P3("Administration"), steps, gap=235).build()


@fig("seq_seance_edt")
def seq_seance_edt():
    steps = [
        ("call", "u", "ihm", "saisir une séance (module, enseignant, jour, heures, salle)"),
        ("call", "ihm", "api", "POST /api/admin/schedules"),
        ("self", "api", "api", "valider ; déduire l'année académique de la session"),
        ("call", "api", "bd", "séances de l'enseignant qui chevauchent le créneau"),
        ("ret", "bd", "api", "conflit | aucun"),
        ("alt", "conflit enseignant"),
        ("ret", "api", "ihm", "422 « conflit enseignant »"),
        ("ret", "ihm", "u", "afficher le conflit"),
        ("else", "aucun conflit enseignant"),
        ("call", "api", "bd", "séances du groupe qui chevauchent le créneau"),
        ("ret", "bd", "api", "conflit | aucun"),
        ("alt", "conflit de groupe"),
        ("ret", "api", "ihm", "422 « conflit emploi du temps »"),
        ("ret", "ihm", "u", "afficher le conflit"),
        ("else", "créneau libre"),
        ("call", "api", "bd", "créer la séance"),
        ("ret", "bd", "api", "séance"),
        ("ret", "api", "ihm", "201 séance ajoutée"),
        ("ret", "ihm", "u", "afficher la séance dans la grille"),
        ("end",),
        ("end",),
    ]
    return Seq(P3("Administration"), steps, gap=235).build()


@fig("seq_creer_examen")
def seq_creer_examen():
    steps = [
        ("call", "u", "ihm", "saisir l'examen (titre, type, module, date, durée, groupe)"),
        ("call", "ihm", "api", "POST /api/teacher/exams"),
        ("call", "api", "bd", "vérifier l'affectation au module"),
        ("ret", "bd", "api", "affecté | non"),
        ("alt", "non affecté"),
        ("ret", "api", "ihm", "403 « accès non autorisé »"),
        ("ret", "ihm", "u", "afficher l'erreur"),
        ("else", "affecté"),
        ("call", "api", "bd", "lire la session active"),
        ("ret", "bd", "api", "année académique"),
        ("call", "api", "bd", "créer l'examen (brouillon, spécialité et semestre du module)"),
        ("ret", "bd", "api", "examen"),
        ("loop", "pour chaque stagiaire du module"),
        ("call", "api", "bd", "créer une notification « nouvel examen »"),
        ("ret", "bd", "api", "ok"),
        ("end",),
        ("ret", "api", "ihm", "201 examen créé"),
        ("ret", "ihm", "u", "afficher la carte de l'examen"),
        ("end",),
    ]
    return Seq(P3("Enseignant"), steps, gap=235).build()


@fig("seq_deliberation")
def seq_deliberation():
    steps = [
        ("call", "u", "ihm", "choisir la session, la spécialité et le semestre"),
        ("call", "ihm", "api", "GET /api/admin/deliberations"),
        ("call", "api", "bd", "stagiaires, notes du semestre, modules et délibérations"),
        ("ret", "bd", "api", "données"),
        ("loop", "pour chaque stagiaire"),
        ("self", "api", "api", "moyenne de chaque module puis moyenne pondérée par les coefficients"),
        ("self", "api", "api", "proposer le résultat (≥ 10 : admis)"),
        ("end",),
        ("ret", "api", "ihm", "moyennes calculées et résultats proposés"),
        ("ret", "ihm", "u", "afficher le tableau de délibération"),
        ("call", "u", "ihm", "vérifier, ajuster et confirmer"),
        ("call", "ihm", "api", "POST /api/admin/deliberations"),
        ("self", "api", "api", "valider (moyenne 0–20, résultat, date)"),
        ("call", "api", "bd", "créer ou mettre à jour la délibération"),
        ("ret", "bd", "api", "délibération"),
        ("ret", "api", "ihm", "200 délibération enregistrée"),
        ("ret", "ihm", "u", "mettre à jour la ligne"),
    ]
    return Seq(P3("Administration"), steps, gap=235).build()


@fig("seq_activation_session")
def seq_activation_session():
    parts = P3("Administration")
    parts.insert(3, ("svc", ":ServicePassage", "control"))
    steps = [
        ("call", "u", "ihm", "activer une session"),
        ("call", "ihm", "api", "POST /api/admin/sessions/{id}/activate"),
        ("call", "api", "bd", "lire la session"),
        ("ret", "bd", "api", "session"),
        ("alt", "déjà active ou mois de début non atteint"),
        ("ret", "api", "ihm", "422 message d'erreur"),
        ("ret", "ihm", "u", "afficher l'erreur"),
        ("else", "activable"),
        ("call", "api", "bd", "archiver la session active, activer celle-ci"),
        ("ret", "bd", "api", "ok"),
        ("call", "api", "svc", "executer()"),
        ("loop", "pour chaque stagiaire actif"),
        ("call", "svc", "bd", "dernière délibération du semestre courant"),
        ("ret", "bd", "svc", "délibération | aucune"),
        ("alt", "admis"),
        ("call", "svc", "bd", "semestre suivant, ou diplômé au dernier semestre"),
        ("ret", "bd", "svc", "ok"),
        ("else", "ajourné"),
        ("call", "svc", "bd", "créer une révision « en attente »"),
        ("ret", "bd", "svc", "ok"),
        ("end",),
        ("end",),
        ("ret", "svc", "api", "bilan (avancés, diplômés, révisions, ignorés)"),
        ("ret", "api", "ihm", "200 session activée + bilan"),
        ("ret", "ihm", "u", "afficher le bilan"),
        ("end",),
    ]
    return Seq(parts, steps, gap=215).build()


@fig("seq_document")
def seq_document():
    parts = P3("Administration") + [("fs", ":Stockage", "entity")]
    steps = [
        ("call", "u", "ihm", "choisir le fichier, le titre et la cible"),
        ("call", "ihm", "api", "POST /api/admin/documents"),
        ("self", "api", "api", "valider (fichier ≤ 50 Mo, cible, session, spécialités)"),
        ("alt", "données invalides"),
        ("ret", "api", "ihm", "422 erreurs de validation"),
        ("ret", "ihm", "u", "afficher les erreurs"),
        ("else", "données valides"),
        ("call", "api", "fs", "enregistrer le fichier"),
        ("ret", "fs", "api", "chemin"),
        ("call", "api", "bd", "créer le document (cible : enseignants, stagiaires, session ou spécialités)"),
        ("ret", "bd", "api", "document"),
        ("ret", "api", "ihm", "201 document publié"),
        ("ret", "ihm", "u", "afficher le document dans la liste"),
        ("end",),
    ]
    return Seq(parts, steps, gap=205).build()


@fig("seq_chatbot")
def seq_chatbot():
    parts = [("u", "Visiteur", "actor"), ("ihm", ":Interface", "boundary"), ("api", ":API Laravel", "control"),
             ("bd", ":Base de données", "entity"), ("gem", ":API Gemini", "boundary")]
    steps = [
        ("call", "u", "ihm", "poser une question"),
        ("call", "ihm", "api", "POST /api/chatbot (message)"),
        ("call", "api", "bd", "spécialités actives et sessions ouvertes"),
        ("ret", "bd", "api", "contexte"),
        ("self", "api", "api", "construire la requête : consignes + contexte + question"),
        ("alt", "clé API configurée"),
        ("call", "api", "gem", "generateContent(requête)"),
        ("ret", "gem", "api", "réponse générée"),
        ("else", "clé absente ou erreur du service"),
        ("self", "api", "api", "réponse de secours"),
        ("end",),
        ("ret", "api", "ihm", "réponse"),
        ("ret", "ihm", "u", "afficher la réponse"),
    ]
    return Seq(parts, steps, gap=205).build()


if __name__ == "__main__":
    import os, sys
    from svglib import render
    out = sys.argv[1]
    names = ["seq_generer_numero", "seq_approbation", "seq_seance_edt", "seq_creer_examen",
             "seq_deliberation", "seq_activation_session", "seq_document", "seq_chatbot"]
    import uml
    for n in names:
        render(uml.FIG[n](), os.path.join(out, n + ".png"))
        print(n)
