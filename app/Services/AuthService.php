<?php

namespace App\Services;

use Carbon\Carbon;
use App\Libraries\Mailer;
use App\Models\SocialLink as SocialModel;

class AuthService
{
    protected $mailer;
    
    public function __construct()
    {
        $this->mailer = new Mailer();
        $this->view = \Config\Services::renderer();
    }

    // This method sends new employee
    public function sendEmailToEmployee($userData): bool
    {
        try {

            $firstName = explode(" ", $userData['full_name'])[0];

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'user_name'         => $firstName,
                'email'             => $userData['email'],
                'password'          => $userData['password'],
                'employee_number'   => $userData['employee_number'],
                'verification_url'  => $userData['verification_url'],
                'join_date'         => $userData['join_date'],
                'social_links'      => $social,
            ])->render('backend/emails/welcome_employee_template');
            
            // Send email
            $subject = 'Welcome to KEWASNET';
            $message = $message;
            $from = env('EMAIL_FROM_ADDRESS');
            $fromName = env('EMAIL_FROM_NAME');
            $to = $userData['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }            
    }

    // This method sends an email to the newly assigned instructor
    public function sendEmailToInstructor($userData): bool
    {
        try {

            $firstName = explode(" ", $userData['full_name'])[0];

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'user_name'         => $firstName,
                'email'             => $userData['email'],
                'password'          => $userData['password'],
                'employee_number'   => $userData['employee_number'],
                'verification_url'  => $userData['verification_url'],
                'join_date'         => $userData['join_date'],
                'social_links'      => $social,
            ])->render('backend/emails/welcome_instructor_template');
            
            // Send email
            $subject    = 'New user role assignment';
            $message    = $message;
            $from       = env('EMAIL_FROM_ADDRESS');
            $fromName   = env('EMAIL_FROM_NAME');
            $to         = $userData['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }            
    }

    // This method sends new user
    public function sendEmailToUser($userData): bool
    {
        try {

            $firstName = explode(" ", $userData['full_name'])[0];

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'user_name'         => $firstName,
                'email'             => $userData['email'],
                'verification_url'  => $userData['verification_url'],
                'social_links'      => $social,
            ])->render('backend/emails/welcome_user_template');
            
