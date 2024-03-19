<template>
    <div class="position-relative">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row no-print mb-2">
            <div class="w-100 text-right float-right d-flex justify-content-between card-header flex-wrap acc_trn_head">

                <div class="d-flex align-items-center">
                    <button class="modal-default-button btn btn-primary float-left" @click="filterOpen = !filterOpen">
                        {{ $t("common.filter") }}<i class="fas ml-1"
                            :class="filterOpen ? 'fa-angle-up' : 'fa-angle-down '" />
                    </button>
                    <span class="px-2 py-1 date_disp">
                        {{ form.fromDate | moment("Do MMM, YYYY") }} TO {{ form.toDate | moment("Do MMM, YYYY") }}
                    </span>
                </div>


                <div class="btn-group" v-if="allData">
                    <!-- <a
                      @click="generatePDF()"
                      href="#"
                      class="btn btn-primary"
                      v-tooltip="$t('download')"
                    >
                      <i class="fas fa-download"></i>
                    </a> -->
                    <a :href="'/account-transactions/excel/' + allData.slug" v-tooltip="$t('common.export_table')"
                        class="btn btn-info">
                        <i class="fas fa-download"></i> {{ $t("download") }}
                    </a>

                    <a href="#" @click="printWindow" class="btn btn-dark"><i class="fas fa-print"></i> {{ $t("common.print")
                    }}</a>
                    <router-link :to="{ name: 'ledger-account' }" class="btn btn-secondary rounded-top rounded-bottom float-right">
                        <i class="fas fa-long-arrow-alt-left" />
                        {{ $t("common.back") }}
                    </router-link>
                </div>
            </div>
        </div>
        <div class="col-3 my-3 date_pic date_picker_container" v-if="filterOpen">
            <template :class="w - 100">
                <date-range-picker :from="form.fromDate" :to="form.toDate" @update="updateDate" />
            </template>
        </div>
        <div class="row">
            <div class="invoice p-3 mb-3 w-100">
                <!-- info row -->
                <div class="row mb-3">
                    <div v-if="allData" class="col-12 invoice-col">
                        <h5 v-if="allData.date">
                            {{ allData.ledgerName }} {{ isAsset ? '(Asset)' : '' }}
                        </h5>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box">
                            <div class="inner">
                                <div class="icon mb-2">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <h4>
                                    <span>{{ openingBalance | withCurrency }}</span>
                                </h4>
                                <p>{{ $t("cashbook.common.opening_balance") }}</p>
                            </div>
                        </div>

                        <!-- <div class="small-box bg-info">
                            <div class="inner">
                                <h4>
                                    <span>{{ openingBalance | withCurrency }}</span>
                                </h4>
                                <p>{{ $t("cashbook.common.opening_balance") }}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box">
                            <div class="inner">
                                <div class="icon mb-2">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <h4>{{ creditAmount | withCurrency }}</h4>
                                <p>{{ isAsset ? $t("cashbook.common.debit_amount") : $t("cashbook.common.credit_amount") }}
                                </p>
                            </div>
                        </div>
                        <!-- <div class="small-box bg-success">
                            <div class="inner">
                                <h4>{{ creditAmount | withCurrency }}</h4>
                                <p>{{ isAsset ? $t("cashbook.common.debit_amount") : $t("cashbook.common.credit_amount") }}
                                </p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box">
                            <div class="inner">
                                <div class="icon mb-2">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <h4>{{ debitAmount | withCurrency }}</h4>
                                <p>{{ isAsset ? $t("cashbook.common.credit_amount") : $t("cashbook.common.debit_amount") }}
                                </p>
                            </div>
                        </div>
                        <!-- <div class="small-box bg-secondary">
                            <div class="inner">
                                <h4>{{ debitAmount | withCurrency }}</h4>
                                <p>{{ isAsset ? $t("cashbook.common.credit_amount") : $t("cashbook.common.debit_amount") }}
                                </p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box">
                            <div class="inner">
                                <div class="icon mb-2">
                                    <i class="fas fa-piggy-bank"></i>
                                </div>
                                <h4>{{ closingBalance | withCurrency }}</h4>
                                <p>{{ $t("common.closing_balance") }}</p>
                            </div>
                        </div>
                        <!-- <div class="small-box bg-primary">
                            <div class="inner">
                                <h4>{{ closingBalance | withCurrency }}</h4>
                                <p>{{ $t("common.closing_balance") }}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="row">
                    <table-loading v-show="loading" />
                    <div class="table-responsive table-custom">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ $t("common.s_no") }}</th>
                                    <th>{{ $t("common.date") }}</th>
                                    <th>{{ $t("sidebar.shop") }}</th>
                                    <th>{{ $t("common.particular") }}</th>
                                    <th>{{ inr }}  {{ $t("common.debit") }}</th>
                                    <th>{{ inr }} {{ $t("common.credit") }}</th>
                                    <th>{{ inr }} {{ $t("common.balance") }}</th>
                                    <th>Entry By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="transactions && transactions.length"  v-for="(data, index) in transactions" :key="data.id">
                                    <td>
                                    <span v-if="pagination.current_page > 1">
                                        {{
                                        pagination.per_page * (pagination.current_page - 1) +
                                        (index + 1)
                                        }}
                                    </span>
                                    <span v-else>{{ index + 1 }}</span>
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
                                    <td>
                                        <span v-if="data.customer_name != ''">
                                            <router-link v-if="$can('client-view')" :to="{
                                                name: 'clients.show',
                                                params: { slug: data.customer_slug },
                                            }">
                                                {{ data.customer_name }}
                                            </router-link>
                                            <span v-else>{{ data.customer_name }}</span>
                                            -
                                        </span>
                                        {{ data.reason }}
                                    </td>
                                    <td class="text-right" v-if="!data.isAsset">
                                        <span v-if="data.type === 1">{{ 0 }}</span>
                                        <span v-else>{{ Number(data.amount).toFixed(2) | indformate }}</span>
                                    </td>
                                    <td class="text-right" v-else>
                                        <span v-if="data.type === 1">{{
                                            Number(data.amount).toFixed(2) | indformate
                                        }}</span>
                                        <span v-else>{{ 0 }}</span>
                                    </td>
                                    <td class="text-right" v-if="!data.isAsset">
                                        <span v-if="data.type === 1">{{
                                            Number(data.amount).toFixed(2) | indformate
                                        }}</span>
                                        <span v-else>{{ 0 }}</span>
                                    </td>
                                    <td class="text-right" v-else>
                                        <span v-if="data.type === 1">{{ 0 }}</span>
                                        <span v-else>{{ Number(data.amount).toFixed(2) | indformate }}</span>
                                    </td>
                                    <td class="text-right">{{ Number(data.balance).toFixed(2) }}</td>
                                    <td class="text-right">{{ data.user.name }}</td>
                                </tr>
                                <tr v-show="!loading && !transactions.length">
                                    <td colspan="8">
                                        <EmptyTable />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="dtable-footer w-100">
                        <div class="form-group row display-per-page">
                            <label>{{ $t("per_page") }} </label>
                            <div>
                                <select @change="updatePerPager" v-model="perPage"
                                    class="form-control form-control-sm ml-1">
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
                    </div>
                    <!-- <div v-show="allData.length" class="no-print callout callout-danger mt-4 w-100">
                        <h5>{{ $t("cashbook.accounts.view.empty_transaction") }}</h5>
                        <p>{{ $t("cashbook.accounts.view.empty_transaction_msg") }}</p>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import i18n from "~/plugins/i18n";
