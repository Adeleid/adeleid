# Dr Sara Clinic

## Quick Preview (Static)
If package install is blocked, you can still preview the design quickly:

```bash
python3 -m http.server 4173
```

Then open:
- http://localhost:4173/dist/index.html

## Full Next.js App
After dependency access is available:

```bash
npm install
npm run db:push
npm run db:seed
npm run dev
```

Then open:
- http://localhost:3000
- http://localhost:3000/admin
