<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card" v-if="$can('shops-list') ||
                    $can('shops-create') ||
                    $can('shops-edit') ||
                    $can('shops-view') ||
                    $can('shops-delete')
                    ">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-4 mb-2">
                                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
                            </div>
                            <div class="col-12 col-md-6 col-xl-4 mb-2">

                            </div>
                            <div class="col-12 col-md-12 col-xl-4 text-right mb-2">
                                <div class="btn-group c-w-100">
                                    <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                                        <i class="fas fa-sync"></i>
                                    </a>
                                    <router-link :to="{ name: 'meals.create' }" class="btn btn-primary">
                                        {{ $t("common.create") }}
                                        <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body position-relative">
                        <table-loading v-show="loading" />
                        <div id="printMe" class="table-responsive table-custom mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ $t("common.s_no") }}</th>
                                        <th>{{ $t("common.name") }}</th>
                                        <th>{{ $t("common.short_name") }}</th>
                                        <th v-if="$can('hotel-edit') ||
                                            $can('hotel-view') ||
                                            $can('hotel-delete')
                                            " class="text-right no-print">
                                            {{ $t("common.action") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                                        <td>
                                            <span v-if="pagination && pagination.current_page > 1">
                                                {{
                                                    pagination.per_page * (pagination.current_page - 1) +
                                                    (i + 1)
                                                }}
                                            </span>
                                            <span v-else>{{ i + 1 }}</span>
                                        </td>
                                        <td>{{ data.plan_name }}</td>
                                        <td>{{ data.short_name }}</td>
                                        <td v-if="$can('hotel-edit') ||
                                            $can('hotel-view') ||
                                            $can('hotel-delete')
                                            " class="text-right no-print">
                                            <div class="btn-group">
                                                <router-link v-if="$can('hotel-edit')" v-tooltip="$t('common.edit')" :to="{
                                                    name: 'meals.edit',
                                                    params: { slug: data.id },
                                                }" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit" />
                                                </router-link>
                                                <a v-if="$can('hotel-delete')" v-tooltip="$t('common.delete')" href="#"
                                                    class="btn btn-danger btn-sm" @click="deleteData(data.id)">
                                                    <i class="fas fa-trash" />
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-show="!loading && !items.length">
                                        <td colspan="12">
                                            <EmptyTable />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="dtable-footer px-4 py-3">
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
                        <!-- pagination-end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import i18n from "~/plugins/i18n";
import DateRangePicker from "vue2-daterange-picker";
import moment from "moment";
import { mapGetters } from "vuex";
export default {
    // middleware: ["auth", "check-permissions"],
    metaInfo() {
        return { title: this.$t("meals.index.page_title") };
    },
    components: {
        DateRangePicker,
    },
    data: () => ({
        breadcrumbsCurrent: "meals.index.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "meals.index.breadcrumbs_first",
                url: "home",
            },
            {
                name: "meals.index.breadcrumbs_second",
                url: "",
            },
        ],
        perPage: 10,
        query: "",
        prefix: "",
        minDate: moment(new Date("01-01-2021")).format("YYYY-MM-DD"),
        maxDate: moment().add(1, "days").format("YYYY-MM-DD"),
        dateRange: {
            startDate: "",
            endDate: "",
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
        ...mapGetters("operations", ["items", "loading", "pagination", "appInfo", "hotelCategoryItems"]),
    },
    watch: {
        // watch search data
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
    },
    created() {
        this.getData();
        this.prefix = this.appInfo.purchasePrefix;
    },
    methods: {
        // filter data for selected date range
        async updateValues() {
            this.dateRange.startDate = moment(this.dateRange.startDate).format(
                "YYYY-MM-DD"
            );
            this.dateRange.endDate = moment(this.dateRange.endDate).format(
                "YYYY-MM-DD"
            );
            this.searchData();
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
            this.$store.state.operations.loading = true;
            let currentPage = this.pagination ? this.pagination.current_page : 1
            await this.$store.dispatch("operations/fetchData", {
                path: "/api/meal?page=",
                currentPage: currentPage + '&perPage=' + this.perPage,
            });
        },

        // Pagination
        async paginate() {
            this.query === "" ? this.getData() : this.searchData();
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
                path: "/api/purchases/search",
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

        // delete data
        async deleteData(slug) {
            Swal.fire({
                title: this.$t("common.delete_title"),
                text: this.$t("purchases.list.index.delete_warning"),
                type: "warning",
                showCancelButton: true,
                confirmButtonText: this.$t("common.delete_confirm_text"),
            }).then((result) => {
                // Send request to the server
                if (result.value) {
                    this.$store
                        .dispatch("operations/hotelDeleteData", {
                            path: "/api/meal/delete/",
                            slug: slug,
                        })
                        .then((response) => {
                            if (response === true) {
                                Swal.fire(
                                    this.$t("common.deleted"),
                                    this.$t("common.delete_success"),
                                    "success"
                                );
                                this.getData()
                            } else {
                                Swal.fire(
                                    this.$t("common.failed"),
                                    this.$t(response.message),
                                    "warning"
                                );
                            }
                        });
                }
            });
        },
    },
};
</script>
