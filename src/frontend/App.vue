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
        setTimeout(() => {
          loading.value = false;
        }, 1000);

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
      <div class=" bg-gradient-to-br from-slate-50 to-slate-100 p-6">
        <div class="max-w-6xl mx-auto">
          <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">Room Booking System</h1>
            <p class="text-slate-600">Select and manage your room reservations</p>
          </div>

          <div v-if="loading" class="p-4 bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
            <div class="text-center">
              <div class="relative inline-flex">
                <div class="w-20 h-20 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                  <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                </div>
              </div>
              <p class="mt-4 !mb-0 text-slate-600 font-medium">Loading rooms...</p>
            </div>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                    <h2 class="text-2xl font-bold text-slate-800 m-0">Room {{ room.room_no }}</h2>
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

                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600 font-medium">Booked:</span>
                    <div class="flex items-center space-x-1">
                      <div v-if="room.persons.length > 0">

                        <el-popover placement="right" :width="400" trigger="hover">
                          <template #reference>

                            <p class="!mb-0 cursor-pointer">
                              <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"
                                   fill="currentColor">
                                <path
                                    d="M140,176a4,4,0,0,1-4,4,12,12,0,0,1-12-12V128a4,4,0,0,0-4-4,4,4,0,0,1,0-8,12,12,0,0,1,12,12v40a4,4,0,0,0,4,4A4,4,0,0,1,140,176ZM124,92a8,8,0,1,0-8-8A8,8,0,0,0,124,92Zm104,36A100,100,0,1,1,128,28,100.11,100.11,0,0,1,228,128Zm-8,0a92,92,0,1,0-92,92A92.1,92.1,0,0,0,220,128Z"></path>
                              </svg>
                            </p>
                          </template>
                          <div class="">
                            <p class="!mb-0 cursor-pointer px-2 py-2" v-for="person in room.persons"
                               style="border-bottom: 1px solid #eaecf0">
                              {{ person.name }} {{ person.isYourself ? "(By You)" : "" }}
                              <br>
                              {{ person.email }}
                            </p>
                          </div>
                        </el-popover>

                      </div>
                      <span class="text-slate-800 font-semibold">{{ room.total_seat - room.available }}</span>
                    </div>
                  </div>
                </div>


                <template v-if="room.isBookedByMe">
                  <button
                      @click="cancelBooking(room.id)"
                      class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center gap-2"
                      :disabled="cancelingBooking"
                  >
                    <span v-if="cancelingBooking" class="loader-icon block">
                      <svg class="block" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" opacity="0.2" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2.5"></circle>

                        <path d="m12,2c5.52,0,10,4.48,10,10" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2.5">
                          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="0.5s" from="0 12 12" to="360 12 12" repeatCount="indefinite"></animateTransform>
                        </path>
                      </svg>
                    </span>
                    Cancel My Booking
                  </button>
                </template>

                <template v-else>
                  <div v-if="hasReservation"
                       class="border-2 border-dashed border-slate-300 rounded-xl py-3 px-4 text-center bg-slate-50">

                    <div
                        class="text-sm text-slate-500 font-medium !mb-0 flex items-center justify-center rounded-xl cursor-not-allowed">
                      <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" stroke="currentColor"
                           viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                      </svg>
                      Already booked another room
                    </div>
                  </div>


                  <button
                      v-if="!room.isBooked && !hasReservation"
                      @click="bookRoom(room.id)"
                      :disabled="room.available === 0 || bookingRoom"
                      :class="room.available === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                      class="w-full text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200  flex items-center justify-center gap-2"
                  >
                    <span v-if="bookingRoom && bookingRoomId === room.id" class="loader-icon block">
                      <svg class="block" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" opacity="0.2" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2.5"></circle>

                        <path d="m12,2c5.52,0,10,4.48,10,10" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2.5">
                          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="0.5s" from="0 12 12" to="360 12 12" repeatCount="indefinite"></animateTransform>
                        </path>
                      </svg>
                    </span>
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

