<?php

namespace fluentReservation\Classes;

use fluentReservation\Models\Rooms;

class ShortCode
{
    public function register()
    {
        add_shortcode('fluent_reservation', function ($shortcodeAttributes) {
            return $this->render($shortcodeAttributes);
        });

        add_action('init', function(){
            Vite::enqueueStyle('fluent_reservation_frontend_style',
                'style.css',
            );
        });
    }

    public function render($shortcodeAttributes): string
    {
        $availableRooms = (new Rooms())->getBookableRooms();
        ob_start();
        include FLUENTRESERVATION_DIR . 'includes/Views/room-list.php';
        return ob_get_clean();
    }
}