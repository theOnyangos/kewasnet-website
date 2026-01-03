# KEWASNET Server Requirements & Configuration Guide

## Table of Contents
1. [Server Requirements](#server-requirements)
2. [Server Setup](#server-setup)
3. [PHP Configuration](#php-configuration)
4. [Web Server Configuration](#web-server-configuration)
5. [Database Configuration](#database-configuration)
6. [SSL/TLS Configuration](#ssltls-configuration)
7. [Security Hardening](#security-hardening)
8. [Performance Optimization](#performance-optimization)
9. [Monitoring & Logging](#monitoring--logging)
10. [Backup Strategy](#backup-strategy)

---

## 1. Server Requirements

### Minimum Requirements

#### Operating System
- **Recommended:** Ubuntu 20.04 LTS / 22.04 LTS or Debian 11/12
- **Alternative:** CentOS 8+ / Rocky Linux 8+
- **CPU:** 2 cores minimum (4+ recommended for production)
- **RAM:** 4GB minimum (8GB+ recommended for production)
- **Storage:** 50GB SSD minimum (100GB+ recommended)

#### Software Stack

**PHP**
- **Version:** PHP 8.1 or higher (PHP 8.4 recommended)
- **Required Extensions:**
  - intl (Internationalization)
  - mbstring (Multibyte String)
  - json
  - mysqlnd (MySQL Native Driver)
  - xml
  - curl
  - gd (Image manipulation)
  - zip
  - fileinfo
  - openssl
  - tokenizer
  - pdo
  - pdo_mysql

**Web Server**
- **Option 1:** Nginx 1.18+ (Recommended)
- **Option 2:** Apache 2.4+

**Database**
- **MySQL:** 5.7+ or 8.0+ (Recommended: MySQL 8.0)
- **Alternative:** MariaDB 10.3+

**Additional Software**
- **Composer:** Latest 2.x version
- **Node.js:** 16.x or 18.x LTS
- **NPM:** 8.x or higher
- **Git:** 2.x
- **Supervisor:** For queue workers (optional)
- **Redis:** 6.x or 7.x (for caching and sessions - optional but recommended)

---

## 2. Server Setup

### Initial Server Setup (Ubuntu 22.04)

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install essential tools
sudo apt install -y software-properties-common curl wget git unzip

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 and required extensions
sudo apt install -y php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl \
    php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath \
    php8.2-soap php8.2-opcache php8.2-readline

# Install MySQL 8.0
sudo apt install -y mysql-server mysql-client

# Secure MySQL installation
sudo mysql_secure_installation

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js 18.x LTS
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Redis (optional but recommended)
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Install Supervisor (for queue workers)
sudo apt install -y supervisor
```

### Create Deployment User

```bash
# Create a deployment user
sudo adduser deploy
sudo usermod -aG sudo deploy
sudo usermod -aG www-data deploy

# Set up SSH key authentication for deployment user
sudo su - deploy
mkdir -p ~/.ssh
chmod 700 ~/.ssh
# Add your public SSH key to ~/.ssh/authorized_keys
nano ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

---

## 3. PHP Configuration

### PHP-FPM Configuration

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
; Basic Settings
memory_limit = 512M
max_execution_time = 300
max_input_time = 300
post_max_size = 100M
upload_max_filesize = 100M

; Date & Time
date.timezone = Africa/Nairobi

; Error Handling
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
error_log = /var/log/php/error.log

; Session
session.save_handler = files
session.save_path = "/var/lib/php/sessions"
session.gc_maxlifetime = 7200
session.cookie_secure = On
session.cookie_httponly = On
session.cookie_samesite = "Lax"

; Security
expose_php = Off
allow_url_fopen = On
allow_url_include = Off
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

; OPcache Settings (Important for performance)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0
opcache.revalidate_freq = 0
opcache.fast_shutdown = 1
opcache.enable_cli = 0
```

### PHP-FPM Pool Configuration

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
[www]
user = www-data
group = www-data

listen = /run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

; PHP settings
php_admin_value[error_log] = /var/log/php-fpm/www-error.log
php_admin_flag[log_errors] = on
```

Create log directory and restart PHP-FPM:

```bash
sudo mkdir -p /var/log/php-fpm
sudo chown www-data:www-data /var/log/php-fpm
sudo systemctl restart php8.2-fpm
```

---

## 4. Web Server Configuration

### Nginx Configuration

#### Main Nginx Configuration

Edit `/etc/nginx/nginx.conf`:

```nginx
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 2048;
    multi_accept on;
    use epoll;
}

http {
    # Basic Settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;
    
    # Buffer Settings
    client_body_buffer_size 128k;
    client_max_body_size 100M;
    client_header_buffer_size 1k;
    large_client_header_buffers 4 16k;
    
    # Timeouts
    client_body_timeout 12;
    client_header_timeout 12;
    send_timeout 10;
    
    # MIME Types
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    # Logging
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;
    
    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript 
               application/json application/javascript application/xml+rss 
               application/rss+xml font/truetype font/opentype 
               application/vnd.ms-fontobject image/svg+xml;
    gzip_disable "msie6";
    
    # Virtual Host Configs
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
```

#### Site Configuration for Production

Create `/etc/nginx/sites-available/kewasnet.co.ke`:

```nginx
# Redirect HTTP to HTTPS
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
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/kewasnet.co.ke/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/kewasnet.co.ke/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_stapling on;
    ssl_stapling_verify on;
    
    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;
    
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

Enable the site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/kewasnet.co.ke /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 5. Database Configuration

### MySQL 8.0 Configuration

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
# Basic Settings
user = mysql
pid-file = /var/run/mysqld/mysqld.pid
socket = /var/run/mysqld/mysqld.sock
port = 3306
datadir = /var/lib/mysql

# Character Set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# InnoDB Settings
innodb_buffer_pool_size = 2G
innodb_log_file_size = 512M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Connection Settings
max_connections = 200
max_allowed_packet = 64M

# Query Cache (disabled in MySQL 8.0)
# Use application-level caching instead

# Slow Query Log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

# Error Log
log_error = /var/log/mysql/error.log

# Binary Log (for replication)
# server-id = 1
# log_bin = /var/log/mysql/mysql-bin.log
# binlog_expire_logs_seconds = 604800
```

### Create Database and User

```bash
sudo mysql -u root -p
```

```sql
-- Create database
CREATE DATABASE kewasnet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'kewasnet_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON kewasnet.* TO 'kewasnet_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Verify
SHOW GRANTS FOR 'kewasnet_user'@'localhost';

EXIT;
```

---

## 6. SSL/TLS Configuration

### Install Certbot and Obtain SSL Certificate

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate for your domain
sudo certbot --nginx -d kewasnet.co.ke -d www.kewasnet.co.ke

# Test automatic renewal
sudo certbot renew --dry-run

# Set up automatic renewal (already configured via systemd timer)
sudo systemctl status certbot.timer
```

### Force HTTPS (already configured in Nginx)

The Nginx configuration above already includes:
- HTTP to HTTPS redirect
- Strong SSL/TLS configuration
- HSTS header for enhanced security

---

## 7. Security Hardening

### Firewall Configuration (UFW)

```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow MySQL only from localhost (if using remote connections, adjust accordingly)
sudo ufw deny 3306/tcp

# Check status
sudo ufw status verbose
```

### Fail2Ban Configuration

```bash
# Install Fail2Ban
sudo apt install -y fail2ban

# Create local configuration
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edit jail.local
sudo nano /etc/fail2ban/jail.local
```

Add/modify these settings:

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5
destemail = admin@kewasnet.co.ke
sendername = Fail2Ban
action = %(action_mwl)s

[sshd]
enabled = true
port = 22

[nginx-http-auth]
enabled = true

[nginx-noscript]
enabled = true

[nginx-badbots]
enabled = true

[nginx-noproxy]
enabled = true
```

Restart Fail2Ban:

```bash
sudo systemctl restart fail2ban
sudo systemctl enable fail2ban
```

### File Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/kewasnet.co.ke

# Set directory permissions
sudo find /var/www/kewasnet.co.ke -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/kewasnet.co.ke -type f -exec chmod 644 {} \;

# Writable directories
sudo chmod -R 775 /var/www/kewasnet.co.ke/writable
sudo chmod -R 775 /var/www/kewasnet.co.ke/public/uploads

# Make spark executable
sudo chmod +x /var/www/kewasnet.co.ke/spark

# Protect sensitive files
sudo chmod 600 /var/www/kewasnet.co.ke/.env
```

---

## 8. Performance Optimization

### OPcache Status Monitoring

Create `/var/www/kewasnet.co.ke/public/opcache-status.php`:

```php
<?php
// Protect with authentication
$username = 'admin';
$password = 'your_secure_password';

if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] != $username || 
    $_SERVER['PHP_AUTH_PW'] != $password) {
    header('WWW-Authenticate: Basic realm="OPcache Status"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}

phpinfo(INFO_MODULES);
?>
```

### Redis Configuration for Sessions/Cache

Edit `/etc/redis/redis.conf`:

```conf
# Bind to localhost only
bind 127.0.0.1

# Set max memory (adjust based on available RAM)
maxmemory 256mb
maxmemory-policy allkeys-lru

# Enable persistence (optional)
save 900 1
save 300 10
save 60 10000

# Log file
logfile /var/log/redis/redis-server.log
```

Update CodeIgniter configuration in `.env`:

```env
# Cache Configuration
cache.handler = redis
cache.redis.host = 127.0.0.1
cache.redis.port = 6379
cache.redis.password = 
cache.redis.database = 0

# Session Configuration
session.driver = redis
session.redis.host = 127.0.0.1
session.redis.port = 6379
session.redis.password =
```

---

## 9. Monitoring & Logging

### Log Rotation Configuration

Create `/etc/logrotate.d/kewasnet`:

```conf
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

### System Monitoring with Netdata (Optional)

```bash
# Install Netdata
bash <(curl -Ss https://my-netdata.io/kickstart.sh)

# Access at http://your-server-ip:19999
# Secure it by configuring nginx proxy or firewall rules
```

---

## 10. Backup Strategy

### Automated Backup Script

Create `/usr/local/bin/backup-kewasnet.sh`:

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

# Backup application files
tar -czf "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" \
    -C "$APP_DIR" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='writable/cache' \
    --exclude='writable/session' \
    --exclude='writable/debugbar' \
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

Make it executable and schedule with cron:

```bash
sudo chmod +x /usr/local/bin/backup-kewasnet.sh

# Add to crontab (run daily at 2 AM)
sudo crontab -e
```

Add this line:

```cron
0 2 * * * /usr/local/bin/backup-kewasnet.sh
```

---

## Additional Resources

### Environment Variables (.env file)

Key environment variables for production:

```env
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'https://kewasnet.co.ke/'
app.indexPage = ''
app.forceGlobalSecureRequests = true

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = kewasnet
database.default.username = kewasnet_user
database.default.password = your_database_password
database.default.DBDriver = MySQLi
database.default.port = 3306

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------
encryption.key = your_32_character_encryption_key_here

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------
session.driver = redis
session.cookieName = kewasnet_session
session.expiration = 7200
session.savePath = /var/www/kewasnet.co.ke/writable/session
session.matchIP = false
session.timeToUpdate = 300
session.regenerateDestroy = false

#--------------------------------------------------------------------
# CACHE
#--------------------------------------------------------------------
cache.handler = redis
cache.storePath = /var/www/kewasnet.co.ke/writable/cache/
```

### Deployment Checklist

- [ ] Server meets minimum requirements
- [ ] PHP 8.1+ installed with all required extensions
- [ ] Web server (Nginx/Apache) configured
- [ ] MySQL 8.0+ installed and secured
- [ ] SSL certificate installed and configured
- [ ] Firewall rules configured
- [ ] Fail2Ban installed and configured
- [ ] Redis installed for caching and sessions
- [ ] File permissions set correctly
- [ ] .env file configured for production
- [ ] Database migrations completed
- [ ] Cron jobs configured
- [ ] Backup script set up
- [ ] Monitoring tools configured
- [ ] Log rotation configured
- [ ] Application tested and working

### Useful Commands

```bash
# Check PHP version and extensions
php -v
php -m

# Check Nginx configuration
sudo nginx -t

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo systemctl restart mysql

# View logs
tail -f /var/log/nginx/kewasnet-error.log
tail -f /var/www/kewasnet.co.ke/writable/logs/log-$(date +%Y-%m-%d).log

# Check disk usage
df -h

# Check memory usage
free -h

# Monitor processes
htop
```

---

**Last Updated:** January 2, 2026  
**Document Version:** 1.0
