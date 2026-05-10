import type { Config } from 'tailwindcss';
export default { content: ['./app/**/*.{ts,tsx}','./components/**/*.{ts,tsx}'], theme: { extend: { colors: { deepPurple:'#3E1B6D', hotPink:'#E91E8D', softPink:'#F8D7EA' } } }, plugins: [] } satisfies Config;
