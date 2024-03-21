<template>
  <div>
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
                    <div class="small-box mb-0">
                      <div class="inner">
                        <!-- Testing -->
                        <!-- <div class="icon mb-2">
                      <i class="fas fa-sign-in-alt"></i>
                    </div> -->
                        <h3>
                          <span>{{ inr }}</span> {{ dashboardSummery.cashbook }}
                        </h3>
                        <p>
                          {{ $t("dashboard.summery_items.cashbook") }}
                        </p>
                      </div>
                    </div>
                  </router-link>
                </div>

                <div>
                  <textarea v-model="billContent" placeholder="Enter bill content"></textarea>
                  <button @click="connectAndPrint">Print Bill</button>
                </div>

                <div class="col-xl-2 col-md-3 col-6">
                  <router-link :to="{ name: 'transferBalances.index' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner">
                        <h3>
                          <span>{{ inr }}</span> {{ dashboardSummery.paymentReceived }}
                        </h3>
                        <p>
                          {{ $t("dashboard.summery_items.balance_transfers") }}
                        </p>
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
    </div>
  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts';
import Form from "vform";
import axios from "axios";
import { use } from "echarts/core";
// import "echarts/lib/component/grid";
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

    billContent: '',
    printerServiceUUID: '000018f0-0000-1000-8000-00805f9b34fb',
    device: null,
    server: null,
    characteristic: null,

    day_Lables: [],
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
  }),
  computed: {

    ...mapGetters("operations", ["selectedHotel", "hotelCategoryItems", "appInfo"]),
  },
  created() {

    this.loading = true;
    let currentDate = new Date();
    currentDate.setDate(currentDate.getDate() - 6);
    this.inr = this.appInfo.currency.symbol;
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

    async connectAndPrint() {
      try {
        this.device = await navigator.bluetooth.requestDevice({
          filters: [{ services: [this.printerServiceUUID] }]
        });
        this.server = await this.device.gatt.connect();
        this.characteristic = await this.server.getPrimaryService(this.printerServiceUUID)
          .then(service => service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb'));
          let encoder = new TextEncoder("utf-8");
          // Add line feed + carriage return chars to text
          let encodedBillContent = encoder.encode(this.billContent + '\u000A\u000D');
        //const encodedBillContent = new TextEncoder().encode();
        await this.characteristic.writeValue(encodedBillContent);
        console.log('Bill sent to printer successfully.');
      } catch (error) {
        console.error('Error printing bill:', error);
      }
    }

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
