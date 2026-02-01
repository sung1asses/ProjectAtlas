# Lightweight PHP-FPM base image that keeps the footprint small.
FROM php:8.3-fpm-alpine

# Allow callers to align container permissions with the host filesystem.
ARG APP_UID=1000
ARG APP_GID=1000

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    PATH="/home/dev/.composer/vendor/bin:${PATH}" \
    HOME=/home/dev

# Install OS tools needed for Composer, Git, SSH, and PHP intl/opcache extensions.
RUN apk add --no-cache bash git curl openssh-client icu-data-full icu-libs icu-dev tzdata zip unzip libzip-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql intl opcache zip \
    && rm -rf /var/cache/apk/*

# Provide Composer globally for dependency management.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create a non-root user that mirrors the host UID/GID to avoid permission issues on bind mounts.
RUN addgroup -g ${APP_GID} devgroup 2>/dev/null || addgroup devgroup \
    && adduser -G devgroup -u ${APP_UID} -D dev \
    && mkdir -p /home/dev/.composer \
    && chown -R dev:devgroup /home/dev

# Ship project-specific PHP config and an entrypoint that configures Git before php-fpm starts.
COPY php.ini /usr/local/etc/php/conf.d/z-atlas.ini
COPY entrypoint.sh /usr/local/bin/php-entrypoint

RUN chmod +x /usr/local/bin/php-entrypoint

WORKDIR /workspace

USER dev

ENTRYPOINT ["/usr/local/bin/php-entrypoint"]
CMD ["php-fpm"]
