"""Diagrammes UML et figures d'analyse du mémoire INSFP."""
import math, os, sys, datetime as dt
from svglib import Svg, render, wrap

OUT = sys.argv[1] if len(sys.argv) > 1 else "out"
os.makedirs(OUT, exist_ok=True)
FILL, STROKE = "#E6F3F5", "#03909E"


# =====================================================================
#  CAS D'UTILISATION
# =====================================================================
class UseCase:
    def __init__(self, w, h, title, boundary, bname):
        self.s = Svg(w, h)
        self.title, self.boundary, self.bname = title, boundary, bname
        self.uc, self.act = {}, {}
        self.links = []

    def arc(self, items, actor, L, y0, step):
        ax, ay = self.act[actor][0], self.act[actor][1]
        ys = [y0 + i * step for i in range(len(items))]
        R = max(abs(y - ay) for y in ys) + (L - ax) * 0.55
        for (id, t), y in zip(items, ys):
            self.u(id, ax + (R * R - (y - ay) ** 2) ** 0.5, y, t)
            self.L("assoc", actor, id)

    def u(self, id, x, y, text, rx=None):
        lines = wrap(text, 150)
        rx = rx or max(78, max(len(l) for l in lines) * 3.6 + 26)
        self.uc[id] = (x, y, rx, 14 + len(lines) * 9, lines)

    def a(self, id, x, y, name, system=False):
        self.act[id] = (x, y, name, system)

    def L(self, kind, a, b, label=None):
        self.links.append((kind, a, b, label))

    def anchor(self, id, tx, ty):
        if id in self.uc:
            x, y, rx, ry, _ = self.uc[id]
            dx, dy = tx - x, ty - y
            t = 1 / math.sqrt((dx / rx) ** 2 + (dy / ry) ** 2) if (dx or dy) else 0
            return x + dx * t, y + dy * t
        x, y, _, sysf = self.act[id]
        if sysf:
            return (x + (55 if tx > x else -55), y)
        return (x + (16 if tx > x else -16), y + 6)

    def center(self, id):
        return self.uc[id][:2] if id in self.uc else (self.act[id][0], self.act[id][1] + 6)

    def draw(self):
        s = self.s
        bx, by, bw, bh = self.boundary
        s.rect(bx, by, bw, bh, fill="#fbfbff", stroke="#444", sw=1.4)
        s.text(bx + bw / 2, by + 24, self.bname, 14, "middle", "bold", "#222")
        for kind, a, b, label in self.links:
            ca, cb = self.center(a), self.center(b)
            p1, p2 = self.anchor(a, *cb), self.anchor(b, *ca)
            if kind == "assoc":
                s.line(*p1, *p2, "#333", 1.3)
            elif kind == "gen":
                s.line(*p1, *p2, "#333", 1.3, marker="tri")
            else:  # include / extend
                s.line(*p1, *p2, "#333", 1.2, dash="6,4", marker="open")
                mx, my = (p1[0] + p2[0]) / 2, (p1[1] + p2[1]) / 2
                s.text(mx, my - 4, "«" + kind + "»", 11, "middle", italic=True, fill="#333", halo=True)
        for id, (x, y, rx, ry, lines) in self.uc.items():
            s.ellipse(x, y, rx, ry, fill=FILL, stroke=STROKE, sw=1.4)
            s.mtext(x, y, lines, 12, 14)
        for id, (x, y, name, sysf) in self.act.items():
            if sysf:
                s.rect(x - 55, y - 28, 110, 56, fill="white", stroke="#333", sw=1.3)
                s.text(x, y - 8, "«système»", 11, "middle", italic=True)
                s.text(x, y + 12, name, 12.5, "middle", "bold")
            else:
                s.circle(x, y - 30, 10, fill="white", stroke="#222", sw=1.5)
                s.path(f"M{x},{y-20} L{x},{y+12} M{x-16},{y-10} L{x+16},{y-10} M{x},{y+12} L{x-12},{y+32} M{x},{y+12} L{x+12},{y+32}", "#222", 1.5)
                for i, l in enumerate(wrap(name, 110)):
                    s.text(x, y + 50 + i * 15, l, 12.5, "middle", "bold")
        return s


FIG = {}


def fig(name):
    def deco(fn):
        FIG[name] = fn
        return fn
    return deco


