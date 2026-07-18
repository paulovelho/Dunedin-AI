# Dunedin AI — Claude Instructions

## Mandatory Rule
**If any questions are raised, ask me before acting.** This applies to every prompt — if there is ambiguity, missing context, or a decision to be made, pause and ask rather than assume.

## General Behavior

- If any questions are raised, ask me before acting.
- **ALWAYS ask for explicit permission before editing, creating, or deleting any file.** Present your plan and wait for approval before making any file changes.

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
- **API response shape**: every successful response is `{ "success": true, "data": <payload> }`. When the payload represents a model, `data` is the model's flat field map (i.e. `$model->ToArray()`) — never the MagratheaPHP envelope from `ToJson()` (which wraps fields under an outer `object`/`id`/`created_at`/`updated_at`/`fields` structure). For collections, return an array of `ToArray()` results. Related/nested data is attached as extra keys on the same flat map (e.g. `notes` on a highlight). Frontend code should rely on `response.data` containing the fields directly.
- **Not found vs forbidden**: when a resource exists but belongs to another user, the API returns 404 (not 403) — same as a truly-missing resource. This is deliberate: returning 403 would confirm the resource's existence to a caller who doesn't own it (an enumeration leak). Applied consistently across Highlight, Note, File, and SharedLink controllers.
- **Sharing highlights**: `shared_links` (+ join table `shared_link_highlights`, `ON DELETE CASCADE` both ways) lets a user publicly share one or more of their own highlights via a UUID link, no auth required to view. Public read/visit endpoints (`GET /shared/:uuid`, `POST /shared/:uuid/visit`) strip all internal fields (id, user_id, hash, etc.) and never expose notes. See `api/src/features/SharedLink/`.

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

# PHP tests (mocked DB, no MariaDB needed) — run from api/src
php vendor/platypustechnology/magratheaphp2/phpunit.phar -c phpunit.xml
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
- PHP follows MagratheaPHP2 conventions, organized as feature folders under `api/src/features/<Name>/` (namespace `Dunedin\<Name>`), registered via `->AddFeature("<Name>", ...)` in `api/src/inc.php`:
  - Model: `<Name>.php`, extends `MagratheaModel`, sets `$dbTable`, `$dbPk`, `$dbValues` (each field also declared as an explicit typed public property)
  - Control: `<Name>Control.php`, extends `MagratheaModelControl`, sets `$modelName`, `$modelNamespace`, `$dbTable`
  - API controller(s): `<Name>ApiControl.php` (and e.g. `<Name>PublicApiControl.php` for no-auth routes), extend `MagratheaApiControl`, throw `MagratheaApiException` for errors
- Routes registered in `api/src/DunedinApi.php` (extends `MagratheaApi`), grouped into one private method per feature; `api/src/public/index.php` is the thin HTTP entry point that just runs it
- PHP tests live in `api/src/tests/` (PHPUnit via the vendored `phpunit.phar`, DB mocked with `DatabaseSimulate` — see Development Commands)
- Vue views in `app/src/views/`, reusable components in `app/src/components/`
- Database schema lives in `api/db/migrations/schema.sql` (auto-applied by the `dunedin_db` container on first boot via `/docker-entrypoint-initdb.d`); it's edited directly (no incremental migration files) since the dev DB is wiped on rebuild
- Container names are prefixed `dunedin_` (e.g. `dunedin_api`, `dunedin_app`, `dunedin_db`)

## Reference Files
- `readme.md` — project objective and high-level structure
- `blueprint.md` — database schema and feature requirements
- `plan.md` — detailed implementation plan (phases; written against the original Node/Supabase stack — implementation details are now PHP/MariaDB)
- `api-node/` — archived Express implementation (reference only)
