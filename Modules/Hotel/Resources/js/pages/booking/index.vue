<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <CalenderView v-if="false"></CalenderView>
            <div class="col-lg-12" v-else>
                <div class="card" v-if="$can('hotel-booking-list') ||
                    $can('hotel-booking-create') ||
                    $can('hotel-booking-edit') ||
                    $can('hotel-booking-view') ||
                    $can('hotel-booking-delete')
                    ">
                    <div class="card-header">
                        <div class="row d-flex justify-content-end">
                            <div class="col-12 col-md-12 col-xl-4 text-right mb-2">
                                <div class="d-flex mt-3 ml-2" style="gap: 0.6rem; float:right">
                                    <toggle-button v-model="viewType" @change="toggleView" />
                                    <label for="card">{{ viewType ? 'Card view' : 'Table view' }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body position-relative mt-2 ledger-account">
                        <div id="tabs" class="justify-content-end">
                            <div class="tabs w-100 pa-2 d-flex justify-content-start ledger_acc_tab align-items-center col-12 py-3" v-if="!past">
                                <a v-if="bookingCounts.checkout == 0" v-on:click="activetab = 1" v-bind:class="[activetab === 1 ? 'active' : '']" class="outline_btn">Upcoming
                                    Booking<span>{{ bookingCounts.upcoming }}</span></a>
                                <a v-if="bookingCounts.checkout == 0" @click="activetab = 2"
                                    v-bind:class="[activetab === 2 ? 'active' : '']" class="outline_btn">Check-In<span>{{ bookingCounts.checkin }}</span></a>
                                <a v-if="bookingCounts.checkout == 0" @click="activetab = 3"
                                    v-bind:class="[activetab === 3 ? 'active' : '']" class="outline_btn">Occupied<span>{{ bookingCounts.occupied }}</span></a>
                                <a v-if="bookingCounts.checkout == 0" @click="activetab = 4"
                                    v-bind:class="[activetab === 4 ? 'active' : '']" class="outline_btn">Check-Out<span>{{ bookingCounts.checkout }}</span></a>
                                <a v-if="bookingCounts.checkout == 0" @click="activetab = 5"
                                    v-bind:class="[activetab === 5 ? 'active' : '']" class="outline_btn">Canceled<span>{{ bookingCounts.canceled }}</span></a>
                                <a v-if="bookingCounts.checkout == 0" @click="activetab = 6" v-bind:class="[activetab === 6 ? 'active' : '']" class="outline_btn">Room Status</a>
                                <router-link v-if="$can('hotel-booking-create') && bookingCounts.checkout == 0" :to="{ name: 'booking.create' }"
                                    class="btn btn-primary  ml-auto">
                                    {{ $t("common.create") }}
                                    <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                                </router-link>
                                <!--                                <router-link :to="{ name: 'booking.create.instant', params: {instant: 1} }" class="btn btn-primary text-white">-->
                                <!--                                    {{ $t("common.book_now") }}-->
                                <!--                                    <i class="fas fa-plus-circle d-none d-sm-inline-block" />-->
                                <!--                                </router-link>-->
                            </div>
                            <div v-if="activetab && activetab != 6" class="tabcontent">
                                <table-loading v-if="loading" />
                                <div id="printMe" class="table-responsive table-custom mt-3" v-else>
                                    
                                    <BookingCommon v-if="viewType" :hotelCategoryItems="ledgerAccountData"
                                        @refreshData="refreshData" :loading="loading" :activeTab="activetab" />
                                    <TableView v-else :past="past" :hotelCategoryItems="ledgerAccountData"
                                        @refreshData="refreshData" :loading="loading" :activeTab="activetab"
                                        @refresh="getData"></TableView>
                                </div>
                            </div>
                            <div v-if="activetab == 6">
                                <room_status />
                            </div>
                            <div v-show="ledgerAccountData.length <= 0 && viewType">
                                <EmptyTable />
                            </div>
                        </div>
                    </div>

                    <!-- /.card-body -->
                    <div class="dtable-footer card-footer">
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
                        <div v-if="activetab == 6 || $route.params.slug == 'past'">
                            <!-- pagination-start -->
                            <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
                                class="justify-flex-end" @paginate="paginate" />
                            <!-- pagination-end -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import room_status from './room-status.vue'
import Form from 'vform';
import BookingCommon from '../../components/BookingCoomon.vue';
import TableView from '../check-in/index.vue';
import Multiselect from 'vue-multiselect';
import axios from "axios";
import Cookies from "js-cookie";
import CalenderView from '../calender/index.vue';

export default {
    metaInfo() {
        return { title: this.$t("ledger_account.index.page_title") };
    },
    components: {
        BookingCommon,
        TableView,
        Multiselect,
        CalenderView,
        room_status
    },
    data: () => ({
        breadcrumbsCurrent: "common.reservation_details",
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
        showCancelModal: {
            title: 'Percentage',
            id: 2,
        },
        cancelTypeOptions: [
            {
                title: 'Fixed',
                id: 1,
            },
            {
                title: 'Percentage',
                id: 2,
            },
        ],
        cancelType: null,
        perPage: 10,
        cancelRate: 0,
        currentBooking: null,
        activetab: 1,
        viewType: true,
        form: new Form({
            booking_type: 2,
            search: '',
            perPage: '',
            current_page: 1,
            past: null
        }),
        bookingCounts: {},
        ledgerAccountData: [],
        dateRange: {
            startDate: null,
            endDate: null
        },
        query: '',
        pagination: {},
        past: false,
        hotels: [],
        currentSelectHotel: '',
    }),
    // Map Getters
    computed: {
        ...mapGetters("operations", ["items", "loading", "appInfo", "hotelCategoryItems", "hotelItems", "selectedHotel"]),
    },
    watch: {
        async activetab(currentValue, oldValue) {
            if (currentValue == 1) {
                this.form.booking_type = 2
            } else if (currentValue == 2) {
                this.form.booking_type = 3
            } else if (currentValue == 3) {
                this.form.booking_type = 5
            } else if (currentValue == 4) {
                this.form.booking_type = 4
            } else if (currentValue == 5) {
                this.form.booking_type = 1
            }
            this.getData()
        },
        selectedHotel: function () {
            this.getData();
        },
    },
    created() {
        this.past = !!this.$route?.params?.slug
        this.getData();
        this.getHotels();
        this.viewType = Cookies.get('viewType') && Cookies.get('viewType') === 'true'
    },
    methods: {
        handleHotelFilter() {
            this.getData();
        },
        refreshData() {
            this.getData();
        },
        toggleView(value) {
            Cookies.set('viewType', value.value, { expires: 365 })
        },
        // refresh table
        refreshTable() {
            this.query = "";
            this.dateRange.startDate = null;
            this.dateRange.endDate = null;

            this.getData()

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
            this.getData()
        },
        // get data
        async getData() {
            try {
                if (this.past) {
                    this.form.booking_type = 6
                    this.form.past = true
                }
                
                this.form.search = this.query
                this.form.perPage = this.perPage
                this.form.page = this.pagination?.current_page || 1
                const { data } = await this.form.post(window.location.origin + `/api/booking?type=${this.form.booking_type}&hotel_id=${this.currentSelectHotel?.id || ''}`);
                
                this.ledgerAccountData = data.data?.filter((value) => {
                    return this.past ? value.main_status == 4 || value.main_status == 1 : value.main_status != 4;
                });
                
                this.pagination = data.meta;
                this.bookingCounts = data.bookingCounts;
                if(!this.past && this.bookingCounts.checkout > 0){
                    this.activetab = 4;
                    Swal.fire({
                        title: 'Checkout Pending',
                        text: "Please Checkout All the Pending Rooms Which is ready for checkout, After the checkout you will able to create new booking",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: 'OK',
                    })
                }
                this.$store.state.operations.loading = false;
            } catch (e) {
                console.log(e)
            }

        },

        // Pagination
        async paginate(page) {
            this.pagination.current_page = page;
            this.getData()
        },
        // Reset pagination
        async resetPagination() {
            this.pagination.current_page = 1;
        },

        // Reload after search
        async reload() {
            this.query = "";
            await this.getData();
        },

        async getHotels() {
            await axios.get('/api/hotel/all').then((response) => {
                this.hotels = response?.data?.data;
            });
        },

        // print table
        async print() {
            await this.$htmlToPaper("printMe");
        },
    },
};
</script>
<style src="../../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
:deep(.multiselect__tags) {
    min-height: 38px !important;
    border: none !important;
    padding: 4px 40px 0 4px !important;
}

:deep(.multiselect__placeholder) {
    margin-bottom: 4px !important;
    padding-top: 4px !important;
}

:deep(.multiselect__single) {
    margin-bottom: 0px !important;
    margin-top: 4px !important;
}

:deep(.multiselect) {
    width: auto;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
    min-height: 38px !important;
}

.room-booking-form {
    background-color: #f2f4f5;
    border-radius: 6px;
}

.close-icon {
    width: 25px;
    display: flex;
    height: 25px;
    justify-content: center;
    align-items: center;
    font-size: 15px;
}

.plus-icon {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
}

.note-textarea:focus-visible {
    outline: none;
}

:deep(.mx-input) {
    height: 38px !important;
}

:deep(.multiselect__single) {
    text-overflow: ellipsis !important;
    overflow: hidden !important;
}

@media (min-width: 1200px) {
    .modal-dialog.modal-width {
        max-width: 1000px;
    }
}

.search-area {
    position: relative;
    width: 100%;
}

:deep(.search-btn) {
    left: 10px !important;
}

:deep(.dropdown-toggle::after) {
    display: none !important;
}

.suggestion-box {
    top: 100px;
    border-radius: 8px;
    box-shadow: 0px 3px 7px rgba(0, 0, 0, 0.2);
    max-height: 200px;
    overflow: auto;
}

.border-b-gray-1 {
    border-bottom: 1px solid #ced4da
}

.tabs {
    background-color: #fff;
    border-bottom: 1px solid beige;
}

#tabs {
    box-shadow: 1px 1px 10px lightgray;
}

