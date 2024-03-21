<template>
  <div>

    <div class="container-fluid">
      <!-- ALert for demo version -->
      <!-- <div class="alert alert-danger">
              <strong class="text-capitalize"
                ><i class="icon fas fa-ban"></i> Delete buttons are hidden in demo
                version.</strong
              ><br />
              <strong class="text-capitalize"
                ><i class="icon fas fa-ban"></i> Demo database will be cleared every
                two hours.</strong
              ><br />
              <strong class="text-capitalize"
                ><i class="icon fas fa-ban"></i> Email & SMS notifications are
                disabled in demo version.</strong
              >
            </div> -->

      <!-- cashbook -->
      <div v-if="$can('account-summery') && dashboardSummery" class="row">
        <div class="col-md-12">
          <div class="my-4">
            <div class="">
              <div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4" @onload="cashbook()">
                  <!-- <div class="small-box bg-olive">
                    <div class="inner"> -->
                  <!-- Testing -->
                  <!-- <h3>
                        {{ dashboardSummery.cashbook | withCurrency }}
                      </h3>
                      <p>
                        {{ $t("dashboard.summery_items.cashbook") }}
                      </p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <router-link :to="{ name: 'invoicePayments.index' }" class="small-box-footer">
                      {{ $t("common.more_info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div> -->
                  <router-link :to="{ name: 'pos.create' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                        <!-- <span>{{ inr }}</span> {{ dashboardSummery.cashbook }} -->
                        Sales
                      </h3>
                        <!-- <p>
                          {{ $t("dashboard.summery_items.cashbook") }}
                        </p> -->
                      </div>
                    </div>
                  </router-link>
                </div>

                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'transferBalances.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                        <!-- <span>{{ inr }}</span> {{ dashboardSummery.cashbook }} -->
                   Stock
                      </h3>
                        <!-- <p>
                          {{ $t("dashboard.summery_items.balance_transfers") }}
                        </p> -->
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

                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'transferBalances.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                        <!-- <span>{{ inr }}</span> {{ dashboardSummery.cashbook }} -->
                   Cash
                      </h3>
                        <!-- <p>
                          {{ $t("dashboard.summery_items.balance_transfers") }}
                        </p> -->
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--  -->
      <!-- Main row -->
      <!-- <div v-if="$can('account-summery') && dashboardSummery" class="row today_summry_k">
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
                        <span>{{ inr }}</span>  {{
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
      </div> -->
    </div>
  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts';
import Form from "vform";
import axios from "axios";
import { use } from "echarts/core";
import "echarts/lib/component/grid";
import { BarChart, LineChart, PieChart } from "echarts/charts";
import VChart, { THEME_KEY } from "vue-echarts";
import { CanvasRenderer } from "echarts/renderers";
import moment from "moment";
import { LegendComponent, TitleComponent, TooltipComponent, } from "echarts/components";
import { mapGetters } from "vuex";

use([
  CanvasRenderer,
  PieChart,
  LineChart,
  BarChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
]);

export default {
  middleware: "auth",
  metaInfo() {
    return { title: this.$t("dashboard.page_title") };
  },
  components: {
    VChart,
    apexchart: VueApexCharts
  },
  provide: {
    [THEME_KEY]: "vintage",
  },

  data: () => ({
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

    inr: "",
    // options for pie chart(Top selling products)


    // options for line chart(payment sent & receive)
    lineChartOptions: {
      responsive: true,
      tooltip: {
        trigger: "axis",
      },
      legend: {
        data: ["Payment Sent", "Payment Received"],
      },
      xAxis: {
        type: "category",
        boundaryGap: false,
        data: [],
      },
      yAxis: {
        type: "value",
      },
      series: [
        {
          name: "Payment Sent",
          type: "line",
          smooth: true,
          data: [],
        },
        {
          name: "Payment Received",
          type: "line",
          smooth: true,
          data: [],
        },
      ],
      color: ["#dc3545", "#28a745"],
    },

    // options for bar chart(purchases vs sales)
    barChartOptions: {
      responsive: true,
      tooltip: {
        trigger: "axis",
        axisPointer: {
          type: "shadow",
        },
      },
      legend: {
        data: ["Purchases", "Sales"],
      },
      grid: {
        left: "3%",
        right: "4%",
        bottom: "3%",
        containLabel: true,
      },
      xAxis: {
        type: "category",
        data: [],
      },
      yAxis: {
        type: "value",
        boundaryGap: [0, 0.01],
      },
      series: [
        {
          name: "Purchases",
          type: "bar",
          data: [],
        },
        {
          name: "Sales",
          type: "bar",
          data: [],
        },
      ],
      color: ["#007bff", "#28a745"],
    },

  }),
  computed: {

    ...mapGetters("operations", ["selectedHotel", "hotelCategoryItems", "appInfo"]),
  },
  created() {
    this.loading = true;
    let currentDate = new Date();
    currentDate.setDate(currentDate.getDate() - 6);
    this.generateLinechart(currentDate, new Date())
    this.generateBarchart(currentDate, new Date())
    // this.getData();
    // this.getD();



    this.inr = this.appInfo.currency.symbol;
    if (this.$can("account-summery")) {
      this.getSummery();
    }
    if (this.$can("top-selling-products")) {
      this.getTopSellingProducts();
    }
    if (this.$can("payment-sent-vs-payment-received")) {
      this.getMonthlySentAndReceived();
    }

    if (this.$can("sales-vs-purchases")) {
      this.getMonthlySalesAndPurchases();
    }
    this.loading = false;
  },
  watch: {
    selectedHotel() {
      this.loading = true;
      if (this.$can("account-summery")) {
        this.getSummery();
      }
      if (this.$can("top-selling-products")) {
        this.getTopSellingProducts();
      }
      if (this.$can("payment-sent-vs-payment-received")) {
        this.getMonthlySentAndReceived();
      }

      if (this.$can("sales-vs-purchases")) {
        this.getMonthlySalesAndPurchases();
      }
      // this.getData();
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
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard-summery/" + summerType
      );
      this.dashboardSummery = data;
    },

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
  },
};
</script>
<style scoped>
.chart {
  height: 400px;
}

/* tr,td {
  text-align: left;
  border: 1px solid black;
} */
</style>
