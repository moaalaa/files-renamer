import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            scrollbar: {
                thumb: {
                    rounded: 'sm', // Add rounded corners
                },
            },
        },
    },
    plugins: [
        require("daisyui"),
        require('tailwind-scrollbar')({ nocompatible: true }),
        require('tailwind-scrollbar-hide'),
    ],
};
