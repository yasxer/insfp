// Date bounds for <input type="date|datetime-local"> min/max attributes.
// Built from local time: toISOString() is UTC and would be off by one around midnight.

const pad = (n) => String(n).padStart(2, '0')

const toDateString = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`

// "YYYY-MM-DD" for today
export const todayDate = () => toDateString(new Date())

// "YYYY-MM-DDTHH:mm" for now
export const nowDateTime = () => {
  const d = new Date()
  return `${toDateString(d)}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

// Latest birth date allowed for a trainee of at least `years` years old
export const maxBirthDate = (years = 15) => {
  const d = new Date()
  d.setFullYear(d.getFullYear() - years)
  return toDateString(d)
}

// Earliest birth date accepted by the backend (after:1950-01-01)
export const MIN_BIRTH_DATE = '1950-01-02'
