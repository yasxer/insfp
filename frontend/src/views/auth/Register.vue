<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/api/axios'
import { registerSchema } from '@/validations/schemas'
import AuthLayout from '@/components/layout/AuthLayout.vue'

const router = useRouter()

const emptyForm = () => ({
  session_id: '',
  registration_number: '',
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  specialty_id: '',
  study_mode: '',
  password: '',
  password_confirmation: ''
})
const form = ref(emptyForm())

// Two steps: 1 = registration number + specialty, 2 = personal info + password
const step = ref(1)
const STEP_ONE_FIELDS = ['registration_number', 'session_id', 'study_mode', 'specialty_id']

const sessions = ref([])
const loading = ref(false)
const error = ref(null)
const successMessage = ref(null)
const fieldErrors = ref({})
const showPassword = ref(false)

// Registration number lookup state
const lookupLoading = ref(false)
const lookupError = ref(null)
const lookupData = ref(null) // { session, specialty, study_modes }
let lookupTimer = null

// Frontend study mode value -> backend study type
const reverseTypeMapping = {
  initial: 'presential',
  alternance: 'apprentissage',
  continue: 'cours_soir'
}

const fetchSessions = async () => {
  try {
    const response = await apiClient.get('/api/sessions')
    sessions.value = response.data.data || []
  } catch (err) {
    console.error('Failed to fetch sessions:', err)
    error.value = 'Impossible de charger les sessions. Actualisez la page.'
  }
}

const selectedSession = computed(() => {
  if (!form.value.session_id) return null
  return sessions.value.find(s => s.id === form.value.session_id)
})

const sessionLabel = computed(() =>
  selectedSession.value?.name || lookupData.value?.session?.name || ''
)

const studyModeLabel = computed(() => {
  if (!form.value.study_mode) return ''
  const match = (lookupData.value?.study_modes || []).find(m => m.value === form.value.study_mode)
  return match?.label || ''
})

const availableSpecialties = computed(() => {
  if (!selectedSession.value || !form.value.study_mode) return []

  const backendType = reverseTypeMapping[form.value.study_mode]
  const group = selectedSession.value.specialties_by_type.find(g => g.type === backendType)

  return group ? group.specialties : []
})

const canContinue = computed(() => !!lookupData.value && !!form.value.specialty_id && !lookupLoading.value)

// Reset everything that depends on the registration number
const clearLookup = () => {
  lookupData.value = null
  lookupError.value = null
  form.value.session_id = ''
  form.value.study_mode = ''
  form.value.specialty_id = ''
}

// Look up the registration number and auto-fill session + study mode
const lookupRegistration = async (number) => {
  lookupLoading.value = true
  lookupError.value = null
  try {
    const response = await apiClient.post('/api/lookup-registration', {
      registration_number: number
    })
    const data = response.data.data
    lookupData.value = data

    // Auto-fill session + study mode (read-only for the student)
    form.value.session_id = data.session?.id || ''
    form.value.study_mode = data.study_modes?.[0]?.value || ''
    form.value.specialty_id = '' // student chooses the specialty
  } catch (err) {
    clearLookup()
    lookupError.value = err.response?.data?.message || 'Numéro d’inscription invalide.'
  } finally {
    lookupLoading.value = false
  }
}

// Debounced lookup whenever the registration number changes
watch(() => form.value.registration_number, (number) => {
  clearTimeout(lookupTimer)
  const trimmed = (number || '').trim()
  if (!trimmed) {
    clearLookup()
    return
  }
  lookupTimer = setTimeout(() => lookupRegistration(trimmed), 500)
})

// Clear specialty if study mode changes (e.g. after a new lookup)
watch(() => form.value.study_mode, () => {
  form.value.specialty_id = ''
})

const goToStep2 = () => {
  if (!canContinue.value) return
  error.value = null
  step.value = 2
}

// Send the student back to the step that holds the first invalid field
const showErrors = (errors) => {
  fieldErrors.value = errors
  error.value = 'Veuillez corriger les champs signalés.'
  if (Object.keys(errors).some(field => STEP_ONE_FIELDS.includes(field))) step.value = 1
}

