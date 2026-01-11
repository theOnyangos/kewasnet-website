<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\CourseEnrollmentModel;
use App\Models\CourseModel;
use App\Models\EventBookingModel;
use App\Models\EventModel;
use App\Models\PaystackSetting;
use App\Models\PaystackTransactions;
use App\Libraries\Paystack;
use GuzzleHttp\Client;

class PaymentService
{
    protected $orderModel;
    protected $enrollmentModel;
    protected $courseModel;
    protected $eventBookingModel;
    protected $eventModel;
    protected $paystackTransactions;
    protected $paystack;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->enrollmentModel = new CourseEnrollmentModel();
        $this->courseModel = new CourseModel();
        $this->eventBookingModel = new EventBookingModel();
        $this->eventModel = new EventModel();
        $this->paystackTransactions = new PaystackTransactions();
    }

    /**
     * Initialize course payment (Inline Integration)
     */
    public function initiateCoursePayment($userId, $courseId, $email)
    {
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return [
                'status' => 'error',
                'message' => 'Course not found'
            ];
        }

        // Check if already enrolled
        if ($this->enrollmentModel->isEnrolled($userId, $courseId)) {
            return [
                'status' => 'error',
                'message' => 'You are already enrolled in this course'
            ];
        }

        // Calculate amount (use discount if available)
        $amount = $course['discount_price'] > 0 ? $course['discount_price'] : $course['price'];

        // Create order
        $orderData = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'country_id' => 1, // Default country, can be made dynamic
            'order_number' => $this->generateOrderNumber(),
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'paystack',
        ];

        $orderId = $this->orderModel->insert($orderData);
        if (!$orderId) {
            return [
                'status' => 'error',
                'message' => 'Failed to create order'
            ];
        }

        // Get Paystack settings
        $paystackSettings = (new PaystackSetting())->where('status', 1)->first();
        log_message('debug', 'Paystack Settings Query Result: ' . json_encode($paystackSettings));
        
        if (!$paystackSettings) {
            log_message('error', 'Payment gateway not configured - no settings found with status=1');
            return [
                'status' => 'error',
                'message' => 'Payment gateway not configured'
            ];
        }

        $reference = $this->generateReference();

        // Update order with reference
        $this->orderModel->update($orderId, [
            'payment_reference' => $reference
        ]);

        // Get currency from settings, default to NGN if not set
        $currency = !empty($paystackSettings['currency']) ? $paystackSettings['currency'] : 'NGN';

        // For inline integration, we return payment details for frontend
        return [
            'status' => 'success',
            'public_key' => $paystackSettings['public_key'],
            'email' => $email,
            'amount' => $amount * 100, // Convert to smallest currency unit (kobo for NGN, cents for others)
            'reference' => $reference,
            'order_id' => $orderId,
            'course_title' => $course['title'] ?? 'Course',
            'currency' => $currency,
            'metadata' => [
                'order_id' => $orderId,
                'course_id' => $courseId,
                'user_id' => $userId,
                'course_title' => $course['title'] ?? 'Course',
            ]
        ];
    }

    /**
     * Verify payment
     */
    public function verifyPayment($reference)
    {
        $order = $this->orderModel->where('payment_reference', $reference)->first();
        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }

        $paystackSettings = (new PaystackSetting())->where('status', 1)->first();
        if (!$paystackSettings) {
            return [
                'status' => 'error',
                'message' => 'Payment gateway not configured'
            ];
        }

        $client = new Client([
            'base_uri' => $paystackSettings['payment_url'],
            'headers' => [
                'Authorization' => 'Bearer ' . $paystackSettings['secret_key'],
                'Content-Type' => 'application/json',
            ],
        ]);

        try {
            $response = $client->get("/transaction/verify/{$reference}");
            $result = json_decode($response->getBody(), true);

            if ($result['status'] && $result['data']['status'] === 'success') {
                return $this->processPaymentSuccess($order['id'], $result['data']);
            }

            return [
                'status' => 'error',
                'message' => 'Payment verification failed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Payment verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process successful payment
     */
    public function processPaymentSuccess($orderId, $paymentData)
    {
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }

        // Update order status
        $this->orderModel->update($orderId, [
            'status' => 'completed'
        ]);

        // Save transaction record
        $transactionData = [
            'user_id' => $order['user_id'],
            'event_id' => null,
            'course_id' => $order['course_id'],
            'amount' => $order['amount'],
            'status' => 'success',
            'reference' => $order['payment_reference'] ?? $paymentData['reference'] ?? null,
        ];

        $transactionId = $this->paystackTransactions->createTransaction($transactionData);
        
        if ($transactionId) {
            log_message('info', "Course payment transaction saved: Transaction ID {$transactionId} for order #{$orderId}");
        } else {
            log_message('error', "Failed to save course payment transaction for order #{$orderId}");
        }

        // Enroll user in course
        $enrolled = $this->enrollmentModel->enrollUser(
            $order['user_id'],
            $order['course_id'],
            date('Y-m-d H:i:s')
        );

        if ($enrolled) {
            // Send notifications after successful payment and enrollment
            try {
                $course = $this->courseModel->find($order['course_id']);
                $userModel = model('UserModel');
                $user = $userModel->find($order['user_id']);
                
                if ($course && $user) {
                    $courseName = $course['title'] ?? 'Unknown Course';
                    $userName = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
                    $amount = number_format($order['amount'] ?? 0, 2);
                    
                    $notificationService = new \App\Services\NotificationService();
                    
                    // Notify user about payment success and enrollment
                    $notificationService->notifyPaymentSuccess($order['user_id'], $amount, $courseName, $order['course_id']);
                    
                    // Notify admins about new payment received
                    $adminUsers = $userModel->getAdministrators();
                    if (!empty($adminUsers)) {
                        $adminIds = array_column($adminUsers, 'id');
                        $notificationService->notifyAdminPaymentReceived($adminIds, $amount, $courseName, trim($userName), $orderId);
                    }
                }
            } catch (\Exception $notificationError) {
                log_message('error', "Error sending payment success notifications: " . $notificationError->getMessage());
                // Don't fail enrollment if notification fails
            }
            
            return [
                'status' => 'success',
                'message' => 'Payment successful and enrollment completed',
                'course_id' => $order['course_id']
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Payment successful but enrollment failed'
        ];
    }

    /**
     * Generate order number
     */
    protected function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }

    /**
     * Generate payment reference
     */
    protected function generateReference()
    {
        return 'PAY-' . time() . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Initialize event payment (Inline Integration)
     */
    public function initiateEventPayment($userId, $bookingId, $email)
    {
        $booking = $this->eventBookingModel->find($bookingId);
        if (!$booking) {
            return [
                'status' => 'error',
                'message' => 'Booking not found'
            ];
        }

        if ($booking['user_id'] != $userId) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized access'
            ];
        }

        if ($booking['payment_status'] != 'pending') {
            return [
                'status' => 'error',
                'message' => 'Booking already processed'
            ];
        }

        $event = $this->eventModel->find($booking['event_id']);
        if (!$event) {
            return [
                'status' => 'error',
                'message' => 'Event not found'
            ];
        }

        $amount = $booking['total_amount'];

        // Get Paystack settings
        $paystackSettings = (new PaystackSetting())->where('status', 1)->first();
        log_message('debug', 'Paystack Settings Query Result: ' . json_encode($paystackSettings));
        
        if (!$paystackSettings) {
            log_message('error', 'Payment gateway not configured - no settings found with status=1');
            return [
                'status' => 'error',
                'message' => 'Payment gateway not configured'
            ];
        }

        $reference = $this->generateReference();

        // Update booking with reference
        $this->eventBookingModel->update($bookingId, [
            'payment_reference' => $reference
        ]);

        // Get currency from settings, default to NGN if not set
        $currency = !empty($paystackSettings['currency']) ? $paystackSettings['currency'] : 'NGN';

        // For inline integration, we return payment details for frontend
        return [
            'status' => 'success',
            'public_key' => $paystackSettings['public_key'],
            'email' => $email,
            'amount' => $amount * 100, // Convert to smallest currency unit (kobo for NGN, cents for others)
            'reference' => $reference,
            'booking_id' => $bookingId,
            'event_title' => $event['title'] ?? 'Event',
            'currency' => $currency,
            'metadata' => [
                'booking_id' => $bookingId,
                'event_id' => $booking['event_id'],
                'user_id' => $userId,
                'event_title' => $event['title'] ?? 'Event',
            ]
        ];
    }

    /**
     * Process successful event payment
     */
    public function processEventPaymentSuccess($bookingId, $paymentData)
    {
        $booking = $this->eventBookingModel->find($bookingId);
        if (!$booking) {
            return [
                'status' => 'error',
                'message' => 'Booking not found'
            ];
        }

        // Update booking payment status
        $this->eventBookingModel->update($bookingId, [
            'payment_status' => 'paid',
            'status' => 'confirmed'
        ]);

        // Save transaction record
        $transactionData = [
            'user_id' => $booking['user_id'],
            'event_id' => $booking['event_id'],
            'course_id' => null,
            'amount' => $booking['total_amount'],
            'status' => 'success',
            'reference' => $booking['payment_reference'] ?? $paymentData['reference'] ?? null,
        ];

        $transactionId = $this->paystackTransactions->createTransaction($transactionData);
        
        if ($transactionId) {
            log_message('info', "Event payment transaction saved: Transaction ID {$transactionId} for booking #{$bookingId}");
        } else {
            log_message('error', "Failed to save event payment transaction for booking #{$bookingId}");
        }

        // Send notifications after successful event payment
        try {
            $eventModel = new \App\Models\EventModel();
            $event = $eventModel->find($booking['event_id']);
            $eventTitle = $event ? $event['title'] : 'Event';

            $bookingNumber = $booking['booking_number'] ?? '';
            $amount = number_format($booking['total_amount'] ?? 0, 2);

            $notificationService = new \App\Services\NotificationService();

            // Notify user about payment success (if logged in)
            if (!empty($booking['user_id'])) {
                $notificationService->notifyEventPaymentSuccess($booking['user_id'], $eventTitle, $amount, $bookingId);
            }

            // Notify admins about event payment received
            $userModel = model('UserModel');
            $user = $booking['user_id'] ? $userModel->find($booking['user_id']) : null;
            $userName = $user ? ($user['first_name'] . ' ' . $user['last_name']) : ($booking['email'] ?? 'Guest');

            $adminUsers = $userModel->getAdministrators();
            if (!empty($adminUsers)) {
                $adminIds = array_column($adminUsers, 'id');
                $notificationService->notifyAdminEventPayment($adminIds, $amount, $eventTitle, $bookingNumber, $bookingId);
            }
        } catch (\Exception $notificationError) {
            log_message('error', "Error sending event payment notifications: " . $notificationError->getMessage());
            // Don't fail payment if notification fails
        }

        return [
            'status' => 'success',
            'message' => 'Payment successful and booking confirmed',
            'booking_id' => $bookingId,
            'event_id' => $booking['event_id']
        ];
    }

    /**
     * Verify event payment
     */
    public function verifyEventPayment($reference)
    {
        $booking = $this->eventBookingModel->where('payment_reference', $reference)->first();
        if (!$booking) {
            return [
                'status' => 'error',
                'message' => 'Booking not found'
            ];
        }

        $paystackSettings = (new PaystackSetting())->where('status', 1)->first();
        if (!$paystackSettings) {
            return [
                'status' => 'error',
                'message' => 'Payment gateway not configured'
            ];
        }

        $client = new Client([
            'base_uri' => $paystackSettings['payment_url'],
            'headers' => [
                'Authorization' => 'Bearer ' . $paystackSettings['secret_key'],
                'Content-Type' => 'application/json',
            ],
        ]);

        try {
            $response = $client->get("/transaction/verify/{$reference}");
            $result = json_decode($response->getBody(), true);

            if ($result['status'] && $result['data']['status'] === 'success') {
                return $this->processEventPaymentSuccess($booking['id'], $result['data']);
            }

            // Mark booking as failed if payment failed
            $this->eventBookingModel->update($booking['id'], [
                'payment_status' => 'failed'
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment verification failed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Payment verification error: ' . $e->getMessage()
            ];
        }
    }
}

