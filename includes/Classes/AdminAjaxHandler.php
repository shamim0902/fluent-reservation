<?php

namespace fluentReservation\Classes;

use fluentReservation\Models\Bookings;
use fluentReservation\Models\Rooms;

class AdminAjaxHandler
{

    public function register()
    {
        add_action('wp_ajax_fluent_reservation_admin_ajax', [
            $this,
            'handleEndPoint'
        ]);

        add_action('wp_ajax_nopriv_fluent_reservation_admin_ajax', [
            $this,
            'handleEndPoint'
        ]);

    }

    public function handleEndPoint()
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
            'getRooms' => 'getRooms',
            'addRoom' => 'addRoom',
            'updateRoom' => 'updateRoom',
            'bookNow' => 'bookNow',
            'seeBookingPersons' => 'seeBookingPersons',
            'cancelBooking' => 'cancelBooking'
        ];

        if (isset($validRoutes[$route])) {
            do_action('fluent-reservation/doing_ajax_forms_' . $route);
            $data = isset($_REQUEST['data']) ? sanitize_text_field($_REQUEST['data']) : [];
            return $this->{$validRoutes[$route]}($data);
        }
        do_action('fluent-reservation/admin_ajax_handler_catch', $route);
    }

    public function cancelBooking()
    {
        $roomId = intval($_REQUEST['room_id']);
        $room = (new Rooms())->find($roomId);

        if (empty($room)) {
            wp_send_json_error(
                [
                    'message' => "No room found!"
                ],
                423
            );
        };

        (new Bookings())->cancelMyBooking($roomId);

        wp_send_json_success(
            [
                'message' => 'Reservation canceled!',
                'status' => true
            ]
        );

    }


    public function seeBookingPersons()
    {
        $roomId = intval($_REQUEST['room_id']);
        $room = (new Rooms())->find($roomId);

        if (empty($room)) {
            wp_send_json_error(
                [
                    'message' => "No room found!"
                ],
                423
            );
        }

        $bookings = (new Bookings())->getBookingPersons($room->id);

        if (!is_array($bookings)) {
            wp_send_json_error(
                [
                    'message' => "No bookings received yet!"
                ],
                423
            );
        }

        $names = [];
        foreach ($bookings as $key => $value) {
            $names[] = intval($key) + 1 . '.' . $value->name;
        }

        wp_send_json_success([
                'bookings' => implode('<br/>', $names),
                'status' => true
            ], 200);
    }

    public function bookNow()
    {
        global $current_user;

        if (!$current_user->ID) {
            wp_send_json_error(
                [
                    'message' => "Please login in to book a reservation!"
                ],
                423
            );
        }

        if (!empty((new Bookings())->getBookingsByEmail($current_user->user_email))) {
            wp_send_json_error(
                [
                    'message' => "You have already reserved a room!!"
                ],
                423
            );
        }


        if (!isset($_REQUEST['room_id'])) {
            wp_send_json_error(
                [
                    'message' => "Please reload the page to get valid rooms info!"
                ],
                423
            );
        }
        $roomId = intval($_REQUEST['room_id']);
        $room = (new Rooms())->find($roomId);

        if (empty($room)) {
            wp_send_json_error(
                [
                    'message' => "No room found!"
                ],
                423
            );
        }

        $previousBookings = (new Bookings())->getBookingsByRoom($roomId);
        $previousBookingCount = count($previousBookings);

        if ($previousBookingCount >= $room->total_seat) {
            wp_send_json_error(
                [
                    'message' => "No available seat"
                ],
                423
            );
        }

        $data = [
            'name' => $current_user->display_name,
            'email' => $current_user->user_email,
            'user_id' => $current_user->ID,
            'room_id' => $roomId,
            'booked_seat' => 1
        ];

        wp_send_json_success(
            [
                'status' => (new Bookings())->addBooking($data),
                'message' => 'Reservation updates!'
            ]
        );
    }

    public function getRooms()
    {
        wp_send_json_success(
            [
                'rooms' => (new Rooms())->getRooms()
            ]
            , 200);
    }

    public function addRoom()
    {
        if ((new Rooms())->addRoom($_REQUEST['data'])) {
            return wp_send_json_success(
                [
                    'rooms' => (new Rooms())->getRooms()
                ]
                , 200);
        } else {
            return wp_send_json_error([
                'message' => "Can't add room"
            ]);
        }
    }

    public function updateRoom()
    {

        if ((new Rooms())->updateRoom($_REQUEST['data'])) {
            return wp_send_json_success(
                [
                    'rooms' => (new Rooms())->getRooms()
                ]
                , 200);
        } else {
            return wp_send_json_error([
                'message' => "Can't add room"
            ]);
        }
    }
}