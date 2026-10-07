p = 'content2.js'
lines = open(p, encoding='utf-8').read().split('\n')
start = next(i for i, l in enumerate(lines) if "memoire/uml/seq_authentification.png" in l)
end = next(i for i, l in enumerate(lines) if "memoire/uml/seq_devoir.png" in l)

def f(name, cap):
    return f"    ...figure('memoire/uml/{name}.png', \"Diagramme de séquence : {cap}\", {{ maxH: 860 }}),"

new = [
    "    H4('4.5.1 Scénarios communs et visiteur'),",
    f('seq_authentification', 'authentification'),
    f('seq_inscription', "inscription en ligne d'un stagiaire"),
    f('seq_chatbot', "utilisation de l'assistant intelligent"),
    "    H4('4.5.2 Scénarios de l\\'administration'),",
    f('seq_generer_numero', "génération d'un numéro d'inscription"),
    f('seq_approbation', "approbation ou rejet d'une inscription"),
    f('seq_activation_session', "activation d'une session et passage de semestre"),
    f('seq_seance_edt', "ajout d'une séance à l'emploi du temps"),
    f('seq_deliberation', 'délibération semestrielle'),
    f('seq_document', "publication d'un document"),
    "    H4('4.5.3 Scénarios de l\\'enseignant'),",
    f('seq_creer_examen', "création d'un examen"),
    f('seq_notes', "saisie des notes d'un examen"),
    f('seq_presence', "faire l'appel d'une séance"),
    "    H4('4.5.4 Scénarios du stagiaire'),",
    f('seq_devoir', "remise d'un devoir"),
]
lines[start:end + 1] = new
open(p, 'w', encoding='utf-8').write('\n'.join(lines))
print('ok')
