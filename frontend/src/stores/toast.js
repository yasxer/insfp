import { defineStore } from 'pinia'

let nextId = 1

export const useToastStore = defineStore('toast', {
  state: () => ({
    toasts: []
  }),
  actions: {
    push(message, type = 'info', duration = 4000) {
      const id = nextId++
      this.toasts.push({ id, message, type })
      if (duration > 0) {
        setTimeout(() => this.remove(id), duration)
      }
      return id
    },
    success(message, duration = 4000) {
      return this.push(message, 'success', duration)
    },
    error(message, duration = 5000) {
      return this.push(message, 'error', duration)
    },
    warning(message, duration = 4500) {
      return this.push(message, 'warning', duration)
    },
    info(message, duration = 4000) {
      return this.push(message, 'info', duration)
    },
    remove(id) {
      this.toasts = this.toasts.filter(t => t.id !== id)
    }
  }
})
