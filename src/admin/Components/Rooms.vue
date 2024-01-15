<template>
  <div>
    <div class="fluent_reservation_admin_header">

      <el-button @click="openAddModal">Add Room</el-button>

      <el-dialog v-model="showAddModal" title="Shipping address">

        <RoomForm/>
      </el-dialog>

      <el-table :data="rooms" style="width: 100%">
        <el-table-column label="Date" prop="date"/>
        <el-table-column label="Name" prop="name"/>
        <el-table-column align="right">
          <template #header>
            <el-input v-model="search" size="small" placeholder="Type to search"/>
          </template>
          <template #default="scope">
            <el-button size="small" @click="handleEdit(scope.$index, scope.row)"
            >Edit
            </el-button
            >
            <el-button
                size="small"
                type="danger"
                @click="handleDelete(scope.$index, scope.row)"
            >Delete
            </el-button
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

import RoomForm from "./Forms/RoomForm.vue";

export default {
  name: "dashboard-page",
  components: {RoomForm},
  data() {
    return {
      rooms: [],
      search: '',
      showAddModal: false
    }
  },
  methods: {

    getRooms() {
      this.$adminAjax({
        route: 'getRooms',
        nonce: window.fluentReservationVars.nonce
      })
          .then(res => {
            res.data.rooms = this.rooms
          })
    },
    openAddModal() {
      this.showAddModal = true;
    },

    hideAddModal() {
      this.showAddModal = false;
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