import axios from "axios";
import { mapGetters } from "vuex";
import Form from "vform";
import moment from "moment";
import "vue-mj-daterangepicker/dist/vue-mj-daterangepicker.css";

export default {
    middleware: ["auth", "check-permissions"],
    metaInfo() {
        return { title: this.$t("cashbook.accounts.view.page_title") };
    },
    data: () => ({
        form: new Form({
            fromDate: String(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)),
            toDate: String(new Date()),
        }),
        breadcrumbsCurrent: "cashbook.accounts.view.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "cashbook.accounts.view.breadcrumbs_first",
                url: "home",
            },
            {
                name: "cashbook.accounts.view.breadcrumbs_second",
                url: "accounts.index",
            },
            {
                name: "cashbook.accounts.view.breadcrumbs_active",
                url: "",
            },
        ],
        query: "",
        allData: "",
        creditAmount: 0,
        debitAmount: 0,
        openingBalance: 0,
        closingBalance: 0,
        filterOpen: false,
        perPage: 10,
        page: 1,
        totalCount: 0,
        isAsset: 0,
        query: "",
        prefix: "",
        inr: "",
        currentPage: 1,
        sortBy: '', // Property to sort by
        sortDirection: 'asc',
        transactions:[],
        pagination:[],
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
        ...mapGetters("operations", ["items", "loading","appInfo"]),
    },
    watch: {
        // watch search data
        query: function (newQ) {
            if (newQ === "") {
                this.getTransactions();
            } else {
                this.searchTransactions();
            }
        },
    },

    created() {
        this.getAccount();
        this.getTransactions();
        this.prefix = this.appInfo.purchasePrefix;
        this.inr = this.appInfo.currency.symbol;
    },
    methods: {
        
        async updateDate(values) {
            this.form.fromDate = values.from;
            this.form.toDate = values.to;
            await this.getTransactions(values.from, values.to)
        },
        // update per page count
        updatePerPager() {
            this.pagination.current_page = 1; 
            this.query === "" ? this.getTransactions() : this.searchTransactions();
        },

        // get the account
        async getAccount(to = '', from = '') {
            const { data } = await axios.get(
              window.location.origin + "/api/accounts/" + this.$route.params.slug + "?from=" + from + "&to=" + to

            );
            this.allData = data.data;
        },

        // get the supplier lc
        async getTransactions(from = '', to = '') {
          try { 
            if (from == '' && to == '') {
                const formDate = moment().subtract(7, 'd').format('YYYY-MM-DD');
                this.form.fromDate = formDate;
                const endDate = moment().endOf('day');
                this.form.toDate = endDate.format('YYYY-MM-DD HH:mm:ss.SSS');
            } 
          
            let currentPage = this.pagination?.current_page ? this.pagination.current_page : 1;
            
            this.$store.state.operations.loading = true; 
            
            const { data } = await axios.get(
                    window.location.origin +
                    "/api/accounts/transactions/" +
                    this.$route.params.slug +"?from="+from+"&to="+to+"&perPage="+this.perPage+"&page="+currentPage
                );
            
            if(data){
                let totalBalance = data?.data[0]?.prevTotal || 0;
                this.openingBalance = data?.data[0]?.prevTotal || 0;
                this.transactions = data.data;
                this.pagination = data.meta;
               
                this.transactions = data.data.map((transaction) => {
                    totalBalance =
                        transaction.type == 0
                            ? totalBalance - transaction.amount
                            : totalBalance + transaction.amount; // Debit subtracts, Credit adds
                    return { ...transaction, balance: totalBalance };
                });

                this.transactions.sort((a, b) => b.id - a.id);

                this.creditAmount = 0;
                this.debitAmount = 0;
                this.isAsset = 0;
                data.data.forEach((value) => {
                    if (value.type == 0) {
                        this.debitAmount -= value.amount;
                    } else {
                        this.creditAmount += value.amount;
                    }
                    if (value.isAsset === 1) this.isAsset = 1;
                })
            }

            this.closingBalance = this.openingBalance + this.creditAmount + this.debitAmount;

            this.debitAmount = Math.abs(this.debitAmount);
            this.totalCount = this.transactions.length;
            this.$store.state.operations.loading = false;
            this.filterOpen = false;

          } catch (error) {
         // Handle errors
        console.error('Error fetching transactions:', error);
      } finally {
        this.$store.state.operations.loading = false;
        this.filterOpen = false;
      }
        },



        // search lc
        async searchTransactions() {
            let currentPage = this.pagination ? this.pagination.current_page : 1;
            this.$store.state.operations.loading = true;
            await this.$store.dispatch("operations/searchData", {
                term: this.query,
                path: "/api/accounts/transactions/" + this.$route.params.slug + "/search",
                currentPage: currentPage + "&perPage=" + this.perPage,
                startDate: this.dateRange.startDate,
                endDate: this.dateRange.endDate,
            });
        },

        // pagination
        async paginate(page) { 
            console.log(this.pagination);
            this.pagination.current_page = page;
            console.log(this.pagination.current_page);
            this.query === "" ? this.getTransactions() : this.searchTransactions();
        },


        // reset purchase pagination
        async resetPagination() {
            this.pagination.current_page = 1;
        },

        // reload purchases after search
        async reload() {
            this.query = "";
            await this.searchTransactions();
        },

        // print
        printWindow() {
            window.print();
        },
    },
};
</script>

