import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// `command` is 'serve' during dev and 'build' for the production bundle.
export default defineConfig(({ command }) => ({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  // Strip all console.* and debugger statements from the production build so no
  // tokens / user data leak to the browser console. They stay available in dev.
  esbuild: command === 'build' ? { drop: ['console', 'debugger'] } : {},
  server: {
    port: 5173,
    cors: {
      origin: 'http://localhost:8000',
    },
  },
}))
