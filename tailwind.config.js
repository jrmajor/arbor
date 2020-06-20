module.exports = {
    purge: false,
    theme: {
        screens: {
            xs: '420px',
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
        },
        fontFamily: {
            sans: [
                'Inter',
                'Zapf Dingbats',
                'Arial Unicode MS',
                'system-ui',
                '-apple-system',
                'BlinkMacSystemFont',
                '"Segoe UI"',
                'Roboto',
                '"Helvetica Neue"',
                'Arial',
                '"Noto Sans"',
                'sans-serif',
            ],
            serif: ['Georgia', 'Cambria', '"Times New Roman"', 'Times', 'serif'],
            mono: ['Menlo', 'Monaco', 'Consolas', '"Liberation Mono"', '"Courier New"', 'monospace'],
        },
        extend: {
            fontSize: {
                '7xl': '5rem',
                '8xl': '6rem',
                '9xl': '7rem',
                '10xl': '8rem',
            },
            spacing: {
                '80': '20rem',
                '96': '24rem',
                '112': '28rem',
                '128': '32rem',
            },
            strokeWidth: {
                '3': '3',
            },
        },
    },
    variants: {
        backgroundColor: ['responsive', 'hover', 'focus', 'active', 'group-hover', 'group-focus'],
        borderColor: ['responsive', 'hover', 'focus', 'active', 'focus-within'],
        borderRadius: ['responsive', 'important'],
        borderWidth: ['responsive', 'important'],
        textColor: ['responsive', 'hover', 'focus', 'active', 'group-hover', 'group-focus'],
        width: ['responsive', 'important'],
    },
    plugins: [
        require('tailwindcss-important')()
    ],
}
