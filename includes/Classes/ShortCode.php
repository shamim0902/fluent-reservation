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
    }

    public function render($shortcodeAttributes): string
    {
        $availableRooms = (new Rooms())->getBookableRooms();
        echo "<pre>";
        var_dump($availableRooms);
        echo "</pre>";
        die();
        return "Hello";
    }
}