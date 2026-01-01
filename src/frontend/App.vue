<script setup>
import {ref, onMounted, computed} from 'vue';
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

const activeFilter = ref('all');
const searchQuery = ref('');
const availableOnly = ref(false);
const filteredRooms = computed(() => {
  const list = rooms.value || [];
  let genderFiltered = list;
  if (activeFilter.value === 'male') {
    genderFiltered = list.filter(r => r.gender === 'male' || r.gender === 'both');
  } else if (activeFilter.value === 'female') {
    genderFiltered = list.filter(r => r.gender === 'female' || r.gender === 'both');
  }
  const q = (searchQuery.value || '').toString().trim().toLowerCase();
  let searchFiltered = genderFiltered;
  if (q) {
    searchFiltered = genderFiltered.filter(r => {
      const name = (r.name || r.room_no || '').toString().toLowerCase();
      return name.includes(q);
    });
  }
  if (availableOnly.value) {
    searchFiltered = searchFiltered.filter(r => (r.available || 0) > 0);
  }
  return searchFiltered;
});


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
  <div class="fct-customer-app fluentreservation-frontend-app">


    <div>
      <div class=" bg-gradient-to-br from-slate-50 to-slate-100 p-6">
        <div class="max-w-6xl mx-auto">
          <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">Room Booking System</h1>
            <p class="text-slate-600">Select and manage your room reservations</p>
          </div>

          <div class="mb-6">
            <div class="flex items-center justify-between flex-wrap gap-2">
              <div class="flex items-center gap-2">
                <button
                    @click="activeFilter = 'all'"
                    :class="activeFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border border-blue-600'"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition-colors duration-200"
                >
                  All
                </button>
                <button
                    @click="activeFilter = 'male'"
                    :class="activeFilter === 'male' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border border-blue-600'"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition-colors duration-200"
                >
                  Male
                </button>
                <button
                    @click="activeFilter = 'female'"
                    :class="activeFilter === 'female' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border border-blue-600'"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition-colors duration-200"
                >
                  Female
                </button>
                <label class="ml-2 inline-flex items-center gap-2 select-none">
                  <input
                      v-model="availableOnly"
                      type="checkbox"
                      class="w-4 h-4 accent-blue-600"
                  />
                  <span class="text-sm font-semibold text-slate-700">Available Only</span>
                </label>
              </div>
              <div class="ml-auto w-full sm:w-64">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search room number..."
                    class="w-full px-4 py-2 border border-slate-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                />
              </div>
            </div>
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
                v-for="room in filteredRooms"
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
                    <h2 class="text-lg font-bold text-slate-800 !m-0">Room {{ room.room_no }}</h2>
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


                    <div class="flex items-center space-x-2">

                      <template v-if="room.gender ==='male'">

                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21" fill="currentColor">
                          <path
                              d="m7.5.5c1.65685425 0 3 1.34314575 3 3v2c0 1.65685425-1.34314575 3-3 3s-3-1.34314575-3-3v-2c0-1.65685425 1.34314575-3 3-3zm7 14v-.7281753c0-3.1864098-3.6862915-5.2718247-7-5.2718247s-7 2.0854149-7 5.2718247v.7281753c0 .5522847.44771525 1 1 1h12c.5522847 0 1-.4477153 1-1z"
                              fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              transform="translate(3 2)"></path>
                        </svg>
                        <span class="text-blue-600 font-semibold uppercase">{{
                            room.gender
                          }}</span>

                      </template>


                      <template v-else-if="room.gender ==='both'">
                        <svg class="w-4 h-4"
                             viewBox="0 0 583.35 564.13">

                          <g id="Layer_1-2" data-name="Layer 1">
                            <g id="yyMjT6">
                              <g>
                                <circle class="cls-1" cx="104.42" cy="45.06" r="45.05"
                                        transform="translate(-8.58 32.19) rotate(-16.85)"/>
                                <path class="cls-1"
                                      d="M204.56,128.04c-1.66-3.32-2.11-7.26-5.93-9.89-5.79-10.32-20.94-14.05-33.25-14.02l-128.44.39c-12.9.04-24.99,7.88-29.59,18.6C1.8,130.97.16,141.35.15,151.59l-.06,57.74L.01,253.14l.17,20.87-.15,23.08L0,322.71c-.01,9.38,5.36,17.41,13.63,19.77,9.12,2.6,18.53-.73,22.87-9.26,1.77-3.48,3.06-6.24,3.06-11.24l-.02-145.82c.13-.18.27-.35.42-.5,3.73-.91,6.73-.97,10.3.12l.82,267.36c.04,33.65.14,67.34.31,101.08.03,9.46,11.01,16.56,18.75,17.75,10.88,1.67,21.12-2.78,25.43-12.74.61-1.41,2.83-3.12,2.83-5.66l.15-204.84c4.53-.98,8.34-.98,12.95,0l-.05,204.21c0,2.33,1.48,2.63,1.96,4.3,2.72,9.39,12.04,14.96,21.63,15.1,9.68.14,19.63-5.01,22.54-15.11.48-1.68,1.91-2.55,1.91-5.4V176.31c2.92-2.05,6.98-1.82,9.85,0,1.08.96,1.15,3.93,1.15,5.95l-.12,139.68c0,5.23.08,8.06,2.85,12.01,2.13,3.03,3.1,5.25,7.29,7.51,5.34,2.88,14.23,3.54,18.29-1,1.7-.32,3.06-2.94,4.84-4.11,4.09-2.69,5.9-7.35,5.89-13.38l-.31-176.15c-.01-6.97-1.8-13.07-4.65-18.77ZM51.56,256.19v-15.63s.16-8.26.16-8.26c-.06,7.96-.11,15.93-.16,23.89Z"/>
                                <path class="cls-1"
                                      d="M407.1,78.47c15.24,13.97,38.04,15.76,55.28,4.35,16.03-10.61,23.56-30.22,18.75-48.83-4.81-18.61-20.9-32.12-40.07-33.62-19.16-1.51-37.17,9.31-44.84,26.94-7.67,17.63-3.3,38.18,10.87,51.16Z"/>
                                <path class="cls-1"
                                      d="M582.26,302.19l-47.45-158.19c-3.38-11.26-9.08-20.99-17.41-28.82-2.53-2.38-6.52-6.24-9.82-6.13-4.44-2.81-10.32-4.64-16.5-4.65l-106.02-.1c-6.58.62-21.05,4.93-23.63,9.88-12.47,7.18-21.12,34.17-24.04,47.87-5.17,11.65-7.2,24.76-10.9,37l-32.03,105.85c-3.05,10.08,3.65,20.35,12.56,23.21,9.67,3.1,21.2-1.53,24.36-12.15l41.43-139.39c.75-2.51,3.58-2.18,4.95-2.46,1.29-.26,4.96.98,4.41,2.85l-61.79,210.88c-1.19,4.05-.55,7.56,1.64,10.26,2.09,2.58,5.59,4.08,9.45,4.09l52.78.14.49,141.72c.04,10.55,10.51,17.86,18.64,19.56,10.66,2.23,20.46-2.82,25.17-11.39.16-.38.31-.77.46-1.16-.05.2-.09.39-.14.55,2.4-4.49,3.7-8.45,3.7-14.66l-.13-134.62c.08-.02.17-.05.26-.07,3.78-.26,7.06-.26,10.72.02l.25,139.57c.02,9.62,6.94,15.82,13.76,19.26,7.43,3.75,15.5,4.36,23.61-.88,6.31-4.08,11-11.33,11-20.31l.04-137.68,51.4-.1c4.04,0,7.29-2.11,8.96-4.55,2.38-3.47,2.35-6.73,1.18-10.82l-33.03-115.65c-4.11-14.4-7.21-29.22-12.29-43.11-.19-.64-.42-1.29-.71-1.98-3.35-15.62-8.15-31-12.93-46.23.18-1.06-.18-2.76.68-4.12,1.26-2,8.06-2.52,9.02.6l22.01,71.82,18.54,65.56c3.14,11.11,13.33,17.83,24.32,14.72,11.52-3.26,16.51-14.58,13.02-26.2Z"/>
                                <path class="cls-1" d="M428.89,551.61c-.11.2-.21.4-.33.61,0,.48.13.16.33-.61Z"/>
                              </g>
                            </g>
                          </g>
                        </svg>
                        <span class="text-orange-400 font-semibold uppercase">{{
                            room.gender
                          }}</span>
                      </template>

                      <template v-else>

                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21" fill="currentColor">
                          <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linejoin="round"
                             transform="translate(3 2)">
                            <path
                                d="m8.5 2.5 2 2v1c0 1.65685425-1.34314575 3-3 3-1.59768088 0-2.90366088-1.24891996-2.99490731-2.82372721l-.00509269-1.17627279z"
                                stroke-linecap="round"></path>
                            <path
                                d="m2.5 10v-4.5c0-2.76142375 2.23857625-5 5-5 2.7614237 0 5 2.23857625 5 5v4.5"></path>
                            <path
                                d="m14.5 14.5v-.7281753c0-3.1864098-3.6862915-5.2718247-7-5.2718247s-7 2.0854149-7 5.2718247v.7281753c0 .5522847.44771525 1 1 1h12c.5522847 0 1-.4477153 1-1z"
                                stroke-linecap="round"></path>
                          </g>
                        </svg>
                        <span class="text-pink-500 font-semibold uppercase">{{ room.gender }}</span>

                      </template>
                    </div>


                  </div>

                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600 font-medium">Bed:</span>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                           stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                        <path d="M22 17v-3h-20"></path>
                        <path d="M2 8v9"></path>
                        <path d="M12 14h10v-2a3 3 0 0 0 -3 -3h-7v5z"></path>
                      </svg>
                      <span class="text-slate-800 font-semibold">{{ room.total_seat }}</span>
                    </div>
                  </div>

                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600 font-medium">Floor:</span>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                           stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M22 6h-5v5h-5v5h-5v5h-5"></path>
                        <path d="M6 10v-7"></path>
                        <path d="M3 6l3 -3l3 3"></path>
                      </svg>
                      <span class="text-slate-800 font-semibold">{{ room.floor_no || 1 }}</span>
                    </div>
                  </div>

                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600 font-medium">Available:</span>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                           xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 36 36"
                           preserveAspectRatio="xMidYMid meet" fill="currentColor"><title>
                        calendar-outline-badged</title>
                        <path class="clr-i-outline--badged clr-i-outline-path-1--badged"
                              d="M32,13.22V30H4V8H7V6H3.75A1.78,1.78,0,0,0,2,7.81V30.19A1.78,1.78,0,0,0,3.75,32h28.5A1.78,1.78,0,0,0,34,30.19V12.34A7.45,7.45,0,0,1,32,13.22Z"></path>
                        <rect class="clr-i-outline--badged clr-i-outline-path-2--badged" x="8" y="14" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-3--badged" x="14" y="14" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-4--badged" x="20" y="14" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-5--badged" x="26" y="14" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-6--badged" x="8" y="19" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-7--badged" x="14" y="19" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-8--badged" x="20" y="19" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-9--badged" x="26" y="19" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-10--badged" x="8" y="24" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-11--badged" x="14" y="24" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-12--badged" x="20" y="24" width="2"
                              height="2"></rect>
                        <rect class="clr-i-outline--badged clr-i-outline-path-13--badged" x="26" y="24" width="2"
                              height="2"></rect>
                        <path class="clr-i-outline--badged clr-i-outline-path-14--badged"
                              d="M10,10a1,1,0,0,0,1-1V3A1,1,0,0,0,9,3V9A1,1,0,0,0,10,10Z"></path>
                        <path class="clr-i-outline--badged clr-i-outline-path-15--badged"
                              d="M22.5,6H13V8h9.78A7.49,7.49,0,0,1,22.5,6Z"></path>
                        <circle class="clr-i-outline--badged clr-i-outline-path-16--badged clr-i-badge" cx="30" cy="6"
                                r="5"></circle>
                        <rect x="0" y="0" width="36" height="36" fill-opacity="0"></rect>
                      </svg>
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


                <template v-if="room.isBooked && !hasReservation">
                  <div
                      class="border-2 border-dashed border-slate-300 rounded-xl py-3 px-4 text-center bg-slate-50">

                    <div
                        class="text-sm text-slate-500 font-medium !mb-0 flex items-center justify-center rounded-xl cursor-not-allowed">
                      <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" stroke="currentColor"
                           viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                      </svg>
                      Room is full
                    </div>
                  </div>
                </template>


                <template v-if="room.isBookedByMe">
                  <button
                      @click="cancelBooking(room.id)"
                      class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center gap-2"
                      :disabled="cancelingBooking"
                  >
                    <span v-if="cancelingBooking" class="loader-icon block">
                      <svg class="block" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" opacity="0.2" fill="none" stroke="currentColor"
                                stroke-miterlimit="10" stroke-width="2.5"></circle>

                        <path d="m12,2c5.52,0,10,4.48,10,10" fill="none" stroke="currentColor" stroke-linecap="round"
                              stroke-miterlimit="10" stroke-width="2.5">
                          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="0.5s"
                                            from="0 12 12" to="360 12 12" repeatCount="indefinite"></animateTransform>
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
                        <circle cx="12" cy="12" r="10" opacity="0.2" fill="none" stroke="currentColor"
                                stroke-miterlimit="10" stroke-width="2.5"></circle>

                        <path d="m12,2c5.52,0,10,4.48,10,10" fill="none" stroke="currentColor" stroke-linecap="round"
                              stroke-miterlimit="10" stroke-width="2.5">
                          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="0.5s"
                                            from="0 12 12" to="360 12 12" repeatCount="indefinite"></animateTransform>
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

<style lang="scss">
.fluentreservation-frontend-app {
  button {
    &:disabled {
      cursor: not-allowed;
      opacity: 0.5;
    }
  }
}
</style>
