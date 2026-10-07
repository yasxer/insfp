import json
SIO, SFPC, DIR, SURV, ENS, STG, TK, DFEP = 'sio', 'sfpc', 'dir', 'surv', 'ens', 'stg', 'takwin', 'dfep'
NOM = {SIO: "Service de l'information", SFPC: 'Service de la formation continue', DIR: 'Direction', SURV: 'Service de la surveillance',
       ENS: 'Enseignant', STG: 'Stagiaire', TK: 'takwin.dz', DFEP: 'DFEP'}
FL = [
    ('F1', DFEP, DIR, 'Correspondance de la DFEP', 'D1'),
    ('F1', DIR, SIO, 'Correspondance de la DFEP', 'D1'),
    ('F2', SIO, TK, 'Calendrier de la session', 'D2'),
    ('F2', SIO, STG, 'Calendrier de la session', 'D2'),
    ('F3', SIO, TK, 'Fiche descriptive de la spécialité', 'D3'),
    ('F4', TK, SIO, 'Liste des candidats inscrits', '—'),
    ('F5', STG, SIO, "Dossier d'inscription", '—'),
    ('F6', SIO, SFPC, 'Liste des stagiaires admis', '—'),
    ('F7', SFPC, ENS, 'Emploi du temps', 'D4'),
    ('F7', SFPC, STG, 'Emploi du temps', 'D4'),
    ('F8', ENS, SURV, "Feuille d'absences", 'D5'),
    ('F9', SFPC, ENS, 'Planning des examens semestriels', 'D6'),
    ('F9', SFPC, STG, 'Planning des examens semestriels', 'D6'),
    ('F10', ENS, SFPC, 'Document de transfert des notes', 'D7'),
    ('F11', SFPC, DIR, 'Procès-verbal des délibérations', 'D8'),
    ("F11'", DIR, SFPC, 'Procès-verbal des délibérations signé', 'D8'),
    ('F12', SURV, SFPC, 'État des absences des stagiaires', '—'),
    ('F13', SFPC, DIR, 'Relevé de notes', 'D9'),
    ("F13'", DIR, SFPC, 'Relevé de notes signé', 'D9'),
    ("F13'", SFPC, STG, 'Relevé de notes signé', 'D9'),
    ('F14', STG, SFPC, "Demande d'attestation de stage", '—'),
    ('F15', SFPC, DIR, 'Attestation de stage', 'D10'),
    ("F15'", DIR, SFPC, 'Attestation de stage signée', 'D10'),
    ("F15'", SFPC, STG, 'Attestation de stage signée', 'D10'),
]

# script du diagramme
p = '../figs/flux3.py'; s = open(p, encoding='utf-8').read()
a = s.index('FLOWS = ['); b = s.index(']\n', a) + 2
body = 'FLOWS = [\n' + ''.join(f"    ({n!r}, {x!r}, {y!r}, {t!r}, {d!r}),\n" for n, x, y, t, d in FL) + ']\n'
s = s[:a] + body + s[b:]
s = s.replace("s.text(mx, my + 4.3, f'F{n}', 11, 'middle', 'bold', NAVY)", "s.text(mx, my + 4.3, n, 11, 'middle', 'bold', NAVY)")
s = s.replace("s.rect(mx - 15, my - 10, 30, 20,", "s.rect(mx - 17, my - 10, 34, 20,")
open(p, 'w', encoding='utf-8').write(s)

# tableau du mémoire
p = 'content1.js'; s = open(p, encoding='utf-8').read()
a = s.index('const FLUX = ['); b = s.index('];\n', a) + 3
js = 'const FLUX = [\n' + ''.join(f"  [{json.dumps(n, ensure_ascii=False)}, {json.dumps(t, ensure_ascii=False)}, {json.dumps(NOM[x], ensure_ascii=False)}, {json.dumps(NOM[y], ensure_ascii=False)}, {json.dumps(d, ensure_ascii=False)}],\n" for n, x, y, t, d in FL) + '];\n'
s = s[:a] + js + s[b:]
old = "FLUX.map((f) => [f[0], f[4] !== '—' ? `${f[1]} (${f[4]})` : f[1]])"
assert old in s
s = s.replace(old, "FLUX.map((f) => [f[0], f[1]])")
s = s.replace("**Le flux externe** : échange d'informations entre un acteur interne et un acteur externe (stagiaire, plateforme takwin.dz, DFEP).",
              "**Le flux externe** : échange d'informations entre un acteur interne et un acteur externe (stagiaire, plateforme takwin.dz, DFEP). Les échanges entre deux acteurs externes ne sont pas représentés.")
s = s.replace("XXX",
              "Le diagramme montre les **documents** échangés et l'ordre de ces échanges ; un document envoyé pour signature puis retourné signé porte le même numéro marqué d'un prime (ex. : F15 puis F15'),")
open(p, 'w', encoding='utf-8').write(s)
print('ok', len(FL))