@fig("uc_global")
def uc_global():
    d = UseCase(1250, 940, "", (400, 20, 600, 900), "Plateforme INSFP")
    d.a("vis", 190, 110, "Visiteur (futur stagiaire)")
    d.a("usr", 190, 400, "Utilisateur authentifié")
    d.a("adm", 80, 640, "Administration")
    d.a("ens", 200, 740, "Enseignant")
    d.a("stg", 320, 830, "Stagiaire")
    d.a("gem", 1150, 170, "API Gemini", system=True)
    d.u("insc", 580, 80, "S'inscrire en ligne")
    d.u("chat", 580, 170, "Utiliser l'assistant (chatbot)")
    d.u("auth", 580, 290, "S'authentifier")
    d.u("prof", 580, 370, "Gérer son profil")
    d.u("msg", 580, 450, "Consulter la messagerie")
    d.u("docs", 580, 530, "Consulter les documents")
    d.u("gadm", 850, 640, "Administrer l'institut")
    d.u("gped", 850, 740, "Assurer le suivi pédagogique")
    d.u("gstg", 850, 830, "Suivre sa scolarité")
    d.L("assoc", "vis", "insc"); d.L("assoc", "vis", "chat"); d.L("assoc", "chat", "gem")
    for u in ("auth", "prof", "msg", "docs"):
        d.L("assoc", "usr", u)
    d.L("gen", "adm", "usr"); d.L("gen", "ens", "usr"); d.L("gen", "stg", "usr")
    d.L("assoc", "adm", "gadm"); d.L("assoc", "ens", "gped"); d.L("assoc", "stg", "gstg")
    d.L("include", "gadm", "auth"); d.L("include", "gped", "auth"); d.L("include", "gstg", "auth")
    return d.draw()


@fig("uc_admin")
def uc_admin():
    d = UseCase(1300, 1000, "", (220, 20, 1050, 960), "Plateforme INSFP — Espace administration")
    d.a("adm", 100, 470, "Administration")
    L, R = 450, 1090
    items = [("dash", "Consulter le tableau de bord"), ("sess", "Gérer les sessions (promotions)"),
             ("spec", "Gérer les spécialités et les modules"), ("num", "Générer les numéros d'inscription"),
             ("stg", "Gérer les stagiaires"), ("ens", "Gérer les enseignants"), ("edt", "Élaborer l'emploi du temps"),
             ("exa", "Consulter les examens et les notes"), ("del", "Délibérer"), ("rev", "Traiter les révisions de passage"),
             ("doc", "Publier des documents"), ("msg", "Envoyer des messages")]
    d.arc(items, "adm", L, 90, 74)
    d.u("act", R, 110, "Activer une session")
    d.u("pas", R, 215, "Lancer le passage de semestre")
    d.u("aff", R, 320, "Affecter un enseignant à un module")
    d.u("app", R, 420, "Approuver / rejeter une inscription")
    d.u("conf", R, 530, "Détecter les conflits d'horaire")
    d.u("pub", R, 630, "Publier l'emploi du temps")
    d.L("extend", "act", "sess"); d.L("include", "act", "pas")
    d.L("extend", "aff", "spec"); d.L("extend", "app", "stg")
    d.L("include", "edt", "conf"); d.L("extend", "pub", "edt")
    s = d.draw()
    s.rect(790, 900, 440, 46, fill="#fffbe6", stroke="#999", sw=1)
    s.text(802, 920, "Pré-condition commune : l'administration est authentifiée", 11.5, fill="#444", italic=True)
    s.text(802, 936, "(cas « S'authentifier » inclus — voir le diagramme global).", 11.5, fill="#444", italic=True)
    return s


@fig("uc_enseignant")
def uc_enseignant():
    d = UseCase(1200, 720, "", (220, 20, 950, 680), "Plateforme INSFP — Espace enseignant")
    d.a("ens", 100, 340, "Enseignant")
    L, R = 450, 1000
    items = [("dash", "Consulter son tableau de bord"), ("mod", "Consulter ses modules et leurs stagiaires"),
             ("edt", "Consulter son emploi du temps"), ("cours", "Déposer des supports de cours"),
             ("app", "Faire l'appel d'une séance"), ("exa", "Gérer les examens"),
             ("dev", "Gérer les devoirs"), ("docs", "Consulter les documents")]
    d.arc(items, "ens", L - 10, 80, 72)
    d.u("notes", R, 450, "Saisir les notes d'un examen")
    d.u("soum", R, 380, "Soumettre l'examen")
    d.u("noter", R, 560, "Noter une remise")
    d.u("hist", R, 300, "Consulter l'historique des présences")
    d.L("extend", "notes", "exa"); d.L("extend", "soum", "exa"); d.L("extend", "noter", "dev"); d.L("extend", "hist", "app")
    s = d.draw()
    s.rect(700, 630, 440, 46, fill="#fffbe6", stroke="#999", sw=1)
    s.text(712, 650, "Pré-condition commune : l'enseignant est authentifié et", 11.5, fill="#444", italic=True)
    s.text(712, 666, "affecté au module concerné.", 11.5, fill="#444", italic=True)
    return s


