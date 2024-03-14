<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card" v-if="$can('hotel-create') ||
                    $can('hotel-list') ||
                    $can('hotel-edit') ||
                    $can('hotel-delete')
                    ">
                    <div class="card-header m-0">
                        <div class="row justify-content-between p-2">
                            <div class="col-6 col-xl-4 mb-2">
                                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
                            </div>
                            <div class="col-6 col-xl-4 text-right mb-2" v-if="$can('hotel-create')">
                                <div class="btn-group c-w-100">
                                    <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                                        <i class="fas fa-sync"></i>
                                    </a>
                                    <a href="/hotel/excel" v-tooltip="$t('common.export_table')" class="btn btn-dark ">
                                        <i class="fas fa-file-export"></i>
                                    </a>
                                    <router-link :to="{ name: 'hotel.create' }" class="btn btn-primary">
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
                            <table class="table border">
                                <thead>
                                    <tr>
                                        <th>{{ $t("common.s_no") }}</th>
                                        <th>{{ $t("common.image") }}</th>
                                        <th>{{ $t("hotel.common.name") }}</th>
                                        <th>{{ $t("hotel.common.hotel_email") }}</th>
                                        <th>{{ $t("common.phone") }}</th>
                                        <th>{{ $t("hotel.common.total_no_of_rooms") }}</th>
                                        <th v-if="$can('hotel-edit') ||
                                            $can('hotel-view') ||
                                            $can('hotel-delete')
                                            " class="text-right no-print">
                                            {{ $t("common.action") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-show="hotelCategoryItems.length" v-for="(data, i) in hotelCategoryItems" :key="i">
                                        <td>
                                            <span v-if="pagination && pagination.current_page > 1">
                                                {{
                                                    pagination.per_page * (pagination.current_page - 1) +
                                                    (i + 1)
                                                }}
                                            </span>
                                            <span v-else>{{ i + 1 }}</span>
                                        </td>
                                        <td> <a v-if="data?.images" href="#" id="show-modal">
                                                <img :src="'images/hotel/' + data?.image_path" class="rounded preview-sm"
                                                    loading="lazy" />
                                            </a>
                                            <div v-else class="bg-secondary rounded no-preview-sm">
                                                <small>{{ $t("common.no_preview") }}</small>
                                            </div>
                                        </td>
                                        <td>{{ data.hotel_name }}</td>
                                        <td>{{ data.hotel_email }}</td>
                                        <td>{{ data.contact_phone }}</td>
                                        <td>{{ data.total_no_of_rooms }}</td>
                                        <td v-if="$can('hotel-edit') ||
                                            $can('hotel-view') ||
                                            $can('hotel-delete')
                                            " class="text-right no-print">
                                            <div class="btn-group">
                                                <router-link v-if="$can('hotel-edit')" v-tooltip="$t('common.edit')" :to="{
                                                    name: 'hotel.edit',
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
                    <div class="dtable-footer card-footer" v-if="shouldShowPerPageSelection">
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
        return { title: this.$t("hotel.hotelAdd.index.page_title") };
    },
    components: {
        DateRangePicker,
    },
    data: () => ({
        breadcrumbsCurrent: "hotel.hotelAdd.index.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "hotel.hotelAdd.index.breadcrumbs_first",
                url: "home",
            },
            {
                name: "hotel.hotelAdd.index.breadcrumbs_active",
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
        ...mapGetters("operations", ["items", "loading", "pagination", "appInfo", "hotelCategoryItems", "selectedHotel"]),

        shouldShowPerPageSelection() {
            return this.hotelCategoryItems.length === 10;
        },
    },
    watch: {
        // watch search data
        query: function (newQ, oldQ) {
            this.getData();
        },
        selectedHotel: function () {
            this.getData();
        }
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
            await this.$store.dispatch("operations/getHotelCategoryData", {
                path: "/api/hotel?search=",
                search: this.query
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
            await this.getData();
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
                            path: "/api/hotel/delete/",
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

