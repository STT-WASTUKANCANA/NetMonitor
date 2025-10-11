import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: 'var(--primary)',
                    hover: 'var(--primary-hover)',
                },
                bg: {
                    DEFAULT: 'var(--bg)',
                    secondary: 'var(--bg-secondary)',
                },
                text: {
                    DEFAULT: 'var(--text)',
                    secondary: 'var(--text-secondary)',
                },
            },
        },
    },

    plugins: [forms],
};