@fig("uc_stagiaire")
def uc_stagiaire():
    d = UseCase(1200, 760, "", (220, 20, 950, 720), "Plateforme INSFP — Espace stagiaire")
    d.a("stg", 100, 360, "Stagiaire")
    L, R = 450, 1000
    items = [("dash", "Consulter son tableau de bord"), ("edt", "Consulter son emploi du temps"),
             ("cours", "Consulter / télécharger les cours"), ("notes", "Consulter ses notes et examens"),
             ("abs", "Consulter son assiduité"), ("dev", "Consulter les devoirs"),
             ("del", "Consulter ses délibérations"), ("doc", "Télécharger les documents"), ("prof", "Gérer son profil")]
    d.arc(items, "stg", L - 10, 80, 70)
    d.u("rem", R, 430, "Remettre un devoir")
    d.u("fic", R, 540, "Joindre un fichier")
    d.u("comp", R, 640, "Compléter le profil (1re connexion)")
    d.L("extend", "rem", "dev"); d.L("extend", "fic", "rem"); d.L("extend", "comp", "prof")
    s = d.draw()
    s.rect(700, 690, 440, 34, fill="#fffbe6", stroke="#999", sw=1)
    s.text(712, 712, "Pré-condition commune : le compte est approuvé et authentifié.", 11.5, fill="#444", italic=True)
    return s


# =====================================================================
#  SÉQUENCE
# =====================================================================
class Seq:
    """parts : [(id, nom, kind)] kind ∈ actor|boundary|control|entity ; steps : messages et fragments."""

    def __init__(self, parts, steps, width=None, gap=205):
        self.parts, self.steps, self.gap = parts, steps, gap
        self.x = {p[0]: 110 + i * gap for i, p in enumerate(parts)}
        self.W = width or 110 + (len(parts) - 1) * gap + 150

    def build(self):
        top, row = 110, 42
        y = top + 30
        acts = []  # (pid, y0, y1, depth)
        stack = {}
        frags = []
        items = []
        import copy
        snaps = []
        for st in self.steps:
            k = st[0]
            if k in ("alt", "opt", "loop"):
                y += 6
                frags.append([k, st[1], y - 14, None, [], len(frags)])
                snaps.append(copy.deepcopy(stack))
                y += 38
            elif k == "else":
                # fermer les activations ouvertes dans la branche puis restaurer l'état d'entrée
                for pid, ys in stack.items():
                    for y0 in ys:
                        acts.append((pid, y0, y - 10))
                stack = {pid: [y - 2 for _ in ys] for pid, ys in snaps[-1].items()}
                frags[-1][4].append((y - 6, st[1]))
                y += 40
            elif k == "end":
                f = frags.pop()
                snaps.pop()
                f[3] = y - 6
                items.append(("frag", f))
                y += 34
            else:
                items.append(("msg", st, y))
                a, b = st[1], st[2]
                if k == "call":
                    stack.setdefault(b, []).append(y)
                elif k == "ret":
                    if stack.get(a):
                        acts.append((a, stack[a].pop(), y))
                elif k == "self":
                    acts.append((a, y + 4, y + 26, 1))
                    y += 18 + 14 * max(0, len(wrap(st[3], 230)) - 1)
                y += row
        H = y + 60
        for pid, ys in stack.items():
            for y0 in ys:
                acts.append((pid, y0, H - 70))
        s = Svg(self.W, H)
        # participants
        for pid, name, kind in self.parts:
            x = self.x[pid]
            if kind == "actor":
                s.circle(x, 34, 9, fill="white", stroke="#222", sw=1.5)
                s.path(f"M{x},{43} L{x},{68} M{x-14},{52} L{x+14},{52} M{x},{68} L{x-11},{86} M{x},{68} L{x+11},{86}", "#222", 1.5)
                s.text(x, 102, name, 12.5, "middle", "bold")
            else:
                w = max(120, len(name) * 7.2 + 20)
                s.rect(x - w / 2, 40, w, 50, fill=FILL, stroke=STROKE, sw=1.4)
                s.text(x, 60, "«" + kind + "»", 10.5, "middle", italic=True, fill="#444")
                s.text(x, 78, name, 12.5, "middle", "bold")
                s.add(f'<line x1="{x-w/2+10}" y1="81" x2="{x+w/2-10}" y2="81" stroke="none"/>')
            s.line(x, 104 if kind == "actor" else 90, x, H - 40, "#777", 1, dash="5,4")
        # fragments (dessous)
        for kind, it in [i for i in items if i[0] == "frag"]:
            k, guard, y0, y1, elses, depth = it
            xs = [self.x[p[0]] for p in self.parts]
            fx0, fx1 = min(xs) - 60 + depth * 14, max(xs) + 70 - depth * 14
            s.rect(fx0, y0, fx1 - fx0, y1 - y0, fill="none", stroke="#555", sw=1.2)
            s.path(f"M{fx0},{y0} h46 v14 l-8,8 H{fx0} z", fill="#f0f0f0", stroke="#555", sw=1.2)
            s.text(fx0 + 8, y0 + 16, k, 12, weight="bold")
            s.text(fx0 + 56, y0 + 16, "[" + guard + "]", 11.5, italic=True, fill="#333")
            for ey, eg in elses:
                s.line(fx0, ey, fx1, ey, "#555", 1.1, dash="7,4")
                s.text(fx0 + 56, ey + 16, "[" + eg + "]", 11.5, italic=True, fill="#333")
        # barres d'activation
        for a in acts:
            pid, y0, y1 = a[0], a[1], a[2]
            dx = 7 if len(a) > 3 else 0
            x = self.x[pid] + dx
            s.rect(x - 6, y0 - 4, 12, y1 - y0 + 8, fill="white", stroke="#333", sw=1.2)
        # messages
        for kind, st, y in [i for i in items if i[0] == "msg"]:
            k, a, b, label = st
            xa, xb = self.x[a], self.x[b]
            if k == "self":
                s.path(f"M{xa+6},{y} h42 v22 h-36", "#333", 1.2, marker="ar")
                for i, l in enumerate(wrap(label, 230)):
                    s.text(xa + 54, y + 14 + i * 14, l, 11.5, halo=True)
                continue
            dirn = 1 if xb > xa else -1
            x1, x2 = xa + 6 * dirn, xb - 6 * dirn
            if k == "call":
                s.line(x1, y, x2, y, "#333", 1.3, marker="ar")
            else:
                s.line(x1, y, x2, y, "#333", 1.2, dash="6,4", marker="open")
            lines = wrap(label, abs(xb - xa) - 20)
            for i, l in enumerate(lines):
                s.text((x1 + x2) / 2, y - 6 - (len(lines) - 1 - i) * 13, l, 11.5, "middle", halo=True)
        return s


