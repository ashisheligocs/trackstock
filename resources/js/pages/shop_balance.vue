Shop balance page

<template>
  <div>
    <div class="container-fluid">
      <div class="row">
        <div>
           <button class="btn btn-secondary mt-2 mb-2" @click="goback"><i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}</button>
        </div>
        <div class="col-md-12">
          <div class="my-4">
            <div class="">
              <div class="row justify-content-center">
                <div class="col-md-4 col-6 mb-4">

                  <div class="small-box mb-0">
                    <div class="inner p-4 text-center">
                      <h3>
                        Shop Balance
                      </h3>
                    </div>
                  </div>

                </div>

              </div>
              <div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4">
                  <div class="small-box mb-0">
                    <div class="inner p-4 text-center">
                      <h3>
                        QR
                      </h3>
                      <span>{{ shop_bank_balance | forBalanceSheetCurrencyDecimalOnly }}</span>
                    </div>
                  </div>
                </div>

                <div class="col-md-3 col-6 mb-4">
                  <div class="small-box mb-0">
                    <div class="inner p-4 text-center">
                      <h3>
                        Cash
                      </h3>
                      <span>{{ shop_cash_balance | forBalanceSheetCurrencyDecimalOnly }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row justify-content-center" v-if="$can('incharge-activities')">
                <!-- <button class="btn btn-primary" @click="toggleVisibility">Collect Cash</button> -->
              </div>
              <div class="col-12 m-auto mw500" v-show="isVisible">
                <div class="table-responsive mt-4 ">
                  <table class="table table-bordered bg-white">
                    <tbody>
                      <tr>
                        <td class="align-middle">QR</td>
                        <td class="align-middle text-right">20,000</td>
                        <td class="align-middle text-right">20,000</td>
                      </tr>

                      <tr>
                        <td class="align-middle">Cash</td>
                        <td class="align-middle text-right">1,20,000</td>
                        <td class="align-middle text-right">
                          <div class="form-group mb-0">
                            <input type="text" class="form-control text-right">
                          </div>

                        </td>
                      </tr>
                      <tr>
                        <th class="align-middle">Total</th>
                        <th class="align-middle text-right">1,40,000</th>
                        <!-- hgfghh -->
                        <th class="align-middle text-right">Balance : 40,000</th>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="row justify-content-center">


                </div>
              </div>
              <div class="row justify-content-center">
                <router-link :to="{ name: 'transferBalances.create' }" class="small-box-footer" v-if="$can('incharge-activities')">
                      <button class="btn btn-primary" >Collect Cash</button>
                      </router-link>

              </div>
              <v-modal>
                <h4>Receipt Number</h4>
                <table>
                  <tr>
                    <td>
                      Incharge Name
                    </td>
                    <td>
                      Krishna
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Date
                    </td>
                    <td>
                      21-03-2024
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Time
                    </td>
                    <td>
                      3:00 pm
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Shop No.
                    </td>
                    <td>
                      5
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Cash Received
                    </td>
                    <td>
                      5000
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      Remarks
                    </td>

                  </tr>
                  <tr>
                    <td colspan="2">
                      <textarea>
                        Remarks
                      </textarea>
                    </td>
                  </tr>

                  <tr>
                    <td colspan="2">
                      <div>

                      </div>
                    </td>
                  </tr>
                </table>
              </v-modal>
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
  computed: {
    ...mapGetters("operations", ["selectedHotel", "appInfo"]),
  },
  data: () => ({
    isVisible: false,
    shop_bank_balance : 0,
    shop_cash_balance : 0,
    shop_total_balance : 0,
  }),
  created(){
    this.getShopAvailableBalance();
  },
  methods: {
    goback() {
      this.$router.go(-1);
    },
    toggleVisibility() {
      this.isVisible = !this.isVisible;
    },
    async getShopAvailableBalance() {
      const { data } = await axios.get(
        window.location.origin + "/api/shop-balance/" + this.selectedHotel
      );

      this.shop_bank_balance = data.bank;
      this.shop_cash_balance = data.cash;
      this.shop_total_balance = data.total_balance;

    },
  }
};
</script>
