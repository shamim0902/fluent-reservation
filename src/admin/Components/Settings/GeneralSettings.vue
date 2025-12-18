<script type="module">

import { ElNotification } from 'element-plus';

  export default {
    data() {
      return {
        confirmation_url: '',
        updating: false
      }
    },
    methods: {
      getConfirmation(){
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
        this.updating = true;
        this.$adminAjax({
          method: 'post',
          'confirmation_url': this.confirmation_url,
          route: 'updateConfirmation',
          nonce: window.fluentReservationVars.nonce
        })
            .then(res => {
              this.updating = false;
              ElNotification({
                type: 'success',
                message: 'Confirmation URL updated!',
                duration: 0,
                offset: 40
              })
            })
      }
    },
    mounted() {
      this.getConfirmation();
    }
  }
</script>

<template>
  <div class="fluentreservation-settings-body">
    <div class="fluentreservation-settings-body-inner">
      <div class="fluentreservation-settings-body--header">
        <div class="fluentreservation-settings-body--header-left">
          <div class="fluentreservation-settings-body--header-title">
            General
          </div>
        </div>

        <div class="fluentreservation-settings-body--header-action">
          <el-button type="primary" @click="updateConfirmation" :loading="updating">Save</el-button>
        </div>
      </div>

      <div class="fluentreservation-settings-body--content">
        <el-form label-position="top">
          <el-form-item label="Redirect after booking success">
            <el-input v-model="confirmation_url" placeholder="https://" />
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