const handleRegister = async () => {
  error.value = null
  fieldErrors.value = {}

  try {
    await registerSchema.validate(form.value, { abortEarly: false })
  } catch (validationError) {
    const errors = {}
    for (const issue of validationError.inner) {
      if (!errors[issue.path]) errors[issue.path] = [issue.message]
    }
    showErrors(errors)
    return
  }

  loading.value = true
  try {
    await apiClient.post('/api/register', form.value)
    successMessage.value = 'Votre compte a été créé. Il sera activé après validation de votre dossier par l’administration.'
    form.value = emptyForm()
    setTimeout(() => router.push('/login'), 4000)
  } catch (err) {
    if (err.response?.status === 422) {
      showErrors(err.response.data.errors || {})
    } else if (err.response?.status === 429) {
      error.value = 'Trop de tentatives d’inscription. Réessayez dans une heure.'
    } else {
      error.value = err.response?.data?.message || 'L’inscription a échoué. Veuillez réessayer.'
    }
  } finally {
    loading.value = false
  }
}

onMounted(fetchSessions)
</script>

<template>
  <AuthLayout panel-title="Créez votre compte stagiaire en deux étapes" width="440px">
    <!-- Success -->
    <div v-if="successMessage" class="reg-success">
      <span class="reg-success-icon">
        <svg class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
      </span>
      <h1 class="auth-title">Inscription enregistrée</h1>
      <p class="auth-subtitle">{{ successMessage }}</p>
      <router-link to="/login" class="auth-btn">Aller à la connexion</router-link>
      <p class="auth-help">Redirection automatique…</p>
    </div>

    <template v-else>
      <h1 class="auth-title">Inscription</h1>
      <p class="auth-subtitle">Munissez-vous du numéro d’inscription remis par l’administration.</p>

      <!-- Steps -->
      <ol class="reg-steps" aria-label="Étapes">
        <li :class="{ active: step === 1, done: step > 1 }">
          <span class="reg-step-num">
            <svg v-if="step > 1" class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
            <template v-else>1</template>
          </span>
          Formation
        </li>
        <li class="reg-step-line" aria-hidden="true"></li>
        <li :class="{ active: step === 2 }">
          <span class="reg-step-num">2</span>
          Compte
        </li>
      </ol>

      <form class="auth-form" novalidate @submit.prevent="step === 1 ? goToStep2() : handleRegister()">
        <!-- STEP 1 -->
        <template v-if="step === 1">
          <div class="auth-field">
            <label for="registration_number" class="auth-label">Numéro d’inscription</label>
            <div class="auth-control">
              <svg class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10" /></svg>
              <input
                id="registration_number"
                v-model.trim="form.registration_number"
                type="text"
                autocomplete="off"
                autofocus
                :class="['auth-input', 'reg-mono', { 'is-invalid': fieldErrors.registration_number || lookupError, 'is-valid': lookupData }]"
                placeholder="0001125P1647"
              />
              <span class="auth-suffix reg-status" aria-hidden="true">
                <span v-if="lookupLoading" class="auth-spinner reg-spinner"></span>
                <svg v-else-if="lookupData" class="auth-icon reg-ok" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5" /></svg>
              </span>
            </div>
            <p v-if="lookupError" class="auth-error">{{ lookupError }}</p>
            <p v-else-if="fieldErrors.registration_number" class="auth-error">{{ fieldErrors.registration_number[0] }}</p>
            <p v-else-if="!lookupData" class="auth-help">La session et le mode de formation sont remplis automatiquement.</p>
          </div>

          <Transition name="reg-fade">
            <div v-if="lookupData" class="reg-chips">
              <div class="reg-chip">
                <span>Session</span>
                <strong>{{ sessionLabel || '—' }}</strong>
              </div>
              <div class="reg-chip">
                <span>Mode de formation</span>
                <strong>{{ studyModeLabel || '—' }}</strong>
              </div>
            </div>
          </Transition>

          <div class="auth-field">
            <label for="specialty" class="auth-label">Spécialité</label>
            <select
              id="specialty"
              v-model="form.specialty_id"
              :disabled="!form.study_mode"
              :class="['auth-input', 'no-icon', { 'is-invalid': fieldErrors.specialty_id }]"
            >
              <option value="" disabled>{{ form.study_mode ? 'Choisir une spécialité' : 'Saisissez d’abord votre numéro' }}</option>
              <option v-for="specialty in availableSpecialties" :key="specialty.specialty_id" :value="specialty.specialty_id">
                {{ specialty.specialty_name }} ({{ specialty.specialty_code }})
              </option>
            </select>
            <p v-if="fieldErrors.specialty_id" class="auth-error">{{ fieldErrors.specialty_id[0] }}</p>
            <p v-else-if="fieldErrors.session_id || fieldErrors.study_mode" class="auth-error">{{ (fieldErrors.session_id || fieldErrors.study_mode)[0] }}</p>
          </div>

          <button type="submit" class="auth-btn" :disabled="!canContinue">
            Continuer
            <svg class="auth-icon reg-arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
          </button>
        </template>

        <!-- STEP 2 -->
        <template v-else>
          <div class="auth-row">
            <div class="auth-field">
              <label for="first_name" class="auth-label">Prénom</label>
              <input id="first_name" v-model.trim="form.first_name" type="text" autocomplete="given-name" autofocus
                :class="['auth-input', 'no-icon', { 'is-invalid': fieldErrors.first_name }]" />
              <p v-if="fieldErrors.first_name" class="auth-error">{{ fieldErrors.first_name[0] }}</p>
            </div>
            <div class="auth-field">
              <label for="last_name" class="auth-label">Nom</label>
              <input id="last_name" v-model.trim="form.last_name" type="text" autocomplete="family-name"
                :class="['auth-input', 'no-icon', { 'is-invalid': fieldErrors.last_name }]" />
              <p v-if="fieldErrors.last_name" class="auth-error">{{ fieldErrors.last_name[0] }}</p>
            </div>
          </div>

          <div class="auth-row">
            <div class="auth-field">
              <label for="email" class="auth-label">Email</label>
              <input id="email" v-model.trim="form.email" type="email" autocomplete="email" placeholder="nom@exemple.com"
                :class="['auth-input', 'no-icon', { 'is-invalid': fieldErrors.email }]" />
              <p v-if="fieldErrors.email" class="auth-error">{{ fieldErrors.email[0] }}</p>
            </div>
            <div class="auth-field">
              <label for="phone" class="auth-label">Téléphone <span class="auth-label-hint">(facultatif)</span></label>
              <input id="phone" v-model.trim="form.phone" type="tel" autocomplete="tel" placeholder="0612345678"
                :class="['auth-input', 'no-icon', { 'is-invalid': fieldErrors.phone }]" />
              <p v-if="fieldErrors.phone" class="auth-error">{{ fieldErrors.phone[0] }}</p>
            </div>
          </div>

          <div class="auth-row">
            <div class="auth-field">
              <label for="password" class="auth-label">Mot de passe</label>
              <div class="auth-control">
                <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password"
                  :class="['auth-input', 'no-icon', { 'is-invalid': fieldErrors.password, 'is-valid': form.password.length >= 8 }]" />
                <button type="button" class="auth-suffix" :aria-label="showPassword ? 'Masquer' : 'Afficher'" @click="showPassword = !showPassword">
                  <svg v-if="!showPassword" class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12zM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" /></svg>
                  <svg v-else class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6C3.8 8.4 2 12 2 12s3.5 7 10 7c1.6 0 3-.4 4.3-1M9.9 9.9a3 3 0 0 0 4.2 4.2" /></svg>
                </button>
              </div>
              <p v-if="fieldErrors.password" class="auth-error">{{ fieldErrors.password[0] }}</p>
              <p v-else class="auth-help" :class="{ 'reg-good': form.password.length >= 8 }">{{ Math.min(form.password.length, 8) }}/8 caractères minimum</p>
            </div>
            <div class="auth-field">
              <label for="password_confirmation" class="auth-label">Confirmation</label>
              <input id="password_confirmation" v-model="form.password_confirmation" :type="showPassword ? 'text' : 'password'" autocomplete="new-password"
                :class="['auth-input', 'no-icon', { 'is-invalid': fieldErrors.password_confirmation || (form.password_confirmation && form.password_confirmation !== form.password), 'is-valid': form.password_confirmation && form.password_confirmation === form.password }]" />
              <p v-if="fieldErrors.password_confirmation" class="auth-error">{{ fieldErrors.password_confirmation[0] }}</p>
              <p v-else-if="form.password_confirmation && form.password_confirmation !== form.password" class="auth-error">Les mots de passe ne correspondent pas</p>
            </div>
          </div>

          <div class="reg-actions">
            <button type="button" class="auth-btn auth-btn-ghost" @click="step = 1">
              <svg class="auth-icon reg-arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7" /></svg>
              Retour
            </button>
            <button type="submit" class="auth-btn" :disabled="loading">
              <span v-if="loading" class="auth-spinner" aria-hidden="true"></span>
              {{ loading ? 'Création…' : 'Créer mon compte' }}
            </button>
          </div>
        </template>

        <div v-if="error" class="auth-alert auth-alert-error" role="alert">
          <svg class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z" /></svg>
          <span>{{ error }}</span>
        </div>
      </form>

      <p class="auth-switch">
        Déjà inscrit ?
        <router-link to="/login">Se connecter</router-link>
      </p>
    </template>
  </AuthLayout>
