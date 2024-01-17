<?php

namespace fluentReservation\Classes;

class LoadAssets
{
    public function admin()
    {
        Vite::enqueueScript('fluent-reservation-script-boot', 'admin/start.js', array('jquery'), FLUENTRESERVATION_VERSION, true);
        wp_localize_script(
            'fluent-reservation-script-boot',
            'fluentReservationVars',
            [
                'nonce' => wp_create_nonce('fluent_reservation_nonce'),
                'assets_url' => Vite::staticPath(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'preview_url' => site_url('?book_reservation'),
            ]
        );
    }
  
}
