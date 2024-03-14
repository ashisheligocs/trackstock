<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row no-print justify-content-end mx-2 position-relative">
            <div class="float-right mb-2 d-flex align-items-center">
        <span class="px-2 py-1">
              {{ form.fromDate | moment("Do MMM, YYYY") }} TO {{ form.toDate | moment("Do MMM, YYYY") }}
            </span>
        <button
                class="modal-default-button btn btn-primary float-left"
                @click="filterOpen = !filterOpen"
        >
            {{ $t("common.filter") }}<i class="fas ml-1" :class="filterOpen ? 'fa-angle-up' : 'fa-angle-down '"/>
        </button>
           
        </div>
      <div class="col-lg-5 position-absolute bal_filter" v-if="filterOpen">
        <div class="filter_card">
          <!-- form start -->
          <form
            role="form"
            @submit.prevent="saveType"
            @keydown="form.onKeydown($event)"
          >
            <div class="card-body p-0">

                            <!-- <div class="col-3">
                <template :class="w - 100">
                  <date-range-picker
                    :from="form.fromDate"
                    :to="form.toDate"
                    :panel="$route.query.panel"
                    @update="update"
                  />
                </template>
              </div> -->
                            <div class="row">
                                <div class="col" v-if="filterOpen">
                                    <template :class="w - 100">
                                        <date-range-picker :from="form.fromDate" :to="form.toDate"
                                            :panel="$route.query.panel" @update="update" />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 invoice p-3 mb-3">
                <div class="row invoice-info mt-2 position-relative">
                    <div class="col-sm-4 invoice-col">
                        <CompanyInfo :hotel="currentHotel" class="text-left" :showImage="false" />
                    </div>
                </div>
                <div>
                    <table-loading v-show="loading" />

                    <div class="row col-md-12 mt-3" v-if="loading == false">
                        <!-- Liability Table -->
                        <div class="col-md-6 position-relative">
                            <div class="table-responsive table-custom">
                                <Asset :assetdata="assetallData" :total_asset="totalAsset"></Asset>
                            </div>
                        </div>
                        <!-- Asset Table -->
                        <div class="col-md-6 position-relative">
                            <div class="table-responsive table-custom">
                                <Liability :liabilitydata="liabilityallData" :total_liability="totalLiability"></Liability>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- info row -->
                <div class="row invoice-info mt-2 position-relative">
                    <div class="col-sm-4 m-auto invoice-col">
                        <CompanyInfo :hotel="currentHotel" class="text-center" :showImage="false" />
                    </div>
                </div>
                <!-- /.row -->

                <!-- Table row -->
                <div class="row mt-3 position-relative">
                    <table-loading v-show="loading" />
                    <div class="table-responsive w-100">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th class="red" colspan="2">
                                        <h5>{{ $t("reports.liabilities") }}</h5>
                                    </th>
                                    <th class="green" colspan="2">
                                        <h5>{{ $t("reports.asset") }}</h5>
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="balanceInfo">
                                <tr>
                                    <th>{{ $t("reports.account_payable") }}</th>
                                    <th class="text-right">
                                        {{ balanceInfo.accPayable | forBalanceSheetCurrency }}
                                    </th>
                                    <th>{{ $t("reports.account_receivable") }}</th>
                                    <th class="text-right">
                                        {{ balanceInfo.accReceivable | forBalanceSheetCurrency }}
                                    </th>

                                </tr>
                                <tr>
                                    <th>{{ $t("reports.GST") }}</th>
                                    <th class="text-right">
                                        {{ (balanceInfo.outputGst - balanceInfo.inputGst) || 0 | forBalanceSheetCurrency }}
                                    </th>
                                    <th>{{ $t("products.list.common.inventory_value") }}</th>
                                    <th class="text-right">
                                        {{ balanceInfo.inventoryValue | forBalanceSheetCurrency }}
                                    </th>
                                </tr>
                                <tr>
                                    <th>{{ $t("reports.bank_loan") }}</th>
                                    <th class="text-right">
                                        {{ balanceInfo.loanDue | forBalanceSheetCurrency }}
                                    </th>
                                    <th>{{ $t("reports.bank_balance") }}</th>
                                    <th class="text-right">
                                        {{ balanceInfo.bankBalance | forBalanceSheetCurrency }}
                                    </th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{ $t("reports.security") }}</th>
                                    <th class="text-right">
                                        {{ balanceInfo.assets | forBalanceSheetCurrency }}
                                    </th>
                                </tr>
                                <tr class="text-center">
                                    <th class="red" colspan="4">
                                    </th>
                                </tr>
                                <tr>
                                    <th>{{ $t("reports.equity") }}</th>
                                    <th class="text-right">
                                        0
                                    </th>
                                    <th colspan="2"></th>
                                </tr>

                                <tr class="text-center">
                                    <th class="red" colspan="4">
                                    </th>
                                </tr>
                                <tr>
                                    <th>{{ $t("reports.revenue") }}</th>
                                    <th class="text-right">
                                        {{ balanceInfo.revenue }}
                                    </th>
                                    <th colspan="2"></th>
                                </tr>

                                <tr class="text-center">
                                    <th class="red" colspan="4">
                                    </th>
                                </tr>
                                <tr class="text-right">
                                    <th>{{ $t("common.total") }}</th>
                                    <th>
                                        {{ balanceInfo.liabilities | forBalanceSheetCurrency }}
                                    </th>
                                    <th>{{ $t("common.total") }}</th>
                                    <th>
                                        {{ balanceInfo.totalAsset | forBalanceSheetCurrency }}
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.row -->

                <!-- this row will not appear when printing -->
                <div class="row no-print mt-5">
                    <div class="col-12">
                        <router-link :to="{ name: 'home' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}
                        </router-link>
                        <a href="#" @click="printWindow" class="btn btn-default"><i class="fas fa-print"></i> {{
                            $t("common.print") }}</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import moment from "moment";
