<?php

namespace fluentReservation\Models;

class Bookings
{

    protected $table = 'fluent_reservation_bookings';

    public function getBookings(): array
    {
        return fluentReservationDb()->table($this->table)->get();
    }


    public function cancelMyBooking($roomId)
    {
        global $current_user;
        return fluentReservationDb()->table($this->table)->where('room_id', $roomId)->where('user_id', $current_user->ID)->delete();
    }

    public function getBookingsByRoom($roomId): array
    {
        return fluentReservationDb()->table($this->table)->where('room_id', $roomId)->get();
    }

    public function getBookingPersons($roomId)
    {
        return fluentReservationDb()->table($this->table)->where('room_id', $roomId)->get();
    }

    public function getMyBookings($userId, array $select)
    {
        return fluentReservationDb()->table($this->table)->select($select??'*')->where('user_id', $userId)->get();
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