            // Send email
            $subject    = 'Welcome to KEWASNET';
            $message    = $message;
            $from       = env('EMAIL_FROM_ADDRESS');
            $fromName   = env('EMAIL_FROM_NAME');
            $to         = $userData['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            log_message('error', 'Failed to send verification email: ' . $th->getMessage());
            return false;
        }            
    }

    // This method sends new administrator email
    public function sendEmailToAdmin($userData): bool
    {
        try {

            $firstName = explode(" ", $userData['full_name'])[0];

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'user_name'         => $firstName,
                'email'             => $userData['email'],
                'password'          => $userData['password'],
                'employee_number'   => $userData['employee_number'],
                'verification_url'  => $userData['verification_url'],
                'join_date'         => $userData['join_date'],
                'social_links'      => $social,
            ])->render('backend/emails/welcome_admin_template');
            
            // Send email
            $subject    = 'Welcome to KEWASNET';
            $message    = $message;
            $from       = env('EMAIL_FROM_ADDRESS');
            $fromName   = env('EMAIL_FROM_NAME');
            $to         = $userData['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            echo $th->getMessage();
            return false;
        }            
    }

    // This function sends deletion confirmation email to the user
    public function sendDeletionConfirmationEmail($userData): bool
    {
        try {

            $firstName = explode(" ", $userData['full_name'])[0];

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'user_name' => $firstName,
                'email' => $userData['email'],
                'social_links' => $social,
            ])->render('backend/emails/account_deletion_confirmation');
            
            // Send email
            $subject = 'Account Deletion Confirmation';
            $message = $message;
            $from = env('EMAIL_FROM_ADDRESS');
            $fromName = env('EMAIL_FROM_NAME');
            $to = $userData['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }            
    }

    // This method sends account retrieval email to user
    public function sendAccountRetrievalEmail($userData)
    {
        try {

            $firstName = explode(" ", $userData['full_name'])[0];

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'user_name' => $firstName,
                'email' => $userData['email'],
                'employee_number' => $userData['employee_number'],
                'verification_url' => $userData['verification_url'],
                'join_date' => $userData['join_date'],
                'social_links' => $social,
            ])->render('backend/emails/account_retrieval_template');
            
            // Send email
            $subject = 'User Account Retrieval';
            $message = $message;
            $from = env('EMAIL_FROM_ADDRESS');
            $fromName = env('EMAIL_FROM_NAME');
            $to = $userData['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            echo $th->getMessage();
            return false;
        }  
    }

    // This method sends contact information
    public function sendContactMessage($contactData)
    {
        try {

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'full_name' => $contactData['full_name'],
                'email' => $contactData['email'],
                'phone' => $contactData['phone'],
                'message' => $contactData['message'],
                'social_links' => $social,
            ])->render('backend/emails/contact_message_template');
            
            // Send email
            $subject = $contactData['subject'];
            $message = $message;
            $from = $contactData['email'];
            $fromName = $contactData['full_name'];
            $to = env('ADMIN_EMAIL'); // Administrator email

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }  
    }

    // This method sends email to job applicant
    public function sendEmailToJobApplicant($userData)
    {
        try {

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'full_name' => $userData['full_name'],
                'email' => $userData['email'],
                'job_reference_number' => $userData['job_reference_number'],
                'job_title' => $userData['job_title'],
                'social_links' => $social,
            ])->render('backend/emails/job_applicant_template');
            
            // Send email
            $subject = 'Your Job Application Reference Number';
            $message = $message;
            $from = env('EMAIL_FROM_ADDRESS');
            $fromName = env('EMAIL_FROM_NAME');
            $to = $userData['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }  
    }

    // This method sends email to Administrator
    public function sendEmailToOpportunityAdmin($userData)
    {
        try {
            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'full_name' => $userData['full_name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'job_reference_number' => $userData['job_reference_number'],
                'job_title' => $userData['job_title'],
                'social_links' => $social,
            ])->render('backend/emails/job_application_notification_template');

            // To Email
            $toEmail = "";

            if ($userData['job_type'] === 'jobs') {
                $toEmail = env('OPPORTUNITY_EMAIL');
            } else if ($userData['job_type'] === 'tenders') {
                $toEmail = env('TENDER_EMAIL');
            }
            
            // Send email
            $subject = 'New Job Application';
            $message = $message;
            $from = $userData['email'];
            $fromName = $userData['full_name'];
            $to = $toEmail;

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }  
    }

    // Send email to users registered for event (Incomplete)
    public function sendEmailToEventAttendees($eventData, $attendees)
    {
        try {

            // Get social links information
            $social = $this->getSocialMediaInfo();

            foreach ($attendees as $attendee) {
                $message = $this->view->setData([
                    'full_name' => $attendee['attendee_name'],
                    'event_title' => $eventData['title'],
                    'event_start_date' => Carbon::parse($eventData['start_date'])->format('d M Y'),
                    'event_start_time' => Carbon::parse($eventData['start_time'])->format('h:i A'),
                    'event_end_date' => Carbon::parse($eventData['end_date'])->format('d M Y'),
                    'event_end_time' => Carbon::parse($eventData['end_time'])->format('h:i A'),
                    'event_location' => $eventData['location'],
                    'event_cover_image' => $eventData['event_cover_image'],
                    'social_links' => $social,
                ])->render('backend/emails/event_attendee_template');
                
                // Send email
                $subject = 'Event Attendance Confirmation';
                $message = $message;
                $from = env('EMAIL_FROM_ADDRESS');
                $fromName = env('EMAIL_FROM_NAME');
                $to = $attendee['email'];

                $this->mailer->send($to, $subject, $message, $from, $fromName);
            }
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }  
    }

    // Send email to single user registered for event
    public function sendEmailToSingleEventAttendee($eventData, $attendee)
    {
        try {

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'full_name' => $attendee['attendee_name'],
                'event_title' => $eventData['title'],
                'event_start_date' => Carbon::parse($eventData['start_date'])->format('d M Y'),
                'event_start_time' => Carbon::parse($eventData['start_time'])->format('h:i A'),
                'event_end_date' => Carbon::parse($eventData['end_date'])->format('d M Y'),
                'event_end_time' => Carbon::parse($eventData['end_time'])->format('h:i A'),
                'event_location' => $eventData['location'],
                'is_paid' => $eventData['is_paid'],
                'registration_number' => $attendee['registration_number'],
                'event_cover_image' => $eventData['event_cover_image'],
                'social_links' => $social,
            ])->render('backend/emails/event_attendee_template');
            
            // Send email
            $subject = 'Event Attendance Confirmation';
            $message = $message;
            $from = env('EMAIL_FROM_ADDRESS');
            $fromName = env('EMAIL_FROM_NAME');
            $to = $attendee['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }  
    }

    // send course purchase details to user
    public function sendCoursePurchaseDetails($userData)
    {
        try {

            // Get social links information
            $social = $this->getSocialMediaInfo();

            $message = $this->view->setData([
                'full_name' => $userData['full_name'],
                'courses' => $userData['course_details'],
                'reference_number' => $userData['order_number'],
                'amount' => $userData['amount'],
                'email' => $userData['email'],
                'payment_method' => $userData['payment_method'],
                'social_links' => $social,
            ])->render('backend/emails/course_purchase_template');
            
            // Send email
            $subject = 'Course Purchase Confirmation';
            $message = $message;
            $from = env('EMAIL_FROM_ADDRESS');
            $fromName = env('EMAIL_FROM_NAME');
            $to = $userData['email'];

            $this->mailer->send($to, $subject, $message, $from, $fromName);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }  
    }

    // This method gets the company social media information
    public function getSocialMediaInfo()
    {
        $socialModel = new SocialModel();
        $socialInfo = $socialModel->select('facebook, twitter, instagram, linkedin, youtube')->first();
        return $socialInfo;
    }
}