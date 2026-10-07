"""Bannières (parchemins) des pages de séparation des parties et des chapitres + cadre du thème de la page de garde."""
import json, os, sys
from svglib import Svg, render, wrap

OUT = sys.argv[1]
os.makedirs(OUT, exist_ok=True)

BANNERS = [
    ('Partie I : Partie théorique', 'Chapitre I : Étude préalable'),
    ('Partie II : Analyse et conception', "Chapitre II : Analyse de l'existant"),
    (None, 'Chapitre III : Modélisation et conception du nouveau système'),
    ('Partie III : Développement', 'Chapitre IV : La réalisation'),
]


def scroll(lines):
    W, H = 620, 470
    s = Svg(W, H, bg='white')
    s.add('<defs><linearGradient id="g" x1="0" y1="0" x2="0" y2="1">'
          '<stop offset="0" stop-color="#5FB7C2"/><stop offset="1" stop-color="#0A2F5C"/></linearGradient>'
          '<linearGradient id="r" x1="0" y1="0" x2="1" y2="0">'
          '<stop offset="0" stop-color="#E8F4F6"/><stop offset="1" stop-color="#7FB8D0"/></linearGradient></defs>')
    x0, y0, x1, y1 = 70, 60, 560, 420
    # corps du parchemin
    s.add(f'<path d="M{x0},{y0+20} H{x1-25} V{y1} H{x0+25} Z" fill="url(#g)"/>')
    # enroulement haut droit
    s.add(f'<path d="M{x1-25},{y0+20} a25,25 0 1 1 25,25 v{y1-y0-60} a25,25 0 0 1 -25,25" fill="url(#g)"/>')
    s.add(f'<circle cx="{x1-12}" cy="{y0+20}" r="13" fill="url(#r)" stroke="#0A2F5C" stroke-width="1.2"/>')
    # enroulement bas gauche
    s.add(f'<path d="M{x0+25},{y1} a25,25 0 1 1 -25,-25 v{-(y1-y0-60)} a25,25 0 0 1 25,-25" fill="url(#g)" opacity="0.95"/>')
    s.add(f'<circle cx="{x0+12}" cy="{y1}" r="13" fill="url(#r)" stroke="#0A2F5C" stroke-width="1.2"/>')
    s.add(f'<path d="M{x0+5},{y0+30} H{x1-30}" stroke="#D7971D" stroke-width="3"/>')
    s.add(f'<path d="M{x0+30},{y1-10} H{x1-5}" stroke="#D7971D" stroke-width="3"/>')
    out = []
    for l in lines:
        if l:
            out += wrap(l, 400, 12.2)
            out.append('')
    out = out[:-1]
    cy = (y0 + y1) / 2 + 10
    lh = 40
    y = cy - (len(out) - 1) * lh / 2 + 9
    for l in out:
        if l:
            s.text(W / 2, y, l, 25, 'middle', 'bold', 'white')
        y += lh if l else lh * 0.5
    return s


def theme_box(lines):
    W, H = 640, 150
    s = Svg(W, H, bg='white')
    s.rect(8, 8, W - 16, H - 16, fill='white', stroke='#0A2F5C', sw=3.5, rx=26)
    s.mtext(W / 2, H / 2, lines, 21, 28, weight='bold', fill='#0A2F5C')
    return s


index = {}
for i, (part, chap) in enumerate(BANNERS, 1):
    name = f'banniere_{i}.png'
    render(scroll([part, chap]), os.path.join(OUT, name))
    index[chap] = name
render(theme_box(["Conception et réalisation d'une plateforme web", "pour la gestion intégrée d'un institut", 'de formation professionnelle (INSFP)']),
       os.path.join(OUT, 'cadre_theme.png'))
json.dump(index, open(os.path.join(OUT, 'bannieres.json'), 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
print('ok')
