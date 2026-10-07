"""Générateur de diagrammes d'activité UML (swimlanes) en SVG."""
import html, os, subprocess, sys

FONT = "Segoe UI, Arial, Helvetica, sans-serif"
FS, CW, LH = 13, 6.7, 16
ROWH = 78
MARGIN, TAGH, HEADH = 16, 30, 34
FILL, STROKE = "#E6F3F5", "#03909E"
LINE = "#333"
HALO = 'stroke="white" stroke-width="4" paint-order="stroke" stroke-linejoin="round"'


def wrap(text, maxw):
    out = []
    for para in text.split("\n"):
        cur = ""
        for w in para.split(" "):
            t = (cur + " " + w).strip()
            if len(t) * CW <= maxw or not cur:
                cur = t
            else:
                out.append(cur)
                cur = w
        out.append(cur)
    return out


class Node:
    def __init__(self, id, kind, lane, row, text="", dx=0.0, w=None):
        self.id, self.kind, self.lane, self.row = id, kind, lane, row
        self.text, self.dx, self.maxw = text, dx, w


class Diagram:
    def __init__(self, key, title, lanes, widths):
        self.key, self.title, self.lanes, self.widths = key, title, lanes, widths
        self.nodes, self.edges = {}, []

    # --- construction -------------------------------------------------------
    def n(self, id, kind, lane, row, text="", dx=0.0, w=None):
        self.nodes[id] = Node(id, kind, lane, row, text, dx, w)

    def e(self, a, b, label="", sp=None, dp=None, via=None):
        self.edges.append(dict(a=a, b=b, label=label, sp=sp, dp=dp, via=via))

    # --- géométrie ----------------------------------------------------------
    def lane_x(self, i):
        return MARGIN + sum(self.widths[:i])

    def X(self, lane, dx):
        return self.lane_x(lane) + self.widths[lane] / 2 + dx * self.widths[lane]

    def Y(self, row):
        return self.top + row * ROWH + ROWH / 2

    def layout(self):
        self.top = MARGIN + TAGH + HEADH + 8
        rows = max(n.row for n in self.nodes.values()) + 1
        self.W = sum(self.widths) + 2 * MARGIN
        self.H = self.top + rows * ROWH + MARGIN
        for n in self.nodes.values():
            n.cx, n.cy = self.X(n.lane, n.dx), self.Y(n.row)
        for n in self.nodes.values():
            if n.kind == "action":
                maxw = n.maxw or (self.widths[n.lane] - 44)
                n.lines = wrap(n.text, maxw - 22)
                n.w = max(96, max(len(l) for l in n.lines) * CW + 26)
                n.h = len(n.lines) * LH + 16
            elif n.kind in ("decision", "merge"):
                n.w, n.h = 30, 30
            elif n.kind == "start":
                n.w = n.h = 22
            elif n.kind in ("end", "flowfinal"):
                n.w = n.h = 26
            elif n.kind in ("fork", "join"):
                xs = [n.cx]
                for ed in self.edges:
                    if ed["a"] == n.id:
                        xs.append(self.nodes[ed["b"]].cx)
                    if ed["b"] == n.id:
                        xs.append(self.nodes[ed["a"]].cx)
                n.x1, n.x2 = min(xs) - 24, max(xs) + 24
                n.cx, n.w, n.h = (n.x1 + n.x2) / 2, n.x2 - n.x1, 7

    def port(self, n, p):
        if p == "t":
            return (n.cx, n.cy - n.h / 2)
        if p == "b":
            return (n.cx, n.cy + n.h / 2)
        if p == "l":
            return (n.cx - n.w / 2, n.cy)
        if p == "r":
            return (n.cx + n.w / 2, n.cy)
        if p == "bl":
            return (n.cx - n.w / 4, n.cy + n.h / 4)
        if p == "br":
            return (n.cx + n.w / 4, n.cy + n.h / 4)
        raise ValueError(p)

    def route(self, ed):
        S, T = self.nodes[ed["a"]], self.nodes[ed["b"]]
        sp, dp, via = ed["sp"], ed["dp"], ed["via"]
        if via:
            pts = [self.port(S, sp)]
            pts += [(self.X(l, dx), self.Y(r)) for (l, dx, r) in via]
            pts.append(self.port(T, dp))
            return pts
        if S.kind == "fork":
            return [(T.cx, S.cy + S.h / 2), self.port(T, dp or "t")]
        if T.kind in ("join", "fork"):
            a = self.port(S, sp or "b")
            return [a, (a[0], T.cy - T.h / 2)]
        if T.row == S.row:
            sp = sp or ("r" if T.cx > S.cx else "l")
            dp = dp or ("l" if sp == "r" else "r")
            return [self.port(S, sp), self.port(T, dp)]
        if T.row < S.row:
            raise ValueError(f"{self.key}: boucle {ed['a']}->{ed['b']} sans via")
        aligned = abs(T.cx - S.cx) < 1
        if sp is None:
            sp = "b"
            if S.kind == "decision" and not aligned:
                sp = "r" if T.cx > S.cx else "l"
        if dp is None:
            dp = "t"
            if T.kind == "merge" and not aligned and sp in ("b", "bl", "br"):
                dp = "l" if S.cx < T.cx else "r"
        a, b = self.port(S, sp), self.port(T, dp)
        if sp in ("l", "r"):
            return [a, (b[0], a[1]), b] if dp == "t" else [a, (a[0] + (b[0] - a[0]) / 2, a[1]), b]
        if dp in ("l", "r"):
            return [a, (a[0], b[1]), b]
        if abs(a[0] - b[0]) < 1:
            return [a, b]
        my = b[1] - 16
        return [a, (a[0], my), (b[0], my), b]

    # --- rendu --------------------------------------------------------------
    def svg(self):
        self.layout()
        o = []
        add = o.append
        add(f'<svg xmlns="http://www.w3.org/2000/svg" width="{self.W:.0f}" height="{self.H:.0f}" '
            f'viewBox="0 0 {self.W:.0f} {self.H:.0f}" font-family="{FONT}" font-size="{FS}">')
        add('<defs><marker id="ar" viewBox="0 0 10 10" refX="9.5" refY="5" markerWidth="9" '
            'markerHeight="9" orient="auto-start-reverse"><path d="M0,0 L10,5 L0,10 z" fill="#333"/></marker></defs>')
        add(f'<rect width="100%" height="100%" fill="white"/>')
        # cadre UML "act"
        fx, fy = MARGIN / 2, MARGIN / 2
        fw, fh = self.W - MARGIN, self.H - MARGIN
        add(f'<rect x="{fx}" y="{fy}" width="{fw}" height="{fh}" fill="none" stroke="#555" stroke-width="1.2"/>')
        tag = "act " + self.title
        tw = len(tag) * 7.4 + 24
        add(f'<path d="M{fx},{fy} h{tw} v{TAGH-10} l-10,10 H{fx} z" fill="#f6f6f6" stroke="#555" stroke-width="1.2"/>')
        add(f'<text x="{fx+10}" y="{fy+TAGH/2+5}" font-weight="600" font-size="14">{html.escape(tag)}</text>')
        # couloirs
        ly = MARGIN + TAGH
        for i, name in enumerate(self.lanes):
            x = self.lane_x(i)
            w = self.widths[i]
            add(f'<rect x="{x}" y="{ly}" width="{w}" height="{HEADH}" fill="#D5E9ED" stroke="#555"/>')
            add(f'<rect x="{x}" y="{ly+HEADH}" width="{w}" height="{self.H-MARGIN-ly-HEADH}" fill="none" stroke="#555"/>')
            add(f'<text x="{x+w/2}" y="{ly+HEADH/2+5}" text-anchor="middle" font-weight="600" font-size="14">'
                f'{html.escape(name)}</text>')
        # arcs
        for ed in self.edges:
            pts = self.route(ed)
            d = "M" + " L".join(f"{x:.1f},{y:.1f}" for x, y in pts)
            add(f'<path d="{d}" fill="none" stroke="{LINE}" stroke-width="1.3" marker-end="url(#ar)"/>')
            if ed["label"]:
                (x0, y0), (x1, y1) = pts[0], pts[1]
                lab = html.escape(ed["label"])
                if abs(y1 - y0) < 1:  # premier segment horizontal
                    anchor = "start" if x1 > x0 else "end"
                    lx = x0 + (8 if x1 > x0 else -8)
                    add(f'<text x="{lx:.1f}" y="{y0-6:.1f}" text-anchor="{anchor}" font-size="12" '
                        f'font-style="italic" fill="#222" {HALO}>{lab}</text>')
                else:
                    left = ed["sp"] == "bl"
                    add(f'<text x="{x0+(-7 if left else 7):.1f}" y="{y0+15:.1f}" text-anchor="{"end" if left else "start"}" '
                        f'font-size="12" font-style="italic" fill="#222" {HALO}>{lab}</text>')
        # nœuds
        for n in self.nodes.values():
            if n.kind == "action":
                x, y = n.cx - n.w / 2, n.cy - n.h / 2
                add(f'<rect x="{x:.1f}" y="{y:.1f}" width="{n.w:.1f}" height="{n.h:.1f}" rx="12" '
                    f'fill="{FILL}" stroke="{STROKE}" stroke-width="1.4"/>')
                ty = n.cy - (len(n.lines) - 1) * LH / 2 + 4.5
                for i, l in enumerate(n.lines):
                    add(f'<text x="{n.cx:.1f}" y="{ty+i*LH:.1f}" text-anchor="middle">{html.escape(l)}</text>')
            elif n.kind in ("decision", "merge"):
                c, r = (n.cx, n.cy), 15
                add(f'<path d="M{c[0]},{c[1]-r} L{c[0]+r},{c[1]} L{c[0]},{c[1]+r} L{c[0]-r},{c[1]} z" '
                    f'fill="white" stroke="{LINE}" stroke-width="1.4"/>')
            elif n.kind == "start":
                add(f'<circle cx="{n.cx}" cy="{n.cy}" r="11" fill="#111"/>')
            elif n.kind == "end":
                add(f'<circle cx="{n.cx}" cy="{n.cy}" r="13" fill="white" stroke="#111" stroke-width="1.6"/>')
                add(f'<circle cx="{n.cx}" cy="{n.cy}" r="8" fill="#111"/>')
            elif n.kind == "flowfinal":
                add(f'<circle cx="{n.cx}" cy="{n.cy}" r="12" fill="white" stroke="#111" stroke-width="1.6"/>')
                k = 8.4
                add(f'<path d="M{n.cx-k},{n.cy-k} L{n.cx+k},{n.cy+k} M{n.cx-k},{n.cy+k} L{n.cx+k},{n.cy-k}" '
                    f'stroke="#111" stroke-width="1.6"/>')
            elif n.kind in ("fork", "join"):
                add(f'<rect x="{n.x1:.1f}" y="{n.cy-n.h/2:.1f}" width="{n.w:.1f}" height="{n.h}" fill="#111"/>')
        add("</svg>")
        return "\n".join(o)


