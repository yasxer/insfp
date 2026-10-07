// Bibliothèque de mise en forme du mémoire (style « design moderne » : noir, doré, Calibri).
const fs = require('fs');
const path = require('path');
const d = require('docx');
const {
  Paragraph, TextRun, HeadingLevel, AlignmentType, Table, TableRow, TableCell, WidthType, ShadingType,
  BorderStyle, ImageRun, PageBreak, TabStopType, TabStopPosition, VerticalAlign, HeightRule, LevelFormat,
  PageNumber, Header, Footer, TableOfContents, StyleLevel,
} = d;

const C = { ink: '0A2F5C', text: '1A1A1A', gold: 'D7971D', goldLight: 'EBC77F', light: 'E8F4F6', grid: 'C7D3E0', head: '0A2F5C', teal: '03909E' };
const ROOT = 'C:/laragon/www/insfp/diagrams_memoire';
const CONTENT_W = 9070;       // largeur utile A4 portrait (dxa)
const CONTENT_W_L = 14002;    // largeur utile A4 paysage (dxa)

// ---------------------------------------------------------------- texte enrichi
// **gras**, *italique*, __souligné__, `code`, [[À COMPLÉTER : ...]] (surligné jaune)
function runs(text, base = {}) {
  const out = [];
  const re = /(\*\*[^*]+\*\*|\*[^*]+\*|__[^_]+__|`[^`]+`|\[\[[^\]]+\]\])/g;
  let last = 0, m;
  const push = (t, o = {}) => t && out.push(new TextRun({ text: t, ...base, ...o }));
  while ((m = re.exec(text))) {
    push(text.slice(last, m.index));
    const tok = m[0];
    if (tok.startsWith('**')) push(tok.slice(2, -2), { bold: true, color: base.color || C.ink });
    else if (tok.startsWith('__')) push(tok.slice(2, -2), { underline: {} });
    else if (tok.startsWith('`')) push(tok.slice(1, -1), { font: 'Consolas', size: (base.size || 22) - 2 });
    else if (tok.startsWith('[[')) push('⚠ ' + tok.slice(2, -2), { highlight: 'yellow', bold: true, color: '7A4B00' });
    else push(tok.slice(1, -1), { italics: true });
    last = m.index + tok.length;
  }
  push(text.slice(last));
  return out;
}

const P = (text, o = {}) => new Paragraph({
  children: runs(text, o.run || {}),
  alignment: o.align || AlignmentType.JUSTIFIED,
  spacing: { after: o.after ?? 140, before: o.before ?? 0, line: 336 },
  indent: o.indent, keepNext: o.keepNext,
});

const bullets = (items, level = 0) => items.map((t) => new Paragraph({
  children: runs(t), numbering: { reference: 'puces', level },
  alignment: AlignmentType.JUSTIFIED, spacing: { after: 80, line: 290 },
}));

const numbered = (items, ref = 'num') => items.map((t) => new Paragraph({
  children: runs(t), numbering: { reference: ref, level: 0 },
  alignment: AlignmentType.JUSTIFIED, spacing: { after: 80, line: 290 },
}));

// ---------------------------------------------------------------- titres
const H1 = (t) => new Paragraph({ heading: HeadingLevel.HEADING_1, children: [new TextRun(t)] });
const H2 = (t) => new Paragraph({ heading: HeadingLevel.HEADING_2, children: [new TextRun(t)] });
const H3 = (t) => new Paragraph({ heading: HeadingLevel.HEADING_3, children: [new TextRun(t)] });
const H4 = (t) => new Paragraph({
  children: [new TextRun({ text: t, bold: true, color: C.ink, size: 24 })],
  spacing: { before: 180, after: 100 }, keepNext: true,
});

const pageBreak = () => new Paragraph({ children: [new PageBreak()] });

// Marqueurs de section : build.js découpe le document (bannière + en-tête du chapitre)
function chapter(label, title) {
  const t = `${label} : ${title}`;
  return [{ __chapter: t }, new Paragraph({ heading: HeadingLevel.HEADING_1, children: [new TextRun(t)] })];
}
function partPage(num, title) {
  return [{ __part: `${num} : ${title}` }];
}
const section = (t) => ({ __section: t });

