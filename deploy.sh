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
#   - PHP 8.2 (specifically configured for this application)
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

# PHP version to use
PHP_VERSION="8.2"
PHP_BIN="/usr/bin/php$PHP_VERSION"

# Deployment paths (update these based on your server structure)
if [ "$ENVIRONMENT" = "production" ]; then
    DEPLOY_PATH="/var/www/html/kewasnet-website"
    DOMAIN="kewasnet.co.ke"  # Update with your actual domain
elif [ "$ENVIRONMENT" = "staging" ]; then
    DEPLOY_PATH="/var/www/html/kewasnet-website-staging"
    DOMAIN="staging.kewasnet.co.ke"  # Update with your actual staging domain
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

# Helper function to run PHP commands with specific version
run_php() {
    $PHP_BIN "$@"
}

# Helper function to run Composer with specific PHP version
run_composer() {
    $PHP_BIN $(which composer) "$@"
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
check_command "npm"
check_command "$PHP_BIN"

print_success "All required commands are available"

# Verify PHP 8.2 is being used
print_info "Verifying PHP $PHP_VERSION installation..."
CURRENT_PHP=$($PHP_BIN -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
if [ "$CURRENT_PHP" != "$PHP_VERSION" ]; then
    print_error "PHP $PHP_VERSION is required. Current version: $CURRENT_PHP"
    print_info "Please ensure PHP $PHP_VERSION is installed and configured."
    exit 1
fi
print_success "PHP $PHP_VERSION detected"

# Get full PHP version
PHP_VERSION_FULL=$($PHP_BIN -r "echo PHP_VERSION;")
print_info "PHP Version: $PHP_VERSION_FULL"

# Check PHP extensions
print_info "Checking required PHP extensions..."
REQUIRED_EXTENSIONS=("intl" "mbstring" "json" "mysqlnd" "xml" "curl" "gd" "zip" "dom")
MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! $PHP_BIN -m | grep -qi "^$ext$"; then
        MISSING_EXTENSIONS+=("$ext")
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -ne 0 ]; then
    print_error "Missing PHP extensions: ${MISSING_EXTENSIONS[*]}"
    print_info "Install them with:"
    print_info "  sudo apt install -y php$PHP_VERSION-intl php$PHP_VERSION-xml php$PHP_VERSION-dom php$PHP_VERSION-gd php$PHP_VERSION-zip php$PHP_VERSION-mbstring php$PHP_VERSION-curl"
    exit 1
fi
print_success "All required PHP extensions are installed"

# Rest of your script continues here with modifications to use run_php and run_composer...