<?php

namespace App\Services;

use App\Models\EventModel;
use App\Models\EventTicketTypeModel;
use App\Models\EventTicketModel;

class EventService
{
    protected $eventModel;
    protected $ticketTypeModel;
    protected $ticketModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->ticketTypeModel = new EventTicketTypeModel();
        $this->ticketModel = new EventTicketModel();
    }

    /**
     * Get event with ticket types
     */
    public function getEvent($eventId)
    {
        $event = $this->eventModel->find($eventId);
        
        if (!$event) {
            return null;
        }

        // Get ticket types for this event
        $event['ticket_types'] = $this->ticketTypeModel->getEventTicketTypes($eventId);

        return $event;
    }

    /**
     * Check if tickets are available for a ticket type
     */
    public function checkAvailability($eventId, $ticketTypeId, $quantity)
    {
        $ticketType = $this->ticketTypeModel->find($ticketTypeId);
        
        if (!$ticketType || $ticketType['event_id'] != $eventId) {
            return [
                'available' => false,
                'message' => 'Invalid ticket type'
            ];
        }

        // If quantity is null, it means unlimited
        if ($ticketType['quantity'] === null) {
            return [
                'available' => true,
                'remaining' => null,
                'message' => 'Unlimited tickets available'
            ];
        }

        // Count sold tickets for this type
        $soldTickets = $this->ticketModel
            ->where('ticket_type_id', $ticketTypeId)
            ->where('status !=', 'cancelled')
            ->countAllResults(false);

        $remaining = $ticketType['quantity'] - $soldTickets;

        if ($remaining < $quantity) {
            return [
                'available' => false,
                'remaining' => $remaining,
                'message' => "Only {$remaining} tickets available"
            ];
        }

        return [
            'available' => true,
            'remaining' => $remaining,
            'message' => "{$remaining} tickets available"
        ];
    }

    /**
     * Get upcoming published events
     */
    public function getUpcomingEvents($limit = null)
    {
        return $this->eventModel->getUpcomingEvents($limit);
    }

    /**
     * Calculate total amount for booking
     */
    public function calculateTotal($eventId, $ticketQuantities)
    {
        $total = 0.00;

        foreach ($ticketQuantities as $ticketTypeId => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            $ticketType = $this->ticketTypeModel->find($ticketTypeId);
            
            if ($ticketType && $ticketType['event_id'] == $eventId) {
                $total += $ticketType['price'] * $quantity;
            }
        }

        return $total;
    }

    /**
     * Get remaining capacity for ticket type
     */
    public function getRemainingCapacity($ticketTypeId)
    {
        $ticketType = $this->ticketTypeModel->find($ticketTypeId);
        
        if (!$ticketType) {
            return 0;
        }

        if ($ticketType['quantity'] === null) {
            return null; // Unlimited
        }

        $soldTickets = $this->ticketModel
            ->where('ticket_type_id', $ticketTypeId)
            ->where('status !=', 'cancelled')
            ->countAllResults(false);

        return max(0, $ticketType['quantity'] - $soldTickets);
    }
}
