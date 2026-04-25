# Dunedin AI — Claude Instructions

## Mandatory Rule
**If any questions are raised, ask me before acting.** This applies to every prompt — if there is ambiguity, missing context, or a decision to be made, pause and ask rather than assume.

## Project Overview
Kindle/web highlights manager. Users upload Kindle clipping files, search highlights, and add notes.

## Tech Stack
- **Backend**: PHP 8.4 + MagratheaPHP2 framework (`api/` directory), served via Apache
- **Frontend**: Vue 3 + Vite + TypeScript (`app/` directory)
- **Database**: MariaDB
- **Auth**: Firebase Authentication (Google OAuth) — frontend uses the Firebase JS SDK; the PHP API verifies Firebase ID tokens via `kreait/firebase-php`

## Key Decisions
- The original Node.js/Express + Supabase implementation was replaced by PHP + MariaDB due to server-side hosting constraints. The old Express code is preserved in `api-node/` for reference (do not modify or run).
- Auth is done client-side via the Firebase JS SDK (Google OAuth popup). The frontend sends the Firebase ID token as `Authorization: Bearer <token>`; `App\Api\AuthControl::ValidateToken` verifies it against Google's JWKS and lazy-creates / updates a `users` row keyed by `firebase_uid`.
- Per-user data isolation is enforced in PHP (`WHERE user_id = ?` on every query) — no RLS, since MariaDB has no equivalent.
- Highlights are deduplicated via SHA-256 hash of (user_id + text), stored in `highlights.hash` with a `(user_id, hash)` unique index.
- File import currently supports only Kindle `my_clippings.txt` format (`kindle3` type), but the schema allows future formats. Uploaded files land in `api/storage/uploads/` (local volume).
- All API routes are mounted under `/api/v1`.

## Development Commands
```bash
# Full stack (api + app + db)
./start.sh                  # docker compose up + wait for health
./stop.sh                   # docker compose down -v

# Container shells
docker exec -it dunedin_api  bash
docker exec -it dunedin_app  sh
docker exec -it dunedin_db   mariadb -u dunedin -pdunedin dunedin

# Composer (run inside the api container)
docker exec -it dunedin_api composer install
```

## Environment Variables

### `app/` (Vite — `VITE_` prefix required to expose to the client)
- `VITE_FIREBASE_API_KEY` — Firebase web API key
- `VITE_FIREBASE_AUTH_DOMAIN` — `<project>.firebaseapp.com`
- `VITE_FIREBASE_PROJECT_ID` — Firebase project ID
- `VITE_FIREBASE_APP_ID` — Firebase web app ID
- `VITE_API_URL` — API base URL including version prefix (default `http://localhost:3000/api/v1`)

### `api/` (server-only, never expose)
- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` — MariaDB connection (defaults match the `dunedin_db` service)
- `FIREBASE_PROJECT_ID` — used by `kreait/firebase-php` to verify ID tokens (no service account needed for token verification)

## Code Conventions
- TypeScript strict mode in the frontend
- PHP follows MagratheaPHP2 conventions:
  - Models in `api/models/` (namespace `App\Models`), one per table, set `$dbTable`, `$dbPk`, `$dbValues`
  - Controls in `api/controls/` (namespace `App\Controls`), extend `MagratheaModelControl`
  - API controllers in `api/api/` (namespace `App\Api`), extend `MagratheaApiControl`, throw `MagratheaApiException` for errors
  - Routes registered in `api/public/index.php`
- Vue views in `app/src/views/`, reusable components in `app/src/components/`
- Database migrations in `api/db/migrations/` (auto-applied by the `dunedin_db` container on first boot via `/docker-entrypoint-initdb.d`)
- Container names are prefixed `dunedin_` (e.g. `dunedin_api`, `dunedin_app`, `dunedin_db`)

## Reference Files
- `readme.md` — project objective and high-level structure
- `blueprint.md` — database schema and feature requirements
- `plan.md` — detailed implementation plan (phases; written against the original Node/Supabase stack — implementation details are now PHP/MariaDB)
- `api-node/` — archived Express implementation (reference only)
