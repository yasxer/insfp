const fs = require('fs')

const file = 'src/views/teacher/HomeworkDetail.vue';
let content = fs.readFileSync(file, 'utf-8');

// Doc icon
content = content.replace(/<div class="bg-red-100 p-2 rounded-md mr-3 text-red-600\s*group-hover:bg-red-600 group-hover:text-white transition-colors">\s*<QuestionMarkCircleIcon class="w-5 h-5" \/>/g,
  '<div class="bg-blue-100 p-2 rounded-md mr-3 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">\n                  <DocumentTextIcon class="w-5 h-5" />');

// Search Icon
content = content.replace(/<QuestionMarkCircleIcon class="text-gray-400 w-5 h-5 mr-2" \/>/g,
  '<MagnifyingGlassIcon class="text-gray-400 w-5 h-5 mr-2" />');

// Empty inbox icon
content = content.replace(/<QuestionMarkCircleIcon class="text-4xl text-gray-300 mb-3" \/>/g,
  '<InboxIcon class="w-10 h-10 text-gray-300 mx-auto mb-3" />');

// Clock Icon 
content = content.replace(/<QuestionMarkCircleIcon class="w-4 h-4 mr-1" \/>\s*Remis le:/g,
  '<ClockIcon class="w-4 h-4 mr-1" />\n                      Remis le:');

// Noté Check icon
content = content.replace(/<QuestionMarkCircleIcon class="text-\[12px\] mr-1" \/> Noté/g,
  '<CheckCircleIcon class="w-3 h-3 mr-1" /> Noté');

// Replace the imports to include the missing ones
content = content.replace(/import \{.*?\} from '@heroicons\/vue\/24\/outline'/, `import { ArrowLeftIcon, ExclamationCircleIcon, CheckCircleIcon, ClockIcon, ArrowDownTrayIcon, PaperClipIcon, XMarkIcon, DocumentTextIcon, MagnifyingGlassIcon, InboxIcon } from '@heroicons/vue/24/outline'`);

fs.writeFileSync(file, content);
console.log('Fixed teacher HomeworkDetail.vue')