def P3(actor):
    return [("u", actor, "actor"), ("ihm", ":Interface", "boundary"), ("api", ":API Laravel", "control"), ("bd", ":Base de données", "entity")]


@fig("seq_authentification")
def seq_auth():
    steps = [
        ("call", "u", "ihm", "saisir identifiant et mot de passe"),
        ("call", "ihm", "api", "POST /api/login (identifiant, mot de passe)"),
        ("alt", "identifiant = e-mail"),
        ("call", "api", "bd", "rechercher l'utilisateur par e-mail"),
        ("ret", "bd", "api", "utilisateur | null"),
        ("else", "sinon (n° d'inscription)"),
        ("call", "api", "bd", "rechercher le stagiaire par n° d'inscription"),
        ("ret", "bd", "api", "stagiaire.utilisateur | null"),
        ("end",),
        ("self", "api", "api", "vérifier le mot de passe (hachage)"),
        ("alt", "compte introuvable ou mot de passe incorrect"),
        ("ret", "api", "ihm", "422 « identifiants incorrects »"),
        ("ret", "ihm", "u", "afficher le message d'erreur"),
        ("else", "compte non approuvé"),
        ("ret", "api", "ihm", "403 « inscription en attente d'approbation »"),
        ("ret", "ihm", "u", "afficher le message d'attente"),
        ("else", "compte valide et approuvé"),
        ("self", "api", "api", "ouvrir la session (cookie httpOnly)"),
        ("ret", "api", "ihm", "200 utilisateur + profil complet ?"),
        ("ret", "ihm", "u", "rediriger vers l'espace du rôle (ou compléter le profil)"),
        ("end",),
    ]
    return Seq(P3("Utilisateur"), steps, gap=235).build()


