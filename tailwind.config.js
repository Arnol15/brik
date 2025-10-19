/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./**/*.php",     // So it scans your PHP views
        "./**/*.html",
        "./**/*.js"
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/line-clamp'),
    ],
}
