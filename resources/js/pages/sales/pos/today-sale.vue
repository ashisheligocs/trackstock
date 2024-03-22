<template>
    <div class="mb-50">
        <breadcrumbs :current="breadcrumbsCurrent" />
        <router-link :to="{ name: 'dashoard' }" class="small-box-footer"></router-link>
            <button class="btn btn-secondary mt-2 mb-2"><i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}</button>
        </router-link>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body position-relative"> 
                       

                        <table-loading v-show="loading" />
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
                                        <td class="text-right">{{ data?.total_price |
                                            forBalanceSheetCurrencyDecimalOnly }}</td>
                                    </tr>
                                    <tr v-show="!loading && !todaySale?.length">
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

                    <div class="dtable-footer pb-3">
                        <!-- pagination-start -->
                        <div></div>
                        <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
                            class="justify-flex-end" @paginate="paginate" />
                        <!-- pagination-end -->
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
        form: new Form({
            todayDate: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss.SSS'),
            shop_id: '',
        }),
    }),
    computed: {
          ...mapGetters("operations", ["appInfo","selectedHotel"]),
      },
    created() {
        this.getTodaySale();
    },
    watch:{
        selectedHotel(){
            this.getTodaySale();
        }
    },
    methods:{
       async getTodaySale(){
            this.form.shop_id = this.selectedHotel;

            await this.form
              .post(window.location.origin + '/api/food/order/todaysale')
              .then((response) => {
                    this.todaySale = response.data.data
                  
              });
        }
    }

}
</script>
