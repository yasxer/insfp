// Assemblage du mémoire INSFP → .docx (page de garde officielle, sections par chapitre)
const fs = require('fs');
const L = require('./lib');
const c1 = require('./content1');
const c2 = require('./content2');
const c3 = require('./content3');
const { Document, Packer, Paragraph, TextRun, AlignmentType, Table, TableRow, TableCell, WidthType, BorderStyle,
  NumberFormat, ImageRun, VerticalAlign } = L.d;

const OUT = process.argv[2] || 'memoire.docx';
const A4 = { width: 11906, height: 16838 };
const margins = { top: 1418, bottom: 1418, left: 1418, right: 1418, header: 700, footer: 700 };
const IMG = 'C:/laragon/www/insfp/diagrams_memoire/memoire/';
const BANNERS = JSON.parse(fs.readFileSync(IMG + 'bannieres/bannieres.json', 'utf-8'));

// ------------------------------------------------------------------ page de garde
function cover() {
  const t = (text, o = {}) => new TextRun({ text, font: 'Times New Roman', size: o.size || 24, bold: o.bold, underline: o.u ? {} : undefined });
  const line = (children, o = {}) => new Paragraph({ alignment: o.align || AlignmentType.CENTER, spacing: { before: o.before || 0, after: o.after ?? 60 }, children });
  const img = (file, w, h) => new ImageRun({ type: 'png', data: fs.readFileSync(IMG + file), transformation: { width: w, height: h } });
  const none = { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' };
  const nb = { top: none, bottom: none, left: none, right: none };
  const col = (children) => new TableCell({ width: { size: 4535, type: WidthType.DXA }, borders: nb, verticalAlign: VerticalAlign.TOP, children });
  return [
    line([t("MINISTÈRE DE LA FORMATION ET DE L'ENSEIGNEMENT PROFESSIONNELS", { bold: true, size: 22 })]),
    line([t('INSTITUT NATIONAL SPÉCIALISÉ DE FORMATION PROFESSIONNELLE', { bold: true, size: 22 })]),
    line([t('Mohamed Tayeb Boucenna, Hussein Dey ex-Mohammadia, Alger', { bold: true, size: 20 })], { after: 200 }),
    line([img('logo_institut.png', 95, 103)], { after: 240 }),
    line([t("MÉMOIRE DE FIN D'ÉTUDE", { bold: true, size: 36 })], { after: 240 }),
    line([t("En vue de l'obtention du diplôme de technicien supérieur en informatique option :", { bold: true })], { after: 120 }),
    line([t('Développeur web et mobile', { bold: true, size: 26 })], { after: 360 }),
    line([t('Thème :', { bold: true, size: 26, u: true })], { after: 160 }),
    line([img('bannieres/cadre_theme.png', 560, 131)], { after: 360 }),
    line([t("Organisme d'accueil", { bold: true, u: true })], { after: 80 }),
    line([t('Institut National Spécialisé de la Formation Professionnelle Mohamed Tayeb Boucenna')], { after: 120 }),
    line([img('logo_insfp.png', 70, 70)], { after: 360 }),
    new Table({ width: { size: 9070, type: WidthType.DXA }, columnWidths: [4535, 4535], rows: [new TableRow({ children: [
      col([line([t('Réalisé par :', { bold: true, u: true })], { align: AlignmentType.LEFT, after: 120 }),
        ...['HAOUES Yasser', 'CHRMAT Khaled', 'LASSEL Abdelmalek'].map((n) => line([t(n)], { align: AlignmentType.LEFT }))]),
      col([line([t('Promotrice :', { bold: true, u: true })], { align: AlignmentType.LEFT, after: 120 }),
        line([t('Mme SAIGHI')], { align: AlignmentType.LEFT })]),
    ] })] }),
    line([t('Promotion', { bold: true })], { before: 720, after: 80 }),
    line([t('Octobre 2026', { bold: true })]),
  ];
}

// ------------------------------------------------------------------ découpage en sections
const page = (extra = {}) => ({ page: { size: A4, margin: margins, ...extra } });

function bannerSection(chapter) {
  return {
    properties: page(), headers: { default: L.header(null) }, footers: { default: L.footer() },
    children: [
      new Paragraph({ spacing: { before: 2600 }, children: [] }),
      new Paragraph({ alignment: AlignmentType.CENTER, children: [new ImageRun({ type: 'png', data: fs.readFileSync(IMG + 'bannieres/' + BANNERS[chapter]), transformation: { width: 560, height: 425 } })] }),
    ],
  };
}

function splitSections(items) {
  const out = [];
  let cur = null, first = true, lastTitle = null;
  const open = (title) => {
    lastTitle = title;
    if (cur && cur.children.length) out.push(cur);
    cur = { properties: page(first ? { pageNumbers: { start: 1, formatType: NumberFormat.DECIMAL } } : {}),
      headers: { default: L.header(title) }, footers: { default: L.footer() }, children: [] };
    first = false;
  };
  for (const it of items) {
    if (it && it.__part) continue;
    if (it && it.__chapter) {
      if (cur && cur.children.length) out.push(cur);
      cur = null;
      if (first) { out.push({ ...bannerSection(it.__chapter), properties: page({ pageNumbers: { start: 1, formatType: NumberFormat.DECIMAL } }) }); first = false; }
      else out.push(bannerSection(it.__chapter));
      open(it.__chapter);
      continue;
    }
    if (it && it.__section) { open(it.__section); continue; }
    if (it && it.__a3) {
      if (cur && cur.children.length) out.push(cur);
      out.push({ properties: { page: { size: { width: 16838, height: 23811, orientation: L.d.PageOrientation.LANDSCAPE }, margin: { top: 900, bottom: 900, left: 1000, right: 1000, header: 500, footer: 500 } } },
        headers: { default: L.header(lastTitle) }, footers: { default: L.footer() },
        children: L.figure(it.__a3[0], it.__a3[1], { maxW: 1400, maxH: 920 }) });
      cur = { properties: page(), headers: { default: L.header(lastTitle) }, footers: { default: L.footer() }, children: [] };
      continue;
    }
    if (!cur) open(null);
    cur.children.push(it);
  }
  if (cur && cur.children.length) out.push(cur);
  return out;
}

const body = [...c1.introduction(), ...require('./chap1_new').chapitre1(), ...c1.chapitre2(), ...c2.chapitre3(), ...c3.chapitre4(),
  ...c3.conclusion(), ...c3.bibliographie()];

const doc = new Document({
  creator: 'INSFP', title: "Mémoire de fin d'études — Plateforme INSFP", description: 'Mémoire PFE',
  styles: L.styles, numbering: L.numbering, features: { updateFields: true },
  sections: [
    { properties: page(), headers: { default: L.header(null) }, footers: { default: new L.d.Footer({ children: [] }) }, children: cover() },
    { properties: page({ pageNumbers: { start: 1, formatType: NumberFormat.LOWER_ROMAN } }),
      headers: { default: L.header(null) }, footers: { default: L.footer() },
      children: [...c1.remerciements(), ...c1.dedicace(), ...c1.listes(), ...c1.abreviations()] },
    ...splitSections(body),
  ],
});

Packer.toBuffer(doc).then((buf) => { fs.writeFileSync(OUT, buf); console.log('OK', OUT, buf.length); });
