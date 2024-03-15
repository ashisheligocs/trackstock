<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent"/>
        <!-- breadcrumbs end -->
      <div v-if="requireRefreshMessage" class="d-flex justify-content-center position-relative" style="top: 10rem">
        <div class="z-5 text-bold text-lg" role="status">
          <span>Please refresh the page.</span>
        </div>
      </div>
        <div v-else-if="initialLoading" class="d-flex justify-content-center position-relative">
            <div class="spinner-border z-5" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ pageTitle }} </h3>
                        <router-link :to="{ name: 'booking' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left"/> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveBooking" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="hotel" class="d-block">{{ $t('sidebar.hotel') }}
                                        <span class="required">*</span></label>
                                    <multiselect v-if="hotelItems" @remove="changeHotel" @select="changeHotel" v-model="hotel_id" :disabled="!!slugdata" :options="hotelItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a hotel" class="form-control" label="hotel_name" track-by="hotel_name" :class="{ 'is-invalid': form.errors.has('hotel_id') }"></multiselect>
                                    <has-error :form="form" field="hotel_id"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="check_in_date" class="d-block">CheckIn - Checkout date
                                        <span class="required">*</span></label>
                                    <date-range-picker
                                            ref="bookingeditPicker"
                                            opens="right"
                                            :singleDatePicker="false"
                                            :autoApply="true"
                                            v-model="dateRange"
                                            :ranges="false"
                                            @update="updateValues"
                                            class="w-100"
                                    >
                                        <template v-slot:input="picker" style="min-width: 350px">
                                            {{ check_in_date | startDate }} -
                                            {{ check_out_date | endDate }}
                                        </template>
                                    </date-range-picker>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="total_day" class="d-block">{{ $t('common.total_days') }}</label>
                                    <input v-if="check_in_date && check_out_date" id="total_day" v-model="totalDays" type="text" class="form-control" disabled name="total_day" :placeholder="$t('common.total_days')"/>
                                    <input v-else id="total_day" v-model="totalDaysStatic" type="text" class="form-control" disabled name="total_day" :placeholder="$t('common.total_days')"/>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="arrivalFrom" class="d-block">{{ $t('common.arrival_form') }}
                                        </label>
                                    <input id="arrivalFrom" v-model="arrivalForm" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.arrival_form_placeholder')"/>
                                    <has-error :form="form" field="arrival_from"/>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="bookingtypetitle" class="d-block">GRN Number</label>
                                    <input id="bookingtypetitle" v-model="puposeOfVisit" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" placeholder="Enter GRN Number"/>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="booked_by" class="d-block">{{ 'Booking source' }}</label>
                                    <multiselect v-if="bookedByList" v-model="bookedBy" :options="bookedByList" :show-labels="false" tag-placeholder="Add this as new tag" :custom-label="customBookedByLabel"
                                                 placeholder="Search a booking by" class="form-control" label="name" track-by="name" :class="{ 'is-invalid': form.errors.has('booked_by') }"></multiselect>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="hotel" class="d-block">{{ $t('common.booked_status') }}
                                        <span class="required">*</span></label>
                                    <multiselect v-if="bookedStatusData" v-model="client_booking_status_id" :options="bookedStatusData" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a booked status" class="form-control" label="name" track-by="name"></multiselect>
                                    <has-error :form="form" field="client_booking_status"/>
                                </div>
                                <div v-for="(item, index) in roomcategoryArray" :key="index + '1'" class="form-group col-md-12 room-booking-form px-4 py-3 mt-3">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label for="room_hotel_cat_lsit" class="d-block">{{
                                                $t('sidebar.room_hotel_cat_lsit') }}
                                                <span class="required">*</span></label>
                                            <multiselect v-if="hotelCategoryItems" @select="roomCategorySelect(index, $event)" v-model="item.room_booking_id" :options="hotelCategoryItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Room category" class="form-control" label="room_category_name" track-by="room_category_name" :class="{ 'is-invalid': form.errors.has('room_booking_id') }"></multiselect>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="room_number" class="d-block">{{ $t('common.room_number') }}
                                                <span class="required">*</span></label>
                                            <multiselect v-if="item.roomNumber" @select="roomNumberSelect(item, item.room_number_id)" v-model="item.room_number_id" :options="item.roomNumber" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Room number" class="form-control" label="room_name" track-by="room_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="adult" class="d-block">#{{ $t('common.adult') }}</label>
                                            <input id="adult" min="0" :max="item.adult" :value="item.adult" @input="limitAdult($event, item, index)" type="number" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.adult_placeholder')"/>
                                            <!--                                            <p v-if="item.adultLimit">Max {{item.adultLimit}}</p>-->
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="children" class="d-block">#{{ $t('common.children') }}</label>
                                            <input id="children" min="0" :max="item.children" :value="item.children" @input="limitChildren($event, item, index)" type="number" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.children_placeholder')"/>
                                            <!--                                            <p v-if="item.childrenLimit">Max {{item.childrenLimit}}</p>-->
                                        </div>
                                        <div class="form-group" :style="index != 0 ? 'width: 12.49%' : 'width: 16.66%'">
                                            <label for="infant" class="d-block">#{{ $t('common.infant') }}</label>
                                            <input id="infant" min="0" :max="item.infant" :value="item.infant" @input="limitInfant($event, item, index)" type="number" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.infant_placeholder')"/>
                                            <!--                                            <p v-if="item.infantLimit">Max {{item.infantLimit}}</p>-->
                                        </div>
                                        <div class="form-group" style="width: 4%">
                                            <button @click="removeItem(index)" v-if="index != 0" type="button" class="btn btn-danger float-right close-icon">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="card-body p-0 position-relative">
                                            <div id="printMe" class="table-responsive table-custom mt-3">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ $t('common.s_no') }}</th>
                                                        <th>{{ $t('common.room_no') }}</th>
                                                        <th>{{ $t('sidebar.room_hotel_cat_lsit') }}</th>
                                                        <th>{{ $t('common.room_rate') }}</th>
                                                        <th>{{ $t('common.gst') }}</th>
                                                        <th>{{ $t('common.subtotal') }}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <span>{{index+1}}</span>
                                                        </td>
                                                        <td>
                                                            <div v-if="item?.room_number_id?.room_name" class="d-flex align-items-center">
                                                                {{ item?.room_number_id?.room_name }}
                                                            </div>
                                                            <div v-else>
                                                                No available room.
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div v-if="item?.room_booking_id?.room_category_name" class="d-flex align-items-center">
                                                                {{ item?.room_booking_id?.room_category_name }}
                                                            </div>
                                                            <div v-else>
                                                                No available category.
                                                            </div>
                                                        </td>
                                                        <td>
                                                          <input v-if="item?.room_rate !== undefined && isTextBoxShow"
                                                                 type="number" :value="(item?.room_rate || 0)" step="0.01"
                                                                 @change="changeRoomRateValue($event, item),isTextBoxShow=false">
                                                          <span v-if="item?.room_rate !== undefined && isTextBoxShow" class="ml-2" style="cursor: pointer" @click="isTextBoxShow=false"><i class="fas fa-window-close"></i></span>
                                                          <span v-if="!isTextBoxShow">{{ (item?.room_rate || 0) | twoPoints}}
                                                              <span class="ml-2" style="cursor: pointer" @click="isTextBoxShow=true"><i class="fas fa-pen"></i></span></span>

                                                          <div v-if="item?.room_rate">
                                                            <p class="mb-0 mt-1">{{ item?.room_rate | twoPoints }} * {{ totalDays }}</p>
                                                            <p class="font-weight-bold mb-0"> = {{ item.room_rate * totalDays | twoPoints }}</p>
                                                          </div>
                                                          <div v-else>
                                                            0.00
                                                          </div>

                                                        </td>
                                                        <td>
                                                            <div v-if="item?.room_rate">
                                                                <p class="mb-0" v-for="(gstItem, index) in item.gstRate" :key="index + '2'">
                                                                    {{ gstItem.tax_name.code }} =
                                                                    {{ ((item.room_rate * totalDays) * gstItem.tax_name.rate / 100) | twoPoints }} </p>
                                                            </div>
                                                            <p v-if="item.room_rate" class="font-weight-bold mb-0">= {{
                                                                ((item.room_rate * totalDays) * item.total_tax / 100) | twoPoints
                                                                }} </p>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.room_rate">
                                                                <p class="mb-0">{{ ((item.room_rate * totalDays) + ((item.room_rate * totalDays) * item.total_tax / 100)) | twoPoints }}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="index == 0">
                                                        <td colspan="5">
                                                            <div v-if="slugdata" class="col-12" style="margin-top: -2rem">
                                                                <div class="d-flex justify-content-between"
                                                                     style="margin-top: 2rem">
                                                                    <span class="font-weight-bold">Customer Info.</span>
                                                                    <div class="mr-4 mt-1 dropdown show">
                                                                        <a class="btn btn-primary float-right plus-icon" @click="clientPlusClick" role="button" data-toggle="modal" data-target="#newClient">
                                                                            <i class="fas fa-times" style="transform: rotate(45deg);"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                <div v-if="clientItemsData.length"
                                                                     class="d-flex justify-content-between px-4 py-3 border-b-gray-1">
                                                                    <label>Name</label>
                                                                    <label>Phone</label>
                                                                    <label>Action</label>
                                                                </div>
                                                                <div v-for="(clientItem, key) in clientItemsData"
                                                                     :key="key + '3'"
                                                                     class="d-flex justify-content-between px-4 py-1 align-items-center border-b-gray-1">
                                                                    <div style="width: 180px">
                                                                        <label :for="clientItem.id">{{
                                                                            clientItem.clientName }}</label>
                                                                    </div>
                                                                    <div class="w-25">
                                                                        {{ clientItem.clientPhone }}
                                                                    </div>
                                                                    <div>
                                                                        <button @click="removeClientItemEdit(key)"
                                                                                type="reset"
                                                                                class="btn btn-danger close-icon">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="row d-flex align-items-center mb-4">
                                                                <div>
                                                                    <p class="mb-0 mr-1">{{ $t('sidebar.meal_plan')
                                                                        }}</p>
                                                                </div>
                                                                <div>
                                                                    <multiselect v-if="item.mealListData" @remove="mealPlanData(item, item.meal_plan_id, $event)" @select="mealPlanData(item, item.meal_plan_id, $event)" v-model="item.meal_plan_id" :options="item.mealListData" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Meal plan" class="form-control mr-2" style="width: 160px;" label="shortName" track-by="shortName" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                                                </div>
                                                                <div>
                                                                    <input id="bookingtypetitle" @input="mealPersonChange(item)" v-model.number="item.extra_meal_plan" type="number" class="form-control col-md-10" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.meal_plan_placeholder')" :disabled="item.meal_plan_id && item.meal_plan_id.length === 0"/>
                                                                </div>
                                                                <div>
                                                                    <div class="d-flex align-items-center" v-if="item.extra_meal_plan">
                                                                        = {{ item.meal_plan_price | twoPoints }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="ml-n2 text-secondary mt-4">Enter number of person after selecting meal plan to get proper amount.</p>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.meal_plan_price && item.extra_meal_plan">
                                                                <p class="mb-0" v-if="item.extra_meal_plan || item.meal_plan_price">
                                                                   {{ totalDays }} * {{ item.extra_meal_plan }} * {{ item.meal_plan_price | twoPoints }}</p>
                                                                <p v-if="item.meal_plan_price && item.extra_meal_plan" class="font-weight-bold mb-0">
                                                                    = {{ (item.meal_rate_extra_price * totalDays) | twoPoints }}</p>
                                                            </div>
                                                            <div v-else-if="item.extra_meal_plan">
                                                                <p v-if="item.meal_plan_id" class="font-weight-bold mb-0">
                                                                    = {{ item.meal_plan_price | twoPoints }} * {{ totalDays }}</p>
                                                                <p v-if="item.meal_plan_id" class="font-weight-bold mb-0">
                                                                    = {{ (item.meal_plan_price * totalDays) | twoPoints }}</p>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.meal_plan_id && item.extra_meal_plan">
                                                                <p class="mb-0" v-for="(taxRateItem,index) in item.meal_tax_rate" :key="index+'7'">
                                                                    {{ taxRateItem.tax_name.code }} =
                                                                    {{ (((item.meal_rate_extra_price * totalDays) ) * taxRateItem.tax_name.rate / 100) | twoPoints }} </p>
                                                            </div>
                                                            <p v-if="item.meal_plan_id && item.extra_meal_plan" class="font-weight-bold mb-0">=
                                                                {{ (((item.meal_rate_extra_price * totalDays) ) * item.meal_sum_tax_rate / 100) | twoPoints }}</p>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.meal_plan_id">
                                                                <p class="mb-0">{{ ((item.meal_rate_extra_price *totalDays) + (((item.meal_rate_extra_price *totalDays) ) * item.meal_sum_tax_rate / 100)) | twoPoints}}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="row d-flex align-items-center">
                                                              <div>
                                                                    <p class="mb-0 mr-3">{{ $t('common.add_services')
                                                                        }}</p>
                                                                </div>
                                                                <div>
                                                                    <multiselect v-if="item.facilitiesListData" @remove="roomFacilitySelect(item, item.room_facility_id, $event)" @select="roomFacilitySelect(item, item.room_facility_id, $event)" v-model="item.room_facility_id" :options="item.facilitiesListData" :multiple="true" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Add services" class="form-control w-100" label="facility_name" track-by="facility_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.room_facility_id?.length > 0" class="d-flex align-items-center">
                                                                <p class="mb-0" v-for="(facilityItem,index) in item.facilities_price" :key="index+'4'">
                                                                    {{ facilityItem.price }}
                                                                    <span v-if="item.facilities_price?.length != (index + 1)">+</span>
                                                                </p>
                                                            </div>
<!--                                                            <p v-if="item.room_facility_id?.length > 0" class="mb-0 font-weight-bold">-->
<!--                                                                = {{ item.facilities_rate_price }}</p>-->
                                                            <p v-if="item.room_facility_id?.length > 0" class="mb-0 font-weight-bold">
                                                                 {{ item.facilities_rate_price | twoPoints }} * {{ totalDays }} </p>
                                                            <p class="font-weight-bold mb-0">= {{ (item.facilities_rate_price * totalDays) | twoPoints }}</p>
<!--                                                            <p v-if="item.room_facility_id?.length > 0" class="mb-0 font-weight-bold">-->
<!--                                                                </p>-->
                                                        </td>
                                                        <td>
                                                            <div v-if="item.room_facility_id?.length > 0">
                                                                <p v-for="(facilityItem,main_index) in item.facilities_price" :key="main_index+'5'" class="mb-0 mt-1">
                                                                        <span v-for="(taxRateItems, index) in facilityItem.tax_rate" :key="index+'6'" class="d-block">
                                                                            {{ taxRateItems.taxname.code }} =
                                                                            {{ (((item.facilities_rate_price * totalDays) * taxRateItems.taxname.rate) / 100) | twoPoints }}
                                                                        </span>
                                                                </p>
                                                                <p class="font-weight-bold mb-0">= {{
                                                                    (((item.facilities_rate_price * totalDays) * item.facility_gst_rate_price) / 100) | twoPoints }}</p>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.room_facility_id?.length > 0">
                                                                <p class="mb-0">{{ ((item.facilities_rate_price * totalDays) + (((item.facilities_rate_price * totalDays) * item.facility_gst_rate_price) / 100)) | twoPoints }}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label for="check_in_date" class="d-block">{{
                                                                        $t('common.extra_bed') }} </label>
                                                                    <input id="bookingtypetitle" @change="extraBedRateChange($event, item)" v-model="item.extra_bed" type="number" min="0" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.extra_bed_placeholder')"/>
                                                                    <p class="text-primary" v-if="bedCapacityError">Max allowed bed are {{item.extra_bed_capacity || 0}}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.extra_bed">
                                                                <p class="mb-0">{{ item.extra_bed }} * {{ totalDays }} * {{ item.extra_bed_rate | twoPoints }}</p>
                                                                <p class="font-weight-bold mb-0">= {{
                                                                    (item.extra_bed_rate_price * totalDays) | twoPoints }}</p>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.extra_bed">
                                                                <p class="mb-0" v-for="(gstRateItem, index) in item.gstRate" :key="index+'8'">
                                                                    {{ gstRateItem.tax_name.code }} =
                                                                    {{ ((item.extra_bed_rate_price * totalDays) * gstRateItem.tax_name.rate / 100) | twoPoints }} </p>
                                                            </div>
                                                            <p v-if="item.extra_bed" class="font-weight-bold mb-0">= {{
                                                                ((item.extra_bed_rate_price * totalDays) * item.total_tax / 100) | twoPoints }}</p>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.extra_bed">
                                                                <p class="mb-0">{{ ((item.extra_bed_rate_price * totalDays) + ((item.extra_bed_rate_price * totalDays) * item.total_tax / 100)) | twoPoints }}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label for="check_in_date" class="d-block">{{
                                                                        $t('common.extra_person') }}</label>
                                                                    <input id="bookingtypetitle" @change="extraPersonChange($event, item)" v-model.number="item.extra_person" type="number" min="0" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.extra_person_placeholder')"/>
                                                                    <p class="text-primary" v-if="bedCapacityError">Max allowed extra persons are {{item.extra_person_capacity || 0}}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.extra_person">
                                                                <p class="mb-0">{{ item.per_person }} * {{ totalDays }} * {{ item.extra_person }}</p>
                                                                <p class="font-weight-bold mb-0">= {{
                                                                    (item.extra_person_rate_price * totalDays) | twoPoints }}</p>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.extra_person">
                                                                <p v-for="(gstItem, index) in item.gstRate" :key="index+'9'" class="mb-0">
                                                                    {{ gstItem.tax_name.code }} =
                                                                    {{ (((item.extra_person_rate_price * totalDays) ) * (gstItem.tax_name.rate) / 100) | twoPoints }} </p>
                                                            </div>
                                                            <p v-if="item.extra_person" class="font-weight-bold mb-0">=
                                                                {{ (((item.extra_person_rate_price * totalDays) ) * (item.total_tax) / 100) | twoPoints }}</p>
                                                        </td>
                                                        <td>
                                                            <div v-if="item.extra_person">
                                                                <p class="mb-0">{{( (item.extra_person_rate_price *totalDays) + (((item.extra_person_rate_price *totalDays) ) * (item.total_tax) / 100)) | twoPoints }}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr style="background-color: #f2f4f5;">
                                                        <td colspan="4">
                                                            <p class="mb-0 font-weight-bold d-flex justify-content-end">
                                                                {{ $t('common.sub_total') }}</p>
                                                        </td>
                                                        <td></td>
                                                        <td>
                                                            <p class="mb-0 font-weight-bold"> {{ ((item.room_rate || 0)
                                                                * totalDays) + (((item.room_rate || 0) * totalDays) *
                                                                item.total_tax / 100) + (item.meal_rate_extra_price *
                                                                totalDays) + (((item.meal_rate_extra_price * totalDays)
                                                                ) * item.meal_sum_tax_rate / 100) +
                                                                (item.facilities_rate_price * totalDays) +
                                                                (((item.facilities_rate_price * totalDays) *
                                                                item.facility_gst_rate_price) / 100) +
                                                                (item.extra_bed_rate_price * totalDays) +
                                                                ((item.extra_bed_rate_price * totalDays) *
                                                                item.total_tax / 100) + (item.extra_person_rate_price *
                                                                totalDays) + (((item.extra_person_rate_price *
                                                                totalDays)) * (item.total_tax) / 100) | withCurrency}} </p>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="oldClient" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Old Client</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div>
                                                        <form role="form" @submit.prevent="saveOldClient('item')" @keydown="clientForm.onKeydown($event)">
                                                            <div>
                                                                <div v-if="isLoadingClient" class="d-flex justify-content-center position-relative">
                                                                    <div class="spinner-border z-5" role="status">
                                                                        <span class="sr-only">Loading...</span>
                                                                    </div>
                                                                </div>
                                                                <div v-else-if="isSuggestionBox" :class="items?.length > 0 ? 'bg-white p-3' : ''">
                                                                    <div class="form-group">
                                                                        <label for="customer" class="d-block">{{ 'Select customer' }}</label>
                                                                        <multiselect v-if="items" @select="clientChange" @remove="clientChange" v-model="selectedOldCustomer" :options="items || []"
                                                                                     :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a customer" class="form-control"
                                                                                     :custom-label="({ name, phone }) => `${name} - ${phone}`" label="name" track-by="id"></multiselect>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- /.card-body -->
                                                            <div class="card-footer">
                                                                <v-button :loading="form.busy" class="btn btn-primary">
                                                                    <i class="fas fa-save"/> {{ $t('common.save') }}
                                                                </v-button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mt-3">
                                            <button v-if="roomcategoryArray.length == (index + 1)" @click="addRoomCategory" type="button" class="btn btn-primary float-right plus-icon">
                                                <i class="fas fa-times" style="transform: rotate(45deg);"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-12 row">
                                    <div class="col-md-4">
                                        <div style="background-color: #f8f9fa; border-radius: 6px;" class="p-3 h-100 room-form_card">
                                            <div>
                                                <label for="totalRoom" class="font-weight-bold">{{
                                                    $t('common.total_room_amount') }}</label>
                                                <p class="mb-0 ml-3">{{ final_room_rate_price | withCurrency }}</p>
                                            </div>
                                            <div class="mt-4">
                                                <label for="roomRate" class="font-weight-bold">{{
                                                    $t('common.discount_room_rate') }}</label>
                                                <multiselect v-if="discountRoomRateData" v-model="discount_room_rate" :options="discountRoomRateData" :show-labels="false" tag-placeholder="Add this as new tag"
                                                             placeholder="Search a Discount room rate" class="form-control" label="title" track-by="title"
                                                             :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                            </div>
                                            <div class="mt-4 d-flex justify-content-between">
                                                <div class="col-md-6">
                                                    <label for="discount" class="font-weight-bold">{{
                                                        $t('common.discount')
                                                        }}</label>
                                                    <input id="bookingtypetitle" v-model="discount" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                           name="bookingtypetitle" :placeholder="$t('common.discount_placeholder')"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="discount" class="font-weight-bold">{{
                                                        $t('common.discount_amount') }}</label>
                                                    <p class="mb-0 ml-2 mt-2">{{ discount_amount | withCurrency }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label for="discount" class="font-weight-bold">{{
                                                    $t('common.net_room_amount') }}</label>
                                                <p class="mb-0 ml-2 mt-2">{{ (final_room_rate_price - discount_amount) | withCurrency }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="background-color: #f8f9fa; border-radius: 6px;" class="p-3 h-100 room-form_card">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t('common.room_amount') }}
                                                </p>
                                                <p class="mb-0">{{ final_room_rate_price | withCurrency }}</p>
                                                <!-- <p v-else class="mb-0">0</p> -->
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t('sidebar.meal_plan') }}
                                                </p>
                                                <p class="mb-0">{{ final_meal_rate_price | withCurrency }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t('common.add_services_amount') }}
                                                </p>
                                                <p class="mb-0">{{ final_facility_rate_price | withCurrency }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <p class="font-weight-bold mb-0">
                                                    Other Price (Ex person & bed)
                                                </p>
                                                <p class="mb-0">{{ final_ex_bed_person_rate_price | withCurrency }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t('common.total_amount_after_discount') }}
                                                </p>
                                                <p v-if="final_room_rate_price + final_meal_rate_price + final_facility_rate_price" class="mb-0">
                                                    {{ final_room_rate_price + final_meal_rate_price +
                                                    final_facility_rate_price + final_ex_bed_person_rate_price - discount_amount | withCurrency }}</p>
                                                <p v-else class="mb-0">0</p>
                                            </div>
                                          <div class="border-top mt-4 pt-2">
                                            <input id="include_gst" type="checkbox" v-model="tax_included">
                                            <label for="include_gst">Inclusive GST</label>
                                          </div>
                                            <div class="mt-2">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t('common.gst') }} </p>
                                                <p class="mb-0 text-muted font-weight-bold mt-3 mb-2" style="font-size: 14px;">
                                                    Total Gst</p>
                                                <div v-if="final_gst_rate" v-for="(item, index) in Object.keys(final_gst_rate)" :key="index + '12'" class="ml-3 mt-1">
                                                    <div v-if="final_gst_rate[item] > 0" class="d-flex justify-content-between align-items-center">
                                                        <p class="font-weight-bold text-muted mb-0">{{ item }}</p>
                                                        <p class="mb-0">{{ final_gst_rate[item] | withCurrency }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-4">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t('common.net_payable_amount') }} </p>
                                                <p class="mb-0">{{ netPayableRound(total_room_amount - discount_amount) | withCurrency }}</p>
                                            </div>
                                            <div></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="background-color: #f8f9fa; border-radius: 6px;" class="p-3 h-100 room-form_card">
                                            <div class="mt-4">
                                                <label for="roomRate" class="font-weight-bold">{{
                                                    $t('common.choose_payment_mode') }}</label>
                                                <multiselect v-if="paymentModeData" v-model="payment_mode_id" :options="paymentModeData" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search an option" class="form-control" label="ledger_name" track-by="ledger_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                            </div>
                                            <div class="mt-3">
                                                <label for="advance_amount" class="font-weight-bold">{{
                                                    $t('common.advance_amount') }}</label>
                                                <input id="advance_amount" :value="advance_amount" @input="advanceAmountLimit" type="text" class="form-control" name="advance_amount" :placeholder="$t('common.advance_amount_placeholder')"/>
                                            </div>
                                            <div class="mt-3">
                                                <label for="discount" class="font-weight-bold">{{
                                                    $t('common.pending_amount') }}</label>
                                                <input id="pending_amount" disabled :value="netPayableRound(pending_amount)" type="text" class="form-control" name="pending_amount" :placeholder="$t('common.pending_amount_placeholder')"/>
                                            </div>

                                            <div class="mt-3">
                                                <label for="advanceRemark" class="font-weight-bold">{{
                                                    $t('common.advance_remarks') }}</label>
                                                <textarea id="advanceRemark" rows="2" v-model="form.advance_remarks" type="text" class="form-control" name="advanceRemark" :placeholder="$t('common.advance_remarks_placeholder')"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100">
                                    <div class="mt-3 col-md-12">
                                        <label for="discount" class="font-weight-bold">{{ $t('common.note') }}</label>
                                        <textarea v-model="form.comments" name="w3review" rows="2" class="w-100 note-textarea p-3" :placeholder="$t('common.note_placeholder')" style="border-radius: 4px; border-color: #ced4da;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <v-button :loading="form.busy" class="btn btn-primary">
                                <i class="fas fa-save"/> {{ $t('common.save') }}
                            </v-button>
                            <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                                <i class="fas fa-power-off"/> {{ $t('common.reset') }}
                            </button>
                        </div>
                    </form>
                    <div class="modal fade" id="oldClientEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-width modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Select a customer</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        <form role="form" @submit.prevent="saveOldClient">
                                            <div>
                                                <div v-if="isLoadingClient" class="d-flex justify-content-center position-relative">
                                                    <div class="spinner-border z-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div v-else :class="items?.length > 0 ? 'bg-white p-3' : ''">
                                                    <div class="form-group">
                                                        <label for="customer" class="d-block">{{ 'Select customer' }}</label>
                                                        <multiselect v-model="selectedOldCustomer" @select="clientChange"
                                                                     @remove="clientChange" :options="items && Array.isArray(items) ? items : []"
                                                                     :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a customer"
                                                                     class="form-control" :custom-label="({ name, phone }) => `${name} - ${phone}`"
                                                                     label="name" track-by="id"></multiselect>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <v-button :loading="form.busy" class="btn btn-primary">
                                                    <i class="fas fa-save"/> {{ $t('common.save') }}
                                                </v-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="newClient" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-width modal-dialog-centered" role="document" style="max-width: 800px">
                            <div class="modal-content" style="max-width: 800px">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Select a Client</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        <form role="form" @submit.prevent="saveClient" @keydown="clientForm.onKeydown($event)">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="customer" class="d-block">{{ 'Search number' }}</label>
                                                        <multiselect v-if="items" @select="clientChange" @remove="clientChange" v-model="selectedOldCustomer" :options="items || []"  :taggable="true"
                                                                     @tag="addNewCustomer"
                                                                     :show-labels="false" tag-placeholder="Add this as new number" placeholder="Search a customer" class="form-control"
                                                                     :custom-label="({ name, phone }) => `${name ? name + '-' : ''} ${phone}`" label="name" track-by="id"></multiselect>
                                                      <p v-if="customerError" class="text-red">{{ customerError }}</p>
                                                      <has-error v-else :form="clientForm" field="phoneNumber"/>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="name">{{ $t('common.customer_name') }}
                                                            <span class="required">*</span></label>
                                                        <input id="name" v-model="clientForm.name" type="text" class="form-control" :class="{ 'is-invalid': clientForm.errors.has('name') }" name="name" :placeholder="$t('common.name_placeholder')"/>
                                                        <has-error :form="clientForm" field="name"/>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="identity">{{ $t('common.identity') }}
                                                            </label>
                                                        <input id="identity" v-model="clientForm.identity" type="text" class="form-control" :class="{ 'is-invalid': clientForm.errors.has('identity') }" name="identity" :placeholder="$t('common.identity_placeholder')"/>
                                                        <has-error :form="clientForm" field="identity"/>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="email">{{ $t('common.email') }}</label>
                                                        <input id="email" v-model="clientForm.email" type="email" class="form-control" :class="{ 'is-invalid': clientForm.errors.has('email') }" name="email" :placeholder="$t('common.email_placeholder')"/>
                                                        <has-error :form="clientForm" field="email"/>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label for="address">{{ $t('common.address') }}</label>
                                                        <textarea id="address" v-model="clientForm.address" class="form-control" :class="{ 'is-invalid': clientForm.errors.has('address') }" :placeholder="$t('common.address_placeholder')"/>
                                                        <has-error :form="clientForm" field="address"/>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label for="email">{{ $t('common.nationality') }}</label>
                                                        <input id="nationality" v-model="clientForm.nationality" type="nationality" class="form-control" :class="{ 'is-invalid': clientForm.errors.has('nationality') }" name="nationality" :placeholder="$t('common.nationality_placeholder')"/>
                                                        <has-error :form="clientForm" field="nationality"/>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label class="image_label" for="file-upload">Upload Id</label>
                                                        <input type="file" id="file-upload" multiple @change="handleFileUpload">
                                                        <div class="image-container">
                                                            <div v-for="(image, index) in images" :key="index" class="image-item">
                                                                <img :src="image.previewUrl" alt="Preview" class="profile-pic" >
                                                                <button class="remove-button" @click.stop="removeImage(index)">X</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <v-button :loading="clientForm.busy" class="btn btn-primary float-right">
                                                    <i class="fas fa-save"/> {{ $t('common.save') }}
                                                </v-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import {mapGetters} from 'vuex';
    import axios from 'axios';
    import Form from 'vform';
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import Multiselect from 'vue-multiselect';
    import _ from 'lodash';
    import moment from 'moment'
    import DateRangePicker from "vue2-daterange-picker";
    import i18n from "~/plugins/i18n";

    export default {
        // middleware: ['auth', 'check-permissions'],
        metaInfo () {
            return { title: this.$route?.params?.slug ? this.$t('booking.edit.page_title') : this.$t('booking.create.page_title') };
        },
        components: {
            DatePicker,
            Multiselect,
            DateRangePicker
        },
        computed: {
            ...mapGetters('operations',
                ['items', 'facilityItems', 'hotelItems', 'bedTypeItems', 'floorItems','mealItems','selectedHotel']),

            hotelCategoryItems() {
                return this.hotel_id?.roomCategories ? _.uniqBy(this.hotel_id?.roomCategories, 'id') : [];
            },
            pageTitle () {
                return this.$t('booking.edit.page_title');
            },
            breadcrumbsCurrent() {
                return this.$t('booking.edit.breadcrumbs_current');
            },
            breadcrumbs () {
                return [
                    {
                        name: 'booking.create.breadcrumbs_first',
                        url: 'home',
                    },
                    {
                        name: 'booking.create.breadcrumbs_second',
                        url: 'booking',
                    },
                    {
                        name: this.$t('booking.edit.breadcrumbs_active'),
                        url: '',
                    },
                ]
            },
            final_room_rate_price () {
                console.log(this.trigger)
                let finalRoomPrice = this.roomcategoryArray.map((el) => el.room_rate * this.totalDays);
                return _.sum(finalRoomPrice);
            },
            final_room_rate_with_gst () {
                console.log(this.trigger)
                let tax = this.roomcategoryArray.map((el) => this.slugdata ? el.room_tax : el.totalRoomGst);
                tax = _.sum(tax, 'tax');
                return this.final_room_rate_price + tax;
            },
            final_meal_rate_price () {
                console.log(this.trigger)
                let finalMealRatePrice = this.roomcategoryArray.map((el) => el.meal_rate_extra_price * this.totalDays);
                return _.sum(finalMealRatePrice);
            },
            final_facility_rate_price () {
                console.log(this.trigger)
                let finalFacilityRatePrice = this.roomcategoryArray.map(
                    (el) => {
                        return el.facilities_rate_price * this.totalDays
                    });
                return _.sum(finalFacilityRatePrice);
            },
            final_ex_bed_person_rate_price () {
                console.log(this.trigger)
                let finalBedPersonPrice = this.roomcategoryArray.map(
                    (el) => (el.extra_bed_rate_price + el.extra_person_rate_price) * this.totalDays);
                return _.sum(finalBedPersonPrice);
            },

            total_room_amount() {
              console.log(this.trigger)
                const test = [];
                let total = 0;
                this.roomcategoryArray.forEach((el) => {
                  let rate = (parseFloat(el.room_rate || 0) + parseFloat(el.meal_rate_extra_price || 0) +
                    parseFloat(el.facilities_rate_price || 0) + parseFloat(el.extra_bed_rate_price || 0) +
                    parseFloat(el.extra_person_rate_price || 0))*this.totalDays;

                  let taxRate = (((parseFloat(el.room_rate || 0) * this.totalDays) - ((el.discount || 0) || 0)) * (el.total_tax || 0) / 100) +
                    (((el.meal_rate_extra_price || 0) * this.totalDays) * (el.meal_sum_tax_rate || 0) / 100) +
                    (((el.facilities_rate_price || 0) * this.totalDays) * (el.facility_gst_rate_price || 0) / 100) +
                    (((el.extra_bed_rate_price || 0) * this.totalDays) * (el.total_tax || 0) / 100) +
                    (((el.extra_person_rate_price || 0) * this.totalDays) * (el.total_tax || 0) / 100);

                  test.push(parseFloat(rate) + parseFloat(taxRate));
                });
                if (test && test.length) {
                    test.forEach((el) => {
                        total += el;
                    });
                }
                return total;
            },

            final_gst_rate () {
                console.log(this.trigger)
                let gst = {};
                _.map(this.allVatRates, 'name').forEach((val) => gst[val] = 0);
                this.roomcategoryArray.forEach((room, index) => {
                    room.gstRate.forEach((rate) => {
                        if (room.room_rate) gst[rate.tax_name.name] += ((room.room_rate * this.totalDays) - (room.discount || 0)) *
                            rate.tax_name.rate / 100;
                        if (room.extra_bed) gst[rate.tax_name.name] += (room.extra_bed_rate_price * this.totalDays) *
                            rate.tax_name.rate / 100;
                        if (room.extra_person) gst[rate.tax_name.name] += (room.extra_person_rate_price *
                            this.totalDays) * rate.tax_name.rate / 100;
                    });

                    room.meal_tax_rate.forEach((rate) => {
                        if (room.meal_plan_id) gst[rate.tax_name.name] += (room.meal_rate_extra_price *
                            this.totalDays) * rate.tax_name.rate / 100;
                    });

                    if (room.room_facility_id?.length > 0) {
                        room.facilities_price?.forEach((item) => {
                            item.tax_rate?.forEach((rate) => {
                                gst[rate.taxname.name] += (room.facilities_rate_price * this.totalDays) *
                                    rate.taxname.rate / 100;
                            });
                        });
                        this.roomcategoryArray[index]['totalFacilityGst'] = ((room.facilities_rate_price *
                            this.totalDays)) * room.facility_gst_rate_price / 100;
                    }

                    if (room.meal_rate_extra_price) this.roomcategoryArray[index]['totalMealGst'] = ((room.meal_rate_extra_price *
                        this.totalDays)) * room.meal_sum_tax_rate / 100;
                });

                return gst;
            },
        },
        data: () => ({
            trigger: 0,
            room_price_with_gst: 0,
            initialLoading: false,
            BookingId: null,
            selectedOldCustomer: {},
            form: new Form({
                type: 1,
                customer: [
                    {
                        customer_id: '',
                        customer_id_request: null,
                    }],
                total_tax: '',
                booked_by: '',
                total_room: 1,
                client_booking_status: '',
                advance_amount: '',
                advance_remarks: '',
                paid_amount: '',
                total_price: '',
                payment_method: '',
                remarks: '',
                discount_amount: null,
                discount_reason: null,
                full_guest_name: null,
                special_request: null,
                comments: '',
                booking_date: '',
                check_in_date: '',
                check_out_date: '',
                hotel_id: '',
                booked_from: '',
                booking_source: '',
                arrival_from: '',
                bookingtypetitle: '',
                rooms: [],
                discount_type: '',
            }),
            roomsList: [],
            mealDataList: [],
            complementraysList: [],
            facilitiesListDataList: [],
            mealPlanDetailsList: [],
            extraMealPersons: [],
            facilitiesListData: [],
            loading: false,
            isLoadingClient: false,
            check_in_date: moment(new Date()).format("YYYY-MM-DD"),
            minDate: moment(new Date()).format("YYYY-MM-DD"),
            dateRange: {
                startDate: moment(new Date()).format("YYYY-MM-DD"),
                endDate: moment().add(1, "days").format("YYYY-MM-DD"),
            },
            processing: false,
            check_out_date: moment().add(1, "days").format("YYYY-MM-DD"),
            arrivalForm: '',
            puposeOfVisit: '',
            remarks: '',
            // total_room_amount: 0,
            allVatRates: [],
            bedCapacityError: false,
            personCapacityError: false,
           tax_included: false,
            roomcategoryArray: [
                {
                    id: 1,
                    original_id: null,
                    room_booking_id: '',
                    room_number_id: '',
                    roomNumber: [],
                    adult: '',
                    children: '',
                    infant: '',
                    room_rate: '',
                    total_tax: '',
                    gstRate: [],
                    totalRoomGst: 0,
                    per_person: 0,
                    totalExtraPersonGst: 0,
                    totalFacilityGst: 0,
                    totalMealGst: 0,
                    totalBedGst: 0,
                    clientItemsData: [],
                    meal_plan_id: '',
                    mealListData: [],
                    extra_meal_plan: 0,
                    meal_plan_price: '',
                    meal_rate_extra_price: '',
                    meal_tax_rate: [],
                    meal_sum_tax_rate: '',
                    facilitiesListData: [],
                    room_facility_id: '',
                    facilities_price: [],
                    facilities_rate_price: '',
                    facility_gst_rate_price: '',
                    extra_bed: '',
                    extra_bed_rate: '',
                    extra_bed_rate_price: '',
                    extra_person_capacity: '',
                    extra_bed_capacity: '',
                    extra_person: '',
                    extra_person_rate_price: '',
                },
            ],
            bookedByList: [],
            bookedBy: '',
            booked_by_form: new Form({
                leadsSupplierType: '',
            }),
            room_category_form: new Form({
                cat_id: '',
                hotel_id: '',
                check_in_date: ''
            }),
            room_number_form: new Form({
                id: '',
            }),
            booking_source: '',
            room_booking_id: '',
            roomNumber: [],
            room_number_id: '',
            room_facility_id: '',
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
            bookedStatusData: [
                {
                    id: 1,
                    name: 'Pending',
                },
                {
                    id: 2,
                    name: 'Hold',
                },
                {
                    id: 3,
                    name: 'Confirmed',
                },
                {
                    id: 4,
                    name: 'Available',
                },
            ],
            discount_room_rate: 0,
            payment_method_form: new Form({
                ledger_type: '',
            }),
            paymentModeData: [],
            payment_mode_id: '',
            discount: 0,
            discount_amount: 0,
            net_payable_amount: 0,
            advance_amount: 0,
            pending_amount: 0,
            meal_plan_form: new Form({
                meal_id: '',
                hotel_id: '',
            }),
            hotel_id: '',
            meal_list_form: new Form({
                hotel_id: '',
            }),
            facilities_list_form: new Form({
                hotel_id: '',
            }),
            facilities_single_form: new Form({
                id: '',
            }),
            facility_form: new Form({
                id: '',
            }),
            totalDays: 0,
            totalDaysStatic: 0,
            customerError: '',
            clientForm: new Form({
                name: '',
                email: '',
                phoneNumber: '',
                companyName: '',
                address: '',
                image: '',
                status: 1,
                hotel_id: {id: null}
            }),
            clientData: {},
            clientItemsData: [],
            isTextBoxShow: false,
            isSuggestionBox: false,
            client_booking_status_id: '',
            booking_status_main: 1,
            clientItemId: '',
            url: '',
            gstFacilitiRate: [],
            slugdata: '',
            bookingData: new Form({
                type: 2,
                customer: [
                    {
                        customer_id: '',
                        customer_id_request: null,
                    }],
                total_tax: '',
                booked_by: '',
                total_room: 1,
                client_booking_status: '',
                advance_amount: '',
                advance_remarks: '',
                paid_amount: '',
                final_gst_rate: {
                    'IGST 5%' : 0,
                    'SGST 5%': 0
                },
                total_price: '',
                payment_method: '',
                remarks: '',
                discount_amount: null,
                discount_reason: null,
                full_guest_name: null,
                special_request: null,
                comments: '',
                booking_date: '',
                check_in_date: '',
                check_out_date: '',
                hotel_id: '',
                booked_from: '',
                booking_source: '',
                arrival_from: '',
                bookingtypetitle: '',
                rooms: [],
                discount_type: '',
            }),
            images: [],
            requireRefreshMessage: false,
        }),
        async created () {
          /*Dont remove console from this component*/
            setTimeout(() => {
              if (this.initialLoading) {
                this.requireRefreshMessage = true;
              }
              }, 40000)
            await this.getBookingType();
            await this.getVatRates();
            await this.getHotelDataList();
            await this.getLedgerListData();
            await this.getBookingData();
            await this.getBookedBySupplier();
            await this.searchClientData();
            this.slugdata = this.$route?.params?.slug;

            // console.log = function () {};
        },
        watch: {
            selectedHotel: function () {
                this.getHotelDataList();
            },
            hotelItems(){
                   if (this.selectedHotel && this.selectedHotel !== 'all') {
                          this.hotelItems?.forEach((hotel) => {

                              if (hotel.id ==this.selectedHotel) {
                                  this.hotel_id = hotel
                                  this.changeHotel();
                              }
                          })
                      }
               },
            check_in_date: {
                handler: 'calculateDays',
            },
            check_out_date: {
                handler: 'calculateDays',
            },
            tax_included: {
              handler: 'setNewPrices',
            },
            // booking_source (currentValue) {
            //     this.booked_by_form.leadsSupplierType = currentValue?.id;
            //
            //     this.getBookedBySupplier();
            // },
            discount (currentValue) {
                if (this.discount_room_rate.title === "Percentage" && currentValue > 100){
                    this.discount = '';
                }else{
                    if (this.discount_room_rate.id == 1) {
                        this.discount_amount = this.discount;
                    } else {
                        this.discount_amount = ((this.final_room_rate_price * currentValue) / 100);
                    }
                    this.roomcategoryArray.forEach((el) => {
                        console.log(this.discount_amount, el.room_rate, this.totalDays, el.totalRoomGst, this.room_price_with_gst)
                        el.discount = this.discount_amount * ((el.room_rate * this.totalDays)) / this.final_room_rate_price ?? 0
                    });
                    this.pending_amount = ((this.total_room_amount - this.discount_amount) - this.advance_amount);
                    this.trigger += 1;
                }
            },
            advance_amount (currentValue) {
                this.pending_amount = ((this.total_room_amount - this.discount_amount) - currentValue);
            },
            hotel_id (currentValue) {
                this.getMealPlanData(currentValue.id);
                this.getRoomFacilityData(currentValue.id);
            },
            total_room_amount(value) {
                this.pending_amount = ((value - this.discount_amount) - this.advance_amount);
            },
            roomcategoryArray: {
                handler: async function () {
                    let roomPriceWithGst = this.roomcategoryArray.map(
                        (el) => {
                            return !this.initial && el.total_tax ? el.room_rate * this.totalDays + (el.room_rate * this.totalDays) * el.total_tax / 100 : 0
                        }
                    );
                    this.room_price_with_gst = _.sum(roomPriceWithGst);
                    if (this.discount_room_rate.id == 1) {
                        this.discount_amount = this.discount;
                    } else {
                        this.discount_amount = ((this.final_room_rate_price * this.discount) / 100);
                    }
                    this.roomcategoryArray.forEach((el) => {
                        el.discount = this.discount_amount * (( el.room_rate * this.totalDays))/this.final_room_rate_price ?? 0
                    });
                    console.log(this.total_room_amount, this.discount_amount, this.advance_amount, 'advance')
                    this.pending_amount = ((this.total_room_amount - this.discount_amount) - this.advance_amount);
                },
                deep: true,
            },
        },
        filters: {
            startDate(val) {
                return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.from");
            },
            endDate(val) {
                return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.to");
            },
        },
        methods: {
            netPayableRound(amount) {
              return amount ? Math.round(amount) : 0;
            },
            inclusiveTaxAmount(amount, taxRate) {
              let price = parseFloat(amount || 0);
              let rate = parseFloat(taxRate || 0);

              return  price * (100 / (100 + rate));
            },
            exclusiveTaxAmount(amount, taxRate) {
              let price = parseFloat(amount || 0);
              let rate = parseFloat(taxRate || 0);

              return  price * ((100 + rate)/100);
            },
            setNewPrices() {
              if (this.initialLoading) return;
              if (this.tax_included) this.setTaxInclusivePrice();
              else this.setTaxExclusivePrice();
            },
            setTaxInclusivePrice() {
              this.tax_included && this.roomcategoryArray.forEach((el) => {
                el.room_rate = this.inclusiveTaxAmount(el.room_rate, el.total_tax);
                el.meal_plan_price = this.inclusiveTaxAmount(el.meal_plan_price, el.meal_sum_tax_rate);
                el.meal_rate_extra_price = this.inclusiveTaxAmount(el.meal_rate_extra_price, el.meal_sum_tax_rate)
                el.facilities_rate_price = this.inclusiveTaxAmount(el.facilities_rate_price, el.facility_gst_rate_price);
                el.extra_bed_rate_price = this.inclusiveTaxAmount(el.extra_bed_rate_price, el.total_tax);
                el.extra_person_rate_price = this.inclusiveTaxAmount(el.extra_person_rate_price, el.total_tax);
              })
            },
            setTaxExclusivePrice() {
              !this.tax_included && this.roomcategoryArray.forEach((el) => {
                el.room_rate = this.exclusiveTaxAmount(el.room_rate, el.total_tax);
                el.meal_plan_price = this.exclusiveTaxAmount(el.meal_plan_price, el.meal_sum_tax_rate);
                el.meal_rate_extra_price = this.exclusiveTaxAmount(el.meal_rate_extra_price, el.meal_sum_tax_rate)
                el.facilities_rate_price = this.exclusiveTaxAmount(el.facilities_rate_price, el.facility_gst_rate_price);
                el.extra_bed_rate_price = this.exclusiveTaxAmount(el.extra_bed_rate_price, el.total_tax);
                el.extra_person_rate_price = this.exclusiveTaxAmount(el.extra_person_rate_price, el.total_tax);
              })
            },
            async updateValues() {
                this.check_in_date = this.dateRange.startDate = moment(this.dateRange.startDate).format(
                    "YYYY-MM-DD"
                );
                this.check_out_date = this.dateRange.endDate = moment(this.dateRange.endDate).format(
                    "YYYY-MM-DD"
                );
            },
            handleFileUpload(event) {
                const files = event.target.files;
                let error = false;
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    if (
                        files[i].size < 2111775 &&
                        (files[i].type === "image/jpeg" ||
                            files[i].type === "image/png" ||
                            files[i].type === "image/gif")
                    ) {
                        reader.onload = (e) => {
                            this.images.push({
                                file: files[i],
                                previewUrl: e.target.result
                            });
                        };
                        reader.readAsDataURL(files[i]);
                    } else {
                        error = true;
                        Swal.fire(
                            this.$t("common.error"),
                            this.$t("common.image_error"),
                            "error"
                        );
                    }
                }
                if (error) this.images = [];
            },
            removeImage(index) {
                this.images.splice(index, 1);
            },
            clientPlusClick (item = null) {
                if (item) {
                    this.clientItemId = item.id;
                }
                this.selectedOldCustomer = null;
                this.images = [];
            },
            advanceAmountLimit(event) {
              if (event.target.value > Math.round(this.total_room_amount - this.discount_amount))  {
                  return this.advance_amount = Math.round(this.total_room_amount - this.discount_amount)
              }
              this.advance_amount = event.target.value;
            },
            limitAdult (event, item, index) {
                if (event.target.value > parseInt(item.adultLimit)) {
                    this.roomcategoryArray[index].adult = parseInt(item.adultLimit);
                } else {
                    this.roomcategoryArray[index].adult = event.target.value;
                }
            },
            limitChildren (event, item, index) {
                if (event.target.value > parseInt(item.childrenLimit)) {
                    this.roomcategoryArray[index].children = parseInt(item.childrenLimit);
                } else {
                    this.roomcategoryArray[index].children = event.target.value;
                }
            },
            limitInfant (event, item, index) {
                if (event.target.value > parseInt(item.infantLimit)) {
                    this.roomcategoryArray[index].infant = parseInt(item.infantLimit);
                } else {
                    this.roomcategoryArray[index].infant = event.target.value;
                }
            },
            customBookedByLabel({name, phone}) {
                return `${name}  ( ${phone} )`
            },
            facilityGstSum (val, item) {
                const sum = val.reduce((total, el) => {
                    return total + el.price;
                }, 0);

                this.roomcategoryArray.forEach((el) => {
                    if (el.id === item.id) {
                        el.facilities_rate_price = sum;

                        val.forEach((ele) => {
                            ele.tax_rate.forEach((element) => {
                                this.gstFacilitiRate.push(element.taxname.rate);
                            });
                        });

                        const gstFacility = this.gstFacilitiRate.reduce((total, el) => {
                            return total + el;
                        }, 0);

                        el.facility_gst_rate_price = gstFacility;

                        this.gstFacilitiRate = [];
                    }
                });
            },
            addNewCustomer(data) {
                this.customerError = '';
                if (isNaN(data)) {
                  this.customerError = 'Enter number to add new customer'
                  this.selectedOldCustomer = null;
                  return;
                }
                this.selectedOldCustomer = {
                    id: null,
                    phone: data
                }
                this.clientForm.name= '';
                this.clientForm.email= '';
                this.clientForm.phoneNumber= data;
                this.clientForm.address= '';
                this.clientForm.image= '';
                this.clientForm.images= [];
                this.clientForm.status= 1;
                this.clientForm.identity= '';
                this.clientForm.nationality= 'Indian';
                this.clientForm.pastImages= [];
            },
            extraBedGstSum (val, item) {
                const sum = val.reduce((total, el) => {
                    return total + el.tax_name.rate;
                }, 0);
                this.roomcategoryArray.forEach((el) => {
                    if (el.id === item.id) {
                        el.meal_sum_tax_rate = sum;
                    }
                });
            },
            mealPersonChange (item) {
                this.roomcategoryArray.forEach((el) => {
                    if (el.id === item.id) {
                        el.meal_rate_extra_price = ((item.extra_meal_plan) * (el.meal_plan_price));
                    }
                });
                this.trigger += 1;
            },
          changeRoomRateValue(event, item) {
            this.loading = true;
            let rateValue = event.target.value;
            this.setRoomRateValue(rateValue, item.id);
            this.loading = false;
            this.trigger += 1;
          },
          setRoomRateValue(rateValue, itemId){
            let gst = [];
            let indRate = 6;
            if (rateValue <= 7500) {
              indRate = 6;
            } else {
              indRate = 9;
            }

            this.allVatRates.forEach((value) => {
              if (value.rate == indRate) {
                gst.push({tax_name: value})
              }
            })

            this.roomcategoryArray.forEach((el) => {
              if (el.id === itemId) {
                el.total_tax = 2*indRate;
                el.gstRate = gst;
                el.room_rate = rateValue;
                el.totalRoomGst = (rateValue * this.totalDays) * 2 * indRate / 100;
              }
            });
          },
            async roomCategorySelect (index, event = null) {
                event && (this.initialLoading = true);

                this.roomcategoryArray[index].room_number_id = '';
                this.roomcategoryArray[index].room_rate = 0;
                this.roomcategoryArray[index].gstRate = [];
                this.roomcategoryArray[index].total_tax = 0;

                this.room_category_form.cat_id = _.cloneDeep(this.roomcategoryArray[index]?.room_booking_id?.id);
                this.room_category_form.hotel_id = this.hotel_id.id;

                this.room_category_form.check_in_date = moment(this.check_in_date).format('yyyy-MM-DD hh:MM:SS');
                this.roomcategoryArray[index].adultLimit += 1;

                await this.getRoomCategoryListData(this.roomcategoryArray[index].id, event);
            },
            roomNumberSelect (items, item, rate = 0) {
                this.getRoomNumberListData(item.id, items.id, rate);
            },
            async roomFacilitySelect (item, facility_id, event) {
                event && (this.initialLoading = true);
                const idArray = facility_id.map((el) => el.id);
                this.facilities_single_form.id = idArray.join(',');
                const { data } = await this.facilities_single_form.post(
                    window.location.origin +
                    '/api/room/category/lsit/facility/price/hotel/singel',
                );
                const idArray2 = facility_id.map((el) => el.id);
                this.facilities_single_form.id = idArray2.join(',');
                const facilityData = await this.facilities_single_form.post(
                    window.location.origin +
                    '/api/room/category/lsit/facility/price/hotel/singel/price',
                );
                await this.roomcategoryArray.forEach((el) => {
                    if (el.id === item.id) {
                      let sumOfTax = facilityData.data?.data[0]?.sumOfTax;
                      let rate = this.tax_included
                        ? data.data.map(v => {
                          v.price = this.inclusiveTaxAmount(v.price, sumOfTax)
                          return v;
                        })
                        : data.data;
                        el.facility_sum_tax_rate = sumOfTax;
                        el.facilities_price = rate;
                        this.facilityGstSum(data.data, item);
                    }
                });
                event && (this.initialLoading = false);
                this.trigger += 1;
            },
            async mealPlanData (item, id, event = null) {
                event && (this.initialLoading = true);
                this.meal_plan_form.meal_id = _.cloneDeep(id);
                this.meal_plan_form.hotel_id = this.hotel_id.id;
                const { data } = await this.meal_plan_form.post(
                    window.location.origin +
                    '/api/hotel/room/meal/view',
                );
                this.meal_plan_form.meal_id = _.cloneDeep(id);
                this.meal_plan_form.hotel_id = this.hotel_id.id;
                const mealData = await this.meal_plan_form.post(
                    window.location.origin +
                    '/api/hotel/room/meal/view/price',
                );

                if (data.data || mealData.data) {
                    this.roomcategoryArray.forEach((el) => {
                        if (el.id === item.id) {
                          let rate = this.tax_included
                            ? this.inclusiveTaxAmount(data.data?.price || 0, mealData.data?.data?.sumOfTax)
                            : (data.data?.price || 0);
                            el.meal_sum_tax_rate = mealData?.data?.data?.sumOfTax || 0;
                            el.meal_plan_price = rate;
                            el.meal_tax_rate = data?.data?.tax_rate || [];
                        }
                    });
                }
                event && (this.initialLoading = false);
                this.trigger += 1;
            },
            extraBedRateChange (event, item) {
                this.bedCapacityError = false;
                if (event && event.target.value > item.extra_bed_capacity) item.extra_bed = item.extra_bed_capacity;
                this.bedCapacityError = true;
                this.roomcategoryArray.forEach((el) => {
                    if (el.id === item.id) {
                      let rate = this.tax_included
                        ? this.inclusiveTaxAmount((el.extra_bed_rate) * (el.extra_bed), el.total_tax)
                        : (el.extra_bed_rate) * (el.extra_bed);
                        el.extra_bed_rate_price = rate;
                        el.totalBedGst = (rate * this.totalDays) * el.total_tax / 100;
                    }
                });
                this.trigger += 1;
            },
            extraPersonChange (event, item) {
                this.personCapacityError = false;
                if (event.target.value > item.extra_person_capacity) item.extra_person = item.extra_person_capacity;
                this.personCapacityError = true;
                this.roomcategoryArray.forEach((el) => {
                    if (el.id === item.id) {
                      let rate = this.tax_included
                        ? this.inclusiveTaxAmount(((el.per_person) * (el.extra_person)), el.total_tax)
                        : ((el.per_person) * (el.extra_person));
                        el.extra_person_rate_price = rate;
                        el.totalExtraPersonGst = ((rate * this.totalDays)) * (el.total_tax) / 100;
                    }
                });
                this.trigger += 1;
            },
            notBeforeToday (date) {
                return date < new Date(new Date().setHours(0, 0, 0, 0));
            },
            notBeforeTodayTwelveOClock (date) {
                return date < new Date(new Date().setHours(12, 0, 0, 0));
            },
            notBeforeCheckIn (date) {
                return date <= this.check_in_date;
            },
            notBeforeCheckInTwelveOClock (date) {
                return date < this.check_in_date;
            },
            calculateDays () {
                if (this.processing) return;
                this.totalDays = moment(this.check_out_date).diff(moment(this.check_in_date), 'days')
                // const differenceInMilliseconds = this.check_out_date - this.check_in_date;
                // this.totalDays = Math.floor(differenceInMilliseconds / (1000 * 60 * 60 * 24));
            },
            defaultRoomCategoryArray(id) {
                return {
                    id: id,
                    room_booking_id: '',
                    room_number_id: '',
                    roomNumber: [],
                    adult: '',
                    children: '',
                    infant: '',
                    room_rate: '',
                    total_tax: '',
                    gstRate: [],
                    clientItemsData: [],
                    meal_plan_id: '',
                    mealListData: [],
                    extra_meal_plan: 0,
                    meal_plan_price: '',
                    meal_rate_extra_price: '',
                    totalRoomGst: 0,
                    totalExtraPersonGst: 0,
                    totalFacilityGst: 0,
                    totalMealGst: 0,
                    totalBedGst: 0,
                    meal_tax_rate: [],
                    meal_sum_tax_rate: '',
                    facilitiesListData: [],
                    room_facility_id: '',
                    facilities_price: [],
                    facilities_rate_price: '',
                    facility_gst_rate_price: '',
                    extra_bed: '',
                    extra_bed_rate: '',
                    extra_bed_rate_price: '',
                    extra_person_capacity: '',
                    extra_bed_capacity: '',
                    extra_person: '',
                    extra_person_rate_price: '',
                    per_person: 0,
                }
            },
            async addRoomCategory () {
                this.roomcategoryArray.push(
                    this.defaultRoomCategoryArray(this.roomcategoryArray.length + 1)
                );
                await this.getMealPlanData(this.hotel_id.id);
                await this.getRoomFacilityData(this.hotel_id.id);
            },
            changeHotel() {
                this.roomcategoryArray = [
                    this.defaultRoomCategoryArray(1)
                ]
            },
            removeItem (index) {
                const newArray = [...this.roomcategoryArray];
                newArray.splice(index, 1);
                this.roomcategoryArray = newArray;
            },
            async getBookingType () {
                await this.$store.dispatch('operations/getFacilityData', {
                    path: '/api/booking/type/list',
                });
            },
            async getVatRates () {
                await axios.get('/api/all-vat-rates').then((response) => {
                    this.allVatRates = response.data.data;
                });
            },
            async getHotelDataList () {
                await this.$store.dispatch('operations/getHotelData', {
                    path: '/api/hotel',
                });
            },
            async getMealPlanData (id) {
                this.meal_list_form.hotel_id = id;
                const { data } = await this.meal_list_form.post(
                    window.location.origin +
                    '/api/hotel/room/meal/view/all/name',
                );
                this.roomcategoryArray.forEach((el) => {
                    el.mealListData = data.data;
                });
            },
            async getRoomFacilityData (id) {
                this.facilities_list_form.hotel_id = id;
                const { data } = await this.facilities_list_form.post(
                    window.location.origin +
                    '/api/room/category/lsit/facility/price/hotel',
                );

                this.roomcategoryArray.forEach((el) => {
                    el.facilitiesListData = data.data;
                });
            },
            async getBookedBySupplier () {
                const { data } = await axios.get(
                    window.location.origin +
                    '/api/all-clients',
                );
                this.bookedByList = data.data.filter((datum) => datum.type == 2);
                this.loading = false;
            },
            async getLedgerListData () {
                this.payment_method_form.ledger_type = '';
                this.payment_method_form.bank_only = 1;
                const { data } = await this.payment_method_form.post(
                    window.location.origin + '/api/account/ledger/list');
                this.paymentModeData = data.data;
                this.payment_mode_id = this.paymentModeData[0]
            },
            async getRoomCategoryListData (id, event) {
                const { data } = await this.room_category_form.post(
                    window.location.origin +
                    '/api/hotel/get-hotel-cat'
                );
                this.roomcategoryArray.forEach((el) => {
                    data.data = data.data.filter((ele) => el.room_number_id.id !== ele.id);

                    if (el.id === id) {
                        el.roomNumber = data.data;
                    }
                });
                event && (this.initialLoading = false);
            },
            async getRoomNumberListData (id, itemId, rate) {
                this.room_number_form.id = id;
                const { data } = await this.room_number_form.post(
                    window.location.origin +
                    '/api/hotel/get-single-room',
                );
                this.room_number_form.id = id;
                const rate_data = await this.room_number_form.post(
                    window.location.origin +
                    '/api/hotel/get-single-room-price',
                );

                await this.roomcategoryArray.forEach((el) => {
                    if (el.id === itemId) {
                        el.total_tax = rate_data.data.data.sumOfTax;
                        el.extra_bed_capacity = data.data.extra_bed_capacity;
                        el.extra_person_capacity = data.data.extra_person_capacity;
                        el.room_rate = rate ? rate : data.data.room_rate;
                        el.per_person = data.data.per_person;
                        el.extra_bed_rate = data.data.extra_bed_rate;
                        el.gstRate = data?.data?.tax_rate;
                        el.totalRoomGst = (data.data.room_rate * this.totalDays) * rate_data.data.data.sumOfTax / 100;
                    }
                });

              this.setRoomRateValue(rate ? rate : data.data.room_rate, itemId);
              this.trigger += 1;
            },
            onFileChange (e) {
                const file = e.target.files[0];
                if (
                    file.size < 2111775 &&
                    (file.type === 'image/jpeg' ||
                        file.type === 'image/png' ||
                        file.type === 'image/gif')
                ) {
                    this.clientForm.image = file;
                    this.url = URL.createObjectURL(file);
                } else {
                    Swal.fire(
                        this.$t('common.error'),
                        this.$t('common.image_error'),
                        'error',
                    );
                }
            },
            async saveClient () {
                let hotelId = this.hotel_id?.id || this.hotel_id || 1;
                this.clientForm.hotel_id = {id: hotelId};
                if (this.selectedOldCustomer?.id) await this.updateOldClient();
                else await this.createNewClient();
            },
            async updateOldClient() {
                this.clientForm.images = [];
                this.clientForm.pastImages = [];
                if (this.images && this.images.length) {
                    this.images.forEach((image) => {
                        if (image.file) this.clientForm.images.push(image.file);
                        else this.clientForm.pastImages.push(image.name)
                    })
                }
                await this.clientForm
                    .post(
                        window.location.origin + '/api/clients/' + this.selectedOldCustomer.slug
                    )
                    .then((response) => {
                        if (this.slugdata) {
                            this.clientItemsData.push({
                                id: response.data.data.id,
                                clientPhone: response.data.data.phone,
                                clientName: response.data.data.name,
                            })
                        } else {
                            this.roomcategoryArray.forEach((el) => {
                                if (el.id === this.clientItemId) {
                                    el.clientItemsData.push({
                                        id: response.data.data.id,
                                        clientPhone: response.data.data.phone,
                                        clientName: response.data.data.name,
                                        checked: '9898989898',
                                    });
                                }
                            });
                        }

                        $('#newClient').modal('hide');
                    })
                    .catch(() => {
                        toast.fire({
                            type: 'error',
                            title: this.$t('common.error_msg'),
                        })
                    })
            },
            async createNewClient () {
                await this.clientForm.post(window.location.origin + '/api/clients').then((res) => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('clients.create.success_msg'),
                    });
                    if (this.slugdata) {
                        this.clientItemsData.push({
                            id: res.data.data.id,
                            clientPhone: res.data.data.phone,
                            clientName: res.data.data.name,
                        })
                    } else {
                        this.roomcategoryArray.forEach((el) => {
                            if (el.id === this.clientItemId) {
                                el.clientItemsData.push({
                                    id: res.data.data.id,
                                    clientPhone: res.data.data.phone,
                                    clientName: res.data.data.name,
                                    checked: '9898989898',
                                });
                            }
                        });
                    }
                    $('#newClient').modal('hide');
                }).catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                });
            },
            async searchClientData () {
                await this.searchData(this.$store, this.clientPhone);
            },
            async searchData (store, clientPhone){
                store.dispatch('operations/searchData', {
                    path: '/api/clients/search?customer_only=1',
                    term: clientPhone,
                })
                this.isLoadingClient = false;
            },
            async handleOldClient () {
                await this.searchClientData();
                if(this.slugdata) return $('#oldClientEdit').modal('show');
                this.isSuggestionBox = true;
                this.selectedOldCustomer = this.items[0]
                $('#oldClient').modal('show');
            },
            async reload () {
                this.clientPhone = '';
                await this.searchClientData();
            },
            async clientChange (data) {
                this.clientData = this.selectedOldCustomer ? {
                    clientId: data.id,
                    clientPhone: data.phone,
                    clientName: data.name,
                } : {};

                this.clientForm.name = data.name;
                this.clientForm.email = data.email;
                this.clientForm.phoneNumber = data.phone;
                this.clientForm.address = data.address;
                this.clientForm.image = data.image;
                this.clientForm.images = data.images;
                this.clientForm.identity = data.identity;
                this.clientForm.nationality = data.nationality;
                this.images = data?.images?.map((image) => {
                    return {
                        file: null,
                        previewUrl: `/images/clients/${image}`,
                        name: image
                    }
                })
                if (!this.images) this.images = [];
                if ((!data.images || !data.images?.length) && data.image) {
                    this.images.push({
                        file : null,
                        previewUrl: `/images/clients/${data.image}`,
                        name: data.image
                    })
                }
            },
            saveOldClient (item = null) {
                if (!this.clientData.clientId){
                    toast.fire({ type: 'error', title: 'Please select client.' });
                    return false;
                }

                if (this.slugdata) {
                    this.clientItemsData.push({
                        id: this.clientData.clientId,
                        clientPhone: this.clientData.clientPhone,
                        clientName: this.clientData.clientName,
                    });

                    $('#oldClientEdit').modal('hide');
                    return;
                }
                this.roomcategoryArray.forEach((el) => {
                    if (el.id === this.clientItemId) {
                        el.clientItemsData.push({
                            id: this.clientData.clientId,
                            clientPhone: this.clientData.clientPhone,
                            clientName: this.clientData.clientName,
                        });
                        el.clientItemsData[0].checked = '9898989898';
                    }
                });
                this.isSuggestionBox = false;
                $('#oldClient').modal('hide');
                this.selectedOldCustomer = {};
                this.clientData = {};
                this.clientItemId = '';
            },
            removeClientItem (index, item) {
                this.roomcategoryArray.forEach((el) => {
                    if (el.id === item.id) {
                        el.clientItemsData.splice(index, 1);
                    }
                });
            },
            removeClientItemEdit(key) {
                this.clientItemsData.splice(key, 1);
            },
            async saveBooking () {
                if (!this.payment_mode_id) return toast.fire({ type: 'error', title: this.$t('booking.create.payment_error') });
                setTimeout(async () => {
                    this.form.customer = [];
                    this.processing = true;
                    let checkIn = _.cloneDeep(this.check_in_date)
                    let checkOut = _.cloneDeep(this.check_out_date)
                    checkIn = moment(checkIn).format('yyyy-MM-DD')
                    checkOut = moment(checkOut).format('yyyy-MM-DD')

                    this.form.type = this.booking_status_main == 3 ? 2 : 1;
                    this.form.edit = 1;
                    this.form.customer = [];
                    try {
                        this.clientItemsData.forEach((element, index) => {
                            this.form.customer.push({ id: null, customer_id: element.id, customer_id_request: null });
                            if (index === 0) {
                                this.form.booked_by = element.id;
                            }
                        });
                    } catch (error) {
                        toast.fire({ type: 'error', title: error.message })
                    }
                    this.form.total_room = 1;
                    this.form.id = this.slugdata;
                    this.form.client_booking_status = this.client_booking_status_id.id;
                    this.form.advance_amount = this.advance_amount;
                    this.form.paid_amount = this.pending_amount;
                    this.form.total_price = this.total_room_amount;
                    this.form.payment_method = this.payment_mode_id.id;
                    this.form.remarks = this.remarks;
                    this.form.discount_amount = this.discount_amount;
                    this.form.discount_reason = null;
                    this.form.full_guest_name = null;
                    this.form.special_request = null;
                    this.form.check_in_date = checkIn;
                    this.form.check_out_date = checkOut;
                    this.form.hotel_id = this.hotel_id.id;
                    this.form.booking_source = this.bookedBy?.id || null;
                    this.form.arrival_from = this.arrivalForm;
                    this.form.discount_type = this.discount_room_rate?.id || null;
                    this.form.final_gst_rates = this.final_gst_rate;
                    this.form.tax_included = this.tax_included ? 1 : 0;
                    this.form.rooms = [];
                    this.roomcategoryArray.forEach((el) => {
                        if (el.room_booking_id.id) {
                            const roomObj = {
                                id: el.original_id,
                                room_booking_id: null,
                                room_id: el.room_booking_id.id,
                                room_no: el.room_number_id.id,
                                adult: parseInt(el.adult),
                                children: parseInt(el.children),
                                infant: parseInt(el.infant),
                                meal_id: el.meal_plan_id.id,
                                extra_bed: el.extra_bed,
                                extra_person: el.extra_person,
                                extra_child: null,
                                complementary: el.room_facility_id,
                                extra_facility_days: this.totalDays,
                                room_tax: this.tax_included ? (((parseFloat(el.room_rate || 0) * this.totalDays) - (el.discount || 0)) * el.total_tax / 100) : (el.totalRoomGst || 0),
                                meal_plan_tax: this.tax_included ?  ((el.meal_rate_extra_price * this.totalDays) * el.meal_sum_tax_rate / 100) : (el.totalMealGst || 0),
                                facility_tax: this.tax_included ? ((el.facilities_rate_price * this.totalDays) * el.facility_gst_rate_price / 100) : (el.totalFacilityGst || 0),
                                extra_bed_tax: this.tax_included ? ((el.extra_bed_rate_price * this.totalDays) * el.total_tax / 100) : (el.totalBedGst || 0),
                                extra_person_tax: this.tax_included ? ((el.extra_person_rate_price * this.totalDays) * el.total_tax / 100) : (el.totalExtraPersonGst || 0),
                                room_rate: el.room_rate * this.totalDays,
                                modified_room_rate: el.room_rate,
                                extra_meal_plan: el.extra_meal_plan,
                                discount: el.discount
                            };
                            this.form.rooms.push(roomObj);
                        }
                    });
                    await this.form.post(window.location.origin + '/api/booking/add').then(() => {
                        toast.fire({
                            type: 'success',
                            title: 'The booking edited successfully',
                        });
                        this.$router.push({ name: 'booking' });
                    }).catch((error) => {
                        let message = error.response?.data?.message || this.$t('common.error_msg');
                        toast.fire({ type: 'error', title: message });
                    });
                    // }
                    this.processing = false;
                }, 200)

            },
            async getBookingData () {
                const { data } = await axios.get(
                    window.location.origin + '/api/booking/view/' + this.$route.params.slug);
                this.roomsList = [];
                this.mealPlanDetailsList = [];
                this.mealDataList = [];
                this.complementraysList = [];
                this.facilitiesListDataList = [];
                this.bookingData = data.data;
                this.booking_status_main = data.data.booking_status_main;
                this.check_in_date = new Date(this.bookingData.check_in_date.split(' ')[0]);
                this.check_out_date = new Date(this.bookingData.check_out_date.split(' ')[0]);
                this.dateRange = {
                    startDate: this.check_in_date,
                    endDate: this.check_out_date,
                };
                this.arrivalForm = this.bookingData.arrival_from;
                this.puposeOfVisit = this.bookingData.purpose;
                this.remarks = this.bookingData.remarks;
                this.tax_included = this.bookingData.tax_included
                this.hotelItems?.forEach((hotel) => {
                    if (hotel.id == this.bookingData.hotel.id) {
                        this.hotel_id = hotel;
                    }
                })

                await this.prepareRoomCategoryArray(this.bookingData.booking_details);
                await this.bookingData.booking_details?.forEach((room, index) => {
                    this.$set(this.roomcategoryArray[index], 'original_id', room.id)
                })
                // this.roomcategoryArray = this.bookingData.booking_details;
                this.discount_amount = this.bookingData.discount_amount;
                this.form.comments = this.bookingData.comments;
                this.form.advance_remarks = this.bookingData.advance_remarks;
                this.advance_amount = this.bookingData.advance_amount;
                this.booking_source = this.bookingData?.booking_type;
                this.bookedBy = this.bookingData.source;
                this.client_booking_status_id = this.bookingData.boking_status;
                // this.total_room_amount = this.bookingData.total_price;
                this.paymentModeData.forEach((mode) => {
                    if (mode.id == this.bookingData.payment_method) this.payment_mode_id = mode;
                })
                this.discountRoomRateData.forEach((type) => {
                    if (type.id == this.bookingData.discount_type) this.discount_room_rate = type;
                })

                if (this.discount_room_rate.id == 1) {
                    this.discount = this.discount_amount;
                } else {
                    this.discount = ((100 * this.discount_amount) / (_.sumBy(this.bookingData.booking_details,'room_rate')));
                    this.discount = Math.round(this.discount);
                }

                this.clientItemsData = _.uniqBy(this.bookingData.customers,'client_id');
                this.clientItemsData = this.clientItemsData.map((client) => {
                    return {
                        id: client.id,
                        clientPhone: client.phone,
                        clientName: client.name,
                    }
                })
            },
            async prepareRoomCategoryArray(rooms) {
                this.roomcategoryArray = rooms;
                let roomId = 0;
                for (const room of this.roomcategoryArray) {
                    setTimeout(async () =>
                    {
                        ++roomId;
                        room.id = roomId;
                        room.room_booking_id = room.room?.roomcategory;
                        await this.roomCategorySelect(roomId-1);
                        room.room_number_id = {room_name : room.room?.room_name, id: room.room?.id};
                        await this.roomNumberSelect(room, room.room_number_id, room.modified_room_rate)
                        room.adultLimit = _.cloneDeep(room.adult);
                        room.childrenLimit = _.cloneDeep(room.children);
                        room.infantLimit = _.cloneDeep(room.infant);
                        room.mealListData?.forEach((meal) => {
                            if (meal.id == room?.meal_plan_details.id) room.meal_plan_id = meal;
                        })
                        room.extra_meal_plan = room.meal_plan_persons || 0;
                        await this.mealPlanData(room, room.meal_plan_id)
                        await this.mealPersonChange(room)
                        room.extra_person_rate_price = this.tax_included ? room.extra_person * room.per_person - (room.extra_person_tax || 0) : room.extra_person * room.per_person;
                        room.totalExtraPersonGst = room.extra_person_tax || 0;
                        room.extra_bed_rate_price = room.extra_bed_rate * (room.extra_bed || 0);
                        room.room_facility_id = [];
                        let compIds = room.complementrays && _.map(room.complementrays, 'complementary_id');
                        await room.facilitiesListData?.forEach((facility) => {
                            if(compIds && compIds.length && compIds.includes(facility.room_facility_id)) {
                                room.room_facility_id.push(facility);
                            }
                        })
                        await this.roomFacilitySelect(room, room.room_facility_id)
                        await this.extraBedRateChange(null, room)
                        setTimeout(() => {this.initialLoading = false;}, 200)
                    }, 200);
                }
            }
        },
        beforeDestroy () {
            $('.modal-backdrop').remove();
        },
        async mounted () {
            this.initialLoading = true;
            this.bookingId = this.$route?.params?.slug
            this.selectedOldCustomer = this.items.length ? this.items[0] : null
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
.border-b-gray-1{
    border-bottom: 1px solid #ced4da
}
input[type="file"] {
    display: none;
}

/* Style for the file input label */
.image_label {
    padding: 10px 20px;
    background-color: #6c788b;
    color: #ffffff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.image_label:hover {
    background-color: #3367d6;
}

.image-container {
    display: flex;
    flex-wrap: wrap;
    margin-top: 20px;
}

.image-item {
    position: relative;
    margin-right: 10px;
    margin-bottom: 10px;
}
.profile-pic {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    cursor: pointer;
}
    .remove-button {
        background:none;
        border:none;
        position: absolute;
        top: 1px;
        right: 1px;
        padding: 5px;
        color: red;
        cursor: pointer;
        font-weight: bold;
    }

    .close {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 35px;
        font-weight: bold;
        color: #f1f1f1;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
</style>
