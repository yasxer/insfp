import * as yup from 'yup'

const phoneRegex = /^0[5-7][0-9]{8}$/

export const loginSchema = yup.object({
  registration_number: yup.string().required('Email ou numéro d’inscription requis'),
  password: yup.string().required('Mot de passe requis')
})

export const registerSchema = yup.object({
  session_id: yup.string().required('Session introuvable : vérifiez votre numéro d’inscription'),
  registration_number: yup.string().required('Numéro d’inscription requis'),
  first_name: yup.string().required('Prénom requis'),
  last_name: yup.string().required('Nom requis'),
  email: yup.string().email('Adresse email invalide').required('Email requis'),
  phone: yup.string().matches(phoneRegex, { message: 'Numéro invalide (ex : 0612345678)', excludeEmptyString: true }).nullable().notRequired(),
  specialty_id: yup.string().required('Choisissez une spécialité'),
  study_mode: yup.string().required('Mode d’étude introuvable : vérifiez votre numéro d’inscription'),
  password: yup.string().min(8, 'Au moins 8 caractères').required('Mot de passe requis'),
  password_confirmation: yup.string()
    .oneOf([yup.ref('password')], 'Les mots de passe ne correspondent pas')
    .required('Confirmez le mot de passe')
})

export const studentSchema = yup.object({
  first_name: yup.string().required('First name is required'),
  last_name: yup.string().required('Last name is required'),
  email: yup.string().email('Please enter a valid email address').required('Email is required'),
  phone: yup.string().matches(phoneRegex, { message: 'Phone must be a valid 10-digit number (e.g. 0612345678)', excludeEmptyString: true }).nullable().notRequired(),
  date_of_birth: yup.string().required('Date of birth is required'),
  address: yup.string().required('Address is required'),
  registration_number: yup.string().required('Registration number is required'),
  specialty_id: yup.string().required('Specialty is required'),
  study_mode: yup.string().required('Study mode is required'),
  current_semester: yup.number().min(1).max(6).required('Current semester is required'),
  years_enrolled: yup.number().min(1, 'Years enrolled must be at least 1').required('Years enrolled is required')
})

export const specialtySchema = yup.object({
  name: yup.string().required('Name is required'),
  code: yup.string().required('Code is required'),
  description: yup.string().nullable().notRequired(),
  duration_years: yup.number().min(0.5, 'Duration must be at least 0.5 years').required('Duration is required')
})
