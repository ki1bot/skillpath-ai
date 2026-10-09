ARG COMPOSER_IMAGE=public.ecr.aws/composer/composer:2
ARG NODE_IMAGE=public.ecr.aws/docker/library/node:22-bookworm-slim
ARG PHP_CLI_IMAGE=public.ecr.aws/docker/library/php:8.4-cli-bookworm
ARG PHP_APACHE_IMAGE=public.ecr.aws/docker/library/php:8.4-apache-bookworm

FROM ${COMPOSER_IMAGE} AS composer

FROM ${NODE_IMAGE} AS node

FROM ${PHP_CLI_IMAGE} AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        ca-certificates \
        git \
        gosu \
        libcurl4-openssl-dev \
        libicu-dev \
        libonig-dev \
        libpq-dev \
        libstdc++6 \
        libzip-dev \
        procps \
        unzip \
    && docker-php-ext-install \
        bcmath \
        curl \
        intl \
        mbstring \
        pdo_pgsql \
        zip \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/* \
    && printf 'expose_php=Off\n' > /usr/local/etc/php/conf.d/zz-security.ini

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -sf ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

ENV HOME=/tmp
ENV COMPOSER_HOME=/tmp/composer
ENV NPM_CONFIG_CACHE=/tmp/npm-cache

FROM php-base AS development

ENV DOCKER_MODE=development

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/skillpath-entrypoint

RUN chmod +x /usr/local/bin/skillpath-entrypoint

EXPOSE 8080 5173

ENTRYPOINT ["/usr/local/bin/skillpath-entrypoint"]

FROM php-base AS build

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --no-scripts

COPY package.json package-lock.json .npmrc ./

RUN npm ci

COPY . .

RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative \
    --no-interaction

RUN npm run build \
    && rm -rf node_modules

FROM ${PHP_APACHE_IMAGE} AS runtime

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        ca-certificates \
        libcurl4-openssl-dev \
        libicu-dev \
        libonig-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        bcmath \
        curl \
        intl \
        mbstring \
        opcache \
        pdo_pgsql \
        zip \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -f \
        /etc/apache2/mods-enabled/mpm_event.conf \
        /etc/apache2/mods-enabled/mpm_event.load \
        /etc/apache2/mods-enabled/mpm_worker.conf \
        /etc/apache2/mods-enabled/mpm_worker.load \
        /etc/apache2/mods-enabled/mpm_prefork.conf \
        /etc/apache2/mods-enabled/mpm_prefork.load \
    && a2enmod mpm_prefork \
    && a2enmod rewrite headers expires deflate \
    && rm -rf /var/lib/apt/lists/* \
    && printf 'expose_php=Off\n' > /usr/local/etc/php/conf.d/zz-security.ini

ENV DOCKER_MODE=production
ENV APP_ENV=production
ENV APP_DEBUG=false

WORKDIR /var/www/html

COPY --from=build /app /var/www/html

COPY docker/apache-laravel.conf /etc/apache2/conf-available/laravel.conf
COPY docker/entrypoint.sh /usr/local/bin/skillpath-entrypoint

RUN a2enconf laravel \
    && mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/skillpath-entrypoint \
    && apache2ctl configtest \
    && test ! -d /var/www/html/node_modules

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/skillpath-entrypoint"]

CMD ["apache2-foreground"]
