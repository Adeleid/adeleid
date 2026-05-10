# Dr Sara Clinic

## Fix for `next: command not found` / `ERESOLVE`
Use this clean setup sequence locally:

```bash
rm -rf node_modules package-lock.json
npm install
npm run db:push
npm run db:seed
npm run dev
```

Then open:
- http://localhost:3000
- http://localhost:3000/admin

## Why this fix works
- React/Next were pinned to incompatible prerelease combos before.
- Package versions are now aligned to stable compatible versions:
  - Next `14.2.33`
  - React `18.2.0`
  - React DOM `18.2.0`
  - Framer Motion `11.18.2`
