<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card" v-if="$can('ledger-account-list') ||
            $can('ledger-account-create') ||
            $can('ledger-account-edit') ||
            $can('ledger-account-transaction') ||
            $can('ledger-account-delete')
            ">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-4 mb-2">
                                <!--                                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />-->
                            </div>
                            <div class="col-12 col-md-6 col-xl-4 mb-2">

                            </div>
                            <div class="col-12 col-md-12 col-xl-4 text-right mb-2">
                                <div class="btn-group c-w-100">
                                    <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                                        <i class="fas fa-sync"></i>
                                    </a>
                                    <router-link :to="{ name: 'ledger-account.create' }" class="btn btn-primary"
                                        v-if="$can('ledger-account-create')">
                                        {{ $t("common.create") }}
                                        <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body position-relative mt-4">
                        <div id="tabs">
                            <div class="tabs">
                                <a v-for="item in hotelItems" :key="item.id" v-on:click="activetab = (item.id + 1)"
                                    v-bind:class="[activetab === (item.id + 1) ? 'active' : '']">{{ item.name }}</a>

                                <a v-on:click="activetab = 1" v-bind:class="[activetab === 1 ? 'active' : '']">All</a>
                            </div>

                            <div v-if="activetab === 1" class="tabcontent">
                                <table-loading v-show="loading" />
                                <div id="printMe" class="table-responsive table-custom mt-3">
                                    <LedgerAccountCommon :hotelCategoryItems="ledgerAccountData"
                                        :page="pagination.current_page" @refreshData="refreshData" :loading="loading" />
                                </div>
                            </div>

                            <div v-if="activetab === 2" class="tabcontent">
                                <table-loading v-show="loading" />
                                <div id="printMe" class="table-responsive table-custom mt-3">
                                    <LedgerAccountCommon :hotelCategoryItems="ledgerAccountData"
                                        :page="pagination.current_page" @refreshData="refreshData" :loading="loading" />
                                </div>
                            </div>

                            <div v-if="activetab === 3" class="tabcontent">
                                <table-loading v-show="loading" />
                                <div id="printMe" class="table-responsive table-custom mt-3">
                                    <LedgerAccountCommon :hotelCategoryItems="ledgerAccountData"
                                        :page="pagination.current_page" @refreshData="refreshData" :loading="loading" />
                                </div>
                            </div>

                            <div v-if="activetab === 4" class="tabcontent">
                                <table-loading v-show="loading" />
                                <div id="printMe" class="table-responsive table-custom mt-3">
                                    <LedgerAccountCommon :hotelCategoryItems="ledgerAccountData"
                                        :page="pagination.current_page" @refreshData="refreshData" :loading="loading" />
                                </div>
                            </div>

                            <div v-if="activetab === 5" class="tabcontent">
                                <table-loading v-show="loading" />
                                <div id="printMe" class="table-responsive table-custom mt-3">
                                    <LedgerAccountCommon :hotelCategoryItems="ledgerAccountData"
                                        :page="pagination.current_page" @refreshData="refreshData" :loading="loading" />
                                </div>
                            </div>

                            <div v-if="activetab === 6" class="tabcontent">
                                <table-loading v-show="loading" />
                                <div id="printMe" class="table-responsive table-custom mt-3">
                                    <LedgerAccountCommon :hotelCategoryItems="ledgerAccountData"
                                        :page="pagination.current_page" @refreshData="refreshData" :loading="loading" />
                                </div>
                            </div>
                        </div>
                        <small class="ml-2">
                            <i>
                                All prices are in {{ '' | defaultwithCurrency }}
                            </i>
                        </small>

                    </div>
                    <!-- /.card-body -->
                    <div v-if="shouldShowPerPageSelection" class="dtable-footer card-footer">
                        <div class="form-group row display-per-page mb-0">
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
                        <!-- pagination-end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import Form from 'vform'
import LedgerAccountCommon from '../../components/LedgerAccountCoomon.vue'

