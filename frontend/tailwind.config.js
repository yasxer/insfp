/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        wf: {
          blue: '#146ef5',
          'blue-400': '#3b89ff',
          'blue-300': '#006acc',
          'button-hover': '#0055d4',
          black: '#080808',
        },
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#3b89ff',
          500: '#146ef5',
          600: '#146ef5',
          700: '#0055d4',
          800: '#006acc',
          900: '#1e3a8a',
        },
        indigo: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#3b89ff',
          500: '#146ef5',
          600: '#146ef5',
          700: '#0055d4',
          800: '#006acc',
          900: '#1e3a8a',
        },
        gray: {
          50: '#f9f9f9',
          100: '#f3f3f3',
          200: '#e5e5e5',
          300: '#d8d8d8', // Border Gray
          400: '#ababab', // Muted text
          500: '#898989', // Border Hover
          600: '#5a5a5a', // Mid Gray (link text/muted)
          700: '#363636', // Mid text
          800: '#222222', // Dark secondary text
          900: '#080808', // Primary text
        },
        secondary: {
          purple: '#7a3dff',
          pink: '#ed52cb',
          green: '#00d722',
          orange: '#ff6b00',
          yellow: '#ffae13',
          red: '#ee1d36',
        }
      },
      fontFamily: {
        sans: ['"WF Visual Sans Variable"', '"Plus Jakarta Sans"', 'Arial', 'sans-serif'],
        mono: ['Inconsolata', 'monospace'],
      },
      boxShadow: {
        'sm': '0px 3px 7px rgba(0,0,0,0.09)',
        DEFAULT: '0px 13px 13px rgba(0,0,0,0.08), 0px 3px 7px rgba(0,0,0,0.09)',
        'md': '0px 30px 18px rgba(0,0,0,0.04), 0px 13px 13px rgba(0,0,0,0.08), 0px 3px 7px rgba(0,0,0,0.09)',
        'lg': '0px 54px 22px rgba(0,0,0,0.01), 0px 30px 18px rgba(0,0,0,0.04), 0px 13px 13px rgba(0,0,0,0.08), 0px 3px 7px rgba(0,0,0,0.09)',
        'xl': '0px 84px 24px rgba(0,0,0,0), 0px 54px 22px rgba(0,0,0,0.01), 0px 30px 18px rgba(0,0,0,0.04), 0px 13px 13px rgba(0,0,0,0.08), 0px 3px 7px rgba(0,0,0,0.09)',
      },
      borderRadius: {
        'none': '0',
        'sm': '2px',
        DEFAULT: '4px',
        'md': '4px',
        'lg': '8px',
        'xl': '8px',
        'full': '50%',
      },
      letterSpacing: {
        'tightest': '-0.8px',
        'tighter': '-0.16px',
        'widest': '1.5px', // For uppercase labels
      }
    },
  },
  plugins: [],
}
