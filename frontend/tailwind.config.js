/** @type {import('tailwindcss').Config} */

// INSFP brand palette (same as the landing / login pages).
// `blue`, `indigo` and `primary` all point to the navy scale so every existing
// bg-blue-600 / text-indigo-600 / ring-indigo-500 in the app follows the brand.
const navy = {
  50: '#eef3f9',
  100: '#dce6f2',
  200: '#b9cce4',
  300: '#a9c0e0',
  400: '#7f9fcc',
  500: '#2f5a92',
  600: '#0f3460',
  700: '#0c2b52',
  800: '#0a2443',
  900: '#081c35',
  950: '#051226',
}

const teal = {
  50: '#ebf6f6',
  100: '#d2ecea',
  200: '#a6d9d6',
  300: '#72c1bd',
  400: '#3fa5a1',
  500: '#178c88',
  600: '#0e7c7b',
  700: '#0d6463',
  800: '#0e5150',
  900: '#0d4342',
  950: '#052827',
}

// Cool grey that sits well next to the navy (light and dark mode)
const gray = {
  50: '#f6f8fb',
  100: '#eef1f5',
  200: '#e1e6ed',
  300: '#cfd6df',
  400: '#9aa5b4',
  500: '#6b7686',
  600: '#566173',
  700: '#3c4757',
  800: '#1c2636',
  900: '#111a27',
  950: '#0a111c',
}

export default {
  darkMode: 'class',
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        navy,
        teal,
        blue: navy,
        indigo: navy,
        primary: navy,
        // decorative purple accents follow the brand teal
        purple: teal,
        violet: teal,
        gray,
        gold: {
          50: '#fbf6e9',
          100: '#f6ecd2',
          400: '#dcb24a',
          500: '#c9971c',
          600: '#a97c12',
        },
      },
      fontFamily: {
        sans: ['"IBM Plex Sans"', '"Segoe UI"', 'Arial', 'sans-serif'],
        serif: ['"Source Serif 4"', 'Georgia', 'serif'],
        arabic: ['"Noto Naskh Arabic"', 'serif'],
        mono: ['ui-monospace', '"Cascadia Code"', 'Consolas', 'monospace'],
      },
      boxShadow: {
        sm: '0 1px 2px rgba(15, 52, 96, 0.06)',
        DEFAULT: '0 1px 3px rgba(15, 52, 96, 0.08), 0 1px 2px rgba(15, 52, 96, 0.04)',
        md: '0 4px 12px rgba(15, 52, 96, 0.08)',
        lg: '0 10px 24px rgba(15, 52, 96, 0.10)',
        xl: '0 20px 40px rgba(15, 52, 96, 0.14)',
      },
      borderRadius: {
        sm: '4px',
        DEFAULT: '6px',
        md: '6px',
        lg: '8px',
        xl: '12px',
        '2xl': '16px',
      },
    },
  },
  plugins: [],
}
