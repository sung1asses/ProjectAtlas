#!/usr/bin/env sh
# Treat any failure during boot as fatal so the dev server never runs in a broken state.
set -e

FRONTEND_DIR_PATH="/workspace/${FRONTEND_DIR:-frontend}"
PORT="${VITE_PORT:-5173}"

# Apply the same Git identity that the PHP container uses for consistent commit authorship.
if [ -n "${GIT_USER_NAME}" ]; then
  git config --global user.name "${GIT_USER_NAME}"
fi

if [ -n "${GIT_USER_EMAIL}" ]; then
  git config --global user.email "${GIT_USER_EMAIL}"
fi

# Accept mounted SSH keys (if present) by fixing their permissions.
if [ -d "/home/dev/.ssh" ]; then
  chmod 700 /home/dev/.ssh || true
  find /home/dev/.ssh -type f -exec chmod 600 {} \; || true
fi

if [ ! -d "${FRONTEND_DIR_PATH}" ]; then
  # Create the frontend directory if it doesn't exist to ensure the application has a place to scaffold the Vue app.
  mkdir -p "${FRONTEND_DIR_PATH}"
fi

cd "${FRONTEND_DIR_PATH}"

# Wait patiently on a blank repo so you can scaffold the Vue app without the container restarting repeatedly.
if [ ! -f package.json ]; then
  echo "No package.json found in ${FRONTEND_DIR_PATH}. The container will wait until the frontend scaffold is added."
  exec tail -f /dev/null
fi

# Install dependencies only when node_modules is missing to keep restarts fast.
if [ ! -d node_modules ]; then
  echo "Installing frontend dependencies..."
  npm install
fi

# Launch the Vite dev server with host/port overrides so both Docker and the host browser can talk to it.
echo "Starting Vite dev server on port ${PORT}"
exec npm run dev -- --host 0.0.0.0 --port "${PORT}"