D = []

# ============ 1. Inscription d'un stagiaire ============
d = Diagram("inscription", "Inscription d'un stagiaire", ["Administration", "Stagiaire", "Système"], [290, 290, 470])
d.n("s", "start", 0, 0)
d.n("a1", "action", 0, 1, "Choisir la session et la spécialité")
d.n("a2", "action", 2, 2, "Générer un numéro d'inscription unique (séquence + session + année + type d'étude + codes wilaya et INSFP)", dx=-0.1, w=340)
d.n("a3", "action", 0, 3, "Remettre le numéro au futur stagiaire")
d.n("m1", "merge", 1, 4)
d.n("a4", "action", 1, 5, "Saisir le numéro d'inscription")
d.n("a5", "action", 2, 6, "Rechercher le numéro", dx=-0.2)
d.n("d1", "decision", 2, 7, dx=-0.2)
d.n("e1", "action", 2, 7, "Afficher « numéro introuvable ou déjà utilisé »", dx=0.25, w=180)
d.n("m2", "merge", 1, 8)
d.n("a6", "action", 1, 9, "Choisir le mode d'étude et saisir ses informations (nom, e-mail, téléphone, mot de passe)")
d.n("a7", "action", 2, 10, "Vérifier la cohérence session / spécialité / mode d'étude", dx=-0.2, w=250)
d.n("d2", "decision", 2, 11, dx=-0.2)
d.n("e2", "action", 2, 11, "Afficher l'erreur de validation", dx=0.25, w=180)
d.n("a8", "action", 2, 12, "Créer le compte (en attente) et le profil stagiaire, marquer le numéro comme utilisé", dx=-0.2, w=260)
d.n("a9", "action", 0, 13, "Consulter les inscriptions en attente")
d.n("d3", "decision", 0, 14)
d.n("a10", "action", 2, 15, "Activer le compte", dx=-0.23, w=180)
d.n("a11", "action", 2, 15, "Supprimer le compte et libérer le numéro", dx=0.25, w=180)
d.n("a12", "action", 1, 16, "Se connecter puis compléter son profil")
d.n("ff", "flowfinal", 2, 16, dx=0.25)
d.n("end", "end", 1, 17)
d.e("s", "a1"); d.e("a1", "a2"); d.e("a2", "a3"); d.e("a3", "m1"); d.e("m1", "a4"); d.e("a4", "a5"); d.e("a5", "d1")
d.e("d1", "e1", "[invalide]")
d.e("e1", "m1", sp="r", dp="r", via=[(2, 0.47, 7), (2, 0.47, 4)])
d.e("d1", "m2", "[valide]")
d.e("m2", "a6"); d.e("a6", "a7"); d.e("a7", "d2")
d.e("d2", "e2", "[incohérent]")
d.e("e2", "m2", sp="r", dp="r", via=[(2, 0.47, 11), (2, 0.47, 8)])
d.e("d2", "a8", "[cohérent]")
d.e("a8", "a9"); d.e("a9", "d3")
d.e("d3", "a10", "[approuvée]", sp="b")
d.e("d3", "a11", "[rejetée]", sp="r")
d.e("a10", "a12"); d.e("a11", "ff"); d.e("a12", "end")
D.append(d)

