<template>
  <div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="my-4">
            <div class="">
              <div class="row justify-content-center" v-if="$can('sales-activities')">
                <div class="col-md-3 col-6 mb-4">

                  <router-link :to="{ name: 'pos.create' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Sales
                        </h3>
                      </div>
                    </div>
                  </router-link>
                </div>
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'reports.inventory' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Stock
                        </h3>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
              <div class="row justify-content-center" v-if="$can('sales-activities')">
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'today.sale' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Today Sales
                        </h3>
                      </div>
                    </div>
                  </router-link>
                </div>

                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'shop_balance' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Cash
                          {{ getHotelDataList.shop_name }}
                        </h3>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>

              <div class="row" v-if="$can('incharge-activities')">
                <div class="col-4 shop_list">
                  <div v-for="(data, i) in hotelItems" :key="i">
                    <div class="mb-4">
                      <div class="small-box mb-0">
                        <div class="inner text-center" @click="setShop(data)">
                            <p>{{ data.shop_name }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-6" v-if="$can('incharge-activities')">
                  <div class="table-responsive table-custom">
                    <table class="table">
                      <tbody>
                        <tr>
                          <td>QR</td>
                          <td>{{ incharge_qr | forBalanceSheetCurrencyDecimalOnly }}</td>
                        </tr>

                        <tr>
                          <td>Cash</td>
                          <td>{{ incharge_cash | forBalanceSheetCurrencyDecimalOnly }}</td>
                        </tr>
                        <tr>
                          <th>Total</th>
                          <th>{{ incharge_collection | forBalanceSheetCurrencyDecimalOnly }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
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

  data: () => ({
    shop_bank_balance : 0,
    shop_cash_balance : 0,
    shop_total_balance : 0,
    incharge_collection:0,
    incharge_cash:0,
    incharge_qr:0,
  }),
  computed: {
    ...mapGetters("operations", ["selectedHotel", "appInfo", "hotelItems"]),
  },
  async created() {
    await this.getHotelDataList();
    await this.getShopAvailableBalance();
  },
  methods: {
    setShop(hotel){
      this.$store.dispatch("operations/setHotel", { hotel });
      this.$router.push({name: 'ShopView'});
    },
    async getHotelDataList() {
      await this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
    async getShopAvailableBalance() {
      const { data } = await axios.get(
        window.location.origin + "/api/shop-balance/" + this.selectedHotel
      );

      this.shop_bank_balance = data.bank;
      this.shop_cash_balance = data.cash;
      this.shop_total_balance = data.total_balance;
      this.incharge_collection = data.total_balance_incharge;
      this.incharge_cash = data.incharge_cash;
      this.incharge_qr = data.incharge_qr;
    },
  },
  watch:{
    selectedHotel(){
      this.getShopAvailableBalance();
    }
  }
};
</script>
