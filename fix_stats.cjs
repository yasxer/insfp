const fs = require('fs');
let code = fs.readFileSync('frontend/src/views/Home.vue', 'utf8');

const rx1 = /const statsObserver = new IntersectionObserver\(\(entries\) => \{\s*entries\.forEach\(\(entry\) => \{\s*if \(entry\.isIntersecting\) \{\s*entry\.target\.classList\.add\('visible'\)\s*statsObserver\.disconnect\(\)\s*\}\s*\}\)\s*\}, \{ threshold: 0\.5 \}\)/;

const rep1 = `const statsObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible')
          animateCounter('facultes', 4)
          animateCounter('programmes', 8)
          animateCounter('etudiants', 300)
          animateCounter('enseignants', 100)
          animateCounter('anciens', 200)
          animateCounter('partenariats', 50)
          statsObserver.disconnect()
        }
      })
    }, { threshold: 0.1 })`;

code = code.replace(rx1, rep1);

const rx2 = /\.stat-circle::after \{ content: ''; position: absolute; inset: -10px; border-radius: 50%; border: 3px dashed #00BCD4; border-right-style: solid; opacity: 0; transition: opacity 0\.3s; \}/;

const rep2 = `.stat-circle::after { content: ''; position: absolute; inset: -10px; border-radius: 50%; border: 3px dashed #00BCD4; border-right-style: solid; opacity: 1; animation: spinDash 15s linear infinite; }`;

code = code.replace(rx2, rep2);

const rx3 = /\.timeline-stat:hover \.stat-circle \{ transform: scale\(1\.05\); \}\s*\.timeline-stat:hover \.stat-circle::after \{ opacity: 1; animation: spinDash [0-9]+s linear infinite; \}/g;

code = code.replace(rx3, '');

fs.writeFileSync('frontend/src/views/Home.vue', code);
console.log('Fixed styles and logic');
const fs = require('fs');
let code = fs.readFileSync('frontend/src/views/Home.vue', 'utf8');

const rx1 = /const statsObserver = new IntersectionObserver\(\(entries\) => \{\s*entries\.forEach\(\(entry\) => \{\s*if \(entry\.isIntersecting\) \{\s*entry\.target\.classList\.add\('visible'\)\s*statsObserver\.disconnect\(\)\s*\}\s*\}\)\s*\}, \{ threshold: 0\.5 \}\)/;

const rep1 = \const statsObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible')
          animateCounter('facultes', 4)
          animateCounter('programmes', 8)
          animateCounter('etudiants', 300)
          animateCounter('enseignants', 100)
          animateCounter('anciens', 200)
          animateCounter('partenariats', 50)
          statsObserver.disconnect()
        }
      })
    }, { threshold: 0.1 })\;

code = code.replace(rx1, rep1);

const rx2 = /\.stat-circle::after \{ content: ''; position: absolute; inset: -10px; border-radius: 50%; border: 3px dashed #00BCD4; border-right-style: solid; opacity: 0; transition: opacity 0\.3s; \}/;

const rep2 = \.stat-circle::after { content: ''; position: absolute; inset: -10px; border-radius: 50%; border: 3px dashed #00BCD4; border-right-style: solid; opacity: 1; animation: spinDash 15s linear infinite; }\;

code = code.replace(rx2, rep2);

const rx3 = /\.timeline-stat:hover \.stat-circle \{ transform: scale\(1\.05\); \}\s*\.timeline-stat:hover \.stat-circle::after \{ opacity: 1; animation: spinDash 10s linear infinite; \}/g;

code = code.replace(rx3, '');

fs.writeFileSync('frontend/src/views/Home.vue', code);
console.log('Fixed styles and logic');

