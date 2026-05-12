#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT_DIR"

if ! command -v curl >/dev/null 2>&1; then
  echo "[ERROR] curl not found. Please install curl first."
  exit 1
fi

PHP_BIN=""
if command -v php >/dev/null 2>&1; then
  PHP_BIN="$(command -v php)"
elif [ -x "/c/xampp/php/php.exe" ]; then
  PHP_BIN="/c/xampp/php/php.exe"
elif [ -x "C:/xampp/php/php.exe" ]; then
  PHP_BIN="C:/xampp/php/php.exe"
else
  echo "[ERROR] php not found in PATH and XAMPP PHP not found."
  exit 1
fi

NGROK_URL="$(curl -s http://127.0.0.1:4040/api/tunnels | sed -n 's/.*"public_url":"\(https:[^"]*\)".*/\1/p' | head -n 1)"

if [ -z "${NGROK_URL}" ]; then
  echo "[ERROR] Could not detect ngrok HTTPS URL."
  echo "Make sure ngrok is running: ngrok http 8000"
  exit 1
fi

if [ ! -f .env ]; then
  echo "[ERROR] .env file not found in $ROOT_DIR"
  exit 1
fi

if grep -q '^APP_URL=' .env; then
  sed -i "s#^APP_URL=.*#APP_URL=${NGROK_URL}#" .env
else
  echo "APP_URL=${NGROK_URL}" >> .env
fi

"$PHP_BIN" artisan config:clear >/dev/null
"$PHP_BIN" artisan cache:clear >/dev/null
"$PHP_BIN" artisan route:clear >/dev/null

echo "[OK] APP_URL updated to: ${NGROK_URL}"
echo "[OK] Laravel config/cache/route cleared."
echo "[NEXT] Start payment flow from: ${NGROK_URL}/online-registration"
