<template>
  <div class="mb-50">
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="row">
      <div class="col-lg-12">
        <div class="card custom-card w-100">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("cashbook.transactions.index.page_title") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                  <i class="fas fa-sync"></i>
                </a>
                <a href="/cashbook/transactions/excel" v-tooltip="$t('common.export_table')" class="btn btn-dark">
                  <i class="fas fa-download"></i>
                </a>
                <a @click="print" v-tooltip="$t('common.print_table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
              </div>
            </div>
          </div>
          <!-- /.card-header -->

          <div class="card-body position-relative mt-4">
            <div id="tabs">
              <div class="tabs">
                <a v-on:click="activetab = 1" v-bind:class="[activetab === 1 ? 'active' : '']">Transaction New</a>
                <a v-on:click="activetab = 2" v-bind:class="[activetab === 2 ? 'active' : '']">Transaction</a>
              </div>
              <div v-if="activetab === 1" class="tabcontent">
                <table-loading v-show="loading" />

                <div id="printMe" class="table-responsive table-custom mt-3">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-6 col-xl-4 mb-2">
                        <!-- <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" /> -->
                      </div>
                      <div class="col-6 col-xl-8 mb-2 text-right">
                        <date-range-picker ref="pickernew" opens="left" :locale-data="locale" :minDate="minDate"
                          :maxDate="maxDate" :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true"
                          :autoApply="true" v-model="newdateRange" @update="updatenewValues" :linkedCalendars="true"
                          class="c-w-100">
                          <template v-slot:input="pickernew" style="min-width: 350px">
                            {{ pickernew.startDate | startDate }} -
                            {{ pickernew.endDate | endDate }}
                          </template>
                        </date-range-picker>
                      </div>
                    </div>
                    <table class="table ">
                      <thead>
                        <tr>
                          <th>{{ $t("common.date") }}</th>
                          <th>{{ $t("sidebar.shops") }}</th>
                          <th>{{ $t("common.description") }}</th>
                          <th class="text-right">{{ $t("common.debit") }}</th>
                          <th class="text-right">{{ $t("common.credit") }}</th>
                          <th class="text-right">{{ $t("common.created_by") }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <template v-show="newtransactiondata.length" v-for="(plutus, i) in newtransactiondata">
                          <tr v-for="(entry, index) in plutus.items" :key="index + entry.date">
                            <td>{{ entry.date | moment("DD-MM-YYYY") }}</td>
                            <td>{{ entry?.shop }}</td>
                            <td class="pl-4">
                              <router-link v-if="$can('client-view')" :to="{
                                name: 'accounts.show',
                                params: { slug: entry?.slug },
                              }">
                                {{ entry?.ledger_name }}
                              </router-link>
                              - {{ entry?.ledger_category }} ( {{ entry?.ledger_type }} )
                            </td>

                            <td class="text-right">{{ (entry?.debit) | forBalanceSheetCurrencyDecimalOnly }}</td>
                            <td class="text-right">{{ (entry?.credit) | forBalanceSheetCurrencyDecimalOnly }}</td>
                            <td v-if="entry?.user_name" class="text-right">
                              {{ entry?.user_name }}
                            </td>
                          </tr>
                          <tr class="tablerow-background" :key="i">
                            <td colspan="1"></td>
                            <td></td>
                            <td colspan="6" ><strong class="bold-500">{{ plutus?.note }}</strong></td>
                          </tr>

                        </template>
                        <tr v-show="!newtransactiondata.length">
                          <td colspan="6">
                            <EmptyTable />
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <small class="ml-2">
                      <i>
                        All prices are in {{ '' | defaultwithCurrency }}
                      </i>
                    </small>
                  </div>
                </div>
                <div class="card-footer" v-if="newtransactiondata.length">
                  <div class="dtable-footer">
                    <div class="form-group row display-per-page">
                      <label>{{ $t("per_page") }} </label>
                      <div>
                        <select @change="tupdatePerPager" v-model="tperPage" class="form-control form-control-sm ml-1">
                          <option value="5">5</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                          <option value="">ALL</option>
                        </select>
                      </div>
                    </div>
                    
                    <!-- pagination-start -->
                    <pagination v-if="pagination1 && pagination1.last_page > 1" :pagination="pagination1" :offset="5"
                      class="justify-flex-end" @paginate="tpaginate" />
                    <!-- pagination-end -->
                  </div>
                </div>
              </div>
              <div v-if="activetab === 2" class="tabcontent">
                <table-loading v-show="loading" />
                <div id="printMe" class="table-responsive table-custom mt-3">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-6 col-xl-4 mb-2">
                        <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
                      </div>
                      <div class="col-6 col-xl-8 mb-2 text-right">
                        <date-range-picker ref="picker" opens="left" :locale-data="locale" :minDate="minDate"
                          :maxDate="maxDate" :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true"
                          :autoApply="true" v-model="dateRange" @update="updateValues" :linkedCalendars="true"
                          class="c-w-100">
                          <template v-slot:input="picker" style="min-width: 350px">
                            {{ picker.startDate | startDate }} -
                            {{ picker.endDate | endDate }}
                          </template>
                        </date-range-picker>
                      </div>
                    </div>
                    <div class="table-responsive table-custom mt-3" id="printMe">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>{{ $t("common.s_no") }}</th>
                            <th>{{ $t("common.date") }}</th>
                            <th>{{ $t("sidebar.shops") }}</th>
                            <th>{{ $t("common.account") }}</th>
                            <th>{{ $t("common.reason") }}</th>
                            <th>{{ $t("common.type") }}</th>
                            <th class="text-right">{{ $t("common.debit") }}</th>
                            <th class="text-right">{{ $t("common.credit") }}</th>
                            <!--                    <th>{{ $t("common.status") }}</th>-->
                            <th class="text-right">{{ $t("common.created_by") }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                            <td>
                              <span v-if="pagination.current_page > 1">
                                {{
                                  pagination.per_page * (pagination.current_page - 1) +
                                  (i + 1)
                                }}
                              </span>
                              <span v-else>{{ i + 1 }}</span>
                            </td>
                            <td>
                              <span v-if="data.transactionDate">{{
                                data.transactionDate | moment("Do MMM, YYYY")
                              }}</span>
                            </td>
                            <td>
                              <span v-if="data?.shop?.shop_name">{{
                                data?.shop?.shop_name
                              }}</span>
                            </td>
                            <td v-if="data.account">{{ data.account.ledgerName }}</td>
                            <td>{{ data.reason }}</td>
                            <td>
                              <span v-if="data.type === 1" class="badge "
                                :class="data.isAsset || data.isExpense ? 'bg-danger' : 'bg-success'">{{
                                  data.isAsset || data.isExpense ? $t("common.debit") : $t("common.credit")
                                }}</span>
                              <span v-else class="badge bg-danger"
                                :class="data.isAsset || data.isExpense ? 'bg-success' : 'bg-danger'">{{
                                  data.isAsset || data.isExpense ? $t("common.credit") : $t("common.debit")
                                }}</span>
                            </td>
                            <td class="text-right" v-if="data.isAsset || data.isExpense">
                              <span v-if="data.type === 1">{{ data.amount | twoPoints }}</span>
                              <span v-else>{{ 0 }}</span>
                            </td>
                            <td class="text-right" v-else>
                              <span v-if="data.type === 1">{{ 0 }}</span>
                              <span v-else>{{ data.account?.slug && data.account?.slug === 'GST-INPUT' ?
                                -Math.abs(data.amount) : data.amount | twoPoints }}</span>
                            </td>
                            <td class="text-right" v-if="data.isAsset || data.isExpense">
                              <span v-if="data.type === 1">{{ 0 }}</span>
                              <span v-else>{{ data.amount | twoPoints }}</span>
                            </td>
                            <td class="text-right" v-else>
                              <span v-if="data.type === 1">{{
                                data.amount | twoPoints
                              }}</span>
                              <span v-else>{{ 0 }}</span>
                            </td>
                            <td v-if="data.user" class="text-right">
                              {{ data.user.name }}
                            </td>
                          </tr>
                          <tr v-show="!loading && !items.length">
                            <td colspan="8">
                              <EmptyTable />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <small class="ml-2">
                    <i>
                      All prices are in {{ '' | defaultwithCurrency }}
                    </i>
                  </small>
                  <div class="card-footer">
                    <div class="dtable-footer">
                      <div class="form-group row display-per-page">
                        <label>{{ $t("per_page") }} </label>
                        <div>
                          <select @change="updatePerPager" v-model="perPage" class="form-control form-control-sm ml-1">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                          </select>
                        </div>
                      </div>
                      <!-- pagination-start -->
                      <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
                        class="justify-flex-end" @paginate="paginate" />
                      <!-- pagination-end -->
                    </div>
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
import moment from "moment";
import axios from "axios";
import { mapGetters } from "vuex";
import i18n from "~/plugins/i18n";
import DateRangePicker from "vue2-daterange-picker";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("cashbook.transactions.index.page_title") };
  },
  components: {
    DateRangePicker,
  },
  data: () => ({
    breadcrumbsCurrent: "cashbook.transactions.index.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "cashbook.transactions.index.breadcrumbs_first",
        url: "home",
      },
      {
        name: "cashbook.transactions.index.breadcrumbs_second",
        url: "",
      },
      {
        name: "cashbook.transactions.index.breadcrumbs_active",
        url: "",
      },
    ],
    query: "",
    perPage: 10,
    tperPage: 5,
    activetab: 1,
    newtransactiondata: [],
    minDate: moment(new Date("01-01-2021")).format("YYYY-MM-DD"),
    maxDate: moment().add(1, "days").format("YYYY-MM-DD"),
    dateRange: {
      startDate: moment().subtract(7, 'd').format('YYYY-MM-DD'),
      endDate: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss.SSS'),
    },
    newdateRange: {
      startDate: moment().subtract(7, 'd').format('YYYY-MM-DD'),
      endDate: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss.SSS'),
    },
    locale: {
      direction: "ltr",
      format: "YYYY-MM-DD",
      separator: " - ",
      applyLabel: "Apply",
      cancelLabel: "Cancel",
      weekLabel: "W",
      customRangeLabel: "Custom Range",
      daysOfWeek: moment.weekdaysMin(),
      monthNames: moment.monthsShort(),
      firstDay: 1,
    },
    pagination1: [],
  }),
  filters: {
    startDate(val) {
      return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.from");
    },
    endDate(val) {
      return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.to");
    },
  },
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination", "selectedHotel"]),
  },
  watch: {
    // watch search data
    async activetab(currentValue, oldValue) {
      if (currentValue == 1) {
        //Get Transaction Data without Grouping Like Old One
        // console.log('old');
      } else if (currentValue == 2) {
        //Get Transaction Data with Grouping
        // console.log('new');
      }
    },
    query: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchData();
        } else {
          this.getData();
        }
      } else {
        this.searchData();
      }
    },
    selectedHotel: function () {
      this.getData();
      this.getNewData();
    },
  },
  created() {
    this.getData();
    this.getNewData();
  },
  methods: {
    // filter data for selected date range
    async updateValues() {
      this.dateRange.startDate = moment(this.dateRange.startDate).format(
        "YYYY-MM-DD"
      );
      this.dateRange.endDate = moment(this.dateRange.endDate).format(
        "YYYY-MM-DD HH:mm:ss.SSS"
      );
      // this.searchData();
      this.getData();
    },

    async updatenewValues() {
      this.newdateRange.startDate = moment(this.newdateRange.startDate).format(
        "YYYY-MM-DD"
      );
      this.newdateRange.endDate = moment(this.newdateRange.endDate).format(
        "YYYY-MM-DD HH:mm:ss.SSS"
      );
      this.getNewData();
    },
    // refresh table
    refreshTable() {
      this.query = "";
      this.dateRange.startDate = null;
      this.dateRange.endDate = null;

      this.query === "" ? this.getData() : this.searchData();

      setTimeout(
        function () {
          this.dateRange.startDate = "";
          this.dateRange.endDate = "";
          this.newdateRange.startDate = "";
          this.newdateRange.endDate = "";
        }.bind(this),
        500
      );
    },
    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
    },
    tupdatePerPager() {
      if (this.perPage === "") {
        this.getDataAll();
      } else {
        this.pagination1.current_page = 1;
        this.query === "" ? this.getNewData() : "";
      }
    },
    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchDataWithDate", {
        path: "/api/transactions?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
        startDate: "&startDate=" + this.dateRange.startDate,
        endDate: "&endDate=" + this.dateRange.endDate,
      });
    },

    // get new data

    async getNewData() {
    this.$store.state.operations.loading = true;
    let currentPage = this.pagination1 ? this.pagination1.current_page : 1;
    let perPageParam = this.tperPage === "" ? "all" : this.tperPage;

    try {
        const response = await axios.get(window.location.origin + "/api/newtransactions", {
            params: {
                page: currentPage,
                perPage: perPageParam,
                startDate: this.newdateRange.startDate,
                endDate: this.newdateRange.endDate
            }
        });

        this.newtransactiondata = response.data.data;
        console.log(this.newtransactiondata);
        this.pagination1 = response.data;
    } catch (error) {

        console.error('Error fetching data:');
        toast.fire({ type: "error", title: this.$t("common.delete_failed") });
    } finally {
        this.$store.state.operations.loading = false;
    }
},

    // Pagination
    async paginate(page) {
      this.pagination.current_page = page
      this.query === "" ? this.getData() : this.searchData();
    },

    async tpaginate(page) {

      this.pagination1.current_page = page
      this.query === "" ? this.getNewData() : "";
      // this.query === "" ? this.getNewData() : "";
    },
    // Reset pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    // search data
    async searchData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/searchData", {
        path: "/api/transactions/search",
        term: this.query,
        currentPage: currentPage + "&perPage=" + this.perPage,
        startDate: this.dateRange.startDate,
        endDate: this.dateRange.endDate,
      });
    },

    // Reload after search
    async reload() {
      this.query = "";
      await this.searchData();
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },
  },
};
</script>
<style scoped>
.tabs {
  overflow: hidden;
  margin-bottom: -2px;
  padding-left: 24px;
  border-bottom: 1px solid #ccc;
}

.tabs a {
  float: left;
  cursor: pointer;
  padding: 10px 20px;
  transition: background-color 0.2s;
  border-radius: 4px 4px 0 0;
  font-weight: bold;
  border: 1px solid transparent;
  color: #000000;
}

.tabs a:hover {
  background-color: #dee8f1;
  color: #000000;
}

.tabs a.active {
  color: #6366f1;
  cursor: default;
  border: 1px solid #ccc;
  border-bottom: 0px;
}

.bold-500{
  font-weight: 500;
}
</style>