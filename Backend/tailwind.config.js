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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
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
