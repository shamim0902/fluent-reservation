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

        <el-table-column label="Action">
          <template #default="scope">
            <el-button type="warning" @click="()=>{
              openEditModal(scope.row);
            }" plain>Edit
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <div style="margin-top: 20px">
        <el-button @click="openAddModal">Add Room</el-button>
        <el-dialog v-model="showAddModal" title="Add Room">
          <RoomAddForm ref="room_form" @on-success="onRoomAdded"/>
        </el-dialog>
      </div>

      <div>
        <el-dialog v-model="showEditModal" title="Edit Room">
          <RoomEditForm ref="room_edit_form" @on-success="onRoomUpdated"/>
        </el-dialog>
      </div>
    </div>
    <div>

    </div>
  </div>
</template>
<script type="module">

import RoomAddForm from "./Forms/RoomAddForm.vue";
import RoomEditForm from "./Forms/RoomEditForm.vue";
import {nextTick} from 'vue'

export default {
  name: "dashboard-page",
  components: {RoomAddForm, RoomEditForm},
  data() {
    return {
      rooms: [],
      search: '',
      showAddModal: false,
      showEditModal: false,
      currentEditableRoom: {}
    }
  },
  methods: {
    onRoomAdded(data) {
      this.rooms = data.rooms;
      this.hideAddModal();
    },
    onRoomUpdated(data) {
      this.rooms = data.rooms;
      this.hideEditModal();
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
      nextTick(() => {
        this.$refs.room_add_form.clearForm()
      })
    },

    hideAddModal() {
      this.showAddModal = false;
    },

    openEditModal(row) {
      this.showEditModal = true;
      nextTick(() => {
        this.$refs.room_edit_form.setValue({...row})
      })
    },

    hideEditModal() {
      this.showEditModal = false;
    },
    handleDelete() {

    }
  },
  mounted() {
    this.getRooms();
  }
};
</script>
