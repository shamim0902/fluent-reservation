<?php

namespace fluentReservation\Models;

class Rooms
{

    public function getRooms()
    {
        return [
            '1' => [
                'floor' => '4',
                'total_seat' => '6',
            ],
            '2' => [
                'floor' => '3',
                'total_seat' => '2',
            ],
        ];
    }

}