# Applique la palette du logo INSFP : marine #0A2F5C, turquoise #03909E, doré #D7971D
import re, shutil
from PIL import Image

NAVY, NAVY2, TEAL, GOLD = '0A2F5C', '06396E', '03909E', 'D7971D'

def sub(path, pairs):
    s = open(path, encoding='utf-8').read()
    for a, b in pairs:
        s = s.replace(a, b)
    open(path, 'w', encoding='utf-8').write(s)

# ---- mémoire
sub('memoire/lib.js', [
    ("const C = { ink: '1A1A1A', text: '53565A', gold: '8C6F3F', goldLight: 'C9B084', light: 'F4F1EA', grid: 'D9D4C8', head: '1A1A1A' };",
     f"const C = {{ ink: '{NAVY}', text: '3F4652', gold: '{GOLD}', goldLight: 'EBC77F', light: 'E8F4F6', grid: 'C7D3E0', head: '{NAVY}', teal: '{TEAL}' }};"),
    ("'FAF8F4'", "'F3F7FB'"),
])
sub('memoire/docs_section.js', [("'E7E0D0'", "'D5E9ED'"), ("'FAF8F4'", "'F3F7FB'"), ("'8C6F3F'", "'B07A10'")])
sub('memoire/build.js', [("fill: '161616'", f"fill: '{NAVY}'"), ("'BDBDBD'", "'C9D6E6'"), ("'5A5A5A'", "'3E6590'")])

# ---- diagrammes
sub('figs/uml.py', [('FILL, STROKE = "#ECECFF", "#7B61C9"', 'FILL, STROKE = "#E6F3F5", "#03909E"'),
                    ('fill="#8C6F3F", stroke="#8C6F3F"', 'fill="#D7971D", stroke="#D7971D"'),
                    ('fill="#7B61C9", stroke="#5a48a0"', 'fill="#03909E", stroke="#02707B"')])
sub('activity.py', [('FILL, STROKE = "#ECECFF", "#7B61C9"', 'FILL, STROKE = "#E6F3F5", "#03909E"'),
                    ('fill="#E3E0F5"', 'fill="#D5E9ED"')])
sub('figs/svglib.py', [])
open('cfg.json', 'w').write('{"theme":"base","maxTextSize":200000,"class":{"useMaxWidth":false},'
    '"themeVariables":{"primaryColor":"#E6F3F5","primaryBorderColor":"#03909E","primaryTextColor":"#0A2F5C",'
    '"lineColor":"#0A2F5C","fontFamily":"Segoe UI, Arial"}}')

# ---- images
D = 'C:/laragon/www/insfp/diagrams_memoire/memoire/'
logo = Image.open('../images/8.webp').convert('RGB'); logo.save(D + 'logo_insfp.png')
Image.open('../images/9.png').convert('RGBA').save(D + 'uml/scrum_cycle.png')
print('ok')
