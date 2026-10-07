// Captures d'écran réelles de l'application (environnement local, comptes de test du seeder ITDataSeeder).
const puppeteer = require('C:/Users/User/AppData/Local/npm-cache/_npx/668c188756b835f3/node_modules/puppeteer-core');
const path = require('path');
const fs = require('fs');

const OUT = process.argv[2];
const BASE = 'http://localhost:5173';
const CHROME = 'C:/Users/User/.cache/puppeteer/chrome-headless-shell/win64-148.0.7778.97/chrome-headless-shell-win64/chrome-headless-shell.exe';
fs.mkdirSync(OUT, { recursive: true });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function newPage(browser) {
  const ctx = await browser.createBrowserContext();
  const page = await ctx.newPage();
  await page.setViewport({ width: 1440, height: 860, deviceScaleFactor: 1.5 });
  await page.evaluateOnNewDocument(() => localStorage.setItem('theme', 'light'));
  return page;
}

async function shot(page, url, name, prepare) {
  await page.goto(BASE + url, { waitUntil: 'networkidle2', timeout: 30000 }).catch(() => {});
  await sleep(1500);
  if (prepare) { try { await prepare(page); } catch (e) { console.log('prep', name, e.message); } }
  await page.screenshot({ path: path.join(OUT, name + '.png') });
  console.log(name);
}

async function login(page, email) {
  await page.goto(BASE + '/login', { waitUntil: 'networkidle2' });
  const inputs = await page.$$('input');
  await inputs[0].type(email);
  await inputs[1].type('password');
  await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 20000 }).catch(() => {}), page.keyboard.press('Enter')]);
  await sleep(1500);
}

async function selectByIndex(page, selIndex, optIndex) {
  await page.evaluate((i, o) => {
    const s = document.querySelectorAll('select')[i];
    if (s && s.options.length > o) { s.selectedIndex = o; s.dispatchEvent(new Event('change', { bubbles: true })); }
  }, selIndex, optIndex);
  await sleep(1200);
}

(async () => {
  const browser = await puppeteer.launch({ executablePath: CHROME, args: ['--no-sandbox'] });

  // --- visiteur
  let p = await newPage(browser);
  await shot(p, '/login', 'cap_01_connexion');
  await shot(p, '/register', 'cap_02_inscription');

  // --- administration
  p = await newPage(browser);
  await login(p, 'admin@insfp.dz');
  await shot(p, '/admin/dashboard', 'cap_10_admin_dashboard');
  await shot(p, '/admin/students', 'cap_11_admin_stagiaires');
  await shot(p, '/admin/teachers', 'cap_12_admin_enseignants');
  await shot(p, '/admin/specialties', 'cap_13_admin_specialites');
  await shot(p, '/admin/sessions', 'cap_14_admin_sessions');
  await shot(p, '/admin/registration-generator', 'cap_15_admin_numeros', async (pg) => { await selectByIndex(pg, 0, 2); });
  await shot(p, '/admin/schedule', 'cap_16_admin_edt');
  await shot(p, '/admin/deliberations', 'cap_17_admin_deliberations', async (pg) => {
    await selectByIndex(pg, 0, 1); await selectByIndex(pg, 1, 1);
    const b = await pg.$$('button'); for (const x of b) { const t = await x.evaluate((e) => e.innerText); if (/search/i.test(t)) { await x.click(); break; } }
    await sleep(2000);
  });
  await shot(p, '/admin/advancement-reviews', 'cap_18_admin_passages');
  await shot(p, '/admin/files', 'cap_19_admin_documents');

  // --- enseignant
  p = await newPage(browser);
  await login(p, 'prof.3@insfp.dz');
  await shot(p, '/teacher/dashboard', 'cap_20_ens_dashboard');
  await shot(p, '/teacher/modules', 'cap_21_ens_modules');
  await shot(p, '/teacher/exams', 'cap_22_ens_examens');
  await shot(p, '/teacher/homeworks', 'cap_23_ens_devoirs');
  await shot(p, '/teacher/schedule', 'cap_24_ens_edt');

  // --- stagiaire
  p = await newPage(browser);
  await login(p, 'student.2@insfp.dz');
  await shot(p, '/student/dashboard', 'cap_30_stag_dashboard');
  await shot(p, '/student/schedule', 'cap_31_stag_edt');
  await shot(p, '/student/exams', 'cap_32_stag_examens');
  await shot(p, '/student/attendance', 'cap_33_stag_assiduite');
  await shot(p, '/student/homeworks', 'cap_34_stag_devoirs');
  await shot(p, '/student/deliberations', 'cap_35_stag_deliberations');

  await browser.close();
})();