@fig("seq_inscription")
def seq_inscription():
    steps = [
        ("call", "u", "ihm", "saisir le numéro d'inscription"),
        ("call", "ihm", "api", "POST /api/lookup-registration (numéro)"),
        ("call", "api", "bd", "rechercher le numéro"),
        ("ret", "bd", "api", "numéro (session, spécialité, is_used)"),
        ("alt", "numéro introuvable ou déjà utilisé"),
        ("ret", "api", "ihm", "404 / 422 message d'erreur"),
        ("ret", "ihm", "u", "afficher l'erreur"),
        ("else", "numéro valide"),
        ("call", "api", "bd", "lire les modes d'étude proposés"),
        ("ret", "bd", "api", "offres (session, spécialité, type)"),
        ("ret", "api", "ihm", "session, spécialité, modes d'étude"),
        ("ret", "ihm", "u", "pré-remplir le formulaire"),
        ("end",),
        ("call", "u", "ihm", "saisir ses informations et le mode d'étude"),
        ("call", "ihm", "api", "POST /api/register (données)"),
        ("call", "api", "bd", "vérifier numéro + cohérence session / spécialité / mode"),
        ("ret", "bd", "api", "résultat de la vérification"),
        ("alt", "incohérent"),
        ("ret", "api", "ihm", "422 erreurs de validation"),
        ("ret", "ihm", "u", "afficher les erreurs"),
        ("else", "cohérent (transaction)"),
        ("call", "api", "bd", "créer l'utilisateur (non approuvé) et le stagiaire"),
        ("call", "api", "bd", "marquer le numéro comme utilisé"),
        ("ret", "bd", "api", "ok"),
        ("ret", "api", "ihm", "201 inscription enregistrée"),
        ("ret", "ihm", "u", "« inscription réussie, en attente d'approbation »"),
        ("end",),
    ]
    return Seq(P3("Stagiaire"), steps, gap=235).build()


@fig("seq_notes")
def seq_notes():
    steps = [
        ("call", "u", "ihm", "ouvrir la saisie des notes d'un examen"),
        ("call", "ihm", "api", "GET /api/teacher/exams/{id}/students"),
        ("call", "api", "bd", "stagiaires de la spécialité et du semestre de l'examen"),
        ("ret", "bd", "api", "liste + notes existantes"),
        ("ret", "api", "ihm", "liste des stagiaires"),
        ("ret", "ihm", "u", "afficher la grille de saisie"),
        ("call", "u", "ihm", "saisir les notes (sur 20)"),
        ("call", "ihm", "api", "POST /api/teacher/exams/{id}/results"),
        ("self", "api", "api", "vérifier l'affectation au module, les notes (0–20) et les stagiaires"),
        ("alt", "données invalides"),
        ("ret", "api", "ihm", "403 / 422 message d'erreur"),
        ("ret", "ihm", "u", "afficher l'erreur"),
        ("else", "données valides"),
        ("call", "api", "bd", "créer ou mettre à jour les notes (transaction)"),
        ("ret", "bd", "api", "ok"),
        ("ret", "api", "ihm", "200 notes enregistrées"),
        ("ret", "ihm", "u", "confirmation"),
        ("end",),
    ]
    return Seq(P3("Enseignant"), steps, gap=235).build()


@fig("seq_presence")
def seq_presence():
    steps = [
        ("call", "u", "ihm", "choisir une séance et une date"),
        ("call", "ihm", "api", "demander les stagiaires de la séance (séance, date)"),
        ("self", "api", "api", "vérifier l'affectation au module"),
        ("call", "api", "bd", "stagiaires du module + présences déjà saisies"),
        ("ret", "bd", "api", "liste"),
        ("ret", "api", "ihm", "liste des stagiaires et statuts"),
        ("ret", "ihm", "u", "afficher la feuille d'appel"),
        ("loop", "pour chaque stagiaire"),
        ("call", "u", "ihm", "choisir présent / absent / retard / excusé"),
        ("end",),
        ("call", "ihm", "api", "enregistrer la feuille de présence (séance, date, statuts)"),
        ("call", "api", "bd", "créer ou mettre à jour les présences (transaction)"),
        ("ret", "bd", "api", "ok"),
        ("ret", "api", "ihm", "liste mise à jour"),
        ("ret", "ihm", "u", "« présences enregistrées »"),
    ]
    return Seq(P3("Enseignant"), steps, gap=235).build()


@fig("seq_devoir")
def seq_devoir():
    parts = P3("Stagiaire") + [("fs", ":Stockage", "entity")]
    steps = [
        ("call", "u", "ihm", "ouvrir la liste des devoirs"),
        ("call", "ihm", "api", "GET /api/student/homeworks"),
        ("call", "api", "bd", "devoirs de sa spécialité et de son semestre"),
        ("ret", "bd", "api", "devoirs + remises"),
        ("ret", "api", "ihm", "liste des devoirs"),
        ("ret", "ihm", "u", "afficher les devoirs"),
        ("call", "u", "ihm", "rédiger la réponse et/ou joindre un fichier"),
        ("call", "ihm", "api", "POST /api/student/homeworks/{id}/submit"),
        ("self", "api", "api", "vérifier le contenu et que le devoir le concerne"),
        ("alt", "contenu vide ou devoir non concerné"),
        ("ret", "api", "ihm", "400 / 403 message d'erreur"),
        ("ret", "ihm", "u", "afficher l'erreur"),
        ("else", "valide"),
        ("opt", "fichier joint"),
        ("call", "api", "fs", "enregistrer le fichier"),
        ("ret", "fs", "api", "chemin du fichier"),
        ("end",),
        ("call", "api", "bd", "créer ou mettre à jour la remise (statut : soumis)"),
        ("ret", "bd", "api", "ok"),
        ("ret", "api", "ihm", "200 devoir soumis"),
        ("ret", "ihm", "u", "confirmation"),
        ("end",),
    ]
    return Seq(parts, steps, gap=205).build()


