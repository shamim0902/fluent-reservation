<?php

namespace fluentReservation\Models;

class Rooms
{

    public function getRooms(): array
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
    public function addRoom($data)
    {
        $data['room_no'] = sanitize_text_field($data['room_no'] ?? '');
        $data['floor_no'] = sanitize_text_field($data['floor_no'] ?? '');
        $data['total_seat'] = (int)sanitize_text_field($data['total_seat'] ?? 0);
        $data['info'] = sanitize_textarea_field($data['info'] ?? '');

        return false;
    }
}