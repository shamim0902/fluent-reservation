<?php

/**
 * Plugin Name: Fluent Reservation
 * Plugin URI: http://wpminers.com/
 * Description: A sample WordPress plugin to implement Vue with tailwind.
 * Author: Hasanuzzaman Shamim
 * Author URI: http://hasanuzzaman.com/
 * Version: 1.0.6
 * Text Domain: fluent-reservation
 */
define('FLUENTRESERVATION_URL', plugin_dir_url(__FILE__));
define('FLUENTRESERVATION_DIR', plugin_dir_path(__FILE__));

define('FLUENTRESERVATION_VERSION', '1.0.5');

// This will automatically update, when you run dev or production
define('FLUENTRESERVATION_DEVELOPMENT', 'yes');

class fluentReservation {
    public function boot()
    {
        $this->loadClasses();
        $this->registerShortCodes();
        $this->ActivatePlugin();
        $this->renderMenu();
        $this->disableUpdateNag();
        $this->loadTextDomain();
        $this->registerAdminAjax();
    }

    public function loadClasses()
    {
        require FLUENTRESERVATION_DIR . 'includes/autoload.php';
    }

    public function registerAdminAjax()
    {
        (new \fluentReservation\Classes\AdminAjaxHandler())->register();
    }

    public function renderMenu()
    {
        add_action('admin_menu', function () {
            if (!current_user_can('manage_options')) {
                return;
            }
            global $submenu;
            add_menu_page(
                'fluentReservation',
                'Fluent Reservation',
                'manage_options',
                'fluent-reservation.php',
                array($this, 'renderAdminPage'),
                'dashicons-editor-code',
                25
            );
            $submenu['fluent-reservation.php']['rooms'] = array(
                'Rooms',
                'manage_options',
                'admin.php?page=fluent-reservation.php#/',
            );
            $submenu['fluent-reservation.php']['bookings'] = array(
                'Bookings',
                'manage_options',
                'admin.php?page=fluent-reservation.php#/bookings',
            );
        });
    }

    /**
     * Main admin Page where the Vue app will be rendered
     * For translatable string localization you may use like this
     * 
     *      add_filter('fluentreservation/frontend_translatable_strings', function($translatable){
     *          $translatable['world'] = __('World', 'fluent-reservation');
     *          return $translatable;
     *      }, 10, 1);
     */
    public function renderAdminPage()
    {
        $loadAssets = new \fluentReservation\Classes\LoadAssets();
        $loadAssets->admin();

        $translatable = apply_filters('fluentreservation/frontend_translatable_strings', array(
            'hello' => __('Hello', 'fluent-reservation'),
        ));

        $fluentreservation = apply_filters('fluentreservation/admin_app_vars', array(
            'assets_url' => FLUENTRESERVATION_URL . 'assets/',
            'ajaxurl' => admin_url('admin-ajax.php'),
            'i18n' => $translatable
        ));

        wp_localize_script('fluentreservation-script-boot', 'fluentreservationAdmin', $fluentreservation);

        echo '<div class="fluentreservation-admin-page" id="fluentreservation_app">
            <div class="main-menu text-white-200 bg-wheat-600 p-4">
                <router-link to="/">
                    Rooms
                </router-link> |
                <router-link to="/bookings" >
                    Bookings
                </router-link>
            </div>
            <hr/>
            <router-view></router-view>
        </div>';
    }

    /*
    * NB: text-domain should match exact same as plugin directory name (Plugin Name)
    * WordPress plugin convention: if plugin name is "My Plugin", then text-domain should be "my-plugin"
    * 
    * For PHP you can use __() or _e() function to translate text like this __('My Text', 'fluent-reservation')
    * For Vue you can use $t('My Text') to translate text, You must have to localize "My Text" in PHP first
    * Check example in "renderAdminPage" function, how to localize text for Vue in i18n array
    */
    public function loadTextDomain()
    {
        load_plugin_textdomain('fluent-reservation', false, basename(dirname(__FILE__)) . '/languages');
    }


    /**
     * Disable update nag for the dashboard area
     */
    public function disableUpdateNag()
    {
        add_action('admin_init', function () {
            $disablePages = [
                'fluent-reservation.php',
            ];

            if (isset($_GET['page']) && in_array($_GET['page'], $disablePages)) {
                remove_all_actions('admin_notices');
            }
        }, 20);
    }


    /**
     * Activate plugin
     * Migrate DB tables if needed
     */
    public function ActivatePlugin()
    {
        register_activation_hook(__FILE__, function ($newWorkWide) {
            require_once(FLUENTRESERVATION_DIR . 'includes/Classes/Activator.php');
            $activator = new \fluentReservation\Classes\Activator();
            $activator->migrateDatabases($newWorkWide);
        });
    }


    /**
     * Register ShortCodes here
     */
    public function registerShortCodes()
    {
        // Use add_shortcode('shortcode_name', 'function_name') to register shortcode
    }
}

(new fluentReservation())->boot();



