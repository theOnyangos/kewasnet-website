<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\CourseEnrollmentModel;
use App\Models\CourseModel;
use App\Models\PaystackSetting;
use App\Libraries\Paystack;
use GuzzleHttp\Client;

class PaymentService
{
    protected $orderModel;
    protected $enrollmentModel;
    protected $courseModel;
    protected $paystack;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->enrollmentModel = new CourseEnrollmentModel();
        $this->courseModel = new CourseModel();
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

        // For inline integration, we return payment details for frontend
        return [
            'status' => 'success',
            'public_key' => $paystackSettings['public_key'],
            'email' => $email,
            'amount' => $amount * 100, // Convert to kobo (smallest currency unit)
            'reference' => $reference,
            'order_id' => $orderId,
            'course_title' => $course['title'] ?? 'Course',
            'currency' => 'KES',
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

        // Enroll user in course
        $enrolled = $this->enrollmentModel->enrollUser(
            $order['user_id'],
            $order['course_id'],
            date('Y-m-d H:i:s')
        );

        if ($enrolled) {
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
}

