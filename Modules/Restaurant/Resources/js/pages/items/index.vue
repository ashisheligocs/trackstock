<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card" v-if="$can('restaurant-create') ||
                    $can('restaurant-list') ||
                    $can('restaurant-edit') ||
                    $can('restaurant-delete') ||
                    $can('restaurant-order')
                    ">
                    <div class="card-header">
                        <div class="row justify-content-between">

                            <div class="col-12 col-md-6 col-xl-4 mb-2">
                                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload"
                                    placeholder="Search" />
                            </div>
                            <div class="col-12 col-md-12 col-xl-4 text-right mb-2">
                                <div class="btn-group c-w-100">
                                    <!-- <search v-model="sub_query" @reset-pagination="resetPagination()" @reload="reload"
                                        placeholder="Search" /> -->

                                    <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                                        <i class="fas fa-sync"></i>
                                    </a>
                                    <router-link :to="{ name: 'items.create' }" class="btn btn-primary">
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
                            <table id="dtBasicExample" class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ $t("common.s_no") }}</th>
                                        <th>{{ $t("common.image") }}</th>
                                        <th @click="sortByColumn('item_name')">
                                            {{ $t("common.name") }}
                                            <i class="fas"
                                                :class="sortBy === 'item_name' ? (sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : ''"></i>
                                        </th>



                                        <th>{{ $t("common.food_category") }}</th>
                                        <!-- <th @click="sortByColumn('food_category')">
                                            {{ $t("common.food_category") }}
                                            <i class="fas"
                                                :class="sortBy === 'food_category' ? (sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : ''"></i>
                                        </th> -->
                                        <th>{{ $t("common.status") }}</th>
                                        <th v-if="$can('restaurant-edit') ||
                                            $can('restaurant-view') ||
                                            $can('restaurant-delete')
                                            " class="text-right no-print">
                                            {{ $t("common.action") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="hotelCategoryItems && hotelCategoryItems.length"
                                        v-for="(data, index) in hotelCategoryItems" :key="data.id">
                                        <td>
                                            <span>{{ calculateIndex(index) }}</span>
                                        </td>
                                        <td>
                                            <!-- <img :src="data?.item_image?.replace('public/', 'storage/')" srcset=""
                                                width="80"> -->
                                         
                                                <img :src="data?.item_image" srcset=""
                                                width="80">
                                        </td>
                                        <td>{{ data?.item_name }}</td>
                                        <td>{{ data?.food_category?.category_name }}</td>
                                        <td v-if="data.status == '1'"><span class="badge bg-green">Active</span></td>
                                        <td v-else> <span class="badge bg-danger">In Active</span></td>
                                        <td v-if="$can('restaurant-edit') ||
                                            $can('restaurant-view') ||
                                            $can('restaurant-delete')
                                            " class="text-right no-print">
                                            <div class="btn-group">
                                                <router-link v-if="$can('restaurant-edit')" v-tooltip="$t('common.edit')"
                                                    :to="{
                                                        name: 'items.edit',
                                                        params: { slug: data.id },
                                                    }" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit" />
                                                </router-link>
                                                <a v-if="$can('restaurant-delete')" v-tooltip="$t('common.delete')" href="#"
                                                    class="btn btn-danger btn-sm" @click="deleteData(data.id)">
                                                    <i class="fas fa-trash" />
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-show="!loading && !hotelCategoryItems.length">
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
        return { title: this.$t("restaurant_item.index.page_title") };
    },
    components: {
        DateRangePicker,
    },
    data: () => ({
        breadcrumbsCurrent: "restaurant_item.index.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "restaurant_item.index.breadcrumbs_first",
                url: "home",
            },
            {
                name: "restaurant_item.index.breadcrumbs_second",
                url: "",
            },
        ],
        perPage: 10,
        currentPage: 1,
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
        sortBy: '', // Property to sort by
        sortDirection: 'asc',
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
                    this.search_filter();
                } else {
                    this.getData();
                }
            } else {
                this.search_filter();
            }
        },
    },
    created() {
        this.getData();
        this.prefix = this.appInfo.purchasePrefix;
    },
    methods: {
        sortByColumn(column) {
            if (column === this.sortBy) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortDirection = 'asc';
            }

            // Implement sorting logic for hotelCategoryItems

            this.hotelCategoryItems.sort((a, b) => {
                const modifier = this.sortDirection === 'desc' ? -1 : 1;
                const aValue = _.get(a, column)?.toLowerCase();
                const bValue = _.get(b, column)?.toLowerCase();

                if (aValue === null || aValue === undefined) return -1;
                if (bValue === null || bValue === undefined) return 1;


                if (aValue.startsWith('a') && !bValue.startsWith('a')) {
                    return -1 * modifier;
                } else if (!aValue.startsWith('a') && bValue.startsWith('a')) {
                    return 1 * modifier;
                }

                return aValue.localeCompare(bValue) * modifier;
            });
        },




        // filter data for selected date range
        async updateValues() {
            this.dateRange.startDate = moment(this.dateRange.startDate).format(
                "YYYY-MM-DD"
            );
            this.dateRange.endDate = moment(this.dateRange.endDate).format(
                "YYYY-MM-DD"
            );
            this.search_filter();
        },
        // refresh table
        refreshTable() {
            this.query = "";
            this.dateRange.startDate = null;
            this.dateRange.endDate = null;

            this.query === "" ? this.getData() : this.search_filter();

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
            console.log(this.perPage);
            this.pagination.current_page = 1;
            this.query === "" ? this.getData() : this.search_filter();
        },
        // get data

        async getData() {
            this.$store.state.operations.loading = true;

            // let currentPage = this.pagination?.current_page || 1
            const apiUrl = `/api/food/item?per_page=${this.perPage}&page=${this.pagination.current_page}`;
            await this.$store.dispatch("operations/getHotelCategoryData", {
                path: apiUrl,
            });

            this.$store.state.operations.loading = false;
        },

        calculateIndex(index) {
            console.log( this.pagination.per_page)
            return (this.pagination.current_page - 1) * this.pagination.per_page + index + 1;
        },

        // Pagination
        async paginate(page) {
            this.pagination.current_page = page;
            this.query === "" ? this.getData() : this.search_filter();
        },

        // Reset pagination
        async resetPagination() {
            this.pagination.current_page = 1;
        },

        // search data
        // async search_filter() {
        //     this.$store.state.operations.loading = true;
        //     let currentPage = this.pagination ? this.pagination.current_page : 1;
        //     await this.$store.dispatch("operations/search_filter", {
        //         path: "/api/food/item/",
        //         term: this.query,
        //         currentPage: currentPage + "&perPage=" + this.perPage,
        //         startDate: this.dateRange.startDate,
        //         endDate: this.dateRange.endDate,
        //     });
        // },

        async search_filter() {
            this.$store.state.operations.loading = true;
            let currentPage = this.pagination ? this.pagination.current_page : 1;
            try {
                const resp = await this.$store.dispatch("operations/search_filter", {
                    path: "/api/food/item/",
                    term: this.query,
                    currentPage: currentPage + "&perPage=" + this.perPage,
                    startDate: this.dateRange.startDate,
                    endDate: this.dateRange.endDate,
                });

                // Check if 'items' property exists in the response
                if (resp.hotelCategoryItems) {
                    console.log(resp)
                    this.hotelCategoryItems = resp.hotelCategoryItems.data[0];
                } else {
                    console.warn("Unexpected response structure:", resp);
                }
            } catch (error) {
                console.error("Error fetching search results:", error);
            } finally {
                this.$store.state.operations.loading = false;
            }
        },


        // Reload after search
        async reload() {
            this.query = "";
            console.log(this.query);
            await this.search_filter();
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
                        .dispatch("operations/deleteData", {
                            path: "/api/food/item/delete/",
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

