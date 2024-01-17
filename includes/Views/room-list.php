<?php
/**
 * @var array $availableRooms
 */
?>
<div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4">
    <?php foreach ($availableRooms as $room): ?>
        <div class="border-2 bg-amber-100 p-4 rounded-2xl" id="<?php echo 'room_id_' . $room->id ?>">
            <h3>Room No: <?php echo  esc_html($room->room_no)?></h3>
            <div>
                <p>Floor No: <?php echo  esc_html($room->floor_no)?></p>
                <p>Occupancy: <?php echo  esc_html($room->total_seat)?></p>
                <p>Available: <?php echo  esc_html($room->total_seat - $room->total_bookings)?></p>
            </div>
            <?php if ($room->status !== 'locked'): ?>
                <p data-id="<?php echo  esc_html($room->id)?>" class="cursor-pointer text-blue-400 fluent_reservation_booking_persons">view booking persons</p>
            <?php endif; ?>
            <?php if (!in_array( $room->id ,$myReservationIds)): ?>
                <button data-id="<?php echo  esc_html($room->id)?>" class="<?php echo $room->status === 'locked' ? 'bg-red-200 hover:bg-red-300' : 'bg-green-500 fluent_reservation_button' ?>" <?php echo $room->status === 'locked' ? 'disabled' : '' ?>>
                    <?php echo $room->status === 'locked' ? 'Booking Locked!' : 'Book My Seat' ?>
                </button>
            <?php else: ?>
                <button data-id="<?php echo  esc_html($room->id)?>" class=" hover:bg-red-300 fluent_reservation_cancel_button' ?>" <?php echo $room->status === 'locked' ? 'disabled' : '' ?>>
                   Cancel My Booking
                </button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
