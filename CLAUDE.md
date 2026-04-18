# Dunedin AI — Claude Instructions

## Mandatory Rule
**If any questions are raised, ask me before acting.** This applies to every prompt — if there is ambiguity, missing context, or a decision to be made, pause and ask rather than assume.

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

Supabase rolled out a new key format (`sb_publishable_...` / `sb_secret_...`)
that replaces the legacy `anon` / `service_role` JWT keys. New projects use
the new format; both still work during the transition.

### `app/` (Vite — `VITE_` prefix required to expose to the client)
- `VITE_SUPABASE_URL` — Supabase project URL
- `VITE_SUPABASE_PUBLISHABLE_KEY` — `sb_publishable_...` (replaces `anon`)
- `VITE_API_URL` — Express API base URL (default `http://localhost:3000`)

### `api/` (server-only, never expose)
- `SUPABASE_URL` — Supabase project URL
- `SUPABASE_PUBLISHABLE_KEY` — `sb_publishable_...`. Used to build per-request, JWT-scoped clients so RLS policies see `auth.uid()`.
- `SUPABASE_SECRET_KEY` — `sb_secret_...` (replaces `service_role`). Used by the auth middleware to verify JWTs and for admin-only operations that bypass RLS.
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
