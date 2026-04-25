# Dunedin AI — API

PHP 8.4 + MagratheaPHP2 backend, served via Apache. See the project root `readme.md` for the broader architecture.

## Resetting the database

Wipe all application data (users, highlights, notes, files) and reset auto-increment counters:

```bash
docker exec dunedin_db mariadb -u dunedin -pdunedin dunedin -e "SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE notes; TRUNCATE TABLE files; TRUNCATE TABLE highlights; TRUNCATE TABLE users; SET FOREIGN_KEY_CHECKS=1;"
```

Notes:
- `TRUNCATE` wipes all rows and resets `AUTO_INCREMENT` back to 1. Use `DELETE FROM <table>` instead if you want to preserve the id counter.
- `FOREIGN_KEY_CHECKS=0` is required because `notes`, `highlights`, and `files` have FKs into `users`. The setting is session-scoped — it does not leak to other connections.
- Only the four application tables are cleared. Magrathea's internal tables (`_magrathea_*`) are untouched.
- Uploaded files in `api/storage/uploads/` are **not** removed by the SQL above. Clear them separately for a full reset:

  ```bash
  rm -rf api/storage/uploads/*
  ```

## Full reset (drop and re-run migrations)

To rebuild the schema from scratch (re-runs `db/migrations/0001_init.sql`):

```bash
./stop.sh && ./start.sh
```

`./stop.sh` runs `docker compose down -v`, which removes the `db_data` volume — so the DB is recreated empty on the next `./start.sh` and the migration script is applied automatically.
