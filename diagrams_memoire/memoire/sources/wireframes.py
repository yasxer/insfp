"""Wireframes (basse fidélité) de la plateforme INSFP, fidèles à la structure réelle de l'application."""
import sys, os
from svglib import Svg, render, wrap

OUT = sys.argv[1] if len(sys.argv) > 1 else "out"
os.makedirs(OUT, exist_ok=True)

INK, MID, LIGHT, FILL, DARK = "#555", "#8a8a8a", "#c9c9c9", "#f1f1f1", "#4a4a4a"
PIN = "#146ef5"


class WF(Svg):
    def pin(self, x, y, n):
        self.circle(x, y, 11, fill=PIN, stroke="white", sw=2)
        self.text(x, y + 4.5, str(n), 12, "middle", "bold", "white")

    def bar(self, x, y, w, h=8, fill=LIGHT):
        self.rect(x, y, w, h, fill=fill, stroke="none", rx=3)

    def box(self, x, y, w, h, fill="white", rx=6, dash=None, stroke=INK):
        self.rect(x, y, w, h, fill=fill, stroke=stroke, sw=1.3, rx=rx, dash=dash)

    def img(self, x, y, w, h):
        self.box(x, y, w, h, FILL, rx=4)
        self.line(x, y, x + w, y + h, MID, 1)
        self.line(x + w, y, x, y + h, MID, 1)

    def icon(self, x, y, s=16):
        self.rect(x, y, s, s, fill=LIGHT, stroke=MID, sw=1, rx=3)

    def button(self, x, y, w, label, primary=False, h=34):
        if primary:
            self.rect(x, y, w, h, fill=DARK, stroke=DARK, rx=5)
            self.text(x + w / 2, y + h / 2 + 4.5, label, 13, "middle", "600", "white")
        else:
            self.rect(x, y, w, h, fill="white", stroke=INK, sw=1.3, rx=5)
            self.text(x + w / 2, y + h / 2 + 4.5, label, 13, "middle", "600", "#333")

    def field(self, x, y, w, label, ph="", select=False, h=34):
        if label:
            self.text(x, y - 7, label.upper(), 10.5, weight="600", fill="#444")
        self.rect(x, y, w, h, fill="white", stroke=INK, sw=1.2, rx=4)
        if ph:
            self.text(x + 10, y + h / 2 + 4.5, ph, 12.5, fill="#999")
        if select:
            self.text(x + w - 16, y + h / 2 + 4.5, "▾", 13, "middle", fill="#555")

    def badge(self, x, y, label, w=None):
        w = w or len(label) * 6.6 + 16
        self.rect(x, y, w, 20, fill="#e6e6e6", stroke="#9a9a9a", sw=1, rx=10)
        self.text(x + w / 2, y + 14, label, 11, "middle", "600", "#333")
        return w

    def title(self, x, y, t, sub=None):
        self.text(x, y, t, 21, weight="bold", fill="#111")
        if sub:
            self.text(x, y + 22, sub, 12.5, fill="#777")

    def table(self, x, y, cols, rows, rowh=40, badge_col=None, badge_vals=None, offsets=None):
        offsets = offsets or {}
        w = sum(c[1] for c in cols)
        self.box(x, y, w, 34 + rows * rowh, rx=6)
        self.rect(x, y, w, 34, fill=FILL, stroke=INK, sw=1.3, rx=6)
        cx = x
        for name, cw in cols:
            self.text(cx + 12, y + 22, name.upper(), 10.5, weight="600", fill="#555")
            cx += cw
        for r in range(rows):
            ry = y + 34 + r * rowh
            if r:
                self.line(x, ry, x + w, ry, LIGHT, 1)
            cx = x
            for i, (name, cw) in enumerate(cols):
                if badge_col is not None and i == badge_col:
                    self.badge(cx + 12, ry + rowh / 2 - 10, badge_vals[r % len(badge_vals)])
                elif name:
                    o = offsets.get(i, 0)
                    self.bar(cx + 12 + o, ry + rowh / 2 - 4, max(30, (cw - 34 - o) * (0.55 + 0.35 * ((r * 7 + i * 3) % 5) / 4)))
                cx += cw
        return w

    # ---- coques d'écran -------------------------------------------------
    def browser(self, url):
        self.rect(10, 10, self.w - 20, self.h - 20, fill="white", stroke="#444", sw=1.6, rx=10)
        self.rect(10, 10, self.w - 20, 38, fill="#e9e9e9", stroke="#444", sw=1.6, rx=10)
        self.rect(11, 36, self.w - 22, 12, fill="#e9e9e9", stroke="none")
        for i, c in enumerate(("#bbb", "#bbb", "#bbb")):
            self.circle(32 + i * 18, 29, 5.5, fill=c, stroke="#888", sw=1)
        self.rect(100, 18, self.w - 220, 22, fill="white", stroke="#aaa", sw=1, rx=11)
        self.text(116, 33, url, 12, fill="#666")
        self.line(10, 48, self.w - 10, 48, "#444", 1.6)

    def shell(self, url, nav, active, user, role, pins=True):
        """Mise en page commune : barre latérale + barre supérieure."""
        self.browser(url)
        sx, sy, sw_, sh = 11, 49, 200, self.h - 60
        self.rect(sx, sy, sw_, sh, fill="#fafafa", stroke="none")
        self.line(sx + sw_, sy, sx + sw_, self.h - 11, INK, 1.2)
        self.text(sx + 18, sy + 34, "INSFP", 18, weight="bold", fill="#111")
        self.line(sx, sy + 56, sx + sw_, sy + 56, LIGHT, 1)
        y = sy + 72
        for item in nav:
            if item == active:
                self.rect(sx + 8, y - 4, sw_ - 16, 30, fill=DARK, stroke="none", rx=6)
                self.rect(sx + 18, y + 3, 15, 15, fill="#888", stroke="white", sw=1, rx=3)
                self.text(sx + 42, y + 15, item, 12.5, weight="600", fill="white")
            else:
                self.icon(sx + 18, y + 3, 15)
                self.text(sx + 42, y + 15, item, 12.5, fill="#333")
            y += 34
        by = self.h - 100
        self.line(sx, by, sx + sw_, by, LIGHT, 1)
        self.circle(sx + 30, by + 26, 13, fill=LIGHT, stroke=MID)
        self.text(sx + 52, by + 23, user, 12, weight="600", fill="#222")
        self.text(sx + 52, by + 38, role, 10.5, fill="#777")
        self.icon(sx + 18, by + 55, 14)
        self.text(sx + 42, by + 67, "Logout", 12.5, fill="#333")
        # barre supérieure
        tx = sx + sw_
        self.line(tx, 104, self.w - 11, 104, INK, 1.2)
        self.circle(self.w - 170, 76, 10, fill=LIGHT, stroke=MID)
        self.line(self.w - 148, 60, self.w - 148, 92, LIGHT, 1)
        self.text(self.w - 80, 72, "Utilisateur", 12, "end", "600", "#222")
        self.text(self.w - 80, 87, "Rôle", 10.5, "end", fill="#777")
        self.circle(self.w - 52, 76, 16, fill=LIGHT, stroke=MID)
        if pins:
            self.pin(sx + sw_ - 16, sy + 30, 1)
            self.pin(self.w - 200, 76, 2)
        return tx + 30, 140  # origine du contenu


