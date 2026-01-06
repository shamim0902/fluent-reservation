<?php

namespace fluentReservation\Classes;

use fluentReservation\Models\Bookings;
use fluentReservation\Models\Rooms;
use fluentReservation\Models\Events;

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
            'getRooms'             => 'getRooms',
            'addRoom'              => 'addRoom',
            'updateRoom'           => 'updateRoom',
            'bookNow'              => 'bookNow',
            'seeBookingPersons'    => 'seeBookingPersons',
            'cancelBooking'        => 'cancelBooking',
            'getBookings'          => 'getBookings',
            'getAdminBookableRoom' => 'getAdminBookableRoom',
            'addAdminBooking'      => 'addAdminBooking',
            'updateAdminBooking'   => 'updateAdminBooking',
            'deleteBookings'       => 'deleteBookings',
            'getEvents'            => 'getEvents',
            'addEvent'             => 'addEvent',
            'updateEvent'          => 'updateEvent',
            'deleteEvent'          => 'deleteEvent',
            'deleteRooms'          => 'deleteRooms',
            'updateConfirmation'   => 'updateConfirmation',
            'getConfirmation'      => 'getConfirmation'
        ];

        if (isset($validRoutes[$route])) {
            do_action('fluent-reservation/doing_ajax_forms_' . $route);
            $data = isset($_REQUEST['data']) ? sanitize_text_field($_REQUEST['data']) : [];
            return $this->{$validRoutes[$route]}($data);
        }
        do_action('fluent-reservation/admin_ajax_handler_catch', $route);
    }

    public function updateAdminBooking()
    {
        $bookingModel = (new Bookings());
        $data = $_REQUEST['data'];

        if (empty($data['id'])) {
            wp_send_json_error(['message' => 'Booking ID missing'], 423);
        }

        $bookingId = intval($data['id']);
        $existingBooking = $bookingModel->find($bookingId);

        if (!$existingBooking) {
            wp_send_json_error(['message' => 'Booking not found'], 404);
        }

        if (empty($data['room_id'])) {
            wp_send_json_error(
                ['message' => 'Please select room!'],
                423
            );
        }

        $roomId = intval($data['room_id']);

        if ($roomId != $existingBooking->room_id) {
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
                        'message' => "No available seat in the selected room"
                    ],
                    423
                );
            }
        }

        $email = sanitize_email($data['email']);
        //get userid by email
        $user = get_user_by('email', $email);
        if ($user) {
            $data['user_id'] = $user->ID;
        } else {
            $data['user_id'] = 0;
        }
        
        $updateData = [
            'name'    => sanitize_text_field($data['name']),
            'email'   => $email,
            'user_id' => $data['user_id'],
            'room_id' => $roomId
        ];

        $bookingModel->updateBooking($bookingId, $updateData);

        wp_send_json_success(
            [
                'message' => 'Booking updated successfully'
            ]
            , 200
        );
    }

    public function deleteBookings()
    {
        if (empty($_REQUEST['booking_id'])) {
            wp_send_json_error(
                [
                    'message' => 'No Booking selected!'
                ],
                423
            );
        }

        (new Bookings())->deleteBookings(intval($_REQUEST['booking_id']));

        wp_send_json_success([
            'message' => 'Booking deleted!',
            'status'  => true
        ], 200);

    }

    public function getConfirmation()
    {
        $confirmationUrl = get_option('fluent_reservation_confirmation_url');
        wp_send_json_success(
            [
                'confirmation_url' => $confirmationUrl ? $confirmationUrl : '',
                'message'          => 'fetched'
            ]
        );
    }

    public function updateConfirmation()
    {
        if (!isset($_REQUEST['confirmation_url'])) {
            wp_send_json_error(
                [
                    'message' => 'Please provide a valid url!'
                ],
                423
            );
        }

        update_option('fluent_reservation_confirmation_url', sanitize_url($_REQUEST['confirmation_url']));

        wp_send_json_success(
            [
                'message' => 'Update success!'
            ], 200
        );
    }

    public function deleteRooms()
    {
        if (empty($_REQUEST['room_id'])) {
            wp_send_json_error(
                [
                    'message' => 'No Room selected!'
                ],
                423
            );
        }

        (new Rooms())->deleteRooms(intval($_REQUEST['room_id']));

        wp_send_json_success([
            'message' => 'Rooms deleted!',
            'status'  => true
        ], 200);

    }

    public function addAdminBooking()
    {
        $booking = (new Bookings());
        $data = $_REQUEST['data'];

        if (empty($data['room_id'])) {
            wp_send_json_error(
                ['message' => 'Please select room!'],
                423
            );
        }

        $roomId = intval($data['room_id']);
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

        $email = sanitize_email($data['email']);
        //get userid by email
        $user = get_user_by('email', $email);
        if ($user) {
            $data['user_id'] = $user->ID;
        } else {
            $data['user_id'] = 0;
        }


        wp_send_json_success(
            [
                'bookings' => $booking->addBooking($data)
            ]
            , 200
        );
    }

    public function getBookings()
    {

        $data = [
            'search' => isset($_REQUEST['search']) ? $_REQUEST['search'] : ''
        ];

        wp_send_json_success(
            [
                'bookings' => (new Bookings())->getBookings($data)
            ]
            , 200
        );
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
                'status'  => true
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
            'status'   => true
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
            'name'        => $current_user->display_name,
            'email'       => $current_user->user_email,
            'user_id'     => $current_user->ID,
            'room_id'     => $roomId,
            'booked_seat' => 1
        ];

        $res = (new Bookings())->addBooking($data);
        if ($res) {
            wp_send_json_success(
                ['status'  => true,
                 'room_id' => (new Bookings())->getBookings($res)[0]->room_no,
                 'message' => 'Reservation updates!'
                ], 200
            );
        }

    }

    public function getRooms()
    {
        $data = [
            'search' => isset($_REQUEST['search']) ? $_REQUEST['search'] : ''
        ];
        wp_send_json_success(
            [
                'rooms' => (new Rooms())->getRooms($data)
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

    public function getAdminBookableRoom()
    {
        $roomId = isset($_REQUEST['room_id']) ? intval($_REQUEST['room_id']) : 0;
        wp_send_json_success(
            [
                'rooms' => (new Rooms())->getAdminBookableRooms($roomId)
            ]
            , 200);
    }

    public function getEvents()
    {
        $data = [
            'search' => isset($_REQUEST['search']) ? sanitize_text_field($_REQUEST['search']) : ''
        ];
        wp_send_json_success(
            [
                'events' => (new Events())->getEvents($data)
            ]
            , 200);
    }

    public function addEvent()
    {
        $data = $_REQUEST['data'];
        
        // Basic validation
        if (empty($data['title'])) {
            wp_send_json_error(['message' => 'Event Title is required'], 423);
        }
        if (empty($data['start_date']) || empty($data['end_date'])) {
            wp_send_json_error(['message' => 'Start Date and End Date are required'], 423);
        }

        // Sanitize
        $eventData = [
            'title' => sanitize_text_field($data['title']),
            'description' => wp_kses_post($data['description']),
            'start_date' => sanitize_text_field($data['start_date']),
            'end_date' => sanitize_text_field($data['end_date'])
        ];

        if ($id = (new Events())->addEvent($eventData)) {
            wp_send_json_success(
                [
                    'message' => 'Event added successfully',
                    'id' => $id,
                    'events' => (new Events())->getEvents()
                ]
                , 200);
        } else {
            wp_send_json_error([
                'message' => "Can't add event"
            ]);
        }
    }

    public function updateEvent()
    {
        $data = $_REQUEST['data'];
        $id = intval($data['id']);

        if (!$id) {
             wp_send_json_error(['message' => 'Event ID is required'], 423);
        }

        // Basic validation
        if (empty($data['title'])) {
            wp_send_json_error(['message' => 'Event Title is required'], 423);
        }
        if (empty($data['start_date']) || empty($data['end_date'])) {
            wp_send_json_error(['message' => 'Start Date and End Date are required'], 423);
        }

        // Sanitize
        $eventData = [
            'title' => sanitize_text_field($data['title']),
            'description' => wp_kses_post($data['description']),
            'start_date' => sanitize_text_field($data['start_date']),
            'end_date' => sanitize_text_field($data['end_date'])
        ];

        (new Events())->updateEvent($id, $eventData);

        wp_send_json_success(
            [
                'message' => 'Event updated successfully',
                'events' => (new Events())->getEvents()
            ]
            , 200);
    }

    public function deleteEvent()
    {
        $id = intval($_REQUEST['event_id']);
        if (!$id) {
            wp_send_json_error(['message' => 'Invalid Event ID'], 423);
        }

        (new Events())->deleteEvent($id);

        wp_send_json_success([
            'message' => 'Event deleted!',
            'status'  => true
        ], 200);
    }
}
