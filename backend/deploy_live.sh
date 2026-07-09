#!/bin/bash

echo "========================================"
echo "🚀 Starting LIVE Deployment"
echo "========================================"
echo "⏱️ Started at: $(date)"

# Define live project directory (configure as needed for production server)
LIVE_DIR="/var/www/html/sabha/backend"

# Confirmation (safety check)
read -p "⚠️ Are you sure you want to deploy to LIVE? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
  echo "❌ Deployment cancelled"
  exit 1
fi

# Navigate to project directory
echo "📁 Switching to LIVE project directory..."
cd "$LIVE_DIR" || { echo "❌ Failed to access LIVE directory: $LIVE_DIR"; exit 1; }

# Enable maintenance mode
echo "🔧 Enabling maintenance mode..."
php artisan down || echo "⚠️ App already in maintenance mode"

# Reset any local changes (important for production)
echo "🧹 Resetting local changes..."
git reset --hard HEAD

# Pull latest code from current active branch (or main)
BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo "📥 Pulling latest code from '${BRANCH}' branch..."
git pull origin "${BRANCH}" || { echo "❌ Git pull failed"; php artisan up; exit 1; }

# Install production dependencies
echo "📦 Installing Composer dependencies (production)..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev || { echo "❌ Composer install failed"; php artisan up; exit 1; }

# Build frontend production assets
if command -v npm &> /dev/null; then
    echo "🎨 Building frontend Vite assets..."
    npm install --no-audit --no-fund
    npm run build
fi

# Clear and cache configurations
echo "⚙️ Caching configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations (force for production)
echo "🗄️ Running database migrations..."
php artisan migrate --force || { echo "❌ Migration failed"; php artisan up; exit 1; }

# Ensure correct permissions for storage and bootstrap/cache
echo "🔐 Setting correct directory permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
sudo chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Ensure the Laravel scheduler is registered in cron (runs every minute).
echo "⏰ Ensuring Laravel scheduler cron entry exists..."
CRON_LINE="* * * * * cd $LIVE_DIR && php artisan schedule:run >> /dev/null 2>&1"
if ! sudo crontab -u www-data -l 2>/dev/null | grep -Fq "artisan schedule:run"; then
    ( sudo crontab -u www-data -l 2>/dev/null; echo "$CRON_LINE" ) | sudo crontab -u www-data -
    echo "✅ Scheduler cron entry added for www-data."
else
    echo "✅ Scheduler cron entry already present — skipping."
fi

# Disable maintenance mode
echo "🟢 Disabling maintenance mode..."
php artisan up

echo "========================================"
echo "✅ LIVE Deployment Completed Successfully!"
echo "⏱️ Finished at: $(date)"
echo "========================================"