# ============ 2. Authentification ============
d = Diagram("authentification", "Authentification", ["Utilisateur", "Système"], [320, 470])
d.n("s", "start", 0, 0)
d.n("m1", "merge", 0, 1)
d.n("a1", "action", 0, 2, "Saisir l'identifiant (e-mail ou n° d'inscription) et le mot de passe")
d.n("d1", "decision", 1, 3)
d.n("a2", "action", 1, 4, "Rechercher l'utilisateur par e-mail", dx=-0.25, w=180)
d.n("a3", "action", 1, 4, "Rechercher le stagiaire par n° d'inscription", dx=0.24, w=180)
d.n("m2", "merge", 1, 5)
d.n("a4", "action", 1, 6, "Vérifier l'existence du compte et le mot de passe", dx=-0.2, w=250)
d.n("d2", "decision", 1, 7, dx=-0.2)
d.n("e1", "action", 1, 7, "Afficher « identifiants incorrects »", dx=0.24, w=180)
d.n("d3", "decision", 1, 8, dx=-0.2)
d.n("e2", "action", 1, 8, "Afficher « inscription en attente d'approbation »", dx=0.24, w=180)
d.n("ff", "flowfinal", 1, 9, dx=0.24)
d.n("a5", "action", 1, 9, "Ouvrir la session (cookie httpOnly)", dx=-0.2, w=250)
d.n("d4", "decision", 1, 10, dx=-0.2)
d.n("a6", "action", 0, 11, "Compléter le profil (date de naissance, adresse, téléphone)")
d.n("a7", "action", 1, 12, "Enregistrer le profil", dx=-0.2, w=220)
d.n("m3", "merge", 1, 13, dx=-0.2)
d.n("a8", "action", 1, 14, "Rediriger vers l'espace correspondant au rôle", dx=-0.2, w=250)
d.n("end", "end", 1, 15, dx=-0.2)
d.e("s", "m1"); d.e("m1", "a1"); d.e("a1", "d1")
d.e("d1", "a2", "[e-mail]"); d.e("d1", "a3", "[n° d'inscription]")
d.e("a2", "m2"); d.e("a3", "m2"); d.e("m2", "a4"); d.e("a4", "d2")
d.e("d2", "e1", "[incorrects]")
d.e("e1", "m1", sp="r", dp="r", via=[(1, 0.47, 7), (1, 0.47, 1)])
d.e("d2", "d3", "[corrects]")
d.e("d3", "e2", "[non approuvé]"); d.e("e2", "ff")
d.e("d3", "a5", "[approuvé]")
d.e("a5", "d4")
d.e("d4", "a6", "[stagiaire au profil incomplet]")
d.e("d4", "m3", "[sinon]", sp="r", dp="r", via=[(1, 0.12, 10), (1, 0.12, 13)])
d.e("a6", "a7"); d.e("a7", "m3"); d.e("m3", "a8"); d.e("a8", "end")
D.append(d)

