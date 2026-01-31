# Minimal Node 20 base image for the Vue tooling chain.
FROM node:20-alpine

# Keep UID/GID aligned with the host filesystem.
ARG APP_UID=1000
ARG APP_GID=1000

ENV HOME=/home/dev

# Install shell essentials plus Git/SSH for dependency installs over SSH.
RUN apk add --no-cache git openssh bash \
    && (deluser node 2>/dev/null || true) \
    && (delgroup node 2>/dev/null || true) \
    && (addgroup -g ${APP_GID} devgroup 2>/dev/null || addgroup devgroup) \
    && adduser -G devgroup -u ${APP_UID} -D dev \
    && mkdir -p /home/dev \
    && chown -R dev:devgroup /home/dev

WORKDIR /workspace

# Custom entrypoint lazily installs dependencies and starts the Vite dev server.
COPY entrypoint.sh /usr/local/bin/node-entrypoint
RUN chmod +x /usr/local/bin/node-entrypoint

USER dev

ENTRYPOINT ["/usr/local/bin/node-entrypoint"]
CMD ["npm", "run", "dev"]
