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
                Toastify({
                    text: "Reservation done!",
                    duration: 2000
                }).showToast();

                setTimeout(() => {
                    if (response.data.status && window.fluentReservationVars.confirmation_url) {
                        window.location.href = window.fluentReservationVars.confirmation_url + '?room_id=' + response.data.room_id ;
                    } else {
                        window.location.reload();
                    }
                }, 1500)

            }). catch(error => {
                Toastify({
                    text: (error.responseJSON.data.message),
                    duration: 3000,
                    style: {
                        background: "red",
                    },
                }).showToast();
            })
        ;
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
                jQuery(this).removeClass('text-blue-500');
                jQuery(this).addClass('text-blue-500 break-words');
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