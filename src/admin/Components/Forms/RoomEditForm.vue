<script setup>
import {ref, getCurrentInstance} from "vue";

const emit = defineEmits(['onSuccess'])

const $this = getCurrentInstance().ctx;
const form = ref({
  id: '',
  room_no: '',
  floor_no: '',
  total_seat: 0,
  info: ''
})

const requiredFields = ['room_no', 'floor_no', 'total_seat'];
const validationErrors = ref({});
const updateRoom = () => {
  validationErrors.value = {};
  Object.keys(form.value).forEach((key) => {
    const value = (form.value[key]??"").toString();
    if (value.length < 1 && requiredFields.includes(key)) {
      validationErrors.value[key] = key + ' Is Required';
    }
  })
  if (!Object.keys(validationErrors.value).length) {
    $this.$adminAjax({
      route: 'updateRoom',
      nonce: window.fluentReservationVars.nonce,
      method: 'post',
      data: form.value
    })
        .then(res => {
          emit('onSuccess', res.data)
        });
  }
}

const clearForm = () => {
  form.value = {
    id: '',
    room_no: '',
    floor_no: '',
    total_seat: 0,
    info: ''
  };
}

const setValue = (value) => {
  console.log(value)
  form.value = value;
}

defineExpose({clearForm, setValue})


</script>

<template>

  <el-form :model="form" label-position="top">

    <el-form-item label="Room Number">
      <el-input v-model="form.room_no" autocomplete="off"/>
      <span v-if="validationErrors['room_no']"> {{ validationErrors['room_no'] }}</span>
    </el-form-item>

    <el-form-item label="Floor Number">
      <el-input v-model="form.floor_no" autocomplete="off"/>
      <span v-if="validationErrors['floor_no']"> {{ validationErrors['room_no'] }}</span>
    </el-form-item>

    <el-form-item label="Total Seat">
      <el-input v-model="form.total_seat" autocomplete="off"/>
      <span v-if="validationErrors['room_no']"> {{ validationErrors['room_no'] }}</span>
    </el-form-item>

    <el-form-item label="Room Info">
      <el-input
          v-model="form.info"
          :rows="2"
          type="textarea"
          placeholder="Please input"
      />
    </el-form-item>

    <el-button @click="updateRoom">Update Room
    </el-button>
  </el-form>

</template>

<style scoped>

</style>