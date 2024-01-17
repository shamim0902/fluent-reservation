<template>
  <div>
    <div class="col-12 md:col-6 p-6 text-center md:text-left flex align-items-center ">
      <el-table :data="bookings" style="width: 100%">
        <el-table-column label="Booked By" prop="name"/>
        <el-table-column label="Email" prop="email"/>
        <el-table-column label="Room No" prop="room_no"/>
        <el-table-column label="Floor No" prop="floor_no"/>
      </el-table>
    </div>
  </div>
</template>

<script setup>
import {getCurrentInstance, onMounted, ref} from "vue";

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
</script>