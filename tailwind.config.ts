import type { Config } from "tailwindcss";
import typography from "@tailwindcss/typography";

const config: Config = {
  content: [
    "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        // Navy palette
        navy: {
          100: '#e8eef4',
          600: '#2a4a6e',
          700: '#1e3a5f',
          800: '#132337',
          900: '#0c1929',
        },
        // Orange accent
        orange: {
          100: '#fff0e6',
          500: '#e85d04',
          600: '#dc5503',
        },
        // Green for success states
        green: {
          100: '#d1fae5',
          500: '#059669',
        },
        // Cream backgrounds
        cream: {
          DEFAULT: '#faf9f7',
          dark: '#f5f3ef',
        },
        // Gray for body text
        gray: {
          400: '#9ca3af',
          600: '#4b5563',
        },
      },
      fontFamily: {
        heading: ['Poppins', 'sans-serif'],
        body: ['Plus Jakarta Sans', 'sans-serif'],
      },
      fontSize: {
        // Custom type scale
        'hero': ['clamp(2.5rem, 5vw, 3.5rem)', { lineHeight: '1.15', fontWeight: '600' }],
        'section': ['clamp(2rem, 4vw, 2.75rem)', { lineHeight: '1.2', fontWeight: '600' }],
        'card-title': ['1.2rem', { lineHeight: '1.4', fontWeight: '700' }],
        'body-lg': ['1.15rem', { lineHeight: '1.7', fontWeight: '400' }],
        'eyebrow': ['0.85rem', { lineHeight: '1.5', fontWeight: '700', letterSpacing: '0.1em' }],
      },
      spacing: {
        '18': '4.5rem',
        '22': '5.5rem',
        '30': '7.5rem',
      },
      borderRadius: {
        'card': '16px',
        'button': '8px',
        'icon': '12px',
        'hero': '20px',
        'cta': '24px',
      },
      boxShadow: {
        'card': '0 20px 40px rgba(0, 0, 0, 0.08)',
        'card-hover': '0 20px 40px rgba(0, 0, 0, 0.08)',
        'button': '0 4px 14px rgba(232, 93, 4, 0.25)',
        'button-hover': '0 6px 20px rgba(232, 93, 4, 0.3)',
        'hero': '0 25px 50px -12px rgba(0, 0, 0, 0.15)',
        'floating': '0 10px 40px rgba(0, 0, 0, 0.1)',
      },
      animation: {
        'fade-in': 'fadeIn 0.6s ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      },
    },
  },
  plugins: [typography],
};
export default config;
