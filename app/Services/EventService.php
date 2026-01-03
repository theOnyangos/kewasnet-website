<?php

namespace App\Services;
use Carbon\Carbon;
use App\Libraries\ClientAuth;

class EventService 
{
    // Render Upcoming Events Html
    public function renderUpcomingEvents($events)
    {
        if (empty($events)) {
            return '<div class="h-[300px] w-full flex justify-center items-center gap-3 bg-white/50 rounded-md">

                        <div class="flex flex-col justify-center items-center gap-3">
                            <p class="text-center text-gray-500 roboto">No Upcoming Events!</p>
                            '.(!ClientAuth::isLoggedIn() ? '
                                <!-- Link to login -->
                                <p class="text-base text-gray-800 roboto font-normal mb-3">Login to view past events <a href="'. base_url("client/login") .'" class="text-primary roboto font-bold hover:underline">Login</a></p>
                            ' : '
                                <!-- Go to past events button -->
                                <a href="'. base_url("client/events") .'" class="block text-center bg-gradient-to-r from-primary to-secondary text-white py-3 roboto px-8 rounded-full text-[16px] font-normal transition duration-300 shadow-sm">View Past Events</a>
                            ').'
                        </div>   
                    </div>';
        }

        $html = '<div class="grid grig-cols-1 md:grid-cols-3 gap-4">';

        foreach ($events as $key => $event) {

            $datePublished = Carbon::parse($event['start_date'])->format('d M Y');

            $eventTime = Carbon::parse($event['start_time'])->format('h:i A');
            
            $html .= '<div class="border border-borderColor bg-white/50 rounded-md">
                <div class="event-image-height relative">
                    <img src="'. ($event['event_cover_image'] ?? base_url("image-7.png")) .'" alt="'. $event['title'] .'" class="w-full h-full object-cover rounded-t-md">

                    <!-- Event Free or Paid Badge -->
                    <div class="event-badge p-3">
                        '. $this->renderEventState($event['is_paid']) .'
                    </div>

                    <!-- Event Type -->
                    <div class="event-type-badge p-3">
                        '. $this->renderEventTypes($event['type']) .'
                    </div>
                </div>

                <div class="p-3">
                    <h5 class="text-[18px] md:text-[20px] font-bold mb-4 roboto text-primary">'. substr($event['title'], 0, 35) .'...' .'</h5>
                    <p class="text-md md:text-[16px] text-dark">'. ( $event['summary'] ? (substr($event['summary'], 0, 100) . '...') : 'Event summery was not provided. Open the event details for more information.') .' </p>

                    <div class="flex justify-between items-center mt-3 border border-borderColor rounded-md p-3">
                        <!-- Date & Time -->
                        <div class="">
                            <p class="text-md font-bold text-primary">Date</p>
                            <p class="text-md font-bold text-slate-800">'.$datePublished.'</p>
                        </div>

                        <!-- Action Button -->
                        <a href="'. base_url('client/events/'. $event['slug']) .'" class="block text-center bg-gradient-to-r from-primary to-secondary text-white py-2 md:py-3 roboto px-8 rounded-full text-[16px] font-normal transition duration-300 shadow-sm">Join Now</a>
                    </div>
                </div>
            </div>';
        };

        $html .= '</div>';

        return $html;
    }

    // Render Event Types
    public function renderEventTypes($eventType)
    {
        if ($eventType === 'Conference') {
            return '<span class="conference-badge text-white roboto py-1 px-5 shadow-md rounded">Conference</span>';
        }

        if ($eventType === 'Webinar') {
            return '<span class="webinar-badge text-white roboto py-1 px-5 shadow-md rounded">Webinar</span>';
        }

        if ($eventType === 'Workshop') {
            return '<span class="workshop-badge text-white roboto py-1 px-5 shadow-md rounded">Workshop</span>';
        }

        if ($eventType === 'Seminar') {
            return '<span class="seminar-badge text-white roboto py-1 px-5 shadow-md rounded">Seminar</span>';
        }

        if ($eventType === 'Training') {
            return '<span class="training-badge text-white roboto py-1 px-5 shadow-md rounded">Training</span>';
        }

        if ($eventType === 'Meeting') {
            return '<span class="meeting-badge text-white roboto py-1 px-5 shadow-md rounded">Exhibition</span>';
        }
    }

    // Render Event State if paid or free
    public function renderEventState($isPaid)
    {
        if ($isPaid) {
            return '<span class="bg-red-600 text-white roboto rounded-full py-1 px-5 shadow-md">Paid</span>';
        }

        return '<span class="bg-green-600 text-white roboto rounded-full py-1 px-5 shadow-md">Free</span>';
    }