// ---------------------------------------------------------------- tableaux
const cellBorders = { top: { style: BorderStyle.SINGLE, size: 4, color: C.grid }, bottom: { style: BorderStyle.SINGLE, size: 4, color: C.grid },
  left: { style: BorderStyle.SINGLE, size: 4, color: C.grid }, right: { style: BorderStyle.SINGLE, size: 4, color: C.grid } };

function table(headers, rows, pct, o = {}) {
  const W = o.width || CONTENT_W;
  const widths = pct.map((p) => Math.round((W * p) / 100));
  widths[widths.length - 1] = W - widths.slice(0, -1).reduce((a, b) => a + b, 0);
  const size = o.size || 20;
  const mk = (txt, i, head, zebra) => new TableCell({
    width: { size: widths[i], type: WidthType.DXA },
    shading: { type: ShadingType.CLEAR, color: 'auto', fill: head ? C.head : zebra ? 'F3F7FB' : 'FFFFFF' },
    borders: cellBorders, verticalAlign: VerticalAlign.CENTER,
    margins: { top: 60, bottom: 60, left: 100, right: 100 },
    children: String(txt).split('\n').map((line) => new Paragraph({
      children: runs(line, { size, color: head ? 'FFFFFF' : C.text, bold: head }),
      alignment: o.align?.[i] || AlignmentType.LEFT, spacing: { after: 20, line: 260 },
    })),
  });
  return new Table({
    width: { size: W, type: WidthType.DXA }, columnWidths: widths,
    rows: [new TableRow({ tableHeader: true, children: headers.map((h, i) => mk(h, i, true)) }),
      ...rows.map((r, k) => new TableRow({ cantSplit: true, children: r.map((c, i) => mk(c, i, false, k % 2 === 1)) }))],
  });
}

// ---------------------------------------------------------------- figures & légendes
const counters = { chap: 0, fig: 0, tab: 0 };
const setChapter = (n) => { counters.chap = n; };

function pngSize(file) {
  const b = fs.readFileSync(file);
  return { w: b.readUInt32BE(16), h: b.readUInt32BE(20) };
}

function figure(rel, caption, o = {}) {
  const file = path.isAbsolute(rel) ? rel : path.join(ROOT, rel);
  const { w, h } = pngSize(file);
  const maxW = o.maxW || 600, maxH = o.maxH || 820;
  const k = Math.min(maxW / w, maxH / h, o.scale || 10);
  counters.fig += 1;
  const num = `Figure ${counters.fig}`;
  return [
    new Paragraph({ alignment: AlignmentType.CENTER, keepNext: true, spacing: { before: 120, after: 60 },
      children: [new ImageRun({ type: 'png', data: fs.readFileSync(file), transformation: { width: Math.round(w * k), height: Math.round(h * k) } })] }),
    new Paragraph({ style: 'FigCaption', alignment: AlignmentType.CENTER, children: [new TextRun(`${num} : ${caption}`)] }),
  ];
}

function tcaption(caption) {
  counters.tab += 1;
  return new Paragraph({ style: 'TabCaption', alignment: AlignmentType.CENTER,
    children: [new TextRun(`Tableau ${counters.tab} : ${caption}`)] });
}
const T = (caption, headers, rows, pct, o) => [table(headers, rows, pct, o), tcaption(caption)];

// Encadré (note / à compléter)
function note(text, kind = 'info') {
  const fill = kind === 'todo' ? 'FFF7D6' : C.light;
  const bc = kind === 'todo' ? 'D9A400' : C.gold;
  return new Paragraph({
    children: runs(text, { size: 20 }), alignment: AlignmentType.LEFT,
    shading: { type: ShadingType.CLEAR, color: 'auto', fill },
    border: { left: { style: BorderStyle.SINGLE, size: 24, color: bc, space: 8 } },
    indent: { left: 200, right: 100 }, spacing: { before: 100, after: 160, line: 280 },
  });
}

