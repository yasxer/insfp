p = 'build.js'; s = open(p, encoding='utf-8').read()
a = s[s.index("      new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 400 },\n        children: [new TextRun({ text: '  INSFP  '"):s.index("      line('RÉPUBLIQUE ALGÉRIENNE")]
s = s.replace(a, """      new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 360 },
        children: [new L.d.ImageRun({ type: 'png', data: fs.readFileSync('C:/laragon/www/insfp/diagrams_memoire/memoire/logo_card.png'),
          transformation: { width: 150, height: 150 } })] }),
""")
open(p, 'w', encoding='utf-8').write(s)

p = 'lib.js'; s = open(p, encoding='utf-8').read()
s = s.replace("run: { font: 'Calibri', size: 23, bold: true, color: C.gold },", "run: { font: 'Calibri', size: 23, bold: true, color: C.teal },")
old = """    children: [new TextRun({ text: "Mémoire de fin d'études", size: 15, color: '8A8A8A' }),
      new TextRun({ text: '\\tINSFP — Plateforme de gestion intégrée', size: 15, color: '8A8A8A' })] })] });
}
function headerL() {"""
assert old in s
new = """    children: [new ImageRun({ type: 'png', data: fs.readFileSync(ROOT + '/memoire/logo_small.png'), transformation: { width: 17, height: 22 } }),
      new TextRun({ text: "  Mémoire de fin d'études", size: 15, color: '5A6B80' }),
      new TextRun({ text: '\\tINSFP — Plateforme de gestion intégrée', size: 15, color: '5A6B80' })] })] });
}
function headerL() {"""
s = s.replace(old, new)
s = s.replace("border: { bottom: { style: BorderStyle.SINGLE, size: 4, color: 'D0D0D0', space: 4 } },\n    children: [new ImageRun",
              "border: { bottom: { style: BorderStyle.SINGLE, size: 6, color: C.teal, space: 4 } },\n    children: [new ImageRun")
open(p, 'w', encoding='utf-8').write(s)
print('ok')
