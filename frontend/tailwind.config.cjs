const defaultTheme = require('tailwindcss/defaultTheme');

const pastelPalette = {
  ink: '#0f172a',
  mist: '#f5f5f4',
  blush: '#f9e7dd',
  lavender: '#ece7f6',
  aqua: '#cde8e0',
  sage: '#e5f0e7',
  peach: '#ffe4d9',
  lilac: '#ddd6f3',
  accent: '#94a3b8',
  stone: {
    50: '#fafaf9',
    100: '#f5f5f4',
    200: '#e7e5e4',
    300: '#d6d3d1',
    400: '#a8a29e',
    500: '#78716c'
  }
};

module.exports = {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: pastelPalette,
      fontFamily: {
        display: ['"Space Grotesk"', ...defaultTheme.fontFamily.sans]
      },
      boxShadow: {
        glow: '0 25px 80px rgba(148, 179, 253, 0.25)'
      },
      backgroundImage: {
        'soft-radial': 'radial-gradient(circle at 15% 20%, rgba(148, 179, 253, 0.25), transparent 45%), radial-gradient(circle at 80% 0%, rgba(252, 232, 243, 0.65), transparent 40%), linear-gradient(135deg, #f5f5f4 0%, #e7e5e4 65%)'
      }
    }
  },
  plugins: []
};
