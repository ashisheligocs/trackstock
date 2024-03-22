<template>
  <div>
    <div>
      
      <button class="btn btn-secondary mt-2 mb-2" @click="goback"><i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}</button>
    </div>
    <div class="card-body position-relative">
      <!-- <table-loading v-show="loading" /> -->
      <div class="table-responsive table-custom mt-3" id="printMe">
        <table class="table">
          <thead>
            <tr>
              <th>{{ $t("common.s_no") }}</th>
              <th>{{ $t("common.name") }}</th>
              <th>{{ $t("common.contact_number") }}</th>
              <th class="text-right no-print">
                Action
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-show="salePerson.length" v-for="(data, i) in salePerson" :key="i">
              <td>
                <span>{{ i + 1 }}</span>
              </td>
              <td>
                {{ data.name }}
              </td>
              <td>{{ data.mobile_number }}</td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-secondary mt-1 mb-1">Change Shop</button>
                </div>
              </td>
            </tr>
           
          </tbody>
        </table>
      </div>
    </div>
   
  </div>
</template>

<script>


import axios from "axios";
import { mapGetters } from "vuex";

export default {
  middleware: "auth",
  metaInfo() {
    return { title: this.$t("dashboard.page_title") };
  },
  computed: {
    ...mapGetters("operations", ["selectedHotel", "appInfo","hotelItems"]),
  },
  created(){
    this.getShopDataList();
    this.getSalesPerson();
  },
  data: () => ({
    salePerson : [],

  }),
  methods:{
    goback() {
      this.$router.go(-1);
    },
    getShopDataList(){
      this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
    async getSalesPerson(){
      const { data } = await axios.get(
        window.location.origin + "/api/shop-sales-man/" + this.selectedHotel
      );

      this.salePerson = data.data;
    },
  },
  watch:{
    selectedHotel(){
      this.getSalesPerson();
    }
  }
};
</script>
