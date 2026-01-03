# Email Queue Migration Documentation

## Overview
All email sending functionality in the KEWASNET application has been migrated from synchronous direct sending to an asynchronous queue-based system. This improves user experience by eliminating wait times and provides better reliability with automatic retry logic.

## Architecture

### Components
1. **EmailQueue Model** (`app/Models/EmailQueue.php`)
   - Manages the email queue database operations
   - Handles queueing, status updates, and retrieval of pending emails

2. **ProcessEmailQueue Command** (`app/Commands/ProcessEmailQueue.php`)
   - CLI command that processes the email queue
   - Runs via cron job every minute
   - Processes up to 20 emails per execution
   - Handles retry logic (up to 3 attempts)

3. **Database Table** (`email_queue`)
   - Stores all queued emails with metadata
   - Fields: to, bcc (JSON), subject, message, from_email, from_name, status, attempts, error_message, timestamps
   - Status values: pending, processing, sent, failed

### Cron Job
```bash
* * * * * cd /path/to/project && php spark email:process >> /dev/null 2>&1
```

## Files Modified

### 1. Core Library
**app/Libraries/Mailer.php**
- Converted from direct PHPMailer sending to email queue
- Now uses `EmailQueue` model to queue all emails
- All services using this library automatically benefit from the queue

### 2. Controllers
**app/Controllers/BackendV2/NewsletterController.php**
- Test email sending (lines ~175-200): Converted to queue
- Bulk newsletter sending (lines ~350-385): Converted to queue with individual queueing per subscriber

**app/Controllers/BackendV2/UsersController.php**
- Password change email (lines ~425-444): Converted to queue

**app/Controllers/FrontendV2/NetworkCornerController.php**
- Contact forum moderators (lines ~375-453): Converted to queue with BCC for multiple moderators

**app/Controllers/FrontendV2/DiscussionController.php**
- Contact discussion moderators (lines ~490-525): Converted to queue with BCC

**app/Controllers/Home.php**
- Test email method (lines ~35-60): Converted to queue

**app/Controllers/FrontendV2/ContactsController.php**
- Uses Mailer library (already converted via library update)

### 3. Services
**app/Services/UserService.php**
- Account deletion email (lines ~308-332): Converted to queue

**app/Services/EmailSettingsService.php**
- Test email configuration (lines ~195-230): Converted to queue

**app/Services/ResetPasswordService.php**
- Uses Mailer library (already converted via library update)

**app/Services/AuthService.php**
- Uses Mailer library (already converted via library update)
- All employee welcome emails, instructor notifications, etc. now queued

## Benefits

### 1. Performance
- Immediate page response - no waiting for SMTP
- Users don't experience delays during email sending
- Background processing doesn't block the application

### 2. Reliability
- Automatic retry mechanism (up to 3 attempts)
- Failed emails are logged with error messages
- Queue can be monitored for issues

### 3. Scalability
- Handles bulk email sends efficiently
- Rate limiting through batch processing (20 emails/minute)
- Easy to adjust processing speed

### 4. Maintainability
- Centralized email queue management
- Consistent error handling
- Easy to add monitoring and reporting

## Usage Examples

### Basic Email Queue
```php
$emailQueueModel = new \App\Models\EmailQueue();
$emailQueueModel->queueEmail(
    'user@example.com',           // to
    'Welcome to KEWASNET',        // subject
    '<h1>Welcome!</h1>',          // message (HTML)
    null,                         // bcc (optional array)
    'info@kewasnet.org',         // from_email
    'KEWASNET'                    // from_name
);
```

### Email with BCC
```php
$emailQueueModel = new \App\Models\EmailQueue();
$emailQueueModel->queueEmail(
    'system@kewasnet.org',        // primary recipient
    'Forum Contact Message',       // subject
    $htmlMessage,                  // message
    ['mod1@example.com', 'mod2@example.com'],  // BCC array
    'info@kewasnet.org',          // from_email
    'KEWASNET'                     // from_name
);
```

### Using Mailer Library (Automatic Queue)
```php
$mailer = new \App\Libraries\Mailer();
$mailer->send(
    'user@example.com',
    'Your Subject',
    '<p>Your HTML message</p>',
    'from@kewasnet.org',
    'KEWASNET'
);
// Automatically queues the email
```

## Monitoring

### Check Queue Status
```bash
# Run the processor manually to see output
php spark email:process
```