FIGS = []


def fig(fn):
    FIGS.append(fn)
    return fn


# ============ Web : authentification ============
@fig
def wf_login():
    s = WF(900, 640)
    s.browser("localhost:5173/login")
    cx = 450
    s.box(cx - 190, 90, 380, 500, rx=10)
    s.circle(cx, 142, 26, fill=LIGHT, stroke=MID)
    s.text(cx, 200, "Connexion", 20, "middle", "bold", "#111")
    s.text(cx, 222, "Bienvenue sur le portail INSFP", 12.5, "middle", fill="#777")
    s.field(cx - 160, 270, 320, "E-mail ou numéro d'inscription", "exemple@insfp.dz ou 0001125P1647")
    s.field(cx - 160, 345, 320, "Mot de passe", "••••••••")
    s.rect(cx - 160, 398, 14, 14, fill="white", stroke=INK, rx=2)
    s.text(cx - 138, 410, "SE SOUVENIR DE MOI", 10.5, weight="600", fill="#444")
    s.text(cx + 160, 410, "Mot de passe oublié ?", 11.5, "end", fill="#555")
    s.button(cx - 160, 432, 320, "Se connecter", True, 38)
    s.text(cx, 505, "Pas encore de compte ?  Créer un compte", 12, "middle", fill="#555")
    s.line(cx + 4, 509, cx + 104, 509, "#777", 1)
    s.pin(cx - 175, 287, 1); s.pin(cx - 175, 362, 2); s.pin(cx - 175, 451, 3); s.pin(cx + 118, 505, 4)
    return s


@fig
def wf_inscription():
    s = WF(900, 720)
    s.browser("localhost:5173/register")
    cx = 450
    s.box(cx - 250, 80, 500, 610, rx=10)
    s.text(cx, 122, "Créer un compte", 20, "middle", "bold", "#111")
    s.text(cx, 144, "Rejoindre le portail stagiaire de l'INSFP", 12.5, "middle", fill="#777")
    x0, x1, w2 = cx - 220, cx + 10, 210
    s.field(x0, 185, 440, "Numéro d'inscription", "0001125P1647")
    s.field(x0, 255, w2, "Session", "— (rempli automatiquement)")
    s.field(x1, 255, w2, "Spécialité", "— (rempli automatiquement)")
    s.field(x0, 325, w2, "Prénom", "")
    s.field(x1, 325, w2, "Nom", "")
    s.field(x0, 395, 440, "Mode d'étude", "Choisir parmi les modes proposés", select=True)
    s.field(x0, 465, w2, "E-mail", "stagiaire@insfp.dz")
    s.field(x1, 465, w2, "Téléphone", "0612345678")
    s.field(x0, 535, w2, "Mot de passe", "••••••••")
    s.field(x1, 535, w2, "Confirmer", "••••••••")
    s.button(x0, 600, 440, "Créer le compte", True, 38)
    s.text(cx, 665, "Déjà inscrit ?  Se connecter", 12, "middle", fill="#555")
    s.pin(x0 - 16, 202, 1); s.pin(x0 - 16, 272, 2); s.pin(x0 - 16, 412, 3); s.pin(x0 - 16, 619, 4)
    return s


