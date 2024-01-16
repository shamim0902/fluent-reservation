<?php

namespace fluentReservation\Models;

class Rooms
{

    protected $table = 'fluent_reservation_rooms';

    public function getRooms(): array
    {
        return fluentReservationDb()->table($this->table)->get();
    }

    public function addRoom($data)
    {
        $data['room_no'] = sanitize_text_field($data['room_no'] ?? '');
        $data['floor_no'] = sanitize_text_field($data['floor_no'] ?? '');
        $data['total_seat'] = (int)sanitize_text_field($data['total_seat'] ?? 0);
        $data['info'] = sanitize_textarea_field($data['info'] ?? '');
        return fluentReservationDb()->table($this->table)->insert($data);
    }
}