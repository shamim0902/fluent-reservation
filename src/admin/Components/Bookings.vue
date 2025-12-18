<template>
  <div>
    <div class="fluent_reservation_page_heading_wrap">
      <h1 class="fluent_reservation_page_heading--title">Booking</h1>
      <div class="fluent_reservation_page_heading--actions">
        <el-button @click="openAddBookingModal" type="primary">Add Booking</el-button>
      </div>
    </div>


    <div class="fluentreservation_table_wrap">
      <div class="fluentreservation_table_header">
        <div class="fluentreservation_table_header_inner">
          <div class="fluentreservation_table_header_inner_left">
            <el-input v-model="search" placeholder="Search" size="large" clearable @keyup.enter="getBookings" @clear="getBookings" />
          </div>
        </div>
      </div>

      <div class="fluentreservation_table_body">
        <el-table :data="bookings" style="width: 100%">
          <el-table-column label="Booked By" prop="name"/>
          <el-table-column label="Email" prop="email"/>
          <el-table-column label="Room No" prop="room_no"/>
          <el-table-column label="Floor No" prop="floor_no" />
          <el-table-column>
            <template #default="scope">
              <el-button size="small" @click="confirmDelete(scope.row.id)">Delete</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <div v-if="false" class="col-12 md:col-6 p-6 text-center md:text-left flex align-items-center ">
      <el-table :data="bookings" style="width: 100%">
        <el-table-column label="Booked By" prop="name"/>
        <el-table-column label="Email" prop="email"/>
        <el-table-column label="Room No" prop="room_no"/>
        <el-table-column label="Floor No" prop="floor_no"/>
        <el-table-column>
          <template #default="scope">
            <el-button type="danger" size="small" @click="confirmDelete(scope.row.id)">delete</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>


    <el-dialog v-model="showAddBookingModal" title="Add Room">
      <BookingAddForm ref="room_form" @on-success="onBookingAdded"/>
    </el-dialog>
  </div>
</template>

<script setup>
import {getCurrentInstance, onMounted, ref} from "vue";
import BookingAddForm from "./Forms/BookingAddForm.vue";

const $this = getCurrentInstance().ctx;
const bookings = ref([]);
const search = ref('');
onMounted(() => {
  getBookings();
});


const getBookings = () => {
  $this.$adminAjax({
    route: 'getBookings',
    'search': search.value,
    nonce: window.fluentReservationVars.nonce
  })
      .then(res => {
        bookings.value = res.data.bookings;
      })
}

const showAddBookingModal = ref(false)
const openAddBookingModal = () => {
  showAddBookingModal.value = true;
}

const confirmDelete = id => {
  if (confirm("Delete this reservation?") == true) {
    deleteReservation(id);
  } else {
  }
}

const deleteReservation = id => {
  $this.$adminAjax({
    method:'post',
    route: 'deleteBookings',
    booking_id: id,
    nonce: window.fluentReservationVars.nonce
  })
      .then(res => {
        if (res.data.status) {
          getBookings();
        }
      })
}

const onBookingAdded = () => {
  showAddBookingModal.value = false;
  getBookings();
}
</script>
