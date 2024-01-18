<script setup>
import {ref, getCurrentInstance, onMounted} from "vue";

const emit = defineEmits(['onSuccess'])

const $this = getCurrentInstance().ctx;
const form = ref({
  name: '',
  email: '',
  room_id: '',
})

const requiredFields = ['name', 'email', 'room_id'];
const validationErrors = ref({});
const addBooking = () => {
  validationErrors.value = {};
  Object.keys(form.value).forEach((key) => {
    const value = (form.value[key] ?? "").toString();
    if (value.length < 1 && requiredFields.includes(key)) {
      validationErrors.value[key] = key + ' Is Required';
    }
  })
  if (!Object.keys(validationErrors.value).length) {
    $this.$adminAjax({
      route: 'addAdminBooking',
      nonce: window.fluentReservationVars.nonce,
      method: 'post',
      data: form.value
    })
        .then(res => {
          getBookableRooms();
          emit('onSuccess', res.data)
        });
  }
}

const clearForm = () => {
  form.value = {
    name: '',
    email: '',
    room_id: '',
  };
}

defineExpose({clearForm})

onMounted(() => {
  getBookableRooms();
})


const availableRooms = ref([])

const getBookableRooms = () => {
  $this.$adminAjax({
    route: 'getAdminBookableRoom',
    nonce: window.fluentReservationVars.nonce
  })
      .then(res => {
        availableRooms.value = res.data.rooms;
      })
}


</script>

<template>

  <el-form :model="form" label-position="top">
    <el-form-item label="Name">
      <el-input v-model="form.name" autocomplete="off"/>
      <span v-if="validationErrors['name']"> {{ validationErrors['name'] }}</span>
    </el-form-item>


    <el-form-item label="Email">
      <el-input type="email" v-model="form.email" autocomplete="off"/>
      <span v-if="validationErrors['email']"> {{ validationErrors['email'] }}</span>

    </el-form-item>


    <el-form-item label="Room">
      <el-select
          v-model="form.room_id"
          class="m-2"
          placeholder="Select"
          size="large"
          style="width: 100%"
      >
        <el-option
            v-for="room of availableRooms"
            :key="room.id"
            :label="room.room_no +' -Floor: '+ room.floor_no+ ' Available: ' + (room.total_seat - room.total_bookings)"
            :value="room.id"
        />


      </el-select>
      <span v-if="validationErrors['room_id']"> {{ validationErrors['room_id'] }}</span>

    </el-form-item>


    <el-button @click="addBooking">Add Booking</el-button>
  </el-form>

</template>

<style scoped>

</style>