FROM serversideup/php:8.2-fpm-nginx

# Switch to root to install Node.js (needed for compiling frontend assets)
USER root

RUN apt-get update && apt-get install -y curl \
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy existing application directory permissions
COPY --chown=1000:1000 . /var/www/html

# Switch to the default non-root user (www-data) for security
USER www-data

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Install Node dependencies and compile production assets
RUN npm ci && npm run build

# Expose port 8080 (serversideup/php default port)
EXPOSE 8080
