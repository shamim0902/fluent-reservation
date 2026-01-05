<?php

namespace fluentReservation\Models;

class Rooms
{

    protected $table = 'fluent_reservation_rooms';

    public function getRooms($requestParams = []): array
    {
        $query = fluentReservationDb()->table($this->table);

        if (isset($requestParams['search']) && !empty($requestParams['search'])) {
            $query->where('room_no', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('floor_no', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('gender', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('total_seat', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('status', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('info', 'LIKE', '%' . $requestParams['search'] . '%');
        }

        return $query->get();
    }

    public function deleteRooms($roomId)
    {
        return fluentReservationDb()->table($this->table)->where('id', $roomId)->delete();
    }

    public function getAdminBookableRooms($roomId = null): array
    {
        global $wpdb;

        $query = fluentReservationDb()
            ->table('fluent_reservation_rooms')
            ->leftJoin(
                'fluent_reservation_bookings',
                'fluent_reservation_rooms.id',
                '=',
                'fluent_reservation_bookings.room_id'
            )
            ->select(
                'fluent_reservation_rooms.*',
                fluentReservationDb()->raw("COUNT({$wpdb->prefix}fluent_reservation_bookings.id) AS total_bookings")
            )
            ->groupBy('fluent_reservation_rooms.id')
            ->groupBy('fluent_reservation_rooms.total_seat');

        // Seats available
        $query->having(
            'total_bookings',
            '<',
            fluentReservationDb()->raw($wpdb->prefix . 'fluent_reservation_rooms.total_seat')
        );

        // Always include this room
        if ($roomId) {
            $query->orHaving(
                'fluent_reservation_rooms.id',
                '=',
                (int) $roomId
            );
        }

        return $query->get();
    }






    public function find($id): ?\stdClass
    {
        return fluentReservationDb()->table($this->table)->find($id);
    }

    public function getBookableRooms(): array
    {
        global $wpdb;
        return fluentReservationDb()
            ->table('fluent_reservation_rooms')
            ->leftJoin(
                'fluent_reservation_bookings',
                'fluent_reservation_rooms.id',
                '=',
                'fluent_reservation_bookings.room_id'
            )
            ->select(
                'fluent_reservation_rooms.*',
                fluentReservationDb()->raw("COUNT({$wpdb->prefix}fluent_reservation_bookings.id) AS total_bookings")
            )
//            ->where('status','!=','locked')
            ->groupBy('fluent_reservation_rooms.id')
            ->groupBy('fluent_reservation_rooms.total_seat')
//            ->having('total_bookings', '<', fluentReservationDb()->raw($wpdb->prefix . 'fluent_reservation_rooms.total_seat'))
            ->get();


    }

    public function addRoom($data)
    {
        $data['room_no'] = sanitize_text_field($data['room_no'] ?? '');
        $data['floor_no'] = sanitize_text_field($data['floor_no'] ?? '');
        $data['total_seat'] = (int)sanitize_text_field($data['total_seat'] ?? 0);
        $data['info'] = sanitize_textarea_field($data['info'] ?? '');
        return fluentReservationDb()->table($this->table)->insert($data);
    }

    public function updateRoom($data)
    {
        $data['room_no'] = sanitize_text_field($data['room_no'] ?? '');
        $data['floor_no'] = sanitize_text_field($data['floor_no'] ?? '');
        $data['total_seat'] = (int)sanitize_text_field($data['total_seat'] ?? 0);
        $data['info'] = sanitize_textarea_field($data['info'] ?? '');
        fluentReservationDb()
            ->table($this->table)
            ->where('id', $data['id'])
            ->update($data);
        return true;
    }

}