import "vue-mj-daterangepicker/dist/vue-mj-daterangepicker.css";
import { mapGetters } from "vuex";
import Asset from "../../components/Asset.vue";
import Liability from "../../components/Liability.vue";

export default {
    middleware: ["auth", "check-permissions"],
    components: {
        Asset,
        Liability
    },
    metaInfo() {
        return { title: this.$t("reports.balance_sheet.page_title") };
    },
    data: () => ({
        breadcrumbsCurrent: "reports.balance_sheet.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "reports.balance_sheet.breadcrumbs_first",
                url: "home",
            },
            {
                name: "reports.balance_sheet.breadcrumbs_second",
                url: "",
            },
            {
                name: "reports.balance_sheet.breadcrumbs_active",
                url: "",
            },
        ],
        loading: false,
        balanceInfo: "",
        currentHotel: '',
        totalAsset: 0,
        totalLiability: 0,
        liabilityallData: "",
        assetallData: "",
        filterOpen: false,
        form: new Form({
            fromDate: String(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)),
            toDate: String(new Date()),
            ledger_type: "",
        }),
    }),

    computed: {
        ...mapGetters("operations", ["hotelItems", "selectedHotel"]),
    },

    async created() {
        await this.getData();
        await this.getHotelDataList();
        if (this.selectedHotel && this.selectedHotel !== 'all') {
            this.hotelItems.forEach((hotel) => {
                if (hotel.id == this.selectedHotel) this.currentHotel = hotel
            })
        }
        this.getAssetData();
        this.getLiabilityData();
    },
    methods: {
        async getAssetData(values) {
            this.form.ledger_type = '1';
            // this.loading = true;
            if (values) {
                this.form.fromDate = values.from;
                this.form.toDate = values.to;

            } else {

                const formDate = moment().subtract(7, 'd').format('YYYY-MM-DD');
                this.form.fromDate = formDate;

                // const toDate = new Date(this.form.toDate);

                const endDate = moment().endOf('day');
                this.form.toDate = endDate.format('YYYY-MM-DD HH:mm:ss.SSS');
            }

            await this.form
                .post(window.location.origin + "/api/account/ledger/asset")
                .then((response) => {
                    this.assetallData = response.data.data;
                    this.totalAsset = this.assetallData
                        ? this.assetallData.reduce((total, asset) => total + asset[0].total_amount, 0)
                        : 0;
                    // this.loading = false;
                })
                .catch(() => {
                    toast.fire({ type: "error", title: this.$t("common.delete_failed") });
                });
        },

        async update(values) {

            this.form.fromDate = values.from;
            this.form.toDate = values.to;
            this.getAssetData(values);
            this.getLiabilityData(values);

        },

        async getLiabilityData(values) {
            this.form.ledger_type = '2,3';
            if (values) {
                this.form.fromDate = values.from;
                this.form.toDate = values.to;

            } else {

                const formDate = moment().subtract(7, 'd').format('YYYY-MM-DD');
                this.form.fromDate = formDate;

                // const toDate = new Date(this.form.toDate);

                const endDate = moment().endOf('day');
                this.form.toDate = endDate.format('YYYY-MM-DD HH:mm:ss.SSS');
            }

            this.loading = true;
            await this.form
                .post(window.location.origin + "/api/account/ledger/liability")
                .then((response) => {
                    this.liabilityallData = response.data.groupedData;
                    this.liabilityallData.push(response.data.newData);
                    this.totalLiability = this.liabilityallData
                        ? this.liabilityallData.reduce((total, liability) => total + liability[0].total_amount, 0)
                        : 0;
                    this.loading = false;
                    this.filterOpen = false;
                })
                .catch(() => {
                    toast.fire({ type: "error", title: this.$t("common.delete_failed") });
                });
        },
        async getHotelDataList() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/hotel',
            });
        },
        // get data
        async getData() {
            // this.loading = true;
            const { data } = await axios.get(
                window.location.origin + "/api/reports/balance-sheet"
            );
            this.balanceInfo = data;
            // this.loading = false;
        },
        // print
        printWindow() {
            window.print();
        },
    },
    watch: {
        selectedHotel(newData) {
            this.getAssetData();
            this.getLiabilityData();
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

