"""Organigrammes officiels (INSFP Mohamed Tayeb Boucenna) — couleurs du logo."""
import sys, os
from svglib import Svg, render, wrap

NAVY, TEAL, GOLD = '#0A2F5C', '#03909E', '#D7971D'


def box(s, x, y, w, t, kind):
    lines = wrap(t, w - 18, 6.6)
    h = 22 + len(lines) * 15
    style = {'dir': (NAVY, NAVY, 'white', 'bold'), 'sd': ('#E6F3F5', TEAL, NAVY, 'bold'),
             'svc': ('white', '#8FA3BA', '#2E3A48', '600'), 'hl': ('#FDEFC8', GOLD, NAVY, 'bold'),
             'hls': ('#FFF8E6', GOLD, '#2E3A48', '600')}[kind]
    s.rect(x - w / 2, y - h / 2, w, h, fill=style[0], stroke=style[1], sw=1.6 if kind in ('hl', 'hls') else 1.3, rx=6)
    s.mtext(x, y, lines, 12.3, 15, weight=style[3], fill=style[2])
    return h


def institut(highlight=True):
    s = Svg(1500, 400)
    SD = [
        ('Sous-direction de l\'administration et des finances', [
            'Service du personnel et de la formation', 'Service du budget et de la comptabilité',
            "Service de l'économat, des moyens généraux et des archives"]),
        ("Sous-direction de l'apprentissage et de la formation professionnelle continue", [
            "Service de l'apprentissage", 'Service de la formation professionnelle continue et du partenariat']),
        ('Sous-direction des études et des stages', [
            'Service de l\'organisation et du suivi de la formation résidentielle et des stages pratiques en milieu professionnel',
            'Service de la documentation et des supports pédagogiques']),
        ("Sous-direction de l'information, de l'orientation, de la numérisation et de l'insertion professionnelle", [
            "Service de l'information, de l'orientation et de la numérisation",
            "Service de la surveillance générale, de l'accompagnement et de l'aide à l'insertion"]),
    ]
    cw = 360
    xs = [30 + cw / 2 + i * (cw + 10) for i in range(4)]
    box(s, 750, 40, 260, 'Directeur', 'dir')
    s.line(750, 58, 750, 90, '#333', 1.3)
    s.line(xs[0], 90, xs[-1], 90, '#333', 1.3)
    for i, (name, svcs) in enumerate(SD):
        x = xs[i]
        hl = highlight and i == 1
        s.line(x, 90, x, 110, '#333', 1.3)
        h = box(s, x, 140, cw - 20, name, 'hl' if hl else 'sd')
        y = 140 + h / 2
        lx = x - cw / 2 + 22
        s.line(lx, y, lx, y + 12, '#555', 1.2)
        yy = y + 22
        for t in svcs:
            hh = 22 + len(wrap(t, cw - 80, 6.6)) * 15
            cy = yy + hh / 2
            s.line(lx, yy - 10, lx, cy, '#555', 1.2)
            s.line(lx, cy, lx + 16, cy, '#555', 1.2)
            box(s, lx + 16 + (cw - 60) / 2, cy, cw - 60, t, 'hls' if hl else 'svc')
            yy += hh + 14
    if highlight:
        s.rect(1180, 360, 18, 14, fill='#FDEFC8', stroke=GOLD, sw=1.4, rx=3)
        s.text(1206, 372, "Structure d'accueil", 12.5, fill='#2E3A48')
    return s


def structure():
    s = Svg(1100, 360)
    box(s, 550, 45, 560, "Sous-direction de l'apprentissage et de la formation professionnelle continue", 'hl')
    s.line(550, 72, 550, 110, '#333', 1.3); s.line(280, 110, 820, 110, '#333', 1.3)
    for x, t, tasks in [
        (280, "Service de l'apprentissage", ['Contrats et plans d\'apprentissage', 'Suivi des apprentis en entreprise']),
        (820, 'Service de la formation professionnelle continue et du partenariat', ['Cours du soir', 'Conventions de partenariat']),
    ]:
        s.line(x, 110, x, 130, '#333', 1.3)
        h = box(s, x, 165, 420, t, 'sd')
        y = 165 + h / 2
        s.line(x, y, x, y + 30, '#555', 1.2); s.line(x - 110, y + 30, x + 110, y + 30, '#555', 1.2)
        for dx, tt in zip((-110, 110), tasks):
            s.line(x + dx, y + 30, x + dx, y + 48, '#555', 1.2)
            box(s, x + dx, y + 85, 200, tt, 'svc')
    return s


if __name__ == '__main__':
    out = sys.argv[1]
    render(institut(), os.path.join(out, 'organigramme_insfp.png'))
    render(structure(), os.path.join(out, 'organigramme_structure.png'))
    print('ok')
