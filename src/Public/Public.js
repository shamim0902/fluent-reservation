jQuery(function() {
    jQuery('.fluent_reservation_button').click(function() {
        const id = jQuery(this).data('id');

        jQuery.post(window.fluentReservationVars.ajax_url, {
            action: 'fluent_reservation_admin_ajax',
            route: 'bookNow',
            room_id: id,
            nonce: window.fluentReservationVars.nonce,
        })
            .then(response => {
                if (response.data.status) {
                    window.location.reload();
                }
            });
    });

    jQuery('.fluent_reservation_booking_persons').click(function() {
        const id = jQuery(this).data('id');

        jQuery.post(window.fluentReservationVars.ajax_url, {
            action: 'fluent_reservation_admin_ajax',
            route: 'seeBookingPersons',
            room_id: id,
            nonce: window.fluentReservationVars.nonce,
        })
            .then(response => {
                jQuery(this).html('Person in this room: ' + response.data.bookings);
                jQuery(this).removeClass('text-blue-400');
                jQuery(this).addClass('text-black-400 text-center break-words');
            });
    });

    jQuery('.fluent_reservation_cancel_button').click(function() {
        const id = jQuery(this).data('id');

        jQuery.post(window.fluentReservationVars.ajax_url, {
            action: 'fluent_reservation_admin_ajax',
            route: 'cancelBooking',
            room_id: id,
            nonce: window.fluentReservationVars.nonce,
        })
            .then(response => {
                if (response.data.status) {
                    window.location.reload();
                }
            });
    });


})