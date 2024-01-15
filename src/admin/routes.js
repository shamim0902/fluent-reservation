import Admin from './Components/Admin.vue';
import Bookings from './Components/Bookings.vue';

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
    }
];