# ============ 3. Sessions et passage de semestre ============
d = Diagram("session_passage_semestre", "Ouverture d'une session et passage de semestre", ["Administration", "Système"], [300, 660])
d.n("s", "start", 0, 0)
d.n("a1", "action", 0, 1, "Demander la création de la prochaine session")
d.n("a2", "action", 1, 2, "Calculer le prochain créneau libre (Février ou Septembre)", dx=-0.2, w=300)
d.n("d1", "decision", 1, 3, dx=-0.2)
d.n("e1", "action", 1, 3, "Refuser la création", dx=0.28, w=200)
d.n("ff1", "flowfinal", 1, 4, dx=0.28)
d.n("a3", "action", 1, 4, "Créer la session « en attente » (nom et dates calculés : début + 30 mois)", dx=-0.2, w=320)
d.n("a4", "action", 0, 5, "Ajouter les spécialités proposées et leur type d'étude")
d.n("a5", "action", 0, 6, "Demander l'activation de la session")
d.n("d2", "decision", 1, 7, dx=-0.2)
d.n("e2", "action", 1, 7, "Refuser l'activation", dx=0.28, w=200)
d.n("ff2", "flowfinal", 1, 8, dx=0.28)
d.n("a6", "action", 1, 8, "Archiver la session active et activer la nouvelle", dx=-0.2, w=320)
d.n("a7", "action", 1, 9, "Pour chaque stagiaire actif : lire la dernière délibération de son semestre courant", w=420)
d.n("d3", "decision", 1, 10)
d.n("n1", "action", 1, 11, "Laisser inchangé", dx=-0.34, w=170)
d.n("d4", "decision", 1, 11)
d.n("n2", "action", 1, 11, "Créer une révision « en attente »", dx=0.34, w=180)
d.n("n3", "action", 1, 12, "Déclarer diplômé", dx=-0.15, w=160)
d.n("n4", "action", 1, 12, "Passer au semestre suivant", dx=0.15, w=160)
d.n("m1", "merge", 1, 13)
d.n("a8", "action", 1, 14, "Retourner le bilan (avancés, diplômés, à réviser, ignorés)", w=420)
d.n("a9", "action", 0, 15, "Consulter les révisions en attente")
d.n("a10", "action", 0, 16, "Choisir une décision pour le stagiaire")
d.n("d5", "decision", 1, 17)
d.n("o1", "action", 1, 18, "Redoubler le semestre", dx=-0.375, w=150)
d.n("o2", "action", 1, 18, "Passer au semestre suivant ou diplômer", dx=-0.125, w=150)
d.n("o3", "action", 1, 18, "Exclure le stagiaire (motif)", dx=0.125, w=150)
d.n("o4", "action", 1, 18, "Laisser la révision en attente", dx=0.375, w=150)
d.n("m2", "merge", 1, 19)
d.n("end", "end", 1, 20)
d.e("s", "a1"); d.e("a1", "a2"); d.e("a2", "d1")
d.e("d1", "e1", "[créneau déjà créé]"); d.e("e1", "ff1")
d.e("d1", "a3", "[créneau libre]")
d.e("a3", "a4"); d.e("a4", "a5"); d.e("a5", "d2")
d.e("d2", "e2", "[mois de début non atteint]"); d.e("e2", "ff2")
d.e("d2", "a6", "[mois atteint]")
d.e("a6", "a7"); d.e("a7", "d3")
d.e("d3", "n1", "[aucune délibération]"); d.e("d3", "d4", "[admis]"); d.e("d3", "n2", "[ajourné]")
d.e("d4", "n3", "[dernier semestre]"); d.e("d4", "n4", "[sinon]")
d.e("n1", "m1", dp="l"); d.e("n3", "m1", dp="t"); d.e("n4", "m1", dp="t"); d.e("n2", "m1", dp="r")
d.e("m1", "a8"); d.e("a8", "a9"); d.e("a9", "a10"); d.e("a10", "d5")
d.e("d5", "o1", "[redoubler]", sp="l"); d.e("d5", "o2", "[passer]", sp="bl")
d.e("d5", "o3", "[exclure]", sp="br"); d.e("d5", "o4", "[reporter]", sp="r")
d.e("o1", "m2", dp="l"); d.e("o2", "m2", dp="t"); d.e("o3", "m2", dp="t"); d.e("o4", "m2", dp="r")
d.e("m2", "end")
D.append(d)

