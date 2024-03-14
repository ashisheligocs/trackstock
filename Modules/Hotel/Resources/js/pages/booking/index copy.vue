<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div v-if="$can('hotel-booking-list') ||
                    $can('hotel-booking-create') ||
                    $can('hotel-booking-edit') ||
                    $can('hotel-booking-view') ||
                    $can('hotel-booking-delete') ||
                    $can('hotel-booking-print') ||
                    $can('hotel-booking-check_in')
                    ">
                    <div>
                        <div class="row d-flex justify-content-between">
                            <div class="col-12 col-md-6 col-xl-4 mb-2">
                                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
                            </div>

                            <div class="col-12 col-md-12 col-xl-4 text-right mb-2">
                                <div class="btn-group c-w-100">
                                    <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                                        <i class="fas fa-sync"></i>
                                    </a>
                                    <router-link :to="{ name: 'booking.create' }" class="btn btn-primary">
                                        {{ $t("common.create") }}
                                        <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0 position-relative">
                        <table-loading v-show="loading" />
                        <div id="printMe" class="table-responsive table-custom mt-3">
                            <div class="row">
                                <div class="col-md-3" v-for="(data, index) in hotelCategoryItems" :key="data">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-md-6"><strong class="name_client"> {{ data?.customer_name
                                                }}</strong></div>
                                                <div class="col-md-6">
                                                    <span v-html="data?.status"></span>
                                                </div>
                                                <div class="col-md-6">{{ data?.booking_number }}</div>
                                                <div class="col-md-6">
                                                    <div class="badge-hotel float-right">
                                                        {{ data?.total_price | indformate }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-bottom-header">
                                            <div class="row total_nights">
                                                <div class="col-md-4">{{ data?.check_in_date }}</div>
                                                <div class="col-md-4 card_header_nights"><span>{{
                                                    calculateNights(data?.check_in_date, data?.check_out_date) }}
                                                        Night</span>
                                                </div>
                                                <div class="col-md-4">{{ data?.check_out_date }}</div>
                                            </div>
                                        </div>
                                        <div class="card-body box-profile">
                                            <ul class="list-group list-group-unbordered mb-3">
                                                <li class="list-group-item">
                                                    <strong>PAX</strong>
                                                    <span class="float-right">
                                                        <div class="">
                                                            <span class="p-0 badge fs-7 me-1 pb-0 text-dark" title="Adult">
                                                                <i class="fas fa-male"></i> 2
                                                            </span>
                                                            <span class="p-0 badge fs-7 me-1 me-1 text-dark"
                                                                title="Children">
                                                                <i class="fas fa-child"></i> 2</span>
                                                            <span class="p-0 badge fs-7 me-1 me-1 text-dark" title="Infant">
                                                                <i class="fas fa-baby"></i> 0
                                                            </span>
                                                        </div>
                                                    </span>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>{{ $t("common.phone") }}</strong>
                                                    <span class="float-right">{{ data?.customer_phone }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Total</strong>
                                                    <span class="float-right">{{ data?.total_price | indformate }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>{{ $t("common.paid_amount") }}</strong>
                                                    <span class="float-right">{{ data?.paid_amount | indformate }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>{{ $t("common.due_amount") }}</strong>
                                                    <span class="float-right"><span v-html="data?.dueAmount"></span></span>
                                                </li>
                                            </ul>
                                            <router-link  :to="{
                                                name: 'booking.create.check-in',
                                                params: { slug: data.id },
                                            }" class="btn-block btn bg-success">
                                                {{ $t("common.check_in") }}
                                            </router-link>
                                            <router-link v-if="$can('hotel-booking-view')"  :to="{
                                                name: 'booking.show',
                                                params: { slug: data.id },
                                            }" class="btn-block btn btn-outline-primary">
                                                {{ $t('common.view') }}
                                            </router-link>
                                            <!-- <span v-else class="btn-block btn bg-danger">{{
                                            $t("common.in_active")
                                        }}</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ $t("common.s_no") }}</th>
                                        <th>{{ $t("booking.show.booking_number") }}</th>
                                        <th>{{ $t("common.name") }}</th>
                                        <th>{{ $t("common.phone") }}</th>
                                        <th>{{ $t("common.check_in") }}</th>
                                        <th>{{ $t("common.check_out") }}</th>
                                        <th>{{ $t("common.paid_amount") }}</th>
                                        <th>{{ $t("common.due_amount") }}</th>
                                        <th>{{ $t("common.booking_status") }}</th>

                                        <th v-if="$can('hotel-booking-edit') ||
                                            $can('hotel-booking-view') ||
                                            $can('hotel-booking-delete')
                                            " class="text-right no-print">
                                            {{ $t("common.action") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in hotelCategoryItems" :key="data">
                                        <td>
                                            <span>{{ index + 1 }}</span>
                                        </td>
                                        <td>{{ data?.booking_number }}</td>
                                        <td>{{ data?.customer_name }}</td>
                                        <td>{{ data?.customer_phone }}</td>
                                        <td>{{ data?.check_in_date }}</td>
                                        <td>{{ data?.check_out_date }}</td>
                                        <td>{{ data?.paid_amount }}</td>
                                        <td><span v-html="data?.dueAmount"></span></td>
                                        <td><span v-html="data?.status"></span></td>


                                        <td v-if="$can('hotel-booking-edit') ||
                                            $can('hotel-booking-view') ||
                                            $can('hotel-booking-delete') ||
                                            $can('hotel-booking-print') ||
                                            $can('hotel-booking-check_in')
                                            " class="text-left no-print">
                                            <div class="btn-group d-block">
                                                <router-link v-if="$can('hotel-booking-edit')" v-tooltip="$t('common.edit')" :to="{
                                                    name: 'booking.edit',
                                                    params: { slug: data.id },
                                                }" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit text-white" />
                                                </router-link>
                                                <router-link v-if="$can('hotel-booking-view')" v-tooltip="$t('common.view')" :to="{
                                                    name: 'booking.show',
                                                    params: { slug: data.id },
                                                }" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye" />
                                                </router-link>
                                                <a v-if="$can('hotel-booking-delete')" v-tooltip="$t('common.delete')" href="#"
                                                    class="btn btn-danger btn-sm" @click="deleteData(data.id)">
                                                    <i class="fas fa-trash" />
                                                </a>
                                            </div>
                                            <div class="btn-group mt-1 d-block">
                                                <a :href="'/booking/pdf/' + data.id"
                                                    v-tooltip="$t('common.export_table')" class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-file-export"></i>
                                                </a>

                                                <router-link v-tooltip="$t('common.check_in')" :to="{
                                                    name: 'booking.create.check-in',
                                                    params: { slug: data.id },
                                                }" class="btn btn-dark btn-sm">
                                                    <i class="fas fa-thumbs-up" />
                                                </router-link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-show="!loading && !hotelCategoryItems.length">
                                        <td colspan="12">
                                            <EmptyTable />
                                        </td>
                                    </tr>
                                </tbody>
                            </table> -->
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="dtable-footer">
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
import { mapGetters } from "vuex";
export default {
    // middleware: ["auth", "check-permissions"],
    metaInfo() {
        return { title: this.$t("booking.index.page_title") };
    },
    data: () => ({
        breadcrumbsCurrent: "booking.index.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "booking.index.breadcrumbs_first",
                url: "home",
            },
            {
                name: "booking.index.breadcrumbs_second",
                url: "",
            },
        ],
        perPage: 10,
    }),
    // Map Getters
    computed: {
        ...mapGetters("operations", ["items", "loading", "pagination", "appInfo", "hotelCategoryItems"]),
    },
    created() {
        this.getData();
    },
    methods: {
        /*** calcuate night  */
        calculateNights(startDate, endDate) {
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                // Calculate the time difference in milliseconds
                const timeDiff = end.getTime() - start.getTime();
                // Calculate the number of nights by dividing the time difference by the number of milliseconds in a day (86400000)
                const nights = Math.floor(timeDiff / 86400000);
                // Update the totalNights data property
                return nights;
            }
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
                path: "/api/booking",
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
        // async searchData() {
        //     this.$store.state.operations.loading = true;
        //     let currentPage = this.pagination ? this.pagination.current_page : 1;
        //     await this.$store.dispatch("operations/searchData", {
        //         path: "/api/purchases/search",
        //         term: this.query,
        //         currentPage: currentPage + "&perPage=" + this.perPage,
        //         startDate: this.dateRange.startDate,
        //         endDate: this.dateRange.endDate,
        //     });
        // },

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
                            path: "/api/booking/type/delete/",
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
<style>
.card-header {
    background: #eee;
    border: 0px;
}

.config_textbtn {
    background: #34ADC1;
    color: #fff;
}


.card-bottom-header .row.total_nights {
    text-align: center;
    background: #f3f3f3;
    margin: 0;
    padding: 5px 0px;
}

.card-bottom-header .row.total_nights .col-md-4 {
    padding: 0;
}

.card-bottom-header .row.total_nights .col-md-4.card_header_nights span {
    background: #9b9999;
    color: #fff;
    border-radius: 5px;
    padding: 1px 6px;
}
</style>
