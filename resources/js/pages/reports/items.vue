<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row no-print">
      <div class="col-lg-12">
        <div class="card">
          <!-- form start -->
          <form
            role="form"
            @submit.prevent="saveType"
            @keydown="form.onKeydown($event)"
          >
            <div class="card-body">
              <div class="row">
                <div v-if="items" class="form-group col-md-12">
                  <label for="productName"
                    >{{ $t("common.product_name") }}
                    <span class="required">*</span></label
                  >
                  <v-select
                    v-model="form.productName"
                    :options="items"
                    label="label"
                    :class="{ 'is-invalid': form.errors.has('productName') }"
                    name="productName"
                    :placeholder="$t('common.product_name_placeholder')"
                  />
                  <has-error :form="form" field="productName" />
                </div>
              </div>

              <div class="row">
                <div class="col-6 w-100 text-right float-right mb-2 d-flex">
                  <button type="button" class="btn btn-primary" @click="getItemData()">
                      Submit
                  </button>
                </div>
                <div class="col-6 w-100 text-right float-right mb-2 d-flex inv_filter_cont position-relative justify-content-end">
                  <span class="px-2 py-1">
                    {{ form.fromDate | moment("Do MMM, YYYY") }} TO {{ form.toDate | moment("Do MMM, YYYY") }}
                  </span>
                  <button type="button" class="modal-default-button btn btn-primary float-left" @click="filterOpen = !filterOpen">
                      {{ $t("common.filter") }}<i class="fas ml-1" :class="filterOpen ? 'fa-angle-up' : 'fa-angle-down '"/>
                  </button>
                  <div class="col-10 invent_filter" v-if="filterOpen" style="top: 39px;">
                <template :class="w - 100">
                  <date-range-picker
                    :from="form.fromDate"
                    :to="form.toDate"
                    :panel="$route.query.panel"
                    @update="update"
                  />
                </template>
              </div>
                </div>
              </div>
              
              <!-- <div class="row">
                <div class="col-7"></div>
                <div class="col-3" v-if="filterOpen">
                <template :class="w - 100">
                  <date-range-picker
                    :from="form.fromDate"
                    :to="form.toDate"
                    :panel="$route.query.panel"
                    @update="update"
                  />
                </template>
              </div>
              </div> -->
              
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      v-if="
        allData &&
        allData.stockIns &&
        allData.stockOuts &&
        (allData.stockIns.length > 0 || allData.stockIns.stockOuts > 0)
      "
      class="row"
    >
      <div class="col-lg-12">
        <div class="invoice p-3 mb-3">
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              <!-- <CompanyInfo /> -->
              <CompanyInfo :hotel="currentHotel" class="text-left" :showImage="false"/>
            </div>
            <!-- /.col -->
            <div
              v-if="
                allData.product &&
                allData.product.category &&
                allData.product.subCategory &&
                allData.product.itemUnit
              "
              class="col-sm-6 offset-sm-2 invoice-col float-right text-md-right"
            >
              <h5>
                {{ $t("common.date") }}: {{ date | moment("Do MMM, YYYY") }}
              </h5>
              <strong>{{ $t("common.code") }}:</strong>
              {{ allData.product.code | withPrefix(prfix) }}<br />
              <strong>{{ $t("common.category") }}:</strong>
              {{ allData.product.category.name }}<br />
              <strong>{{ $t("common.sub_category") }}:</strong>
              {{ allData.product.subCategory.name }}<br />
              <strong>{{ $t("products.list.common.stock") }}:</strong>
              {{ allData.product.availableQty }}
              {{ allData.product.itemUnit.code }} <br />
            </div>
          </div>
          <hr />
          <div class="row mt-5 position-relative">
            <table-loading v-show="loading" />
            <div v-if="loading == false" class="col-lg-6 table-responsive mb-5">
              <h4>
                <i>{{ $t("reports.stock_in") }}</i>
              </h4>
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.date") }}</th>
                    <th>{{ $t("reports.stock_in") }}</th>
                    <th>{{ $t("common.price") }}</th>
                    <th>{{ $t("common.type") }}</th>
                    <th>{{ $t("common.code") }}</th>
                    <th class="text-right">
                      {{ $t("common.supplier") }}/{{ $t("common.client") }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, i) in allData.stockIns" :key="i">
                    <td>{{ i + 1 }}</td>
                    <td>{{ data.date | moment("Do MMM, YYYY") }}</td>
                    <td>{{ data.quantity }}</td>
                    <td>{{ data.price | withCurrency }}</td>
                    <td>
                      <span class="badge bg-success">{{ data.type }}</span>
                    </td>
                    <td>{{ data.code }}</td>
                    <td class="text-right">
                      <span v-if="data.type === 'Purchase'">{{
                        data.supplier
                      }}</span>
                      <span v-else-if="data.type === 'Invoice Return'">{{
                        data.client
                      }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right">
                      <strong>{{ $t("reports.total_quantity") }}</strong>
                    </td>
                    <td v-if="allData.stockIns">
                      <strong>{{ stockInQty(allData.stockIns) }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="loading == false" class="col-lg-6 table-responsive">
              <h4>
                <i>{{ $t("reports.stock_out") }}</i>
              </h4>
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.date") }}</th>
                    <th>{{ $t("reports.stock_out") }}</th>
                    <th>{{ $t("common.price") }}</th>
                    <th>{{ $t("common.type") }}</th>
                    <th>{{ $t("common.code") }}</th>
                    <th class="text-right">{{ $t("common.client") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, i) in allData.stockOuts" :key="i">
                    <td>{{ i + 1 }}</td>
                    <td>{{ data.date | moment("Do MMM, YYYY") }}</td>
                    <td>-{{ data.quantity }}</td>
                    <td>{{ data.price | withCurrency }}</td>
                    <td>
                      <span class="badge bg-success">{{ data.type }}</span>
                    </td>
                    <td>{{ data.code }}</td>
                    <td class="text-right">
                      <span v-if="data.type === 'Invoice'">{{
                        data.client
                      }}</span>
                      <span v-else-if="data.type === 'Purchase Return'">{{
                        data.supplier
                      }}</span>
                      <span v-else-if="data.type === 'Order From POS'">{{
                            data.client
                        }}</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right">
                      <strong>{{ $t("reports.total_quantity") }}</strong>
                    </td>
                    <td v-if="allData.stockOuts">
                      <strong>{{ stockOutQty(allData.stockOuts) }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- this row will not appear when printing -->
          <div class="row no-print mt-5">
            <div class="col-12">
              <router-link
                :to="{ name: 'inventory.index' }"
                class="btn btn-secondary float-right"
              >
                <i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}
              </router-link>
              <a href="#" @click="printWindow" class="btn btn-default"
                ><i class="fas fa-print"></i> {{ $t("common.print") }}</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-else-if="
        allData.stockIns &&
        allData.stockOuts &&
        (allData.stockIns.length <= 0 || allData.stockIns.stockOuts <= 0)
      "
      class="row"
    >
      <div class="col-lg-12">
        <EmptyTable />
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";
import moment from "moment";
import "vue-mj-daterangepicker/dist/vue-mj-daterangepicker.css";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("reports.product.page_title") };
  },
  data: () => ({
    breadcrumbsCurrent: "reports.product.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "reports.product.breadcrumbs_first",
        url: "home",
      },
      {
        name: "reports.product.breadcrumbs_second",
        url: "",
      },
      {
        name: "reports.product.breadcrumbs_active",
        url: "",
      },
    ],
    form: new Form({
      fromDate: moment().subtract(7,'d').format('YYYY-MM-DD'),
      toDate: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss.SSS'),
      productName: "",
      shop_id: "",
    }),
    loading: false,
    allData: "",
    date: new Date(),
    prefix: "",
    currentHotel: '',
    filterOpen: false,
  }),

  computed: {
    ...mapGetters("operations", ["items", "appInfo","hotelItems","selectedHotel"]),
  },

  async created() {
    await this.getItems();
    await this.getHotelDataList();
      if (this.selectedHotel && this.selectedHotel !== 'all') {
          this.hotelItems.forEach((hotel) => {
              if (hotel.id == this.selectedHotel) this.currentHotel = hotel
          })
      } else {
        this.currentHotel = '';
      }
    this.prfix = this.appInfo.productPrefix;
  },
  methods: {
    // get all categories
    async getItems() {
      await this.$store.dispatch("operations/allData", {
        path: "/api/all-products",
      });
    },

    async getHotelDataList () {
        await this.$store.dispatch('operations/getHotelData', {
            path: '/api/shop',
        });
    },


    // get filtered data
    async update(values) {
      
      this.form.fromDate = values.from;
      this.form.toDate = values.to;
      this.getItemData();
    },

    // get Item data

    async getItemData(){
      this.loading = true;
      this.form.shop_id = (this.selectedHotel != 'all') ? this.selectedHotel : '',
      await this.form
        .post(window.location.origin + "/api/reports/items")
        .then((response) => {
          this.allData = response.data;
          this.loading = false;
          this.filterOpen = false;
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("common.delete_failed") });
        });
    },

    // count stock in qty
    stockInQty(stockIns) {
      let total = stockIns.reduce(
        (accumulator, current) =>
          Number(accumulator) + Number(current.quantity),
        0
      );
      return total;
    },

    // count stock out qty
    stockOutQty(stockOuts) {
      let total = stockOuts.reduce(
        (accumulator, current) =>
          Number(accumulator) + Number(current.quantity),
        0
      );
      return total;
    },

    // print
    printWindow() {
      window.print();
    },
  },
  watch:{
    selectedHotel(newData){
      
      if(this.form.productName !== ''){
        this.getItemData();
      }
      if (newData && newData !== 'all') {
          this.hotelItems.forEach((hotel) => {
              if (hotel.id == newData) this.currentHotel = hotel
          })
      } else {
        this.currentHotel = '';
      }
    }
  }
};
</script>

