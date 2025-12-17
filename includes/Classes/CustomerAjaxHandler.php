<?php

namespace fluentReservation\Classes;


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

        ds('sdsd');
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
        ];

        if (isset($validRoutes[$route])) {
            do_action('fluent-reservation/doing_frontend_ajax' . $route);
            $data = isset($_REQUEST['data']) ? sanitize_text_field($_REQUEST['data']) : [];
            return $this->{$validRoutes[$route]}($data);
        } else {
            return [
                'code'    => 404,
                'message' => 'Invalid route',
            ];
        }
    }

    public function getRooms()
    {

        return ['sdsd'];
    }


}