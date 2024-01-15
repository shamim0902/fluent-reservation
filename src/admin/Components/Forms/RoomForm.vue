<script setup>
import {ref} from "vue";

const form = ref({
  room_no: '',
  floor_no: '',
  total_seat: 0,
  info: ''
})

const addRoom = () => {
  console.log(form.value)
}
</script>

<template>

  <el-form :model="form" label-position="top">
    <el-form-item label="Room Number">
      <el-input v-model="form.room_no" autocomplete="off"/>
    </el-form-item>

    <el-form-item label="Floor Number">
      <el-input v-model="form.floor_no" autocomplete="off"/>
    </el-form-item>

    <el-form-item label="Total Seat">
      <el-input v-model="form.total_seat" autocomplete="off"/>
    </el-form-item>

    <el-form-item label="Room Info">
      <el-input
          v-model="form.info"
          :rows="2"
          type="textarea"
          placeholder="Please input"
      />
    </el-form-item>

    <el-button @click="addRoom">Add Room
    </el-button>
  </el-form>

</template>

<style scoped>

</style>