.tabs a:not(.btn) {
    /* float: left;
    cursor: pointer;
    padding: 10px 20px;
    transition: background-color 0.2s;
    border-radius: 4px;
    font-weight: bold;

    color: #000000; */
    border: 1px solid var(--borderColor);
    margin-right: 10px;
    padding: 5px 25px;
    cursor: pointer;
    border-radius: 5px;
    color: var(--textColor);
}

/*.tabs a:not(.btn):hover {
    border-color: var(--primaryColor);
}*/

.ledger-account .tabs a.active {
    border-radius: 5px;
    cursor: pointer;
    hyphenate-character: revert;

}

.ledger-account .tabs a {
    /* padding: 5px 25px; */
}

/* .ledger-account .tabs a:hover {
    background: var(--primaryColor);
    color: #fff;
} */

.ledger-account .tabs {
    /* padding-bottom: 20px;
    float: left; */

}

.ledger-account .tabs span {
    background: var(--transparent);
    margin-left: 5px;
    font-size: 10px;
    padding: 2px 4px;
    border-radius: 2px;
    color: var(--textColor);
}

.ledger-account .tabs .active span {
    background: var(--primaryColor);
    margin-left: 5px;
    font-size: 10px;
    padding: 2px 4px;
    border-radius: 2px;
    color: var(--primary-btn-color);
}

/* .ledger-account div#tabs a.btn.btn-primary {
    height: fit-content;
} */

.badge-hotel {
    font-size: 20px;
    font-weight: bold;
    font-family: cursive;
}</style>
