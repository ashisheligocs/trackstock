<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" />
        <!-- breadcrumbs end -->
        <div class="row card cust_info">
            <div class="col-lg-12 p-4 box-shadow">
                <div class="col-lg-12 mt-4 card-header border">
                    <h5>{{ isCustomer ? $t("booking.show.customer_information") : 'Agency information' }}</h5>
                </div>
                <div class="col-lg-12 d-flex flex-wrap p-3 border border-top-0 mb-4">
                    <div class="col-lg-3 col-sm-6">
                        <div>
                            <p class="mb-2 ">{{ $t("booking.show.name") }}</p>
                            <p class="booking-title" v-if="bookingForm.account_name">{{ bookingForm.account_name }}</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6" v-if="bookingForm.email">
                        <div>
                            <p class="mb-2 ">{{ $t("email") }}</p>
                            <p class="booking-title">{{ bookingForm.email }}</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6" v-if="bookingForm.address">
                        <div>
                            <p class="mb-2 ">{{ $t("common.address") }}</p>
                            <p class="booking-title">{{ bookingForm.address }}</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6" v-if="bookingForm.phone">
                        <div>
                            <p class="mb-2 ">{{ $t("common.phone") }}</p>
                            <p class="booking-title">{{ bookingForm.phone }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 card-header border">
                    <h5>{{ $t("booking.show.booking_information") }}</h5>
                </div>
                <div class="col-lg-12 d-flex flex-wrap p-3 border border-top-0 mb-4">
                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("booking.show.booking_number") }}</p>
                        <p class="booking-title" v-if="bookingForm.booking_number">{{ bookingForm.booking_number }}</p>
                        <p class="booking-title" v-else>Not available</p>
                    </div>
                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("common.check_in") }}</p>
                        <p class="booking-title" v-if="bookingForm.check_in_date">{{ bookingForm.check_in_date }}</p>
                        <p class="booking-title" v-else>Not available</p>
                    </div>
                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("common.check_out") }}</p>
                        <p class="booking-title" v-if="bookingForm.check_out_date">{{ bookingForm.check_out_date }}</p>
                        <p class="booking-title" v-else>Not available</p>
                    </div>
                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2">{{ $t("booking.show.arrival_from") }}</p>
                        <p class="booking-title" v-if="bookingForm.arrival_from">{{ bookingForm.arrival_from | capitalize }}</p>
                        <p class="booking-title" v-else>Not available</p>
                    </div>
                </div>
                <div class="col-lg-12 card-header border">
                    <h5>{{ $t("booking.show.payment_information") }}</h5>
                </div>
                <div class="col-lg-12 d-flex flex-wrap p-3 border border-top-0 mb-4">
                    <!-- <div class="mt-2 col-lg-3">
                        <p class="mb-2 booking-title">{{ $t("booking.show.total_comlementary_price") }}</p>
                        <p>0</p>
                    </div> -->
                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2">{{ $t("common.total_price") }}</p>
                        <p class="booking-title" v-if="bookingForm.total_price">{{ bookingForm.total_price | withCurrency }}</p>
                        <p class="booking-title" v-else>0</p>
                    </div>
                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("common.paid_amount") }}</p>
                        <p class="booking-title" v-if="bookingForm.paid_amount">{{ bookingForm.paid_amount | withCurrency }}</p>
                        <p class="booking-title" v-else>0</p>
                    </div>
                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("purchases.list.common.discount") }}</p>
                        <p class="booking-title" v-if="bookingForm.discount_amount">{{ bookingForm.discount_amount | withCurrency }}</p>
                        <p class="booking-title" v-else>0</p>
                    </div>
                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("purchases.list.common.pending") }}</p>
                        <p class="booking-title" v-if="bookingForm.due">{{ parseFloat(bookingForm.due) <= 1 ? 0 : bookingForm.due | withCurrency
                        }}</p>
                                <p class="booking-title" v-else>0</p>
                    </div>
                </div>

                <div class="col-lg-12 card-header border">
                    <h5>{{ $t("booking.show.room_information") }}</h5>
                </div>

                <div v-for="(item, index) in bookingForm.booking_details" :key="index" class="col-lg-12 d-flex flex-wrap border border-top-0">
                    <div class="col-lg-12 mt-2">
                        <h5 class="text-muted font-weight-bold">Room {{ index + 1 }} :-</h5>
                    </div>

                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("common.room_no") }}.</p>
                        <p class="booking-title" v-if="item?.room?.room_name">{{ item.room.room_name }}</p>
                        <p class="booking-title" v-else>Not available</p>
                    </div>

                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2">{{ $t("booking.show.room_type") }}</p>
                        <p class="booking-title">{{ item.room.roomcategory.room_category_name }}</p>
                    </div>

                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("booking.show.adults") }}</p>
                        <p class="booking-title" v-if="item.adult">{{ item.adult + item.extra_person }}</p>
                        <p class="booking-title" v-else>Not available</p>
                    </div>

                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("common.children") }}</p>
                        <p class="booking-title" v-if="item.children">{{ item.children }}</p>
                        <p class="booking-title" v-else>Not available</p>
                    </div>

                    <div class="mt-2 col-lg-3 col-sm-6">
                        <p class="mb-2 ">{{ $t("booking.show.room_rate") }}</p>
                        <p class="booking-title" v-if="item.room_rate">{{ item.room_rate }}</p>
                        <p class="booking-title" v-else>Not available</p>
                    </div>
                </div>


                <div v-if="restaurantOrderDetails" class="col-lg-12">
                    <h3>Orders</h3>
                </div>

                <div v-if="restaurantOrderDetails" class="card-body">
                    <table class="table border-0 rest_table">
                        <thead>
                            <tr class="border-0">
                                <th scope="col">Item</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Price</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody class="border-0">
                            <tr v-if="restaurantOrderDetails?.items" v-for="(item, index) in restaurantOrderDetails.items"
                                :key="item.name + index">
                                <td>{{ item.name }}</td>
                                <td>{{ item.quantity }}</td>
                                <td>{{ item.price | twoPoints }}</td>
                                <td>{{ item.total | twoPoints }}</td>
                            </tr>
                            <tr>
                                <td>Addon items</td>
                                <td></td>
                                <td></td>
                                <td>{{ (restaurantOrderDetails?.addon || 0) | twoPoints }}</td>
                            </tr>
                            <tr class="border-top-2">
                                <td>Subtotal</td>
                                <td></td>
                                <td></td>
                                <td>{{ (restaurantOrderDetails?.subtotal || 0) | twoPoints }}</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td></td>
                                <td></td>
                                <td>{{ (restaurantOrderDetails?.discount || 0) | twoPoints }}</td>
                            </tr>
                            <tr>
                                <td>Tax</td>
                                <td></td>
                                <td></td>
                                <td>{{ (restaurantOrderDetails?.tax || 0) | twoPoints }}</td>
                            </tr>
                            <tr class="border-top text-bold">
                                <td>Net total</td>
                                <td></td>
                                <td></td>
                                <td>{{ (restaurantOrderDetails?.total || 0) | withCurrency }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script>
import Form from 'vform'
import _ from 'lodash'

export default {
    metaInfo() {
        return { title: this.$t("booking.show.booking_information") };
    },
    data: () => ({
        breadcrumbsCurrent: "booking.show.booking_information",
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
        isCustomer: true,
        restaurantOrderDetails: null,
        bookingForm: {
            booking_number: '',
            arrival_from: '',
            check_in_date: '',
            check_out_date: '',
            paid_amount: '',
            discount_amount: '',
            total_price: '',
            account_name: '',
            email: '',
            address: '',
            phone: '',
            booking_details: []
        },
        form: new Form({

        })
    }),
    created() {
        this.getBookingType();
        this.getRestaurantOrder();
    },
    methods: {
        async getBookingType() {
            const { data } = await this.form.get(window.location.origin + '/api/booking/view/' + this.$route.params.slug)
            console.log(data, 'dta')
            this.bookingForm = _.cloneDeep(data.data);
            this.bookingForm.paid_amount = data.data.advance_amount + data.data.credit_amount
            this.bookingForm.due = _.cloneDeep(data.data.paid_amount)
            this.bookingForm.account_name = data.data?.customer?.name || data.data?.source?.name
            this.bookingForm.email = data.data?.customer?.email || data.data?.source?.email
            this.bookingForm.address = data.data?.customer?.address || data.data?.source?.email
            this.bookingForm.phone = data.data?.customer?.phone || data.data?.source?.phone
            this.isCustomer = !!data.data?.customer
        },

        async getRestaurantOrder() {
            try {
                const { data } = await this.form.get(window.location.origin + '/api/booking/restaurant-orders/' + this.$route.params.slug)
                console.log(data, 'res')
                this.restaurantOrderDetails = data || null;
            } catch (e) {
                console.log(e);
            }
        }
    },
};
</script>

<style scoped>
.box-shadow {
    border-radius: 6px;
    box-shadow: 0px 1px 6px rgba(0, 0, 0, 0.2);
}

.booking-title {
    font-size: 18px;
    font-weight: 500;
}
</style>