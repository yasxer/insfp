<script setup>
import { ref } from 'vue'
import { useForm } from 'vee-validate'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { loginSchema } from '@/validations/schemas'
import AuthLayout from '@/components/layout/AuthLayout.vue'

const router = useRouter()
const authStore = useAuthStore()

const { defineField, errors, handleSubmit } = useForm({
  validationSchema: loginSchema,
  initialValues: { registration_number: '', password: '' }
})

const [registration_number, registration_numberAttrs] = defineField('registration_number')
const [password, passwordAttrs] = defineField('password')

const rememberMe = ref(false)
const showPassword = ref(false)
const loading = ref(false)

const dashboards = {
  student: '/student/dashboard',
  teacher: '/teacher/dashboard',
  administration: '/admin/dashboard',
}

const handleLogin = handleSubmit(async (credentials) => {
  loading.value = true
  try {
    const result = await authStore.login(credentials, rememberMe.value)
    if (!result.success) return

    if (!result.profileComplete) {
      router.push('/complete-profile')
    } else {
      router.push(dashboards[authStore.userRole] || '/dashboard')
    }
  } catch (error) {
    console.error('Login error:', error)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <AuthLayout>
    <h1 class="auth-title">Connexion</h1>
    <p class="auth-subtitle">Accédez à votre espace stagiaire, formateur ou administration.</p>

    <form class="auth-form" novalidate @submit.prevent="handleLogin">
      <div class="auth-field">
        <label for="registration_number" class="auth-label">Email ou numéro d’inscription</label>
        <div class="auth-control">
          <svg class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" /></svg>
          <input
            id="registration_number"
            v-model="registration_number"
            v-bind="registration_numberAttrs"
            type="text"
            autocomplete="username"
            autofocus
            :class="['auth-input', { 'is-invalid': errors.registration_number }]"
            placeholder="nom@insfp.dz ou 0001125P1647"
          />
        </div>
        <p v-if="errors.registration_number" class="auth-error">{{ errors.registration_number }}</p>
      </div>

      <div class="auth-field">
        <label for="password" class="auth-label">Mot de passe</label>
        <div class="auth-control">
          <svg class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 11V7a5 5 0 0 1 10 0v4M5 11h14v10H5z" /></svg>
          <input
            id="password"
            v-model="password"
            v-bind="passwordAttrs"
            :type="showPassword ? 'text' : 'password'"
            autocomplete="current-password"
            :class="['auth-input', { 'is-invalid': errors.password }]"
            placeholder="••••••••"
          />
          <button type="button" class="auth-suffix" :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'" @click="showPassword = !showPassword">
            <svg v-if="!showPassword" class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12zM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" /></svg>
            <svg v-else class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6C3.8 8.4 2 12 2 12s3.5 7 10 7c1.6 0 3-.4 4.3-1M9.9 9.9a3 3 0 0 0 4.2 4.2" /></svg>
          </button>
        </div>
        <p v-if="errors.password" class="auth-error">{{ errors.password }}</p>
      </div>

      <div class="auth-inline">
        <label class="auth-check">
          <input v-model="rememberMe" type="checkbox" />
          Se souvenir de moi
        </label>
        <span class="auth-muted" title="L’administration peut réinitialiser votre mot de passe">Mot de passe oublié ? Contactez l’administration</span>
      </div>

      <div v-if="authStore.error" class="auth-alert auth-alert-error" role="alert">
        <svg class="auth-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z" /></svg>
        <span>{{ authStore.error }}</span>
      </div>

      <button type="submit" class="auth-btn" :disabled="loading">
        <span v-if="loading" class="auth-spinner" aria-hidden="true"></span>
        {{ loading ? 'Connexion…' : 'Se connecter' }}
      </button>
    </form>

    <p class="auth-switch">
      Nouveau stagiaire ?
      <router-link to="/register">Créer un compte</router-link>
    </p>
  </AuthLayout>
</template>
