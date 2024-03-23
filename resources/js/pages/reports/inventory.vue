<template>
  <div>
    <div>
      <button class="btn btn-secondary mt-2 mb-2" @click="goBack"><i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}</button>
    </div>
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

                <div class="col-md-6 d-flex align-items-center inv_filter_cont position-relative">
                  <div class="col w-100  float-right d-flex ">

                  <button type="button" class="modal-default-button btn btn-primary float-left" @click="filterOpen = !filterOpen">
                      {{ $t("common.filter") }}<i class="fas ml-1" :class="filterOpen ? 'fa-angle-up' : 'fa-angle-down '"/>
                  </button>
                   <span class="px-2 py-1">
                    {{ form.fromDate | moment("Do MMM, YYYY") }} TO {{ form.toDate | moment("Do MMM, YYYY") }}
                  </span>
                </div>
                <div class="col-10 invent_filter" v-if="filterOpen">
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
                <div class="col-md-6"><h5 class="text-right">{{ $t("reports.inventory.page_title") }}</h5></div>

              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div v-if="inventoryData && inventoryItems(inventoryData) > 0" class="row">
      <div class="col-lg-12">
        <div class="invoice p-3 mb-3">
          <div class="row invoice-info d-none">
            <div class="col-sm-4 invoice-col">
              <!-- <CompanyInfo /> -->
              <CompanyInfo :hotel="currentHotel" class="text-left" :showImage="false"/>
            </div>
            <div class="col-sm-6 offset-sm-2 invoice-col float-right text-md-right">

              <!-- <br /> -->
              <!-- <span
                ><strong>{{ $t("common.date") }}:</strong>
                {{ date | moment("Do MMM, YYYY") }}</span
              ><br />
              <span v-if="form.itemName"
                ><strong>{{ $t("common.product_name") }}:</strong>
                {{ form.itemName.name }}<br
              /></span>
              <strong>{{ $t("common.category_name") }}:</strong>
              {{ form.category.name }}<br />
              <span v-if="form.subCategory"
                ><strong>{{ $t("common.sub_category_name") }}:</strong>
                {{ form.subCategory.name }}<br
              /></span>
              <strong>{{ $t("reports.date_range") }}:</strong>
              {{ form.fromDate | moment("Do MMM, YYYY") }} -
              {{ form.toDate | moment("Do MMM, YYYY") }} <br /> -->
            </div>
          </div>

          <div class="row position-relative">
            <table-loading v-show="loading" />
            <div v-if="loading == false" class="table-responsive table-custom">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>Batch No.</th>
                    <th>{{ $t("common.shop") }}</th>
                    <th>{{ $t("common.name") }}</th>
                    <th>{{ $t("reports.stock_in") }}</th>
                    <th>{{ $t("reports.stock_out") }}</th>
                    <th>{{ $t("reports.stock_in_hand") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, i) in inventoryData" :key="i">
                    <td>{{ ++i }}</td>
                    <td>
                      {{ data.productCode }}
</td>
                    <td>{{ data.hotel_name }}</td>
                    <td>{{ data.productName }}</td>
                    <td>{{ data.stockIn }}</td>
                    <td>{{ data.stockOut }}</td>
                    <td>{{ data.availableStock }}</td>
                  </tr>
                  <tr>
                    <td colspan="4" class="text-right">
                      <strong>{{ $t("reports.total_quantity") }}</strong>
                    </td>
                    <td>
                      <strong>{{ stockIn }}</strong>
                    </td>
                    <td>
                      <strong>{{ stockOut }}</strong>
                    </td>
                    <td>
                      <strong>{{ stockInHand }}</strong>
                    </td>
                    <!-- <td>&nbsp;</td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- <div class="row no-print mt-5">
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
          </div> -->
        </div>
      </div>
    </div>
    <div
      v-else-if="inventoryData && inventoryItems(inventoryData) <= 0"
      class="row"
    >
      <div class="col-lg-12 col-xl-10 offset-xl-1">
        <EmptyTable />
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import { mapGetters } from "vuex";
import moment from "moment";
import "vue-mj-daterangepicker/dist/vue-mj-daterangepicker.css";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("reports.inventory.page_title") };
  },
  data: () => ({
    breadcrumbsCurrent: "reports.inventory.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "reports.inventory.breadcrumbs_first",
        url: "home",
      },
      {
        name: "reports.inventory.breadcrumbs_second",
        url: "",
      },
      {
        name: "reports.inventory.breadcrumbs_active",
        url: "",
      },
    ],
    form: new Form({
      // fromDate: moment().subtract(7,'d').format('YYYY-MM-DD'),
      fromDate: moment().subtract(1,'d').format('YYYY-MM-DD'),
      toDate: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss.SSS'),
      category: {id: 0, name: "All Categories", slug: "all"},
      subCategory: {id: 0, name: "All Sub Categories", slug: "all"},
      itemName: {id: 0, name: "All Items", slug: "all"},
      shop_id:''
    }),
    loading: false,
    subCategories: [],
    products: [],
    date: new Date(),
    inventoryData: "",
    stockIn: 0,
    stockOut: 0,
    stockInHand: 0,
    prefix: "",
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
    this.prefix = this.appInfo.productPrefix;
    this.form.category = 1;
    this.getProducts();
    this.getInventoryData();
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    },
    // get all categories
    async getCatgories() {
      await this.$store.dispatch("operations/allData", {
        path: "/api/all-product-categories",
      });
      this.items.unshift({
        id: 0,
        name: "All Categories",
        slug: "all",
      });
      this.getSubCategories();
    },

    async getHotelDataList () {
        await this.$store.dispatch('operations/getHotelData', {
            path: '/api/shop',
        });
    },

    // get sub categories for a category
    async getSubCategories() {
      this.subCategories = [];
      // this.form.subCategory = "";

      let slug = this.form.category.slug ?? 'liquor';
      const { data } = await axios.get(
        window.location.origin + "/api/pro-sub-categories-by-category/" + slug
      );
      this.subCategories = data.cats;
      this.products = data.products;

      if (this.subCategories.length > 0) {
        this.subCategories.unshift({
          id: 0,
          name: "All Sub Categories",
          slug: "all",
        });
      }

      if (this.products.length > 0) {
        this.products.unshift({
          id: 0,
          name: "All Items",
          slug: "all",
        });
      }
    },

    // get products for a sub category
    async getProducts() {
      this.products = [];
      // this.form.itemName = "";
      let catSlug = this.form.category.slug ?? 'liquor';
      let subCatSlug = this.form.subCategory.slug;
      const { data } = await axios.get(
        window.location.origin +
          "/api/products-by-sub-categories/" +
          catSlug +
          "/" +
          subCatSlug
      );
      this.products = data.data;
      if (this.products.length > 0) {
        this.products.unshift({
          id: 0,
          name: "All Items",
          slug: "all",
        });
      }
    },

    // get filtered data
    async update(values) {

      this.form.fromDate = values.from;
      this.form.toDate = values.to;
      this.getInventoryData();

    },

    //getInventory data

    async getInventoryData(){
      this.loading = true;
      this.form.shop_id = (this.selectedHotel != 'all') ? this.selectedHotel : ''
      await this.form
        .post(window.location.origin + "/api/reports/inventory")
        .then((response) => {
          this.inventoryData = response.data;
          this.calculateSum(this.inventoryData);
          this.loading = false;
          this.filterOpen = false;
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("common.delete_failed") });
        });


    },

    // count inventory items
    inventoryItems(obj) {
      var size = 0,
        key;
      for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
      }
      return size;
    },

    // calculate sum qty
    calculateSum(inventory) {
      let [itemIn, itemOut, itemInHand, key] = [0, 0, 0, 0];
      for (key in inventory) {
        if (inventory.hasOwnProperty(key)) {
          itemIn += Number(inventory[key].stockIn);
          itemOut += Number(inventory[key].stockOut);
          itemInHand += Number(inventory[key].availableStock);
        }
      }
      this.stockIn = itemIn;
      this.stockOut = itemOut;
      this.stockInHand = itemInHand;
    },

    // print
    printWindow() {
      window.print();
    },
  },
  watch:{
    selectedHotel(newData){
      if(this.form.category !== '' && this.form.itemName !== '' && this.form.subCategory !== ''){
        this.getInventoryData();
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
