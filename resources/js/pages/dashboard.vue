<template>
  <div>
<<<<<<< HEAD
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->


    <div class="container-fluid">
      <!-- cashbook -->

      <div v-if="$can('account-summery') && dashboardSummery" class="row">
        <div class="col-md-12">
          <div class="my-4">
            <div class="">
              <div class="row">
                <div class="col-xl-2 col-md-3 col-6" @onload="cashbook()">
                  <router-link :to="{ name: 'invoicePayments.index' }" class="small-box-footer">
=======
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="my-4">
            <div class="">
              <div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'pos.create' }" class="small-box-footer">
>>>>>>> 84a23ec0dd14a3fb24326b601eda3953ac145c69
                    <div class="small-box mb-0">
                      <div class="inner">
                        <!-- Testing -->
                        <!-- <div class="icon mb-2">
                      <i class="fas fa-sign-in-alt"></i>
                    </div> -->
                        <h3>
                          Sales
                        </h3>
<<<<<<< HEAD
                        <!-- <p>
                          {{ $t("dashboard.summery_items.cashbook") }}
                        </p>
=======
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
              <div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'today.sale' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Today Sales
                        </h3>
>>>>>>> 84a23ec0dd14a3fb24326b601eda3953ac145c69
                      </div>
                    </div>
                  </router-link>
                </div>

                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'shop_balance' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
<<<<<<< HEAD
                        <!-- <span>{{ inr }}</span> {{ dashboardSummery.cashbook }} -->
                   Stock
                      </h3>
                        <!-- <p>
                          {{ $t("dashboard.summery_items.balance_transfers") }}
                        </p> -->
=======
                          Cash
                          {{ getHotelDataList.shop_name }}
                        </h3>
>>>>>>> 84a23ec0dd14a3fb24326b601eda3953ac145c69
                      </div>
                    </div>
                  </router-link>
                </div>
</div>
<div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'transferBalances.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                        <!-- <span>{{ inr }}</span> {{ dashboardSummery.cashbook }} -->
                   Today Sales
                      </h3>
                        <!-- <p>
                          {{ $t("dashboard.summery_items.balance_transfers") }}
                        </p> -->
                      </div>
                    </div>
                  </router-link>
                </div>

                <div class="col-xl-2 col-md-3 col-6">
                  <router-link :to="{ name: 'transferBalances.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner">
                        <h3>
                        <!-- <span>{{ inr }}</span> {{ dashboardSummery.cashbook }} -->
                   Cash
                      </h3>
                        <!-- <p>
                          {{ $t("dashboard.summery_items.balance_transfers") }}
                        </p>
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
                          <td>{{ shop_bank_balance | forBalanceSheetCurrencyDecimalOnly }}</td>
                        </tr>

                        <tr>
                          <td>Cash</td>
                          <td>{{ shop_cash_balance | forBalanceSheetCurrencyDecimalOnly }}</td>
                        </tr>
                        <tr>
                          <th>Total</th>
                          <th>{{ shop_total_balance | forBalanceSheetCurrencyDecimalOnly }}</th>
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
<<<<<<< HEAD
      <!--  -->
      <!-- Main row -->
      <div v-if="$can('account-summery') && dashboardSummery" class="row today_summry_k">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                {{ form.summeryType }} {{ $t("dashboard.summary") }}
              </h3>
              <div class="card-tools">
                <select v-model="form.summeryType" @change="getSummery($event)" class="form-control" id="summeryType"
                  name="summeryType">
                  <option value="Today" selected>
                    {{ $t("dashboard.summary_option_1") }}
                  </option>
                  <option value="Last 7 Days">
                    {{ $t("dashboard.summary_option_2") }}
                  </option>
                  <option value="This Month">
                    {{ $t("dashboard.summary_option_3") }}
                  </option>
                  <option value="This Year">
                    {{ $t("dashboard.summary_option_4") }}
                  </option>
                </select>
              </div>
            </div>
            <div class="card-body">
              <div class="row summry_cards">
                <div class="col-xl-2 col-6">
                  <router-link :to="{ name: 'purchases.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner">
                        <div class="icon mb-2">
                          <i class="fas fa-truck-loading"></i>
                        </div>
                        <h3>
                          <span>{{ inr }}</span> {{ dashboardSummery.purchaseAmount }}
                        </h3>
                        <p>{{ $t("dashboard.summery_items.purchase") }}</p>
                      </div>
                    </div>
                  </router-link>
                </div>
                <div class="col-xl-2 col-6">
                  <router-link :to="{ name: 'purchaseReturns.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner">
                        <div class="icon mb-2">
                          <i class="fas fa-forward"></i>
                        </div>
                        <h3>
                          <span>{{ inr }}</span> {{
      dashboardSummery.purchaseReturnAmount
    }}
                        </h3>
                        <p>{{ $t("dashboard.summery_items.purchase_return") }}</p>
                      </div>
                    </div>
                  </router-link>
                </div>
                <div class="col-xl-2 col-6">
                  <router-link :to="{ name: 'invoices.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner">
                        <div class="icon mb-2">
                          <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h3><span>{{ inr }}</span> {{ dashboardSummery.salesAmount }}</h3>
                        <p>{{ $t("dashboard.summery_items.sales") }}</p>
                      </div>
                    </div>
                  </router-link>
                </div>
                <div class="col-xl-2 col-6">
                  <router-link :to="{ name: 'invoiceReturns.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner">
                        <div class="icon mb-2">
                          <i class="fas fa-backward"></i>
                        </div>
                        <h3>
                          <span>{{ inr }}</span> {{ dashboardSummery.salesReturnAmount }}
                        </h3>
                        <p>{{ $t("dashboard.summery_items.sales_return") }}</p>
                      </div>
                    </div>
                  </router-link>
                </div>

                <div class="col-xl-2 col-6">
                  <router-link :to="{ name: 'purchasePayments.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner">
                        <div class="icon mb-2">
                          <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <h3><span>{{ inr }}</span> {{ dashboardSummery.paymentSent }}</h3>
                        <p>{{ $t("dashboard.summery_items.payment_sent") }}</p>
                      </div>
                    </div>
                  </router-link>
                </div>
                <div class="col-xl-2 col-6">
                  <router-link :to="{ name: 'expenses.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner">
                        <div class="icon mb-2">
                          <i class="fas fa-calculator"></i>
                        </div>
                        <h3>
                          <span>{{ inr }}</span> {{ dashboardSummery.expenseAmount }}
                        </h3>
                        <p>{{ $t("dashboard.summery_items.expense") }}</p>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
