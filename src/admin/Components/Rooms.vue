<template>
  <div>
    <div class="fluent_reservation_admin_header">
      <el-table :data="rooms" style="width: 100%">
        <el-table-column label="Date" prop="date" />
        <el-table-column label="Name" prop="name" />
        <el-table-column align="right">
          <template #header>
            <el-input v-model="search" size="small" placeholder="Type to search" />
          </template>
          <template #default="scope">
            <el-button size="small" @click="handleEdit(scope.$index, scope.row)"
            >Edit</el-button
            >
            <el-button
                size="small"
                type="danger"
                @click="handleDelete(scope.$index, scope.row)"
            >Delete</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div>

    </div>
  </div>
</template>
<script type="module">

export default {
    name: "dashboard-page",
    components: {
    },
    data() {
      return {
        rooms: [],
        search: ''
      }
    },
  methods: {
    getRooms() {
      this.$adminGet({
        route : 'getRooms',
        nonce: window.fluentReservationVars.nonce
      })
          .then (res => {
            res.data.rooms = this.rooms
          })
    },
    handleEdit() {

    },
    handleDelete() {

    }
  },
  mounted() {
      this.getRooms();
  }
};
</script>
