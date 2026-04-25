#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

API_URL="http://localhost:3000"
APP_URL="http://localhost:5173"
TIMEOUT="${TIMEOUT:-300}"
API_HEALTH="$API_URL/api/v1/health"

VENDOR_AUTOLOAD="api/src/vendor/autoload.php"
COMPOSER_JSON="api/src/composer.json"

# Install PHP deps on host when missing or out of date. Uses a throwaway
# composer:2 container so the host doesn't need PHP or ext-pdo_mysql.
if [ ! -f "$VENDOR_AUTOLOAD" ] || [ "$COMPOSER_JSON" -nt "$VENDOR_AUTOLOAD" ]; then
  echo "==> Installing PHP dependencies (composer:2 container)..."
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$PWD/api/src:/app" \
    -w /app \
    composer:2 \
    composer install --no-interaction --prefer-dist --no-progress --ignore-platform-reqs
fi

echo
echo "==> Building and starting containers (verbose)..."
docker compose --progress=plain up -d --build

echo
echo "==> Waiting for services. Cold start builds the PHP image and runs"
echo "    'npm install' inside the app container; it can take a few"
echo "    minutes the first time."
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

wait_for api "$API_HEALTH"
wait_for app "$APP_URL"

cat <<EOF

============================================================
Dunedin AI is running:

  App:    $APP_URL
  API:    $API_URL/api/v1
  Health: $API_HEALTH
  DB:     mariadb on localhost:3306 (user: dunedin / pass: dunedin / db: dunedin)

Useful commands:
  docker compose logs -f          # tail all logs
  docker compose logs -f api      # tail API only
  docker compose logs -f app      # tail app only
  ./stop.sh                       # stop containers (keep volumes)
  docker compose down -v          # stop and wipe all volumes
============================================================
EOF