=======
>>>>>>> 84a23ec0dd14a3fb24326b601eda3953ac145c69
    </div>
  </div>
</template>

<script>
<<<<<<< HEAD
import VueApexCharts from 'vue-apexcharts';
import Form from "vform";
import axios from "axios";
import { use } from "echarts/core";
// import "echarts/lib/component/grid";
import { BarChart, LineChart, PieChart } from "echarts/charts";
import VChart, { THEME_KEY } from "vue-echarts";
import { CanvasRenderer } from "echarts/renderers";
import moment from "moment";
// import { LegendComponent, TitleComponent, TooltipComponent, } from "echarts/components";
import { mapGetters } from "vuex";
=======
>>>>>>> 84a23ec0dd14a3fb24326b601eda3953ac145c69

import axios from "axios";
import { mapGetters } from "vuex";

export default {
  middleware: "auth",
  metaInfo() {
    return { title: this.$t("dashboard.page_title") };
  },

  data: () => ({
<<<<<<< HEAD
    breadcrumbsCurrent: "dashboard.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "dashboard.breadcrumbs_active",
        url: "",
      },
    ],
    form: new Form({
      summeryType: "Today",
    }),
    year: new Date().getFullYear(),
    className: "col-lg-4",
    allData: "",
    topClients: "",
    dashboardSummery: "",
    loading: false,
    rooms: [],
    inr: "",
=======
    shop_bank_balance : 0,
    shop_cash_balance : 0,
    shop_total_balance : 0,
>>>>>>> 84a23ec0dd14a3fb24326b601eda3953ac145c69
  }),
  computed: {
    ...mapGetters("operations", ["selectedHotel", "appInfo", "hotelItems"]),
  },
