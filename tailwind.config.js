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
        },
    },

    safelist: [
        // Simpanan jenis colors
        'bg-blue-100', 'text-blue-800',      // pokok
        'bg-indigo-100', 'text-indigo-800',  // wajib
        'bg-green-100', 'text-green-800',    // sukarela
        'bg-gray-100', 'text-gray-700',      // default
    ],

    plugins: [forms],
};
