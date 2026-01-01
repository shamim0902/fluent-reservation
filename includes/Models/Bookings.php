<?php

namespace fluentReservation\Models;

class Bookings
{

    protected $table = 'fluent_reservation_bookings';

    public function getBookings($requestParams = []): array
    {
        global $wpdb;

        $query = fluentReservationDb()
            ->table($this->table)
            ->leftJoin(
                'fluent_reservation_rooms',
                'fluent_reservation_rooms.id',
                '=',
                'fluent_reservation_bookings.room_id'
            )
            ->select(
                "$this->table.*",
                'fluent_reservation_rooms.room_no',
                fluentReservationDb()->raw("{$wpdb->prefix}fluent_reservation_rooms.id AS main_room_id"),
                'fluent_reservation_rooms.floor_no',
            );

        if (isset($requestParams['search']) && !empty($requestParams['search'])) {
            $query->where('fluent_reservation_bookings.name', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('fluent_reservation_bookings.email', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('fluent_reservation_bookings.room_id', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('fluent_reservation_bookings.info', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('fluent_reservation_bookings.user_id', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('fluent_reservation_bookings.booked_seat', 'LIKE', '%' . $requestParams['search'] . '%');
        }
        return $query->get();
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

    public function deleteBookings($booking_id)
    {
        return fluentReservationDb()->table($this->table)->where('id', $booking_id)->delete();
    }

    public function getBookingPersons($roomId)
    {
        return fluentReservationDb()->table($this->table)->where('room_id', $roomId)->get();
    }

    public function getMyBookings($userId, array $select)
    {
        return fluentReservationDb()->table($this->table)
            ->join(
                'fluent_reservation_rooms',
                'fluent_reservation_rooms.id',
                '=',
                'fluent_reservation_bookings.room_id'
            )
            ->select($select ?? '*')
            ->where('user_id', $userId)->get();
    }

    public function getBookingsByEmail($email): array
    {
        return fluentReservationDb()->table($this->table)->where('email', $email)->get();
    }

    public function addBooking($data)
    {
        return fluentReservationDb()->table($this->table)->insert($data);
    }

    public function getBookingsOfRooms($roomIds){
        return fluentReservationDb()->table($this->table)->whereIn('room_id', $roomIds)->get();
    }
}
