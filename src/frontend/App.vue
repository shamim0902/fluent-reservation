<script setup>
import {ref, onMounted} from 'vue';
import {ElNotification} from 'element-plus'

const $get = (route = {}) => {
  const options = {};
  options['route'] = route;
  options['nonce'] = window.fluentReservationVars.nonce;
  options.action = "fluent_reservation_frontend_ajax"
  return window.jQuery.get(window.fluentReservationVars.ajax_url, options);
}

const $post = (route = '', data = {}) => {
  return window.jQuery.post(
      window.fluentReservationVars.ajax_url,
      {
        action: 'fluent_reservation_frontend_ajax',
        nonce: window.fluentReservationVars.nonce,
        route: route,
        ...data
      }
  );
};


const rooms = ref([]);
const loading = ref(true);
const hasReservation = ref(false);
const reservations = ref([]);
const hasError = ref(false);


const getRooms = () => {
  $get('getRooms')
      .then(data => {
        manageResponse(data, false);
      })
      .always(function () {
        loading.value = false;
      });
}

const manageResponse = (data, message = '') => {
  if (data.code === 200) {
    rooms.value = data.data.availableRooms;
    reservations.value = data.data.myReservationRoomIds;
    hasReservation.value = reservations.value.length > 0;

    if (message) {
      ElNotification({
        type: 'success',
        message: message,
        title: 'Success',
        offset: 40,
      })
    }


  } else {
    hasError.value = true;
    ElNotification({
      type: 'error',
      message: data.message,
    })
  }
}

const cancelingBooking = ref(false);
const cancelingRoomId = ref(null);
const cancelBooking = (roomId) => {
  if (cancelingBooking.value) {
    return;
  }
  cancelingBooking.value = true;
  cancelingRoomId.value = roomId;
  $post('cancelBooking', {
    'room_id': roomId,
  }).then((data) => {
    manageResponse(data, 'Booking successfully cancelled');
  })
      .always(() => {
        cancelingBooking.value = false;
        cancelingRoomId.value = null;
      });
}

const bookingRoom = ref(false);
const bookingRoomId = ref(null);
const bookRoom = (roomId) => {
  if (bookingRoom.value) {
    return;
  }
  bookingRoom.value = true;
  bookingRoomId.value = roomId;
  $post('bookNow', {
    'room_id': roomId,
  }).then((data) => {
    manageResponse(data, 'Booking successful');
  })
      .always(() => {
        bookingRoom.value = false;
        bookingRoomId.value = null;
      });
}

onMounted(() => {
  getRooms();
})


</script>
<template>
  <div class="fct-customer-app">

    <div>
      <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
        <div class="max-w-6xl mx-auto">
          <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">Room Booking System</h1>
            <p class="text-slate-600">Select and manage your room reservations</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="room in rooms"
                :key="room.id"
                class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden"
            >
              <div :class="room.isBooked ? 'bg-red-500' : 'bg-blue-500'" class="h-2"></div>

              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h2 class="text-2xl font-bold text-slate-800">Room {{ room.room_no }}</h2>
                  </div>
                  <span
                      :class="room.isBooked
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-green-100 text-green-700'"
                      class="px-3 py-1 rounded-full text-xs font-semibold"
                  >
                                    {{ room.isBooked ? 'BOOKED' : 'AVAILABLE' }}
                                </span>
                </div>

                <div class="space-y-3 mb-6">
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600 font-medium">Gender Preference:</span>
                    <span class="text-blue-600 font-semibold uppercase" v-if="room.gender ==='male'">{{
                        room.gender
                      }}</span>
                    <span class="text-pink-500 font-semibold uppercase" v-else>{{ room.gender }}</span>
                  </div>

                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600 font-medium">Occupancy:</span>
                    <div class="flex items-center space-x-1">
                      <span class="text-slate-800 font-semibold">{{ room.total_seat }}</span>
                    </div>
                  </div>

                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600 font-medium">Available:</span>
                    <div class="flex items-center space-x-1">
                      <span class="text-slate-800 font-semibold">{{ room.available }}</span>
                    </div>
                  </div>
                </div>

                <div v-if="room.isBooked" class="mb-4">
                  <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium hover:underline">
                    See people in this room
                  </a>
                </div>


                <template v-if="room.isBookedByMe">
                  <button
                      @click="cancelBooking(room.id)"
                      class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200"
                  >
                    Cancel My Booking
                  </button>
                </template>

                <template v-else-if="!hasReservation">
                  <button
                      v-if="!room.isBooked"
                      @click="bookRoom(room.id)"
                      :disabled="room.available === 0"
                      :class="room.available === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                      class="w-full text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200"
                  >
                    Book My Seat
                  </button>
                </template>


              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

