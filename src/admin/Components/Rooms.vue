<template>
  <div>
    <div class="fluent_reservation_page_heading_wrap">
      <h1 class="fluent_reservation_page_heading--title">Rooms</h1>
      <div class="fluent_reservation_page_heading--actions">
        <el-button type="primary" @click="openAddModal">Add Room</el-button>
      </div>
    </div>

    <div class="fluentreservation_table_wrap">
      <div class="fluentreservation_table_header">
        <div class="fluentreservation_table_header_inner">
          <div class="fluentreservation_table_header_inner_left">
            <el-input v-model="search" placeholder="Search" clearable size="large" @keyup.enter="getRooms"
                      @clear="getRooms"/>
          </div>
        </div>
      </div>

      <div class="fluentreservation_table_body">
        <el-table :data="rooms" style="width: 100%">
          <el-table-column
              label="SL"
              width="60"
              align="center"
          >
            <template #default="scope">
              {{ scope.$index + 1 }}
            </template>
          </el-table-column>
          <el-table-column label="Room Number" prop="room_no"/>
          <el-table-column label="Floor" prop="floor_no"/>

          <el-table-column label="Gender">
            <template #default="scope">
              <span style="text-transform: capitalize">{{ scope.row.gender.length ? scope.row.gender : "male" }}</span>
            </template>
          </el-table-column>

          <el-table-column label="Status">
            <template #default="scope">
              <span style="text-transform: capitalize">{{ scope.row.status.length ? scope.row.status : "Open" }}</span>
            </template>
          </el-table-column>
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
              <el-button size="small" type="warning" @click="()=>{
              openEditModal(scope.row);
            }" plain>Edit
              </el-button>
              <el-button size="small" @click="confirmDelete(scope.row.id)">
                Remove
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

    </div>


    <div class="text-right p-4 mt-2 bg-white rounded-s">
      Total Seat: {{ totalSeat }}
    </div>





    <el-dialog v-model="showAddModal" title="Add Room">
      <RoomAddForm ref="room_form" @on-success="onRoomAdded"/>
    </el-dialog>

    <el-dialog v-model="showEditModal" title="Edit Room">
      <RoomEditForm ref="room_edit_form" @on-success="onRoomUpdated"/>
    </el-dialog>
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
      currentEditableRoom: {},
      confirmation_url: '',
      totalSeat: 0
    }
  },
  methods: {
    getConfirmation() {
      this.$adminAjax({
        method: 'get',
        'confirmation_url': this.confirmation_url,
        route: 'getConfirmation',
        nonce: window.fluentReservationVars.nonce
      })
          .then(res => {
            this.confirmation_url = res.data.confirmation_url;
          })
    },
    updateConfirmation() {
      this.$adminAjax({
        method: 'post',
        'confirmation_url': this.confirmation_url,
        route: 'updateConfirmation',
        nonce: window.fluentReservationVars.nonce
      })
          .then(res => {
          })
    },
    onRoomAdded(data) {
      this.rooms = data.rooms;
      this.hideAddModal();
    },
    onRoomUpdated(data) {
      this.rooms = data.rooms;
      this.hideEditModal();
    },

    confirmDelete(id) {
      if (confirm("Delete this Room? Also delete all bookigs of this room.") == true) {
        this.deleteReservation(id);
      } else {
      }
    },
    deleteReservation(id) {

      this.$adminAjax({
        method: 'post',
        'room_id': id,
        route: 'deleteRooms',
        nonce: window.fluentReservationVars.nonce
      })
          .then(res => {
            this.getRooms();
          })
    },
    getRooms() {
      this.$adminAjax({
        route: 'getRooms',
        'search': this.search,
        nonce: window.fluentReservationVars.nonce
      })
          .then(res => {

            this.totalSeat = 0;
            this.rooms = res.data.rooms;

            for (let i = 0; i < this.rooms.length; i++) {
              this.totalSeat += parseInt(this.rooms[i].total_seat);
            }
          })
    },
    openAddModal() {
      this.showAddModal = true;
      nextTick(() => {
        this.$refs.room_form.clearForm()
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
    this.getConfirmation();
  }
};
</script>
