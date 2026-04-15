const fs = require('fs')

const files = [
  'src/views/teacher/Homeworks.vue',
  'src/views/student/Homeworks.vue',
  'src/views/teacher/HomeworkDetail.vue'
]

const colorReplacements = {
  'bg-[#8B2323]': 'bg-blue-600',
  'hover:bg-[#721c1c]': 'hover:bg-blue-700',
  'shadow-red-900/20': 'shadow-blue-900/20',
  'text-[#8B2323]': 'text-blue-600',
  'hover:text-[#721c1c]': 'hover:text-blue-700',
  'focus:ring-[#8B2323]/20': 'focus:ring-blue-600/20',
  'focus:border-[#8B2323]': 'focus:border-blue-600',
  'border-[#8B2323]/50': 'border-blue-600/50',
  'ring-[#8B2323]': 'ring-blue-600',
  'bg-red-50/10': 'bg-blue-50/50'
}

const icons = [
  { match: /<span class="material-icons([^"]*)">([^<]+)<\/span>/g, getRepl: (className, iconName) => {
    let componentName = ''
    switch(iconName.trim()) {
      case 'add_circle_outline': componentName = 'PlusCircleIcon'; break;
      case 'error_outline': componentName = 'ExclamationCircleIcon'; break;
      case 'check_circle_outline': componentName = 'CheckCircleIcon'; break;
      case 'schedule': componentName = 'ClockIcon'; break;
      case 'assignment_turned_in': componentName = 'ClipboardDocumentCheckIcon'; break;
      case 'arrow_forward': componentName = 'ArrowRightIcon'; break;
      case 'assignment': componentName = 'ClipboardDocumentListIcon'; break;
      case 'close': componentName = 'XMarkIcon'; break;
      case 'upload_file': componentName = 'ArrowUpTrayIcon'; break;
      case 'check_circle': componentName = 'CheckCircleIcon'; break;
      case 'autorenew': componentName = 'ArrowPathIcon'; break;
      case 'publish': componentName = 'PaperAirplaneIcon'; break;
      case 'arrow_back': componentName = 'ArrowLeftIcon'; break;
      case 'info': componentName = 'InformationCircleIcon'; break;
      case 'attachment': componentName = 'PaperClipIcon'; break;
      case 'download': componentName = 'ArrowDownTrayIcon'; break;
      case 'analytics': componentName = 'ChartBarIcon'; break;
      case 'pending_actions': componentName = 'ClockIcon'; break;
      case 'update': componentName = 'ArrowPathIcon'; break;
      case 'check': componentName = 'CheckIcon'; break;
      case 'more_vert': componentName = 'EllipsisVerticalIcon'; break;
      default: console.log('UNKNOWN ICON', iconName); componentName = 'QuestionMarkCircleIcon';
    }
    className = className.replace('material-icons', '').replace('text-[14px]', 'w-4 h-4').replace('text-[16px]', 'w-4 h-4').replace('text-[18px]', 'w-5 h-5').replace('text-[20px]', 'w-5 h-5').trim();
    if (!className.includes('w-') && !className.includes('text-')) {
       className += ' w-5 h-5'; // default size
    } else if (className.includes('text-3xl')) {
       className = className.replace('text-3xl', 'w-10 h-10')
    } else if (className.includes('text-xl')) {
       className = className.replace('text-xl', 'w-6 h-6')
    }
    return `<${componentName} class="${className}" />`
  }}
]

files.forEach(file => {
  let content = fs.readFileSync(file, 'utf-8')
  
  for (const [oldC, newC] of Object.entries(colorReplacements)) {
    content = content.split(oldC).join(newC)
  }
  
  icons.forEach(rule => {
    content = content.replace(rule.match, (m, c, n) => rule.getRepl(c, n))
  })
  
  // Also collect dependencies for Heroicons
  const foundIcons = new Set()
  content.replace(/<([A-Z][a-zA-Z0-Icon]+)\s+class=/g, (m, name) => {
    foundIcons.add(name)
  })
  
  if (foundIcons.size > 0 && !content.includes('@heroicons/vue')) {
    const importStr = `import { ${Array.from(foundIcons).join(', ')} } from '@heroicons/vue/24/outline'`
    content = content.replace('<script setup>', `<script setup>\n${importStr}`)
  }
  
  fs.writeFileSync(file, content)
})
console.log('Fixed styles and icons in all 3 files.')