@fig
def wf_profil():
    s = WF(900, 600)
    s.browser("localhost:5173/complete-profile")
    cx = 450
    s.box(cx - 210, 80, 420, 490, rx=10)
    s.circle(cx, 130, 24, fill=LIGHT, stroke=MID)
    s.text(cx, 186, "Compléter votre profil", 20, "middle", "bold", "#111")
    s.text(cx, 208, "Quelques informations sont nécessaires avant d'accéder à votre espace", 12, "middle", fill="#777")
    s.field(cx - 180, 255, 360, "Date de naissance *", "jj/mm/aaaa")
    s.rect(cx - 180, 325, 360, 70, fill="white", stroke=INK, sw=1.2, rx=4)
    s.text(cx - 180, 318, "ADRESSE *", 10.5, weight="600", fill="#444")
    s.text(cx - 170, 345, "Rue, ville, wilaya…", 12.5, fill="#999")
    s.text(cx - 180, 410, "10 caractères minimum", 10.5, fill="#888")
    s.field(cx - 180, 445, 360, "Téléphone (optionnel)", "")
    s.button(cx - 180, 500, 360, "Enregistrer et continuer", True, 38)
    s.pin(cx - 195, 272, 1); s.pin(cx - 195, 360, 2); s.pin(cx - 195, 519, 3)
    return s


# ============ Web : administration ============
ADMIN_NAV = ["Dashboard", "Students", "Teachers", "Specialties", "Sessions", "Deliberations", "Passages",
             "Registration Gen", "Schedule", "Examens", "Files", "Profile"]


@fig
def wf_admin_dashboard():
    s = WF(1200, 800)
    x, y = s.shell("localhost:5173/admin/dashboard", ADMIN_NAV, "Dashboard", "admin@insfp.dz", "Administration")
    W = s.w - x - 40
    s.title(x, y + 10, "Tableau de bord", "Bienvenue, Administration")
    s.icon(x + W - 110, y - 2, 14); s.bar(x + W - 90, y + 1, 90)
    cw = (W - 3 * 16) / 4
    for i, lab in enumerate(["Stagiaires", "Enseignants", "Modules", "Spécialités"]):
        cx = x + i * (cw + 16)
        s.box(cx, y + 50, cw, 104)
        s.text(cx + 16, y + 76, lab, 12, fill="#666")
        s.text(cx + 16, y + 112, "000", 28, weight="bold", fill="#222")
        s.rect(cx + cw - 50, y + 64, 34, 34, fill=FILL, stroke=MID, rx=6)
        s.badge(cx + 16, y + 124, "↗ 0 %", 52); s.bar(cx + 76, y + 131, 70)
    hw = (W - 16) / 2
    for i, lab in enumerate(["Stagiaires par spécialité", "Enseignants par spécialité"]):
        cx = x + i * (hw + 16)
        s.box(cx, y + 172, hw, 260)
        s.text(cx + 16, y + 198, lab, 14, weight="bold", fill="#222")
        s.line(cx + 16, y + 212, cx + hw - 16, y + 212, LIGHT, 1)
        if i == 0:
            for k, bw in enumerate([0.85, 0.5, 0.45, 0.5]):
                s.bar(cx + 30, y + 236 + k * 44, 70, 8)
                s.rect(cx + 120, y + 228 + k * 44, (hw - 160) * bw, 24, fill=LIGHT, stroke=MID, sw=1)
        else:
            for k, bh in enumerate([0.8, 0.35, 0.55, 0.25, 0.65]):
                bx = cx + 50 + k * (hw - 90) / 5
                s.rect(bx, y + 410 - 170 * bh, 36, 170 * bh, fill=LIGHT, stroke=MID, sw=1)
            s.line(cx + 36, y + 226, cx + 36, y + 410, MID, 1); s.line(cx + 36, y + 410, cx + hw - 20, y + 410, MID, 1)
    s.box(x, y + 450, W, 190)
    s.text(x + 16, y + 476, "Stagiaires (aperçu)", 14, weight="bold", fill="#222")
    s.table(x + 16, y + 490, [("Stagiaire", 260), ("N° d'inscription", 200), ("Spécialité", 280), ("Année", W - 32 - 740)], 3, 36)
    s.pin(x - 12, y + 102, 3); s.pin(x - 12, y + 300, 4); s.pin(x - 12, y + 560, 5)
    return s


