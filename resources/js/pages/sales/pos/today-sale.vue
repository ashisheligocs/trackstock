<template>
  <div class="mb-50">
    <breadcrumbs :current="breadcrumbsCurrent" />
    <router-link :to="{ name: 'home' }" class="small-box-footer">
      <!-- <button class="btn btn-secondary mt-2 mb-2"><i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}</button> -->
      <span class="btn btn-secondary mt-2 mb-2"><i class="fas fa-long-arrow-alt-left" /> Back</span>
    </router-link>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- /.card-header -->
          <div class="card-body position-relative">


            <!-- <table-loading v-show="loading" /> -->
            <div id="printMe" class="table-responsive table-custom mt-3">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th class="text-right">Total</th>
                  </tr>

                </thead>
                <tbody>
                  <tr v-if="todaySale && todaySale?.length" v-for="(data, i) in todaySale" :key="i">

                    <td>
                      <span>{{ i + 1 }}</span>
                    </td>
                    <td>{{ data.name }}</td>
                    <td>{{ data.quantity }}</td>
                    <td>{{ data.price }}</td>
                    <td class="text-right">{{ data.quantity * data.price |
      forBalanceSheetCurrencyDecimalOnly }}</td>
                  </tr>
                  <tr>
                    <th></th>
                    <th>Qty</th>
                    <th>{{ total_qty_sale }}</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">{{ total_amount }}</th>
                  </tr>
                  <tr v-show="!todaySale?.length">
                    <td colspan="12">
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

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import moment from "moment";
import { mapGetters } from "vuex";

export default {
  middleware: ["auth"],
  metaInfo() {
    return { title: 'Today Sale' };
  },
  data: () => ({
    breadcrumbsCurrent: "Today Sale",
    breadcrumbs: [
      {
        name: "Home",
        url: "home",
      },
      {
        name: "Today Sale",
        url: "",
      },
      {
        name: "Today Sale",
        url: "",
      },
    ],
    todaySale: [],
    total_qty_sale: 0,
    total_amount: 0,
    form: new Form({
      todayDate: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss.SSS'),
      shop_id: '',
    }),
  }),
  computed: {
    ...mapGetters("operations", ["appInfo", "selectedHotel"]),
  },
  created() {
    this.getTodaySale();
  },
  watch: {
    selectedHotel() {
      this.getTodaySale();
    }
  },
  methods: {
    async getTodaySale() {
      this.form.shop_id = this.selectedHotel;

      await this.form
        .post(window.location.origin + '/api/food/order/todaysale')
        .then((response) => {
          console.log(response);
          this.todaySale = response.data.data;
          this.total_qty_sale = response.data.qty;
          this.total_amount = response.data.amount;
          this.last_order_id = response.data.last_order_id;
        });
    }
  }

}
</script>
