p = 'lib.js'; s = open(p, encoding='utf-8').read()

def rep(a, b):
    global s
    assert a in s, a[:80]
    s = s.replace(a, b)

rep("text: '3F4652',", "text: '1A1A1A',")
rep("spacing: { after: o.after ?? 140, before: o.before ?? 0, line: 300 },", "spacing: { after: o.after ?? 140, before: o.before ?? 0, line: 336 },")
# chapitres / parties : marqueurs de section
a = s.index('// En-tête de chapitre'); b = s.index('// ---------------------------------------------------------------- tableaux')
s = s[:a] + """// Marqueurs de section : build.js découpe le document (bannière + en-tête du chapitre)
function chapter(label, title) {
  const t = `${label} : ${title}`;
  return [{ __chapter: t }, new Paragraph({ heading: HeadingLevel.HEADING_1, children: [new TextRun(t)] })];
}
function partPage(num, title) {
  return [{ __part: `${num} : ${title}` }];
}
const section = (t) => ({ __section: t });

""" + s[b:]
rep("const size = o.size || 18;", "const size = o.size || 20;")
# légendes numérotées en continu, sous le tableau
rep("const setChapter = (n) => { counters.chap = n; counters.fig = 0; counters.tab = 0; };", "const setChapter = (n) => { counters.chap = n; };")
rep("const num = `Figure ${counters.chap}.${counters.fig}`;", "const num = `Figure ${counters.fig}`;")
rep("""  return new Paragraph({ style: 'TabCaption', alignment: AlignmentType.CENTER, keepNext: true,
    children: [new TextRun(`Tableau ${counters.chap}.${counters.tab} : ${caption}`)] });""",
    """  return new Paragraph({ style: 'TabCaption', alignment: AlignmentType.CENTER,
    children: [new TextRun(`Tableau ${counters.tab} : ${caption}`)] });""")
rep("const T = (caption, headers, rows, pct, o) => [tcaption(caption), table(headers, rows, pct, o), P('', { after: 60 })];",
    "const T = (caption, headers, rows, pct, o) => [table(headers, rows, pct, o), tcaption(caption)];")
# en-tête = titre du chapitre ; pied = numéro
a = s.index('function header() {'); b = s.index('function headerL() {')
s = s[:a] + """function header(text) {
  if (!text) return new Header({ children: [new Paragraph({ children: [] })] });
  return new Header({ children: [new Paragraph({
    border: { bottom: { style: BorderStyle.SINGLE, size: 12, color: C.teal, space: 4 } },
    children: [new TextRun({ text, bold: true, size: 24, color: '1A1A1A' })] })] });
}
""" + s[b:]
a = s.index('function footer() {'); b = s.index('// ---------------------------------------------------------------- styles du document')
s = s[:a] + """function footer() {
  return new Footer({ children: [new Paragraph({ alignment: AlignmentType.CENTER,
    children: [new TextRun({ children: [PageNumber.CURRENT], size: 22 })] })] });
}

""" + s[b:]
# styles : Times New Roman, tailles du mémoire de référence
rep("default: { document: { run: { font: 'Calibri', size: 22, color: C.text } } },",
    "default: { document: { run: { font: 'Times New Roman', size: 24, color: C.text } } },")
rep("run: { font: 'Calibri', size: 36, bold: true, color: C.ink },", "run: { font: 'Times New Roman', size: 32, bold: true, color: C.ink },")
rep("run: { font: 'Calibri', size: 27, bold: true, color: C.ink },", "run: { font: 'Times New Roman', size: 28, bold: true, color: C.ink },")
rep("run: { font: 'Calibri', size: 23, bold: true, color: C.teal },", "run: { font: 'Times New Roman', size: 24, bold: true, color: C.ink },")
rep("border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: C.gold, space: 8 } } } },", "} },")
rep("run: { size: 18, italics: true, color: '6A6A6A' }, paragraph: { spacing: { before: 40, after: 240 } } },",
    "run: { size: 22, bold: true, color: '1A1A1A' }, paragraph: { spacing: { before: 40, after: 240 } } },")
rep("run: { size: 18, italics: true, color: '6A6A6A' }, paragraph: { spacing: { before: 160, after: 80 } } },",
    "run: { size: 22, bold: true, color: '1A1A1A' }, paragraph: { spacing: { before: 80, after: 240 } } },")
rep("children: [new TextRun({ text: t, bold: true, italics: true, color: C.ink, size: 22 })],", "children: [new TextRun({ text: t, bold: true, color: C.ink, size: 24 })],")
# puces classiques
rep("{ level: 0, format: LevelFormat.BULLET, text: '—', alignment: AlignmentType.LEFT,\n        style: { run: { color: C.gold, bold: true },",
    "{ level: 0, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT,\n        style: { run: { color: '1A1A1A' },")
rep("style: { run: { color: C.gold, bold: true }, paragraph: { indent: { left: 560, hanging: 360 } } } }] })),",
    "style: { run: { color: '1A1A1A', bold: true }, paragraph: { indent: { left: 560, hanging: 360 } } } }] })),")
rep("  d, C, CONTENT_W,", "  d, C, section, CONTENT_W,")
open(p, 'w', encoding='utf-8').write(s)

# tableaux des fiches : légende sous le tableau
p = 'docs_section.js'; s = open(p, encoding='utf-8').read()
s = s.replace("""    tcaption(`Fiche description du document ${f.code}`),
    new Table({ width: { size: W, type: WidthType.DXA }, columnWidths: COLS, rows }),""",
"""    new Table({ width: { size: W, type: WidthType.DXA }, columnWidths: COLS, rows }),
    tcaption(`Fiche description du document ${f.code}`),""")
open(p, 'w', encoding='utf-8').write(s)
print('ok')
