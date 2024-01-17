<script setup>
import {ref, getCurrentInstance} from "vue";

const emit = defineEmits(['onSuccess'])

const $this = getCurrentInstance().ctx;
const form = ref({
  id: '',
  gender: '',
  room_no: '',
  floor_no: '',
  total_seat: 0,
  info: '',
  status: 'open'
})

const requiredFields = ['room_no', 'floor_no', 'total_seat'];
const validationErrors = ref({});
const updateRoom = () => {
  validationErrors.value = {};
  Object.keys(form.value).forEach((key) => {
    const value = (form.value[key] ?? "").toString();
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
    gender: '',
    room_no: '',
    floor_no: '',
    total_seat: 0,
    info: '',
    status: 'open'
  };
}

const setValue = (value) => {
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

    <el-form-item label="Gender">
      <el-select
          v-model="form.gender"
          class="m-2"
          placeholder="Select"
          size="large"
          style="width: 100%"
      >
        <el-option
            key="male"
            label="Male"
            value="male"
        />

        <el-option
            key="female"
            label="Female"
            value="female"
        />
      </el-select>
      <span v-if="validationErrors['gender']"> {{ validationErrors['gender'] }}</span>
    </el-form-item>

    <el-form-item label="Status">
      <el-select
          v-model="form.status"
          class="m-2"
          placeholder="Select"
          size="large"
          style="width: 100%"
      >
        <el-option
            key="locked"
            label="Locked"
            value="locked"
        />

        <el-option
            key="open"
            label="Open"
            value="open"
        />
      </el-select>
      <span v-if="validationErrors['gender']"> {{ validationErrors['gender'] }}</span>
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