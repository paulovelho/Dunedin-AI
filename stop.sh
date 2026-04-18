#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "==> Stopping containers and wiping named volumes (verbose)..."
docker compose --progress=plain down -v --remove-orphans

echo
echo "============================================================"
echo "Dunedin AI containers stopped and volumes wiped."
echo "node_modules cache will be rebuilt on next ./start.sh"
echo "============================================================"
