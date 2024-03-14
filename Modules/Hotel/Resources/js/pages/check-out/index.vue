<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row mb-4">
            <!-- customer-details -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $t('check_in.index.customer_details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-25 mb-0 fs-14"> {{ $t('check_in.index.name') }} </h6>
                            <p class="mb-0 fs-14">{{ checkOutData.customer_details.name }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-25 mb-0 fs-14"> {{ $t('check_in.index.room_no') }} </h6>
                            <p class="mb-0 fs-14">{{ checkOutData.customer_details.room_no }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-25 mb-0 fs-14"> {{ $t('check_in.index.booking_no') }} </h6>
                            <p class="mb-0 fs-14">{{ checkOutData.customer_details.booking_no }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-25 mb-0 fs-14"> {{ $t('check_in.index.email_id') }} </h6>
                            <p class="mb-0 fs-14">{{ checkOutData.customer_details.email }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-25 mb-0 fs-14"> {{ $t('check_in.index.mobile_no') }} </h6>
                            <p class="mb-0 fs-14">{{ checkOutData.customer_details.mobile_no }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-25 mb-0 fs-14"> {{ $t('check_in.index.address') }} </h6>
                            <p class="mb-0 fs-14">{{ checkOutData.customer_details.address }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- default-customer -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $t('check_in.index.room_checkout') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="card-main mb-3" v-for="(item, index) in checkOutData.customer" :key="index">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" :value="item.id" v-model="selectedRooms"
                                    @change="changeSelectedRooms" name="flexRadioDefault" :id="item.room_no"
                                    :disabled="item.booking_status == 4">
                                <label class="form-check-label" :for="index">
                                    <p class="mb-0">{{ item.room_no }} - {{ item.booking_no }} {{ item.booking_status == 4 ?
                                        '(Already checked out)' : '' }}</p>
                                    <h6 class="mb-0">{{ item.check_in_date | moment("Do MMM, YYYY") }} - {{
                                        item.check_out_date | moment("Do MMM, YYYY") }}</h6>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template v-if="roomDetails && roomDetails.length > 0">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-container">
                            <thead>
                                <tr>
                                    <th scope="col">{{ $t('check_in.index.room_no') }}</th>
                                    <th scope="col">{{ $t('check_in.index.date') }}</th>
                                    <th class="text-center">{{ $t('check_in.index.rent_details') }}</th>
                                </tr>
                            </thead>
                            <tbody v-for="roomDetail in roomDetails" :key="roomDetail.room_no">
                                <tr>
                                    <td scope="row">
                                        <h6>{{ roomDetail.room_no }}</h6>
                                        <p class="text-color">{{ roomDetail.room_category }}</p>
                                    </td>
                                    <td>
                                        <div class="border-b">
                                            {{ roomDetail.check_in | moment("Do MMM, YYYY") }} to <br>
                                            {{ roomDetail.check_out | moment("Do MMM, YYYY") }}
                                        </div>
                                        <p class="border-b text-color mb-0 py-1">
                                            {{ $t('check_in.index.adults') }} {{ roomDetail.adult_count }}
                                        </p>
                                        <p class="border-b text-color mb-0 py-1">
                                            {{ $t('check_in.index.children') }} {{ roomDetail.children_count }}
                                        </p>
                                        <p class="border-b text-color mb-0 py-1">
                                            {{ $t('check_in.index.infants') }} {{ roomDetail.infant_count }}
                                        </p>
                                    </td>
                                    <td>
                                        <table class="table table-bordered table-bg">
                                            <thead>
                                                <tr>
                                                    <!--                                            <th scope="col">{{ $t('check_in.index.from_date') }}</th>-->
                                                    <!--                                            <th scope="col">{{ $t('check_in.index.to_date') }}</th>-->
                                                    <th scope="col"> {{ $t('check_in.index.total_days') }}</th>
                                                    <th scope="col">({{ CURRENCY }}) {{ $t('check_in.index.rent_day') }}
                                                    </th>
                                                    <th scope="col">({{ CURRENCY }}) {{ $t('check_in.index.total_rent') }}
                                                    </th>
                                                    <th scope="col">({{ CURRENCY }}) {{ $t('check_in.index.commission') }}
                                                    </th>
                                                    <th scope="col">({{ CURRENCY }}) {{ $t('check_in.index.tax') }}</th>
                                                    <th scope="col">({{ CURRENCY }}) {{ $t('check_in.index.total_amt') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody style="color: rgb(61, 61, 61);">
                                                <tr>

                                                    <td>{{ roomDetail.room_rent_details?.days }}</td>
                                                    <td>{{ roomDetail.room_rent_details?.rent_per_day }}</td>
                                                    <td>{{ totalRent(roomDetail.room_rent_details) -
                                                        roomDetail.commission_amount }}</td>
                                                    <td>{{ roomDetail.commission_amount }}</td>
                                                    <td v-if="roomDetail.room_rent_details">
                                                        <div v-for="(tax, taxIndex) in roomDetail.room_rent_details.tax_details"
                                                            :key="taxIndex">
                                                            ({{ tax?.type }}) = {{ tax?.amount | twoPoints }} *
                                                            {{ roomDetail.room_rent_details?.days }} <br>
                                                        </div>
                                                    </td>
                                                    <td>{{ totalAmount(roomDetail.room_rent_details) | twoPoints }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4"></td>
                                                    <td>{{ totalTax(roomDetail.room_rent_details) | twoPoints }}</td>
                                                    <td>{{ totalAmount(roomDetail.room_rent_details) | twoPoints }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <!--------------------------------- Room charges  ----------------------------------------------------->
                    <div class="card-header">
                        <h6 class="mb-0">{{ $t('check_in.index.billing_details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="custom-width mb-0">{{ $t('check_in.index.rent_amt') }}</h6>
                            <p class="mb-0">{{ (totalRoomRentsDetails.totalRent - commission) | twoPoints }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="custom-width mb-0">{{ $t('check_in.index.total_tax') }}</h6>
                            <p class="mb-0">{{ totalRoomRentsDetails.totalTax | twoPoints }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="custom-width mb-0">{{ $t('check_in.index.discount_amount') }}</h6>
                            <p class="mb-0">{{ (selectedRooms.length ? (roomDiscount) : '0') | twoPoints }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="custom-width mb-0">{{ $t('check_in.index.commission') }}</h6>
                            <p class="mb-0">{{ (selectedRooms.length ? (commission) : '0') | twoPoints }}</p>
                        </div>
                        <div class="d-flex align-items-center py-2 border-t">
                            <h6 class="custom-width mb-0">{{ $t('check_in.index.room_amount') }}</h6>
                            <p class="mb-0">{{ (selectedRooms.length ? (totalRoomRentsDetails.totalAmount - roomDiscount) :
                                `0`) | withCurrency }}</p>
                        </div>
                    </div>

                    <!--------------------------------- Features charges  ----------------------------------------------------->
                    <div class="card-header border-top" style="margin-top: -2rem;">
                        <h6 class="mb-0">Additional Billing Details</h6>
                    </div>
                    <div class="card-body">
                        <table class="table addition_table">
                            <!--                            <thead class="border-t">-->
                            <!--                            <tr class="border-b">-->
                            <!--                                <th>{{ $t('check_in.index.bill_type') }}</th>-->
                            <!--                                <th class="text-right">({{ CURRENCY }}){{ $t('check_in.index.total') }}</th>-->
                            <!--                            </tr>-->
                            <!--                            </thead>-->
                            <tbody>
                                <tr v-if="totalRoomRentsDetails.mealPlanCharge">
                                    <td>{{ $t('check_in.index.plan_chaege') }}</td>
                                    <td class="text-right">{{ totalRoomRentsDetails.mealPlanCharge | twoPoints }}</td>
                                </tr>
                                <tr v-if="totalRoomRentsDetails.totalBedAmt">
                                    <td> {{ $t('check_in.index.bed_charge') }}</td>
                                    <td class="text-right">{{ totalRoomRentsDetails.totalBedAmt | twoPoints }}</td>
                                </tr>
                                <tr v-if="totalRoomRentsDetails.totalExtraPersonAmt">
                                    <td>{{ $t('check_in.index.person_charge') }}</td>
                                    <td class="text-right">{{ totalRoomRentsDetails.totalExtraPersonAmt | twoPoints }}</td>
                                </tr>
                                
                                <tr v-for="comp in Object.keys(complimentaryItems)" :key="comp">
                                    <td>{{ comp }}</td>
                                    <td class="text-right">{{ complimentaryItems[comp] | twoPoints }}</td>
                                </tr>
                                <template
                                    v-if="totalRoomRentsDetails.mealPlanCharge || totalRoomRentsDetails.totalBedAmt
                                        || totalRoomRentsDetails.totalExtraPersonAmt || Object.keys(complimentaryItems).length > 0">
                                    <tr class="border-t">
                                        <td>Total tax</td>
                                        <td class="text-right">{{ (totalRoomRentsDetails.additionalTax || 0) | twoPoints }}
                                        </td>
                                    </tr>
                                    <tr class="border-t">
                                        <td class="font-weight-bold">{{ $t('check_in.index.total') }}</td>
                                        <td class="font-weight-bold text-right">{{ totalAdditionalBillingAmt | withCurrency }}
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-------------------------------------Additional charges---------------------------------------------->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $t('check_in.index.addditional_charges') }}</h6>
                    </div>
                    <div class="card-body w-75">
                        <div class="d-flex align-items-center mb-2 justify-content-between">
                            <h6 class="custom-width-input mb-0">{{ $t('check_in.index.addditional_charges') }}</h6>
                            <p class="mb-0"><input type="number" v-model.number="extra_charge"
                                    class="input-width form-control mr-2"></p>
                        </div>
                        <div class="d-flex align-items-center mb-2 justify-content-between">
                            <h6 class="custom-width-input mb-0">{{ $t('check_in.index.charge_comments') }}</h6>
                            <p class="mb-0">
                                <textarea v-model="checkOutData.extra_charge_comment" class="form-textarea form-control"
                                    placeholder="Additional charge Comments"></textarea>
                            </p>
                        </div>
                    </div>

                    <!-------------------------------SPECIAL DISCOUNT------------------------------------>

                    <div class="card-header">
                        <h6 class="mb-0">{{ $t('check_in.index.special_discounts') }}</h6>
                    </div>
                    <div class="card-body">
                        <table class="table addition_table">
                            <tbody>
                                <tr>
                                    <td>
                                        <label>{{ $t('check_in.index.discount_type') }}</label>
                                        <multiselect v-model="specialDiscountType" :options="discountRoomRateData"
                                            :show-labels="false" tag-placeholder="Add this as new tag"
                                            placeholder="Search a Discount room rate" class="form-control" label="title"
                                            track-by="title" />
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>{{
                                        $t('common.discount')
                                    }}</label>
                                        <input id="bookingtypetitle" step="0.1" v-model.number="specialDiscountAmount"
                                            type="number" class="form-control" name="bookingtypetitle"
                                            :placeholder="$t('common.discount_placeholder')" />
                                    </td>
                                    <td><label>{{
                                        $t('common.discount_amount') }}</label>
                                        <p class="mb-0 ml-2 mt-2">{{ (specialDiscount ? specialDiscount : 0) |
                                            withCurrency }}</p>
                                    </td>
                                </tr>
                                <tr class="border-t mt-2">
                                    <td><label class="font-weight-bold">{{ $t('check_in.index.payable_amount') }}</label>
                                    </td>
                                    <td>
                                        <p class="mb-0 ml-2 mt-2">{{ (netPayableAmount > 0 ? netPayableAmount -
                                            parseFloat(this.specialDiscount || 0) : 0) | withCurrency }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- customer-details -->
            <div class="col-lg-6">
                <div class="card h-100">

                    <!------------------------------------PAYMENT DETAILS--------------------------------------------->

                    <div class="card-header">
                        <h6 class="mb-0">{{ $t('check_in.index.payment_details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14">{{ $t('check_in.index.total_amt') }}</h6>
                            <p class="mb-0 text-right">+ {{ (this.totalAdditionalBillingAmt +
                                this.totalRoomRentsDetails.totalAmount - this.roomDiscount) | twoPoints }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14">{{ $t('check_in.index.credit_amt') }}</h6>
                            <p class="mb-0 text-right">- {{ (checkOutData.advance_amount + checkOutData.creditAmount) |
                                twoPoints }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14">{{ $t('check_in.index.special_discount') }}</h6>
                            <p class="mb-0 text-right">- {{ (checkOutData.special_discount || 0) | twoPoints }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14">{{ $t('check_in.index.remaining_amt') }}</h6>
                            <p class="mb-0 text-right">+ {{ (checkOutData.remaining_amount || 0) | twoPoints }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14">Extra charge amount</h6>
                            <p class="mb-0 text-right">+ {{ (checkOutData.extra_charge || extra_charge || 0) | twoPoints }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center mb-2" v-if="restaurantOrderDetails?.total">
                            <h6 class="w-50 mb-0 fs-14">Restaurant order</h6>
                            <p class="mb-0 text-right">+ {{ (restaurantOrderDetails?.total || 0) | withCurrency }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14 font-weight-bold">{{ $t('check_in.index.payable_amt') }}</h6>
                            <p class="mb-0 text-right font-weight-bold">= {{ (netPayableAmount >= 0 ? netPayableAmount : 0)
                                | withCurrency }}</p>
                        </div>
                    </div>

                    <!------------------------------ Restaurant billing details --------------------------------->
                    <div class="card-header" v-if="restaurantOrderDetails">
                        <h6 class="mb-0">Restaurant Billing Details</h6>
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
                                <tr v-if="restaurantOrderDetails?.items"
                                    v-for="(item, index) in restaurantOrderDetails.items" :key="item.name + index">
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
                                <tr class="border-top">
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
            <!-- default-customer -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $t('check_in.index.final_balance') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14">
                                {{ $t('check_in.index.payment_mode') }}
                            </h6>
                            <div class="d-flex w-100">
                                <multiselect @select="setDropDownData(selectedPaymentMethod, paymentModeData)"
                                    v-model="selectedPaymentMethod" :options="paymentModeData" :show-labels="false"
                                    tag-placeholder="Add this as new tag" placeholder="Search an option"
                                    class="form-control" label="ledger_name" track-by="ledger_name" style="width:80%;" />
                                <div class="input-group-text create-btn" @click="addExtraPaymentMethod = true">
                                    <i class="fas fa-solid fa-plus-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14">
                                ({{ CURRENCY }}) {{ $t('check_in.index.credit_amts') }}
                            </h6>
                            <p class="mb-0 w-100">
                                <input type="number" v-model.number="creditAmount" class="form-control" @input="checkLimit">
                            </p>
                        </div>
                        <div class="d-flex align-items-center mb-2" v-if="selectedPaymentMethod?.ledger_name !== 'Cash'">
                            <h6 class="w-50 mb-0 fs-14">
                                ({{ CURRENCY }}) {{ $t('check_in.index.bank_charge') }}
                            </h6>
                            <p class="mb-0 w-100">
                                <input type="number" v-model.number="bank_charges" class="form-control"
                                    @input="bankChargesLimit">
                            </p>
                        </div>
                        <!--Start Extra Payment Method -->
                        <div v-if="addExtraPaymentMethod">
                            <div class="d-flex align-items-center mb-2">
                                <h6 class="w-50 mb-0 fs-14">
                                    {{ $t('check_in.index.payment_mode') }}
                                </h6>
                                <div class="d-flex w-100">
                                    <multiselect v-model="selectedPaymentMethod1" :options="paymentModeData1"
                                        :show-labels="false" tag-placeholder="Add this as new tag"
                                        placeholder="Search an option" class="form-control" label="ledger_name"
                                        track-by="ledger_name" style="width:80%;" />
                                    <div class="input-group-text create-btn"
                                        @click="addExtraPaymentMethod = false; selectedPaymentMethod1 = ''; creditAmount1 = 0">
                                        <i class="fas fa-solid fa-minus-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <h6 class="w-50 mb-0 fs-14">
                                    ({{ CURRENCY }}) {{ $t('check_in.index.credit_amts') }}
                                </h6>
                                <p class="mb-0 w-100">
                                    <input type="number" v-model.number="creditAmount1" class="form-control"
                                        @input="checkLimit1">
                                </p>
                            </div>
                            <div class="d-flex align-items-center mb-2"
                                v-if="selectedPaymentMethod1?.ledger_name !== 'Cash'">
                                <h6 class="w-50 mb-0 fs-14">
                                    ({{ CURRENCY }}) {{ $t('check_in.index.bank_charge') }}
                                </h6>
                                <p class="mb-0 w-100">
                                    <input type="number" v-model.number="bank_charges1" class="form-control"
                                        @input="bankChargesLimit1">
                                </p>
                            </div>
                        </div>

                        <!--End Extra Payment Method -->
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="w-50 mb-0 fs-14">
                                {{ $t('check_in.index.remarks') }}
                            </h6>
                            <p class="mb-0 w-100">
                                <textarea placeholder="Remarks" v-model="checkOutData.advance_remarks"
                                    class="form-textarea form-control"></textarea>
                            </p>
                        </div>
                    </div>
                    <div class="card-header">
                        <h6 class="mb-0">{{ $t('check_in.index.blance_details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="custom-width mb-0 font-weight-bold">{{ $t('check_in.index.remain_amt') }}</h6>
                            <p class="mb-0">{{ Math.round(remainingAmt) | withCurrency }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mt-4">
                <div class="d-flex justify-content-end align-items-center" style="gap: 1rem;">
                    <label for="customer" class="d-block">{{ 'Select invoice party' }}</label>
                    <multiselect v-model="selectedInvoicePerson" :options="invoicePersons || []" :taggable="false"
                        :multiple="false" :allowEmpty="false" :show-labels="false" placeholder="Search a customer"
                        class="form-control" style="min-width: 20rem"
                        :custom-label="({ name, phone }) => `${name ? name + '-' : ''} ${phone}`" label="name"
                        track-by="id"></multiselect>
                </div>
            </div>

        </div>
        <div class="card-footer">
            <v-button class="btn btn-primary float-right" :loading="submitLoader" @click.native.prevent="submit">
                <i class="fas fa-save" /> {{ $t('common.save') }}
            </v-button>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import Multiselect from 'vue-multiselect';
import Form from "vform";
import _ from "lodash";
import axios from "axios";

export default {
    metaInfo() {
        return { title: this.$t("check_out.index.page_title") };
    },
    components: {
        Multiselect,
    },
    data: () => ({
        breadcrumbsCurrent: "check_out.index.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "check_out.index.breadcrumbs_first",
                url: "home",
            },
            {
                name: "check_out.index.breadcrumbs_second",
                url: "",
            },
        ],
        CURRENCY: '$',
        selectedRooms: [],
        addExtraPaymentMethod: false,
        restaurantOrderDetails: null,
        paymentModeData: [],
        paymentModeData1: [],
        selectedPaymentMethod: null,
        selectedPaymentMethod1: null,
        extra_charge: 0,
        selectedInvoicePerson: null,
        invoicePersons: [],
        submitLoader: false,
        checkOutData:
        {
            extra_charge: 0,
            discount: 0,
            final_gst: 0,
            extra_charge_comment: '',
            advance_amount: 0,
            advance_remarks: '',
            remaining_amount: 0,
            special_discount: 0,
            customer_details: {
                name: "",
                room_no: "",
                booking_no: "",
                email: "",
                mobile_no: "",
                address: '',
            },
            customer: [
                {
                    room_no: null,
                    booking_no: "",
                    check_in_date: "",
                    check_out_date: "",
                },
            ],
        },
        payment_method_form: new Form({
            ledger_type: '',
        }),
        form: new Form({
            invoicePerson: '',
            id: '',
            extra_charge: '',
            extra_charge_comment: '',
            net_amount: '',
            advance_remarks: '',
            credit_amount: '',
            credit_amount1: '',
            bank_charges: '',
            bank_charges1: '',
            special_discount: '',
            special_discount_rate: 0,
            special_discount_type: null,
            payment_method: '',
            payment_method1: '',
            selected_rooms: [],
            restaurantTotal: 0,
            restaurantOrderId:[],
            actual_remaining_amount: 0,
            is_extra_payment_method: false,
        }),
        roomDetails: [],
        tax_included: false,
        restaurantNetAmount: 0,
        restaurantOrderId:[],
        specialDiscountType: null,
        specialDiscount: null,
        creditAmount: 0,
        creditAmount1: 0,
        bank_charges: 0,
        bank_charges1: 0,
        specialDiscountAmount: 0,
        discountRoomRateData: [
            {
                title: 'Fixed',
                id: 1,
            },
            {
                title: 'Percentage',
                id: 2,
            },
        ],
    }),
    computed: {
        ...mapGetters("operations", ["items", "loading", "checkOutItems", 'checkoutRoomsDetails']),
        
        totalRoomRentsDetails() {
            let totalRent = 0;
            let totalTax = 0;
            let totalAmount = 0;
            let totalBedAmt = 0;
            let totalExtraPersonAmt = 0;
            let mealPlanCharge = 0;
            let additionalTax = 0;
            
            this.roomDetails.forEach((room) => {
                let days = room.room_rent_details?.days || 0;
                totalRent += this.totalRent(room.room_rent_details);
                totalTax += this.totalTax(room.room_rent_details);
                totalAmount += this.totalAmount(room.room_rent_details);
                mealPlanCharge += room.mealPlanPrice * days * room.mealPersons
                totalBedAmt += room.bedPrice;
                totalExtraPersonAmt += room.perPersonPrice;
                additionalTax += (room.mealTax + room.bedTax + room.extraPersonTax + room.facilityTax)
            })

            return {
                'totalRent': totalRent,
                'totalTax': totalTax,
                'totalAmount': totalAmount,
                'totalBedAmt': totalBedAmt,
                'totalExtraPersonAmt': totalExtraPersonAmt,
                'mealPlanCharge': mealPlanCharge,
                'additionalTax': additionalTax
            }
        },

        roomDiscount() {
            let discount = 0;
            this.roomDetails.forEach((room) => {
                discount += room.discount || 0
            })
            return discount;
        },

        commission() {
            let commission = 0;
            this.roomDetails.forEach((room) => {
                commission += room.commission_amount || 0
            })
            return commission;
        },

        complimentaryItems() {
            let compPrices = {};
            let days = 0;
            this.roomDetails.forEach((room) => {
                console.log(room.complementary);
                days = room.room_rent_details?.days || 0;
                room.complementary.forEach((comp) => {
                    compPrices[comp.title] = compPrices[comp.title] ? (compPrices[comp.title] + comp.price)
                        : comp.price;
                })
            })
            // Object.keys(compPrices).forEach((c) => {
            //     compPrices[c] = compPrices[c] * days
            // })

            return compPrices;
        },

        complimentaryPrice() {
            let complementaryPrice = 0;
            this.complimentaryItems && Object.keys(this.complimentaryItems).forEach((comp) => {
                complementaryPrice += this.complimentaryItems[comp];
            })

            return complementaryPrice;
        },

        totalAdditionalBillingAmt() {
            return this.complimentaryPrice + this.totalRoomRentsDetails.mealPlanCharge + this.totalRoomRentsDetails.additionalTax +
                this.totalRoomRentsDetails.totalBedAmt + this.totalRoomRentsDetails.totalExtraPersonAmt
        },

        remainingAmt() {
            let amount = this.netPayableAmount > 0 ? this.netPayableAmount - parseFloat(this.creditAmount || 0) - parseFloat(this.creditAmount1 || 0) - parseFloat(this.bank_charges || 0) - parseFloat(this.bank_charges1 || 0) - parseFloat(this.specialDiscount || 0) : 0;
            this.form.actual_remaining_amount = amount;
            return Math.round(amount);
        },

        netPayableAmount() {
            let advance = this.checkOutData.advance_amount;
            this.checkOutData.customer.forEach((data) => {
                if (data.booking_status == 4) advance = 0;
            })

            return this.checkOutData.remaining_amount + this.totalAdditionalBillingAmt + this.totalRoomRentsDetails.totalAmount - this.roomDiscount + parseFloat(this.extra_charge || 0)
                - advance + parseFloat(this.restaurantNetAmount)
        },
    },
    watch: {
        specialDiscountAmount(currentValue) {
            if (this.specialDiscountType?.id == 1) {
                this.specialDiscount = currentValue;
            } else {
                this.specialDiscount = ((this.netPayableAmount * currentValue) / 100);
            }
        },
    },
    mounted() {
        this.getBookingItems();
        this.getCurrency();
    },
    methods: {
        async getCurrency() {
            await axios.get('/api/default-currency').then((response) => {
                this.CURRENCY = response?.data?.data?.symbol;
            });
        },
        checkLimit(event) {
            let inputValue = event.target.value;
            let remainAmount = this.netPayableAmount - parseFloat(this.specialDiscount || 0) - parseFloat(this.creditAmount1 || 0) - parseFloat(this.bank_charges || 0) - parseFloat(this.bank_charges1 || 0);
            if (inputValue > (1 + remainAmount)) {
                if (inputValue - remainAmount > 0) this.creditAmount = Math.ceil(1 + remainAmount);
                else this.creditAmount = Math.ceil(remainAmount)
            }
        },
        checkLimit1(event) {
            let inputValue = event.target.value;
            let remainAmount = this.netPayableAmount - parseFloat(this.specialDiscount || 0) - parseFloat(this.creditAmount || 0) - parseFloat(this.bank_charges || 0) - parseFloat(this.bank_charges1 || 0);
            if (inputValue > (1 + remainAmount)) {
                if (inputValue - remainAmount > 0) this.creditAmount1 = Math.ceil(1 + remainAmount);
                else this.creditAmount1 = Math.ceil(remainAmount)
            }
        },
        bankChargesLimit(event) {
            let inputValue = event.target.value;
            let remainAmount = this.netPayableAmount - parseFloat(this.specialDiscount || 0) - parseFloat(this.creditAmount || 0) - parseFloat(this.creditAmount1 || 0) - parseFloat(this.bank_charges1 || 0);
            if (inputValue > (1 + remainAmount)) {
                if (inputValue - remainAmount > 0) this.bank_charges = Math.ceil(1 + remainAmount);
                else this.bank_charges = Math.ceil(remainAmount)
            }
        },
        bankChargesLimit1(event) {
            let inputValue = event.target.value;
            let remainAmount = this.netPayableAmount - parseFloat(this.specialDiscount || 0) - parseFloat(this.creditAmount || 0) - parseFloat(this.creditAmount1 || 0) - parseFloat(this.bank_charges || 0);
            if (inputValue > (1 + remainAmount)) {
                if (inputValue - remainAmount > 0) this.bank_charges1 = Math.ceil(1 + remainAmount);
                else this.bank_charges1 = Math.ceil(remainAmount)
            }
        },
        async getLedgerListData() {
            this.payment_method_form.ledger_type = '';
            this.payment_method_form.bank_only = 1;
            const { data } = await this.payment_method_form.post(
                window.location.origin + '/api/account/ledger/list');
            this.paymentModeData = data.data;
            this.selectedPaymentMethod = this.paymentModeData[0];

            /*set default value into extra payment method*/
            this.paymentModeData1 = this.paymentModeData.filter(item => item.code !== this.paymentModeData[0].code);
            this.selectedPaymentMethod1 = this.paymentModeData1[0];
        },

        /*set dropdown for extra payment*/

        setDropDownData(paymentId, paymentData) {
            this.paymentModeData1 = paymentData.filter(item => item.code !== paymentId.code);
            this.selectedPaymentMethod1 = this.paymentModeData[0];
        },
        totalRent(room) {
            return room.rent_per_day * room.days;
        },
        totalAmount(room) {
            return room ? room.rent_per_day * room.days + this.totalTax(room) : 0;
        },
        totalTax(room) {
            let total = 0;
            room.tax_details && room.tax_details.forEach(tax => {
                total += tax.amount * room.days;
            })
            return total;
        },
        async changeSelectedRooms() {
            this.roomDetails = [];
            this.processing = true;
            await this.selectedRooms.forEach((room) => {
                this.getCheckoutRoomsDetails(room);
            })
            this.processing = false;
        },
        async getBookingItems() {
            let slug = this.$route?.params?.slug;
            this.$store.state.operations.loading = true;
            await this.$store.dispatch('operations/getCheckOutData', {
                path: `/api/booking/check-out/view/${slug}`
            })
            this.checkOutData = this.checkOutItems;
            this.tax_included = this.checkOutData.tax_included;
            this.restaurantOrderDetails = this.checkOutItems?.restaurantOrderDetails || null;
            this.restaurantNetAmount = this.checkOutItems?.restaurantOrderDetails?.total || 0;
            this.restaurantOrderId = this.checkOutItems?.restaurantOrderDetails?.order_id || [];
            this.selectedRooms = [];
            this.invoicePersons = this.checkOutData.invoicePersons
            this.hotel_id = this.checkOutData.hotel_id
            this.invoicePersons.forEach((value, index) => {
                if (index === 0) this.selectedInvoicePerson = value;
                if (value.agency) {
                    this.selectedInvoicePerson = value;
                }
            })
            await this.checkOutData.customer.forEach((item) => {
                if (item.booking_status != 4) {
                    this.selectedRooms.push(item.id)
                }
            })
            await this.changeSelectedRooms();
        },

        async getCheckoutRoomsDetails(roomId) {
            this.$store.state.operations.loading = true;
            await this.$store.dispatch('operations/getCheckoutRoomsDetails', {
                path: `/api/booking/check-out/room/${roomId}?hotel_id=${this.hotel_id}`
            })
            this.roomDetails.push(this.checkoutRoomsDetails);
        },
        async submit() {
            this.submitLoader = true;
            /*Start Allowing Customer Checkout without pay full amount (if allow then uncomment below code)*/
            // if (!this.checkOutData.booking_source && this.remainingAmt > 1) {
            //     this.submitLoader = false;
            //     return toast.fire({ type: 'error', title: this.$t('check_out.index.payment_error') });
            // }
            /*End Allow Customer Checkout without pay full amount*/
            if (this.selectedRooms.length <= 0) {
                this.submitLoader = false;
                return toast.fire({ type: 'error', title: this.$t('common.room_error') });
            }
            let id = this.$route?.params?.slug;
            this.form.id = id;
            this.form.invoicePerson = this.selectedInvoicePerson?.id;
            this.form.extra_charge = this.extra_charge;
            this.form.extra_charge_comment = this.checkOutData.extra_charge_comment;
            this.form.advance_remarks = this.checkOutData.advance_remarks;
            this.form.credit_amount = this.creditAmount;
            this.form.credit_amount1 = this.creditAmount1;
            this.form.bank_charges = this.bank_charges;
            this.form.bank_charges1 = this.bank_charges1;
            this.form.special_discount = this.specialDiscount;
            this.form.special_discount_rate = this.specialDiscountAmount;
            this.form.special_discount_type = this.specialDiscountType?.id || null;
            this.form.credit_payment_method = this.selectedPaymentMethod?.id || null;
            this.form.credit_payment_method1 = this.selectedPaymentMethod1?.id || null;
            this.form.selected_rooms = this.selectedRooms;
            this.form.remaining_amount = this.remainingAmt;
            this.form.restaurantTotal = this.restaurantNetAmount;
            this.form.restaurantOrderId = this.restaurantOrderId;
            this.form.is_extra_payment_method = this.addExtraPaymentMethod;

            await this.form.post(window.location.origin + '/api/booking/check-out/' + id).then((res) => {
                toast.fire({
                    type: 'success',
                    title: 'Checkout Successfully',
                    // title: this.$t('booking.edit.success_msg'),
                });

                if (res.data.data?.invoice) {
                    return this.$router.push({ name: 'invoices.show', params: { slug: res.data.data?.invoice } });
                }
                this.$router.push({ name: 'booking' });
            }).catch((err) => {
                let message = err.response?.data?.message || this.$t('common.error_msg');
                toast.fire({ type: 'error', title: message })
            });

            this.submitLoader = false;
        }
    },
    created() {
        this.getLedgerListData();
    }
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

:deep(.multiselect__single) {
    text-overflow: ellipsis !important;
    overflow: hidden !important;
}

.addition_table td {
    border: none;
    font-size: 14px;
}

.card-header {
    border-bottom: 1px solid #e9e9e9;
}

.card-main {
    border: 1px solid #e9e9e9;
    padding: 10px 15px;
    border-radius: 5px;
}

.card-main p {
    font-size: 14px;
}

.form-check-label {
    cursor: pointer;
}

.card {
    box-shadow: 0 7px 9px rgb(197 197 197 / 50%);
}

.table-container tr th {
    background: #f7f7f7;
    color: #4a4a4a;
}

.border-b {
    border-bottom: 1px solid #e9e9e9;
}

.border-t {
    border-top: 1px solid #e9e9e9;
}

.table-bg {
    background: #eaeaea;
}

.text-color {
    color: #959595;
}

.custom-width {
    width: 230px;
    font-weight: 500;
    font-size: 14px;
}

.input-width {
    width: 68%;
    border: 1px solid #e5e5e5;
    border-radius: 2px;
    height: 26px;
}

.form-textarea {
    width: 100%;
    border: 1px solid #e5e5e5;
    border-radius: 2px;
}

.custom-width-input {
    width: 160px;
    font-size: 14px;
}

.fs-14 {
    font-size: 14px;
}

.form-input {
    border: 0;
    background: transparent;
    border-bottom: 1px solid #dddddd;
    color: #535353;
}

.form-textarea::placeholder {
    font-size: 14px;
}

.form-control {
    border-radius: 0;
    height: unset;
}

.rest_table td,
.rest_table th {
    border: none;
    padding: 3px;
    font-size: 14px;
}

.addition_table td {
    padding: 5px;
}

.create-btn {
    cursor: pointer;
}</style>
