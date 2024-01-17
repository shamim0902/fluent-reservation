<?php
/**
 * @var array $availableRooms
 */
?>
<div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4">
    <?php foreach ($availableRooms as $room): ?>
        <div class="border-2 bg-amber-100 p-4 rounded-2xl">
            <h3>Room No: <?php echo  esc_html($room->room_no)?></h3>
            <div>
                <p>Floor No: <?php echo  esc_html($room->floor_no)?></p>
                <p>Occupancy: <?php echo  esc_html($room->total_seat)?></p>
                <p>Available: <?php echo  esc_html($room->total_seat - $room->total_bookings)?></p>
            </div>
            <button data-id="<?php echo  esc_html($room->id)?>" class="<?php echo $room->status === 'locked' ? 'bg-red-200 hover:bg-red-300' : 'bg-green-500 fluent_reservation_button' ?>" <?php echo $room->status === 'locked' ? 'disabled' : '' ?>>
                <?php echo $room->status === 'locked' ? 'Booking Locked!' : 'Book My Seat' ?>
            </button>
        </div>
    <?php endforeach; ?>
</div>