# =====================================================================
#  FLUX DU CONTEXTE (système existant)
# =====================================================================
@fig("flux_contexte")
def flux():
    s = Svg(1150, 720)
    cx, cy, erx, ery = 575, 360, 175, 92
    acts = {"stg": (150, 130, "Stagiaire"), "ens": (150, 590, "Enseignant"), "dir": (1000, 130, "Direction"),
            "mfep": (1000, 590, "Tutelle (MFEP / DFEP)")}
    # (numéro, acteur, sens) — sens "in" = acteur -> domaine
    flows = {"stg": [(1, "in"), (2, "out"), (3, "out"), (4, "in")],
             "ens": [(5, "out"), (6, "in"), (7, "in")],
             "dir": [(8, "out"), (9, "in")],
             "mfep": [(10, "out"), (11, "in")]}
    for k, (ax, ay, n) in acts.items():
        dx, dy = cx - ax, cy - ay
        L = (dx * dx + dy * dy) ** 0.5
        ux, uy = dx / L, dy / L          # direction acteur -> domaine
        px, py = -uy, ux                 # perpendiculaire
        m = len(flows[k])
        for i, (num, sens) in enumerate(flows[k]):
            off = (i - (m - 1) / 2) * 22
            bx, by = ax + px * off, ay + py * off          # point de la droite dans la boîte acteur
            tb = min((100 - (bx - ax) * (1 if ux > 0 else -1)) / abs(ux) if ux else 1e9,
                     (42 - (by - ay) * (1 if uy > 0 else -1)) / abs(uy) if uy else 1e9)
            sx, sy = bx + ux * tb, by + uy * tb            # sortie de la boîte
            # intersection avec l'ellipse (première racine positive)
            qx, qy = bx - cx, by - cy
            A = (ux / erx) ** 2 + (uy / ery) ** 2
            B = 2 * (qx * ux / erx ** 2 + qy * uy / ery ** 2)
            C = (qx / erx) ** 2 + (qy / ery) ** 2 - 1
            te = (-B - (B * B - 4 * A * C) ** 0.5) / (2 * A)
            ex, ey = bx + ux * te, by + uy * te
            p1, p2 = ((sx, sy), (ex, ey)) if sens == "in" else ((ex, ey), (sx, sy))
            s.line(*p1, *p2, "#333", 1.4, marker="ar")
            f = 0.3 + 0.4 * (i % 2)
            mx, my = sx + (ex - sx) * f, sy + (ey - sy) * f
            s.circle(mx, my, 12, fill="white", stroke="#333", sw=1.3)
            s.text(mx, my + 4.5, str(num), 12, "middle", "bold")
    s.ellipse(cx, cy, erx, ery, fill=FILL, stroke=STROKE, sw=1.6)
    s.mtext(cx, cy, ["Domaine d'étude :", "gestion pédagogique", "et scolarité de l'INSFP"], 14, 18, weight="bold")
    for k, (x, y, n) in acts.items():
        s.rect(x - 100, y - 42, 200, 84, fill="white", stroke="#333", sw=1.5)
        s.text(x, y + 5, n, 14, "middle", "bold")
    return s