# ============ 4. Examens et notes ============
d = Diagram("examens_notes", "Gestion des examens et des notes", ["Enseignant", "Système", "Stagiaire"], [300, 450, 270])
d.n("s", "start", 0, 0)
d.n("a1", "action", 0, 1, "Créer un examen (titre, type : contrôle ou examen, module, date, durée, groupe)")
d.n("d1", "decision", 1, 2, dx=-0.2)
d.n("e1", "action", 1, 2, "Refuser l'opération", dx=0.27, w=170)
d.n("ff1", "flowfinal", 1, 3, dx=0.27)
d.n("a2", "action", 1, 3, "Enregistrer l'examen en brouillon (spécialité, semestre et année académique déduits)", dx=-0.2, w=260)
d.n("f", "fork", 1, 4, dx=-0.2)
d.n("b1", "action", 0, 5, "Soumettre l'examen (statut : soumis)")
d.n("b2", "action", 2, 5, "Recevoir la notification du nouvel examen")
d.n("ff2", "flowfinal", 2, 6)
d.n("d2", "decision", 0, 6)
d.n("c1", "action", 1, 6, "Enregistrer la modification (statut : modifié)", dx=-0.2, w=240)
d.n("m1", "merge", 0, 7)
d.n("a4", "action", 0, 8, "Ouvrir la saisie des notes")
d.n("a5", "action", 1, 9, "Afficher les stagiaires de la spécialité et du semestre de l'examen", dx=-0.2, w=260)
d.n("m2", "merge", 0, 10)
d.n("a6", "action", 0, 11, "Saisir les notes (sur 20)")
d.n("d3", "decision", 1, 12, dx=-0.2)
d.n("e2", "action", 1, 12, "Afficher l'erreur (note hors 0–20 ou stagiaire non concerné)", dx=0.27, w=175)
d.n("a7", "action", 1, 13, "Enregistrer ou mettre à jour les notes (transaction)", dx=-0.2, w=260)
d.n("a8", "action", 2, 14, "Consulter ses résultats d'examen")
d.n("end", "end", 2, 15)
d.e("s", "a1"); d.e("a1", "d1")
d.e("d1", "e1", "[non affecté]"); d.e("e1", "ff1")
d.e("d1", "a2", "[affecté]")
d.e("a2", "f"); d.e("f", "b1"); d.e("f", "b2"); d.e("b2", "ff2")
d.e("b1", "d2")
d.e("d2", "c1", "[modification]"); d.e("d2", "m1", "[sinon]"); d.e("c1", "m1")
d.e("m1", "a4"); d.e("a4", "a5"); d.e("a5", "m2", dp="t"); d.e("m2", "a6"); d.e("a6", "d3")
d.e("d3", "e2", "[invalide]")
d.e("e2", "m2", sp="r", dp="r", via=[(1, 0.47, 12), (1, 0.47, 10)])
d.e("d3", "a7", "[valide]")
d.e("a7", "a8"); d.e("a8", "end")
D.append(d)

