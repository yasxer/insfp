p = 'content2.js'; s = open(p, encoding='utf-8').read()
a = s.index("    H3('5.3 La charte graphique'),")
b = s.index("    H2('6. Conclusion'),")
new = """    H3('5.3 La charte graphique'),
    P("La charte graphique fixe l'identité visuelle de la plateforme : le logo, les couleurs, la typographie et le style des composants. Elle est commune à l'application web et à l'application mobile."),
    H4('5.3.1 Logo'),
    P("Le logo reprend les symboles de la formation professionnelle : la toque de diplômé, le livre ouvert, l'engrenage (métiers techniques) et un personnage en progression. Le sigle **INSFP** et la mention « Formation Professionnelle » sont accompagnés du croissant et de l'étoile du drapeau national."),
    ...figure('memoire/logo_insfp.png', 'Logo de la plateforme INSFP', { maxW: 260, maxH: 260 }),
    H4('5.3.2 Palette de couleurs'),
    P("Les couleurs principales sont tirées du logo ; elles sont complétées par des couleurs neutres et des couleurs d'état."),
    ...figure('memoire/palette_couleurs.png', 'Palette de couleurs', { maxW: 560 }),
    H4('5.3.3 Typographie et composants'),
    ...bullets([
      "**Typographie** : police sans empattement (Plus Jakarta Sans, à défaut Arial) ; titres en gras, étiquettes de formulaires en petites capitales espacées.",
      "**Composants** : cartes à bordure fine, tableaux filtrables et paginés, badges de statut colorés, fenêtres modales, notifications (toasts), indicateurs de chargement.",
      "**Icônes** : bibliothèque Heroicons (contour), utilisée de façon cohérente dans la navigation et les boutons.",
      "**Formes** : rayons de 4 à 8 px, ombres légères.",
      "**Responsive** : la barre latérale se replie sur petit écran ; l'application mobile reprend les mêmes couleurs ; un thème sombre est disponible.",
    ]),

"""
s = s[:a] + new + s[b:]
open(p, 'w', encoding='utf-8').write(s)
print('ok')