@fig
def wf_admin_stagiaires():
    s = WF(1200, 800)
    x, y = s.shell("localhost:5173/admin/students", ADMIN_NAV, "Students", "admin@insfp.dz", "Administration")
    W = s.w - x - 40
    s.title(x, y + 14, "Gestion des stagiaires")
    s.button(x + W - 290, y - 6, 120, "↻ Actualiser")
    s.button(x + W - 160, y - 6, 160, "+ Ajouter un stagiaire", True)
    tabs = ["Stagiaires actifs", "Diplômés", "Inscriptions en attente"]
    tx = x
    for i, t in enumerate(tabs):
        s.text(tx, y + 62, t, 13, weight="600" if i == 0 else "normal", fill="#111" if i == 0 else "#777")
        if i == 0:
            s.rect(tx, y + 70, len(t) * 7, 3, fill=DARK, stroke="none")
        tx += len(t) * 7 + 40
    s.line(x, y + 73, x + W, y + 73, LIGHT, 1)
    s.box(x, y + 90, W, 80)
    s.field(x + 16, y + 124, 280, "Recherche", "Nom, matricule ou e-mail…")
    s.field(x + 312, y + 124, 190, "Spécialité", "Toutes", select=True)
    s.field(x + 518, y + 124, 125, "Semestre", "Tous", select=True)
    s.field(x + 659, y + 124, 125, "Groupe", "Tous", select=True)
    s.button(x + W - 90, y + 124, 74, "Effacer")
    cols = [("", 40), ("Nom", 260), ("Matricule", 140), ("Spécialité", 200), ("Sem. / Groupe", 110), ("Statut", 100), ("", W - 850)]
    tw = s.table(x, y + 190, cols, 8, 44, badge_col=5, badge_vals=["Inscrit"], offsets={1: 30})
    for r in range(8):
        ry = y + 190 + 34 + r * 44
        s.rect(x + 14, ry + 15, 14, 14, fill="white", stroke=INK, rx=2)
        s.circle(x + 66, ry + 22, 13, fill=LIGHT, stroke=MID)
        s.rect(x + tw - 46, ry + 15, 18, 14, fill=LIGHT, stroke=MID, rx=2)
    s.pin(x - 12, y + 62, 3); s.pin(x - 12, y + 140, 4); s.pin(x - 12, y + 300, 5); s.pin(x + W - 170, y - 18, 6)
    return s


@fig
def wf_admin_sessions():
    s = WF(1200, 800)
    x, y = s.shell("localhost:5173/admin/sessions", ADMIN_NAV, "Sessions", "admin@insfp.dz", "Administration")
    W = s.w - x - 40
    s.title(x, y + 10, "Sessions (promotions)", "Gérer les sessions de formation et leurs spécialités")
    s.button(x + W - 330, y - 6, 150, "Voir les archives")
    s.button(x + W - 166, y - 6, 166, "+ Nouvelle session", True)
    for k, (name, st) in enumerate([("Session Février 2027", "en attente"), ("Session Septembre 2026", "en cours")]):
        by = y + 60 + k * 270
        s.box(x, by, W, 250)
        s.rect(x + 16, by + 16, 40, 40, fill=FILL, stroke=MID, rx=6)
        s.text(x + 70, by + 32, name, 15, weight="bold", fill="#111")
        bw = s.badge(x + 70, by + 40, st)
        s.bar(x + 80 + bw, by + 46, 150); s.bar(x + 250 + bw, by + 46, 80)
        for j in range(4):
            s.icon(x + W - 130 + j * 28, by + 24, 16)
        cw = (W - 64) / 3
        for j, t in enumerate(["Présentiel", "Apprentissage", "Cours du soir"]):
            cx = x + 16 + j * (cw + 16)
            s.rect(cx, by + 76, cw, 156, fill=FILL, stroke=MID, sw=1, rx=6)
            s.icon(cx + 14, by + 92, 16)
            s.text(cx + 38, by + 105, t, 15, fill="#222")
            for r in range(2 if j != 1 else 0):
                s.rect(cx + 12, by + 124 + r * 38, cw - 24, 30, fill="white", stroke=LIGHT, sw=1, rx=4)
                s.bar(cx + 22, by + 135 + r * 38, cw * 0.5); s.bar(cx + cw - 50, by + 135 + r * 38, 26)
            if j == 1:
                s.text(cx + 14, by + 138, "Aucune spécialité", 12, fill="#999", italic=True)
    s.pin(x - 12, y + 92, 3); s.pin(x - 12, y + 200, 4); s.pin(x + W - 140, y + 84, 5); s.pin(x + W - 176, y - 18, 6)
    return s


