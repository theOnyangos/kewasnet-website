# Paystack Inline Payment Integration

## Overview
This document describes the Paystack inline payment integration for the KEWASNET Learning Hub. The inline integration provides a seamless payment experience where users complete payments within a modal popup without leaving the application.

## Configuration

### Credentials
The Paystack credentials are stored in the `paystack_settings` table:

- **Public Key**: `pk_test_9c79c48824b3ad364c99e197cee566e2c0f84afe`
- **Secret Key**: `sk_test_d80891834ac52efe752067bf957a46cb72ee9180`
- **Payment URL**: `https://api.paystack.co`
- **Status**: Active (1)
- **Currency**: KES (Kenyan Shillings)

### Database Setup
To apply the Paystack settings:
```bash
php spark db:seed PaystackSettingSeeder
```

## How It Works

### 1. Payment Flow

```
User clicks "Enroll Now"
    ↓
Frontend calls /ksp/payment/initiate (AJAX)
    ↓
PaymentService creates order & returns payment data
    ↓
Frontend opens Paystack popup with payment details
    ↓
User enters card details in Paystack modal
    ↓
User completes payment
    ↓
Frontend receives callback from Paystack
    ↓
Frontend calls /ksp/payment/verify (AJAX)
    ↓
PaymentService verifies transaction with Paystack
    ↓
User is enrolled in course & redirected
```

### 2. Key Components

#### PaymentService (app/Services/PaymentService.php)
Handles payment initialization and verification:
- `initiateCoursePayment()` - Creates order and returns payment data for inline popup
- `verifyPayment()` - Verifies transaction with Paystack backend
- `processPaymentSuccess()` - Enrolls user in course after successful payment

#### PaymentController (app/Controllers/FrontendV2/PaymentController.php)
API endpoints for payment operations:
- `POST /ksp/payment/initiate` - Initialize payment
- `POST /ksp/payment/verify` - Verify payment after completion
- `GET /ksp/payment/callback` - Optional callback URL (for redirect fallback)

#### Frontend (app/Views/frontendV2/ksp/pages/learning-hub/catalog/course-details.php)
JavaScript integration:
- Loads Paystack Inline SDK: `https://js.paystack.co/v1/inline.js`
- `openPaystackPopup()` - Opens payment modal with PaystackPop.setup()
- `verifyPayment()` - Verifies transaction after successful payment
- User-friendly status messages and error handling

## Benefits of Inline Integration

### User Experience
✅ No redirect - users stay on the website
✅ Fast - payment modal loads instantly
✅ Professional - seamless, integrated experience
✅ Mobile-friendly - responsive popup design
✅ Lower abandonment - users less likely to cancel

### Technical
✅ Secure - all card data handled by Paystack
✅ PCI Compliant - no sensitive data touches your server
✅ Same security as redirect method
✅ Built-in fraud detection
✅ Supports all Paystack payment methods

## Payment Data Structure

### Initiate Payment Response
```json
{
    "status": "success",
    "public_key": "pk_test_...",
    "email": "user@example.com",
    "amount": 500000,
    "reference": "PAY-1703680000-ABC123",
    "order_id": "123",
    "course_title": "Water Management Fundamentals",
    "currency": "KES",
    "metadata": {
        "order_id": "123",
        "course_id": "456",
        "user_id": "789",
        "course_title": "Water Management Fundamentals"
    }
}
```

### Verify Payment Response
```json
{
    "status": "success",
    "message": "Payment successful and enrollment completed",
    "course_id": "456"
}
```

## Testing

### Test Cards (Paystack)
Use these test cards for development:

**Successful Payment:**
- Card: `4084 0840 8408 4081`
- CVV: Any 3 digits
- Expiry: Any future date
- PIN: `0000`
- OTP: `123456`

**Failed Payment:**
- Card: `5060 6666 6666 6666`
- CVV: Any 3 digits
- Expiry: Any future date

### Testing Flow
1. Navigate to a paid course: `/ksp/learning-hub/course/{id}`
2. Click "Enroll Now - {price} KES"
3. Payment modal should open (no redirect)
4. Enter test card details
5. Complete payment
6. Verify enrollment is successful

## Error Handling

### Frontend Errors
- Payment initialization failure: "Payment initialization failed"
- User closes popup: "Payment cancelled"
- Verification failure: "Payment verification failed"
- Network errors: Graceful error messages with retry option

### Backend Errors
- Course not found: 404 with error message
- Already enrolled: Prevents duplicate payment
- Gateway not configured: Configuration error
- Payment verification fails: Detailed error from Paystack

## Security Considerations

1. **CSRF Protection**: All AJAX requests include CSRF tokens
2. **Server-side Verification**: Always verify payments on the backend
3. **No Sensitive Data**: Card details never touch your server
4. **Reference Validation**: Each payment has a unique reference
5. **Order Status**: Orders tracked from pending to completed

## Monitoring & Logs

### Payment Logs
Check these locations for debugging:
- CodeIgniter logs: `writable/logs/`
- Database: `orders` table (status, payment_reference)
- Paystack Dashboard: https://dashboard.paystack.com/

### Order Statuses
- `pending` - Order created, payment not completed
- `completed` - Payment successful, user enrolled

## Migration from Redirect to Inline

### Changes Made
1. ✅ Updated `PaymentService::initiateCoursePayment()` to return payment data instead of authorization URL
2. ✅ Added Paystack Inline SDK to course details page
3. ✅ Replaced redirect logic with popup initialization
4. ✅ Added client-side payment verification
5. ✅ Maintained backward compatibility with callback endpoint

### Breaking Changes
None - the API endpoints remain the same. Only the frontend behavior changed.

## Troubleshooting

### Popup Not Opening
- Check browser console for JavaScript errors
- Verify Paystack SDK is loaded: `https://js.paystack.co/v1/inline.js`
- Check public key is correctly passed to PaystackPop.setup()

### Payment Verification Fails
- Check server logs for API errors
- Verify secret key is correct in `paystack_settings` table
- Check Paystack dashboard for transaction status
- Ensure payment reference matches between frontend and backend

### User Not Enrolled After Payment
- Check order status in database
- Verify `CourseEnrollmentModel::enrollUser()` is working
- Check for errors in `PaymentService::processPaymentSuccess()`

## Support Resources

- **Paystack Documentation**: https://paystack.com/docs/payments/accept-payments/
- **Paystack Inline Docs**: https://paystack.com/docs/payments/accept-payments/#inline
- **Test Mode Dashboard**: https://dashboard.paystack.com/test
- **Support**: support@paystack.com

## Next Steps

### Production Deployment
1. Replace test credentials with live credentials
2. Update seeder with live keys: `PaystackSettingSeeder.php`
3. Run seeder in production: `php spark db:seed PaystackSettingSeeder`
4. Test with live cards
5. Monitor transactions in Paystack dashboard

### Future Enhancements
- Add support for multiple payment methods (Mobile Money, Bank Transfer)
- Implement payment webhooks for real-time status updates
- Add payment receipts via email
- Implement refund functionality
- Add payment analytics dashboard

---

**Last Updated**: December 27, 2025
**Version**: 1.0
**Integration Type**: Inline/Popup