<<<<<<< HEAD
  created() {
    this.loading = true;
    let currentDate = new Date();
    currentDate.setDate(currentDate.getDate() - 6);
    this.generateLinechart(currentDate, new Date())
    this.generateBarchart(currentDate, new Date())
    // this.getData();
    // this.getD();



    this.inr = this.appInfo.currency.symbol;
    this.list_all = (this.selectedHotel);
    if (this.$can("account-summery")) {
      this.getSummery();
    }
    this.loading = false;
  },
  watch: {
    selectedHotel() {
      this.loading = true;
      if (this.$can("account-summery")) {
        this.getSummery();
      }
      this.loading = false;
    }
  },
  methods: {
    async generateLinechart(startDate, endDate) {
      endDate.setDate(startDate.getDate() + 6); // Adding 7 days to the end date
      const dayLabels = [];
      const oneDay = 24 * 60 * 60 * 1000;
      for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
        const formattedDate = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        dayLabels.push(formattedDate);
      }
      if (dayLabels) {
        this.chartOptions = {
          chart: {
            type: 'area'
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: 'smooth'
          },
          xaxis: {
            type: 'category',
            categories: dayLabels
          },
          colors: ['#ff6262', '#6be96b'],
        }
      }
      this.series = [];
      axios.post(window.location.origin + `/api/getDaywiseRevenue`, { startDate: startDate, endDate: endDate }).then(response => {
        this.series.push({
          name: response.data.total_rev > 0 ? 'Total Revenue (₹' + response.data.total_rev + ')' : '',
          data: response.data.data,
          color: '#6be96b'
        });
      });
      axios.post(window.location.origin + `/api/getDaywiseExpenses`, { startDate: startDate, endDate: endDate }).then(response => {
        this.series.push({
          name: response.data.total_exp > 0 ? 'Total Expenses (₹' + response.data.total_exp + ')' : '',
          data: response.data.data,
          color: '#ff6262'
        });
      });
    },
    async generateBarchart(startDate, endDate) {
      endDate.setDate(startDate.getDate() + 6); // Adding 7 days to the end date
      const dayLabels = [];
      const oneDay = 24 * 60 * 60 * 1000;
      for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
        const formattedDate = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        dayLabels.push(formattedDate);
      }
      if (dayLabels) {
        this.chartOptions1 = {
          chart: {
            height: 350,
            type: 'line',
          },
          stroke: {
            width: [0, 4]
          },
          dataLabels: {
            enabled: true,
            enabledOnSeries: [1]
          },
          xaxis: {
            type: 'category',
            categories: dayLabels
          },
          yaxis: [{
            title: {
              text: 'Purchase',
            },

          }, {
            opposite: true,
            title: {
              text: 'Sales'
            }
          }]
        }
        this.series1 = [];
        // this.series1 =[[{
        //   name: 'Website Blog',
        //   type: 'column',
        //   data: [440, 505, 414, 671, 227, 413, 201, 352, 752, 320, 257, 160]
        // }, {
        //   name: 'Social Media',
        //   type: 'line',
        //   data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16]
        // }];

        axios.post(window.location.origin + `/api/getDaywiseSales`, { startDate: startDate, endDate: endDate }).then(response => {
          this.series1.push({
            name: response.data.total_sale > 0 ? 'Total Sales (₹' + response.data.total_sale + ')' : '',
            data: response.data.data,
            // color:'#059905',
            type: 'column',
          });
        });
        axios.post(window.location.origin + `/api/getDaywisePurchases`, { startDate: startDate, endDate: endDate }).then(response => {
          this.series1.push({
            name: response.data.total_purchase > 0 ? 'Total Purchase (₹' + response.data.total_purchase + ')' : '',
            data: response.data.data,
            // color:'#ff6262',
            type: 'line',
          });
        });
      }
    },

    // get summery
    async getSummery(event) {
      let summerType = "Today";
      if (event) {
        summerType = event.target.value;
      }
=======
  async created() {
    await this.getHotelDataList();
    await this.getShopAvailableBalance();
  },
  methods: {
    async getHotelDataList() {
      await this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
    async getShopAvailableBalance() {
>>>>>>> 84a23ec0dd14a3fb24326b601eda3953ac145c69
      const { data } = await axios.get(
        window.location.origin + "/api/shop-balance/" + this.selectedHotel
      );

      this.shop_bank_balance = data.bank;
      this.shop_cash_balance = data.cash;
      this.shop_total_balance = data.total_balance;

    },
<<<<<<< HEAD

    // async cashbook() {
    //   const { data } = await axios.get(
    //     window.location.origin + "/api/dashboard");
    //   this.dashboard = data;

    //   console.log(this.dashboard);
    //   console.log(this.dashboard);
    // },
    // get top selling products
    async getTopSellingProducts() {
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/top-selling-products"
      );
      this.pieChartOptions.legend.data = data.names;
      this.pieChartOptions.series[0].data = data.products;
    },

    // get monthly sent & received
    async getMonthlySentAndReceived() {
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/monthly-payment-sent-received"
      );
      this.lineChartOptions.xAxis.data = data.months;
      this.lineChartOptions.series[0].data = data.sent;
      this.lineChartOptions.series[1].data = data.received;
    },

    // get monthly sales & purchases
    async getMonthlySalesAndPurchases() {
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/monthly-sales-purchases"
      );
      this.barChartOptions.xAxis.data = data.months;
      this.barChartOptions.series[0].data = data.purchase;
      this.barChartOptions.series[1].data = data.sales;
    },
=======
>>>>>>> 84a23ec0dd14a3fb24326b601eda3953ac145c69
  },
  watch:{
    selectedHotel(){
      this.getShopAvailableBalance();
    }
  }
};
</script>
