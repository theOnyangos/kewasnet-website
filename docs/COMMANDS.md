1. Supervisor Monitoring Commands:
```bash
    # Check all processes
    sudo supervisorctl status

    # Check specific worker
    sudo supervisorctl status kewasnet-queue:00

    # View logs
    sudo supervisorctl tail kewasnet-queue:*

    # Restart all workers
    sudo supervisorctl restart kewasnet-queue:*

    # Stop all workers
    sudo supervisorctl stop kewasnet-queue:*

    # Reload config
    sudo supervisorctl reload
```

2. Setting Permissions
```bash
    # Navigate to your web directory
    cd /var/www/html

    # Fix ownership - make deploy user owner
    sudo chown -R deploy:deploy kewasnet-website/

    # OR if you want to keep www-data group (for web server access):
    sudo chown -R deploy:www-data kewasnet-website/

    # Set proper permissions
    sudo chmod -R 755 kewasnet-website/

    # For writable directories (uploads, backups, writable folder)
    sudo chmod -R 775 kewasnet-website/writable/
    sudo chmod -R 775 kewasnet-website/backups/
    sudo chmod -R 775 kewasnet-website/public/uploads/
```

3. Supervisor Script
```bash
    #!/bin/bash
    # kewasnet-worker-setup.sh

    set -e

    echo "Setting up KEWASNET queue workers and scheduled tasks..."

    # Install supervisor if not installed
    if ! command -v supervisorctl &> /dev/null; then
        echo "Installing Supervisor..."
        sudo apt-get update
        sudo apt-get install -y supervisor
    fi

    # Create supervisor config
    echo "Creating Supervisor configuration..."
    sudo tee /etc/supervisor/conf.d/kewasnet-queue.conf > /dev/null << 'EOF'
    [program:kewasnet-queue]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/html/kewasnet-website/spark email:process --max-time=300 --sleep=3
    directory=/var/www/html/kewasnet-website
    autostart=true
    autorestart=true
    startretries=3
    user=www-data
    numprocs=2
    redirect_stderr=true
    stdout_logfile=/var/www/html/kewasnet-website/writable/logs/queue-worker.log
    stdout_logfile_maxbytes=50MB
    stdout_logfile_backups=10
    stopwaitsecs=60
    EOF
```

```bash
    # Update supervisor
    echo "Updating Supervisor..."
    sudo supervisorctl reread
    sudo supervisorctl update

    # Start workers
    echo "Starting queue workers..."
    sudo supervisorctl start kewasnet-queue:*

    # Configure cron jobs
    echo "Setting up cron jobs..."
    (sudo crontab -l 2>/dev/null; echo "# KEWASNET Scheduled Tasks") | sudo crontab -
    (sudo crontab -l 2>/dev/null; echo "# Run scheduler every minute") | sudo crontab -
    (sudo crontab -l 2>/dev/null; echo "* * * * * cd /var/www/html/kewasnet-website && php spark schedule:run >> /dev/null 2>&1") | sudo crontab -
    (sudo crontab -l 2>/dev/null; echo "# Clean sessions daily at 4 AM") | sudo crontab -
    (sudo crontab -l 2>/dev/null; echo "0 4 * * * find /var/www/html/kewasnet-website/writable/session -mtime +1 -delete") | sudo crontab -
    (sudo crontab -l 2>/dev/null; echo "# Clear cache daily at 5 AM") | sudo crontab -
    (sudo crontab -l 2>/dev/null; echo "0 5 * * * cd /var/www/html/kewasnet-website && php spark cache:clear >> /dev/null 2>&1") | sudo crontab -

    # Create log directory
    echo "Creating log directories..."
    sudo mkdir -p /var/www/kewasnet.co.ke/writable/logs
    sudo chown -R www-data:www-data /var/www/kewasnet.co.ke/writable/logs

    echo "Setup complete!"
    echo ""
    echo "Check Supervisor status: sudo supervisorctl status"
    echo "Check cron jobs: sudo crontab -l"
    echo "View queue logs: sudo supervisorctl tail kewasnet-queue:*"
```