export default {
    // middleware: ["auth", "check-permissions"],
    metaInfo() {
        return { title: this.$t("ledger_account.index.page_title") };
    },
    mounted() {
        this.getCoaListData()
    },
    components: {
        LedgerAccountCommon
    },
    data: () => ({
        breadcrumbsCurrent: "ledger_account.index.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "ledger_account.index.breadcrumbs_first",
                url: "home",
            },
            {
                name: "ledger_account.index.breadcrumbs_second",
                url: "",
            },
        ],
        activetab: 2,
        perPage: 10,
        form: new Form({
            ledger_type: '1',
            perPage: 10,
            page: 1,
        }),
        pagination: {},
        ledgerAccountData: [],
        query: ''
    }),
    // Map Getters
    computed: {
        ...mapGetters("operations", ["items", "loading", "appInfo", "hotelCategoryItems", "hotelItems", "selectedHotel"]),

        shouldShowPerPageSelection() {
            return this.ledgerAccountData.length === 10;
        },
    },
    watch: {
        selectedHotel: function () {
            this.getData();
        },
        async activetab(currentValue, oldValue) {
            this.form.page = 1;
            this.form.perPage = this.perPage;
            let pageData = this.pagination;
            if (currentValue == 1) {
                this.form.ledger_type = ''
                const { data } = await this.form.post(window.location.origin + '/api/account/ledger/list')
                this.ledgerAccountData = data.data
                console.log(this.ledgerAccountData);
                pageData = data;
                this.$store.state.operations.loading = false;
            } else if (currentValue == 2) {
                this.form.ledger_type = 1
                const { data } = await this.form.post(window.location.origin + '/api/account/ledger/list')
                this.ledgerAccountData = data.data
                pageData = data;
                this.$store.state.operations.loading = false;
            } else if (currentValue == 3) {
                this.form.ledger_type = 2
                const { data } = await this.form.post(window.location.origin + '/api/account/ledger/list')
                this.ledgerAccountData = data.data
                pageData = data;
                this.$store.state.operations.loading = false;
            } else if (currentValue == 4) {
                this.form.ledger_type = 3
                const { data } = await this.form.post(window.location.origin + '/api/account/ledger/list')
                this.ledgerAccountData = data.data
                pageData = data;
                this.$store.state.operations.loading = false;
            } else if (currentValue == 5) {
                this.form.ledger_type = 4
                const { data } = await this.form.post(window.location.origin + '/api/account/ledger/list')
                this.ledgerAccountData = data.data
                pageData = data;
                this.$store.state.operations.loading = false;
            } else if (currentValue == 6) {
                this.form.ledger_type = 5
                const { data } = await this.form.post(window.location.origin + '/api/account/ledger/list')
                this.ledgerAccountData = data.data
                pageData = data;
                this.$store.state.operations.loading = false;
            }
            this.pagination = pageData;
            delete this.pagination.data;
        }
    },
    created() {
        this.getData();
        this.getCoaListData()
    },
    methods: {
        refreshData() {
            this.getData();
        },

        async getCoaListData() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/account/coa'
            })
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
                }.bind(this),
                500
            );
        },
        // update per page count
        updatePerPager() {
            this.pagination.current_page = 1;
            this.query === "" ? this.getData() : this.searchData();
        },
        // get data
        async getData() {
            let page = this.pagination?.current_page || 1
            this.form.page = page;
            this.form.perPage = this.perPage;
            const { data } = await this.form.post(window.location.origin + '/api/account/ledger/list')
            this.ledgerAccountData = data.data
            console.log(this.ledgerAccountData);
            this.pagination = data;
            delete this.pagination.data;
            this.$store.state.operations.loading = false;
        },

        // Pagination
        async paginate(page) {
            this.pagination.current_page = page;
            this.query === "" ? this.getData() : this.searchData();
        },

        // Reset pagination
        async resetPagination() {
            this.pagination.current_page = 1;
        },

        // search data
        async searchData() {
            this.getData();
            // this.$store.state.operations.loading = true;
            // let currentPage = this.pagination ? this.pagination.current_page : 1;
            // await this.$store.dispatch("operations/searchData", {
            //     path: "/api/purchases/search",
            //     term: this.query,
            //     currentPage: currentPage + "&perPage=" + this.perPage,
            //     startDate: this.dateRange.startDate,
            //     endDate: this.dateRange.endDate,
            // });
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
</style>
