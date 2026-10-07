"""Figure « Palette de couleurs » de la charte graphique (couleurs du logo INSFP)."""
import sys
from svglib import Svg, render

SW = [
    ('#0A2F5C', 'Bleu marine', 'Couleur principale : barre latérale, titres, boutons'),
    ('#03909E', 'Turquoise', 'Couleur secondaire : liens, élément actif, graphiques'),
    ('#D7971D', 'Doré', "Couleur d'accent : badges, mises en évidence"),
    ('#F4F7FA', 'Gris très clair', 'Fond des pages'),
    ('#3F4652', 'Gris anthracite', 'Texte courant'),
    ('#1E8E5A', 'Vert', 'Succès : présent, admis'),
    ('#C62828', 'Rouge', 'Erreur : absent, ajourné, suppression'),
]
W, row = 900, 70
s = Svg(W, 30 + len(SW) * row)
for i, (hexa, name, role) in enumerate(SW):
    y = 20 + i * row
    s.rect(40, y, 190, 48, fill=hexa, stroke='#C7D3E0', sw=1, rx=6)
    s.text(255, y + 21, hexa.upper(), 17, weight='bold', fill='#0A2F5C')
    s.text(255, y + 42, name, 13, fill='#3F4652')
    s.text(430, y + 31, role, 14, fill='#3F4652')
render(s, sys.argv[1])
print('ok')
