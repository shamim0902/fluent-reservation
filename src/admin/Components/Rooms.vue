<template>
  <div>
    <div class="fluent_reservation_admin_header">
      <el-table :data="rooms" style="width: 100%">
        <el-table-column label="Room Number" prop="room_no"/>
        <el-table-column label="Floor" prop="floor_no"/>
        <el-table-column label="Total Occupancy" prop="total_seat"/>

        <el-table-column label="Info">
          <template #default="scope">
            <el-popover effect="light" trigger="hover" placement="top" width="auto">
              <template #default>
                <div>Info: {{ scope.row.info }}</div>
              </template>
              <template #reference>
                <el-tag>Info</el-tag>
              </template>
            </el-popover>
          </template>
        </el-table-column>

      </el-table>

      <div style="margin-top: 20px">
        <el-button @click="openAddModal">Add Room</el-button>
        <el-dialog v-model="showAddModal" title="Shipping address">
          <RoomForm @on-success="onRoomAdded"/>
        </el-dialog>
      </div>
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

    onRoomAdded(data) {
      this.rooms = data.rooms;
      this.hideAddModal();
    },

    getRooms() {
      this.$adminAjax({
        route: 'getRooms',
        nonce: window.fluentReservationVars.nonce
      })
          .then(res => {
            this.rooms = res.data.rooms;
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