</template>

<style scoped>
.reg-steps { display: flex; align-items: center; gap: 10px; list-style: none; margin: 0 0 22px; padding: 0; font-size: 14px; font-weight: 500; color: var(--muted); }
.reg-steps li { display: flex; align-items: center; gap: 8px; }
.reg-steps li.active, .reg-steps li.done { color: var(--navy); }
.reg-step-num { display: grid; place-items: center; width: 26px; height: 26px; border-radius: 50%; border: 2px solid var(--line); font-size: 13px; font-weight: 700; transition: all .25s; }
.reg-step-num .auth-icon { width: 14px; height: 14px; stroke-width: 3; }
.reg-steps li.active .reg-step-num { border-color: var(--navy); background: var(--navy); color: #fff; }
.reg-steps li.done .reg-step-num { border-color: var(--teal); background: var(--teal); color: #fff; }
.reg-step-line { flex: 1; height: 2px; background: var(--line); border-radius: 2px; }

.reg-mono { font-family: ui-monospace, 'Cascadia Code', Consolas, monospace; letter-spacing: .04em; }
.reg-status { pointer-events: none; }
.reg-spinner { border-color: rgba(15, 52, 96, .2); border-top-color: var(--navy); }
.reg-ok { color: var(--success); stroke-width: 3; }

.reg-chips { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.reg-chip { display: grid; gap: 2px; padding: 10px 12px; border-radius: 8px; background: #eef6f6; border: 1px solid #cfe6e5; }
.reg-chip span { font-size: 12px; color: var(--muted); }
.reg-chip strong { font-size: 14px; color: var(--navy); font-weight: 600; }

.reg-arrow { width: 18px; height: 18px; }
.reg-good { color: var(--success); }
.reg-actions { display: grid; grid-template-columns: auto 1fr; gap: 10px; }

.reg-success { display: grid; justify-items: center; text-align: center; gap: 4px; }
.reg-success-icon { display: grid; place-items: center; width: 56px; height: 56px; margin-bottom: 10px; border-radius: 50%; background: #ecf7f1; color: var(--success); animation: reg-pop .4s cubic-bezier(.2, .8, .2, 1.3); }
.reg-success-icon .auth-icon { width: 28px; height: 28px; stroke-width: 3; }
.reg-success .auth-btn { margin-top: 8px; text-decoration: none; }

.reg-fade-enter-active, .reg-fade-leave-active { transition: opacity .25s, transform .25s; }
.reg-fade-enter-from, .reg-fade-leave-to { opacity: 0; transform: translateY(-6px); }

@keyframes reg-pop { from { transform: scale(.6); opacity: 0; } to { transform: none; opacity: 1; } }

@media (max-width: 520px) {
  .reg-chips { grid-template-columns: 1fr; }
}
</style>
