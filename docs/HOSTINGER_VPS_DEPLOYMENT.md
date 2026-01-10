# Hostinger VPS Deployment Guide for CodeIgniter 4

## Table of Contents
1. [Introduction](#introduction)
2. [Prerequisites](#prerequisites)
3. [Accessing Your Hostinger VPS](#accessing-your-hostinger-vps)
4. [Initial Server Setup](#initial-server-setup)
5. [Installing Required Software](#installing-required-software)
6. [Application Deployment](#application-deployment)
7. [Database Configuration](#database-configuration)
8. [Environment Configuration](#environment-configuration)
9. [Web Server Configuration (Nginx)](#web-server-configuration-nginx)
10. [SSL Certificate Setup](#ssl-certificate-setup)
11. [File Permissions and Security](#file-permissions-and-security)
12. [Cron Jobs Setup](#cron-jobs-setup)
13. [Backup Configuration](#backup-configuration)
14. [Monitoring and Maintenance](#monitoring-and-maintenance)
15. [Troubleshooting](#troubleshooting)
16. [Post-Deployment Checklist](#post-deployment-checklist)

---

## Introduction

This guide provides step-by-step instructions for deploying the KEWASNET CodeIgniter 4 application on a Hostinger VPS. Hostinger VPS typically comes with Ubuntu 20.04/22.04 LTS and may include hPanel control panel.

**Estimated Time:** 2-3 hours for initial setup

---

## Prerequisites

Before starting, ensure you have:

- [ ] Hostinger VPS account with root/SSH access
- [ ] Domain name pointed to your VPS IP address
- [ ] SSH client (PuTTY for Windows, Terminal for Mac/Linux)
- [ ] FTP/SFTP client (FileZilla, WinSCP, or similar)
- [ ] Access to your Hostinger hPanel (if available)
- [ ] Git repository access (or prepared application files)
- [ ] Basic knowledge of Linux command line

**VPS Requirements:**
- **Minimum:** 2 CPU cores, 4GB RAM, 50GB SSD
- **Recommended:** 4 CPU cores, 8GB RAM, 100GB SSD
- **OS:** Ubuntu 20.04/22.04 LTS (most common on Hostinger)

---

## Accessing Your Hostinger VPS

### Method 1: SSH Access (Recommended)

1. **Get SSH Credentials from Hostinger:**
   - Log into your Hostinger account
   - Navigate to VPS section
   - Find your VPS and click "Manage"
   - Look for "SSH Access" or "Server Details"
   - Note your:
     - Server IP address
     - SSH port (usually 22)
     - Root password or SSH key

2. **Connect via SSH:**
   
   **Windows (PuTTY):**
   ```
   Host: your-server-ip
   Port: 22
   Connection type: SSH
   ```
   
   **Mac/Linux (Terminal):**
   ```bash
   ssh root@your-server-ip
   # Enter password when prompted
   ```

3. **Verify Connection:**
   ```bash
   whoami  # Should show: root
   pwd     # Should show: /root
   ```

### Method 2: Hostinger hPanel (If Available)

1. Log into Hostinger account
2. Navigate to VPS → hPanel
3. Use File Manager for file uploads
4. Use Terminal/SSH for command execution

**Note:** For deployment, SSH access is highly recommended as it's faster and more reliable.

---

## Initial Server Setup

### 1. Update System Packages

```bash
# Update package list
apt update && apt upgrade -y

# Install essential tools
apt install -y software-properties-common curl wget git unzip build-essential
```

### 2. Create Deployment User (Optional but Recommended)

```bash
# Create a non-root user for deployment
adduser deploy
usermod -aG sudo deploy
usermod -aG www-data deploy

# Switch to deploy user
su - deploy

# Set up SSH key (if you have one)
mkdir -p ~/.ssh
chmod 700 ~/.ssh
# Add your public SSH key to ~/.ssh/authorized_keys
nano ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### 3. Configure Firewall (UFW)

```bash
# Install UFW if not installed
apt install -y ufw

# Allow SSH (IMPORTANT: Do this first!)
ufw allow 22/tcp

# Allow HTTP and HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Enable firewall
ufw enable

# Check status
ufw status verbose
```

**Warning:** Make sure SSH (port 22) is allowed before enabling the firewall, or you may lock yourself out!

---

## Installing Required Software

### 1. Install PHP 8.2

```bash
# Add PHP repository
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.2 and required extensions
apt install -y php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl \
    php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath \
    php8.2-soap php8.2-opcache php8.2-readline

# Verify installation
php -v  # Should show PHP 8.2.x
php -m  # List installed extensions
```

### 2. Install MySQL/MariaDB

```bash
# Install MySQL
apt install -y mysql-server mysql-client

# Secure MySQL installation
mysql_secure_installation
```

Follow the prompts:
- Set root password (choose a strong password)
- Remove anonymous users? **Yes**
- Disallow root login remotely? **Yes**
- Remove test database? **Yes**
- Reload privilege tables? **Yes**

### 3. Install Nginx

```bash
# Install Nginx
apt install -y nginx

# Start and enable Nginx
systemctl start nginx
systemctl enable nginx

# Check status
systemctl status nginx
```

### 4. Install Composer

```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Verify installation
composer --version
```

### 5. Install Node.js and NPM

```bash
# Install Node.js 18.x LTS
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Verify installation
node -v  # Should show v18.x.x
npm -v   # Should show 9.x.x or higher
```

### 6. Install Redis (Optional but Recommended)

```bash
# Install Redis
apt install -y redis-server

# Start and enable Redis
systemctl start redis-server
systemctl enable redis-server

# Test Redis
redis-cli ping  # Should return: PONG
```

---

## Application Deployment

### 1. Create Application Directory

```bash
# Create directory for your application
mkdir -p /var/www/kewasnet.co.ke
cd /var/www/kewasnet.co.ke
```

**Note:** Replace `kewasnet.co.ke` with your actual domain name.

### 2. Deploy Application Files

**Option A: Using Git (Recommended)**

```bash
# Clone your repository
git clone https://github.com/yourusername/kewasnet-website.git .

# Or if repository is private, use SSH:
# git clone git@github.com:yourusername/kewasnet-website.git .
```

**Option B: Upload via SFTP**

1. Use FileZilla, WinSCP, or similar
2. Connect to your server via SFTP
3. Upload all application files to `/var/www/kewasnet.co.ke/`
4. Ensure hidden files (like `.env.example`) are uploaded

### 3. Install Dependencies

```bash
cd /var/www/kewasnet.co.ke

# Install PHP dependencies (production mode)
composer install --no-dev --optimize-autoloader

# Install NPM dependencies
npm ci --production

# Build assets (if you have build scripts)
npm run build
```

### 4. Set Up Environment File

```bash
# Copy environment file
cp .env.example .env

# Edit environment file
nano .env
```

Update these critical values:

```env
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'https://yourdomain.com/'
app.indexPage = ''
app.forceGlobalSecureRequests = true

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = kewasnet
database.default.username = deploy_kewasnet
database.default.password = Deploy@kewasnet2026
database.default.DBDriver = MySQLi
database.default.port = 3306

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------
encryption.key = your_32_character_encryption_key_here
```

**Generate Encryption Key:**
```bash
php spark key:generate
```

This will automatically update your `.env` file with a secure encryption key.

---

## Database Configuration

### 1. Create Database and User

```bash
# Log into MySQL
mysql -u root -p
```

Enter your MySQL root password, then run:

```sql
-- Create database
CREATE DATABASE kewasnet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'kewasnet_user'@'localhost' IDENTIFIED BY 'your_strong_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON kewasnet.* TO 'kewasnet_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Verify
SHOW DATABASES;
EXIT;
```

### 2. Run Migrations

```bash
cd /var/www/kewasnet.co.ke

# Run all migrations
php spark migrate

# If you need to seed initial data
php spark db:seed AllSeeder
```

### 3. Verify Database Connection

```bash
# Test database connection
php spark db:table users
```

If successful, you should see table information without errors.

---

## Web Server Configuration (Nginx)

### 1. Create Nginx Configuration

```bash
# Create site configuration
nano /etc/nginx/sites-available/kewasnet.co.ke
```

Paste the following configuration (replace `kewasnet.co.ke` with your domain):

```nginx
# HTTP to HTTPS Redirect
server {
    listen 80;
    listen [::]:80;
    server_name kewasnet.co.ke www.kewasnet.co.ke;
    
    # Let's Encrypt validation
    location ^~ /.well-known/acme-challenge/ {
        default_type "text/plain";
        root /var/www/kewasnet.co.ke/public;
    }
    
    location / {
        return 301 https://kewasnet.co.ke$request_uri;
    }
}

# HTTPS Server Block
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name kewasnet.co.ke www.kewasnet.co.ke;
    
    root /var/www/kewasnet.co.ke/public;
    index index.php index.html;
    
    # SSL Configuration (will be updated by Certbot)
    ssl_certificate /etc/letsencrypt/live/kewasnet.co.ke/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/kewasnet.co.ke/privkey.pem;
    
    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Logging
    access_log /var/log/nginx/kewasnet-access.log;
    error_log /var/log/nginx/kewasnet-error.log;
    
    # Character set
    charset utf-8;
    
    # Disable access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Deny access to sensitive files
    location ~ /(\.env|\.git|composer\.json|composer\.lock|package\.json|phpunit\.xml) {
        deny all;
        return 404;
    }
    
    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Main location block
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM Configuration
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # FastCGI Buffers
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }
}
```

### 2. Enable Site

```bash
# Create symbolic link
ln -s /etc/nginx/sites-available/kewasnet.co.ke /etc/nginx/sites-enabled/

# Remove default site (optional)
rm /etc/nginx/sites-enabled/default

# Test Nginx configuration
nginx -t

# If test passes, restart Nginx
systemctl restart nginx
```

---

## SSL Certificate Setup

### Option 1: Using Certbot (Let's Encrypt) - Recommended

```bash
# Install Certbot
apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
certbot --nginx -d kewasnet.co.ke -d www.kewasnet.co.ke

# Follow the prompts:
# - Enter your email address
# - Agree to terms of service
# - Choose whether to redirect HTTP to HTTPS (recommended: Yes)
```

Certbot will automatically:
- Obtain the certificate
- Update your Nginx configuration
- Set up auto-renewal

**Test auto-renewal:**
```bash
certbot renew --dry-run
```

### Option 2: Using Hostinger SSL (If Available)

1. Log into Hostinger hPanel
2. Navigate to SSL section
3. Select "Free SSL" or "Let's Encrypt"
4. Follow Hostinger's instructions
5. Update Nginx configuration manually if needed

---

## File Permissions and Security

### 1. Set Correct Ownership

```bash
# Set ownership to www-data
chown -R www-data:www-data /var/www/kewasnet.co.ke

# If you created a deploy user, add them to www-data group
usermod -aG www-data deploy
```

### 2. Set File Permissions

```bash
cd /var/www/kewasnet.co.ke

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Writable directories (CodeIgniter specific)
chmod -R 775 writable/
chmod -R 775 public/uploads/

# Make spark executable
chmod +x spark

# Protect .env file
chmod 600 .env
```

### 3. Configure PHP Security

Edit PHP configuration:

```bash
nano /etc/php/8.2/fpm/php.ini
```

Update these settings:

```ini
; Basic Settings
memory_limit = 512M
max_execution_time = 300
post_max_size = 100M
upload_max_filesize = 100M

; Date & Time
date.timezone = Africa/Nairobi

; Error Handling (Production)
display_errors = Off
display_startup_errors = Off
log_errors = On

; Security
expose_php = Off
allow_url_fopen = On
allow_url_include = Off

; OPcache (Performance)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0
```

Restart PHP-FPM:

```bash
systemctl restart php8.2-fpm
```

### 4. Install Fail2Ban (Security)

```bash
# Install Fail2Ban
apt install -y fail2ban

# Create local configuration
cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edit configuration
nano /etc/fail2ban/jail.local
```

Add/modify:

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[nginx-http-auth]
enabled = true
```

Restart Fail2Ban:

```bash
systemctl restart fail2ban
systemctl enable fail2ban
```

---

## Cron Jobs Setup

### 1. Set Up Cron Jobs

```bash
# Edit crontab
crontab -e
```

Add these entries (adjust paths as needed):

```cron
# Run queue workers every minute (if using queues)
* * * * * cd /var/www/kewasnet.co.ke && php spark queue:work --max-time=60 >> /dev/null 2>&1

# Generate sitemap daily at 3 AM
0 3 * * * cd /var/www/kewasnet.co.ke && php spark sitemap:generate >> /dev/null 2>&1

# Clean old sessions daily at 4 AM
0 4 * * * find /var/www/kewasnet.co.ke/writable/session -mtime +1 -delete

# Clear cache daily at 5 AM
0 5 * * * cd /var/www/kewasnet.co.ke && php spark cache:clear >> /dev/null 2>&1
```

### 2. Verify Cron Jobs

```bash
# List current cron jobs
crontab -l

# Check cron service status
systemctl status cron
```

---

## Backup Configuration

### 1. Create Backup Script

```bash
# Create backup directory
mkdir -p /backups/kewasnet

# Create backup script
nano /usr/local/bin/backup-kewasnet.sh
```

Paste this script:

```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/backups/kewasnet"
APP_DIR="/var/www/kewasnet.co.ke"
DB_NAME="kewasnet"
DB_USER="kewasnet_user"
DB_PASS="your_database_password"
RETENTION_DAYS=7

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Timestamp
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Backup database
mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/db_$TIMESTAMP.sql.gz"

# Backup application files (excluding vendor, node_modules, cache)
tar -czf "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" \
    -C "$APP_DIR" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='writable/cache' \
    --exclude='writable/session' \
    --exclude='writable/debugbar' \
    --exclude='.git' \
    .

# Backup uploads separately
tar -czf "$BACKUP_DIR/uploads_$TIMESTAMP.tar.gz" \
    -C "$APP_DIR/public" \
    uploads

# Remove old backups
find "$BACKUP_DIR" -name "*.gz" -mtime +$RETENTION_DAYS -delete

# Log backup
echo "$(date): Backup completed - $TIMESTAMP" >> /var/log/kewasnet-backup.log
```

Make it executable:

```bash
chmod +x /usr/local/bin/backup-kewasnet.sh
```

### 2. Schedule Backups

```bash
# Edit crontab
crontab -e
```

Add backup schedule (daily at 2 AM):

```cron
0 2 * * * /usr/local/bin/backup-kewasnet.sh
```

### 3. Test Backup

```bash
# Run backup manually
/usr/local/bin/backup-kewasnet.sh

# Verify backup files
ls -lh /backups/kewasnet/
```

---

## Monitoring and Maintenance

### 1. Set Up Log Rotation

```bash
# Create logrotate configuration
nano /etc/logrotate.d/kewasnet
```

Add:

```
/var/www/kewasnet.co.ke/writable/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        systemctl reload php8.2-fpm > /dev/null
    endscript
}

/var/log/nginx/kewasnet*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    postrotate
        if [ -f /var/run/nginx.pid ]; then
            kill -USR1 `cat /var/run/nginx.pid`
        fi
    endscript
}
```

### 2. Monitor Application Logs

```bash
# View application logs
tail -f /var/www/kewasnet.co.ke/writable/logs/log-$(date +%Y-%m-%d).log

# View Nginx error logs
tail -f /var/log/nginx/kewasnet-error.log

# View PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### 3. Monitor Server Resources

```bash
# Install monitoring tools
apt install -y htop iotop nethogs

# Check disk usage
df -h

# Check memory usage
free -h

# Monitor processes
htop
```

---

## Troubleshooting

### Common Issues and Solutions

#### 1. Application Not Loading

**Symptoms:** Blank page or 500 error

**Solutions:**
```bash
# Check Nginx error logs
tail -f /var/log/nginx/kewasnet-error.log

# Check PHP-FPM logs
tail -f /var/log/php8.2-fpm.log

# Check application logs
tail -f /var/www/kewasnet.co.ke/writable/logs/log-$(date +%Y-%m-%d).log

# Verify file permissions
ls -la /var/www/kewasnet.co.ke/public/index.php

# Test PHP-FPM
systemctl status php8.2-fpm
```

#### 2. Permission Denied Errors

**Symptoms:** Cannot write to files, upload fails

**Solutions:**
```bash
# Reset ownership
chown -R www-data:www-data /var/www/kewasnet.co.ke

# Reset writable permissions
chmod -R 775 /var/www/kewasnet.co.ke/writable
chmod -R 775 /var/www/kewasnet.co.ke/public/uploads
```

#### 3. Database Connection Failed

**Symptoms:** Database errors in logs

**Solutions:**
```bash
# Test database connection
mysql -u kewasnet_user -p kewasnet

# Check .env file
cat /var/www/kewasnet.co.ke/.env | grep database

# Verify MySQL is running
systemctl status mysql

# Check MySQL error log
tail -f /var/log/mysql/error.log
```

#### 4. SSL Certificate Issues

**Symptoms:** SSL errors, certificate expired

**Solutions:**
```bash
# Check certificate status
certbot certificates

# Renew certificate manually
certbot renew

# Test renewal
certbot renew --dry-run

# Check Nginx SSL configuration
nginx -t
```

#### 5. 404 Errors on Routes

**Symptoms:** Routes not working, always 404

**Solutions:**
```bash
# Verify .htaccess exists in public directory
ls -la /var/www/kewasnet.co.ke/public/.htaccess

# Check Nginx try_files configuration
grep -A 2 "try_files" /etc/nginx/sites-available/kewasnet.co.ke

# Test Nginx configuration
nginx -t

# Restart Nginx
systemctl restart nginx
```

#### 6. High Memory Usage

**Symptoms:** Server slow, out of memory errors

**Solutions:**
```bash
# Check memory usage
free -h
htop

# Adjust PHP-FPM pool settings
nano /etc/php/8.2/fpm/pool.d/www.conf

# Reduce max_children if needed
# Restart PHP-FPM
systemctl restart php8.2-fpm
```

#### 7. Can't Access via Domain

**Symptoms:** Site works via IP but not domain

**Solutions:**
1. **Check DNS Settings:**
   - Verify A record points to your VPS IP
   - Check DNS propagation: `nslookup yourdomain.com`
   - Wait for DNS propagation (can take up to 48 hours)

2. **Check Nginx Configuration:**
   ```bash
   # Verify server_name matches your domain
   grep server_name /etc/nginx/sites-available/kewasnet.co.ke
   
   # Test configuration
   nginx -t
   ```

#### 8. Hostinger-Specific Issues

**Issue: Port 80/443 Blocked**

If Hostinger blocks ports, check:
- Hostinger firewall settings in hPanel
- VPS firewall rules
- Contact Hostinger support if needed

**Issue: Pre-installed Software Conflicts**

If Hostinger has pre-installed software:
```bash
# Check what's installed
dpkg -l | grep -E "apache|nginx|php|mysql"

# Stop conflicting services
systemctl stop apache2  # If Apache is installed
systemctl disable apache2
```

---

## Post-Deployment Checklist

Use this checklist to ensure everything is properly configured:

### Server Configuration
- [ ] System packages updated
- [ ] Firewall (UFW) configured and enabled
- [ ] SSH access secured
- [ ] Non-root user created (optional)

### Software Installation
- [ ] PHP 8.2 installed with all required extensions
- [ ] MySQL/MariaDB installed and secured
- [ ] Nginx installed and configured
- [ ] Composer installed
- [ ] Node.js and NPM installed
- [ ] Redis installed (optional)

### Application Setup
- [ ] Application files deployed
- [ ] Composer dependencies installed
- [ ] NPM dependencies installed (if applicable)
- [ ] `.env` file configured with production settings
- [ ] Encryption key generated
- [ ] File permissions set correctly

### Database
- [ ] Database created
- [ ] Database user created with proper permissions
- [ ] Migrations run successfully
- [ ] Database connection tested

### Web Server
- [ ] Nginx configuration created
- [ ] Site enabled and default site removed
- [ ] Nginx configuration tested
- [ ] Nginx restarted successfully

### SSL/TLS
- [ ] SSL certificate obtained (Let's Encrypt)
- [ ] Auto-renewal configured
- [ ] HTTPS redirect working
- [ ] SSL certificate valid

### Security
- [ ] File permissions set correctly
- [ ] `.env` file protected (600 permissions)
- [ ] Fail2Ban installed and configured
- [ ] Security headers configured in Nginx
- [ ] PHP security settings configured

### Cron Jobs
- [ ] Cron jobs configured
- [ ] Cron service running
- [ ] Cron jobs tested

### Backups
- [ ] Backup script created
- [ ] Backup script executable
- [ ] Backup scheduled in crontab
- [ ] Backup tested successfully

### Monitoring
- [ ] Log rotation configured
- [ ] Application logs accessible
- [ ] Server monitoring tools installed
- [ ] Resource monitoring set up

### Testing
- [ ] Application accessible via domain
- [ ] HTTPS working correctly
- [ ] All routes working
- [ ] Database operations working
- [ ] File uploads working
- [ ] No errors in logs

### Documentation
- [ ] Deployment documented
- [ ] Backup/restore procedures documented
- [ ] Troubleshooting guide accessible
- [ ] Credentials securely stored

---

## Quick Reference Commands

### Service Management
```bash
# Restart services
systemctl restart nginx
systemctl restart php8.2-fpm
systemctl restart mysql

# Check service status
systemctl status nginx
systemctl status php8.2-fpm
systemctl status mysql

# Enable services on boot
systemctl enable nginx
systemctl enable php8.2-fpm
systemctl enable mysql
```

### Application Commands
```bash
# Clear cache
cd /var/www/kewasnet.co.ke && php spark cache:clear

# Run migrations
cd /var/www/kewasnet.co.ke && php spark migrate

# View logs
tail -f /var/www/kewasnet.co.ke/writable/logs/log-$(date +%Y-%m-%d).log

# Check routes
cd /var/www/kewasnet.co.ke && php spark routes
```

### System Monitoring
```bash
# Check disk usage
df -h

# Check memory usage
free -h

# Check CPU usage
top
htop

# Check network connections
netstat -tulpn
```

### Backup and Restore
```bash
# Run backup manually
/usr/local/bin/backup-kewasnet.sh

# List backups
ls -lh /backups/kewasnet/

# Restore database
gunzip < /backups/kewasnet/db_YYYYMMDD_HHMMSS.sql.gz | mysql -u kewasnet_user -p kewasnet
```

---

## Hostinger Support Resources

If you encounter issues specific to Hostinger:

1. **Hostinger Knowledge Base:**
   - https://www.hostinger.com/tutorials

2. **Hostinger Support:**
   - Available 24/7 via live chat
   - Email support
   - Ticket system in hPanel

3. **Hostinger VPS Documentation:**
   - Check Hostinger's VPS-specific guides
   - SSH access instructions
   - hPanel usage guides

---

## Additional Notes

### Performance Optimization

1. **Enable OPcache** (already configured in PHP)
2. **Use Redis for caching** (if installed)
3. **Enable Gzip compression** (already in Nginx config)
4. **Optimize database queries** (use indexes)
5. **Use CDN for static assets** (optional)

### Security Best Practices

1. **Regular Updates:**
   ```bash
   apt update && apt upgrade -y
   ```

2. **Monitor Logs Regularly:**
   - Check application logs daily
   - Review Nginx access logs for suspicious activity
   - Monitor failed login attempts

3. **Keep Backups:**
   - Test backup restoration regularly
   - Keep backups off-server (cloud storage)

4. **SSL Certificate Renewal:**
   - Certbot auto-renewal is configured
   - Monitor renewal emails from Let's Encrypt

---

## Conclusion

Your CodeIgniter 4 application should now be successfully deployed on your Hostinger VPS. 

**Next Steps:**
1. Test all application features
2. Monitor logs for the first few days
3. Set up additional monitoring if needed
4. Schedule regular maintenance windows
5. Document any custom configurations

**Need Help?**
- Review the troubleshooting section
- Check application logs
- Contact Hostinger support for VPS-specific issues
- Refer to CodeIgniter 4 documentation for application issues

---

**Last Updated:** January 2025  
**Document Version:** 1.0  
**Compatible with:** Hostinger VPS, Ubuntu 20.04/22.04, CodeIgniter 4.x