    // Render Event HTML
    public function renderEvent($events)
    {
        if (empty($events)) {
            return '<div class="h-[300px] w-full flex justify-center items-center gap-3 bg-white/50 rounded-md">

                        <div class="flex flex-col justify-center items-center gap-3">
                            <p class="text-center text-gray-500 roboto">No Events!</p>
                            '.(!ClientAuth::isLoggedIn() ? '
                                <!-- Link to login -->
                                <p class="text-base text-gray-800 roboto font-normal mb-3">Login to view past events <a href="'. base_url("client/login") .'" class="text-primary roboto font-bold hover:underline">Login</a></p>
                            ' : '
                                <!-- Go to past events button -->
                                <a href="'. base_url("client/events") .'" class="block text-center bg-gradient-to-r from-primary to-secondary text-white py-3 roboto px-8 rounded-full text-[16px] font-normal transition duration-300 shadow-sm">View Past Events</a>
                            ').'
                        </div>   
                    </div>';
        }

        $html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">';

        foreach ($events as $key => $event) {
                
                $datePublished = Carbon::parse($event['start_date'])->format('d M Y');
    
                $eventTime = Carbon::parse($event['start_time'])->format('h:i A');
                
                $html .= '<!-- Event 1 -->
                    <div class="bg-white/50 rounded-md shadow-md">
                        <div class="event-image-height relative">
                            <!-- Event Image -->
                            <img src="'. ($event['event_cover_image'] ?? base_url("image-5.png")) .'" alt="" class="w-full event-image-height object-cover rounded-t-md">

                            <!-- Event Free or Paid Badge -->
                            <div class="event-badge p-3">
                                '. $this->renderEventState($event['is_paid']) .'
                            </div>
                        </div>

                        <div class="p-3">
                            <!-- Event Tags -->
                            <h5 class="text-xl text-primary roboto font-bold mb-2 capitalize">'. substr($event['title'], 0, 35) .'...' .'</h5>
                            <p class="text-base text-gray-800 roboto mb-5">'. ( $event['summary'] ? (substr($event['summary'], 0, 70) . '...') : 'Event summery was not provided. Open the event details for more information.') .'</p>

                            <!-- Event Date & Time -->
                            <div class="flex justify-between items-center">
                                <div class="flex justify-start items-center">
                                    <ion-icon name="calendar-outline" class="text-[20px] text-gray-500"></ion-icon>
                                    <span class="text-base text-gray-800 roboto ml-2">'.$datePublished.'</span>
                                </div>

                                <div class="flex justify-start items-center">
                                    <ion-icon name="time-outline" class="text-[20px] text-gray-500"></ion-icon>
                                    <span class="text-base text-gray-800 roboto ml-2">'.$eventTime.'</span>
                                </div>
                            </div>

                            <!-- Event Location -->
                            <div class="flex justify-start items-center mt-3">
                                <ion-icon name="location-outline" class="text-[20px] text-gray-500"></ion-icon>
                                <span class="text-base text-gray-800 roboto ml-2">'.$event['location'].'</span>
                            </div>

                            <!-- Event Type (Webinar or Conference) -->
                            <div class="flex justify-start items-center mt-3 mb-3">
                                <ion-icon name="chatbox-ellipses-outline" class="text-[20px] text-gray-500"></ion-icon>
                                <span class="text-base text-gray-800 roboto ml-2">'.$event['type'].'</span>
                            </div>

                            <div class="w-full flex justify-center items-center">
                                <a href="'. base_url('client/events/'. $event['slug']) .'" class=" bg-gradient-to-r from-primary to-secondary text-white shadow-md rounded-full roboto py-2 flex justify-center items-center gap-2 w-[180px]">
                                    View Details <ion-icon name="arrow-forward-outline" class="text-[21px]"></ion-icon>
                                </a>
                            </div>
                        </div>
                    </div>';
        }

        $html .= '</div>';

        return $html;
    }

    // Render event details HTML
    public function renderEventDetails($event, $total)
    {
        $datePublished = Carbon::parse($event['start_date'])->format('d M Y');

        $eventTime = Carbon::parse($event['start_time'])->format('h:i A') .' - ' . Carbon::parse($event['end_time'])->format('h:i A');

        $availableSeats = $event['capacity'] - $total;

        return '<div class="p-5">
                        <h5 class="text-lg text-gray-800 roboto font-bold">Order summary</h5>
                        <div class="flex justify-between items-center mt-5">
                            <p class="text-base text-gray-800 roboto font-normal">Event Type:</p>
                            <p class="text-base text-gray-800 roboto font-normal">'.$event['type'].'</p>
                        </div>

                        <div class="flex justify-between items-center mt-5">
                            <p class="text-base text-gray-800 roboto font-normal">Event Date:</p>
                            <p class="text-base text-gray-800 roboto font-normal">'.$datePublished.'</p>
                        </div>

                        <div class="flex justify-between items-center mt-5">
                            <p class="text-base text-gray-800 roboto font-normal">Event Time:</p>
                            <p class="text-base text-gray-800 roboto font-normal">'.$eventTime.'</p>
                        </div>

                        <div class="flex justify-between items-center mt-5">
                            <p class="text-base text-gray-800 roboto font-normal">Location:</p>
                            <p class="text-base text-gray-800 roboto font-normal">'.$event['location'].'</p>
                        </div>

                        <div class="flex justify-between items-center mt-5">
                            <p class="text-base text-gray-800 roboto font-normal">Event Fee:</p>
                            '.($event['is_paid'] == 1 ? 
                            '<p class="text-base text-gray-800 roboto font-normal">Ksh. '.number_format($event['price'], 2).'</p>' : 
                            '<p class="text-base text-gray-800 roboto font-normal">Free</p>
                            ').'
                            
                        </div>

                        <div class="flex justify-between items-center mt-5">
                            <p class="text-base text-gray-800 roboto font-normal">Total Seats:</p>
                            <p class="text-base text-gray-800 roboto font-normal">'.($availableSeats == 0 ? 'Event is fully booked' : $event['capacity']).'</p>
                        </div>

                        <div class="flex justify-between items-center mt-5 border-b pb-3">
                            <p class="text-base text-gray-800 roboto font-normal">Available Seats:</p>
                            <p class="text-base text-gray-800 roboto font-normal">'.($event['is_paid'] == 1 ? $availableSeats : 'Unlimited').'</p>
                        </div>

                        <div class="flex justify-between items-center mt-5">
                            <p class="text-base text-gray-900 roboto font-bold">Total:</p>
                            <p class="text-base text-gray-900 roboto font-bold">Ksh. '.number_format($event['price'], 2).'</p>
                        </div>
                    </div>';
    }
}