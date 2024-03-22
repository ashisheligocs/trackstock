<template>
  <div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="my-4">
            <div class="">
              <div class="row justify-content-center">
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
                  <router-link :to="{ name: 'transferBalances.index' }" class="small-box-footer">
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
              <div class="row justify-content-center">
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
                  <router-link :to="{ name: 'transferBalances.index' }" class="small-box-footer">
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
              <div class="row">
                <div class="col-4">
                  <div v-for="(data, i) in hotelItems" :key="i">
                    <div class="mb-4">
                      <div class="small-box mb-0">
                        <div class="inner p-4 text-center">
                          <router-link :to="{ name: 'ShopView' }">
                            <h3>{{ data.shop_name }}</h3>
                          </router-link>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="table-responsive table-custom">
                    <table class="table">
                      <tbody>
                        <tr>
                          <td>QR</td>
                          <td>20,000</td>
                        </tr>

                        <tr>
                          <td>Cash</td>
                          <td>1,20,000</td>
                        </tr>
                        <tr>
                          <th>Total</th>
                          <th>1,40,000</th>
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
// import Form from "vform";
// import axios from "axios";
import { mapGetters } from "vuex";

export default {
  middleware: "auth",
  metaInfo() {
    return { title: this.$t("dashboard.page_title") };
  },

  data: () => ({
    list_all: '',
  }),
  computed: {
    ...mapGetters("operations", ["selectedHotel", "hotelCategoryItems", "appInfo", "hotelItems"]),
  },
  async created() {
    await this.getHotelDataList();
    console.log('this', this.hotelItems);
    // console.log('this', this.getHotelDataList);
    this.list_all = (this.selectedHotel);
  },
  methods: {
    async getHotelDataList() {
      await this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
  },
};
</script>
