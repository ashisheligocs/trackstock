<template>
  <div v-if="appInfo" class="company-logo d-flex">
    <img
      v-if="appInfo && showImage"
      :src="appInfo.blackLogo"
      :alt="appInfo.companyName"
      class="lg-logo mr-3"
      style="width: 9rem"
    />
      <div v-if="hotel && Object.keys(hotel).length" class="mb-2">
          <strong v-if="hotel.hotel_name">Name: </strong>
          {{ hotel.hotel_name }}<br />
          <strong v-if="hotel.hotel_phone">Phone: </strong>
          {{ hotel.hotel_phone }}<br />
          <strong v-if="hotel.hotel_email">Email: </strong>
          {{ hotel.hotel_email }}<br />
          <strong v-if="hotel.hotel_address">Address: </strong>
          {{ hotel.hotel_address }}<br />
          <!-- <strong v-if="appInfo.address">Address: </strong>
          {{ appInfo.address }}<br /> -->
      </div>
    <address class="mt-2" v-else>
      <strong v-html="appInfo.companyTagline" /><br />
      <strong v-if="appInfo.phone">{{ $t("common.phone") }}: </strong>
      {{ appInfo.phone }}<br />
      <strong v-if="appInfo.email">{{ $t("common.email") }}: </strong>
      {{ appInfo.email }}<br />
      <strong v-if="appInfo.address">{{ $t("common.address") }}: </strong>
      {{ appInfo.address }}<br>
      <!-- <strong v-if="appInfo.companyTagline">{{ $t("common.address") }}: </strong> -->
      <p class="tagline">{{ appInfo.companyTagline }}</p>
    </address>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "CompanyInfo",
    props: {
      hotel: [Object, String],
        showImage : {
            type: Boolean,
            default: true
        }},
  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },
};
</script>

<style scoped>
    .table th, .table td {
        padding: 0.50rem;
    }

    .break-all{
        word-break: break-all;
    }
    .tagline{
      font-size: 12px; 
      position: relative;
      top: 28px;
    }
</style>
