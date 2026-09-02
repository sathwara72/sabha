import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                outfit: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                background: 'var(--background)',
                foreground: 'var(--foreground)',
                muted: 'var(--muted)',
                'muted-foreground': 'var(--muted-foreground)',
                primary: {
                    DEFAULT: 'var(--primary)',
                    dark: 'var(--primary-dark)',
                    foreground: 'var(--primary-foreground)',
                    soft: 'var(--primary-soft)',
                },
                accent: {
                    DEFAULT: 'var(--accent)',
                    dark: 'var(--accent-dark)',
                    foreground: 'var(--accent-foreground)',
                    soft: 'var(--accent-soft)',
                    coral: 'var(--accent-coral)',
                },
                card: {
                    DEFAULT: 'var(--card)',
                    foreground: 'var(--card-foreground)',
                },
                surface: 'var(--surface)',
                border: 'var(--border)',
                input: 'var(--input)',
                ring: 'var(--ring)',
            },
            borderRadius: {
                lg: 'var(--radius-lg)',
                md: 'var(--radius-md)',
                sm: 'var(--radius-sm)',
                xl: '0.875rem',
                '2xl': '6px',
                '3xl': '6px',
            },
            boxShadow: {
                // Every shadow-* utility tinted toward the brand navy instead
                // of flat black, and layered (tight contact + soft ambient)
                // for a softer, more elevated "premium" depth.
                '2xs': '0 1px 1px rgb(var(--shadow-color) / 0.03)',
                xs: '0 1px 2px rgb(var(--shadow-color) / 0.05)',
                sm: '0 1px 2px rgb(var(--shadow-color) / 0.04), 0 1px 1px rgb(var(--shadow-color) / 0.03)',
                DEFAULT: '0 1px 3px rgb(var(--shadow-color) / 0.06), 0 4px 8px -4px rgb(var(--shadow-color) / 0.06)',
                md: '0 2px 4px rgb(var(--shadow-color) / 0.05), 0 8px 16px -6px rgb(var(--shadow-color) / 0.10)',
                lg: '0 4px 8px rgb(var(--shadow-color) / 0.05), 0 16px 28px -10px rgb(var(--shadow-color) / 0.14)',
                xl: '0 8px 16px rgb(var(--shadow-color) / 0.06), 0 28px 44px -14px rgb(var(--shadow-color) / 0.18)',
                '2xl': '0 32px 64px -16px rgb(var(--shadow-color) / 0.28)',
            },
        },
    },
    plugins: [],
};
