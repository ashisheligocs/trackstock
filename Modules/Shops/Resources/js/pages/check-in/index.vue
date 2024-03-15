<template>
    <div class="mb-10 d-flex justify-content-center">
        <!-- breadcrumbs end -->
        <div class="row w-100">
            <div class="col-lg-12 p-0">
                <div class="card mb-0" v-if="$can('hotel-booking-list') ||
                    $can('hotel-booking-create') ||
                    $can('hotel-booking-edit') ||
                    $can('hotel-booking-view') ||
                    $can('hotel-booking-delete') ||
                    $can('hotel-booking-print') ||
                    $can('hotel-booking-check_in')
                    ">
                    <div class="card-body position-relative">
                        <table-loading v-show="loading" />
                        <div id="printMe" class="table-responsive table-custom">
                            <table class="table border">
                                <thead>
                                    <tr>
                                        <th>{{ $t("common.s_no") }}</th>
                                        <th>{{ $t("booking.show.booking_number") }}</th>
                                        <th>{{ $t("booking.show.room_number") }}</th>
                                        <th>{{ $t("common.name") }}</th>
                                        <th>{{ $t("common.check_in") }} - {{ $t("common.check_out") }}</th>
                                        <th>{{ $t("common.paid_amount") }}</th>
                                        <th v-if="($can('hotel-booking-edit') ||
                                            $can('client-view') ||
                                            $can('hotel-booking-delete')) && activeTab != 5
                                            " class="text-right no-print">
                                            {{ $t("common.action") }}
                                        </th>
                                    </tr>
                                </thead>
                                <!-- {{ hotelCategoryItems }} -->
                                <tbody>
                                    <tr v-for="(data, index) in hotelCategoryItems" :key="index">
                                        <td>
                                            <span>{{ index + 1 }}</span>
                                        </td>
                                        <td>{{ data?.booking_number }}
                                            <br v-if="data?.source_name" />
                                            <span>{{ data?.source_name ? `By ${data?.source_name}` : '' }}</span>
                                            <br v-if="data.purpose" />
                                            <span v-if="data.purpose" class="text-bold">GRN:</span>{{ data.purpose }}
                                        </td>
                                        <td>{{ data?.roomsNo }}
                                            <br />
                                            <span v-html="data?.status" style="float: left !important;"></span>
                                        </td>
                                        <td>{{ data?.customer_name }} <br />{{ data?.customer_phone }}</td>
                                        <td>{{ data?.check_in_date }} <br /> {{ data?.check_out_date }}</td>
                                        <td class="d-flex flex-column text-right">
                                            <span>Total: {{ data?.total_price }}</span>
                                            <span v-if="data?.discount">Discount: {{ (data?.discount || 0) | indformate
                                            }}</span>
                                            <span>Paid: {{ data?.total_price - data?.paid_amount - (data?.discount || 0)
                                            }}</span>
                                            <span> Due:<span v-html="data?.dueAmount"></span></span>
                                        </td>

                                        <td v-if="($can('hotel-booking-edit') ||
                                            $can('hotel-booking-view') ||
                                            $can('hotel-booking-delete') ||
                                            $can('hotel-booking-print') ||
                                            $can('hotel-booking-check_in'))
                                            && activeTab != 5" class="text-left no-print">
                                            <div class="btn-group d-block">
                                                <router-link
                                                    v-if="$can('hotel-booking-edit') && activeTab != 4 && (data.main_status != 4 && data.main_status != 1)"
                                                    v-tooltip="$t('common.edit')" :to="{
                                                        name: 'booking.edit',
                                                        params: { slug: data.id },
                                                    }" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit " />
                                                </router-link>
                                                <!-- <router-link v-if="$can('hotel-booking-view')" v-tooltip="$t('common.view')"
                                                    :to="{
                                                        name: 'booking.show',
                                                        params: { slug: data.id },
                                                    }" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye" />
                                                </router-link> -->

                                                <!-- <a v-if="!past && $can('hotel-booking-view') && data.paid_amount > 0"
                                                    v-tooltip="$t('common.add_payment')" class="btn btn-secondary btn-sm"
                                                    @click.prevent="openAddPaymentModal(data)">
                                                    <i class="fas fa-money-check-alt" />
                                                </a> -->
                                                <a v-tooltip="'Over View'" class="btn btn-info btn-sm"
                                                    @click.prevent="showModal = true; currentBooking = data">
                                                    <i class="fas fa-eye" />
                                                </a>
                                                <a v-if="!past && $can('hotel-booking-delete')"
                                                    v-tooltip="$t('common.delete')" href="#" class="btn btn-danger btn-sm"
                                                    @click="deleteData(data.id)">
                                                    <i class="fas fa-trash" />
                                                </a>
                                            </div>
                                            <div class="btn-group mt-1 d-block"
                                                v-if="data.main_status != 4 && activeTab != 5">
                                                <router-link
                                                    v-if="(activeTab === 4 || activeTab === 3) && data.main_status != 4 && activeTab != 1"
                                                    v-tooltip="$t('common.check_out')" :to="{
                                                        name: 'check-out',
                                                        params: { slug: data.id },
                                                    }" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-arrow-right" />
                                                </router-link>
                                                <router-link
                                                    v-else-if="(data?.isChecking || activeTab != 3) && activeTab != 1"
                                                    v-tooltip="$t('common.check_in')" :to="{
                                                        name: 'booking.create.check-in',
                                                        params: { slug: data.id },
                                                    }" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-arrow-left" />
                                                </router-link>
                                                <a v-if="$can('hotel-booking-delete') && (activeTab == 1 || activeTab == 2 || activeTab == 4) && data.main_status != 1"
                                                    v-tooltip="$t('common.cancel')" href="#" class="btn btn-danger btn-sm"
                                                    @click.prevent="openCancelBookingModal(data)">
                                                    <i class="fas fa-ban" />
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-show="!loading && !hotelCategoryItems?.length">
                                        <td colspan="12">
                                            <EmptyTable />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <VModal v-if="showCancelModal" v-model="showCancelModal" @close="showCancelModal = false">

            <h3 slot="title" class="text-center">Are you sure?</h3>

            <div class="mt-1">
                <p class="text-center">Please enter cancellation charges</p>
                <multiselect v-model="cancelType" :options="cancelTypeOptions" :show-labels="false" tag-placeholder=""
                    placeholder="Search a Discount room rate" class="form-control" label="title" track-by="title">
                </multiselect>
            </div>
            <div class="mt-4">
                <div>
                    <input id="discount" v-model="cancelRate" type="text" class="form-control" name="cancel-rate"
                        :placeholder="$t('common.discount_placeholder')" />
                </div>
            </div>
            <div class="mt-4">
                <v-select v-model="accountFrom" :options="accounts" label="label" name="account"
                    :placeholder="$t('common.account_placeholder')" />
            </div>
            <div class="mt-4">
                <div class="mt-2 d-flex justify-content-between border-bottom">
                    <span>Commision Amount</span>
                    <span class="font-weight-bold">{{ currentBooking.advance_amount }}</span>
                </div>
                <div class="mt-2 d-flex justify-content-between border-bottom">
                    <span>Advance amount</span>
                    <span class="font-weight-bold">{{ currentBooking.advance_amount }}</span>
                </div>
                <div class="mt-2 d-flex justify-content-between border-bottom">
                    <span>Cancellation amount</span>
                    <span class="font-weight-bold">{{ cancelAmount }}</span>
                </div>
                <div class="mt-2 d-flex justify-content-between border-bottom">
                    <span>Refund amount</span>
                    <span class="font-weight-bold">{{ refundAmount }}</span>
                </div>

            </div>

            <div slot="modal-footer">
                <button @click="cancelBooking(currentBooking?.id)" class="btn btn-primary">Cancel Booking</button>
                <!-- <button @click="showCancelModal = false" class="btn btn-primary">Abort</button> -->
            </div>
        </VModal>


        <!-- modal for add paymnet  -->

        <Modal v-if="showAddPaymentModal" v-model="showAddPaymentModal" @close="showAddPaymentModal = false">
            <h5 slot="header" class="d-flex justify-content-around">
                <p>

                    {{ $t("payments.common.booking_payment") }} : {{ currentBooking.booking_number }}
                </p>
                <!-- {{ form.selectedInvoice.invoiceNo | withPrefix(prefix) }} -->
                <button class="modal-default-button btn btn-secondary" @click="showAddPaymentModal = false">
                    <i class="fas fa-times"></i>
                    <!-- {{ $t("common.close") }} -->
                </button>
            </h5>
            <div slot="body" class="row">
                <form role="form" @submit.prevent="savePayment" @keydown="form.onKeydown($event)" class="w-100">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="clientInvoiceTotal">{{ $t('common.net_payable_amount') }}</label>
                            <input type="text" class="form-control" readonly v-model="form.netTotal" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="clientInvoiceDue">{{ $t('common.pending_amount') }}</label>
                            <input type="text" class="form-control" readonly v-model="form.pendingAmount" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="paidAmount">{{ $t("common.paid_amount") }}</label>
                            <input v-model="form.paidAmount" type="number" step="any" class="form-control"
                                :placeholder="$t('common.paid_amount_placeholder')" required min="1" @input="checkLimit" />
                        </div>
                        <div class="form-group col-md-8">
                            <label for="account">{{ $t("common.account") }}
                                <span class="required">*</span></label>
                            <v-select :class="{ 'is-invalid': form.errors.has('account') }" v-model="form.account"
                                :options="accounts" label="label" name="account"
                                :placeholder="$t('common.account_placeholder')" />
                            <has-error :form="form" field="account" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6" v-if="form.account.bankName !== 'Cash'">
                            <label for="bank_charges1" class="font-weight-bold">{{
                                $t('common.bank_charges') }}</label>
                            <input id="bank_charges1" v-model="form.bankCharges" @input="bankChargeLimit" type="text"
                                class="form-control" name="bookingtypetitle"
                                :placeholder="$t('common.pending_amount_placeholder')" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="paymentDate">{{ $t("common.payment_date") }}</label>
                            <input v-model="form.paymentDate" :class="{ 'is-invalid': form.errors.has('paymentDate') }"
                                id="paymentDate" type="date" class="form-control" name="paymentDate" />
                            <has-error :form="form" field="paymentDate" />
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="note">{{ $t("common.note") }}</label>
                        <textarea id="note" v-model="form.note" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('note') }"
                            :placeholder="$t('common.note_placeholder')" />
                        <has-error :form="form" field="note" />
                    </div>
                </form>

            </div>
            <template #modal-footer>
                <div class="mt-1">
                    <button class="modal-default-button btn btn-primary" @click="savePayment">
                        <i class="fas fa-save" /> {{ $t("common.save") }}
                    </button>

                </div>
            </template>
        </Modal>

        <!-- Start OverView Popup -->
        <vue-final-modal v-if="showModal" v-model="showModal" @close="showModal = false" :lock-scroll="true"
            classes="z-50 custom-modal-dialog overview" content-class="modal-content overview">
            <span class="modal-header">
                <div class="d-flex flex-column">
                    <h4 class="text-left m-0 text-capitalize">{{ currentBooking.customer_name }}</h4>
                    <span class="text-left">{{ currentBooking.customer_phone }}</span>
                </div>
                <button @click="showModal = false; display = false;" type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </span>

            <div class="modal-body">
                <div class="">
                    <router-link v-if="$can('hotel-booking-edit') && currentBooking.main_status != 4"
                        v-tooltip="$t('common.edit')" :to="{
                            name: 'booking.edit',
                            params: { slug: currentBooking.id },
                        }">
                        <button class="btn btn-warning mr-2">Edit</button>
                    </router-link>
                    <router-link v-if="$can('hotel-booking-view')" v-tooltip="$t('common.view')" :to="{
                        name: 'booking.show',
                        params: { slug: currentBooking.id },
                    }">
                        <button class="btn btn-primary mr-2">View</button>
                    </router-link>
                    <button v-if="currentBooking.main_status != 4" class="btn btn-secondary mr-2 moreOptionBtn"
                        @click="display = !display">More Option</button>
                    <div class="d-flex flex-column moreoptionBox shadow" v-if="display">
                        <div class="flex px-2" @click="openAddPaymentModal(currentBooking)">
                            <i class="fas fa-solid fa-money-bill"></i>
                            <label class="">Add Payment</label>
                        </div>
                        <div class="flex px-2" @click="openAmendStayModal(currentBooking)">
                            <i class="fas fa-calendar-check"></i>
                            <label class="">Amend Stay</label>
                        </div>
                        <div class="flex px-2" @click="openAdditionalServiceModal(currentBooking)">
                            <i class="fas fa-wrench"></i>
                            <label class="">Additional Service</label>
                        </div>
                        <div class="flex px-2" @click="openRoomMoveModal(currentBooking)">
                            <i class="fas fa-bed"></i>
                            <label class="">Room Move</label>
                        </div>
                        <div class="flex px-2">
                            <i class="fas fa-bed"></i>
                            <label class="">Exchange Room</label>
                        </div>
                        <div class="flex px-2">
                            <i class="fas fa-bed"></i>
                            <label class="">Stop Room Move</label>
                        </div>
                        <div class="flex px-2">
                            <i class="fas fa-list"></i>
                            <label class="">Inclusion List</label>
                        </div>
                        <div class="flex px-2">
                            <i class="fas fa-calendar-check"></i>
                            <label class="">Void Transaction</label>
                        </div>
                    </div>
                </div>
                <div class="container mt-3">
                    <div class="row justify-content-between border-bottom mb-2">
                        <div class="d-flex flex-column info_mod">
                            <span class="heading">Booking Number</span>
                            <label class="">{{ currentBooking.booking_number }}</label>
                        </div>
                        <div class="d-flex flex-column text-right info_mod">
                            <span class="heading">Status</span>
                            <label class=""><span v-html="currentBooking.status"
                                    style="float: left !important;"></span></label>
                        </div>
                    </div>
                    <div class="row justify-content-between border-bottom mb-2">
                        <div class="d-flex flex-column info_mod">
                            <span class="heading">Arrival Date</span>
                            <label class="">{{ currentBooking.check_in_date }}</label>
                        </div>
                        <div class="d-flex flex-column text-right info_mod">
                            <span class="heading">Departure Date</span>
                            <label class="">{{ currentBooking.check_out_date }}</label>
                        </div>
                    </div>
                    <div class="row justify-content-between border-bottom mb-2">
                        <div class="d-flex flex-column info_mod">
                            <span class="heading">Room Number</span>
                            <label class="">{{ currentBooking.roomsNo }}</label>
                        </div>
                        <div class="d-flex flex-column text-right info_mod">
                            <span class="heading">Room Type</span>
                            <label class="">{{ currentBooking.BookingDetails[0]?.room?.roomcategory?.room_category_name
                            }}</label>
                        </div>
                    </div>
                    <div class="row justify-content-between border-bottom mb-2">
                        <div class="d-flex flex-column info_mod">
                            <span class="heading">Person</span>
                            <label class="">{{ currentBooking.adult }}</label>
                        </div>
                        <div class="d-flex flex-column text-right info_mod">
                            <span class="heading">Rate</span>
                            <label class="">{{ currentBooking.total_price | twoPoints }}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex flex-column info_mod">
                            <span class="heading">Due</span>
                            <label class=""><span v-html="currentBooking.dueAmount"></span></label>
                        </div>
                    </div>
                </div>
                <!-- <div class="d-flex w-full my-4 ml-2">
                    <div class="w-50">
                        <div class="d-flex flex-column">
                            <span class="heading">Booking Number</span>
                            <label class="">{{ currentBooking.booking_number }}</label>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="heading">Arrival Date</span>
                            <label class="">{{ currentBooking.check_in_date }}</label>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="heading">Room Number</span>
                            <label class="">{{ currentBooking.roomsNo }}</label>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="heading">Person</span>
                            <label class="">{{ currentBooking.adult }}</label>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="heading">Due</span>
                            <label class=""><span v-html="currentBooking.dueAmount"></span></label>
                        </div>

                    </div>
                    <div class="w-50 ml-2">
                        <div class="d-flex flex-column">
                            <span class="heading">Status</span>
                            <label class=""><span v-html="currentBooking.status"
                                    style="float: left !important;"></span></label>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="heading">Departure Date</span>
                            <label class="">{{ currentBooking.check_out_date }}</label>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="heading">Room Type</span>
                            <label class="">{{ currentBooking.BookingDetails[0]?.room?.roomcategory?.room_category_name
                            }}</label>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="heading">Rate</span>
                            <label class="">{{ currentBooking.total_price | twoPoints }}</label>
                        </div>
                    </div>
                </div> -->
            </div>
            <!-- <div class="modal-footer">
                Modal Footer
            </div> -->
        </vue-final-modal>
        <!-- End OverView Popup -->


        <!-- Start Room Amend Stay -->

        <VModal v-if="showAmendStayModal" v-model="showAmendStayModal" @close="showAmendStayModal = false">
            <h5 slot="title">
                {{ $t("common.booking_info") }} :- {{ currentBooking.customer_name }} : {{ currentBooking.booking_number }}
            </h5>
            <div class="row">
                <form role="form" @submit.prevent="saveAmendStay" @keydown="amendForm.onKeydown($event)" class="w-100">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="checkInDate">{{ $t("common.check_in_date") }}</label>
                            <input v-model="amendForm.check_in_date"
                                :class="{ 'is-invalid': amendForm.errors.has('checkInDate') }" id="checkInDate" type="date"
                                class="form-control" name="checkInDate" readonly />
                        </div>

                        <div class="form-group col-md-6">
                            <label for="checkOutDate">{{ $t("common.check_out_date") }}</label>
                            <input v-model="amendForm.check_out_date" @input="updateRoomRate"
                                :class="{ 'is-invalid': amendForm.errors.has('checkOutDate') }" id="checkOutDate"
                                type="date" class="form-control" name="checkOutDate" :min="minDate" />
                            <has-error :form="amendForm" field="checkOutDate" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="roomRate">{{ $t('common.room_rate') }}</label>
                            <input type="text" class="form-control" readonly v-model="amendForm.roomRate" />
                            <has-error :form="amendForm" field="roomRate" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="additional_charges">{{ $t('common.total_tax') }}</label>
                            <p v-for="(gstApplicable, i) in applicableGst" :key="i">
                                {{ gstApplicable.tax_name.name }} : {{ (((parseFloat(amendForm.roomRate) *
                                    parseFloat(gstApplicable.tax_name.rate)) / 100)).toFixed(2) }}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="noOfRooms">Total Rooms</label>
                            <p>{{ noOfRoom }}</p>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="additional_charges">Total Days</label>
                            <p>{{ noOfDays }}</p>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="additional_charges">Facility Charge</label>
                            <p>{{ amendForm.totalFacilityCharge }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="noOfRooms">Extra Bed Charge</label>
                            <p>{{ amendForm.totalExtraBedCharge }}</p>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="additional_charges">Extra Person Charge</label>
                            <p>{{ amendForm.totalExtraPersonCharge }}</p>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="additional_charges">Meal Charge</label>
                            <p>{{ amendForm.totalMealCharge }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="netTotal">{{ $t('common.net_payable_amount') }}</label>
                            <input type="text" class="form-control" readonly v-model="amendForm.netTotal" />
                            <has-error :form="amendForm" field="netTotal" />
                        </div>
                        <div class="form-group col-md-6">
                            <input id="include_gst" type="checkbox" @change="setNewPrices" v-model="amendForm.tax_included">
                            <label for="include_gst">Inclusive GST</label>
                        </div>
                    </div>

                </form>
            </div>
            <div slot="modal-footer">
                <div class="mt-1">
                    <button class="modal-default-button btn btn-primary" @click="saveAmendStay">
                        <i class="fas fa-save" /> {{ $t("common.save") }}
                    </button>
                  
                </div>
            </div>
        </VModal>
        <!-- End Room Amend Stay -->

        <!-- Start Additional Service -->

        <VModal v-if="showAdditionalServiceModal" v-model="showAdditionalServiceModal"
            @close="showAdditionalServiceModal = false">
            <h5 slot="title">
                Additional Service For :- {{ currentBooking.customer_name }} : {{ currentBooking.booking_number }}
            </h5>
            <div class="row">
                <form role="form" @submit.prevent="saveAdditionalService" @keydown="amendForm.onKeydown($event)"
                    class="w-100">
                    <div class="row">

                        <div class="form-group col-md-12">
                            <multiselect @select="changeRoomService" v-model="additionalForm.booking_id"
                                :options="currentBooking.BookingDetails" :show-labels="false"
                                tag-placeholder="Select Room no" placeholder="Select Room no" class="form-control"
                                :custom-label="roomNameCustomLable" label="room" track-by="room"
                                :class="{ 'is-invalid': form.errors.has('account') }">
                            </multiselect>
                            <!-- <v-select :class="{ 'is-invalid': form.errors.has('account') }"
                                v-model="additionalForm.booking_id" :options="currentBooking.BookingDetails"
                                @input="changeRoomService" label="room_no" name="room_no" :placeholder="'Select Room no'" /> -->
                        </div>
                    </div>
                    <div class="row m-auto w-100">
                        <multiselect
                            @remove="roomFacilitySelect(additionalService, additionalService.room_facility_id, 'remove')"
                            v-model="additionalService.room_facility_id" :options="additionalService" :multiple="true"
                            @select="roomFacilitySelect(additionalService, additionalService.room_facility_id, 'select')"
                            :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Add services"
                            class="form-control w-100" label="facility_name" track-by="facility_name"
                            :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                    </div>

                    <div class="row mt-2" v-if="selectedAdditionalService.length">

                        <div class="table-responsive table-custom w-95 m-auto">
                            <table class="table table-hover table-sm text-center">
                                <thead>
                                    <tr>
                                        <th>{{ $t("common.service_name") }}</th>
                                        <th>{{ $t("common.price") }}</th>
                                        <th>{{ $t("common.qty") }}</th>
                                        <th> GST </th>
                                        <th>{{ $t("common.subtotal") }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(services, index) in selectedAdditionalService" :key="index">
                                        <td>{{ services.facility_name }}</td>
                                        <td><input @input="changePriceQty" type="number" v-model="services.price"
                                                style="width:70px;" /></td>
                                        <td><input @input="changePriceQty" type="number" v-model="services.quantity"
                                                style="width:70px;"></td>
                                        <td>{{ services.sumOfTax }}</td>
                                        <th>{{ Number(parseFloat(services.price * services.quantity) +
                                            (parseFloat(services.price * services.quantity) * parseFloat(services.sumOfTax)
                                                / 100)).toFixed(2) }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="netTotal">{{ $t('common.net_payable_amount') }}</label>
                            <input type="text" class="form-control" readonly v-model="additionalForm.netTotal" />
                            <has-error :form="amendForm" field="netTotal" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="netTotal">{{ $t('common.gst') }}</label>
                            <input type="text" class="form-control" readonly v-model="additionalForm.totalGST" />
                            <has-error :form="amendForm" field="netTotal" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <input id="include_gst" type="checkbox" @change="setNewPricesServices"
                                v-model="additionalForm.tax_included">
                            <label for="include_gst">Inclusive GST</label>
                        </div>
                    </div>
                </form>
            </div>
            <div slot="modal-footer">
                <div class="mt-1">
                    <button class="modal-default-button btn btn-primary" @click="saveAdditionalService">
                        <i class="fas fa-save" /> {{ $t("common.save") }}
                    </button>
                    <button class="modal-default-button btn btn-secondary" @click="showAdditionalServiceModal = false">
                        <i class="fas fa-times"></i>
                        {{ $t("common.close") }}
                    </button>
                </div>
            </div>
        </VModal>
        <!-- End Additional Service -->

        <!-- Start Room Move -->
        <VModal v-if="showRoomMoveModal" v-model="showRoomMoveModal" @close="showRoomMoveModal = false">

            <h5 slot="title">
                Room Move For :- {{ currentBooking.customer_name }} : {{ currentBooking.booking_number }}
            </h5>
            <div class="row">
                <form role="form" @submit.prevent="saveRoomMove" @keydown="amendForm.onKeydown($event)" class="w-100">
                    <div class="row">

                        <div class="form-group col-md-12">
                            <!-- <v-select :class="{ 'is-invalid': form.errors.has('account') }"
                                v-model="roomMoveForm.booking_id" :options="currentBooking.BookingDetails"
                               label="room_no" name="room_no" :placeholder="'Select Room no'" /> -->
                            <!-- {{ currentBooking.BookingDetails }} -->
                            <multiselect v-model="roomMoveForm.booking_id" :options="currentBooking.BookingDetails"
                                :show-labels="false" tag-placeholder="Select Room no" placeholder="Select Room no"
                                class="form-control" :custom-label="roomNameCustomLable" label="room" track-by="room"
                                :class="{ 'is-invalid': form.errors.has('account') }">
                            </multiselect>
                        </div>
                    </div>
                    <p>Change To</p>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="room_hotel_cat_lsit" class="d-block">{{
                                $t('sidebar.room_hotel_cat_lsit') }}
                                <span class="required">*</span></label>

                            <multiselect @select="getRoomForCategory()" v-model="roomMoveForm.room_cat_id"
                                :options="roomCategory" :show-labels="false" tag-placeholder="Add this as new tag"
                                placeholder="Search a Room category" class="form-control" label="room_category_name"
                                track-by="room_category_name" :class="{ 'is-invalid': form.errors.has('room_booking_id') }">
                            </multiselect>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="room_number" class="d-block">{{ $t('common.room_number') }}
                                <span class="required">*</span></label>
                            <multiselect @select="getSelectedRoomPrice()" v-model="roomMoveForm.room_id" :options="roomNo"
                                :show-labels="false" tag-placeholder="Add this as new tag"
                                placeholder="Search a Room number" class="form-control" label="room_name"
                                track-by="room_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }">
                            </multiselect>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="room_price">Room Price</label>
                            <input v-model="roomMoveForm.price" @input="setRoomNewPrice"
                                :class="{ 'is-invalid': roomMoveForm.errors.has('room_price') }" id="room_price" type="text"
                                class="form-control" name="room_price" />
                            <has-error :form="roomMoveForm" field="room_price" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="additional_charges">Total Days</label>
                            <p>{{ noOfDays }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="total_room_price">Total Price</label>
                            <input v-model="roomMoveForm.totalRoomRate"
                                :class="{ 'is-invalid': roomMoveForm.errors.has('total_room_price') }" id="total_room_price"
                                type="text" class="form-control" name="total_room_price" readonly />
                            <has-error :form="roomMoveForm" field="total_room_price" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="total_room_gst">Total GST</label>
                            <input v-model="roomMoveForm.totalRoomGST"
                                :class="{ 'is-invalid': roomMoveForm.errors.has('total_room_gst') }" id="total_room_gst"
                                type="text" class="form-control" name="total_room_gst" readonly />
                            <has-error :form="roomMoveForm" field="total_room_gst" />
                        </div>
                    </div>
                </form>
            </div>
            <div slot="modal-footer">
                <div class="mt-1">
                    <button class="modal-default-button btn btn-primary" @click="saveRoomMove">
                        <i class="fas fa-save" /> {{ $t("common.save") }}
                    </button>
                    <button class="modal-default-button btn btn-secondary" @click="showRoomMoveModal = false">
                        <i class="fas fa-times"></i>
                        {{ $t("common.close") }}
                    </button>
                </div>
            </div>
        </VModal>
        <!-- End Room Move -->

    </div>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";
import Multiselect from 'vue-multiselect';
import DatePicker from "vue2-datepicker";
import moment from 'moment';
import axios from "axios";

export default {
    metaInfo() {
        return { title: this.$t("check_in.index.page_title") };
    },
    components: {
        DatePicker,
        Multiselect,
    },
    props: {
        activeTab: {
            type: Number,
            default: 1
        },
        hotelCategoryItems: {
            type: Array,
            required: false
        },
        loading: {
            type: Boolean,
            required: false
        },
        past: {
            type: Boolean,
            default: false
        }
    },
    created() {
        this.getAccounts();
        this.getHotelDataList();
    },
    data: () => {
        return {
            display: false,
            showCancelModal: false,
            showAddPaymentModal: false,
            showAdditionalServiceModal: false,
            showRoomMoveModal: false,
            showModal: false,
            showAmendStayModal: false,
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
            cancelType: {
                title: 'Fixed',
                id: 1,
            },
            cancelRate: 0,
            currentBooking: null,
            accounts: [],
            accountFrom: "",
            minDate: new Date().toISOString().slice(0, 10),
            form: new Form({
                paidAmount: 0,
                bankCharges: 0,
                pendingAmount: 0,
                booking_id: "",
                paymentDate: new Date().toISOString().slice(0, 10),
                date: new Date().toISOString().slice(0, 10),
                account: "",
                note: "",
                netTotal: 0,
                status: 1,
                bank_only: 0,
            }),
            amendForm: new Form({
                booking_id: "",
                check_in_date: "",
                check_out_date: "",
                roomRate: 0,
                netTotal: 0,
                totalGST: 0,
                tax_included: "",
                final_gst_rates: {},
                noOfDays: 0,
                noOfRoom: 0,
                totalFacilityCharge: 0,
                totalExtraBedCharge: 0,
                totalExtraPersonCharge: 0,
                totalMealCharge: 0,
                individualmodifiedRate: [],
                individualroomRate: [],
                individualroomTax: [],
                individualroomExtraPersonTax: [],
                individualroomExtraBedTax: [],
                individualroomExtraPersonPrice: [],
                individualroomExtraBedPrice: [],
                individualroomMealTax: [],
            }),
            allVatRates: [],
            applicableGst: [],
            gstPrice: [],
            finalRoomRate: 0,
            noOfRoom: 0,
            noOfDays: 0,
            additionalForm: new Form({
                booking_id: '',
                netTotal: 0,
                totalGST: 0,
                tax_included: "",
                selectedService: [],
                final_gst_rates: {},
            }),
            facilities_list_form: new Form({
                hotel_id: '',
            }),
            roomMoveForm: new Form({
                booking_id: '',
                room_cat_id: '',
                room_id: '',
                hotel_id: '',
                check_in_date: '',
                price: 0,
                totalRoomRate: 0,
                totalRoomGST: 0,
                final_gst_rates: {},
            }),
            additionalService: [],
            selectedAdditionalService: [],
            roomCategory: [],
            roomNo: [],
            room_category_form: new Form({
                cat_id: '',
                hotel_id: '',
                check_in_date: ''
            }),
            room_number_form: new Form({
                id: '',
            }),
        }
    },
    // Map Getters
    computed: {
        ...mapGetters("operations", ["items", "appInfo", "hotelItems"]),

        cancelAmount() {
            if (this.cancelType?.id === 2) {
                return parseFloat(this.currentBooking?.total_price || 0) * parseFloat(this.cancelRate || 0) / 100;
            } else {
                return this.cancelRate;
            }
        },
        refundAmount() {
            let amount = parseFloat(this.currentBooking?.advance_amount || 0) - parseFloat(this.cancelAmount);
            return amount <= 0 ? 0 : amount;
        },

    },
    methods: {
        roomNameCustomLable({ room }) {
            return room.room_name
        },
        async getHotelDataList() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/hotel',
            });
        },

        bankChargeLimit(event) {

            if (event.target.value > Math.round(this.currentBooking.paid_amount - this.form.paidAmount)) {
                this.form.bankCharges = Math.round(this.currentBooking.paid_amount - this.form.paidAmount);
            } else {
                this.form.bankCharges = event.target.value;
            }
            this.form.pendingAmount = this.currentBooking.paid_amount - this.form.paidAmount - this.form.bankCharges;
        },
        checkLimit(event) {
            if (event.target.value > Math.round(this.currentBooking.paid_amount - this.form.bankCharges)) {
                this.form.paidAmount = Math.round(this.currentBooking.paid_amount - this.form.bankCharges);
            } else {
                this.form.paidAmount = event.target.value;
            }
            this.form.pendingAmount = this.currentBooking.paid_amount - this.form.paidAmount - this.form.bankCharges;
        },

        async print() {
            await this.$htmlToPaper("printMe");
        },
        async savePayment() {
            if (this.form.paidAmount <= 0) return toast.fire({ type: 'error', title: this.$t('booking.create.amount_error') });
            // if (this.form.account.bankName !== 'Cash' && this.form.bankCharges <= 0) return toast.fire({ type: 'error', title: this.$t('booking.create.commission_amount_error') });

            await this.form.post(window.location.origin + '/api/booking/advance-payment').then(() => {
                toast.fire({
                    type: 'success',
                    title: this.$t('booking.create.advance_success_msg'),
                });
                this.$emit('refresh');
                this.showAddPaymentModal = false;
            }).catch((error) => {
                let message = error.response?.data?.message || this.$t('common.error_msg');
                toast.fire({ type: 'error', title: message });
            });
        },

        async getAccounts() {
            this.form.bank_only = 1
            const { data } = await this.form.get(window.location.origin + "/api/all-accounts");
            this.accounts = data.data;

            // assign default account
            if (this.accounts && this.accounts.length > 0) {
                let defaultAccountSlug = this.appInfo.defaultAccountSlug;
                this.form.account = this.accounts.find(
                    (account) => account.slug == defaultAccountSlug
                );
            }
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
                            path: "/api/booking/delete/",
                            slug: slug,
                        })
                        .then((response) => {
                            if (response === true) {
                                Swal.fire(
                                    this.$t("common.deleted"),
                                    this.$t("common.delete_success"),
                                    "success"
                                );
                                this.$emit('refresh')
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
        openCancelBookingModal(data) {
            this.currentBooking = data;
            this.showCancelModal = true;
        },

        openRoomMoveModal(data) {

            this.noOfDays = 0;
            this.hotelItems.forEach((value) => {
                if (value.id == data.hotel_id) {
                    this.roomCategory = _.uniqBy(value.roomCategories, 'id');
                }
            })

            this.currentBooking = data;
            this.showRoomMoveModal = true;
            this.roomMoveForm.hotel_id = data.hotel_id;
            // this.roomMoveForm.booking_id = data.id;
            this.roomMoveForm.check_in_date = data.check_in_date;
            this.noOfDays = data.BookingDetails[0]?.extra_facility_days;
            if (!this.allVatRates.length) {
                this.getVatRates();
            }
            // return this.hotel_id?.roomCategories ? _.uniqBy(this.hotel_id?.roomCategories, 'id') : [];
            this.showModal = false;
            this.display = false;
        },
        openAddPaymentModal(data) {
            this.currentBooking = data;
            this.form.netTotal = data.total_price;
            this.form.pendingAmount = data.paid_amount;
            this.form.booking_id = data.id
            this.showAddPaymentModal = true;
            this.showModal = false;
            this.display = false;
        },
        openAmendStayModal(data) {
            console.log(data);
            this.amendForm.individualmodifiedRate = [];
            this.amendForm.individualroomRate = [];
            this.amendForm.individualroomTax = [];

            this.currentBooking = data;
            this.amendForm.booking_id = data.id;
            this.amendForm.check_in_date = moment(data.check_in_date, 'DD MMM YY').format('yyyy-MM-DD');
            this.amendForm.check_out_date = moment(data.check_out_date, 'DD MMM YY').format('yyyy-MM-DD');
            this.minDate = moment(data.check_out_date, 'DD MMM YY').format('yyyy-MM-DD');
            if (!this.allVatRates.length) {
                this.getVatRates();
            }
            this.noOfRoom = data.BookingDetails.length;
            this.noOfDays = data.BookingDetails[0]?.extra_facility_days;
            this.amendForm.tax_included = data.taxIncluded;
            this.amendForm.netTotal = data.total_price;
            let facilityTax = 0;
            let mealPlanTax = 0;
            let mealPlanPrice = 0;
            let extraPersonTax = 0;
            let extraBedTax = 0;
            let extraPerson = 0;
            let extraBed = 0;
            let facility = 0;
            let roomRate = 0;
            let modifiedRate = 0;
            data.BookingDetails.forEach((room) => {
                this.amendForm.individualmodifiedRate.push(parseFloat(room.modified_room_rate));
                this.amendForm.individualroomRate.push(parseFloat(room.modified_room_rate * this.noOfDays));
                this.amendForm.individualroomTax.push(room.room_tax);
                this.amendForm.individualroomExtraBedPrice.push(room.extra_bed_price);
                this.amendForm.individualroomExtraPersonPrice.push(room.extra_person_price);

                roomRate += room.room.hotel_room_category.rate * this.noOfDays;
                modifiedRate += room.modified_room_rate;
                facilityTax += room.facility_tax;


                if (room.meal_plan_tax != 0) {
                    mealPlanTax += room.meal_plan_tax;
                    mealPlanPrice += room.meal_plan_details.price * room.meal_plan_persons * room.extra_facility_days;
                }

                if (room.extra_person_tax != 0) {
                    extraPersonTax += room.extra_person_tax;
                    extraPerson += room.extra_person_price;
                }

                if (room.extra_bed_tax != 0) {
                    extraBedTax += room.extra_bed_tax;
                    extraBed += room.extra_bed_price;
                }

                if (room.complementrays.length) {
                    room.complementrays.forEach((comp) => {
                        facility += comp.modified_rate * comp.quantity;
                    });
                }
            });

            if (!data.taxIncluded) {
                this.amendForm.roomRate = parseFloat(modifiedRate) * parseFloat(data.BookingDetails[0]?.extra_facility_days);
            } else {
                this.amendForm.roomRate = parseFloat(modifiedRate) * parseFloat(data.BookingDetails[0]?.extra_facility_days);
            }

            this.finalRoomRate = roomRate;
            // this.finalRoomRate = parseFloat(data.BookingDetails[0]?.room?.room_rate) * parseFloat(data.BookingDetails[0]?.extra_facility_days);
            this.amendForm.totalFacilityCharge = facilityTax + facility;
            this.amendForm.totalExtraBedCharge = extraBedTax + extraBed;
            this.amendForm.totalExtraPersonCharge = extraPersonTax + extraPerson;
            this.amendForm.totalMealCharge = mealPlanTax + mealPlanPrice;
            this.showAmendStayModal = true;
            this.showModal = false;
            this.display = false;
        },
        openAdditionalServiceModal(data) {

            this.selectedAdditionalService = [];

            this.additionalService.room_facility_id = '';
            this.additionalForm.netTotal = 0;
            this.additionalForm.totalGST = 0;
            this.additionalForm.booking_id = '';
            // this.additionalForm.booking_id = data.id;
            this.currentBooking = data;
            if (!this.additionalService.length) {
                this.getHotelServices(data.hotel_id);
            }
            if (!this.allVatRates.length) {
                this.getVatRates();
            }
            this.showAdditionalServiceModal = true;
            this.showModal = false;
            this.display = false;
        },

        async getHotelServices(id) {
            this.facilities_list_form.hotel_id = id;
            const { data } = await this.facilities_list_form.post(
                window.location.origin +
                '/api/room/category/lsit/facility/price/hotel/service',
            );
            this.additionalService = data;
        },

        roomFacilitySelect(item, facilityID, type) {
            let netTotal = 0;
            let totalGST = 0;

            let keyValueToAdd = { quantity: 1 };
            this.selectedAdditionalService = facilityID.map(obj => {
                if (!('quantity' in obj)) {
                    return { ...obj, ...keyValueToAdd };
                } else {
                    return obj;
                }
            });

            this.selectedAdditionalService.forEach((el) => {
                netTotal += (el.price * el.quantity) + ((el.price * el.quantity) * el.sumOfTax / 100);
                totalGST += (el.price * el.quantity) * el.sumOfTax / 100;
            });

            this.additionalForm.netTotal = Number(netTotal.toFixed(2));
            this.additionalForm.totalGST = Number(totalGST.toFixed(2));
        },

        changeRoomService(event) {

            if (event != null) {
                if (event.complementrays.length) {
                    this.selectedAdditionalService = [];
                    this.additionalService.room_facility_id = '';
                    this.additionalForm.netTotal = 0;
                    this.additionalForm.totalGST = 0;
                    let netTotal = 0;
                    let totalGST = 0;
                    const result = [];
                    for (const item1 of this.additionalService) {
                        for (const item2 of event.complementrays) {
                            if (item1.room_facility_id === item2.complementary_id) {
                                result.push({ ...item1, ...item2 });
                            }
                        }
                    }
                    this.additionalService.room_facility_id = result;
                    this.selectedAdditionalService = result;
                    this.selectedAdditionalService.forEach((el) => {
                        if (typeof el.modified_rate !== 'undefined') {
                            netTotal += (el.modified_rate * el.quantity) + ((el.modified_rate * el.quantity) * el.sumOfTax / 100);
                            totalGST += (el.modified_rate * el.quantity) * el.sumOfTax / 100;
                        } else {
                            netTotal += (el.price * el.quantity) + ((el.price * el.quantity) * el.sumOfTax / 100);
                            totalGST += (el.price * el.quantity) * el.sumOfTax / 100;
                        }
                    });
                    this.additionalForm.netTotal = Number(netTotal.toFixed(2));
                    this.additionalForm.totalGST = Number(totalGST.toFixed(2));
                } else {
                    this.selectedAdditionalService = [];
                    this.additionalService.room_facility_id = '';
                    this.additionalForm.netTotal = 0;
                    this.additionalForm.totalGST = 0;
                }
            } else {
                this.additionalForm.netTotal = 0;
                this.additionalForm.totalGST = 0;
                this.additionalService.room_facility_id = '';
                this.selectedAdditionalService = [];
            }

        },


        changePriceQty() {
            let netTotal = 0;
            let totalGST = 0;
            this.selectedAdditionalService.forEach((el) => {
                netTotal += (el.price * el.quantity) + ((el.price * el.quantity) * el.sumOfTax / 100);
                totalGST += (el.price * el.quantity) * el.sumOfTax / 100;
            });

            this.additionalForm.netTotal = Number(netTotal.toFixed(2));
            this.additionalForm.totalGST = Number(totalGST.toFixed(2));
        },

        setNewPricesServices() {
            let getFacilityPrice = 0;
            let netTotal = 0;
            let totalGST = 0;
            if (this.additionalForm.tax_included) {

                this.selectedAdditionalService.forEach((el) => {

                    let finalPrice = parseFloat(el.price) * parseFloat(el.quantity);
                    getFacilityPrice = this.inclusiveTaxAmount(finalPrice, el.sumOfTax)
                    el.price = Number(getFacilityPrice / el.quantity).toFixed(2);
                    el.modified_rate = Number(getFacilityPrice / el.quantity).toFixed(2);

                    netTotal += (el.price * el.quantity) + ((el.price * el.quantity) * el.sumOfTax / 100);
                    totalGST += (el.price * el.quantity) * el.sumOfTax / 100;
                })

                this.additionalForm.netTotal = Number(netTotal.toFixed(2)) | this.forBalanceSheetCurrencyDecimalOnly;

            } else {

                this.selectedAdditionalService.forEach((el) => {

                    let finalPrice = parseFloat(el.price) * parseFloat(el.quantity);
                    getFacilityPrice = this.exclusiveTaxAmount(finalPrice, el.sumOfTax);
                    el.price = Number(getFacilityPrice / el.quantity).toFixed(2);
                    el.modified_rate = Number(getFacilityPrice / el.quantity).toFixed(2);

                    netTotal += (el.price * el.quantity) + ((el.price * el.quantity) * el.sumOfTax / 100);
                    totalGST += (el.price * el.quantity) * el.sumOfTax / 100;
                });

                this.additionalForm.netTotal = Number(netTotal.toFixed(2));
            }

            this.additionalForm.totalGST = Number(totalGST.toFixed(2));
        },

        async saveAdditionalService() {
            // console.log(this.additionalService.room_facility_id);
            if (typeof this.additionalService.room_facility_id === 'undefined') {
                return toast.fire({ type: 'error', title: "Please Select Services" });
            }

            if (this.additionalForm.netTotal == 0 || this.additionalForm.totalGST == 0) {
                return toast.fire({ type: 'error', title: "Please Enter Valid amount" });
            }

            this.additionalForm.selectedService = this.selectedAdditionalService;
            const arrayOfKeyValuePairs = Object.entries(this.currentBooking.gst);

            let gst = {};
            arrayOfKeyValuePairs.forEach(([key, value], index) => {
                this.allVatRates.forEach((rate) => {
                    if (key === rate.name) {
                        if (key == 'SGST 2.5%' || key == 'CGST 2.5%') {
                            gst[rate.name] = value + this.additionalForm.totalGST / 2;
                        } else {
                            gst[rate.name] = value;
                        }
                    } else {
                        if (!gst[rate.name]) {
                            gst[rate.name] = 0;
                        }
                    }
                });
            });
            console.log(gst);
            this.additionalForm.final_gst_rates = gst;

            await this.additionalForm.post(window.location.origin + '/api/booking/additional-service').then(() => {
                toast.fire({
                    type: 'success',
                    title: 'Additional Services Added successfully!',
                });
                this.$emit('refresh');
                this.showAdditionalServiceModal = false;
            }).catch((error) => {
                let message = error.response?.data?.message || this.$t('common.error_msg');
                toast.fire({ type: 'error', title: message });
            });

        },

        updateRoomRate(event) {
            // if(moment(this.currentBooking.check_out_date, 'DD MMM YY').format('yyyy-MM-DD') != event.target.value){
            const numberOFDays = moment(this.amendForm.check_out_date).diff(moment(this.currentBooking.check_in_date, 'DD MMM YY').format('yyyy-MM-DD'), 'days');
            this.noOfDays = numberOFDays;
            let modifiedRate = 0;
            this.currentBooking.BookingDetails.forEach((room) => {
                modifiedRate += room.room.hotel_room_category.rate * numberOFDays;
            });

            this.finalRoomRate = modifiedRate;
            this.setNewPrices();

        },
        async saveAmendStay() {
            if (moment(this.currentBooking.check_out_date, 'DD MMM YY').format('yyyy-MM-DD') == this.amendForm.check_out_date) {
                return toast.fire({ type: 'error', title: "Please Change Checkout Date to Save Amend" });
            }

            const arrayOfKeyValuePairs = Object.entries(this.currentBooking.gst);
            let totalGST = 0
            let gst = {};
            arrayOfKeyValuePairs.forEach(([key, value], index) => {

                this.allVatRates.forEach((rate) => {

                    if (key === rate.name) {
                        if (key == 'SGST 6%' || key == 'CGST 6%') {
                            totalGST += (this.amendForm.roomRate * rate.rate / 100);
                            gst[rate.name] = (this.amendForm.roomRate * rate.rate / 100);
                        } else {
                            gst[rate.name] = 0;
                        }

                    } else {
                        if (!gst[rate.name]) {
                            gst[rate.name] = 0;
                        }
                    }
                });
            });

            Object.entries(this.currentBooking.BookingDetails).forEach(([key, value]) => {

                if (value.extra_bed_tax != 0) {
                    gst["SGST 6%"] += this.amendForm.individualroomExtraBedTax[key] / 2;
                    gst["CGST 6%"] += this.amendForm.individualroomExtraBedTax[key] / 2;
                }

                if (value.extra_person_tax != 0) {
                    gst["SGST 6%"] += this.amendForm.individualroomExtraPersonTax[key] / 2;
                    gst["CGST 6%"] += this.amendForm.individualroomExtraPersonTax[key] / 2;
                }

                if (value.meal_plan_tax != 0) {
                    gst["SGST 2.5%"] += this.amendForm.individualroomMealTax[key] / 2;
                    gst["CGST 2.5%"] += this.amendForm.individualroomMealTax[key] / 2;
                }

                if (value.facility_tax != 0) {
                    gst["SGST 2.5%"] += value.facility_tax / 2;
                    gst["CGST 2.5%"] += value.facility_tax / 2;
                }
            });

            this.amendForm.final_gst_rates = gst;
            this.amendForm.totalGST = totalGST;
            this.amendForm.noOfDays = this.noOfDays;
            this.amendForm.noOfRoom = this.noOfRoom;

            await this.amendForm.post(window.location.origin + '/api/booking/amend-stay').then(() => {
                toast.fire({
                    type: 'success',
                    title: 'Checkout Date Extended successfully!',
                });
                this.$emit('refresh');
                this.showAmendStayModal = false;
            }).catch((error) => {
                let message = error.response?.data?.message || this.$t('common.error_msg');
                toast.fire({ type: 'error', title: message });
            });
        },
        async cancelBooking(slug) {
            // Send request to the server
            let accountFrom = (this.accountFrom) ? this.accountFrom.id : '';
            this.$store
                .dispatch("operations/hotelDeleteData", {
                    path: "/api/booking/cancel/",
                    slug: slug,
                    query: '?charge=' + this.cancelAmount + '&account_id=' + accountFrom,
                })
                .then((response) => {
                    if (response === true) {
                        Swal.fire(
                            this.$t("common.canceled"),
                            this.$t("common.cancel_success"),
                            "success"
                        );
                        this.showCancelModal = false
                        this.$emit('refresh')
                    } else {
                        Swal.fire(
                            this.$t("common.failed"),
                            this.$t(response.message),
                            "warning"
                        );
                        this.showCancelModal = false
                    }
                });

        },
        inclusiveTaxAmount(amount, taxRate) {
            let price = parseFloat(amount || 0);
            let rate = parseFloat(taxRate || 0);

            return price * (100 / (100 + rate));
        },
        exclusiveTaxAmount(amount, taxRate) {
            let price = parseFloat(amount || 0);
            let rate = parseFloat(taxRate || 0);

            return price * ((100 + rate) / 100);
        },

        setNewPrices() {

            this.amendForm.individualmodifiedRate = [];
            this.amendForm.individualroomRate = [];
            this.amendForm.individualroomTax = [];
            this.amendForm.individualroomExtraBedTax = [];
            this.amendForm.individualroomExtraPersonTax = [];
            this.amendForm.individualroomMealTax = [];
            this.amendForm.individualroomExtraPersonPrice = [];
            this.amendForm.individualroomExtraBedPrice = [];

            let totalGst = 0;
            let getPrice = 0;
            let extraBedCharges = 0;
            let extraPersonCharges = 0;
            let mealPlanCharges = 0;
            let mealPlanTotalGst = 0;
            // 
            this.applicableGst.forEach((value) => {
                totalGst += value.tax_name.rate;
            });
            console.log(this.currentBooking.BookingDetails[0].meal_plan_details);
            this.currentBooking.BookingDetails[0]?.meal_plan_details?.tax_name.forEach((meal) => {
                mealPlanTotalGst += meal.rate;
            });


            if (this.amendForm.tax_included) {
                this.currentBooking.BookingDetails.forEach((room) => {

                    let RoomPrice = this.inclusiveTaxAmount(room.room.hotel_room_category.rate, totalGst)

                    this.amendForm.individualmodifiedRate.push(RoomPrice);
                    this.amendForm.individualroomRate.push(RoomPrice * this.noOfDays);

                    let price = this.inclusiveTaxAmount(room.room.hotel_room_category.rate * this.noOfDays, totalGst);
                    this.amendForm.individualroomTax.push((price * totalGst) / 100);

                    if (room.extra_bed_tax != 0) {
                        let extraBed = this.inclusiveTaxAmount(room.room.hotel_room_category.extra_bed_rate * this.noOfDays, totalGst);
                        extraBedCharges += extraBed + (extraBed * totalGst / 100);
                        this.amendForm.individualroomExtraBedTax.push((extraBed * totalGst / 100));
                        this.amendForm.individualroomExtraBedPrice.push(extraBed);
                    }

                    if (room.extra_person_tax != 0) {
                        let extraPerson = this.inclusiveTaxAmount(room.room.hotel_room_category.per_person * this.noOfDays, totalGst);
                        extraPersonCharges += extraPerson + (extraPerson * totalGst / 100);
                        this.amendForm.individualroomExtraPersonTax.push((extraPerson * totalGst / 100));
                        this.amendForm.individualroomExtraPersonPrice.push(extraPerson);
                    }

                    if (room.meal_plan_tax != 0) {
                        let mealPlan = this.inclusiveTaxAmount(room.meal_plan_details.price * room.meal_plan_persons * this.noOfDays, mealPlanTotalGst);
                        mealPlanCharges += mealPlan + (mealPlan * mealPlanTotalGst / 100);
                        this.amendForm.individualroomMealTax.push((mealPlan * mealPlanTotalGst / 100));
                    }
                });

                let getRoomPrice = this.inclusiveTaxAmount(this.finalRoomRate, totalGst);
                this.amendForm.roomRate = Number(getRoomPrice.toFixed(2));
                getPrice = getRoomPrice + (getRoomPrice * totalGst / 100);

            } else {

                let modifiedRate = 0;
                this.currentBooking.BookingDetails.forEach((room) => {

                    this.amendForm.individualmodifiedRate.push(room.room.hotel_room_category.rate);

                    this.amendForm.individualroomRate.push(room.room.hotel_room_category.rate * this.noOfDays);
                    this.amendForm.individualroomExtraBedPrice.push(room.room.hotel_room_category.extra_bed_rate * this.noOfDays);
                    this.amendForm.individualroomExtraPersonPrice.push(room.room.hotel_room_category.per_person * this.noOfDays);

                    if (room.extra_bed_tax != 0) {
                        this.amendForm.individualroomExtraBedTax.push(((room.room.hotel_room_category.extra_bed_rate * this.noOfDays) * totalGst / 100));
                        extraBedCharges += (room.room.hotel_room_category.extra_bed_rate * this.noOfDays) + room.room.hotel_room_category.extra_bed_rate * this.noOfDays * totalGst / 100;
                    }

                    if (room.extra_person_tax != 0) {
                        this.amendForm.individualroomExtraPersonTax.push(((room.room.hotel_room_category.per_person * this.noOfDays) * totalGst / 100));
                        extraPersonCharges += (room.room.hotel_room_category.per_person * this.noOfDays) + room.room.hotel_room_category.per_person * this.noOfDays * totalGst / 100;
                    }

                    if (room.meal_plan_tax != 0) {
                        this.amendForm.individualroomMealTax.push((room.meal_plan_details.price * room.meal_plan_persons * this.noOfDays * mealPlanTotalGst / 100));
                        mealPlanCharges += (room.meal_plan_details.price * room.meal_plan_persons * this.noOfDays) + (room.meal_plan_details.price * room.meal_plan_persons * this.noOfDays * mealPlanTotalGst / 100);
                    }

                    let price = this.exclusiveTaxAmount(room.room.hotel_room_category.rate * this.noOfDays, totalGst);
                    this.amendForm.individualroomTax.push(price - (room.room.hotel_room_category.rate * this.noOfDays));


                    modifiedRate += room.room.hotel_room_category.rate * this.noOfDays;
                });

                this.amendForm.roomRate = modifiedRate;
                getPrice = this.exclusiveTaxAmount(modifiedRate, totalGst);
            }


            this.amendForm.totalExtraBedCharge = Number(extraBedCharges.toFixed(2));
            this.amendForm.totalExtraPersonCharge = Number(extraPersonCharges.toFixed(2));
            this.amendForm.totalMealCharge = Number(mealPlanCharges.toFixed(2));
            // this.amendForm.netTotal = Number(getPrice.toFixed(2)) * parseFloat(this.currentBooking.BookingDetails.length) + this.amendForm.totalFacilityCharge;
            this.amendForm.netTotal = Number(getPrice.toFixed(2)) + this.amendForm.totalFacilityCharge + this.amendForm.totalExtraBedCharge + this.amendForm.totalExtraPersonCharge + this.amendForm.totalMealCharge;
        },

        async getVatRates() {
            await axios.get('/api/all-vat-rates').then((response) => {
                this.allVatRates = response.data.data;
                let isFacilityAdded = this.currentBooking.BookingDetails[0]?.complementrays.length
                let gst = [];
                let indRate = 6;
                if (this.currentBooking.BookingDetails[0]?.room?.hotel_room_category?.rate <= 7500) {
                    indRate = 6;
                } else {
                    indRate = 9;
                }

                this.allVatRates.forEach((value) => {
                    if (value.rate == indRate) {
                        gst.push({ tax_name: value })
                    }
                })
                this.applicableGst = gst;
            });
        },



        async getRoomForCategory() {
            this.roomNo = [];
            this.roomMoveForm.price = 0;
            this.room_category_form.cat_id = this.roomMoveForm.room_cat_id.id;
            this.room_category_form.hotel_id = this.roomMoveForm.hotel_id;
            this.room_category_form.check_in_date = moment(this.roomMoveForm.check_in_date).format('yyyy-MM-DD hh:MM:SS');

            const { data } = await this.room_category_form.post(
                window.location.origin +
                '/api/hotel/get-hotel-cat'
            );

            // this.currentBooking.BookingDetails.forEach((el) => {
            //         data.data = data.data.filter((ele) => el.room_id !== ele.id);

            //         if (el.id === id) {
            //             el.roomNumber = data.data;
            //         }
            //     });

            this.roomNo = data.data;
        },

        async getSelectedRoomPrice() {
            let totalGST = 0;
            this.roomMoveForm.price = 0;
            this.roomMoveForm.totalRoomRate = 0;
            this.room_number_form.id = this.roomMoveForm.room_id.id;

            const { data } = await this.room_number_form.post(
                window.location.origin +
                '/api/hotel/get-single-room',
            );
            this.roomMoveForm.price = data.data.hotel_room_category.rate;

            if (this.applicableGst.length) {
                totalGST = this.applicableGst[0].tax_name.rate * 2;
                this.roomMoveForm.totalRoomGST = (data.data.hotel_room_category.rate * this.noOfDays) * totalGST / 100;
            }
            this.roomMoveForm.totalRoomRate = data.data.hotel_room_category.rate * this.noOfDays + this.roomMoveForm.totalRoomGST;
        },

        setRoomNewPrice(event) {
            if (this.room_category_form.cat_id != 0 && this.room_number_form.id != 0 && event.target.value > 0) {
                let totalGST = 0;
                if (this.applicableGst.length) {
                    totalGST = this.applicableGst[0].tax_name.rate * 2;
                    this.roomMoveForm.totalRoomGST = (event.target.value * this.noOfDays) * totalGST / 100;
                }
                this.roomMoveForm.totalRoomRate = event.target.value * this.noOfDays + this.roomMoveForm.totalRoomGST;
            } else {
                return toast.fire({ type: 'error', title: "Please Select Room Category or Room no" });
            }
        },

        async saveRoomMove() {

            if (typeof this.roomMoveForm.booking_id.room_id === "undefined") {
                return toast.fire({ type: 'error', title: "Please Select Room to Move" });
            }

            if (this.room_category_form.cat_id == '' && this.room_number_form.id == '') {
                return toast.fire({ type: 'error', title: "Please Select Room Category or Room no" });
            }

            if (this.roomMoveForm.price <= 0) {
                return toast.fire({ type: 'error', title: "Please Enter Valid Price" });
            }

            let totalRoomExpectSelectedRates = 0;
            let totalRoomRate = 0;
            const arrayOfKeyValuePairs = Object.entries(this.currentBooking.gst);

            this.currentBooking.BookingDetails.forEach((el) => {
                if (el.room_id != this.roomMoveForm.booking_id.room_id) {
                    totalRoomExpectSelectedRates += el.room_rate;
                }
            });

            totalRoomRate = totalRoomExpectSelectedRates + (this.roomMoveForm.price * this.noOfDays);

            let totalGST = 0
            let gst = {};
            arrayOfKeyValuePairs.forEach(([key, value], index) => {

                this.allVatRates.forEach((rate) => {

                    if (key === rate.name) {
                        if (key == 'SGST 6%' || key == 'CGST 6%') {
                            totalGST += (totalRoomRate * rate.rate / 100);
                            gst[rate.name] = (totalRoomRate * rate.rate / 100);
                        } else {
                            gst[rate.name] = value;
                        }

                    } else {
                        if (!gst[rate.name]) {
                            gst[rate.name] = 0;
                        }
                    }
                });
            });

            Object.entries(this.currentBooking.BookingDetails).forEach(([key, value]) => {
                if (value.extra_bed_tax != 0.00) {
                    gst["SGST 6%"] += value.extra_bed_tax / 2;
                    gst["CGST 6%"] += value.extra_bed_tax / 2;
                }

                if (value.extra_person_tax != 0.00) {
                    gst["SGST 6%"] += value.extra_person_tax / 2;
                    gst["CGST 6%"] += value.extra_person_tax / 2;
                }
            });


            this.roomMoveForm.final_gst_rates = gst;
            await this.roomMoveForm.post(window.location.origin + '/api/booking/room-move').then(() => {
                toast.fire({
                    type: 'success',
                    title: 'Room Move successfully!',
                });
                this.$emit('refresh');
                this.showRoomMoveModal = false;
            }).catch((error) => {
                let message = error.response?.data?.message || this.$t('common.error_msg');
                toast.fire({ type: 'error', title: message });
            });
        }
    },
    // watch:{
    //     tax_included: {
    //         handler: 'setNewPrices',
    //     },
    // }
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

/*Overview Modal CSS Added By ABhi*/
.overview .modal-body .heading {
    font-weight: 700;
    font-size: 14px;
    color: #4B5563;
    cursor: auto;
}

.overview .modal-body .moreoptionBox {
    position: absolute;
    background-color: white;
    display: none;
    width: auto;
    margin-left: 80px;
    margin-top: 10px;
}

.overview .modal-body .moreoptionBox i {
    width: 30px;
}

.overview .modal-body .moreOptionBtn {
    position: relative;
}

.overview .modal-body .moreoptionBox .flex:hover {
    color: white !important;
    background-color: #6366f1 !important;
    cursor: pointer;
}

.overview .modal-body .moreoptionBox .flex label:hover {
    cursor: pointer;
}
h5 {
    display: flex;
    justify-content: space-between;
    width: 100%;
}
</style>
