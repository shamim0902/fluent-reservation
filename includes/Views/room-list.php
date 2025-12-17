<?php
/**
 * @var array $availableRooms
 * @var array $myReservationIds
 */
?>

<div id="room-list-app">

</div>
<div class="grid grid-cols-1 md:grid-cols-2  gap-6 pt-5">
    <?php foreach ($availableRooms as $room): ?>
        <div class="room_box border border-solid border-gray-200 bg-white p-4 rounded-lg  <?php echo esc_html($room->info) ?>"
             id="<?php echo 'room_id_' . $room->id ?>">

            <div class="text-[15px]">
                <p class="!mb-1 text-gray-900"> Name: <span
                            class="text-gray-500"><?php echo esc_html($room->room_no) ?></span></p>
                <p class="!mb-1 text-gray-900">Gender Preference: <span
                            class="text-gray-500" style="font-weight:bold;color:#dc2626"><?php echo esc_html($room->gender) ?></span></p>

                <!--<p class="!mb-1 text-gray-900">Info: <span-->
                <!--            class="text-gray-500"><?php echo esc_html($room->info) ?></span></p>            -->

                <p class="!mb-1 text-gray-900">Occupancy: <span
                            class="text-gray-500"><?php echo esc_html($room->total_seat) ?></span></p>
                <p class="!mb-1 text-gray-900">Available: <span
                            class="text-gray-500"><?php echo esc_html($room->total_seat - $room->total_bookings) ?></span>
                </p>
            </div>
            <?php if ($room->status !== 'locked' && ( $room->total_bookings)): ?>
                <p data-id="<?php echo esc_html($room->id) ?>"
                   class="mt-3 cursor-pointer text-blue-500 text-[15px] fluent_reservation_booking_persons">See people
                    in this room</p>
            <?php endif; ?>
            <?php if (!in_array($room->id, $myReservationIds) && ($room->total_seat - $room->total_bookings) !== 0): ?>
                <?php if (count($myReservationIds) === 0): ?>
                    <button data-id="<?php echo esc_html($room->id) ?>"
                            class="bg-blue-500 border-solid border border-blue-500 rounded-lg py-[10px] text-white text-[15px] px-4 w-full transition duration-200 hover:border-blue-600 hover:bg-blue-600 <?php echo $room->status === 'locked' ? 'bg-red-500 hover:bg-red-500' : 'fluent_reservation_button' ?>" <?php echo $room->status === 'locked' ? 'disabled' : '' ?>>
                        <?php echo $room->status === 'locked' ? 'Room Locked!' : 'Book My Seat' ?>
                    </button>
                <?php endif; ?>

            <?php elseif (in_array($room->id, $myReservationIds)): ?>
                <button data-id="<?php echo esc_html($room->id) ?>"
                        class="bg-red-500 border-solid border border-red-500 rounded-lg py-[10px] text-white text-[15px] px-4 w-full transition duration-200 hover:border-red-600 hover:bg-red-600 fluent_reservation_cancel_button" <?php echo $room->status === 'locked' ? 'disabled' : '' ?>>
                    Cancel My Booking
                </button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
