# Deployment Script Fixes Summary

## Issues Fixed

### 1. ✅ Deployment Path Updated
- **Before:** `/var/www/kewasnet.co.ke`
- **After:** `/var/www/html/kewasnet-website`
- **Location:** Line 31

### 2. ✅ Git Remote URL Updated
- **Before:** `https://github.com/yourusername/kewasnet-website.git`
- **After:** `https://github.com/theOnyangos/kewasnet-website.git`
- **Location:** Line 224

### 3. ✅ PHP-FPM Service Detection Improved
- **Before:** Hardcoded `php8.1-fpm`
- **After:** Auto-detects PHP version and tries multiple common versions
- **Location:** Line 415-420

### 4. ✅ Database Backup Command Fixed
- **Before:** Password in command line (security risk)
- **After:** Uses `MYSQL_PWD` environment variable
- **Location:** Line 155-161

### 5. ✅ .env File Handling Improved
- **Before:** Only checked for `env` file
- **After:** Checks for both `env` and `.env.example`
- **Location:** Line 267-275

### 6. ✅ Directory Creation Error Handling
- **Before:** Basic error handling
- **After:** Better error messages and permission checks
- **Location:** Line 214-218

### 7. ✅ File Ownership Command Improved
- **Before:** Single attempt without sudo
- **After:** Tries with sudo first, then without, with helpful error message
- **Location:** Line 312

## Additional Notes for Hostinger VPS

### User/Group Configuration
The script uses `www-data:www-data` by default. If your Hostinger VPS uses a different user (like `deploy`), update lines 305-309:

```bash
WEB_USER="deploy"
WEB_GROUP="deploy"
```

### Running the Script

1. **Make it executable:**
   ```bash
   chmod +x deploy.sh
   ```

2. **Run from the server:**
   ```bash
   cd /var/www/html/kewasnet-website
   ./deploy.sh production
   ```

   Or if the script is in a different location:
   ```bash
   /path/to/deploy.sh production
   ```

### Required Permissions

Some commands may require `sudo`. The script will attempt to use sudo where needed, but you may need to:

1. **Run with sudo:**
   ```bash
   sudo ./deploy.sh production
   ```

2. **Or configure sudoers (recommended):**
   ```bash
   sudo visudo
   ```
   Add:
   ```
   deploy ALL=(ALL) NOPASSWD: /usr/bin/systemctl restart php*-fpm, /usr/bin/systemctl reload nginx, /usr/bin/chown -R www-data\:www-data /var/www/html/kewasnet-website
   ```

### Domain Configuration

Update the `DOMAIN` variable on line 32 if your domain is different from `kewasnet.co.ke`.

## Testing the Script

Before running in production, test with:

```bash
# Dry run (check what would happen)
bash -n deploy.sh  # Syntax check

# Test individual sections
# Comment out sections you don't want to run
```

## Post-Deployment Checklist

After running the script, verify:

- [ ] Application is accessible: `curl -I https://your-domain.com`
- [ ] PHP-FPM is running: `sudo systemctl status php8.2-fpm`
- [ ] Nginx is running: `sudo systemctl status nginx`
- [ ] Logs are being written: `tail -f /var/www/html/kewasnet-website/writable/logs/log-*.log`
- [ ] Permissions are correct: `ls -la /var/www/html/kewasnet-website/writable`
- [ ] Database connection works: `php spark db:table`
- [ ] Email queue cron is set: `crontab -l | grep email:process`
