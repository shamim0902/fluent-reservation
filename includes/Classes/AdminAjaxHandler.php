<?php

namespace fluentReservation\Classes;

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
        ];

        if (isset($validRoutes[$route])) {
            do_action('fluent-reservation/doing_ajax_forms_' . $route);
            $data = isset($_REQUEST['data']) ? sanitize_text_field($_REQUEST['data']) : [];
            return $this->{$validRoutes[$route]}($data);
        }
        do_action('fluent-reservation/admin_ajax_handler_catch', $route);
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
}