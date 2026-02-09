import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                ac: {
                    gold: 'var(--ac-gold)',
                    'gold-bright': 'var(--ac-gold-bright)',
                    green: 'var(--ac-green)',
                    'green-dark': 'var(--ac-green-dark)',
                    'green-light': 'var(--ac-green-light)',
                    blue: 'var(--ac-blue)',
                    border: 'var(--ac-border)',
                    text: 'var(--ac-text-primary)',
                    'text-secondary': 'var(--ac-text-secondary)',
                    cream: 'var(--ac-cream-light)',
                    'nav-brown': '#8b6914',
                    'nav-brown-shadow': '#5c4a1f',
                    'nav-cream': '#faf8ef',
                },
            },
            fontFamily: {
                sans: ['Nunito', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                'dance': 'dance 0.5s ease-in-out infinite',
            },
            keyframes: {
                dance: {
                    '0%, 100%': { transform: 'rotate(-3deg) translateY(0)' },
                    '25%': { transform: 'rotate(3deg) translateY(-5px)' },
                    '50%': { transform: 'rotate(-3deg) translateY(0)' },
                    '75%': { transform: 'rotate(3deg) translateY(-5px)' },
                },
            },
        },
    },

    plugins: [forms],
};
