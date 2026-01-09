#!/bin/bash

###############################################################################
# KEWASNET CodeIgniter 4 Application Deployment Script
###############################################################################
# 
# Usage: ./deploy.sh [environment]
# Example: ./deploy.sh production
#          ./deploy.sh staging
#
# This script automates the deployment process for the KEWASNET application
# 
# Requirements:
#   - PHP 8.4 or higher
#   - Composer
#   - Node.js and npm
#   - Git
#   - MySQL/MariaDB
#   - Nginx or Apache
###############################################################################

set -e  # Exit on error

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT="${1:-production}"
APP_NAME="kewasnet-website"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="backups/$TIMESTAMP"

# Deployment paths (update these based on your server structure)
if [ "$ENVIRONMENT" = "production" ]; then
    DEPLOY_PATH="/var/www/html/kewasnet-website"
    DOMAIN="test.mtalii.tech"  # Update with your actual domain
elif [ "$ENVIRONMENT" = "staging" ]; then
    DEPLOY_PATH="/var/www/html/kewasnet-website-staging"
    DOMAIN="staging.mtalii.tech"  # Update with your actual staging domain
else
    echo -e "${RED}Error: Invalid environment. Use 'production' or 'staging'${NC}"
    exit 1
fi

###############################################################################
# Helper Functions
###############################################################################

print_header() {
    echo -e "\n${BLUE}===================================================================${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}===================================================================${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

check_command() {
    if ! command -v $1 &> /dev/null; then
        print_error "$1 is not installed. Please install it first."
        exit 1
    fi
}

# Helper function to run commands with sudo fallback
run_with_sudo() {
    local cmd="$1"
    # Try with sudo first, then without
    sudo bash -c "$cmd" 2>/dev/null || bash -c "$cmd" 2>/dev/null || {
        print_warning "Command failed: $cmd"
        return 1
    }
}

###############################################################################
# Pre-Deployment Checks
###############################################################################

print_header "Pre-Deployment Checks for $ENVIRONMENT Environment"

# Check if running as appropriate user
if [ "$EUID" -eq 0 ]; then
    print_warning "Running as root. Consider using a deployment user instead."
fi

# Check required commands
print_info "Checking required commands..."
check_command "git"
check_command "composer"
check_command "php"
check_command "npm"

print_success "All required commands are available"

# Verify PHP 8.4 is available
print_info "Verifying PHP 8.4 installation..."
if ! php -v | grep -q "8.4"; then
    print_warning "PHP 8.4 not detected in version string. Continuing with available PHP version..."
    print_info "Note: PHP 8.4 is recommended. To install on Ubuntu/Debian:"
    print_info "  sudo add-apt-repository ppa:ondrej/php -y"
    print_info "  sudo apt update"
    print_info "  sudo apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-gd php8.4-intl"
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR_MINOR=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
print_info "PHP Version: $PHP_VERSION"

# Check if PHP 8.4 or higher
if php -r "exit(version_compare(PHP_VERSION, '8.4.0', '<') ? 0 : 1);"; then
    print_error "PHP 8.4 or higher is required. Current version: $PHP_VERSION"
    print_info "Please install PHP 8.4 before continuing."
    exit 1
fi

# Verify it's PHP 8.4.x (not 8.5+ or 9.0+)
if php -r "exit(version_compare(PHP_VERSION, '8.4.0', '>=') && version_compare(PHP_VERSION, '8.5.0', '<') ? 0 : 1);"; then
    print_success "PHP 8.4 detected - version check passed"
else
    print_warning "PHP 8.4.x is recommended. Current version: $PHP_VERSION (will continue)"
fi

# Check PHP extensions
print_info "Checking required PHP extensions..."
REQUIRED_EXTENSIONS=("intl" "mbstring" "json" "mysqlnd" "xml" "curl" "gd" "zip")
MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -qi "^$ext$"; then
        MISSING_EXTENSIONS+=("$ext")
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -ne 0 ]; then
    print_error "Missing PHP extensions: ${MISSING_EXTENSIONS[*]}"
    exit 1
fi
print_success "All required PHP extensions are installed"

###############################################################################
# Backup Current Deployment
###############################################################################

