<?php

namespace fluentReservation\Classes;


use fluentReservation\Models\Bookings;
use fluentReservation\Models\Rooms;

class CustomerAjaxHandler
{

    public function register()
    {


        add_action('wp_ajax_fluent_reservation_frontend_ajax', [
            $this,
            'handleFrontendEndPoint'
        ]);

        add_action('wp_ajax_nopriv_fluent_reservation_frontend_ajax', [
            $this,
            'handleFrontendEndPoint'
        ]);

    }

    public function handleFrontendEndPoint()
    {

        if (!isset($_REQUEST['nonce'])) {
            wp_send_json_error(array(
                'message' => __("Invalid nonce", 'fluent-reservation')
            ), 403);
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), 'fluent_reservation_nonce')) {
            wp_send_json_error(array(
                'message' => __("Invalid fluent_reservation_nonce", 'fluent-reservation')
            ), 403);
        }

        $route = sanitize_text_field($_REQUEST['route']);


        $validRoutes = [
            'getRooms'      => 'getRooms',
            'cancelBooking' => 'cancelBooking',
            'bookNow'       => 'bookNow'
        ];

        if (isset($validRoutes[$route])) {
            do_action('fluent-reservation/doing_frontend_ajax' . $route);
            $data = isset($_REQUEST['data']) ? sanitize_text_field($_REQUEST['data']) : [];
            return wp_send_json($this->{$validRoutes[$route]}($data));
        } else {
            return [
                'code'    => 404,
                'message' => 'Invalid route',
            ];
        }
    }

    public function getRooms(): array
    {
        if (!is_user_logged_in()) {
            return [
                'message' => 'You are not logged in',
                'code'    => 403,
            ];
        }
        $availableRooms = (new Rooms())->getBookableRooms();

        global $current_user;

        $myRooms = (new Bookings())->getMyBookings($current_user->ID, ['room_id']);


        $myReservationIds = [];
        foreach ($myRooms as $key => $value) {
            $myReservationIds[] = $value->room_id;
        }
        $myReservationIds = array_unique($myReservationIds);

        $availableRoomIds = array_column($availableRooms, 'id');
        $bookings = (new Bookings())->getBookingsOfRooms($availableRoomIds);
        $bookingsByRoom = [];
        foreach ($bookings as $booking) {
            if ($booking->user_id == $current_user->ID) {
                //$booking->name = 'You';
                $booking->isYourself = true;
            }
            $bookingsByRoom[$booking->room_id][] = $booking;
        }


        $availableRooms = array_map(function ($room) use ($myReservationIds, $bookingsByRoom) {
            $room->isBooked = $room->total_seat <= $room->total_bookings;
            $room->isBookedByMe = in_array($room->id, $myReservationIds);
            $room->available = $room->total_seat - $room->total_bookings;


            $room->persons = isset($bookingsByRoom[$room->id]) ? $bookingsByRoom[$room->id] : [];
            return $room;
        }, $availableRooms);


        return [
            'data' => [
                'availableRooms'       => $availableRooms,
                'myReservationRoomIds' => $myReservationIds,

            ],
            'code' => 200,
        ];
    }

    public function cancelBooking()
    {
        $roomId = intval($_REQUEST['room_id']);
        $room = (new Rooms())->find($roomId);

        if (empty($room)) {
            return [
                'message' => 'Failed to find room',
                'code'    => 403,
            ];
        };

        (new Bookings())->cancelMyBooking($roomId);

        return $this->getRooms();

    }


    public function bookNow()
    {

        if (!isset($_REQUEST['room_id'])) {
            return [
                'message' => 'Please reload the page to get valid rooms info!',
                'code'    => 403,
            ];
        }

        global $current_user;

        if (!$current_user->ID) {
            return [
                'message' => 'You are not logged in',
                'code'    => 403,
            ];
        }

        if (!empty((new Bookings())->getBookingsByEmail($current_user->user_email))) {
            return [
                'message' => 'You already has a reservation',
                'code'    => 403,
            ];
        }


        $roomId = intval($_REQUEST['room_id']);
        $room = (new Rooms())->find($roomId);

        if (empty($room)) {
            return [
                'message' => 'Room not found',
                'code'    => 403,
            ];
        }

        $previousBookings = (new Bookings())->getBookingsByRoom($roomId);
        $previousBookingCount = count($previousBookings);

        if ($previousBookingCount >= $room->total_seat) {
            return [
                'message' => 'No seat available for this room',
                'code'    => 403,
            ];
        }

        $data = [
            'name'        => $current_user->display_name,
            'email'       => $current_user->user_email,
            'user_id'     => $current_user->ID,
            'room_id'     => $roomId,
            'booked_seat' => 1
        ];

        $res = (new Bookings())->addBooking($data);
        if ($res) {
            return $this->getRooms();
        } else {
            return [
                'message' => 'Failed to book room',
                'code'    => 403,
            ];
        }

    }


}