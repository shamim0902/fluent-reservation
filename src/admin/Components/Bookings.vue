<template>
  <div>
    <div class="fluent_reservation_page_heading_wrap">
      <h1 class="fluent_reservation_page_heading--title">Booking</h1>
      <div class="fluent_reservation_page_heading--actions">
        <el-button @click="openAddBookingModal" type="primary">Add Booking</el-button>
        <el-button @click="()=>{
          exportToCSV(bookings, 'Bookings', [
              'id',
              'user_id',
              'room_id',
              'info',
              'booked_seat',
              'created_at',
              'main_room_id'

          ]);
        }" type="primary">Export Booking</el-button>

      </div>
    </div>


    <div class="fluentreservation_table_wrap">
      <div class="fluentreservation_table_header">
        <div class="fluentreservation_table_header_inner">
          <div class="fluentreservation_table_header_inner_left">
            <el-input v-model="search" placeholder="Search" size="large" clearable @keyup.enter="getBookings"
                      @clear="getBookings"/>
          </div>
        </div>
      </div>

      <div class="fluentreservation_table_body">
        <el-table :data="bookings" style="width: 100%">
          <el-table-column
              label="SL"
              width="60"
              align="center"
          >
            <template #default="scope">
              {{ scope.$index + 1 }}
            </template>
          </el-table-column>
          <el-table-column label="Booked By" prop="name"/>
          <el-table-column label="Email" prop="email"/>
          <el-table-column label="Room No" prop="room_no"/>
          <el-table-column label="Floor No" prop="floor_no"/>
          <el-table-column>
            <template #default="scope">
              <el-button size="small" @click="confirmDelete(scope.row.id)">Delete</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <div class="text-right p-4 mt-2 bg-white rounded-s">
      Total Booking: {{ bookings.length }}
    </div>


    <el-dialog v-model="showAddBookingModal" title="Add Room">
      <BookingAddForm ref="room_form" @on-success="onBookingAdded"/>
    </el-dialog>
  </div>
</template>

<script setup>
import {getCurrentInstance, onMounted, ref} from "vue";
import BookingAddForm from "./Forms/BookingAddForm.vue";

const $this = getCurrentInstance().ctx;
const bookings = ref([]);
const search = ref('');
onMounted(() => {
  getBookings();
});


const getBookings = () => {
  $this.$adminAjax({
    route: 'getBookings',
    'search': search.value,
    nonce: window.fluentReservationVars.nonce
  })
      .then(res => {
        bookings.value = res.data.bookings;
      })
}

const showAddBookingModal = ref(false)
const openAddBookingModal = () => {
  showAddBookingModal.value = true;
}

const confirmDelete = id => {
  if (confirm("Delete this reservation?") == true) {
    deleteReservation(id);
  } else {
  }
}

const deleteReservation = id => {
  $this.$adminAjax({
    method: 'post',
    route: 'deleteBookings',
    booking_id: id,
    nonce: window.fluentReservationVars.nonce
  })
      .then(res => {
        if (res.data.status) {
          getBookings();
        }
      })
}

const onBookingAdded = () => {
  showAddBookingModal.value = false;
  getBookings();
}

const exportToCSV = (data, filename = 'export.csv', exclude = []) => {
  if (!Array.isArray(data) || !data.length) {
    throw new Error('No data to export');
  }

  const excludeSet = new Set(exclude);

  const headers = Object.keys(data[0]).filter(
      key => !excludeSet.has(key)
  );

  const csv = [
    headers.join(','),
    ...data.map(row =>
        headers.map(field =>
            `"${String(row[field] ?? '').replace(/"/g, '""')}"`
        ).join(',')
    )
  ].join('\n');

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  downloadBlob(blob, filename);
};


const downloadBlob = (blob, filename) => {
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);

  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();

  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}


</script>
