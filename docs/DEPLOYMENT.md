# KEWASNET Website — VPS Deployment Guide

This guide covers setting up a VPS from scratch and deploying the KEWASNET CodeIgniter 4 application, including automation with GitHub Actions.

---

## Table of Contents

1. [Server Requirements](#1-server-requirements)
2. [Initial Server Setup](#2-initial-server-setup)
3. [Install Server Software](#3-install-server-software)
4. [Database Setup](#4-database-setup)
5. [Application Deployment (Manual)](#5-application-deployment-manual)
6. [Automated Deployment with GitHub Actions](#6-automated-deployment-with-github-actions)
7. [Post-Deployment Checklist](#7-post-deployment-checklist)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Server Requirements

### Minimum VPS Specs

| Resource   | Minimum |
|-----------|---------|
| CPU       | 1 vCPU  |
| RAM       | 1 GB    |
| Storage   | 20 GB   |
| OS        | Ubuntu 22.04 LTS (recommended) or 24.04 LTS |

### Application Requirements

- **PHP**: 8.0, 8.1, 8.2, or 8.3 (8.2+ recommended; avoid 8.4 if `intl` is missing on your distro)
- **Web server**: Nginx or Apache with `mod_rewrite`
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Composer**: 2.x
- **Node.js**: 18+ (optional, for Tailwind/Flowbite if you build assets)
- **Git**

### Required PHP Extensions

- `intl`
- `mbstring`
- `json`
- `mysqlnd`
- `xml`
- `curl`
- `gd`
- `zip`
- `opcache` (recommended for production)

---

## 2. Initial Server Setup

### 2.1 Connect to the VPS

```bash
ssh root@YOUR_SERVER_IP
# Or with a key:
ssh -i ~/.ssh/your_key root@YOUR_SERVER_IP
```

### 2.2 Create a Deploy User (Recommended)

```bash
adduser deploy
usermod -aG sudo deploy
usermod -aG www-data deploy
su - deploy
```

### 2.3 Harden SSH (Optional)

```bash
sudo nano /etc/ssh/sshd_config
# Set: PasswordAuthentication no (after setting up keys)
# Set: PermitRootLogin prohibit-password
sudo systemctl reload sshd
```

### 2.4 Set Timezone

```bash
sudo timedatectl set-timezone Africa/Nairobi
```

### 2.5 Update System

```bash
sudo apt update && sudo apt upgrade -y
```

---

## 3. Install Server Software

### 3.1 Install PHP (Ubuntu 22.04 / 24.04)

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

# PHP 8.2 (recommended) with required extensions
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring \
  php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-opcache

# Verify
php -v
php -m | grep -E 'intl|mbstring|mysqlnd|xml|curl|gd|zip'
```

### 3.2 Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 3.3 Install MySQL

```bash
sudo apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql
sudo mysql_secure_installation
```

### 3.4 Install Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 3.5 Install Git

```bash
sudo apt install -y git
```

### 3.6 (Optional) Install Node.js for Asset Builds

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v
npm -v
```

---

## 4. Database Setup

### 4.1 Create Database and User

```bash
sudo mysql -u root -p
```

In MySQL:

```sql
CREATE DATABASE kewasnet_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kewasnet_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON kewasnet_db.* TO 'kewasnet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4.2 Test Connection

```bash
mysql -u kewasnet_user -p kewasnet_db -e "SELECT 1;"
```

---

## 5. Application Deployment (Manual)

### 5.1 Document Root and Directory Layout

Use a single document root pointing to `public/`:

- **App path**: `/var/www/kewasnet-website` (or `/var/www/html/kewasnet-website`)
- **Document root**: `/var/www/kewasnet-website/public`

### 5.2 Clone Repository

```bash
sudo mkdir -p /var/www
sudo chown deploy:www-data /var/www
cd /var/www
git clone https://github.com/YOUR_ORG/kewasnet-website.git
cd kewasnet-website
```

Replace `YOUR_ORG` with your GitHub org or username.

### 5.3 Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
# If you use npm for assets:
npm ci --production
npm run build   # or npm run prod, if defined
```

### 5.4 Environment File

```bash
cp env .env
# Or, if you have .env.example:
# cp .env.example .env

nano .env
```

Set at least:

```ini
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'https://yourdomain.com/'

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = kewasnet_db
database.default.username = kewasnet_user
database.default.password = YOUR_STRONG_PASSWORD
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

#--------------------------------------------------------------------
# CONTENT SECURITY (generate new key for production)
#--------------------------------------------------------------------
# security.csrfProtection = 'session'
# security.tokenRandomize = true
# security.tokenName = 'csrf_token'
# security.headerName = 'X-CSRF-TOKEN'
# security.cookieName = 'csrf_cookie'
# security.expiry = 7200
# security.regenerate = true
```

Generate a new encryption key:

```bash
php spark key:generate
```

### 5.5 Run Migrations

```bash
php spark migrate
# Optional: seed data
# php spark db:seed
```

### 5.6 Permissions

```bash
sudo chown -R www-data:www-data /var/www/kewasnet-website
sudo chmod -R 755 /var/www/kewasnet-website
sudo chmod -R 775 /var/www/kewasnet-website/writable
sudo chmod -R 775 /var/www/kewasnet-website/public/uploads
sudo chmod +x /var/www/kewasnet-website/spark
```

### 5.7 Nginx Configuration

Create a site config:

```bash
sudo nano /etc/nginx/sites-available/kewasnet
```

Paste (adjust `server_name` and `root`):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/kewasnet-website/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(htaccess|env) {
        deny all;
    }
}
```

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/kewasnet /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 5.8 SSL with Let's Encrypt (Recommended)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 5.9 Optional: Cron for Email Queue

```bash
sudo crontab -u www-data -e
# Add:
* * * * * cd /var/www/kewasnet-website && php spark email:process >> /dev/null 2>&1
```

---

## 6. Automated Deployment with GitHub Actions

### 6.1 How It Works

- On push to `main` (or your chosen branch), GitHub Actions runs tests and then deploys via SSH to the VPS.
- The workflow uses GitHub Secrets for SSH key, host, and user.

### 6.2 VPS Preparation for CI/CD

1. **Deploy key on server** (no passphrase):

   On your **local machine**:

   ```bash
   ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/kewasnet_deploy -N ""
   cat ~/.ssh/kewasnet_deploy.pub
   ```

   On the **VPS**, add the **public** key to the deploy user:

   ```bash
   su - deploy
   mkdir -p ~/.ssh
   echo "PASTE_PUBLIC_KEY_HERE" >> ~/.ssh/authorized_keys
   chmod 600 ~/.ssh/authorized_keys
   ```

2. **GitHub repo access**: Ensure the server can clone the repo (public repo: no token; private: use a Personal Access Token or deploy key added to the repo).

### 6.3 GitHub Secrets

In the repo: **Settings → Secrets and variables → Actions**. Add:

| Secret Name     | Description                          |
|-----------------|--------------------------------------|
| `SSH_PRIVATE_KEY` | Full contents of `~/.ssh/kewasnet_deploy` (private key) |
| `DEPLOY_HOST`   | VPS IP or hostname (e.g. `123.45.67.89`) |
| `DEPLOY_USER`   | SSH user (e.g. `deploy`)             |
| `DEPLOY_PATH`   | App path on server (e.g. `/var/www/kewasnet-website`) |

For private repos, optionally:

| Secret Name   | Description        |
|---------------|--------------------|
| `GH_TOKEN` or `REPO_TOKEN` | GitHub PAT with `repo` scope (for checkout) |

### 6.4 Workflow File

The workflow file is at **`.github/workflows/deploy.yml`**.

**Quick reference — GitHub Secrets:**

| Secret            | Example value                          |
|-------------------|----------------------------------------|
| `SSH_PRIVATE_KEY` | Contents of deploy private key file   |
| `DEPLOY_HOST`     | `123.45.67.89` or `vps.yourdomain.com`|
| `DEPLOY_USER`     | `deploy`                               |
| `DEPLOY_PATH`     | `/var/www/kewasnet-website`           |

The workflow will:

1. Checkout code.
2. Set up PHP and run Composer install (and optional npm build).
3. Run tests (if present).
4. Deploy via SSH: pull, `composer install --no-dev`, run migrations, set permissions, reload PHP-FPM/Nginx.

Trigger: push to `main` (or change `branches` in the workflow).

### 6.5 First Deployment via Actions

- Ensure the server already has the app cloned and `.env` configured (you can do one manual deploy first as in Section 5).
- Push to `main`; the workflow will run and deploy.
- Check the **Actions** tab for logs and any failures.

---

## 7. Post-Deployment Checklist

- [ ] `https://yourdomain.com` loads without errors.
- [ ] Login (admin/user) works.
- [ ] Database-driven pages and forms work.
- [ ] File uploads work and `writable/` and `public/uploads` have correct permissions.
- [ ] `writable/logs` is writable; check `log-YYYY-MM-DD.log` for errors.
- [ ] SSL is active and redirects HTTP → HTTPS if applicable.
- [ ] Cron for `email:process` is installed if you use the email queue.
- [ ] Backups: consider a daily cron for DB + `writable` and `public/uploads`.

---

## 8. Troubleshooting

### 500 Internal Server Error

- Check `writable/logs/log-*.log`.
- Ensure `writable/` and subdirs are `775` and owned by `www-data`.
- Verify `.env` exists and `app.baseURL` matches your domain.

### Class "Locale" not found

- Install PHP `intl`: `sudo apt install php8.2-intl` (or your PHP version).
- Restart PHP-FPM: `sudo systemctl restart php8.2-fpm`.

### Database connection failed

- Confirm MySQL is running: `sudo systemctl status mysql`.
- Check `.env`: hostname, database, username, password, port.
- Test: `mysql -u kewasnet_user -p kewasnet_db -e "SELECT 1;"`.

### Nginx 404 for all routes

- Root must be `.../public` and `try_files` must end with `/index.php?$query_string`.
- Restart Nginx: `sudo systemctl reload nginx`.

### Permission denied (writable, uploads)

```bash
sudo chown -R www-data:www-data /var/www/kewasnet-website/writable
sudo chown -R www-data:www-data /var/www/kewasnet-website/public/uploads
sudo chmod -R 775 /var/www/kewasnet-website/writable
sudo chmod -R 775 /var/www/kewasnet-website/public/uploads
```

### GitHub Actions deploy fails (SSH)

- Confirm `SSH_PRIVATE_KEY` is the full private key (including `-----BEGIN ... END ...-----`).
- Confirm `DEPLOY_USER` can SSH: `ssh deploy@DEPLOY_HOST`.
- Confirm `DEPLOY_PATH` exists and is writable by `DEPLOY_USER` (or adjust ownership).

---

## Summary

1. **Server**: Ubuntu 22.04+, PHP 8.2 (and extensions), Nginx, MySQL, Composer, Git.
2. **Database**: Create DB and user; configure `.env`.
3. **Manual deploy**: Clone, `composer install`, `.env`, `spark migrate`, permissions, Nginx, SSL.
4. **Automation**: Add GitHub Secrets, use `.github/workflows/deploy.yml` for deploy on push to `main`.

For the exact steps of the automated workflow, see `.github/workflows/deploy.yml` in the repository.