# ============ 5. Délibération ============
d = Diagram("deliberation", "Délibération semestrielle", ["Administration", "Système", "Stagiaire"], [300, 450, 260])
d.n("s", "start", 0, 0)
d.n("a1", "action", 0, 1, "Choisir la session, la spécialité et éventuellement le semestre")
d.n("d1", "decision", 1, 2, dx=-0.2)
d.n("a2", "action", 1, 2, "Prendre le semestre courant des stagiaires", dx=0.26, w=180)
d.n("m1", "merge", 1, 3, dx=-0.2)
d.n("a3", "action", 1, 4, "Charger les stagiaires et leurs notes du semestre", dx=-0.2, w=260)
d.n("a4", "action", 1, 5, "Calculer la moyenne de chaque module", dx=-0.2, w=260)
d.n("a5", "action", 1, 6, "Calculer la moyenne générale pondérée par les coefficients", dx=-0.2, w=260)
d.n("a6", "action", 1, 7, "Proposer le résultat : admis si moyenne ≥ 10, sinon ajourné", dx=-0.2, w=260)
d.n("m2", "merge", 0, 8)
d.n("a7", "action", 0, 9, "Vérifier ou ajuster la moyenne, le résultat et les observations")
d.n("a8", "action", 0, 10, "Valider la délibération (date)")
d.n("d2", "decision", 1, 11, dx=-0.2)
d.n("e1", "action", 1, 11, "Afficher les erreurs de saisie", dx=0.26, w=180)
d.n("a9", "action", 1, 12, "Enregistrer ou mettre à jour la délibération", dx=-0.2, w=260)
d.n("a10", "action", 2, 13, "Consulter ses résultats de délibération")
d.n("end", "end", 2, 14)
d.e("s", "a1"); d.e("a1", "d1")
d.e("d1", "a2", "[non précisé]"); d.e("d1", "m1", "[précisé]"); d.e("a2", "m1")
d.e("m1", "a3"); d.e("a3", "a4"); d.e("a4", "a5"); d.e("a5", "a6"); d.e("a6", "m2", dp="t")
d.e("m2", "a7"); d.e("a7", "a8"); d.e("a8", "d2")
d.e("d2", "e1", "[invalides]")
d.e("e1", "m2", sp="r", dp="r", via=[(1, 0.47, 11), (1, 0.47, 8)])
d.e("d2", "a9", "[valides]")
d.e("a9", "a10"); d.e("a10", "end")
D.append(d)

