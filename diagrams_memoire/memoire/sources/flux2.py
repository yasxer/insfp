"""Diagramme de flux d'information (système existant) — notation : acteur externe / acteur interne / champ d'étude / flux."""
import math, sys, os
from svglib import Svg, render, wrap

EXT_FILL, EXT_STROKE = '#F1F1F1', '#8A8A8A'
INT_FILL, INT_STROKE = '#FDEFC8', '#D7971D'
NAVY = '#0A2F5C'
RX, RY = 105, 40

A = {  # nom : (x, y, interne ?)
    'takwin': (150, 120, False, ['Plateforme', 'takwin.dz']),
    'stg': (150, 470, False, ['Stagiaire']),
    'cfpa': (760, 48, False, ['CFPA', "(centre d'accueil)"]),
    'dfep': (1330, 230, False, ['DFEP', '(tutelle)']),
    'sco': (500, 230, True, ['Service de la', 'scolarité']),
    'dir': (1000, 230, True, ['Direction']),
    'sp': (500, 720, True, ['Service', 'pédagogique']),
    'ens': (760, 470, True, ['Enseignant']),
    'be': (1000, 720, True, ['Bureau des', 'examens']),
}

FLOWS = [  # (n°, de, vers)
    (1, 'stg', 'takwin'), (2, 'takwin', 'sco'),
    (3, 'stg', 'sco'), (4, 'sco', 'stg'),
    (5, 'sco', 'sp'),
    (6, 'sp', 'ens'), (7, 'sp', 'stg'),
    (8, 'ens', 'sco'), (9, 'stg', 'sco'),
    (10, 'sp', 'ens'), (11, 'sp', 'stg'),
    (12, 'ens', 'be'), (13, 'be', 'dir'), (14, 'dir', 'sco'),
    (15, 'sco', 'stg'), (16, 'stg', 'sco'), (17, 'sco', 'stg'),
    (18, 'sco', 'cfpa'), (19, 'dir', 'dfep'), (20, 'dfep', 'dir'),
]


def edge(cx, cy, ux, uy, px, py, off):
    """Point où la droite (décalée de off) sortant du centre dans la direction u coupe l'ellipse."""
    bx, by = cx + px * off, cy + py * off
    A_ = (ux / RX) ** 2 + (uy / RY) ** 2
    B_ = 2 * ((bx - cx) * ux / RX ** 2 + (by - cy) * uy / RY ** 2)
    C_ = ((bx - cx) / RX) ** 2 + ((by - cy) / RY) ** 2 - 1
    t = (-B_ + math.sqrt(B_ * B_ - 4 * A_ * C_)) / (2 * A_)
    return bx + ux * t, by + uy * t


def build():
    s = Svg(1480, 830)
    # champ d'étude
    s.rect(330, 120, 840, 680, fill='white', stroke='#222', sw=1.8)
    s.text(345, 788, "Champ d'étude : gestion pédagogique et scolarité de l'INSFP", 14, weight='bold', fill=NAVY)

    # regrouper les flux par paire d'acteurs pour les décaler
    pairs = {}
    for n, a, b in FLOWS:
        pairs.setdefault(tuple(sorted((a, b))), []).append((n, a, b))
    gap = 17
    for key, fl in pairs.items():
        a0, b0 = key
        (xa, ya, *_), (xb, yb, *_) = A[a0], A[b0]
        dx, dy = xb - xa, yb - ya
        L = math.hypot(dx, dy); ux, uy = dx / L, dy / L; px, py = -uy, ux
        m = len(fl)
        for i, (n, a, b) in enumerate(fl):
            off = (i - (m - 1) / 2) * gap
            p1 = edge(xa, ya, ux, uy, px, py, off)
            p2 = edge(xb, yb, -ux, -uy, px, py, off)
            src, dst = (p1, p2) if a == a0 else (p2, p1)
            s.line(*src, *dst, '#333', 1.3, marker='ar')
            f = 0.38 + 0.24 * ((i % 3) / 2) if m > 1 else 0.5
            if key == ('dfep', 'dir'):
                f = 0.72
            if key == ('cfpa', 'sco'):
                f = 0.62
            mx, my = p1[0] + (p2[0] - p1[0]) * f, p1[1] + (p2[1] - p1[1]) * f
            s.circle(mx, my, 11, fill='white', stroke=NAVY, sw=1.3)
            s.text(mx, my + 4.3, str(n), 11.5, 'middle', 'bold', NAVY)

    for k, (x, y, inner, lines) in A.items():
        if inner:
            s.ellipse(x, y, RX, RY, fill=INT_FILL, stroke=INT_STROKE, sw=1.6)
        else:
            s.add(f'<ellipse cx="{x}" cy="{y}" rx="{RX}" ry="{RY}" fill="{EXT_FILL}" stroke="{EXT_STROKE}" stroke-width="1.6" stroke-dasharray="7,5"/>')
        s.mtext(x, y, lines, 13.5, 16, weight='bold', fill=NAVY)
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