# =====================================================================
#  GANTT — planification des sprints (d'après l'historique Git)
# =====================================================================
SPRINTS = [
    ("Sprint 1 — Initialisation, API et espace stagiaire", "2025-12-20", "2025-12-26", [
        ("Initialisation du dépôt, Laravel et migrations", "2025-12-20", "2025-12-22"),
        ("API REST de base et authentification", "2025-12-22", "2025-12-23"),
        ("Structure du frontend Vue.js / Tailwind", "2025-12-23", "2025-12-24"),
        ("Tableau de bord et pages du stagiaire", "2025-12-25", "2025-12-26")]),
    ("Sprint 2 — Panneau d'administration", "2025-12-27", "2026-01-09", [
        ("Gestion des enseignants et des spécialités", "2025-12-27", "2025-12-29"),
        ("Emplois du temps (première version)", "2026-01-05", "2026-01-07"),
        ("Sessions, documents et numéros d'inscription", "2026-01-07", "2026-01-09")]),
    ("Sprint 3 — Inscription en ligne et base de données", "2026-01-10", "2026-02-17", [
        ("Révision du schéma et des migrations", "2026-01-10", "2026-02-17"),
        ("Recherche du numéro et inscription en ligne", "2026-02-14", "2026-02-17")]),
    ("Sprint 4 — Emplois du temps par session", "2026-02-18", "2026-03-06", [
        ("Emplois du temps complets par session et spécialité", "2026-02-18", "2026-03-06")]),
    ("Sprint 5 — Espace enseignant et assistant intelligent", "2026-03-07", "2026-04-16", [
        ("Profil, emploi du temps, devoirs et examens (enseignant)", "2026-03-07", "2026-04-15"),
        ("Assistant intelligent (API Gemini)", "2026-04-15", "2026-04-16")]),
    ("Sprint 6 — Délibérations, passage de semestre, sécurité", "2026-04-17", "2026-07-23", [
        ("Statuts des sessions et passage de semestre", "2026-04-17", "2026-06-15"),
        ("Délibérations, révisions et types d'examens", "2026-06-01", "2026-07-15"),
        ("Sécurisation (cookie httpOnly, contrôles d'accès)", "2026-06-20", "2026-07-23")]),
    ("Sprint 7 — Tests, corrections et documentation", "2026-07-24", "2026-09-30", [
        ("Tests fonctionnels et corrections", "2026-07-24", "2026-08-31"),
        ("Rédaction du mémoire et préparation du déploiement", "2026-08-15", "2026-09-30")]),
]


def D(t):
    return dt.date.fromisoformat(t)


@fig("gantt_sprints")
def gantt():
    rows = []
    for name, a, b, tasks in SPRINTS:
        rows.append((name, a, b, True))
        rows += [(t, ta, tb, False) for t, ta, tb in tasks]
    start, end = D("2025-12-15"), D("2026-10-01")
    lw, cw_total, rh = 390, 900, 26
    W, H = lw + cw_total + 30, 70 + len(rows) * rh + 20
    s = Svg(W, H)
    days = (end - start).days
    px = cw_total / days
    # mois
    m = dt.date(2025, 12, 1)
    mois = "janv. févr. mars avr. mai juin juil. août sept. oct. nov. déc.".split()
    while m < end:
        nm = dt.date(m.year + (m.month == 12), m.month % 12 + 1, 1)
        x0 = lw + max(0, (m - start).days) * px
        x1 = lw + min(days, (nm - start).days) * px
        s.rect(x0, 20, x1 - x0, 30, fill="#f3f3f3", stroke="#bbb", sw=1)
        s.text((x0 + x1) / 2, 40, f"{mois[m.month-1]} {str(m.year)[2:]}", 11.5, "middle", "600", "#333")
        s.line(x0, 50, x0, H - 20, "#e2e2e2", 1)
        m = nm
    s.text(12, 40, "Sprints et tâches", 12.5, weight="bold")
    for i, (name, a, b, sprint) in enumerate(rows):
        y = 60 + i * rh
        if sprint:
            s.rect(6, y - 2, W - 12, rh, fill="#f6f2ea", stroke="none")
        s.text(12 if sprint else 28, y + 15, name, 11.5 if not sprint else 12, weight="bold" if sprint else "normal",
               fill="#222" if sprint else "#444")
        x0 = lw + (D(a) - start).days * px
        x1 = lw + ((D(b) - start).days + 1) * px
        if sprint:
            s.path(f"M{x0},{y+5} H{x1} V{y+17} l-5,-5 H{x0+5} l-5,5 z", fill="#D7971D", stroke="#D7971D", sw=1)
        else:
            s.rect(x0, y + 5, max(4, x1 - x0), 13, fill="#03909E", stroke="#02707B", sw=1, rx=3)
    return s


# =====================================================================
#  ARBORESCENCE
# =====================================================================
TREE = [
    ("Authentification", ["Connexion", "Inscription en ligne", "Compléter le profil", "Assistant (chatbot)"]),
    ("Espace administration", ["Tableau de bord", "Stagiaires", "Enseignants",
                               "Spécialités et modules", "Sessions (promotions)", "Délibérations",
                               "Passages (révisions)", "Numéros d'inscription", "Emplois du temps",
                               "Examens et notes", "Fichiers (documents)", "Profil"]),
    ("Espace enseignant", ["Tableau de bord", "Messages", "Documents", "Modules et stagiaires", "Cours (supports)",
                           "Devoirs et remises", "Emploi du temps", "Présences (appel)", "Examens et notes", "Profil"]),
    ("Espace stagiaire", ["Tableau de bord", "Messages", "Cours", "Documents", "Délibérations", "Devoirs",
                          "Emploi du temps", "Assiduité", "Examens et notes", "Profil"]),
]


