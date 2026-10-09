import * as yup from 'yup'
import { i18n } from '@/i18n'

const phoneRegex = /^0[5-7][0-9]{8}$/

// Messages are functions so they follow the language chosen at validation time
const m = (key) => () => i18n.global.t(`validation.${key}`)

export const loginSchema = yup.object({
  registration_number: yup.string().required(m('identifierRequired')),
  password: yup.string().required(m('passwordRequired'))
})

export const registerSchema = yup.object({
  session_id: yup.string().required(m('sessionMissing')),
  registration_number: yup.string().required(m('regNumberRequired')),
  first_name: yup.string().required(m('firstNameRequired')),
  last_name: yup.string().required(m('lastNameRequired')),
  email: yup.string().email(m('emailInvalid')).required(m('emailRequired')),
  phone: yup.string().matches(phoneRegex, { message: m('phoneInvalid'), excludeEmptyString: true }).nullable().notRequired(),
  specialty_id: yup.string().required(m('specialtyRequired')),
  study_mode: yup.string().required(m('modeMissing')),
  password: yup.string().min(8, m('passwordMin')).required(m('passwordRequired')),
  password_confirmation: yup.string()
    .oneOf([yup.ref('password')], m('passwordsMismatch'))
    .required(m('confirmRequired'))
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
