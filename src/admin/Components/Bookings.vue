<template>
  <div>
    <div class="col-12 md:col-6 p-6 text-center md:text-left flex align-items-center ">
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
  </div>

  <div style="margin-top: 20px">
    <el-button @click="openAddBookingModal">Add Booking</el-button>
    <el-dialog v-model="showAddBookingModal" title="Add Room">
      <BookingAddForm ref="room_form" @on-success="onBookingAdded"/>
    </el-dialog>
  </div>
</template>

<script setup>
import {getCurrentInstance, onMounted, ref} from "vue";
import BookingAddForm from "./Forms/BookingAddForm.vue";

const $this = getCurrentInstance().ctx;
const bookings = ref([])
onMounted(() => {
  getBookings();
});


const getBookings = () => {
  $this.$adminAjax({
    route: 'getBookings',
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