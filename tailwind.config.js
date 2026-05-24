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
            colors: {
                primary: {
                    DEFAULT: '#0EA5E9',
                    light: '#E0F2FE', // Background item aktif
                    hover: '#0284C7', // Hover state
                },
                bg: {
                    DEFAULT: '#F8FAFC', // Background aplikasi
                    sidebar: '#FFFFFF', // Background sidebar
                },
                text: {
                    DEFAULT: '#0F172A', // Teks utama (judul, label navigasi)
                    muted: '#64748B',   // Teks navigasi non-aktif
                    hover: '#334155',   // Teks navigasi non-aktif (hover)
                },
                danger: {
                    DEFAULT: '#EF4444', // Badge notifikasi / error
                },
                border: {
                    DEFAULT: '#E2E8F0', // Border separator
                }
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
