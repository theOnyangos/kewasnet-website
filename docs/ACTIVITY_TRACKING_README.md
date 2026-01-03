# KEWASNET Activity Tracking System

## Overview
A comprehensive user activity tracking system that respects user cookie consent preferences and monitors site engagement including page views, user events, and detailed analytics.

## Features
- ✅ Cookie consent-based tracking (respects analytics/marketing preferences)
- ✅ Automatic page view tracking with timing and scroll depth
- ✅ Event tracking (clicks, form submissions, downloads, searches)
- ✅ Session management with device detection
- ✅ Real-time analytics dashboard
- ✅ Offline support with event queuing
- ✅ Privacy-compliant design

## Database Structure

### Tables Created
1. **user_sessions** - Tracks user sessions with device info and consent
2. **page_views** - Records individual page visits with engagement metrics
3. **user_events** - Logs specific user interactions and behaviors

## Files Created

### Backend Components
- `app/Database/Migrations/2025-08-21-130000_CreateActivityTrackingTables.php` - Database schema
- `app/Models/UserSessionModel.php` - Session tracking model
- `app/Models/PageViewModel.php` - Page view tracking model  
- `app/Models/UserEventModel.php` - Event tracking model
- `app/Services/ActivityTrackingService.php` - Core business logic
- `app/Controllers/API/TrackingController.php` - API endpoints

### Frontend Components
- `public/assets/js/kewasnet-tracker.js` - JavaScript tracking library
- `app/Views/tracking/tracking-snippet.php` - Integration snippet
- `app/Views/admin/activity-dashboard.php` - Analytics dashboard
- `app/Controllers/BackendV2/ActivityDashboardController.php` - Dashboard controller

### Test Components
- `app/Views/test/tracking-test.php` - Test page for verification
- `app/Controllers/TrackingTestController.php` - Test controller

## API Endpoints

### Public Tracking Endpoints (No Auth Required)
- `POST /api/tracking/init-session` - Initialize tracking session
- `POST /api/tracking/track-page` - Track page view
- `POST /api/tracking/update-page` - Update page timing/scroll
- `POST /api/tracking/track-event` - Track user event
- `POST /api/tracking/batch-track` - Batch event processing

### Admin Endpoints (Auth Required)
- `GET /api/tracking/admin/dashboard` - Dashboard data
- `GET /api/tracking/admin/real-time` - Real-time activity

## Setup Instructions

### 1. Database Migration
The migration was already run successfully:
```bash
php spark migrate -n 20250821130000
```

### 2. Include Tracking in Layout
Add this to your main layout file before closing `</body>` tag:
```php
<?php echo view('tracking/tracking-snippet'); ?>
```

### 3. Cookie Consent Integration
The system automatically checks for these cookies:
- `cookieConsent=all` - Full consent
- `analyticsCookies=accepted` - Analytics consent
- `marketingCookies=accepted` - Marketing consent

### 4. Access Dashboard
Visit: `/auth/activity-dashboard` (requires admin login)

### 5. Test the System
Visit: `/tracking-test` (development only)

## Usage Examples

### Automatic Tracking
The following are tracked automatically:
- Page views and time on page
- Button and link clicks
- Form submissions
- File downloads
- Scroll depth
- External links
- Social media clicks

### Manual Tracking
```javascript
// Track custom events
window.trackEvent('video_play', 'play', 'Hero Video', null, 'Media');

// Track user registration
window.trackUserRegistration('Newsletter Signup');

// Track downloads
window.trackDownload('brochure.pdf', '/downloads/brochure.pdf');

// Track searches
window.trackSearch('water purification', 'Site Search');
```

### Form Tracking
Add `data-newsletter-form` attribute to newsletter forms:
```html
<form data-newsletter-form>
    <!-- form fields -->
</form>
```

## Privacy & Compliance

### Consent Checking
- All tracking respects user cookie preferences
- No data collected without proper consent
- Graceful degradation when consent not given

### Data Collected
With analytics consent:
- Page URLs and titles
- Time spent on pages
- Click interactions
- Form submissions
- Device type and browser info

With marketing consent:
- Additional behavioral data
- Enhanced user journey tracking

## Dashboard Features

### Overview Metrics
- Total page views
- Active sessions
- Total events tracked
- Average session duration

### Analytics
- Page views over time chart
- Device type breakdown
- Popular pages table
- Top events tracking
- Real-time activity feed

### Filtering
- Today, This Week, This Month, This Year
- Real-time auto-refresh
- Export capabilities (can be added)

## Performance Considerations

### Optimizations
- Batched event processing
- Offline support with queuing
- Automatic cleanup of old data
- Efficient database indexing

### Monitoring
- Failed request retry logic
- Error logging and reporting
- Performance metrics tracking

## Security Features

### Data Protection
- UUID-based identifiers
- No PII stored without explicit consent
- Secure API endpoints
- XSS protection in dashboard

### Admin Access
- Authentication required for analytics dashboard
- Role-based access controls
- Audit logging of admin actions

## Customization

### Adding New Event Types
1. Add to `UserEventModel` validation rules
2. Update JavaScript tracker if needed
3. Add dashboard visualization

### Custom Metrics
Extend the dashboard by modifying:
- `ActivityTrackingService::getDashboardData()`
- `app/Views/admin/activity-dashboard.php`

## Troubleshooting

### Common Issues
1. **Tracking not working**: Check cookie consent status
2. **Dashboard not loading**: Verify admin authentication
3. **Events not saving**: Check API endpoint accessibility
4. **Missing data**: Ensure migration ran successfully

### Debug Mode
Enable in development:
```javascript
// Add to tracking-snippet.php
console.log('Tracking Debug:', {
    consent: window.kewasnetTracker?.hasAnalyticsConsent(),
    initialized: window.kewasnetTracker?.isInitialized,
    sessionId: window.kewasnetTracker?.sessionId
});
```

## Future Enhancements

### Planned Features
- Heatmap generation
- A/B testing support
- Advanced segmentation
- Email reporting
- Data export functionality
- GDPR compliance tools

### Integration Options
- Google Analytics bridge
- Third-party analytics platforms
- CRM system integration
- Marketing automation tools

## Maintenance

### Regular Tasks
- Monitor database size and clean old data
- Update tracking scripts for new features
- Review privacy compliance
- Backup analytics data

### Performance Monitoring
- Track API response times
- Monitor database query performance
- Check JavaScript error rates
- Analyze tracking accuracy

---

The activity tracking system is now fully operational and ready to provide valuable insights into user behavior while maintaining privacy compliance and consent preferences.