@fig("arborescence")
def arbo():
    colw, W = 250, 250 * 4 + 40
    maxn = max(len(c) for _, c in TREE)
    H = 170 + maxn * 34 + 20
    s = Svg(W, H)
    rx = W / 2
    s.rect(rx - 110, 16, 220, 40, fill="#1A1A1A", stroke="#1A1A1A", rx=6)
    s.text(rx, 42, "Plateforme INSFP", 15, "middle", "bold", "white")
    s.line(rx, 56, rx, 76, "#333", 1.4)
    xs = [20 + i * colw + colw / 2 for i in range(4)]
    s.line(xs[0], 76, xs[-1], 76, "#333", 1.4)
    for i, (head, items) in enumerate(TREE):
        x = xs[i]
        s.line(x, 76, x, 92, "#333", 1.4)
        s.rect(x - 110, 92, 220, 38, fill=FILL, stroke=STROKE, sw=1.5, rx=6)
        s.text(x, 116, head, 13.5, "middle", "bold")
        lx = x - 95
        s.line(lx, 130, lx, 150 + (len(items) - 1) * 34 + 14, "#777", 1.2)
        for j, it in enumerate(items):
            y = 150 + j * 34
            s.line(lx, y + 14, lx + 14, y + 14, "#777", 1.2)
            s.rect(lx + 14, y, 190, 28, fill="white", stroke="#999", sw=1.1, rx=4)
            s.text(lx + 24, y + 18.5, it, 11.5, fill="#333")
    return s


# =====================================================================
#  ORGANIGRAMMES
# =====================================================================
def org(s, nodes, edges):
    for k, (x, y, w, t, dark) in nodes.items():
        lines = wrap(t, w - 16)
        h = 20 + len(lines) * 15
        s.rect(x - w / 2, y - h / 2, w, h, fill="#1A1A1A" if dark else FILL, stroke="#1A1A1A" if dark else STROKE, sw=1.4, rx=5)
        s.mtext(x, y, lines, 12.5, 15, weight="bold" if dark else "600", fill="white" if dark else "#222")
    for a, bs in edges.items():
        xa, ya = nodes[a][0], nodes[a][1] + 22
        ym = min(nodes[b][1] for b in bs) - 34
        s.line(xa, ya, xa, ym, "#333", 1.3)
        s.line(min(nodes[b][0] for b in bs), ym, max(nodes[b][0] for b in bs), ym, "#333", 1.3)
        for b in bs:
            s.line(nodes[b][0], ym, nodes[b][0], nodes[b][1] - 22, "#333", 1.3)


@fig("organigramme_insfp")
def organigramme():
    s = Svg(1500, 390)
    leaves = [("a1", "Gestion des inscriptions"), ("a2", "Documents et attestations"), ("a3", "Délibérations et relevés"),
              ("p1", "Corps enseignant / formateurs"), ("p2", "Coordination des spécialités"), ("p3", "Emplois du temps et examens"),
              ("s1", "Maintenance et infrastructure"), ("s2", "Plateforme numérique")]
    n = {}
    for i, (k, t) in enumerate(leaves):
        n[k] = (100 + i * 185, 300, 170, t, False)
    for k, kids, t in (("adm", ["a1", "a2", "a3"], "Service administration et scolarité"),
                       ("ped", ["p1", "p2", "p3"], "Service pédagogique"),
                       ("si", ["s1", "s2"], "Service informatique / SI")):
        x = sum(n[c][0] for c in kids) / len(kids)
        n[k] = (x, 160, 260, t, False)
    n["dir"] = (n["ped"][0], 45, 240, "Direction de l'institut", True)
    org(s, n, {"dir": ["adm", "ped", "si"], "adm": ["a1", "a2", "a3"], "ped": ["p1", "p2", "p3"], "si": ["s1", "s2"]})
    return s


@fig("organigramme_structure")
def organigramme_structure():
    s = Svg(1000, 380)
    n = {"chef": (500, 40, 280, "Service pédagogique et scolarité (structure d'accueil)", True),
         "sco": (200, 160, 230, "Bureau de la scolarité", False),
         "ped": (500, 160, 230, "Coordination pédagogique", False),
         "exa": (800, 160, 230, "Bureau des examens", False),
         "c1": (110, 300, 160, "Inscriptions et dossiers", False), "c2": (290, 300, 160, "Attestations et documents", False),
         "c3": (500, 300, 170, "Emplois du temps et affectations", False),
         "c4": (710, 300, 160, "Notes et procès-verbaux", False), "c5": (890, 300, 160, "Délibérations", False)}
    org(s, n, {"chef": ["sco", "ped", "exa"], "sco": ["c1", "c2"], "ped": ["c3"], "exa": ["c4", "c5"]})
    return s


if __name__ == "__main__":
    only = sys.argv[2:] or list(FIG)
    for name in only:
        render(FIG[name](), os.path.join(OUT, name + ".png"))
        print(name)
