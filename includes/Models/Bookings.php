<?php

namespace fluentReservation\Models;

class Bookings
{

    protected $table = 'fluent_reservation_bookings';

    public function getBookings(): array
    {
        return fluentReservationDb()->table($this->table)->get();
    }

    public function getBookingsByRoom($roomId): array
    {
        return fluentReservationDb()->table($this->table)->where('room_id', $roomId)->get();
    }

    public function getBookingsByEmail($email): array
    {
        return fluentReservationDb()->table($this->table)->where('email', $email)->get();
    }

    public function addBooking($data)
    {
        return fluentReservationDb()->table($this->table)->insert($data);
    }
}