if [ -d "$DEPLOY_PATH" ]; then
    print_header "Creating Backup"
    
    sudo mkdir -p "$DEPLOY_PATH/$BACKUP_DIR" 2>/dev/null || mkdir -p "$DEPLOY_PATH/$BACKUP_DIR"
    
    # Backup .env file
    if [ -f "$DEPLOY_PATH/.env" ]; then
        sudo cp "$DEPLOY_PATH/.env" "$DEPLOY_PATH/$BACKUP_DIR/.env.backup" 2>/dev/null || \
        cp "$DEPLOY_PATH/.env" "$DEPLOY_PATH/$BACKUP_DIR/.env.backup"
        print_success "Backed up .env file"
    fi
    
    # Backup writable directory
    if [ -d "$DEPLOY_PATH/writable" ]; then
        sudo tar -czf "$DEPLOY_PATH/$BACKUP_DIR/writable_backup.tar.gz" -C "$DEPLOY_PATH" writable/ 2>/dev/null || \
        tar -czf "$DEPLOY_PATH/$BACKUP_DIR/writable_backup.tar.gz" -C "$DEPLOY_PATH" writable/
        print_success "Backed up writable directory"
    fi
    
    # Backup uploads
    if [ -d "$DEPLOY_PATH/public/uploads" ]; then
        sudo tar -czf "$DEPLOY_PATH/$BACKUP_DIR/uploads_backup.tar.gz" -C "$DEPLOY_PATH/public" uploads/ 2>/dev/null || \
        tar -czf "$DEPLOY_PATH/$BACKUP_DIR/uploads_backup.tar.gz" -C "$DEPLOY_PATH/public" uploads/
        print_success "Backed up uploads directory"
    fi
    
    # Backup database (if credentials are available)
    if [ -f "$DEPLOY_PATH/.env" ]; then
        DB_NAME=$(grep "^database.default.database" "$DEPLOY_PATH/.env" | cut -d '=' -f2 | tr -d ' "')
        DB_USER=$(grep "^database.default.username" "$DEPLOY_PATH/.env" | cut -d '=' -f2 | tr -d ' "')
        DB_PASS=$(grep "^database.default.password" "$DEPLOY_PATH/.env" | cut -d '=' -f2 | tr -d ' "')
        
        if [ -n "$DB_NAME" ] && [ -n "$DB_USER" ]; then
            print_info "Backing up database: $DB_NAME"
            # Use MYSQL_PWD environment variable for password (more secure)
            export MYSQL_PWD="$DB_PASS"
            mysqldump -u"$DB_USER" "$DB_NAME" > "$DEPLOY_PATH/$BACKUP_DIR/database_backup.sql" 2>/dev/null || {
                print_warning "Database backup failed. Continuing deployment..."
            }
            unset MYSQL_PWD
            if [ -f "$DEPLOY_PATH/$BACKUP_DIR/database_backup.sql" ] && [ -s "$DEPLOY_PATH/$BACKUP_DIR/database_backup.sql" ]; then
                sudo gzip "$DEPLOY_PATH/$BACKUP_DIR/database_backup.sql" 2>/dev/null || \
                gzip "$DEPLOY_PATH/$BACKUP_DIR/database_backup.sql"
                print_success "Database backed up successfully"
            fi
        fi
    fi
    
    print_success "Backup completed: $BACKUP_DIR"
fi

###############################################################################
# Enable Maintenance Mode
###############################################################################

print_header "Enabling Maintenance Mode"

