#!/usr/bin/env sh
# Fail fast so Docker logs show the root cause when setup goes wrong.
set -e

# Mirror the host Git identity so commits made inside the container stay attributed correctly.
if [ -n "${GIT_USER_NAME}" ]; then
  git config --global user.name "${GIT_USER_NAME}"
fi

if [ -n "${GIT_USER_EMAIL}" ]; then
  git config --global user.email "${GIT_USER_EMAIL}"
fi

# Tighten permissions for mounted SSH keys (if any) so OpenSSH accepts them.
if [ -d "/home/dev/.ssh" ]; then
  chmod 700 /home/dev/.ssh || true
  find /home/dev/.ssh -type f -exec chmod 600 {} \; || true
fi

# Make sure the backend directory exists even on a brand-new clone so Composer/artisan commands never fail on missing paths.
BACKEND_DIR_PATH="/workspace/${BACKEND_DIR:-backend}"
if [ ! -d "${BACKEND_DIR_PATH}" ]; then
  mkdir -p "${BACKEND_DIR_PATH}"
fi

# Hand control back to the original container command (php-fpm by default).
exec "$@"
