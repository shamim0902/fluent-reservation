<template>
  <div>
    <div class="fluent_reservation_page_heading_wrap">
      <h1 class="fluent_reservation_page_heading--title">Events</h1>
      <div class="fluent_reservation_page_heading--actions">
        <el-button @click="openAddEventModal" type="primary">Add Event</el-button>
      </div>
    </div>


    <div class="fluentreservation_table_wrap">
      <div class="fluentreservation_table_header">
        <div class="fluentreservation_table_header_inner">
          <div class="fluentreservation_table_header_inner_left">
            <el-input v-model="search" placeholder="Search" size="large" clearable @keyup.enter="getEvents"
                      @clear="getEvents"/>
          </div>
        </div>
      </div>

      <div class="fluentreservation_table_body">
        <el-table :data="events" style="width: 100%">
          <el-table-column
              label="SL"
              width="60"
              align="center"
          >
            <template #default="scope">
              {{ scope.$index + 1 }}
            </template>
          </el-table-column>
          <el-table-column label="Title" prop="title"/>
          <el-table-column label="Start Date" prop="start_date"/>
          <el-table-column label="End Date" prop="end_date"/>
          <el-table-column label="Created At" prop="created_at"/>
          <el-table-column>
            <template #default="scope">
              <el-button size="small" @click="openEditEventModal(scope.row)">Edit</el-button>
              <el-button size="small" @click="confirmDelete(scope.row.id)">Delete</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <div class="text-right p-4 mt-2 bg-white rounded-s">
      Total Events: {{ events.length }}
    </div>


    <el-dialog v-model="showAddEventModal" title="Add Event">
      <EventAddForm @on-success="onEventAdded"/>
    </el-dialog>

    <el-dialog v-model="showEditEventModal" title="Edit Event">
      <EventEditForm :event="currentEditingEvent" @on-success="onEventUpdated"/>
    </el-dialog>
  </div>
</template>

<script setup>
import {getCurrentInstance, onMounted, ref} from "vue";
import EventAddForm from "./Forms/EventAddForm.vue";
import EventEditForm from "./Forms/EventEditForm.vue";

const $this = getCurrentInstance().ctx;
const events = ref([]);
const search = ref('');

onMounted(() => {
  getEvents();
});


const getEvents = () => {
  $this.$adminAjax({
    route: 'getEvents',
    'search': search.value,
    nonce: window.fluentReservationVars.nonce
  })
      .then(res => {
        events.value = res.data.events;
      })
}

const showAddEventModal = ref(false)
const openAddEventModal = () => {
  showAddEventModal.value = true;
}

const showEditEventModal = ref(false)
const currentEditingEvent = ref(null)

const openEditEventModal = (event) => {
  currentEditingEvent.value = event
  showEditEventModal.value = true
}

const confirmDelete = id => {
  if (confirm("Delete this event?") == true) {
    deleteEvent(id);
  }
}

const deleteEvent = id => {
  $this.$adminAjax({
    method: 'post',
    route: 'deleteEvent',
    event_id: id,
    nonce: window.fluentReservationVars.nonce
  })
      .then(res => {
        if (res.data.status) {
          getEvents();
        }
      })
}

const onEventAdded = () => {
  showAddEventModal.value = false;
  getEvents();
}

const onEventUpdated = () => {
  showEditEventModal.value = false;
  getEvents();
}

</script>
