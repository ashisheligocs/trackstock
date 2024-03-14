<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ $t('booking.create.page_title') }}
                        </h3>
                        <router-link :to="{ name: 'booking' }" class="btn btn-dark float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveBooking" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="check_in_date" class="d-block">{{ $t('common.check_in') }}
                                        <span class="required">*</span></label>
                                    <date-picker v-model="time1"  type="datetime" :format="'DD-MM-YYYY HH:mm:ss'" :default-value="new Date().setHours(12, 0, 0, 0)" :disabled-date="notBeforeToday" :disabled-time="notBeforeTodayTwelveOClock" placeholder="Select a Check In date" class="w-100" ></date-picker>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="check_in_date" class="d-block">{{ $t('common.check_out') }}
                                        <span class="required">*</span></label>
                                    <date-picker v-model="time2" type="datetime" :format="'DD-MM-YYYY HH:mm:ss'" :default-value="time1" :disabled-date="notBeforeCheckIn" :disabled-time="notBeforeCheckInTwelveOClock" class="w-100" placeholder="Select a Check Out date" ></date-picker>
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label for="total_day" class="d-block">{{ $t('common.total_days') }}</label>
                                    <input v-if="time1 && time2" id="total_day" v-model="totalDays" type="text" class="form-control" disabled name="total_day" :placeholder="$t('common.total_days')" />
                                    <input v-else id="total_day" v-model="totalDaysStatic" type="text" class="form-control" disabled name="total_day" :placeholder="$t('common.total_days')" />
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="check_in_date" class="d-block">{{ $t('common.arrival_form') }}
                                        <span class="required">*</span></label>
                                    <input id="bookingtypetitle" v-model="arrivalForm" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.arrival_form_placeholder')" />
                                </div>

                                
                                <div class="form-group col-md-4">
                                    <label for="check_in_date" class="d-block">{{ $t('common.purpose_of_visit') }}</label>
                                    <input id="bookingtypetitle" v-model="puposeOfVisit" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.purpose_of_visit_placeholder')" />
                                </div>
                                
                                <div class="form-group col-md-4">
                                <label for="check_in_date" class="d-block">{{ $t('common.remarks') }}</label>
                                    <input id="bookingtypetitle" v-model="remarks" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.remarks_placeholder')" />
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="check_in_date" class="d-block">{{ $t('common.booking_type') }}</label>
                                        <multiselect v-model="booking_source" :options="facilityItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Booking type" class="form-control" label="bookingtypetitle" track-by="bookingtypetitle" :class="{ 'is-invalid': form.errors.has('check_in_date') }"></multiselect>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="check_out_date" class="d-block">{{ $t('common.booked_by') }}</label>
                                        <multiselect v-model="bookedBy" :options="bookedByList" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a booking by" class="form-control" label="name" track-by="name" :class="{ 'is-invalid': form.errors.has('check_out_date') }"></multiselect>
                                    <has-error :form="form" field="check_out_date" />
                                </div>
                                {{ client_booking_status_id }}
                                <div class="form-group col-md-4">
                                    <label for="hotel" class="d-block">{{ $t('common.booked_status') }}
                                        <span class="required">*</span></label>
                                        <multiselect v-model="client_booking_status_id" :options="bookedStatusData" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a booked status" class="form-control" label="title" track-by="title"></multiselect>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="hotel" class="d-block">{{ $t('sidebar.hotel') }}
                                        <span class="required">*</span></label>
                                        <multiselect v-model="hotel_id" :options="hotelItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a hotel" class="form-control" label="hotel_name" track-by="hotel_name" :class="{ 'is-invalid': form.errors.has('hotel') }"></multiselect>
                                    <has-error :form="form" field="hotel" />
                                </div>

                                <div v-for="(item,index) in roomcategoryArray" :key="item" class="form-group col-md-12 room-booking-form px-4 py-3 mt-3">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label for="room_hotel_cat_lsit" class="d-block">{{ $t('sidebar.room_hotel_cat_lsit') }}
                                                <span class="required">*</span></label>
                                                <multiselect @select="roomCategorySelect(item)"  v-model="item.room_booking_id" :options="hotelCategoryItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Room category" class="form-control" label="room_category_name" track-by="room_category_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="room_number" class="d-block">{{ $t('common.room_number') }}
                                                <span class="required">*</span></label>
                                                <multiselect @select="roomNumberSelect(item,item.room_number_id)" v-model="item.room_number_id" :options="item.roomNumber" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Room number" class="form-control" label="room_name" track-by="room_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="adult" class="d-block">#{{ $t('common.adult') }}
                                                <span class="required">*</span></label>
                                            <input id="adult" min="0" :max="item.adult" v-model="item.adult" type="number" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.adult_placeholder')" />
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="children" class="d-block">#{{ $t('common.children') }}
                                                <span class="required">*</span></label>
                                            <input id="children" min="0" :max="item.children" v-model="item.children" type="number" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.children_placeholder')" />
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="infant" class="d-block">#{{ $t('common.infant') }}
                                                <span class="required">*</span></label>
                                            <input id="infant" min="0" :max="item.infant" v-model="item.infant" type="number" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.infant_placeholder')" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="card-body p-0 position-relative">
                                            <div id="printMe" class="table-responsive table-custom mt-3">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ $t("common.s_no") }}</th>
                                                            <th>{{ $t("common.room_no") }}</th>
                                                            <th>{{ $t("sidebar.room_hotel_cat_lsit") }}</th>
                                                            <th>{{ $t("common.room_rate") }}</th>
                                                            <th>{{ $t("common.gst") }}</th>
                                                            <th>{{ $t("common.subtotal") }}</th>
                                                            <th>{{ $t("common.action") }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <span>1</span>
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
                                                                <div v-if="item?.room_rate">
                                                                    <p>{{ item?.room_rate }} * {{ totalDays }}</p>
                                                                    <p class="font-weight-bold mb-0"> = {{ item.room_rate * totalDays }} {{ roomRate(item.room_rate * totalDays) }}</p>
                                                                </div>
                                                                <div v-else>
                                                                    0.00
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item?.room_rate">
                                                                    <p class="mb-0" v-for="(gstItem, index) in item.gstRate" :key="index">
                                                                        {{ gstItem.tax_name.name }} = {{ gstItem.tax_name.rate }} {{ roomGstSum(item.gstRate,item) }}
                                                                    </p>
                                                                </div>
                                                                <p v-if="item.room_rate" class="font-weight-bold mb-0">= {{ item.room_gst_tax_rate_price }}</p>
                                                                <p v-if="item.room_rate" class="font-weight-bold mb-0">= {{ item.room_rate * totalDays }} * {{ item.room_gst_tax_rate_price }} / 100</p>
                                                                <p v-if="item.room_rate" class="font-weight-bold mb-0">= {{ (item.room_rate * totalDays ) * item.room_gst_tax_rate_price / 100 }}</p>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.room_rate">
                                                                    <p class="mb-0">{{ (item.room_rate * totalDays) + ((item.room_rate * totalDays ) * item.room_gst_tax_rate_price / 100) }}.00  {{ room_price_with_gst((item.room_rate * totalDays) + ((item.room_rate * totalDays ) * item.room_gst_tax_rate_price / 100)) }}</p>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button @click="removeItem(index)" v-if="index != 0" type="button" class="btn btn-danger float-right close-icon">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td colspan="2">
                                                                <div v-for="(clientItem, index) in item.clientItemsData" :key="index" class="d-flex">
                                                                    <div style="width: 250px">
                                                                        <input v-model="clientItem.checked" type="radio" :id="clientItem.id" name="fav_language" value="9898989898">
                                                                        <label :for="clientItem.id">{{clientItem.clientName}}</label>
                                                                    </div>
                                                                    <div class="w-50">
                                                                        {{ clientItem.clientPhone }}
                                                                    </div>
                                                                    <div>
                                                                        <button @click="removeClientItem(index, item)" type="reset" class="btn btn-danger float-right close-icon">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex justify-content-end">
                                                                    <!-- <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" /> -->

                                                                    <div class="ml-4 mt-1 dropdown show">
                                                                        <button @click="test(item)" type="reset" class="btn btn-primary float-right plus-icon dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <i class="fas fa-times" style="transform: rotate(45deg);"></i>
                                                                        </button>

                                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                                            <a class="dropdown-item" role="button" data-toggle="modal"  data-target="#newClient" href="#">New client</a>
                                                                            <a class="dropdown-item" role="button" data-toggle="modal"  data-target="#oldClient" href="#">Old client</a>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <has-error :form="form" field="client_booking_status" />
                                                            </td>
                                                            <td></td>
                                                            <td colspan="3"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">
                                                                <div class="row d-flex align-items-center">
                                                                    <div>
                                                                        <p class="mb-0 mr-1">{{ $t("sidebar.meal_plan") }}</p>
                                                                    </div>
                                                                    <div>
                                                                        <multiselect @select="mealPlanData(item, item.meal_plan_id)" v-model="item.meal_plan_id" :options="item.mealListData" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Meal plan" class="form-control mr-2" style="width: 160px;" label="mealname" track-by="mealname" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                                                    </div>
                                                                    <div>
                                                                        <input id="bookingtypetitle" @change="mealPersonChange(item)" v-model="item.extra_meal_plan" type="number" class="form-control col-md-10" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.meal_plan_placeholder')" />
                                                                    </div>
                                                                    <div>
                                                                        <div class="d-flex align-items-center">
                                                                            = {{ item.meal_plan_price }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.meal_plan_price">
                                                                    <p class="mb-0" v-if="item.extra_meal_plan || item.meal_plan_price">{{ item.extra_meal_plan }} * {{ item.meal_plan_price }}</p>
                                                                    <p v-if="item.meal_plan_price" class="font-weight-bold mb-0">= {{ item.meal_rate_extra_price }}</p>
                                                                    <p v-if="item.meal_plan_price" class="font-weight-bold mb-0">= {{ item.meal_rate_extra_price }} * {{ totalDays }}</p>
                                                                    <p v-if="item.meal_plan_price" class="font-weight-bold mb-0">= {{ item.meal_rate_extra_price * totalDays }} {{ mealRate(item.meal_rate_extra_price * totalDays) }}</p>
                                                                </div>
                                                                <div v-else>
                                                                    <p v-if="meal_plan_id" class="font-weight-bold mb-0">= {{ meal_plan_price }} * {{ totalDays }}</p>
                                                                    <p v-if="meal_plan_id" class="font-weight-bold mb-0">= {{ meal_plan_price * totalDays }}</p>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.meal_plan_id">
                                                                    <p class="mb-0" v-for="(taxRateItem,index) in item.meal_tax_rate" :key="index">
                                                                        {{ taxRateItem.tax_name.name }} = {{ taxRateItem.tax_name.rate }} {{ mealGstSum(item.meal_tax_rate,item) }}
                                                                    </p>

                                                                </div>
                                                                <p v-if="item.meal_plan_id" class="font-weight-bold mb-0">= {{ item.meal_rate_tax_rate_price }}</p>
                                                                <p v-if="item.meal_plan_id" class="font-weight-bold mb-0">= {{ (item.meal_rate_extra_price * totalDays) }} * {{ item.meal_rate_tax_rate_price }} / 100</p>
                                                                <p v-if="item.meal_plan_id" class="font-weight-bold mb-0">= {{ ((item.meal_rate_extra_price * totalDays) ) * item.meal_rate_tax_rate_price / 100 }}</p>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.meal_plan_id">
                                                                    <p class="mb-0">{{ (item.meal_rate_extra_price * totalDays) + (((item.meal_rate_extra_price * totalDays) ) * item.meal_rate_tax_rate_price / 100) }}.00</p>
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">
                                                                <div class="row d-flex align-items-center">
                                                                    <div>
                                                                        <p class="mb-0 mr-3">{{ $t("common.add_services") }}</p>
                                                                    </div>
                                                                    <div>
                                                                        <multiselect @select="roomFacilitySelect(item, item.room_facility_id)" v-model="item.room_facility_id" :options="item.facilitiesListData" :multiple="true" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Add services" class="form-control w-100" label="facility_name" track-by="facility_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.room_facility_id?.length > 0" class="d-flex align-items-center">
                                                                    <p class="mb-0" v-for="(facilityItem,index) in item.facilities_price" :key="index">
                                                                        {{ facilityItem.price }}
                                                                        <span v-if="item.facilities_price?.length != (index + 1)">+</span>
                                                                    </p>
                                                                </div>
                                                                <p v-if="item.room_facility_id?.length > 0" class="mb-0 font-weight-bold">= {{ item.facilities_rate_price }}</p>
                                                                <p v-if="item.room_facility_id?.length > 0" class="mb-0 font-weight-bold">= {{ item.facilities_rate_price }} * {{ totalDays }}</p>
                                                                <p v-if="item.room_facility_id?.length > 0" class="mb-0 font-weight-bold">= {{ item.facilities_rate_price * totalDays }} {{ facilityRate(item.facilities_rate_price * totalDays) }}</p>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.room_facility_id?.length > 0">
                                                                    <p v-for="(facilityItem) in item.facilities_price" :key="facilityItem" class="mb-0 mt-1">
                                                                        <span v-for="(taxRateItems, index) in facilityItem.tax_rate" :key="index" class="d-block">
                                                                            {{ taxRateItems.taxname.name }} = {{ taxRateItems.taxname.rate }} {{ facilityGstSum(item.facilities_price, item) }}
                                                                        </span>
                                                                    </p>
                                                                    <p class="font-weight-bold mb-0">= {{ item.facility_gst_rate_price }}</p>
                                                                    <p class="font-weight-bold mb-0">= {{ (item.facilities_rate_price * totalDays) }} * {{ item.facility_gst_rate_price }} / 100</p>
                                                                    <p class="font-weight-bold mb-0">= {{ ((item.facilities_rate_price * totalDays) * item.facility_gst_rate_price) / 100 }}</p>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.room_facility_id?.length > 0">
                                                                    <p class="mb-0">{{ (item.facilities_rate_price * totalDays) + (((item.facilities_rate_price * totalDays) * item.facility_gst_rate_price) / 100) }}.00</p>
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">
                                                                <div class="row">
                                                                    <div class="form-group col-md-6">
                                                                        <label for="check_in_date" class="d-block">{{ $t('common.extra_bed') }}
                                                                            <span class="required">*</span></label>
                                                                        <input id="bookingtypetitle" @change="extraBedRateChange(item)" v-model="item.extra_bed" type="number" min="0" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.extra_bed_placeholder')" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.extra_bed">
                                                                    <p class="mb-0">{{ item.extra_bed }} * {{ item.extra_bed_rate }}</p>
                                                                    <p class="font-weight-bold mb-0">= {{ item.extra_bed_rate_price }}</p>
                                                                    <p class="font-weight-bold mb-0">= {{ item.extra_bed_rate_price }} * {{ totalDays }}</p>
                                                                    <p class="font-weight-bold mb-0">= {{ item.extra_bed_rate_price * totalDays }}</p>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.extra_bed">
                                                                    <p class="mb-0" v-for="(gstRateItem, index) in item.gstRate" :key="index">
                                                                        {{ gstRateItem.tax_name.name }} = {{ gstRateItem.tax_name.rate }}
                                                                    </p>
                                                                </div>
                                                                <p v-if="item.extra_bed" class="font-weight-bold mb-0">= {{ item.room_gst_tax_rate_price }}</p>
                                                                <p v-if="item.extra_bed" class="font-weight-bold mb-0">= {{ item.extra_bed_rate_price * totalDays }} * {{ item.room_gst_tax_rate_price }} / 100</p>
                                                                <p v-if="item.extra_bed" class="font-weight-bold mb-0">= {{ (item.extra_bed_rate_price * totalDays) * item.room_gst_tax_rate_price / 100 }}</p>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.extra_bed">
                                                                    <p class="mb-0">{{ (item.extra_bed_rate_price * totalDays) + ((item.extra_bed_rate_price * totalDays) * item.room_gst_tax_rate_price / 100) }}.00</p>
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">
                                                                <div class="row">
                                                                    <div class="form-group col-md-6">
                                                                        <label for="check_in_date" class="d-block">{{ $t('common.extra_person') }}
                                                                            <span class="required">*</span></label>
                                                                        <input id="bookingtypetitle" @change="extraPersonChange(item)" v-model="item.extra_person" type="number" min="0" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.extra_person_placeholder')" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.extra_person">
                                                                    <p class="mb-0">{{ item.meal_plan_price }} * {{ item.extra_person }}</p>
                                                                    <p class="font-weight-bold mb-0">= {{ item.extra_person_rate_price }}</p>
                                                                    <p class="font-weight-bold mb-0">= {{ item.extra_person_rate_price }} * {{ totalDays }}</p>
                                                                    <p class="font-weight-bold mb-0">= {{ item.extra_person_rate_price * totalDays }}</p>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.extra_person">
                                                                    <p v-if="item.gstRate" class="mb-0 text-muted font-weight-bold" style="font-size: 12px;">Room GST</p>
                                                                    <p v-for="(gstItem, index) in item.gstRate" :key="index" class="mb-0">
                                                                        {{ gstItem.tax_name.name }} = {{ gstItem.tax_name.rate }}
                                                                    </p>
                                                                </div>
                                                                <p v-if="item.extra_person" class="font-weight-bold mb-0">= {{ item.room_gst_tax_rate_price }}</p>
                                                                <p v-if="item.extra_person" class="font-weight-bold mb-0">= {{ (item.extra_person_rate_price * totalDays) }} * {{ (item.room_gst_tax_rate_price) }} / 100</p>
                                                                <p v-if="item.extra_person" class="font-weight-bold mb-0">= {{ ((item.extra_person_rate_price * totalDays) ) * (item.room_gst_tax_rate_price) / 100 }}</p>
                                                            </td>
                                                            <td>
                                                                <div v-if="item.extra_person">
                                                                    <p class="mb-0">{{ (item.extra_person_rate_price * totalDays) + (((item.extra_person_rate_price * totalDays) ) * (item.room_gst_tax_rate_price) / 100) }}.00</p>
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr style="background-color: #f2f4f5;">
                                                            <td colspan="4">
                                                                <p class="mb-0 font-weight-bold d-flex justify-content-end">{{ $t("common.sub_total") }}</p>
                                                            </td>
                                                            <td></td>
                                                            <td>
                                                                <p class="mb-0 font-weight-bold">$ {{ ((item.room_rate || 0) * totalDays)  + (((item.room_rate || 0) * totalDays ) * item.room_gst_tax_rate_price / 100) + (item.meal_rate_extra_price * totalDays) + (((item.meal_rate_extra_price * totalDays) ) * item.meal_rate_tax_rate_price / 100) + (item.facilities_rate_price * totalDays) + (((item.facilities_rate_price * totalDays) * item.facility_gst_rate_price) / 100) + (item.extra_bed_rate_price * totalDays) + ((item.extra_bed_rate_price * totalDays) * item.room_gst_tax_rate_price / 100) + (item.extra_person_rate_price * totalDays) + (((item.extra_person_rate_price * totalDays) ) * (item.room_gst_tax_rate_price) / 100) }}.00 {{ subTotal() }}</p>
                                                            </td>
                                                            <td></td>
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
                                                        <form role="form" @submit.prevent="saveOldClient(item)" @keydown="clientForm.onKeydown($event)">
                                                            <div class="card-body position-relative">
                                                                <div class="row">
                                                                    <div class="form-group col-md-12 p-0 d-none">
                                                                        <label for="id">{{ $t('common.mobile_number') }}</label>
                                                                        <input id="id" v-model="clientId" type="text" class="form-control"
                                                                            name="id" :placeholder="$t('common.contact_number_placeholder')" />
                                                                    </div>
                                                                    <div class="form-group col-md-12 p-0">
                                                                        <label for="phone">{{ $t('common.mobile_number') }}
                                                                            <span class="required">*</span></label>
                                                                        <input id="phone" v-model="clientPhone" type="text" class="form-control"
                                                                            :class="{ 'is-invalid': form.errors.has('phone') }" name="phone"
                                                                            :placeholder="$t('common.contact_number_placeholder')" />
                                                                        <has-error :form="form" field="phone" />
                                                                    </div>
                                                                    <div class="form-group col-md-12 p-0">
                                                                        <label for="name">{{ $t('common.customer') }}</label>
                                                                        <input id="name" v-model="clientName" type="name" class="form-control"
                                                                            :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                                                                            :placeholder="$t('common.name_placeholder')" />
                                                                        <has-error :form="form" field="name" />
                                                                    </div>
                                                                </div>
                                                                
                                                                <div v-if="isSuggestionBox" :class="items?.length > 0 ? 'bg-white suggestion-box position-absolute p-3' : ''">
                                                                    <div v-for="(item, index) in items" :key="index" class="d-flex justify-content-center">
                                                                        <div style="width: 250px">
                                                                            <input v-model="item.checkbox" @click=clientChange(item.slug) type="checkbox" :id="item.checkbox" name="fav_language" value="9898989898">
                                                                            <label :for="item.checkbox" @click=clientChange(item.slug)>{{item.name}}</label>
                                                                        </div>
                                                                        <div class="w-50">
                                                                            {{ item.phoneNumber }}
                                                                        </div>
                                                                    </div>
                                                                    <button v-if="items?.length > 0" @click="isSuggestionBox = false" class="btn btn-primary">Close</button>
                                                                </div>
                                                            </div>
                                                            <!-- /.card-body -->
                                                            <div class="card-footer">
                                                                <v-button :loading="form.busy" class="btn btn-primary">
                                                                    <i class="fas fa-save" /> {{ $t('common.save') }}
                                                                </v-button>
                                                                <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                                                                    <i class="fas fa-power-off" /> {{ $t('common.reset') }}
                                                                </button>
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
                                        <div style="background-color: #f8f9fa; border-radius: 6px;" class="p-3 h-100">
                                            <div>
                                                <label for="totalRoom" class="font-weight-bold">{{ $t("common.total_room_amount") }}</label>
                                                <p class="mb-0 ml-3">{{ total_room_amount }}</p>
                                            </div>
                                            <div class="mt-4">
                                                <label for="roomRate" class="font-weight-bold">{{ $t("common.discount_room_rate") }}</label>
                                                <multiselect v-model="discount_room_rate" :options="discountRoomRateData" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Discount room rate" class="form-control" label="title" track-by="title" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                            </div>
                                            <div class="mt-4 d-flex justify-content-between">
                                                <div class="col-md-6">
                                                    <label for="discount" class="font-weight-bold">{{ $t("common.discount") }}</label>
                                                    <input id="bookingtypetitle" v-model="discount" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }" name="bookingtypetitle" :placeholder="$t('common.discount_placeholder')" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="discount" class="font-weight-bold">{{ $t("common.discount_amount") }}</label>
                                                    <p class="mb-0 ml-2 mt-2">{{ discount_amount }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label for="discount" class="font-weight-bold">{{ $t("common.net_room_amount") }}</label>
                                                <p class="mb-0 ml-2 mt-2">{{ total_room_amount - discount_amount }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div  style="background-color: #f8f9fa; border-radius: 6px;" class="p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t("common.room_amount") }}
                                                    <span class="required">*</span>
                                                </p>
                                                <p v-if="final_room_rate_price" class="mb-0">{{ final_room_rate_price }}</p>
                                                <p v-else class="mb-0">0</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t("sidebar.meal_plan") }}
                                                    <span class="required">*</span>
                                                </p>
                                                <p class="mb-0">{{ final_meal_rate_price }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t("common.add_services_amount") }}
                                                    <span class="required">*</span>
                                                </p>
                                                <p class="mb-0">{{ final_facility_rate_price }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t("common.total_amount_after_discount") }}
                                                    <span class="required">*</span>
                                                </p>
                                                <p v-if="final_room_rate_price + final_meal_rate_price + final_facility_rate_price" class="mb-0">{{ final_room_rate_price + final_meal_rate_price + final_facility_rate_price }}</p>
                                                <p v-else class="mb-0">0</p>
                                            </div>
                                            
                                            <div v-for="(items, index) in roomcategoryArray" :key="index" class="mt-4">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t("common.gst") }} :- Room {{ index + 1 }}
                                                </p>
                                                <p class="mb-0 text-muted font-weight-bold mt-3 mb-2" style="font-size: 14px;">Room GST</p>
                                                <div v-for="(item, index) in items.gstRate" :key="index" class="d-flex justify-content-between align-items-center ml-3 mt-1">
                                                    <p class="font-weight-bold text-muted mb-0">{{ item.tax_name.name }}</p>
                                                    <p class="mb-0">{{ item.tax_name.rate }}</p>
                                                </div>
                                                <p class="mb-0 text-muted font-weight-bold mt-3 mb-2" style="font-size: 14px;">Meal GST</p>
                                                <div v-for="(item,index) in items.meal_tax_rate" :key="item" class="d-flex justify-content-between align-items-center ml-3 mt-1">
                                                    <p class="font-weight-bold text-muted mb-0">{{ item?.tax_name.name }}</p>
                                                    <p class="mb-0">{{ item?.tax_name.rate }}</p>
                                                </div>
                                                <p class="mb-0 text-muted font-weight-bold mt-3 mb-2" style="font-size: 14px;">Services GST</p>
                                                <div v-for="(item) in items.facilities_price" :key="item" class="mb-0 mt-1 text-center">
                                                    <div v-for="(items, index) in item.tax_rate" :key="index" class="d-flex justify-content-between align-items-center ml-3 mt-1">
                                                        <p class="font-weight-bold text-muted mb-0">{{ items?.taxname?.name }}</p>
                                                        <p class="mb-0">{{ items?.taxname?.rate }}</p>
                                                    </div>
                                                </div>
                                                <p class="mb-0 text-muted font-weight-bold mt-3 mb-2" style="font-size: 14px;">Extra Bed GST</p>
                                                <div v-for="(item, index) in items.gstRate" :key="index" class="d-flex justify-content-between align-items-center ml-3 mt-1">
                                                    <p class="font-weight-bold text-muted mb-0">{{ item?.tax_name?.name }}</p>
                                                    <p class="mb-0">{{ item?.tax_name?.rate }}</p>
                                                </div>
                                                <p class="mb-0 text-muted font-weight-bold mt-3 mb-2" style="font-size: 14px;">Extra Person GST</p>
                                                <div v-for="(item, index) in items.gstRate" :key="index" class="d-flex justify-content-between align-items-center ml-3 mt-1">
                                                    <p class="font-weight-bold text-muted mb-0">{{ item?.tax_name?.name }}</p>
                                                    <p class="mb-0">{{ item?.tax_name?.rate }}</p>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-4">
                                                <p class="font-weight-bold mb-0">
                                                    {{ $t("common.net_payable_amount") }}
                                                </p>
                                                <p class="mb-0">{{ total_room_amount - discount_amount }}</p>
                                            </div>
                                            <div></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div  style="background-color: #f8f9fa; border-radius: 6px;" class="p-3 h-100">
                                            <div class="mt-4">
                                                <label for="roomRate" class="font-weight-bold">{{ $t("common.choose_payment_mode") }}</label>
                                                <multiselect v-model="payment_mode_id" :options="paymentModeData" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search an option" class="form-control" label="ledger_name" track-by="ledger_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                            </div>
                                            <div class="mt-3">
                                                <label for="discount" class="font-weight-bold">{{ $t("common.advance_amount") }}</label>
                                                <input id="bookingtypetitle" v-model="advance_amount" type="text" class="form-control" name="bookingtypetitle" :placeholder="$t('common.advance_amount_placeholder')" />
                                            </div>
                                            <div class="mt-3">
                                                <label for="discount" class="font-weight-bold">{{ $t("common.pending_amount") }}</label>
                                                <input id="bookingtypetitle" disabled v-model="pending_amount" type="text" class="form-control" name="bookingtypetitle" :placeholder="$t('common.pending_amount_placeholder')" />
                                            </div>

                                            <div class="mt-3">
                                                <label for="advanceRemark" class="font-weight-bold">{{ $t("common.advance_remarks") }}</label>
                                                <textarea id="advanceRemark" rows="2" v-model="form.advance_remarks" type="text" class="form-control" name="advanceRemark" :placeholder="$t('common.advance_remarks_placeholder')" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-100">
                                    <div class="mt-3 col-md-12">
                                        <label for="discount" class="font-weight-bold">{{ $t("common.note") }}</label>
                                        <textarea v-model="form.comments" name="w3review" rows="2" class="w-100 note-textarea p-3" :placeholder="$t('common.note_placeholder')" style="border-radius: 4px; border-color: #ced4da;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <v-button :loading="form.busy" class="btn btn-primary">
                                <i class="fas fa-save" /> {{ $t('common.save') }}
                            </v-button>
                            <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                                <i class="fas fa-power-off" /> {{ $t('common.reset') }}
                            </button>
                        </div>
                    </form>

                    <div class="modal fade" id="newClient" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-width modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Create a Client</h5>
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
                                                        <label for="name">{{ $t('common.name') }}
                                                            <span class="required">*</span></label>
                                                        <input id="name" v-model="clientForm.name" type="text" class="form-control"
                                                            :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                                                            :placeholder="$t('common.name_placeholder')" />
                                                        <has-error :form="form" field="name" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="email">{{ $t('common.email') }}</label>
                                                        <input id="email" v-model="clientForm.email" type="email" class="form-control"
                                                            :class="{ 'is-invalid': form.errors.has('email') }" name="email"
                                                            :placeholder="$t('common.email_placeholder')" />
                                                        <has-error :form="form" field="email" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="phoneNumber">{{ $t('common.contact_number') }}
                                                            <span class="required">*</span></label>
                                                        <input id="phoneNumber" v-model="clientForm.phoneNumber" type="text" class="form-control"
                                                            :class="{ 'is-invalid': form.errors.has('phoneNumber') }" name="phoneNumber"
                                                            :placeholder="$t('common.contact_number_placeholder')" />
                                                        <has-error :form="form" field="phoneNumber" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="companyName">{{
                                                            $t('common.company_name')
                                                        }}</label>
                                                        <input id="companyName" v-model="clientForm.companyName" type="companyName" class="form-control"
                                                            :class="{ 'is-invalid': form.errors.has('companyName') }" name="companyName"
                                                            :placeholder="$t('common.company_name_placeholder')" />
                                                        <has-error :form="form" field="companyName" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="address">{{ $t('common.address') }}</label>
                                                    <textarea id="address" v-model="clientForm.address" class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('address') }"
                                                    :placeholder="$t('common.address_placeholder')" />
                                                    <has-error :form="form" field="address" />
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="status">{{ $t('common.status') }}</label>
                                                        <select id="status" v-model="clientForm.status" class="form-control"
                                                        :class="{ 'is-invalid': form.errors.has('status') }">
                                                            <option value="1">{{ $t('common.active') }}</option>
                                                            <option value="0">{{ $t('common.in_active') }}</option>
                                                        </select>
                                                        <has-error :form="form" field="status" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="image">{{ $t('common.image') }}</label>
                                                        <div class="custom-file">
                                                            <input id="image" type="file" class="custom-file-input" name="image"
                                                            :class="{ 'is-invalid': form.errors.has('image') }" @change="onFileChange" />
                                                            <label class="custom-file-label" for="image">{{
                                                                $t('common.choose_file')
                                                            }}</label>
                                                        </div>
                                                        <has-error :form="form" field="image" />
                                                        <div class="bg-light mt-4 w-25">
                                                            <img v-if="url" :src="url" class="img-fluid" :alt="$t('common.image_alt')" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <v-button :loading="form.busy" class="btn btn-primary">
                                                    <i class="fas fa-save" /> {{ $t('common.save') }}
                                                </v-button>
                                                <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                                                    <i class="fas fa-power-off" /> {{ $t('common.reset') }}
                                                </button>
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
import { mapGetters } from 'vuex'
import axios from "axios";
import Form from 'vform'
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import Multiselect from 'vue-multiselect'
import debounce from 'lodash.debounce';

