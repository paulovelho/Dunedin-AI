#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "==> Stopping containers (verbose)..."
docker compose --progress=plain down --remove-orphans

echo
echo "============================================================"
echo "Dunedin AI containers stopped."
echo "Named volumes preserved (db_data, api_vendor, app_node_modules)."
echo "To wipe volumes too: docker compose down -v"
echo "============================================================"
