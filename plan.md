# Dunedin AI — Implementation Plan

## Context
Build a web app for managing reading highlights from Kindle (and later web/Twitter). Users upload Kindle clipping files, highlights get parsed and stored, and users can search and annotate them. Greenfield project — only readme.md and blueprint.md exist.

## Tech Stack
- **Backend**: Node.js + Express + TypeScript
- **Frontend**: Vue 3 + Vite + TypeScript
- **Database & Auth**: Supabase (PostgreSQL + Supabase Auth)
- **Monorepo**: Single repo with `api/` and `app/` directories

## Database Schema (Supabase/PostgreSQL)

### Tables
- **users** — id (UUID, from Supabase Auth), email, last_login, active, status
- **highlights** — id, user_id (FK), text, origin (enum: kindle/web/twitter), author, date, hash (unique per user, SHA-256 of user_id + text)
- **notes** — id, highlight_id (FK), user_id (FK), note, date
- **files** — id, user_id (FK), filename, type (e.g. 'kindle3'), imported_date, status

### Constraints
- `highlights.hash` + `highlights.user_id` unique index to prevent duplicates
- RLS (Row Level Security) policies so users only access their own data

## Project Structure
```
dunedin-ai/
├── api/                    # Express backend
│   ├── src/
│   │   ├── index.ts        # Entry point
│   │   ├── routes/
│   │   │   ├── highlights.ts
│   │   │   ├── notes.ts
│   │   │   ├── files.ts
│   │   │   └── auth.ts
│   │   ├── middleware/
│   │   │   └── auth.ts     # Supabase JWT verification
│   │   ├── services/
│   │   │   ├── highlights.ts
│   │   │   ├── import.ts   # Kindle file parser
│   │   │   └── files.ts
│   │   └── lib/
│   │       └── supabase.ts # Supabase client
│   ├── package.json
│   └── tsconfig.json
├── app/                    # Vue frontend
│   ├── src/
│   │   ├── App.vue
│   │   ├── main.ts
│   │   ├── router/
│   │   ├── views/
│   │   │   ├── SearchView.vue    # Main page — search box
│   │   │   ├── LoginView.vue
│   │   │   ├── RegisterView.vue
│   │   │   └── SettingsView.vue  # Import files, stats
│   │   ├── components/
│   │   │   ├── HighlightCard.vue
│   │   │   ├── SearchBar.vue
│   │   │   ├── UserMenu.vue
│   │   │   └── FileUpload.vue
│   │   └── lib/
│   │       └── supabase.ts
│   ├── package.json
│   └── tsconfig.json
├── supabase/
│   └── migrations/         # SQL migration files
├── readme.md
└── blueprint.md
```

## API Endpoints

### Auth (handled mostly by Supabase client-side)
- Supabase Auth handles login/register directly from the Vue app
- API validates JWT tokens via middleware

### Highlights
- `GET /api/highlights?q=&author=&origin=&page=&limit=` — search/list
- `GET /api/highlights/:id` — single highlight with its notes
- `DELETE /api/highlights/:id`

### Notes
- `POST /api/highlights/:id/notes` — add note to highlight
- `PUT /api/notes/:id` — edit note
- `DELETE /api/notes/:id`

### Files / Import
- `POST /api/files/upload` — upload Kindle clippings file
- `GET /api/files` — list uploaded files
- `POST /api/files/:id/import` — trigger parsing/import of uploaded file

## Implementation Phases

### Phase 1: Project scaffolding
- Init monorepo with `api/` and `app/` directories
- Set up Express + TypeScript backend with basic health endpoint
- Set up Vue + Vite frontend with router
- Configure Supabase project connection (client will need to provide Supabase URL + anon key)
- Create database migrations

### Phase 2: Auth
- Supabase Auth integration in Vue app (login, register, logout)
- Auth middleware in Express (verify Supabase JWT)
- User avatar/menu component in corner

### Phase 3: Highlights & Search
- Highlights CRUD API endpoints
- Search page with search box (main page)
- Highlight cards displaying results
- Search by text, author, origin

### Phase 4: Notes
- Notes API endpoints
- Add/edit/delete notes on highlights in the UI

### Phase 5: File Upload & Kindle Import
- File upload endpoint + Supabase Storage
- Kindle `my_clippings.txt` parser
- Import flow: upload → parse → insert highlights (dedup via hash)
- Settings page with file list and import status

## Verification
1. Run `npm install` in both `api/` and `app/`
2. Start API: `npm run dev` in `api/` — verify health endpoint responds
3. Start App: `npm run dev` in `app/` — verify it loads in browser
4. Test auth flow: register, login, logout
5. Upload a Kindle clippings file and verify highlights appear in search
6. Add a note to a highlight, verify it persists
