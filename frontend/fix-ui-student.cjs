const fs = require('fs')

const file = 'src/views/student/Homeworks.vue';
let content = fs.readFileSync(file, 'utf-8');

// Fix the graded/submitted icon inside the dynamic binding
content = content.replace(/<QuestionMarkCircleIcon class="w-4 h-4 mr-1" \/>\s*\{\{ getStatusText\(hw\) \}\}/g, 
  '<component :is="getStatusIcon(hw)" class="w-4 h-4 mr-1" />\n                {{ getStatusText(hw) }}');

// Fix "Remettre mon travail"
content = content.replace(/<QuestionMarkCircleIcon class="mr-2 w-5 h-5\s*group-hover:-translate-y-1 transition-transform" \/>/g, 
  '<ArrowUpTrayIcon class="mr-2 w-5 h-5 group-hover:-translate-y-1 transition-transform" />');

// Fix "Aucun devoir" large icon
content = content.replace(/<QuestionMarkCircleIcon class="text-4xl" \/>/g, 
  '<InboxIcon class="w-10 h-10" />');

// Fix "Voir la pièce" link icon
content = content.replace(/<QuestionMarkCircleIcon class="w-4 h-4 mr-1" \/>\s*Voir la pièce/,
  '<LinkIcon class="w-4 h-4 mr-1" />\n                  Voir la pièce');

// Fix the "Devoirs rendus" box icon
content = content.replace(/<QuestionMarkCircleIcon class="text-2xl" \/>/,
  '<CheckCircleIcon class="w-6 h-6" />');

// Replace the imports with the missing ones
content = content.replace(/import \{.*?\} from '@heroicons\/vue\/24\/outline'/, `import { ExclamationCircleIcon, CheckCircleIcon, ClipboardDocumentListIcon, ClockIcon, ArrowDownTrayIcon, XMarkIcon, InformationCircleIcon, ArrowUpTrayIcon, InboxIcon, LinkIcon } from '@heroicons/vue/24/outline'`);

// Replace the getStatusIcon function
const oldIconFunc = `const getStatusIcon = (hw) => {
  if (hw.status === 'graded') return 'done_all'
  if (hw.status === 'submitted') return 'done'
  return 'pending_actions'
}`

const newIconFunc = `const getStatusIcon = (hw) => {
  if (hw.status === 'graded') return CheckCircleIcon
  if (hw.status === 'submitted') return CheckCircleIcon
  return ClockIcon
}`

content = content.replace(oldIconFunc, newIconFunc);

fs.writeFileSync(file, content);
console.log('Fixed student Homeworks.vue')