// Bloc de code
function code(text) {
  return text.split('\n').map((l, i, a) => new Paragraph({
    children: [new TextRun({ text: l.length ? l : ' ', font: 'Consolas', size: 16, color: '2B2B2B' })],
    shading: { type: ShadingType.CLEAR, color: 'auto', fill: 'F4F4F4' },
    border: { left: { style: BorderStyle.SINGLE, size: 18, color: C.gold, space: 6 } },
    indent: { left: 150 }, spacing: { after: 0, line: 240, before: i === 0 ? 80 : 0 },
    ...(i === a.length - 1 ? { spacing: { after: 160, line: 240 } } : {}),
  }));
}

// ---------------------------------------------------------------- en-têtes / pieds
function header(text) {
  if (!text) return new Header({ children: [new Paragraph({ children: [] })] });
  return new Header({ children: [new Paragraph({
    border: { bottom: { style: BorderStyle.SINGLE, size: 12, color: C.teal, space: 4 } },
    children: [new TextRun({ text, bold: true, size: 24, color: '1A1A1A' })] })] });
}
function headerL() {
  return new Header({ children: [new Paragraph({
    tabStops: [{ type: TabStopType.RIGHT, position: CONTENT_W_L }],
    border: { bottom: { style: BorderStyle.SINGLE, size: 4, color: 'D0D0D0', space: 4 } },
    children: [new TextRun({ text: "Mémoire de fin d'études", size: 15, color: '8A8A8A' }),
      new TextRun({ text: '\tINSFP — Plateforme de gestion intégrée', size: 15, color: '8A8A8A' })] })] });
}
function footer() {
  return new Footer({ children: [new Paragraph({ alignment: AlignmentType.CENTER,
    children: [new TextRun({ children: [PageNumber.CURRENT], size: 22 })] })] });
}

// ---------------------------------------------------------------- styles du document
const styles = {
  default: { document: { run: { font: 'Times New Roman', size: 24, color: C.text } } },
  paragraphStyles: [
    { id: 'Heading1', name: 'Heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true,
      run: { font: 'Times New Roman', size: 32, bold: true, color: C.ink },
      paragraph: { spacing: { before: 120, after: 320 }, outlineLevel: 0, keepNext: true,
        } },
    { id: 'Heading2', name: 'Heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true,
      run: { font: 'Times New Roman', size: 28, bold: true, color: C.ink },
      paragraph: { spacing: { before: 360, after: 160 }, outlineLevel: 1, keepNext: true } },
    { id: 'Heading3', name: 'Heading 3', basedOn: 'Normal', next: 'Normal', quickFormat: true,
      run: { font: 'Times New Roman', size: 24, bold: true, color: C.ink },
      paragraph: { spacing: { before: 260, after: 120 }, outlineLevel: 2, keepNext: true } },
    { id: 'FigCaption', name: 'FigCaption', basedOn: 'Normal', next: 'Normal',
      run: { size: 22, bold: true, color: '1A1A1A' }, paragraph: { spacing: { before: 40, after: 240 } } },
    { id: 'TabCaption', name: 'TabCaption', basedOn: 'Normal', next: 'Normal',
      run: { size: 22, bold: true, color: '1A1A1A' }, paragraph: { spacing: { before: 80, after: 240 } } },
    { id: 'TOCTitle', name: 'TOC Title', basedOn: 'Normal', run: { size: 32, bold: true, color: C.ink } },
  ],
};

const numbering = {
  config: [
    { reference: 'puces', levels: [
      { level: 0, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT,
        style: { run: { color: '1A1A1A' }, paragraph: { indent: { left: 560, hanging: 320 } } } },
      { level: 1, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT,
        style: { run: { color: C.gold }, paragraph: { indent: { left: 1000, hanging: 300 } } } }] },
    ...['num', 'num2', 'num3', 'num4', 'num5', 'num6'].map((ref) => ({ reference: ref, levels: [
      { level: 0, format: LevelFormat.DECIMAL, text: '%1.', alignment: AlignmentType.LEFT,
        style: { run: { color: '1A1A1A', bold: true }, paragraph: { indent: { left: 560, hanging: 360 } } } }] })),
  ],
};

module.exports = {
  d, C, section, CONTENT_W, CONTENT_W_L, runs, P, bullets, numbered, H1, H2, H3, H4, pageBreak, chapter, partPage,
  table, T, tcaption, figure, setChapter, note, code, header, headerL, footer, styles, numbering, counters,
};
