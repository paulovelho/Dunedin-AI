#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

API_URL="http://localhost:3000"
APP_URL="http://localhost:5173"
TIMEOUT="${TIMEOUT:-300}"

echo "==> Pulling images (verbose)..."
docker compose --progress=plain pull

echo
echo "==> Starting containers (verbose)..."
docker compose --progress=plain up -d

echo
echo "==> Waiting for services. Cold start runs 'npm install' inside the"
echo "    containers and can take 1-2 minutes the first time."
echo

wait_for() {
  local name=$1 url=$2 elapsed=0
  while ! curl -sf -o /dev/null --max-time 2 "$url"; do
    if (( elapsed >= TIMEOUT )); then
      echo
      echo "[FAIL] $name did not respond at $url within ${TIMEOUT}s"
      echo "       Check: docker compose logs $name"
      exit 1
    fi
    sleep 2
    elapsed=$((elapsed + 2))
    printf "."
  done
  echo
  echo "  [OK] $name ready at $url"
}

wait_for api "$API_URL/health"
wait_for app "$APP_URL"

cat <<EOF

============================================================
Dunedin AI is running:

  App:    $APP_URL
  API:    $API_URL
  Health: $API_URL/health

Useful commands:
  docker compose logs -f          # tail all logs
  docker compose logs -f api      # tail API only
  docker compose logs -f app      # tail app only
  docker compose down             # stop containers
  docker compose down -v          # stop and wipe node_modules cache
============================================================
EOF
