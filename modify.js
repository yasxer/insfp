const fs = require('fs');
const path = require('path');
const file = path.join(__dirname, 'frontend/src/views/teacher/Exams.vue');
let content = fs.readFileSync(file, 'utf8');

// Update exam_type default value and options
content = content.replace(/exam_type: 'exam',/g, "exam_type: 'midterm',");
content = content.replace(/<option value="exam">Examen<\/option>/g, '<option value="midterm">Examen partiel (Midterm)<\/option>');
content = content.replace(/<option value="control">Contrôle<\/option>/g, '<option value="final">Examen final (Final)<\/option><option value="rattrapage">Rattrapage<\/option>');

// Update rendering of types
content = content.replace(/type === 'exam'/g, "type === 'midterm'");
content = content.replace(/return 'Examen'/g, "return 'Examen partiel'");
content = content.replace(/type === 'control'/g, "type === 'final'");
content = content.replace(/return 'Contrôle'/g, "return 'Examen final'");
content = content.replace(/return 'Inconnu'/g, "if (type === 'rattrapage') return 'Rattrapage'; return 'Inconnu'");

// Add specialty and semester selects before module select
const moduleSelectBlock =                     <!-- Module -->
                    <div>
                      <label for="module_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Module</label>
                      <select id="module_id" v-model="form.module_id" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled>Sélectionner un module</option>
                        <option v-for="mod in modules" :key="mod.id" :value="mod.id">{{ mod.name }}</option>
                      </select>
                    </div>;

const cascadingSelects =                     <!-- Specialité -->
                    <div>
                      <label for="specialty_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Spécialité</label>
                      <select id="specialty_id" v-model="selectedSpecialty" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled>Sélectionner une spécialité</option>
                        <option v-for="spec in uniqueSpecialties" :key="spec.id" :value="spec.id">{{ spec.name }}</option>
                      </select>
                    </div>

                    <!-- Semester -->
                    <div>
                      <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semestre</label>
                      <select id="semester" v-model="selectedSemester" :disabled="!selectedSpecialty" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled>Sélectionner un semestre</option>
                        <option v-for="sem in availableSemesters" :key="sem" :value="sem">Semestre {{ sem }}</option>
                      </select>
                    </div>

                    <!-- Module -->
                    <div>
                      <label for="module_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Module</label>
                      <select id="module_id" v-model="form.module_id" :disabled="!selectedSemester" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled>Sélectionner un module</option>
                        <option v-for="mod in filteredModules" :key="mod.id" :value="mod.id">{{ mod.name }}</option>
                      </select>
                    </div>;

content = content.replace(moduleSelectBlock, cascadingSelects);

// Add reactive state and computed properties
const scriptEndMatch = "  const isModalOpen = ref(false)";
const newVariables =   const isModalOpen = ref(false)
  const selectedSpecialty = ref('')
  const selectedSemester = ref('')

  const uniqueSpecialties = computed(() => {
    const specs = []
    const specMap = new Map()
    modules.value.forEach(m => {
      if (m.specialty_id && !specMap.has(m.specialty_id)) {
        specMap.set(m.specialty_id, true)
        specs.push({ id: m.specialty_id, name: m.specialty_name || 'Spécialité inconnue' })
      }
    })
    return specs
  })

  const availableSemesters = computed(() => {
    if (!selectedSpecialty.value) return []
    const sems = new Set()
    modules.value.forEach(m => {
      if (m.specialty_id === selectedSpecialty.value && m.semester) {
        sems.add(m.semester)
      }
    })
    return Array.from(sems).sort()
  })

  const filteredModules = computed(() => {
    if (!selectedSemester.value) return []
    return modules.value.filter(m => m.specialty_id === selectedSpecialty.value && m.semester === selectedSemester.value)
  })

  watch(selectedSpecialty, () => {
    selectedSemester.value = ''
    form.value.module_id = ''
  })

  watch(selectedSemester, () => {
    form.value.module_id = ''
  })
;

content = content.replace("  const isModalOpen = ref(false)", newVariables);

// Make sure computed and watch are imported
if (!content.includes("computed,")) {
  content = content.replace("import { ref, onMounted } from 'vue'", "import { ref, computed, watch, onMounted } from 'vue'");
}

fs.writeFileSync(file, content, 'utf8');
console.log('Update success');
