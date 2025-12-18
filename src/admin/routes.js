import Admin from './Components/Admin.vue';
import Bookings from './Components/Bookings.vue';
import SettingsView from './Components/SettingsView.vue';
import GeneralSettings from './Components/Settings/GeneralSettings.vue';

export default [{
        path: '/',
        name: 'rooms',
        component: Admin,
        meta: {
            active: 'rooms'
        },
    },
    {
        path: '/bookings',
        name: 'bookings',
        component: Bookings
    },
    {
        path: '/settings',
        component: SettingsView,
        meta: {
            active_menu: 'settings',
            title: 'Settings',
        },
        children: [
            {
                path: '',
                name: 'settings',
                component: GeneralSettings,
                meta: {
                    title: 'Settings',
                },
            }
        ]
    }

];
