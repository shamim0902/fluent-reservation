<script setup>
import {ref, getCurrentInstance} from "vue";

const emit = defineEmits(['onSuccess'])

const $this = getCurrentInstance().ctx;
const form = ref({
  title: '',
  description: '',
  start_date: '',
  end_date: ''
})

const requiredFields = ['title', 'start_date', 'end_date'];
const validationErrors = ref({});
const addEvent = () => {
  validationErrors.value = {};
  Object.keys(form.value).forEach((key) => {
    const value = (form.value[key] ?? "").toString();
    if (value.length < 1 && requiredFields.includes(key)) {
      validationErrors.value[key] = key + ' Is Required';
    }
  })
  if (!Object.keys(validationErrors.value).length) {
    $this.$adminAjax({
      route: 'addEvent',
      nonce: window.fluentReservationVars.nonce,
      method: 'post',
      data: form.value
    })
        .then(res => {
          clearForm();
          emit('onSuccess', res.data)
        });
  }
}

const clearForm = () => {
  form.value = {
    title: '',
    description: '',
    start_date: '',
    end_date: ''
  };
}

defineExpose({clearForm})

</script>

<template>

  <el-form :model="form" label-position="top">
    <el-form-item label="Event Title">
      <el-input v-model="form.title" autocomplete="off"/>
      <span v-if="validationErrors['title']"> {{ validationErrors['title'] }}</span>
    </el-form-item>

    <el-form-item label="Description">
      <el-input type="textarea" v-model="form.description" autocomplete="off"/>
    </el-form-item>

    <el-form-item label="Start Date">
      <el-date-picker
        v-model="form.start_date"
        type="datetime"
        placeholder="Select date and time"
        style="width: 100%"
        value-format="YYYY-MM-DD HH:mm:ss"
      />
      <span v-if="validationErrors['start_date']"> {{ validationErrors['start_date'] }}</span>
    </el-form-item>

    <el-form-item label="End Date">
      <el-date-picker
        v-model="form.end_date"
        type="datetime"
        placeholder="Select date and time"
        style="width: 100%"
        value-format="YYYY-MM-DD HH:mm:ss"
      />
      <span v-if="validationErrors['end_date']"> {{ validationErrors['end_date'] }}</span>
    </el-form-item>

    <el-button @click="addEvent" type="primary">Add Event</el-button>
  </el-form>

</template>

<style scoped>

</style>
