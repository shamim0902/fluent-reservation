<?php

namespace fluentReservation\Classes;

use fluentReservation\Models\Bookings;
use fluentReservation\Models\Rooms;

class ShortCode
{
    public function register()
    {

        add_shortcode('fluent_reservation', function ($atts) {
            return ShortCode::render($atts);
        });

    }

    public static function render($shortcodeAttributes): string
    {
        if (!is_user_logged_in()) {
            ob_start();
            include FLUENTRESERVATION_DIR . 'includes/Views/log-in.php';
            return ob_get_clean();
        }


        Vite::enqueueScript('fluent_reservation_frontend_script_cart',
            'frontend/start.js',
            '1.0.1',
            false
        );

        Vite::enqueueScript('fluent_reservation_frontend_script',
            'Public/Public.js',
            ['jquery'],
            '1.0.1',
            false
        );
        Vite::enqueueStyle('fluent_reservation_frontend_style',
            'style.css',
        );

        wp_enqueue_script('toastify-js-scripts', 'https://cdn.jsdelivr.net/npm/toastify-js', ['jquery']);
        wp_enqueue_style('toastify-js-styles', 'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css');

        wp_localize_script(
            'fluent_reservation_frontend_script',
            'fluentReservationVars',
            [
                'confirmation_url' => get_option('fluent_reservation_confirmation_url', ''),
                'nonce'            => wp_create_nonce('fluent_reservation_nonce'),
                'assets_url'       => Vite::staticPath(),
                'ajax_url'         => admin_url('admin-ajax.php'),
            ],
        );


        global $current_user;

        $myRooms = (new Bookings())->getMyBookings($current_user->ID, ['room_id']);

        $myReservationIds = [];
        foreach ($myRooms as $key => $value) {
            $myReservationIds[] = $value->room_id;
        }
        $myReservationIds = array_unique($myReservationIds);

        $availableRooms = (new Rooms())->getBookableRooms();

        ob_start();
        include FLUENTRESERVATION_DIR . 'includes/Views/room-list.php';
        return ob_get_clean();
    }
}