<?php

namespace fluentReservation\Classes;

use fluentReservation\Models\Bookings;
use fluentReservation\Models\Rooms;

class ShortCode
{
    public function register()
    {
        add_shortcode('fluent_reservation', function ($shortcodeAttributes) {
            return $this->render($shortcodeAttributes);
        });


        add_action('init', function(){
            Vite::enqueueScript('fluent_reservation_frontend_script',
            'Public/Public.js',
                ['jquery'],
                '1.0.1',
                false
            );
            Vite::enqueueStyle('fluent_reservation_frontend_style',
                'style.css',
            );

            wp_localize_script(
                'fluent_reservation_frontend_script',
                'fluentReservationVars',
                [
                    'nonce' => wp_create_nonce('fluent_reservation_nonce'),
                    'assets_url' => Vite::staticPath(),
                    'ajax_url' => admin_url('admin-ajax.php'),
                ]
            );
        });


    }

    public function render($shortcodeAttributes): string
    {
        if (!is_user_logged_in()) {
            return '<h3 style="color:red">Please log in to Book your room!</h3>';
        }

        global $current_user;

        $myRooms = (new Bookings())->getMyBookings($current_user->ID, ['room_id']);

        $myReservationIds = [];
        foreach ($myRooms as $key=>$value) {
            $myReservationIds[] = $value->room_id;
        }
        $myReservationIds = array_unique($myReservationIds);

        $availableRooms = (new Rooms())->getBookableRooms();
        ob_start();
        include FLUENTRESERVATION_DIR . 'includes/Views/room-list.php';
        return ob_get_clean();
    }
}