# ============ 6. Emploi du temps ============
d = Diagram("emploi_du_temps", "Élaboration de l'emploi du temps", ["Administration", "Système"], [330, 460])
d.n("s", "start", 0, 0)
d.n("a1", "action", 0, 1, "Choisir la session, la spécialité, le semestre, le mode d'étude et le groupe")
d.n("m1", "merge", 0, 2)
d.n("a2", "action", 0, 3, "Saisir une séance (module, enseignant, jour, heures de début et de fin, salle)")
d.n("a3", "action", 1, 4, "Déduire l'année académique de la session", dx=-0.2, w=260)
d.n("a4", "action", 1, 5, "Vérifier les conflits d'horaire de l'enseignant et du groupe", dx=-0.2, w=260)
d.n("d1", "decision", 1, 6, dx=-0.2)
d.n("e1", "action", 1, 6, "Afficher le conflit détecté", dx=0.26, w=180)
d.n("a5", "action", 1, 7, "Enregistrer la séance", dx=-0.2, w=260)
d.n("d2", "decision", 0, 8)
d.n("a6", "action", 0, 9, "Finaliser l'emploi du temps")
d.n("a7", "action", 1, 10, "Marquer l'emploi du temps comme publié", dx=-0.2, w=260)
d.n("end", "end", 1, 11, dx=-0.2)
d.e("s", "a1"); d.e("a1", "m1"); d.e("m1", "a2"); d.e("a2", "a3"); d.e("a3", "a4"); d.e("a4", "d1")
d.e("d1", "e1", "[conflit]")
d.e("e1", "m1", sp="r", dp="r", via=[(1, 0.47, 6), (1, 0.47, 2)])
d.e("d1", "a5", "[aucun conflit]")
d.e("a5", "d2")
d.e("d2", "m1", "[autre séance]", sp="l", dp="l", via=[(0, -0.45, 8), (0, -0.45, 2)])
d.e("d2", "a6", "[emploi du temps complet]")
d.e("a6", "a7"); d.e("a7", "end")
D.append(d)

# ============ 7. Présence ============
d = Diagram("presence", "Gestion de la présence", ["Enseignant", "Système"], [320, 460])
d.n("s", "start", 0, 0)
d.n("a1", "action", 0, 1, "Consulter ses séances et choisir une séance et une date")
d.n("d1", "decision", 1, 2, dx=-0.2)
d.n("e1", "action", 1, 2, "Refuser l'accès", dx=0.26, w=170)
d.n("ff", "flowfinal", 1, 3, dx=0.26)
d.n("a2", "action", 1, 3, "Afficher les stagiaires du module avec leur statut déjà saisi", dx=-0.2, w=260)
d.n("a3", "action", 0, 4, "Marquer chaque stagiaire : présent, absent, en retard ou excusé (+ remarque)")
d.n("a4", "action", 0, 5, "Valider la feuille de présence")
d.n("a5", "action", 1, 6, "Créer ou mettre à jour les présences (transaction)", dx=-0.2, w=260)
d.n("a6", "action", 1, 7, "Renvoyer la liste mise à jour", dx=-0.2, w=260)
d.n("end", "end", 1, 8, dx=-0.2)
d.e("s", "a1"); d.e("a1", "d1")
d.e("d1", "e1", "[non affecté]"); d.e("e1", "ff")
d.e("d1", "a2", "[affecté]")
d.e("a2", "a3"); d.e("a3", "a4"); d.e("a4", "a5"); d.e("a5", "a6"); d.e("a6", "end")
D.append(d)

