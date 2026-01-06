<?php

namespace fluentReservation\Models;

class Events
{

    protected $table = 'fluent_reservation_events';

    public function getEvents($requestParams = []): array
    {
        $query = fluentReservationDb()->table($this->table);

        if (isset($requestParams['search']) && !empty($requestParams['search'])) {
            $query->where('title', 'LIKE', '%' . $requestParams['search'] . '%')
                ->orWhere('description', 'LIKE', '%' . $requestParams['search'] . '%');
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }

    public function addEvent($data)
    {
        $data['created_at'] = current_time('mysql');
        $data['updated_at'] = current_time('mysql');
        return fluentReservationDb()->table($this->table)->insert($data);
    }

    public function updateEvent($id, $data)
    {
        $data['updated_at'] = current_time('mysql');
        return fluentReservationDb()->table($this->table)->where('id', $id)->update($data);
    }

    public function deleteEvent($id)
    {
        return fluentReservationDb()->table($this->table)->where('id', $id)->delete();
    }

    public function find($id)
    {
        return fluentReservationDb()->table($this->table)->find($id);
    }
}
