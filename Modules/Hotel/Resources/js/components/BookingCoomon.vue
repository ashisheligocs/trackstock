<template>
    <div class="row common-row">
        <div class="col-md-6 col-xl-3 reserve_detail_card" v-for="data in hotelCategoryItems" :key="data?.id">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6"><strong class="name_client"> {{ data?.customer_name || data?.source_name
                        }}</strong></div>
                        <div class="col-6">
                            <span v-html="data?.status" class="float-right"></span>
                        </div>
                        <div class="col-6">{{ data?.booking_number }}</div>
                        <div class="col-6 text-right">
                            <div class="badge-hotel float-right">
                                {{ data?.roomsNo }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-bottom-header">
                    <div class="row total_nights flex-nowrap">
                        <div class="col-md-4">{{ data?.check_in_date }}</div>
                        <div class="col-md-4 card_header_nights"><span>
                                {{ calculateNights(data?.check_in_date, data?.check_out_date) }}
                            </span>
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
                                        <i class="fas fa-male"></i> {{ data?.adult }}
                                    </span>
                                    <span class="p-0 badge fs-7 me-1 me-1 text-dark" title="Children">
                                        <i class="fas fa-child"></i> {{ data?.children }}</span>
                                    <span class="p-0 badge fs-7 me-1 me-1 text-dark" title="Infant">
                                        <i class="fas fa-baby"></i> {{ data?.infant }}
                                    </span>
                                </div>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <strong>{{ $t("common.phone") }}</strong>
                            <span class="float-right">{{ data?.customer_phone || data.source_phone }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Total</strong>
                            <span class="float-right">{{ data?.total_price | indformate }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>{{ $t("common.paid_amount") }}</strong>
                            <span class="float-right">{{ data?.total_price - data?.paid_amount - (data?.discount || 0) |
                                indformate }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>{{ $t('common.discount') }}</strong>
                            <span class="float-right">{{ (data?.discount || 0) | indformate }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>{{ $t("common.due_amount") }}</strong>
                            <span class="float-right"><span v-html="data?.dueAmount"></span></span>
                        </li>
                    </ul>


                    <router-link v-if="$can('hotel-booking-edit') && (activeTab === 4 || activeTab === 3) && activeTab != 5 && data.main_status != 4" :to="{
                        name: 'check-out',
                        params: { slug: data.id },
                    }" class="btn-block btn btn-secondary">

                        {{ $t("common.check_out") }}
                    </router-link>

                    <router-link v-else-if="data.main_status != 4 && (data?.isChecking || activeTab != 3) && activeTab != 5" :to="{
                        name: 'booking.create.check-in',
                        params: { slug: data.id },
                       }" class="btn-block btn btn-secondary">
                        {{ $t("common.check_in") }}
                    </router-link>
                    <button class="btn-block btn bg-success"
                        v-else-if="$can('hotel-booking-edit') && activeTab != 3 && activeTab != 5 && data.main_status != 4"
                        disabled>{{ $t("common.check_in") }} </button>
                    <div v-if="$can('hotel-booking-edit') && activeTab != 5" class="d-flex" style="gap: 4px"
                        :class="activeTab == 3 || activeTab == 4 ? 'mt-2' : ''">
                        <router-link v-if="$can('hotel-booking-edit') && (activeTab != 4 && data.main_status != 4)" :to="{
                            name: 'booking.edit',
                            params: { slug: data.id },
                        }" class="btn-block btn btn-outline-primary" style="margin-top: 8px">
                            {{ $t('common.edit') }}
                        </router-link>
                        <router-link v-if="$can('hotel-booking-view')" :to="{
                            name: 'booking.show',
                            params: { slug: data.id },
                        }" class="btn-block btn btn-outline-primary">
                                {{ $t('common.view') }}
                            </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        hotelCategoryItems: {
            type: Array,
            required: false
        },
        loading: {
            type: Boolean,
            required: false
        },
        activeTab: {
            type: Number,
            default: 1
        }
    },
    methods: {
        /*** calcuate night  */
        calculateNights(startDate, endDate) {
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const timeDiff = end.getTime() - start.getTime();
                const nights = Math.floor(timeDiff / 86400000);
                return nights;
            }
        },
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
                        .dispatch("operations/getDeleteData", {
                            path: "/api/account/ledger/delete/",
                            slug: slug,
                        })
                        .then((response) => {
                            if (response === true) {
                                Swal.fire(
                                    this.$t("common.deleted"),
                                    this.$t("common.delete_success"),
                                    "success"
                                );
                                this.$emit("refreshData")
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
}
</script>
<style>
.card {
    box-shadow: 0 2px 13px rgba(0, 0.05, 0.05, 0.05) !important;
}

.card-header {
    background: #eee;
    border: 0px;
}

.common-row {
    background-color: #fff;
    padding: 10px;
    margin: 0px !important;
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
    border-radius: 0px 0px 5px 5px;
    padding: 1px 6px;
}

div#printMe {
    border: 0px;
}

div#printMe .card-body.box-profile {
    padding-top: 0;
}

div#printMe .card-body.box-profile ul li.list-group-item:first-child {
    border-top: 0px;
}</style>