### View Queue Table
```sql
-- See pending emails
SELECT * FROM email_queue WHERE status = 'pending';

-- See failed emails
SELECT * FROM email_queue WHERE status = 'failed';

-- See emails with multiple attempts
SELECT * FROM email_queue WHERE attempts > 1;
```

### Logs
Email queue operations are logged in:
- `writable/logs/log-{date}.log`

Look for:
- `"Email queued successfully"` - Email added to queue
- `"Email sent successfully"` - Email delivered
- `"Failed to queue email"` - Queue insertion failed
- `"Failed to send email"` - SMTP delivery failed

## Testing

### Test Email Queue
1. Add an email to the queue:
```php
$emailQueueModel = new \App\Models\EmailQueue();
$result = $emailQueueModel->queueEmail(
    'test@example.com',
    'Test Subject',
    'Test message',
    null,
    env('EMAIL_FROM_ADDRESS'),
    'Test Sender'
);
```

2. Process the queue manually:
```bash
php spark email:process
```

3. Check the database:
```sql
SELECT * FROM email_queue ORDER BY id DESC LIMIT 1;
```

### Test Cron Job
```bash
# Check if cron job exists
crontab -l | grep "email:process"

# Test the cron command manually
cd /path/to/project && php spark email:process
```

## Deployment

The deploy script (`deploy.sh`) automatically:
1. Runs database migrations (creates email_queue table)
2. Checks for existing cron job
3. Adds cron job if missing
4. Tests the email processor
5. Processes any pending emails immediately

### Manual Deployment Steps
If automatic deployment fails:

1. Run migration:
```bash
php spark migrate
```

2. Add cron job:
```bash
crontab -e
```
Add line:
```
* * * * * cd /path/to/project && php spark email:process >> /dev/null 2>&1
```

3. Test processor:
```bash
php spark email:process
```

## Troubleshooting

### Emails Not Sending
1. Check cron job is running: `crontab -l`
2. Check queue table: `SELECT * FROM email_queue WHERE status = 'pending'`
3. Run processor manually: `php spark email:process`
4. Check logs: `tail -f writable/logs/log-*.log`

### Failed Emails
1. Check failed emails:
```sql
SELECT * FROM email_queue WHERE status = 'failed';
```
2. Review error messages in the `error_message` column
3. Check SMTP configuration in `.env`
4. Retry manually by setting status back to pending:
```sql
UPDATE email_queue SET status = 'pending', attempts = 0 WHERE id = ?;
```

### Cron Job Not Running
1. Check cron service: `sudo service cron status` (Linux) or `sudo launchctl list | grep cron` (macOS)
2. Check cron logs: `grep CRON /var/log/syslog` (Linux)
3. Verify path in crontab is correct
4. Ensure proper permissions on project directory

## Migration Checklist

- [x] Created email_queue database table
- [x] Created EmailQueue model
- [x] Created ProcessEmailQueue command
- [x] Updated Mailer library to use queue
- [x] Converted NewsletterController (2 locations)
- [x] Converted UsersController
- [x] Converted NetworkCornerController
- [x] Converted DiscussionController
- [x] Converted Home controller
- [x] Converted UserService
- [x] Converted EmailSettingsService
- [x] Updated deploy.sh with cron setup
- [x] Verified all email sends use queue (except ProcessEmailQueue itself)
- [x] Tested email queueing
- [x] Tested email processing

## Notes

### BCC Implementation
For privacy when sending to multiple recipients (like forum moderators), the system uses BCC:
- Primary recipient: System email address
- BCC: All actual recipients
- This prevents recipients from seeing each other's email addresses

### Rate Limiting
Current settings:
- Process every minute (cron frequency)
- Up to 20 emails per batch
- 0.1 second delay between emails
- Effective rate: ~1200 emails/hour

To adjust, modify `ProcessEmailQueue.php`:
```php
$limit = 20;  // Change batch size
usleep(100000);  // Change delay (microseconds)
```

### Email Priority
All emails are processed FIFO (First In, First Out). To implement priority:
1. Add `priority` column to email_queue table
2. Update `getPendingEmails()` to order by priority
3. Set priority when queueing emails

## Future Enhancements

1. **Admin Dashboard**: Web interface to monitor queue status
2. **Email Templates**: Centralized template management
3. **Priority Levels**: Different priorities for different email types
4. **Scheduled Sending**: Queue emails for future delivery
5. **Batch Processing**: Group similar emails for efficiency
6. **Statistics**: Track sent/failed rates, delivery times
7. **Attachments Support**: Add file attachment handling to queue
