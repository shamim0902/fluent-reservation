<script setup>
import {ref, getCurrentInstance, onMounted, watch} from "vue";

const props = defineProps(['booking', 'showEditBookingModal']);
const emit = defineEmits(['onSuccess', 'onClose'])

const $this = getCurrentInstance().ctx;
const form = ref({
  id: '',
  name: '',
  email: '',
  room_id: '',
})

const showModal = ref(props.showEditBookingModal);

const requiredFields = ['name', 'email', 'room_id'];
const validationErrors = ref({});
const updateBooking = () => {
  validationErrors.value = {};
  Object.keys(form.value).forEach((key) => {
    const value = (form.value[key] ?? "").toString();
    if (value.length < 1 && requiredFields.includes(key)) {
      validationErrors.value[key] = key + ' Is Required';
    }
  })
  if (!Object.keys(validationErrors.value).length) {
    $this.$adminAjax({
      route: 'updateAdminBooking',
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

const setForm = () => {
  if (props.booking) {
    form.value = {
      id: props.booking.id,
      name: props.booking.name,
      email: props.booking.email,
      room_id: props.booking.room_id.toString(),
    };
  }
}

onMounted(() => {
  setForm();
  getBookableRooms();
})

watch(() => props.booking, () => {
  setForm();
}, {deep: true});


const availableRooms = ref([])

const getBookableRooms = () => {
  $this.$adminAjax({
    route: 'getAdminBookableRoom',
    nonce: window.fluentReservationVars.nonce,
    room_id: form.value.room_id
  })
      .then(res => {
        availableRooms.value = res.data.rooms;
      })
}

//add watch for showEditBookingModal
watch(() => props.showEditBookingModal, () => {
  showModal.value = props.showEditBookingModal;
})


</script>

<template>
  <el-dialog v-model="showModal" title="Edit Booking" @open="getBookableRooms" @close="emit('onClose')">
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


      <el-button @click="updateBooking" type="primary">Update Booking</el-button>
    </el-form>
  </el-dialog>

</template>

<style scoped>

</style>
