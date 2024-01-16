<?php
/**
 * @var array $availableRooms
 */
?>
<div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5">
    <?php foreach ($availableRooms as $room): ?>
        <div class="">
            <p>Room No: <?php echo  esc_html($room->room_no)?></p>
            <p>Floor No: <?php echo  esc_html($room->floor_no)?></p>
            <p>Occupancy: <?php echo  esc_html($room->total_seat)?></p>
            <p>Available: <?php echo  esc_html($room->total_seat - $room->total_bookings)?></p>
        </div>
    <?php endforeach; ?>
</div>
