"""Diagramme de flux d'information du système existant (services réels de l'INSFP, documents D1 à D11)."""
import math, sys, os
from svglib import Svg, render

EXT_FILL, EXT_STROKE = '#F1F1F1', '#8A8A8A'
INT_FILL, INT_STROKE = '#FDEFC8', '#D7971D'
NAVY = '#0A2F5C'
RX, RY = 112, 42

A = {
    'takwin': (150, 150, False, ['Plateforme', 'takwin.dz']),
    'stg': (150, 560, False, ['Stagiaire', '(candidat)']),
    'dfep': (1370, 150, False, ['DFEP', "d'Alger"]),
    'sio': (510, 230, True, ["Service de l'information,", "de l'orientation et de", 'la numérisation']),
    'dir': (1010, 230, True, ['Direction']),
    'sfpc': (760, 470, True, ['Service de la formation', 'professionnelle continue']),
    'ens': (510, 720, True, ['Enseignant']),
    'surv': (1010, 720, True, ['Service de la', 'surveillance générale']),
}

# (n°, de, vers, intitulé, document)
FLOWS = [
    ('F1', 'dfep', 'dir', 'Correspondance de la DFEP', 'D1'),
    ('F1', 'dir', 'sio', 'Correspondance de la DFEP', 'D1'),
    ('F2', 'sio', 'takwin', 'Calendrier de la session', 'D2'),
    ('F2', 'sio', 'stg', 'Calendrier de la session', 'D2'),
    ('F3', 'sio', 'takwin', 'Fiche descriptive de la spécialité', 'D3'),
    ('F4', 'takwin', 'sio', 'Liste des candidats inscrits', '—'),
    ('F5', 'stg', 'sio', "Dossier d'inscription", '—'),
    ('F6', 'sio', 'sfpc', 'Liste des stagiaires admis', '—'),
    ('F7', 'sfpc', 'ens', 'Emploi du temps', 'D4'),
    ('F7', 'sfpc', 'stg', 'Emploi du temps', 'D4'),
    ('F8', 'ens', 'surv', "Feuille d'absences", 'D5'),
    ('F9', 'sfpc', 'ens', 'Planning des examens semestriels', 'D6'),
    ('F9', 'sfpc', 'stg', 'Planning des examens semestriels', 'D6'),
    ('F10', 'ens', 'sfpc', 'Document de transfert des notes', 'D7'),
    ('F11', 'sfpc', 'dir', 'Procès-verbal des délibérations', 'D8'),
    ("F11'", 'dir', 'sfpc', 'Procès-verbal des délibérations signé', 'D8'),
    ('F12', 'surv', 'sfpc', 'État des absences des stagiaires', '—'),
    ('F13', 'sfpc', 'dir', 'Relevé de notes', 'D9'),
    ("F13'", 'dir', 'sfpc', 'Relevé de notes signé', 'D9'),
    ("F13'", 'sfpc', 'stg', 'Relevé de notes signé', 'D9'),
    ('F14', 'stg', 'sfpc', "Demande d'attestation de stage", '—'),
    ('F15', 'sfpc', 'dir', 'Attestation de stage', 'D10'),
    ("F15'", 'dir', 'sfpc', 'Attestation de stage signée', 'D10'),
    ("F15'", 'sfpc', 'stg', 'Attestation de stage signée', 'D10'),
]


def edge(cx, cy, ux, uy, px, py, off):
    bx, by = cx + px * off, cy + py * off
    a = (ux / RX) ** 2 + (uy / RY) ** 2
    b = 2 * ((bx - cx) * ux / RX ** 2 + (by - cy) * uy / RY ** 2)
    c = ((bx - cx) / RX) ** 2 + ((by - cy) / RY) ** 2 - 1
    t = (-b + math.sqrt(b * b - 4 * a * c)) / (2 * a)
    return bx + ux * t, by + uy * t


def build():
    s = Svg(1520, 860)
    s.rect(330, 110, 870, 720, fill='white', stroke='#222', sw=1.8)
    s.text(345, 818, "Champ d'étude : gestion pédagogique des stagiaires de la formation professionnelle continue", 13.5, weight='bold', fill=NAVY)
    pairs = {}
    for n, a, b, *_ in FLOWS:
        pairs.setdefault(tuple(sorted((a, b))), []).append((n, a, b))
    for key, fl in pairs.items():
        a0, b0 = key
        (xa, ya, *_), (xb, yb, *_) = A[a0], A[b0]
        dx, dy = xb - xa, yb - ya
        L = math.hypot(dx, dy); ux, uy = dx / L, dy / L; px, py = -uy, ux
        m = len(fl)
        for i, (n, a, b) in enumerate(fl):
            off = (i - (m - 1) / 2) * 16
            p1 = edge(xa, ya, ux, uy, px, py, off)
            p2 = edge(xb, yb, -ux, -uy, px, py, off)
            src, dst = (p1, p2) if a == a0 else (p2, p1)
            s.line(*src, *dst, '#333', 1.3, marker='ar')
            f = 0.5 if m == 1 else 0.3 + 0.4 * (i / (m - 1))
            mx, my = p1[0] + (p2[0] - p1[0]) * f, p1[1] + (p2[1] - p1[1]) * f
            s.rect(mx - 17, my - 10, 34, 20, fill='white', stroke=NAVY, sw=1.3, rx=10)
            s.text(mx, my + 4.3, n, 11, 'middle', 'bold', NAVY)
    for k, (x, y, inner, lines) in A.items():
        if inner:
            s.ellipse(x, y, RX, RY, fill=INT_FILL, stroke=INT_STROKE, sw=1.6)
        else:
            s.add(f'<ellipse cx="{x}" cy="{y}" rx="{RX}" ry="{RY}" fill="{EXT_FILL}" stroke="{EXT_STROKE}" '
                  f'stroke-width="1.6" stroke-dasharray="7,5"/>')
        s.mtext(x, y, lines, 12.5, 15, weight='bold', fill=NAVY)
    return s


def notation():
    s = Svg(560, 250)
    s.rect(10, 10, 540, 230, fill='white', stroke='#222', sw=1.2)
    s.line(160, 10, 160, 240, '#222', 1.2); s.line(10, 50, 550, 50, '#222', 1.2)
    s.text(85, 36, 'Signe', 14, 'middle', 'bold', NAVY); s.text(355, 36, 'Désignation', 14, 'middle', 'bold', NAVY)
    s.add(f'<ellipse cx="85" cy="85" rx="55" ry="17" fill="{EXT_FILL}" stroke="{EXT_STROKE}" stroke-width="1.4" stroke-dasharray="6,4"/>')
    s.text(185, 90, 'Acteur externe', 14)
    s.ellipse(85, 133, 55, 17, fill=INT_FILL, stroke=INT_STROKE, sw=1.4)
    s.text(185, 138, 'Acteur interne', 14)
    s.rect(40, 165, 90, 30, fill='white', stroke='#222', sw=1.4)
    s.text(185, 185, "Champ d'étude", 14)
    s.line(40, 222, 130, 222, '#333', 1.4, marker='ar')
    s.text(185, 227, "Flux d'information (numéroté)", 14)
    return s


if __name__ == '__main__':
    out = sys.argv[1]
    render(build(), os.path.join(out, 'flux_information.png'))
    render(notation(), os.path.join(out, 'flux_notation.png'))
    print('ok')
