# Quick Deployment Guide

This is a quick reference guide for deploying the KEWASNET application. For detailed server requirements and configurations, see [SERVER_REQUIREMENTS.md](SERVER_REQUIREMENTS.md).

## Prerequisites

- Ubuntu 20.04/22.04 LTS server
- Root or sudo access
- Domain name pointed to your server
- SSH access configured

## Quick Setup (Production)

### 1. Initial Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required software
sudo apt install -y software-properties-common curl wget git unzip

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 and extensions
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-intl \
    php8.2-bcmath php8.2-soap php8.2-opcache

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- \
    --install-dir=/usr/local/bin --filename=composer

# Install Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Create Database

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE kewasnet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kewasnet_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON kewasnet.* TO 'kewasnet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Clone Application

```bash
# Create deployment directory
sudo mkdir -p /var/www/kewasnet.co.ke
cd /var/www/kewasnet.co.ke

# Clone repository (update with your repo URL)
sudo git clone https://github.com/yourusername/kewasnet-website.git .

# Or upload files via SFTP/SCP
```

### 4. Install Dependencies

```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies
npm ci --production

# Build assets
npm run build
```

### 5. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit environment file
nano .env
```

Update these critical values:

```env
CI_ENVIRONMENT = production
app.baseURL = 'https://kewasnet.co.ke/'
database.default.database = kewasnet
database.default.username = kewasnet_user
database.default.password = your_database_password
encryption.key = your_32_character_key_here
```

### 6. Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/kewasnet.co.ke

# Set permissions
sudo find /var/www/kewasnet.co.ke -type d -exec chmod 755 {} \;
sudo find /var/www/kewasnet.co.ke -type f -exec chmod 644 {} \;

# Writable directories
sudo chmod -R 775 /var/www/kewasnet.co.ke/writable
sudo chmod -R 775 /var/www/kewasnet.co.ke/public/uploads

# Make spark executable
sudo chmod +x /var/www/kewasnet.co.ke/spark
```

### 7. Run Migrations

```bash
cd /var/www/kewasnet.co.ke
php spark migrate
```

### 8. Configure Nginx

```bash
# Create Nginx configuration
sudo nano /etc/nginx/sites-available/kewasnet.co.ke
```

Paste this configuration:

```nginx
server {
    listen 80;
    server_name kewasnet.co.ke www.kewasnet.co.ke;
    root /var/www/kewasnet.co.ke/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/kewasnet.co.ke /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 9. Install SSL Certificate

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d kewasnet.co.ke -d www.kewasnet.co.ke
```

### 10. Configure Firewall

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

## Using the Deployment Script

### First Time Setup

1. Make the deployment script executable:

```bash
chmod +x deploy.sh
```

2. Edit the script to configure your server paths:

```bash
nano deploy.sh
```

Update these variables:

```bash
DEPLOY_PATH="/var/www/kewasnet.co.ke"
DOMAIN="kewasnet.co.ke"
```

3. Update git remote URL in the script to point to your repository.

### Deploy to Production

```bash
./deploy.sh production
```

### Deploy to Staging

```bash
./deploy.sh staging
```

## Post-Deployment

### Verify Installation

```bash
# Check if site is accessible
curl -I https://kewasnet.co.ke

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check Nginx status
sudo systemctl status nginx

# Check application logs
tail -f /var/www/kewasnet.co.ke/writable/logs/log-$(date +%Y-%m-%d).log
```

### Set Up Cron Jobs

```bash
sudo crontab -e
```

Add these entries:

```cron
# Run queue workers every minute (if using queues)
* * * * * cd /var/www/kewasnet.co.ke && php spark queue:work --max-time=60 >> /dev/null 2>&1

# Generate sitemap daily at 3 AM
0 3 * * * cd /var/www/kewasnet.co.ke && php spark sitemap:generate >> /dev/null 2>&1

# Clean old sessions daily at 4 AM
0 4 * * * find /var/www/kewasnet.co.ke/writable/session -mtime +1 -delete

# Daily backup at 2 AM
0 2 * * * /usr/local/bin/backup-kewasnet.sh
```

### Set Up Monitoring

```bash
# Install basic monitoring
sudo apt install -y htop iotop nethogs

# Optional: Install Netdata for advanced monitoring
bash <(curl -Ss https://my-netdata.io/kickstart.sh)
```

## Troubleshooting

### Application Not Loading

```bash
# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Check application logs
tail -f /var/www/kewasnet.co.ke/writable/logs/log-$(date +%Y-%m-%d).log
```

### Permission Issues

```bash
# Reset permissions
sudo chown -R www-data:www-data /var/www/kewasnet.co.ke
sudo chmod -R 775 /var/www/kewasnet.co.ke/writable
sudo chmod -R 775 /var/www/kewasnet.co.ke/public/uploads
```

### Database Connection Issues

```bash
# Test database connection
mysql -u kewasnet_user -p kewasnet

# Check .env file
cat /var/www/kewasnet.co.ke/.env | grep database
```

### Clear Cache

```bash
cd /var/www/kewasnet.co.ke
php spark cache:clear
rm -rf writable/cache/*
```

### SSL Certificate Issues

```bash
# Test SSL certificate
sudo certbot certificates

# Renew certificate
sudo certbot renew --dry-run
sudo certbot renew
```

## Rollback to Previous Version

If deployment fails, restore from backup:

```bash
# Find latest backup
ls -lt /var/www/kewasnet.co.ke/backups/

# Restore database
cd /var/www/kewasnet.co.ke/backups/[TIMESTAMP]
gunzip database_backup.sql.gz
mysql -u kewasnet_user -p kewasnet < database_backup.sql

# Restore files
tar -xzf files_backup.tar.gz -C /var/www/kewasnet.co.ke/

# Restore uploads
tar -xzf uploads_backup.tar.gz -C /var/www/kewasnet.co.ke/public/

# Restore .env
cp .env.backup /var/www/kewasnet.co.ke/.env

# Restart services
sudo systemctl restart php8.2-fpm nginx
```

## Update Deployment Script

To update repository URL in the deployment script:

```bash
nano deploy.sh
```

Find this line and update:

```bash
git remote add origin https://github.com/yourusername/kewasnet-website.git
```

## Security Checklist

- [ ] SSL certificate installed and auto-renewal configured
- [ ] Firewall (UFW) enabled with only necessary ports open
- [ ] .env file has secure passwords and encryption key
- [ ] File permissions set correctly (755 for directories, 644 for files)
- [ ] Writable directories have 775 permissions
- [ ] Fail2Ban installed and configured
- [ ] Database root password secured
- [ ] SSH key authentication enabled
- [ ] Regular backups scheduled
- [ ] Security headers configured in Nginx
- [ ] OPcache enabled for performance
- [ ] Redis installed for caching (optional but recommended)

## Support

For detailed configuration options and advanced setup, see:
- [SERVER_REQUIREMENTS.md](SERVER_REQUIREMENTS.md) - Complete server requirements and configuration
- [deploy.sh](deploy.sh) - Automated deployment script

## Quick Reference Commands

```bash
# Deploy to production
./deploy.sh production

# Check logs
tail -f /var/www/kewasnet.co.ke/writable/logs/log-$(date +%Y-%m-%d).log

# Restart services
sudo systemctl restart php8.2-fpm nginx

# Clear cache
php spark cache:clear

# Run migrations
php spark migrate

# Check disk space
df -h

# Check memory usage
free -h

# Monitor processes
htop
```
