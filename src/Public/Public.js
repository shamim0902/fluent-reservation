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
                console.log('hello', response)
            });
    });
})