# ============ 8. Devoirs ============
d = Diagram("devoirs", "Gestion des devoirs", ["Enseignant", "Système", "Stagiaire"], [300, 440, 420])
d.n("s", "start", 0, 0)
d.n("a1", "action", 0, 1, "Créer un devoir (module, titre, description, date limite, type de remise, fichier joint)")
d.n("d1", "decision", 1, 2, dx=-0.2)
d.n("e1", "action", 1, 2, "Refuser la création", dx=0.26, w=170)
d.n("ff1", "flowfinal", 1, 3, dx=0.26)
d.n("a2", "action", 1, 3, "Enregistrer le devoir", dx=-0.2, w=250)
d.n("a3", "action", 2, 4, "Consulter les devoirs de sa spécialité et de son semestre", dx=-0.2, w=230)
d.n("d2", "decision", 2, 5, dx=-0.2)
d.n("n1", "action", 2, 5, "Remettre le travail en classe", dx=0.28, w=150)
d.n("ff2", "flowfinal", 2, 6, dx=0.28)
d.n("a4", "action", 2, 6, "Rédiger une réponse et/ou joindre un fichier (10 Mo max)", dx=-0.2, w=230)
d.n("d3", "decision", 1, 7, dx=-0.2)
d.n("e2", "action", 1, 7, "Refuser la remise", dx=0.26, w=170)
d.n("ff3", "flowfinal", 1, 8, dx=0.26)
d.n("a5", "action", 1, 8, "Créer ou mettre à jour la remise (statut : soumis)", dx=-0.2, w=250)
d.n("a6", "action", 0, 9, "Consulter les remises du devoir")
d.n("a7", "action", 0, 10, "Attribuer une note (0 à 20) et un commentaire")
d.n("a8", "action", 1, 11, "Enregistrer la note (statut : noté)", dx=-0.2, w=250)
d.n("a9", "action", 2, 12, "Consulter sa note et le commentaire", dx=-0.2, w=230)
d.n("end", "end", 2, 13, dx=-0.2)
d.e("s", "a1"); d.e("a1", "d1")
d.e("d1", "e1", "[non affecté]"); d.e("e1", "ff1")
d.e("d1", "a2", "[affecté]")
d.e("a2", "a3"); d.e("a3", "d2")
d.e("d2", "n1", "[en présentiel]"); d.e("n1", "ff2")
d.e("d2", "a4", "[en ligne]")
d.e("a4", "d3")
d.e("d3", "e2", "[invalide]"); d.e("e2", "ff3")
d.e("d3", "a5", "[valide]")
d.e("a5", "a6"); d.e("a6", "a7"); d.e("a7", "a8"); d.e("a8", "a9"); d.e("a9", "end")
D.append(d)


if __name__ == "__main__":
    out, chrome = sys.argv[1], sys.argv[2]
    os.makedirs(out, exist_ok=True)
    for i, d in enumerate(D, 1):
        name = f"activite_{i}_{d.key}"
        svg = d.svg()
        svg_path = os.path.join(out, name + ".svg")
        open(svg_path, "w", encoding="utf-8").write(svg)
        page = os.path.join(out, name + ".html")
        open(page, "w", encoding="utf-8").write(
            f'<html><body style="margin:0;background:white">{svg}</body></html>')
        subprocess.run([chrome, "--headless", "--no-sandbox", "--hide-scrollbars",
                        "--force-device-scale-factor=2", f"--window-size={int(d.W)},{int(d.H)}",
                        f"--screenshot={os.path.join(out, name + '.png')}",
                        "file:///" + os.path.abspath(page).replace("\\", "/")],
                       check=True, capture_output=True)
        os.remove(page)
        print(name, int(d.W), int(d.H))
