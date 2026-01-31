#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
ENV_FILE="${ROOT_DIR}/.env"
EXAMPLE_FILE="${ROOT_DIR}/.env.example"

if [ -f "${ENV_FILE}" ]; then
  echo "docker/.env already exists"
  exit 0
fi

cp "${EXAMPLE_FILE}" "${ENV_FILE}"
echo "Created ${ENV_FILE}. Update it with your host-specific values before running docker compose."