@fig
def wf_admin_edt():
    s = WF(1200, 820)
    x, y = s.shell("localhost:5173/admin/schedule", ADMIN_NAV, "Schedule", "admin@insfp.dz", "Administration")
    W = s.w - x - 40
    s.title(x, y + 10, "Emploi du temps", "Développement Web et Mobile · S1 · G1 · Session Septembre 2026 · Formation initiale")
    s.button(x + W - 310, y - 6, 140, "+ Ajouter séance", True)
    s.button(x + W - 160, y - 6, 160, "Finaliser (publier)")
    days = ["Samedi", "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi"]
    slots = ["08:00", "09:30", "11:00", "13:00", "14:30", "16:00"]
    gx, gy, tw = x, y + 56, 70
    dw = (W - tw) / 6
    s.box(gx, gy, W, 36 + len(slots) * 80, rx=6)
    s.rect(gx, gy, W, 36, fill=FILL, stroke=INK, sw=1.3, rx=6)
    s.text(gx + 14, gy + 23, "HEURE", 10.5, weight="600", fill="#555")
    for i, d in enumerate(days):
        s.text(gx + tw + i * dw + dw / 2, gy + 23, d.upper(), 10.5, "middle", "600", "#555")
        s.line(gx + tw + i * dw, gy, gx + tw + i * dw, gy + 36 + len(slots) * 80, LIGHT, 1)
    filled = {(0, 0), (0, 2), (1, 1), (2, 0), (2, 3), (3, 2), (4, 1), (4, 4), (5, 0)}
    for r, t in enumerate(slots):
        ry = gy + 36 + r * 80
        s.line(gx, ry, gx + W, ry, LIGHT, 1)
        s.text(gx + 14, ry + 24, t, 12, fill="#555")
        for c in range(6):
            if (c, r) in filled:
                cx = gx + tw + c * dw
                s.rect(cx + 6, ry + 6, dw - 12, 68, fill=FILL, stroke=INK, sw=1.2, rx=5)
                s.bar(cx + 14, ry + 16, dw * 0.6, 8, "#aaa"); s.bar(cx + 14, ry + 34, dw * 0.45); s.bar(cx + 14, ry + 52, dw * 0.3)
    s.pin(x - 12, y + 10, 3); s.pin(x + W - 320, y - 18, 4); s.pin(gx + tw + dw + 10, gy + 36 + 80 + 8, 5); s.pin(x + W - 12, y - 18, 6)
    return s


@fig
def wf_admin_deliberations():
    s = WF(1200, 800)
    x, y = s.shell("localhost:5173/admin/deliberations", ADMIN_NAV, "Deliberations", "admin@insfp.dz", "Administration")
    W = s.w - x - 40
    s.title(x, y + 14, "Délibérations")
    s.box(x, y + 40, W, 84)
    fw = (W - 220) / 3
    for i, (lab, ph) in enumerate([("Session", "Choisir une session"), ("Spécialité", "Choisir une spécialité"), ("Semestre", "Auto (semestre courant)")]):
        s.field(x + 16 + i * (fw + 10), y + 76, fw - 6, lab, ph, select=True)
    s.button(x + W - 186, y + 76, 170, "Rechercher", True)
    cols = [("Stagiaire", 250), ("Moy. calculée", 140), ("Moyenne / Résultat", 180), ("Observations", 210), ("Action", W - 780)]
    s.table(x, y + 144, cols, 5, 48, badge_col=2, badge_vals=["12.40 · Admis", "08.75 · Ajourné", "—"])
    # fenêtre modale
    mx, my, mw = x + W - 440, y + 330, 420
    s.rect(mx + 6, my + 6, mw, 290, fill="#d9d9d9", stroke="none", rx=8)
    s.box(mx, my, mw, 290, rx=8)
    s.text(mx + 18, my + 30, "Enregistrer la délibération", 15, weight="bold", fill="#111")
    s.field(mx + 18, my + 72, 180, "Moyenne (/20)", "12.40")
    s.field(mx + 214, my + 72, 188, "Résultat", "Admis", select=True)
    s.field(mx + 18, my + 140, 180, "Année académique", "2026-2027")
    s.field(mx + 214, my + 140, 188, "Date", "jj/mm/aaaa")
    s.field(mx + 18, my + 206, 384, "Observations", "")
    s.button(mx + mw - 238, my + 250, 100, "Annuler"); s.button(mx + mw - 128, my + 250, 110, "Confirmer", True)
    s.pin(x - 12, y + 92, 3); s.pin(x - 12, y + 230, 4); s.pin(mx - 12, my + 20, 5)
    return s


# ============ Web : enseignant ============
TEACH_NAV = ["Dashboard", "Messages", "Documents", "Modules", "Courses", "Tasks/Homeworks", "Schedule",
             "Attendance", "Exams & Grades", "Profile"]


