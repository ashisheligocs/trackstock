<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row no-print">
      <div class="col-lg-12">
        <div class="card">
          <!-- form start -->
          <form role="form">
            <div class="card-header">
              <div class="row justify-content-end">
                <div class="col-5 w-100 text-right float-right mb-2 d-flex justify-content-end inv_filter_cont position-relative">
                  <span class="px-2 py-1">
                    {{ form.fromDate | moment("Do MMM, YYYY") }} TO {{ form.toDate | moment("Do MMM, YYYY") }}
                  </span>
                  <button type="button" class="modal-default-button btn btn-primary float-left" @click="filterOpen = !filterOpen">
                      {{ $t("common.filter") }}<i class="fas ml-1" :class="filterOpen ? 'fa-angle-up' : 'fa-angle-down '"/>
                  </button>
                  <div class="col-12 invent_filter" v-if="filterOpen" style="top: 40px;">
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
            <div class="card-body">
              <div class="row">
                <div v-if="items" class="form-group col-md-6">
                  <label for="category"
                    >{{ $t("common.category_name") }}
                    <span class="required">*</span></label
                  >
                  <v-select
                    v-model="form.category"
                    :options="items"
                    label="name"
                    :class="{ 'is-invalid': form.errors.has('category') }"
                    name="category"
                    :placeholder="$t('common.category_name_placeholder')"
                    @input="getSubCategories"
                  />
                  <has-error :form="form" field="category" />
                </div>
                <div v-if="items" class="form-group col-md-6">
                  <label for="subCategory"
                    >{{ $t("common.sub_category_name")
                    }}<span class="required">*</span></label
                  >
                  <v-select
                    :disabled="form.category.id == 0"
                    v-model="form.subCategory"
                    :options="subCategories"
                    label="name"
                    :class="{ 'is-invalid': form.errors.has('subCategory') }"
                    name="subCategory"
                    :placeholder="$t('common.category_name_placeholder')"
                  />
                  <has-error :form="form" field="subCategory" />
                </div>
              </div>
              <div class="row">
                <div class="col-8 w-100 text-right float-right mb-2 d-flex">
                  <button type="button" class="btn btn-primary" @click="getExpenseData()">
                      Submit
                  </button>
                </div>
                <!-- <div class="col-4 w-100 text-right float-right mb-2 d-flex">
                  <span class="px-2 py-1">
                    {{ form.fromDate | moment("Do MMM, YYYY") }} TO {{ form.toDate | moment("Do MMM, YYYY") }}
                  </span>
                  <button type="button" class="modal-default-button btn btn-primary float-left" @click="filterOpen = !filterOpen">
                      {{ $t("common.filter") }}<i class="fas ml-1" :class="filterOpen ? 'fa-angle-up' : 'fa-angle-down '"/>
                  </button>
                </div> -->
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
    <div v-if="expenses && expenses.length > 0" class="row">
      <div class="col-lg-12">
        <div class="invoice p-3 mb-3">
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              <!-- <CompanyInfo /> -->
              <CompanyInfo :hotel="currentHotel" class="text-left" :showImage="false"/>
            </div>
            <div
              class="col-sm-6 offset-sm-2 invoice-col float-right text-md-right"
            >
              <h5>{{ $t("reports.expense.page_title") }}</h5>
              <strong>{{ $t("common.date") }}:</strong>
              {{ date | moment("Do MMM, YYYY") }}<br />
              <strong>{{ $t("common.category") }}:</strong>
              {{ form.category.name }}<br />
              <span v-if="form.subCategory"
                ><strong>{{ $t("common.sub_category") }}:</strong>
                {{ form.subCategory.name }}<br
              /></span>
              <strong>{{ $t("reports.date_range") }}:</strong>
              {{ form.fromDate | moment("Do MMM, YYYY") }} -
              {{ form.toDate | moment("Do MMM, YYYY") }} <br />
            </div>
          </div>
          <hr />
          <div class="row mt-5 position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.date") }}</th>
                    <th>{{ $t("expenses.list.common.expense_reason") }}</th>
                    <th>{{ $t("common.category") }}</th>
                    <th>{{ $t("common.sub_category") }}</th>
                    <th>{{ $t("common.amount") }}</th>
                    <th>{{ $t("common.account") }}</th>
                    <th>{{ $t("common.status") }}</th>
                    <th class="text-right">{{ $t("common.created_by") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, i) in expenses" :key="i">
                    <td>{{ ++i }}</td>
                    <td>
                      <span v-if="data.date">{{
                        data.date | moment("Do MMM, YYYY")
                      }}</span>
                    </td>
                    <td>{{ data.reason }}</td>
                    <td>
                      <span v-if="data.category">
                        {{ data.category.code | withPrefix(categoryPrefix) }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.subCategory">
                        {{
                          data.subCategory.code | withPrefix(subCategoryPrefix)
                        }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.transaction">{{
                         data.transaction.amount | withCurrency
                      }}</span>
                    </td>
                    <td>
                      <span v-if="data.account">
                        {{ data.account.accountNumber }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.status === 1" class="badge bg-success">{{
                        $t("common.active")
                      }}</span>
                      <span v-else class="badge bg-danger">{{
                        $t("common.in_active")
                      }}</span>
                    </td>
                    <td class="text-right">{{ data.createdBy }}</td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-right">
                      <strong>{{ $t("common.total") }}</strong>
                    </td>
                    <td colspan="4">
                      <strong>{{
                        calculateTotal(expenses) | withCurrency
                      }}</strong>
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

    <div v-else-if="expenses && expenses.length <= 0" class="row">
      <div class="col-lg-12 col-xl-10 offset-xl-1">
        <EmptyTable />
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import moment from "moment";
import { mapGetters } from "vuex";
import "vue-mj-daterangepicker/dist/vue-mj-daterangepicker.css";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("reports.expense.page_title") };
  },
  data: () => ({
    breadcrumbsCurrent: "reports.expense.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "reports.expense.breadcrumbs_first",
        url: "home",
      },
      {
        name: "reports.expense.breadcrumbs_second",
        url: "",
      },
      {
        name: "reports.expense.breadcrumbs_active",
        url: "",
      },
    ],
    form: new Form({
      // fromDate: String(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)),
      // toDate: String(new Date()),
      fromDate: moment().subtract(7,'d').format('YYYY-MM-DD'),
      toDate: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss.SSS'),
      category: "",
      subCategory: "",
    }),
    subCategories: [],
    loading: false,
    expenses: "",
    date: new Date(),
    categoryPrefix: "",
    subCategoryPrefix: "",
    currentHotel: '',
    filterOpen: false,
  }),

  computed: {
    ...mapGetters("operations", ["items", "appInfo","hotelItems","selectedHotel"]),
  },

  async created() {
    await this.getCatgories();
    await this.getHotelDataList();
      if (this.selectedHotel && this.selectedHotel !== 'all') {
          this.hotelItems.forEach((hotel) => {
              if (hotel.id == this.selectedHotel) this.currentHotel = hotel
          })
      } else {
        this.currentHotel = '';
      }
    this.categoryPrefix = this.appInfo.expCatPrefix;
    this.subCategoryPrefix = this.appInfo.expSubCatPrefix;
  },
  methods: {

    async getHotelDataList () {
        await this.$store.dispatch('operations/getHotelData', {
            path: '/api/hotel',
        });
    },

    // get all categories
    async getCatgories() {
      await this.$store.dispatch("operations/allData", {
        path: "/api/all-expense-categories",
      });
      this.items.unshift({
        id: 0,
        name: "All Categories",
      });
    },

    // get sub categories for a category
    async getSubCategories() {
      this.subCategories = [];
      this.form.subCategory = "";
      if (this.form.category.id != 0) {
        let slug = this.form.category.slug;
        const { data } = await axios.get(
          window.location.origin + "/api/sub-categories-by-category/" + slug
        );
        this.subCategories = data.data;
        if (this.subCategories.length > 0) {
          this.subCategories.unshift({
            id: 0,
            name: "All Sub Categories",
          });
        }
      }
    },

    // get filtered data
    async update(values) {
     
        this.form.fromDate = values.from;
        this.form.toDate = values.to;
        this.getExpenseData();
    },

    async getExpenseData(){
      this.loading = true;
      await this.form
        .post(window.location.origin + "/api/reports/expenses")
        .then((response) => {
          this.expenses = response.data.data;
          this.loading = false;
          this.filterOpen = false;
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("common.delete_failed") });
        });
    },

    // calculate total
    calculateTotal(expenses) {
      let total = expenses.reduce(
        (accumulator, current) =>
          Number(accumulator) + Number(current.transaction.amount),
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
      if(this.form.category !== ''){
        this.getExpenseData();
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