export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('booking.create.page_title') }
    },
    components:{
        DatePicker,
        Multiselect
    },
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems', "hotelItems", "bedTypeItems", "floorItems"]),
    },
    data: () => ({
        breadcrumbsCurrent: 'booking.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'booking.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'booking.create.breadcrumbs_second',
                url: 'booking',
            },
            {
                name: 'booking.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            type: 1,
            customer: [{
                customer_id: '',
                customer_id_request: null
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
            rooms:[]
        }),
        loading: false,
        time1: null,
        time2: null,
        arrivalForm: '',
        puposeOfVisit: '',
        remarks: '',
        bookingTypeData: [
            {
                id: 1,
                title: "Initial Booking" 
            },
            {
                id: 2,
                title: "Chechk in"
            },
            {
                id: 3,
                title: "Check out" 
            }
        ],
        total_room_amount: 0,
        roomcategoryArray: [
            {
                id: 1,
                room_booking_id: '',
                room_number_id: '',
                roomNumber: [],
                adult: '',
                children: '',
                infant: '',
                room_rate: '',
                room_gst_tax_rate_price: '',
                gstRate: [],
                clientItemsData: [],
                meal_plan_id: '',
                mealListData: [],
                extra_meal_plan: '',
                meal_plan_price: '',
                meal_rate_extra_price: '',
                meal_tax_rate: [],
                meal_rate_tax_rate_price: '',
                facilitiesListData: [],
                room_facility_id: '',
                facilities_price: [],
                facilities_rate_price: '',
                facility_gst_rate_price: '',
                extra_bed: '',
                extra_bed_rate: '',
                extra_bed_rate_price: '',
                extra_person: '',
                extra_person_rate_price: '',
            }
        ],
        bookedByList: [],
        bookedBy: '',
        booked_by_form: new Form({
            leadsSupplierType: ''
        }),
        room_category_form: new Form({
            cat_id: '',
            hotel_id: '',
        }),
        room_number_form: new Form({
            id: ''
        }),
        booking_source: '',
        room_booking_id: '',
        roomNumber: [],
        room_number_id: '',
        room_facility_id: '',
        discountRoomRateData: [
            {
                title: "Fixed",
                id: 1,
            },
            {
                title: "Percentage",
                id: 2,
            }
        ],
        bookedStatusData: [
            {
                id: 1,
                title: "Pending"
            },
            {
                id: 2,
                title: "Hold"
            },
            {
                id: 3,
                title: "Confirmed"
            },
            {
                id: 4,
                title: "Available"
            },
        ],
        discount_room_rate: 0,
        payment_method_form: new Form({
            ledger_type: ''
        }),
        paymentModeData: [],
        payment_mode_id: '',
        meal_plan_id: '',
        discount: 0,
        discount_amount: 0,
        net_payable_amount: 0,
        advance_amount: 0,
        pending_amount: 0,
        meal_plan_form: new Form({
            meal_id: '',
            hotel_id: ''
        }),
        meal_plan_price: 0,
        extra_meal_plan: 0,
        meal_rate_extra_price: 0,
        extra_bed: 0,
        extra_bed_rate: 0,
        extra_bed_rate_price: 0,
        extra_person: 0,
        extra_person_rate_price: 0,
        hotel_id: '',
        gstRate: [],
        meal_list_form: new Form({
            hotel_id: ''
        }),
        mealListData: [],
        facilities_list_form: new Form({
            hotel_id: ''
        }),
        facilitiesListData: [],
        meal_tax_rate: [],
        meal_rate_tax_rate_price: 0,
        facilities_single_form: new Form({
            id: ''
        }),
        facilities_price: [],
        room_gst_tax_rate_price: 0,
        facilities_rate_price: 0,
        gstFacilitiRate: [],
        facility_gst_rate_price: 0,
        totalDays: 0,
        totalDaysStatic: 0,
        clientForm: new Form({
            name: '',
            email: '',
            phoneNumber: '',
            companyName: '',
            address: '',
            image: '',
            status: 1,
        }),
        clientPhone: '',
        clientName: '',
        clientId: '',
        clientItemsData: [],
        isSuggestionBox: false,
        client_booking_status_id: '',
        final_room_rate_price: '',
        final_meal_rate_price: '',
        final_facility_rate_price: '',
        clientItemId: '',
        room_price_final_with_gst: '',
    }),
    created() {
        this.getBookingType()
        this.getHotelDataList()
        this.getRoomCategoryData()
        this.getLedgerListData()
    },
    watch: {
        clientPhone: function (newQ, oldQ) {
            if (newQ != '') {
                this.isSuggestionBox = true
                this.searchClientData()
            }else{
                this.isSuggestionBox = false
            }
        },
        time1: {
            handler: 'calculateDays',
        },
        time2: {
            handler: 'calculateDays',
        },
        booking_source(currentValue) {
            this.booked_by_form.leadsSupplierType = currentValue.id

            this.getBookedBySupplier()
        },
        discount(currentValue) {
            if (this.discount_room_rate.id == 1) {
                this.discount_amount = this.discount
            }else{
                this.discount_amount = ((this.room_price_final_with_gst * currentValue) /100)
            }
        },
        discount_room_rate() {
            this.discount_amount = ''
            this.discount = ''
        },
        advance_amount(currentValue) {
            this.pending_amount = ((this.total_room_amount - this.discount_amount) - currentValue)
        },
        hotel_id(currentValue) {
            this.getMealPlanData(currentValue.id)
            this.getRoomFacilityData(currentValue.id)
        },
    },
    methods: {
        room_price_with_gst(item) {
            this.room_price_final_with_gst = item
        },
        test(item) {
            this.clientItemId = item.id
        },
        roomRate(rate) {
            this.final_room_rate_price = rate
        },
        mealRate(rate) {
            this.final_meal_rate_price = rate
        },
        facilityRate(rate) {
            this.final_facility_rate_price = rate
        },
        subTotal() {
            const test = []
            this.roomcategoryArray.forEach((el)=>{
                this.total_room_amount = 0
                test.push(((el.room_rate || 0) * this.totalDays) + (((el.room_rate || 0) * this.totalDays) * el.room_gst_tax_rate_price /100) + (el.meal_rate_extra_price * this.totalDays) + ((el.meal_rate_extra_price * this.totalDays) * el.meal_rate_tax_rate_price / 100) + (el.facilities_rate_price * this.totalDays) + ((el.facilities_rate_price * this.totalDays) * el.facility_gst_rate_price / 100) + (el.extra_bed_rate_price * this.totalDays) + ((el.extra_bed_rate_price * this.totalDays) * el.room_gst_tax_rate_price / 100) + (el.extra_person_rate_price * this.totalDays) + ((el.extra_person_rate_price * this.totalDays) * el.room_gst_tax_rate_price / 100))
            })
            if (test) {
                test.forEach((el) => {
                    this.total_room_amount+=el
                })
            }
        },
        roomGstSum(val,item){
            const sum = val.reduce((total, el) => {
                return total + el.tax_name.rate;
            }, 0);
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.room_gst_tax_rate_price = sum
                }
            })
        },
        mealGstSum(val,item){
            const sum = val.reduce((total, el) => {
                return total + el.tax_name.rate;
            }, 0);
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.meal_rate_tax_rate_price = sum
                }
            })
        },
        facilityGstSum(val,item){
            const sum = val.reduce((total, el) => {
                return total + el.price;
            }, 0);

            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.facilities_rate_price = sum

                    val.forEach((ele) => {
                        ele.tax_rate.forEach((element) => {
                            this.gstFacilitiRate.push(element.taxname.rate)
                        })
                    })

                    const gstFacility = this.gstFacilitiRate.reduce((total, el) => {
                        return total + el;
                    }, 0)

                    el.facility_gst_rate_price = gstFacility
                    
                    this.gstFacilitiRate = []
                }
            })
        },
        extraBedGstSum(val,item){
            const sum = val.reduce((total, el) => {
                return total + el.tax_name.rate;
            }, 0);
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.meal_rate_tax_rate_price = sum
                }
            })
        },
        mealPersonChange(item) {
            this.loading = true
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.meal_rate_extra_price = ((el.extra_meal_plan) * (el.meal_plan_price))

                    this.loading = false
                }
            })
        },
        roomCategorySelect(item) {
            item.room_number_id = ''
            item.room_rate = 0
            item.gstRate = []
            item.adult = ''
            item.children = ''
            item.infant = ''
            item.room_gst_tax_rate_price = 0

            this.room_category_form.cat_id = item?.room_booking_id?.id
            this.room_category_form.hotel_id = this.hotel_id.id

            this.getRoomCategoryListData(item.id)
        },
        roomNumberSelect(items, item) {
            this.room_number_form.id = item.id

            this.getRoomNumberListData(item.id, items.id)
        },
        async roomFacilitySelect(item, facility_id) {
            const idArray = facility_id.map((el) => el.id);
            const idString = idArray.join(',');
            
            this.facilities_single_form.id = idString
            const { data } = await this.facilities_single_form.post(
                window.location.origin +
                '/api/room/category/lsit/facility/price/hotel/singel'
            )
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.facilities_price = data.data
                }
            })
        },
        async mealPlanData(item, id) {
            this.meal_plan_form.meal_id = id
            this.meal_plan_form.hotel_id = this.hotel_id.id
            const { data } = await this.meal_plan_form.post(
                window.location.origin +
                '/api/hotel/room/meal/view'
            )
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.meal_plan_price = data.data.price,
                    el.meal_tax_rate = data.data.tax_rate
                }
            })
        },
        extraBedRateChange (item) {
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.extra_bed_rate_price = ((el.extra_bed_rate) * (el.extra_bed))
                }
            })
        },
        extraPersonChange (item) {
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.extra_person_rate_price = ((el.meal_plan_price) * (el.extra_person))
                }
            })
        },
        notBeforeToday(date) {
            return date < new Date(new Date().setHours(0, 0, 0, 0));
        },
        notBeforeTodayTwelveOClock(date) {
            return date < new Date(new Date().setHours(12, 0, 0, 0));
        },
        notBeforeCheckIn(date) {
            return date < this.time1;
        },
        notBeforeCheckInTwelveOClock(date) {
            return date < this.time1;
        },
        calculateDays() {
            const differenceInMilliseconds = this.time2 - this.time1;
            this.totalDays = Math.floor(differenceInMilliseconds / (1000 * 60 * 60 * 24));
        },
        addRoomCategory() {
            this.roomcategoryArray.push({
                id: this.roomcategoryArray.length + 1,
                room_booking_id: '',
                room_number_id: '',
                roomNumber: [],
                adult: '',
                children: '',
                infant: '',
                room_rate: '',
                room_gst_tax_rate_price: '',
                gstRate: [],
                clientItemsData: [],
                meal_plan_id: '',
                mealListData: [],
                extra_meal_plan: '',
                meal_plan_price: '',
                meal_rate_extra_price: '',
                meal_tax_rate: [],
                meal_rate_tax_rate_price: '',
                facilitiesListData: [],
                room_facility_id: '',
                facilities_price: [],
                facilities_rate_price: '',
                facility_gst_rate_price: '',
                extra_bed: '',
                extra_bed_rate: '',
                extra_bed_rate_price: '',
                extra_person: '',
                extra_person_rate_price: '',
            })

            this.getMealPlanData(this.hotel_id.id)
            this.getRoomFacilityData(this.hotel_id.id)
        },
        removeItem(index) {
            const newArray = [...this.roomcategoryArray];
            newArray.splice(index, 1);
            this.roomcategoryArray = newArray;
        },
        async getBookingType() {
            await this.$store.dispatch('operations/getFacilityData', {
                path: '/api/booking/type/list'
            })
        },

        async getHotelDataList() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/hotel'
            })
        },

        async getRoomCategoryData() {
            await this.$store.dispatch('operations/getHotelCategoryData', {
                path: '/api/room/category'
            })
        },

        async getMealPlanData(id) {
            this.meal_list_form.hotel_id = id
            const { data } = await this.meal_list_form.post(
                window.location.origin +
                '/api/hotel/room/meal/view/all/name'
            )

            this.roomcategoryArray.forEach((el)=>{
                el.mealListData = data.data
            })
        },

        async getRoomFacilityData(id) {
            this.facilities_list_form.hotel_id = id
            const { data } = await this.facilities_list_form.post(
                window.location.origin +
                '/api/room/category/lsit/facility/price/hotel'
            )

            this.roomcategoryArray.forEach((el)=>{
                el.facilitiesListData = data.data
            })
        },

        async getBookedBySupplier () {
            const { data } = await this.booked_by_form.post(
                window.location.origin +
                '/api/get/supplier'
            )
            this.bookedByList = data.data
            this.loading = false
        },

        async getLedgerListData () {
            this.payment_method_form.ledger_type = ''
            const { data } = await this.payment_method_form.post(window.location.origin + '/api/account/ledger/list')
            this.paymentModeData = data.data
        },

        async getRoomCategoryListData (id) {
            const { data } = await this.room_category_form.post(
                window.location.origin +
                '/api/hotel/get-hotel-cat'
            )
            this.roomcategoryArray.forEach((el) => {
                data.data = data.data.filter((ele) => el.room_number_id.id !== ele.id);

                if (el.id === id) {
                    el.roomNumber = data.data;
                }
            });
        },

        async getRoomNumberListData (id , itemId) {
            const { data } = await this.room_number_form.post(
                window.location.origin +
                '/api/hotel/get-single-room'
            )

            this.roomcategoryArray.forEach((el)=>{
                if (el.id === itemId) {
                    el.adult = data.data.no_of_person,
                    el.children = data.data.no_of_child,
                    el.infant = data.data.no_of_infant,
                    el.room_rate = data.data.room_rate,
                    el.extra_bed_rate = data.data.extra_bed_rate,
                    el.gstRate = data?.data?.tax_rate
                }
            })
        },
        onFileChange(e) {
            const file = e.target.files[0]
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                file.type === 'image/png' ||
                file.type === 'image/gif')
            ) {
                this.clientForm.image = file
                this.url = URL.createObjectURL(file)
            } else {
                Swal.fire(
                    this.$t('common.error'),
                    this.$t('common.image_error'),
                    'error'
                )
            }
        },

        async saveClient() {
            await this.clientForm
                .post(window.location.origin + '/api/clients')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('clients.create.success_msg'),
                    })
                    this.$router.push({ name: 'booking.create' })

                    this.roomcategoryArray.forEach((el)=>{
                        if (el.id === this.clientItemId) {
                            el.clientItemsData.push({
                                id: this.clientForm.id,
                                clientPhone: this.clientForm.phoneNumber,
                                clientName: this.clientForm.name
                            })
                        }
                    })
                    $('#newClient').modal('hide');
                })
                .catch(() => {
                        toast.fire({ type: 'error', title: this.$t('common.error_msg') })
                    })
        },
        async searchClientData(){
            this.searchData(this.$store,this.clientPhone);
        },
         searchData: debounce(async(store,clientPhone)=>{
            // this.$store.state.operations.loading = true
           await store.dispatch('operations/searchData', {
                path: '/api/clients/search',
                term: clientPhone,
            })
        },1000),

        async reload() {
            this.clientPhone = ''
            await this.searchClientData()
        },

        async clientChange(id) {
            setTimeout(() => {
                this.isSuggestionBox = false
            }, 100);

            const { data } = await axios.get(
                window.location.origin +
                '/api/clients/' + id
            )

            this.clientId = data.data.id
            this.clientPhone = data.data.phoneNumber
            this.clientName = data.data.name

        },

        saveOldClient (item) {
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === this.clientItemId) {
                    el.clientItemsData.push({
                        id: this.clientId,
                        clientPhone: this.clientPhone,
                        clientName: this.clientName
                    })
                }
            })
            $('#oldClient').modal('hide');
            this.clientId = ''
            this.clientPhone = ''
            this.clientName = ''
            this.clientItemId = ''
        },

        removeClientItem(index, item) {
            this.roomcategoryArray.forEach((el)=>{
                if (el.id === item.id) {
                    el.clientItemsData.splice(index, 1);
                } 
            })
        },

        async saveBooking() {
            this.form.type = 1
            this.roomcategoryArray.forEach((ele) => {
                ele.clientItemsData.forEach((element, index) => {
                    console.log('element',element);
                    this.form.customer[index] = { customer_id: element.id, customer_id_request: null };
                })
                ele.clientItemsData.forEach((el) => {
                    if (el?.checked) {
                        this.form.booked_by = el.id
                    }
                })
            })
            this.form.total_room = 1,
            this.form.client_booking_status = this.client_booking_status_id.id
            this.form.advance_amount = this.advance_amount
            this.form.paid_amount = this.pending_amount
            this.form.total_price = this.total_room_amount
            this.form.payment_method = this.payment_mode_id.id
            this.form.remarks = this.remarks
            this.form.discount_amount = this.discount_amount
            this.form.discount_reason = null
            this.form.full_guest_name = null
            this.form.special_request = null
            this.form.check_in_date = this.time1
            this.form.check_out_date = this.time2
            this.form.hotel_id = this.hotel_id.id
            this.form.booked_from = this.booking_source.id
            this.form.booking_source = this.bookedBy.id
            this.form.arrival_from = this.arrivalForm

            this.roomcategoryArray.forEach((el) => {
                if (el.room_booking_id.id) {
                    const roomObj = {
                        room_booking_id: null,
                        room_id: el.room_booking_id.id,
                        room_no: el.room_number_id.id,
                        adult: el.adult,
                        children: el.children,
                        infant: el.infant,
                        meal_id: el.meal_plan_id.id,
                        extra_bed: el.extra_bed,
                        extra_person: el.extra_person,
                        extra_child: null,
                        complementary: [],
                        extra_facility_days: this.totalDays
                    };
    
                    el.room_facility_id.forEach((element) => {
                        roomObj.complementary.push({ complementary: element.id, complementary_booked_id: null });
                    });
    
                    this.form.rooms.push(roomObj);
                }
            });

            await this.form
                .post(window.location.origin + '/api/booking/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('booking.create.success_msg'),
                    })
                    this.$router.push({ name: 'booking' })
                })
                .catch(() => {
                    toast.fire({ type: 'error', title: this.$t('common.error_msg') })
                })
        },

        async getBookingData () {
            const { data } = await axios.get(window.location.origin + '/api/booking/view/' + this.$route.params.slug)
            console.log("data--->",data.data);
            let bookingData=data.data;
            // this.time1=new Date("02-08-2023 12:00:00");
            // this.time2="04-08-2023 12:00:00";
            this.arrivalForm=bookingData.arrival_from;
            this.puposeOfVisit=bookingData.purpose;
            this.remarks=bookingData.advance_remarks;
            // this.bookedBy=
            this.client_booking_status_id=bookingData.client_booking_status==0 && {
                "id": "1",
                "title": "Pending"
            };
            this.hotel_id=bookingData.hotel;
            this.roomcategoryArray=bookingData.booking_details;
            // this.bookedStatusData = bookingData
            this.discount_amount= bookingData.discount_amount;
            // this.discount=


        }
    },
    beforeDestroy() {
        $('.modal-backdrop').remove();
    },
    mounted(){
        console.log('this.$route.params.slug',this.$route.params.slug);
        
        this.$route?.params?.slug && this.getBookingData()
    },
}
</script>
<style src="../../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
:deep(.multiselect__tags) {
    min-height: 38px !important;
    border: none !important;
    padding: 4px 40px 0 4px !important;
}
:deep(.multiselect__placeholder){
    margin-bottom: 4px !important;
    padding-top: 4px !important;
}
:deep(.multiselect__single) {
    margin-bottom: 0px !important;
    margin-top: 4px !important;
}
:deep(.multiselect){
    width: auto;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
    min-height: 38px !important;
}
.room-booking-form{
    background-color: #f2f4f5;
    border-radius: 6px;
}
.close-icon{
    width: 25px;
    display: flex;
    height: 25px;
    justify-content: center;
    align-items: center;
    font-size: 15px;
}
.plus-icon{
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
}
.note-textarea:focus-visible{
    outline: none;
}
:deep(.mx-input){
    height: 38px !important;
}
:deep(.multiselect__single) {
    text-overflow: ellipsis !important;
    overflow: hidden !important;
}
@media (min-width: 1200px){
    .modal-dialog.modal-width {
        max-width: 1000px;
    }
}
.search-area{
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
    box-shadow: 0px 3px 7px rgba(0,0,0,0.2);
    max-height: 200px;
    overflow: auto;
}
</style>