@fig
def wf_ens_examens():
    s = WF(1200, 800)
    x, y = s.shell("localhost:5173/teacher/exams", TEACH_NAV, "Exams & Grades", "prof@insfp.dz", "Enseignant")
    W = s.w - x - 40
    s.title(x, y + 10, "Examens & notes", "Gérez les évaluations de vos modules")
    s.button(x + W - 160, y - 6, 160, "+ Créer un examen", True)
    s.box(x, y + 46, W, 60)
    s.field(x + 14, y + 59, W - 330, "", "Rechercher par titre ou module…")
    s.field(x + W - 306, y + 59, 180, "", "Tous les types", select=True)
    s.button(x + W - 112, y + 59, 98, "Filtrer", True)
    cw = (W - 32) / 3
    data = [("Examen", "Brouillon", True), ("Contrôle", "Brouillon", True), ("Contrôle", "Soumis", False),
            ("Contrôle", "Soumis", False), ("Examen", "Modifié", False), ("Contrôle", "Soumis", False)]
    for i, (typ, st, draft) in enumerate(data):
        cx, cy = x + (i % 3) * (cw + 16), y + 126 + (i // 3) * 270
        s.box(cx, cy, cw, 250)
        s.rect(cx, cy, cw, 5, fill="#9a9a9a", stroke="none")
        s.badge(cx + 14, cy + 18, typ); s.badge(cx + cw - 90, cy + 18, st, 76)
        s.bar(cx + 14, cy + 56, cw * 0.4, 10, "#aaa")
        s.rect(cx + 14, cy + 78, cw - 28, 56, fill=FILL, stroke=LIGHT, sw=1, rx=5)
        s.bar(cx + 24, cy + 90, cw * 0.45); s.bar(cx + 24, cy + 112, cw * 0.25); s.bar(cx + cw - 70, cy + 112, 40)
        s.bar(cx + 14, cy + 152, cw * 0.35)
        s.line(cx, cy + 172, cx + cw, cy + 172, LIGHT, 1)
        s.button(cx + 14, cy + 184, cw - 28, "Saisir / modifier les notes", h=28)
        if draft:
            s.rect(cx + 14, cy + 216, cw - 28, 28, fill=DARK, stroke=DARK, rx=5)
            s.text(cx + cw / 2, cy + 234, "Soumettre à l'administration", 12.5, "middle", "600", "white")
    s.pin(x + W - 172, y - 18, 3); s.pin(x - 12, y + 76, 4); s.pin(x - 12, y + 200, 5); s.pin(x + cw - 20, y + 350, 6)
    return s


@fig
def wf_ens_notes():
    s = WF(1200, 760)
    x, y = s.shell("localhost:5173/teacher/exams/12/grading", TEACH_NAV, "Exams & Grades", "prof@insfp.dz", "Enseignant")
    W = s.w - x - 40
    s.title(x, y + 10, "Saisie des notes", "Contrôle · PHP & Laravel · Groupe G1 · 90 min")
    s.button(x + W - 150, y - 6, 150, "Enregistrer", True)
    cols = [("Étudiant", 330), ("Note /20", 150), ("Lettre", 110), ("Observations (optionnel)", W - 590)]
    s.table(x, y + 50, cols, 9, 46)
    for r in range(9):
        ry = y + 50 + 34 + r * 46
        s.rect(x + 342, ry + 9, 90, 28, fill="white", stroke=INK, sw=1.2, rx=4)
        s.text(x + 356, ry + 28, "—", 12.5, fill="#999")
        s.rect(x + 590 + 12, ry + 9, W - 590 - 24, 28, fill="white", stroke=LIGHT, sw=1, rx=4)
    s.pin(x - 12, y + 30, 3); s.pin(x + 330, y + 108, 4); s.pin(x + 480, y + 108, 5); s.pin(x + W - 162, y - 18, 6)
    return s


@fig
def wf_ens_appel():
    s = WF(1200, 760)
    x, y = s.shell("localhost:5173/teacher/attendance/session/8/2026-10-04", TEACH_NAV, "Attendance", "prof@insfp.dz", "Enseignant")
    W = s.w - x - 40
    s.title(x, y + 10, "Faire l'appel", "PHP & Laravel · Samedi 04/10/2026 · 08:00 – 09:30 · Salle B12")
    s.button(x + W - 150, y - 6, 150, "Enregistrer", True)
    cw = (W - 32) / 3
    for i, lab in enumerate(["Inscrits", "Présents", "Absents"]):
        cx = x + i * (cw + 16)
        s.box(cx, y + 46, cw, 70)
        s.text(cx + 16, y + 72, lab, 12, fill="#666")
        s.text(cx + 16, y + 102, "00", 22, weight="bold", fill="#222")
    cols = [("Étudiant", 330), ("Présence", 380), ("Notes (optionnel)", W - 710)]
    s.table(x, y + 136, cols, 8, 46)
    for r in range(8):
        ry = y + 136 + 34 + r * 46
        for k, t in enumerate(["Présent", "Absent", "Retard", "Excusé"]):
            bx = x + 342 + k * 90
            s.rect(bx, ry + 10, 82, 26, fill=DARK if k == (r % 3 == 1) else "white", stroke=INK, sw=1.1, rx=13)
            s.text(bx + 41, ry + 27, t, 11.5, "middle", "600", "white" if k == (r % 3 == 1) else "#333")
        s.rect(x + 722, ry + 10, W - 734, 26, fill="white", stroke=LIGHT, sw=1, rx=4)
        s.text(x + 732, ry + 27, "Motif / observations…", 11.5, fill="#aaa")
    s.pin(x - 12, y + 80, 3); s.pin(x + 330, y + 196, 4); s.pin(x + 710, y + 196, 5); s.pin(x + W - 162, y - 18, 6)
    return s


# ============ Web : stagiaire ============
STU_NAV = ["Dashboard", "Messages", "Courses", "Documents", "Deliberations", "Tasks/Homeworks", "Schedule",
           "Attendance", "Exams", "Profile"]


@fig
def wf_stag_dashboard():
    s = WF(1200, 800)
    x, y = s.shell("localhost:5173/student/dashboard", STU_NAV, "Dashboard", "stagiaire@insfp.dz", "Stagiaire")
    W = s.w - x - 40
    s.title(x, y + 10, "Tableau de bord", "Développement Web et Mobile · Semestre 1 · Groupe G1")
    cw = (W - 48) / 4
    for i, lab in enumerate(["Modules suivis", "Moyenne actuelle", "Séances cette semaine", "Tâches en attente"]):
        cx = x + i * (cw + 16)
        s.box(cx, y + 46, cw, 96)
        s.text(cx + 16, y + 72, lab, 12, fill="#666")
        s.text(cx + 16, y + 110, "00", 26, weight="bold", fill="#222")
        s.rect(cx + cw - 50, y + 60, 34, 34, fill=FILL, stroke=MID, rx=6)
    s.box(x, y + 160, W * 0.38, 250)
    s.text(x + 16, y + 186, "Assiduité", 14, weight="bold", fill="#222")
    ax = x + W * 0.19
    s.circle(ax, y + 290, 70, fill="white", stroke="#aaa", sw=18)
    s.path(f"M{ax},{y+220} A70,70 0 0 1 {ax+66},{y+313}", stroke="#555", sw=18)
    for k, t in enumerate(["Présent", "Retard", "Absent", "Excusé"]):
        s.icon(x + 20 + k * 80, y + 380, 10); s.text(x + 34 + k * 80, y + 390, t, 11, fill="#555")
    s.box(x + W * 0.38 + 16, y + 160, W * 0.62 - 16, 250)
    s.text(x + W * 0.38 + 32, y + 186, "Prochains examens", 14, weight="bold", fill="#222")
    for r in range(4):
        ry = y + 204 + r * 48
        s.rect(x + W * 0.38 + 32, ry, W * 0.62 - 48, 40, fill=FILL, stroke=LIGHT, sw=1, rx=5)
        s.bar(x + W * 0.38 + 44, ry + 16, 180); s.badge(x + W - 110, ry + 10, "Contrôle", 76)
    s.table(x, y + 430, [("Code", 110), ("Module", 380), ("Coefficient", 150), ("Heures / semaine", 170), ("Total", W - 810)], 4, 38)
    s.pin(x - 12, y + 96, 3); s.pin(x - 12, y + 280, 4); s.pin(x + W * 0.38 + 4, y + 280, 5); s.pin(x - 12, y + 500, 6)
    return s


@fig
def wf_stag_edt():
    s = WF(1200, 760)
    x, y = s.shell("localhost:5173/student/schedule", STU_NAV, "Schedule", "stagiaire@insfp.dz", "Stagiaire")
    W = s.w - x - 40
    s.title(x, y + 10, "Mon emploi du temps", "Semaine en cours")
    days = ["Samedi", "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi"]
    tw = 70
    dw = (W - tw) / 6
    gy = y + 46
    s.box(x, gy, W, 36 + 6 * 76, rx=6)
    s.rect(x, gy, W, 36, fill=FILL, stroke=INK, sw=1.3, rx=6)
    s.text(x + 14, gy + 23, "HEURE", 10.5, weight="600", fill="#555")
    for i, d in enumerate(days):
        s.text(x + tw + i * dw + dw / 2, gy + 23, d.upper(), 10.5, "middle", "600", "#555")
        s.line(x + tw + i * dw, gy, x + tw + i * dw, gy + 36 + 6 * 76, LIGHT, 1)
    filled = {(0, 0), (0, 1), (1, 2), (2, 0), (3, 1), (3, 3), (4, 0), (5, 2)}
    for r, t in enumerate(["08:00", "09:30", "11:00", "13:00", "14:30", "16:00"]):
        ry = gy + 36 + r * 76
        s.line(x, ry, x + W, ry, LIGHT, 1)
        s.text(x + 14, ry + 24, t, 12, fill="#555")
        for c in range(6):
            if (c, r) in filled:
                cx = x + tw + c * dw
                s.rect(cx + 6, ry + 6, dw - 12, 64, fill=FILL, stroke=INK, sw=1.2, rx=5)
                s.bar(cx + 14, ry + 16, dw * 0.6, 8, "#aaa"); s.bar(cx + 14, ry + 34, dw * 0.45); s.bar(cx + 14, ry + 50, dw * 0.3)
    s.pin(x - 12, gy + 18, 3); s.pin(x + tw + 10, gy + 50, 4)
    return s


# ============ Mobile ============
def phone(s, ox, oy, title):
    s.rect(ox, oy, 300, 610, fill="#333", stroke="#222", sw=2, rx=34)
    s.rect(ox + 12, oy + 14, 276, 582, fill="white", stroke="none", rx=24)
    s.rect(ox + 115, oy + 20, 70, 8, fill="#555", stroke="none", rx=4)
    s.text(ox + 150, oy + 640, title, 13, "middle", "600", "#333")
    return ox + 12, oy + 14


@fig
def wf_mobile():
    s = WF(1040, 670)
    # 1. connexion
    px, py = phone(s, 20, 10, "Connexion")
    s.circle(px + 138, py + 90, 26, fill=LIGHT, stroke=MID)
    s.text(px + 138, py + 150, "Connexion", 18, "middle", "bold", "#111")
    s.text(px + 138, py + 170, "Bienvenue sur le portail INSFP", 11.5, "middle", fill="#777")
    s.field(px + 20, py + 215, 236, "E-mail ou n° d'inscription", "")
    s.field(px + 20, py + 285, 236, "Mot de passe", "••••••••")
    s.rect(px + 20, py + 335, 12, 12, fill="white", stroke=INK, rx=2); s.text(px + 38, py + 345, "Se souvenir de moi", 11, fill="#444")
    s.button(px + 20, py + 370, 236, "Se connecter", True, 40)
    s.pin(px + 12, py + 232, 1); s.pin(px + 12, py + 390, 2)
    # 2. tableau de bord
    px, py = phone(s, 370, 10, "Tableau de bord")
    s.rect(px, py, 276, 150, fill=DARK, stroke="none", rx=24)
    s.rect(px, py + 120, 276, 30, fill=DARK, stroke="none")
    s.text(px + 18, py + 52, "INSFP Portal", 11.5, fill="#ddd")
    s.text(px + 18, py + 76, "Bonjour, Prénom", 17, weight="bold", fill="white")
    s.circle(px + 250, py + 60, 11, fill="#777", stroke="white", sw=1)
    s.rect(px + 18, py + 96, 200, 26, fill="#666", stroke="none", rx=13)
    s.text(px + 30, py + 113, "Dév. Web et Mobile – S1", 11.5, fill="white")
    s.text(px + 18, py + 180, "ACCÈS RAPIDE", 10.5, weight="600", fill="#555")
    items = ["Messages", "Cours", "Documents", "Délibérations", "Devoirs", "Emploi du temps", "Assiduité", "Examens"]
    for i, it in enumerate(items):
        cx, cy = px + 18 + (i % 2) * 124, py + 194 + (i // 2) * 92
        s.box(cx, cy, 116, 80, rx=8)
        s.rect(cx + 42, cy + 12, 32, 32, fill=FILL, stroke=MID, rx=8)
        s.text(cx + 58, cy + 64, it, 11.5, "middle", "600", "#333")
    s.circle(px + 128, py + 202, 7, fill="#777", stroke="white", sw=1)
    s.pin(px + 8, py + 76, 3); s.pin(px + 8, py + 240, 4)
    # 3. devoir
    px, py = phone(s, 720, 10, "Remise d'un devoir")
    s.rect(px, py, 276, 64, fill="white", stroke="none")
    s.text(px + 18, py + 44, "‹  Devoir", 16, weight="bold", fill="#111")
    s.line(px, py + 64, px + 276, py + 64, LIGHT, 1)
    s.bar(px + 18, py + 88, 180, 10, "#aaa"); s.badge(px + 18, py + 108, "En ligne", 70); s.bar(px + 100, py + 115, 110)
    for k in range(3):
        s.bar(px + 18, py + 150 + k * 16, 230 - k * 40)
    s.text(px + 18, py + 222, "VOTRE RÉPONSE", 10.5, weight="600", fill="#444")
    s.rect(px + 18, py + 232, 240, 110, fill="white", stroke=INK, sw=1.2, rx=5)
    s.rect(px + 18, py + 358, 240, 44, fill=FILL, stroke=MID, sw=1.2, rx=5, dash="5,4")
    s.text(px + 138, py + 385, "+ Joindre un fichier (10 Mo max)", 11.5, "middle", fill="#555")
    s.button(px + 18, py + 420, 240, "Soumettre", True, 40)
    s.pin(px + 10, py + 285, 5); s.pin(px + 10, py + 380, 6); s.pin(px + 10, py + 440, 7)
    return s


if __name__ == "__main__":
    for i, f in enumerate(FIGS, 1):
        svg = f()
        name = f"wf_{i:02d}_{f.__name__[3:]}"
        render(svg, os.path.join(OUT, name + ".png"))
        print(name)
