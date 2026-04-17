# Dunedin AI — Claude Instructions

## Project Overview
Kindle/web highlights manager. Users upload Kindle clipping files, search highlights, and add notes.

## Tech Stack
- **Backend**: Node.js + Express + TypeScript (`api/` directory)
- **Frontend**: Vue 3 + Vite + TypeScript (`app/` directory)
- **Database & Auth**: Supabase (PostgreSQL + Supabase Auth)

## Key Decisions
- Supabase handles both auth and database — no Firebase
- Auth is done client-side via Supabase SDK; the API validates JWTs in middleware
- Highlights are deduplicated via SHA-256 hash of (user_id + text)
- RLS policies enforce per-user data isolation at the database level
- File import currently supports only Kindle `my_clippings.txt` format (`kindle3` type), but the schema supports future formats

## Development Commands
```bash
# API
cd api && npm install && npm run dev    # Express server

# App
cd app && npm install && npm run dev    # Vite dev server
```

## Environment Variables
Both `api/` and `app/` need:
- `SUPABASE_URL` — Supabase project URL
- `SUPABASE_ANON_KEY` — Supabase anonymous/public key

API additionally needs:
- `SUPABASE_SERVICE_ROLE_KEY` — for server-side operations (file import, etc.)
- `PORT` — API port (default 3000)

## Code Conventions
- TypeScript strict mode in both projects
- Express routes in `api/src/routes/`, business logic in `api/src/services/`
- Vue views in `app/src/views/`, reusable components in `app/src/components/`
- Database migrations in `supabase/migrations/`

## Reference Files
- `readme.md` — project objective and high-level structure
- `blueprint.md` — database schema and feature requirements
- `plan.md` — detailed implementation plan with phases