if [ -d "$DEPLOY_PATH" ]; then
    # Create maintenance flag file
    sudo tee "$DEPLOY_PATH/public/maintenance.html" > /dev/null << 'EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - KEWASNET</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        p { color: #666; line-height: 1.6; }
        .logo { width: 150px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Maintenance in Progress</h1>
        <p>We're currently performing scheduled maintenance to improve your experience.</p>
        <p>We'll be back shortly. Thank you for your patience!</p>
        <p><strong>KEWASNET Team</strong></p>
    </div>
</body>
</html>
EOF
    # If sudo tee failed, try without sudo
    if [ ! -f "$DEPLOY_PATH/public/maintenance.html" ]; then
        cat > "$DEPLOY_PATH/public/maintenance.html" << 'EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - KEWASNET</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        p { color: #666; line-height: 1.6; }
        .logo { width: 150px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Maintenance in Progress</h1>
        <p>We're currently performing scheduled maintenance to improve your experience.</p>
        <p>We'll be back shortly. Thank you for your patience!</p>
        <p><strong>KEWASNET Team</strong></p>
    </div>
</body>
</html>
EOF
    fi
    
    # Redirect to maintenance page (optional - requires .htaccess modification)
    print_success "Maintenance mode enabled"
else
    print_info "Fresh deployment - skipping maintenance mode"
fi

###############################################################################
# Pull Latest Code
###############################################################################

print_header "Pulling Latest Code"

# Fix Git ownership issue
print_info "Configuring Git safe directory..."
git config --global --add safe.directory "$DEPLOY_PATH" 2>/dev/null || true

# Ensure we're in the deployment directory
if [ ! -d "$DEPLOY_PATH" ]; then
    print_error "Deployment directory does not exist: $DEPLOY_PATH"
    print_info "Creating deployment directory..."
    sudo mkdir -p "$DEPLOY_PATH" 2>/dev/null || mkdir -p "$DEPLOY_PATH"
    if [ ! -d "$DEPLOY_PATH" ]; then
        print_error "Failed to create deployment directory. Check permissions."
        exit 1
    fi
fi

cd "$DEPLOY_PATH" || {
    print_error "Failed to change to deployment directory: $DEPLOY_PATH"
    exit 1
}

# Initialize git if not already initialized
if [ ! -d ".git" ]; then
    print_info "Initializing git repository..."
    git init
    git remote add origin https://github.com/theOnyangos/$APP_NAME.git
fi

# Ensure proper git ownership
CURRENT_USER=$(whoami)
if [ -d ".git" ]; then
    GIT_OWNER=$(stat -c '%U' .git 2>/dev/null || echo "")
    if [ "$GIT_OWNER" != "$CURRENT_USER" ] && [ "$GIT_OWNER" != "" ]; then
        print_warning "Git directory owned by $GIT_OWNER, fixing to $CURRENT_USER..."
        sudo chown -R $CURRENT_USER:$CURRENT_USER .git 2>/dev/null || true
    fi
fi

# Fetch and pull latest changes
print_info "Fetching latest changes from git..."
git fetch origin
git reset --hard origin/main  # Change 'main' to your production branch

print_success "Code updated successfully"

###############################################################################
# Install/Update Dependencies
###############################################################################

print_header "Installing Dependencies"

# Composer dependencies
print_info "Installing/updating Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Composer dependencies installed"

# NPM dependencies
if [ -f "package.json" ]; then
    print_info "Installing/updating NPM dependencies..."
    npm ci --production
    print_success "NPM dependencies installed"
    
    # Build assets
    print_info "Building frontend assets..."
    npm run build 2>/dev/null || npm run prod 2>/dev/null || print_warning "No build script found"
    print_success "Frontend assets built"
fi

###############################################################################
# Environment Configuration
###############################################################################

print_header "Configuring Environment"

# Restore .env if exists in backup, otherwise prompt to create
if [ -f "$DEPLOY_PATH/$BACKUP_DIR/.env.backup" ]; then
    sudo cp "$DEPLOY_PATH/$BACKUP_DIR/.env.backup" .env 2>/dev/null || \
    cp "$DEPLOY_PATH/$BACKUP_DIR/.env.backup" .env
    print_success "Restored .env from backup"
elif [ ! -f ".env" ]; then
    if [ -f "env" ]; then
        sudo cp env .env 2>/dev/null || cp env .env
        print_warning ".env file created from template. Please update with production values!"
    elif [ -f ".env.example" ]; then
        sudo cp .env.example .env 2>/dev/null || cp .env.example .env
        print_warning ".env file created from .env.example. Please update with production values!"
    else
        print_error ".env file not found. Please create one manually."
        exit 1
    fi
fi

# Set correct environment
sudo sed -i "s/CI_ENVIRONMENT = .*/CI_ENVIRONMENT = $ENVIRONMENT/" .env 2>/dev/null || \
sed -i "s/CI_ENVIRONMENT = .*/CI_ENVIRONMENT = $ENVIRONMENT/" .env
print_success "Environment set to: $ENVIRONMENT"

###############################################################################
# File Permissions
###############################################################################

print_header "Setting File Permissions"

# Set ownership (adjust user:group as needed)
# For Hostinger VPS, typically www-data or deploy user
WEB_USER="www-data"
WEB_GROUP="www-data"
# If using deploy user, uncomment below:
# WEB_USER="deploy"
# WEB_GROUP="deploy"

# First, ensure writable directory structure exists
print_info "Creating writable directory structure..."
sudo mkdir -p "$DEPLOY_PATH/writable/cache" 2>/dev/null || mkdir -p "$DEPLOY_PATH/writable/cache"
sudo mkdir -p "$DEPLOY_PATH/writable/logs" 2>/dev/null || mkdir -p "$DEPLOY_PATH/writable/logs"
sudo mkdir -p "$DEPLOY_PATH/writable/session" 2>/dev/null || mkdir -p "$DEPLOY_PATH/writable/session"
sudo mkdir -p "$DEPLOY_PATH/writable/debugbar" 2>/dev/null || mkdir -p "$DEPLOY_PATH/writable/debugbar"
sudo mkdir -p "$DEPLOY_PATH/writable/uploads" 2>/dev/null || mkdir -p "$DEPLOY_PATH/writable/uploads"
sudo mkdir -p "$DEPLOY_PATH/public/uploads" 2>/dev/null || mkdir -p "$DEPLOY_PATH/public/uploads"

# Set ownership on writable directories first (before general chmod)
print_info "Setting ownership on writable directories to $WEB_USER:$WEB_GROUP..."
sudo chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH/writable" 2>/dev/null || \
chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH/writable" 2>/dev/null || \
print_warning "Could not set ownership on writable directory. You may need to run: sudo chown -R $WEB_USER:$WEB_GROUP $DEPLOY_PATH/writable"

# Set ownership on uploads directory
sudo chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH/public/uploads" 2>/dev/null || \
chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH/public/uploads" 2>/dev/null || \
print_warning "Could not set ownership on uploads directory"

# Set ownership on entire deployment directory
print_info "Setting ownership on deployment directory to $WEB_USER:$WEB_GROUP..."
sudo chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH" 2>/dev/null || \
chown -R $WEB_USER:$WEB_GROUP "$DEPLOY_PATH" 2>/dev/null || \
print_warning "Could not set ownership. You may need to run: sudo chown -R $WEB_USER:$WEB_GROUP $DEPLOY_PATH"

# Set general directory permissions (we'll fix writable separately after)
print_info "Setting general directory permissions..."
# Use a simpler approach - set permissions on common directories first
sudo chmod 755 "$DEPLOY_PATH" 2>/dev/null || chmod 755 "$DEPLOY_PATH"
sudo find "$DEPLOY_PATH/app" -type d -exec chmod 755 {} \; 2>/dev/null || \
find "$DEPLOY_PATH/app" -type d -exec chmod 755 {} \; 2>/dev/null || true
sudo find "$DEPLOY_PATH/public" -type d -exec chmod 755 {} \; 2>/dev/null || \
find "$DEPLOY_PATH/public" -type d -exec chmod 755 {} \; 2>/dev/null || true
sudo find "$DEPLOY_PATH/vendor" -type d -exec chmod 755 {} \; 2>/dev/null || \
find "$DEPLOY_PATH/vendor" -type d -exec chmod 755 {} \; 2>/dev/null || true

# Set general file permissions
print_info "Setting general file permissions..."
sudo find "$DEPLOY_PATH/app" -type f -exec chmod 644 {} \; 2>/dev/null || \
find "$DEPLOY_PATH/app" -type f -exec chmod 644 {} \; 2>/dev/null || true
sudo find "$DEPLOY_PATH/public" -type f -exec chmod 644 {} \; 2>/dev/null || \
find "$DEPLOY_PATH/public" -type f -exec chmod 644 {} \; 2>/dev/null || true

# Writable directories need special permissions (775 = rwxrwxr-x)
print_info "Setting writable directory permissions..."
sudo chmod -R 775 "$DEPLOY_PATH/writable" 2>/dev/null || \
chmod -R 775 "$DEPLOY_PATH/writable"

# Ensure cache directory has correct permissions (explicitly)
print_info "Ensuring cache directory permissions..."
sudo chmod 775 "$DEPLOY_PATH/writable/cache" 2>/dev/null || chmod 775 "$DEPLOY_PATH/writable/cache"
sudo chown $WEB_USER:$WEB_GROUP "$DEPLOY_PATH/writable/cache" 2>/dev/null || \
chown $WEB_USER:$WEB_GROUP "$DEPLOY_PATH/writable/cache" 2>/dev/null || true

# Uploads directory permissions
sudo chmod -R 775 "$DEPLOY_PATH/public/uploads" 2>/dev/null || \
chmod -R 775 "$DEPLOY_PATH/public/uploads"

# Make spark executable
sudo chmod +x "$DEPLOY_PATH/spark" 2>/dev/null || \
chmod +x "$DEPLOY_PATH/spark"

print_success "File permissions set correctly"

###############################################################################
# Database Migrations
###############################################################################

print_header "Running Database Migrations"

print_info "Checking for pending migrations..."
php spark migrate || {
    print_error "Migration failed!"
    print_warning "You may need to run migrations manually"
}

# Run database seeders (if needed)
print_info "Running database seeders..."
php spark db:seed 2>/dev/null || print_info "No seeders configured or seeders already run"

print_success "Database migrations completed"

###############################################################################
# Email Queue Setup
###############################################################################

print_header "Configuring Email Queue"

# Check if email queue cron job exists
CRON_JOB="* * * * * cd $DEPLOY_PATH && php spark email:process >> /dev/null 2>&1"
CRON_EXISTS=$(crontab -l 2>/dev/null | grep -F "php spark email:process" || true)

if [ -z "$CRON_EXISTS" ]; then
    print_info "Setting up email queue cron job..."
    
    # Add cron job for email queue processing
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab - 2>/dev/null && {
        print_success "Email queue cron job added (runs every minute)"
    } || {
        print_warning "Could not add cron job automatically. Please add manually:"
        print_info "  $CRON_JOB"
    }
else
    print_success "Email queue cron job already configured"
fi

# Test email queue command
print_info "Testing email queue processor..."
php spark email:process > /dev/null 2>&1 && {
    print_success "Email queue processor is working"
} || {
    print_warning "Email queue processor test failed. Check logs for details."
}

print_success "Email queue setup completed"

###############################################################################
# Cache Management
###############################################################################

print_header "Managing Cache"

# Clear application cache
print_info "Clearing application cache..."
php spark cache:clear || print_warning "Cache clear command not available"

# Clear route cache
print_info "Clearing route cache..."
sudo rm -rf writable/cache/* 2>/dev/null || rm -rf writable/cache/* 2>/dev/null || true

# Clear view cache
print_info "Clearing view cache..."
sudo rm -rf writable/debugbar/* 2>/dev/null || rm -rf writable/debugbar/* 2>/dev/null || true

# Clear session files (optional - be careful in production)
# rm -rf writable/session/* 2>/dev/null || true

# Warm up cache (if you have a cache warming command)
print_info "Warming up cache..."
php spark optimize 2>/dev/null || print_info "No optimize command available"

print_success "Cache management completed"

###############################################################################
# Optimize Application
###############################################################################

print_header "Optimizing Application"

# Optimize Composer autoloader
print_info "Optimizing Composer autoloader..."
composer dump-autoload --optimize --no-dev

# Generate sitemap (if applicable)
print_info "Generating sitemap..."
php spark sitemap:generate 2>/dev/null || print_info "No sitemap command available"

print_success "Application optimized"

###############################################################################
# Restart Services
###############################################################################

print_header "Restarting Services"

# Restart PHP-FPM (PHP 8.4)
print_info "Restarting PHP-FPM..."
# Try PHP 8.4 first, then fallback to auto-detected version
PHP_MAJOR_MINOR=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
sudo systemctl restart php8.4-fpm 2>/dev/null || \
sudo systemctl restart php${PHP_MAJOR_MINOR}-fpm 2>/dev/null || \
sudo systemctl restart php8.3-fpm 2>/dev/null || \
sudo systemctl restart php8.2-fpm 2>/dev/null || \
sudo systemctl restart php-fpm 2>/dev/null || \
systemctl restart php8.4-fpm 2>/dev/null || \
systemctl restart php${PHP_MAJOR_MINOR}-fpm 2>/dev/null || \
systemctl restart php8.3-fpm 2>/dev/null || \
systemctl restart php8.2-fpm 2>/dev/null || \
systemctl restart php-fpm 2>/dev/null || \
print_warning "Could not restart PHP-FPM. Try: sudo systemctl restart php8.4-fpm"

# Reload Nginx/Apache
print_info "Reloading web server..."
sudo systemctl reload nginx 2>/dev/null || \
sudo systemctl reload apache2 2>/dev/null || \
systemctl reload nginx 2>/dev/null || \
systemctl reload apache2 2>/dev/null || \
print_warning "Could not reload web server. Try: sudo systemctl reload nginx"

# Restart queue workers (if using queues)
# systemctl restart kewasnet-worker 2>/dev/null || print_info "No queue workers to restart"

# Process any pending emails in queue immediately
print_info "Processing pending emails in queue..."
php spark email:process || print_info "No emails to process"

# Restart WebSocket server (if using Ratchet)
print_info "Restarting WebSocket server..."
sudo systemctl restart kewasnet-websocket 2>/dev/null || \
systemctl restart kewasnet-websocket 2>/dev/null || \
print_info "WebSocket service not configured"

print_success "Services restarted"

###############################################################################
# Disable Maintenance Mode
###############################################################################

print_header "Disabling Maintenance Mode"

rm -f "$DEPLOY_PATH/public/maintenance.html" 2>/dev/null || \
sudo rm -f "$DEPLOY_PATH/public/maintenance.html" 2>/dev/null || true
print_success "Maintenance mode disabled"

###############################################################################
# Post-Deployment Tasks
###############################################################################

print_header "Post-Deployment Tasks"

# Test application
print_info "Testing application..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "https://$DOMAIN" || echo "000")
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "301" ] || [ "$HTTP_CODE" = "302" ]; then
    print_success "Application is responding (HTTP $HTTP_CODE)"
else
    print_error "Application is not responding properly (HTTP $HTTP_CODE)"
    print_warning "Check logs at: $DEPLOY_PATH/writable/logs/"
fi

# Clean old backups (keep only last 5)
print_info "Cleaning old backups..."
cd "$DEPLOY_PATH/backups" 2>/dev/null && ls -t | tail -n +6 | xargs sudo rm -rf 2>/dev/null || \
cd "$DEPLOY_PATH/backups" 2>/dev/null && ls -t | tail -n +6 | xargs rm -rf 2>/dev/null || true
print_success "Old backups cleaned"

###############################################################################
# Deployment Summary
###############################################################################

print_header "Deployment Summary"

echo -e "${GREEN}
╔════════════════════════════════════════════════════════════════╗
║                  DEPLOYMENT COMPLETED SUCCESSFULLY              ║
╚════════════════════════════════════════════════════════════════╝
${NC}"

echo -e "${BLUE}Environment:${NC}       $ENVIRONMENT"
echo -e "${BLUE}Domain:${NC}            https://$DOMAIN"
echo -e "${BLUE}Deployment Path:${NC}  $DEPLOY_PATH"
echo -e "${BLUE}Backup Location:${NC}  $DEPLOY_PATH/$BACKUP_DIR"
echo -e "${BLUE}Timestamp:${NC}        $TIMESTAMP"
echo -e "${BLUE}Git Commit:${NC}       $(git rev-parse --short HEAD)"
echo -e "${BLUE}PHP Version:${NC}      $PHP_VERSION"

echo -e "\n${YELLOW}Important Post-Deployment Checks:${NC}"
echo -e "  1. Visit https://$DOMAIN and verify the site is working"
echo -e "  2. Check application logs: tail -f $DEPLOY_PATH/writable/logs/log-$(date +%Y-%m-%d).log"
echo -e "  3. Monitor error logs: tail -f /var/log/nginx/error.log"
echo -e "  4. Test critical functionality (login, forms, payments, etc.)"
echo -e "  5. Check WebSocket connection if using real-time features"
echo -e "  6. Verify email queue cron: crontab -l | grep 'email:process'"
echo -e "  7. Monitor email queue: php spark email:process"

echo -e "\n${GREEN}Deployment completed at $(date)${NC}\n"

# Send deployment notification (optional - configure your notification method)
# curl -X POST -H 'Content-type: application/json' \
#   --data "{\"text\":\"✓ KEWASNET deployed to $ENVIRONMENT at $(date)\"}" \
#   YOUR_SLACK_WEBHOOK_URL

exit 0