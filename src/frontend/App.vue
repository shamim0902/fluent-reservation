<script setup>
import {ref, onMounted} from 'vue'

const $get = (route = {}) => {
  const options = {};
  options['route'] = route;
  options['nonce'] = window.fluentReservationVars.nonce;
  options.action = "fluent_reservation_frontend_ajax"
  return window.jQuery.get(window.fluentReservationVars.ajax_url, options);
}

onMounted(() => {
  $get('getRooms').then(data => {
    //console.log({data})
  }).then(data => {
    console.log(data)
  });
})


</script>
<template>
  <div class="fct-customer-app">

  </div>
</template>

