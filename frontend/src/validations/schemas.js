import * as yup from 'yup'

const phoneRegex = /^0[5-7][0-9]{8}$/

export const loginSchema = yup.object({
  registration_number: yup.string().required('Email or registration number is required'),
  password: yup.string().required('Password is required')
})

export const registerSchema = yup.object({
  session_id: yup.string().required('Session is required'),
  registration_number: yup.string().required('Registration number is required'),
  first_name: yup.string().required('First name is required'),
  last_name: yup.string().required('Last name is required'),
  email: yup.string().email('Please enter a valid email address').required('Email is required'),
  phone: yup.string().matches(phoneRegex, 'Phone must be a valid 10-digit number (e.g. 0612345678)').nullable().notRequired(),
  specialty_id: yup.string().required('Specialty is required'),
  study_mode: yup.string().required('Study mode is required'),
  password: yup.string().min(8, 'Password must be at least 8 characters').required('Password is required'),
  password_confirmation: yup.string()
    .oneOf([yup.ref('password')], 'Passwords do not match')
    .required('Please confirm your password')
})

export const studentSchema = yup.object({
  first_name: yup.string().required('First name is required'),
  last_name: yup.string().required('Last name is required'),
  email: yup.string().email('Please enter a valid email address').required('Email is required'),
  phone: yup.string().matches(phoneRegex, 'Phone must be a valid 10-digit number (e.g. 0612345678)').nullable().notRequired(),
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
