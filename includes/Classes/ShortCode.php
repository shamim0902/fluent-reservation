<?php

namespace fluentReservation\Classes;

class ShortCode
{
    public function register()
    {
        add_shortcode('fluent_products', function ($shortcodeAttributes) {
            return $this->render($shortcodeAttributes);
        });
    }

    public function render($shortcodeAttributes): string
    {
        return "";
    }
}