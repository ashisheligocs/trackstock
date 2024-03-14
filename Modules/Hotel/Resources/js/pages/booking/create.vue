<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div v-if="initialLoading" class="d-flex justify-content-center position-relative">
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
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
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
                                    <multiselect @remove="changeHotel" @select="changeHotel" v-model="hotel_id"
                                        :disabled="!!slugdata" :options="hotelItems" :show-labels="false"
                                        tag-placeholder="Add this as new tag" placeholder="Search a hotel"
                                        class="form-control" label="hotel_name" track-by="hotel_name"
                                        :class="{ 'is-invalid': form.errors.has('hotel_id') }"></multiselect>
                                    <has-error :form="form" field="hotel_id" />
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="input-group">

                                        <label for="check_in_date" class="d-block col-12">Check In - Out
                                            <span class="required">*</span></label>
                                        <div class="d-flex w-100">

                                            <date-range-picker ref="bookingPicker" opens="right"
                                                :singleDatePicker="false" :autoApply="true" v-model="dateRange"
                                                :ranges="false" @update="updateValues" class="w-100">
                                                <template v-slot:input="picker" style="min-width: 350px">
                                                    {{ check_in_date | startDate }} -
                                                    {{ check_out_date | endDate }}
                                                </template>
                                            </date-range-picker>
                                            <div class="input-group-append"
                                                style="max-width: 46px; margin-left: -3px; position: relative;">
                                                <input v-if="check_in_date && check_out_date" id="total_day"
                                                    v-model="totalDays" type="text"
                                                    class="form-control input-group-text" disabled name="total_day"
                                                    :placeholder="$t('common.total_days')" />
                                                <input v-else id="total_day" v-model="totalDaysStatic" type="text"
                                                    class="form-control input-group-text" disabled name="total_day"
                                                    :placeholder="$t('common.total_days')" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group col-md-4">
                                    <label for="total_day" class="d-block">{{ $t('common.total_days') }}</label>
                                    <input v-if="check_in_date && check_out_date" id="total_day" v-model="totalDays"
                                        type="text" class="form-control" disabled name="total_day"
                                        :placeholder="$t('common.total_days')" />
                                    <input v-else id="total_day" v-model="totalDaysStatic" type="text"
                                        class="form-control" disabled name="total_day"
                                        :placeholder="$t('common.total_days')" />
                                </div> -->
                                <div class="form-group col-md-4">
                                    <label for="arrivalFrom" class="d-block">Arrival From</label>
                                    <input id="arrivalFrom" v-model="arrivalForm" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                        name="bookingtypetitle" :placeholder="$t('common.arrival_form_placeholder')" />
                                    <has-error :form="form" field="arrival_from" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="bookingtypetitle" class="d-block">GRN Number</label>
                                    <input id="bookingtypetitle" v-model="puposeOfVisit" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                        name="bookingtypetitle" placeholder="Enter GRN Number" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="booked_by" class="d-block">{{ $t('common.booking_source') }}</label>
                                    <multiselect v-model="bookedBy" :options="bookedByList" :show-labels="false"
                                        tag-placeholder="Add this as new tag" :custom-label="customBookedByLabel"
                                        placeholder="Search a booking by" class="form-control" label="name"
                                        track-by="name" :class="{ 'is-invalid': form.errors.has('booked_by') }">
                                    </multiselect>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="hotel" class="d-block">{{ $t('common.booked_status') }}
                                        <span class="required">*</span></label>
                                    <multiselect v-model="client_booking_status_id" :options="bookedStatusData"
                                        :show-labels="false" tag-placeholder="Add this as new tag"
                                        placeholder="Search a booked status" class="form-control" label="name"
                                        track-by="name"></multiselect>
                                    <has-error :form="form" field="client_booking_status" />
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div v-if="slugdata" class="col-12 border-top" style="margin: 2rem 0">
                                    <div class="d-flex justify-content-between" style="margin-top: 2rem">
                                        <button class="btn btn-secondary" @click="clientPlusClick" role="button"
                                            data-toggle="modal" data-target="#newClient">
                                            &nbsp; Customer Info.
                                        </button>
                                    </div>

                                    <div v-if="clientItemsData.length"
                                        class="d-flex justify-content-between px-4 py-3 border-b-gray-1">
                                        <label>Name</label>
                                        <label>Phone</label>
                                        <label>Action</label>
                                    </div>
                                    <div v-for="(clientItem, key) in clientItemsData" :key="key + '3'"
                                        class="d-flex justify-content-between px-4 py-1 align-items-center border-b-gray-1">
                                        <div style="width: 180px">
                                            <label :for="clientItem.id">{{ clientItem.clientName }}</label>
                                        </div>
                                        <div class="w-25">
                                            {{ clientItem.clientPhone }}
                                        </div>
                                        <div>
                                            <button @click="removeClientItemEdit(key)" type="reset"
                                                class="btn btn-danger close-icon">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="form-box">
                                <div v-if="!slugdata" v-for="(item, index) in roomcategoryArray" :key="index + '1'"
                                    class="form-group col-md-12 room-booking-form px-2 py-3 mt-3 w-100">
                                    <!-- {{ item }} -->
                                    <div class="row">

                                        <div class="form-group col-md-3">
                                            <label for="room_hotel_cat_lsit" class="d-block"><i
                                                    class="fa-regular fa-rectangle-list"></i> {{
            $t('sidebar.room_hotel_cat_lsit') }}
                                                <span class="required">*</span></label>

                                            <multiselect @select="roomCategorySelect(item)"
                                                v-model="item.room_booking_id" :options="hotelCategoryItems"
                                                :show-labels="false" tag-placeholder="Add this as new tag"
                                                placeholder="Select Room category" class="form-control"
                                                :custom-label="nameWithRoomCount" label="room_category_name"
                                                track-by="room_category_name"
                                                :class="{ 'is-invalid': form.errors.has('room_booking_id') }">
                                            </multiselect>
                                        </div>
                                        <!-- <div class="form-group col-md-3">
                                            <label for="room_number" class="d-block">{{ $t('common.room_number') }}
                                                <span class="required">*</span></label>
                                            <multiselect @select="roomNumberSelect(item, item.room_number_id)" v-model="item.room_number_id" :options="item.roomNumber" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Room number" class="form-control" label="room_name" track-by="room_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                        </div> -->
                                        <div class="form-group col-md-3">
                                            <label for="noOfRooms" class="d-block"><i class="fa-solid fa-house"></i>No
                                                of rooms</label>
                                            <input id="noOfRooms" min="1" :max="item.roomNumber.length"
                                                v-model="item.noOfRooms" @input="limitNoOfRoom($event, item, index)"
                                                type="number" class="form-control"
                                                :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                name="bookingtypetitle" placeholder="Enter No of rooms" />

                                            <!-- <multiselect @select="roomNumberSelect(item, item.room_number_id)" v-model="item.room_number_id"   :options="item.roomNumber.slice(0, 23)"  :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Room number" class="form-control" label="room_name" track-by="room_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect> -->

                                            <p class="text-primary small m-0" v-if="item.noOfRoomsLimit">
                                                Max {{ item.roomNumber.length }}
                                            </p>

                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="d-block">
                                                <i class="fa-solid fa-utensils"></i>{{ $t('sidebar.meal_plan')
                                                }}</label>
                                            <multiselect
                                                @select="mealPlanData(item, item.meal_plan_id), mealPersonChange(item)"
                                                v-model="item.meal_plan_id" :options="item.mealListData"
                                                :show-labels="false" tag-placeholder="Add this as new tag"
                                                placeholder="Meal plan" class="form-control mr-2" label="shortName"
                                                track-by="shortName"
                                                :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }">
                                            </multiselect>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="room_rate"><i class="fa-solid fa-indian-rupee-sign"></i> Room
                                                Rate</label>
                                            <input v-if="item?.room_rate !== undefined" class="form-control"
                                                type="number" :value="item?.room_rate | twoPointTextBox" step="0.01"
                                                @change="changeRoomRateValue($event, item)">
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label for="adult" class="d-block"><i class="fas fa-male"></i>{{
            $t('common.adult') }}</label>
                                            <input id="adult" min="0" :max="item.adult" :value="item.adult"
                                                @input="limitAdult($event, item, index)" type="number"
                                                class="form-control"
                                                :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                name="bookingtypetitle" :placeholder="$t('common.adult_placeholder')" />
                                            <p class="text-primary small m-0" v-if="item.adultLimit">Max {{
            item.adultLimit }}
                                            </p>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="children" class="d-block"><i class="fas fa-child"></i>{{
            $t('common.children')
        }}</label>
                                            <input id="children" min="0" :max="item.children" :value="item.children"
                                                @input="limitChildren($event, item, index)" type="number"
                                                class="form-control"
                                                :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                name="bookingtypetitle"
                                                :placeholder="$t('common.children_placeholder')" />
                                            <p class="text-primary small m-0" v-if="item.childrenLimit">Max {{
            item.childrenLimit
        }}
                                            </p>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="infant" class="d-block"><i class="fa-solid fa-baby"></i>
                                                {{ $t('common.infant')
                                                }}</label>
                                            <input id="infant" min="0" :max="item.infant" :value="item.infant"
                                                @input="limitInfant($event, item, index)" type="number"
                                                class="form-control"
                                                :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                name="bookingtypetitle"
                                                :placeholder="$t('common.infant_placeholder')" />
                                            <span class="text-primary" v-if="item.infantLimit">Max {{
            item.infantLimit
        }}
                                            </span>

                                            <!-- <input type="text" name="room_rate" class="form-control" id="room_rate" value="700" disabled> -->
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label for="infant" class="d-block">
                                                &nbsp</label>

                                            <span @click="toggleVisibility(item,index)">

                                                <i class="fa-solid fa-list"></i>
                                            </span>

                                            <!-- <input type="text" name="room_rate" class="form-control" id="room_rate" value="700" disabled> -->
                                        </div>

                                        <!-- {{ item.visible }} -->
                                        <div class="col-12 d-flex flex-wrap py-3 show_additional" v-if="item.visible">
                                            <!-- meal plan -->

                                            <div class="form-group col-md-2 m-md-0 pl-0">
                                                <label class="mb-0 mr-1 d-block"> <i
                                                        class="fa-solid fa-people-line"></i>No. Of Meals</label>
                                                <input id="bookingtypetitle" @change="mealPersonChange(item)"
                                                    v-model="item.extra_meal_plan" type="number" class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                    name="bookingtypetitle"
                                                    :placeholder="$t('common.meal_plan_placeholder')"
                                                    :disabled="item.meal_plan_id && item.meal_plan_id.length === 0" />

                                                <span class="text-info small col-12 m-0 mt-2">Enter person to
                                                    get proper
                                                    amount.
                                                </span>
                                            </div>
                                            <!-- finished meal plan -->

                                            <!-- extra bed -->
                                            <div class="form-group col-md-3 m-md-0">
                                                <label for="check_in_date" class="d-block m-0"><i
                                                        class="fa-solid fa-bed"></i> {{
            $t('common.extra_bed') }} </label>
                                                <input id="bookingtypetitle" @change="extraBedRateChange($event, item)"
                                                    v-model="item.extra_bed" type="number" min="0" class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                    name="bookingtypetitle"
                                                    :placeholder="$t('common.extra_bed_placeholder')" />
                                                <p class="text-primary small m-0" v-if="bedCapacityError">Max {{
            item.extra_bed_capacity || 0
        }}
                                                </p>
                                            </div>
                                            <!-- extra person -->
                                            <div class="form-group col-md-3 m-md-0">
                                                <label for="check_in_date" class="d-block m-0"> <i
                                                        class="fa-solid fa-person"></i>{{
                $t('common.extra_person') }}</label>
                                                <input id="bookingtypetitle" @change="extraPersonChange($event, item)"
                                                    v-model="item.extra_person" type="number" min="0"
                                                    class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                    name="bookingtypetitle"
                                                    :placeholder="$t('common.extra_person_placeholder')" />
                                                <span class="text-primary small m-0" v-if="bedCapacityError">Max{{
            item.extra_person_capacity || 0 }}</span>
                                            </div>
                                            <!-- end extra person -->

                                        </div>



                                    </div>
                                    <div class="row">
                                        <div class="col-12 d-flex">
                                            <span @click="clientPlusClick(item)" role="button" data-toggle="modal"
                                                data-target="#newClient" class="btn btn-secondary">

                                                <span @click="clientPlusClick" role="button" data-toggle="modal"
                                                    data-target="#newClient">
                                                    <i class="fa-regular fa-user mr-2"></i>Select Customer
                                                </span>
                                            </span>

                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-outline-secondary ml-auto mr-2"
                                                data-toggle="modal" :data-target="`#exampleModal${index}`">
                                                <i class="fa-solid fa-calculator mr-2"></i>
                                                {{ (((item.room_rate || 0) * item.noOfRooms * totalDays) +
            (((item.room_rate || 0) * item.noOfRooms * totalDays) *
                item.total_tax / 100) + (item.meal_rate_extra_price * totalDays *
                    item.noOfRooms) +
            (((item.meal_rate_extra_price * totalDays * item.noOfRooms)) *
                item.meal_sum_tax_rate / 100) +
            (item.facilities_rate_price * totalDays) + (((item.facilities_rate_price
                * totalDays) *
                item.facility_gst_rate_price) / 100) +
            (item.extra_bed_rate_price * totalDays * item.noOfRooms) +
            ((item.extra_bed_rate_price * totalDays * item.noOfRooms) *
                item.total_tax / 100) +
            (item.extra_person_rate_price * totalDays * item.noOfRooms) +
            (((item.extra_person_rate_price * totalDays * item.noOfRooms)) *
                (item.total_tax) / 100)) | withCurrency }}
                                            </button>

                                            <!-- CUSTOMER INFO BUTTON -->

                                        </div>




                                        <!-- CUSTOMER INFO DETAILS WHICH IS SECLECTED -->
                                        <div class="col-6 mt-3">


                                            <table class="table table-striped table-hover bg-white table-bordered">
                                                <tr v-for="(clientItem, key) in item.clientItemsData" :key="key + '3'">
                                                    <td>
                                                        <input v-model="clientItem.checked" type="radio"
                                                            :id="clientItem.id" :name="index + 'fav_language'"
                                                            value="9898989898">
                                                        <label :for="clientItem.id">{{ clientItem.clientName
                                                            }}</label>
                                                    </td>
                                                    <td> {{ clientItem.clientPhone }}</td>
                                                    <td> <button @click="removeClientItem(key, item)" type="reset"
                                                            class="btn btn-danger close-icon">
                                                            <i class="fas fa-times"></i>
                                                        </button></td>
                                                </tr>
                                            </table>
                                            <!-- <div v-if="item.clientItemsData.length"
                                                class="d-flex justify-content-between px-4 py-3 border-b-gray-1">
                                                <label>Name</label>
                                                <label>Phone</label>
                                                <label>Action</label>
                                            </div> -->
                                            <!-- <div v-for="(clientItem, key) in item.clientItemsData" :key="key + '3'"
                                                class="d-flex justify-content-between px-4 py-1 align-items-center border-b-gray-1 customer_table">
                                                <div style="width: 180px">
                                                    <input v-model="clientItem.checked" type="radio" :id="clientItem.id"
                                                        :name="index + 'fav_language'" value="9898989898">
                                                    <label :for="clientItem.id">{{ clientItem.clientName
                                                        }}</label>
                                                </div>
                                                <div class="w-25">
                                                    {{ clientItem.clientPhone }}
                                                </div>
                                                <div>
                                                    <button @click="removeClientItem(key, item)" type="reset"
                                                        class="btn btn-danger close-icon">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div> -->
                                        </div>


                                        <!-- OLD CLIENT EDITING MODAL -->
                                        <div class="modal fade" id="oldClient" tabindex="-1" role="dialog"
                                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Old
                                                            Client
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div>
                                                            <form role="form" @submit.prevent="saveOldClient('item')"
                                                                @keydown="clientForm.onKeydown($event)">
                                                                <div>
                                                                    <div v-if="isLoadingClient"
                                                                        class="d-flex justify-content-center position-relative">
                                                                        <div class="spinner-border z-5" role="status">
                                                                            <span class="sr-only">Loading...</span>
                                                                        </div>
                                                                    </div>
                                                                    <div v-else-if="isSuggestionBox"
                                                                        :class="items?.length > 0 ? 'bg-white p-3' : ''">
                                                                        <div class="form-group">
                                                                            <label for="customer" class="d-block">{{
            `customer` }}</label>
                                                                            <!-- allClientList -->
                                                                            <multiselect @select="clientChange"
                                                                                @remove="clientChange"
                                                                                v-model="selectedOldCustomer"
                                                                                :options="allClientList"
                                                                                :show-labels="false"
                                                                                tag-placeholder="Add this as new tag"
                                                                                placeholder="Search a customer"
                                                                                class="form-control"
                                                                                :custom-label="({ name, phone }) => `${name} - ${phone}`"
                                                                                label="name" track-by="id">
                                                                            </multiselect>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- /.card-body -->
                                                                <div class="card-footer">
                                                                    <v-button :loading="form.busy"
                                                                        class="btn btn-primary">
                                                                        <i class="fas fa-save" /> {{ $t('common.save')
                                                                        }}
                                                                    </v-button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- modal for showing calculation --> <!-- Modal -->
                                        <div class="modal fade" :id="`exampleModal${index}`" tabindex="-1" role="dialog"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Romms Calculation</h5>

                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="booking_det_cardbody">

                                                            <!-- room rate -->
                                                            <div class="main_calc_box">
                                                                <h6 class="ml-2 ">Room Rate</h6>
                                                                <div class="px-4 calc_box">
                                                                    <div v-if="item?.room_rate"
                                                                        class="d-flex justify-content-between">
                                                                        <p class="mb-0 mt-1">{{ item?.room_rate
            |
            twoPoints
                                                                            }} *
                                                                            {{ item.noOfRooms }} * {{ totalDays
                                                                            }}</p>
                                                                        <span>=</span>
                                                                        <p class=" mb-0"> {{
            (item.room_rate *
                item.noOfRooms * totalDays) |
            twoPoints }}
                                                                        </p>
                                                                    </div>
                                                                    <!-- 2 -->
                                                                    <div v-if="item?.room_rate"
                                                                        class="d-flex justify-content-between">
                                                                        <p class="gst_box">GST</p><span>=</span>
                                                                        <p v-if="item.room_rate" class=" mb-0">
                                                                            {{
            ((item.room_rate * totalDays *
                item.noOfRooms) *
                item.total_tax / 100) | twoPoints
        }} </p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="main_calc_box">
                                                                <h6 class="ml-2 ">Meal Rate</h6>
                                                                <div class="px-4 calc_box">
                                                                    <div v-if="item.meal_plan_price"
                                                                        class="d-flex justify-content-between">
                                                                        <p class="mb-0">
                                                                            {{ item.extra_meal_plan }} * {{
            item.noOfRooms }} *
                                                                            {{
            totalDays
        }} * {{ item.meal_plan_price |
            twoPoints }}
                                                                        </p><span>=</span>
                                                                        <p v-if="item.meal_plan_price" class=" mb-0">
                                                                            {{ (item.meal_rate_extra_price *
            totalDays *
            item.noOfRooms) | twoPoints }}</p>
                                                                    </div>

                                                                    <div v-else class="d-flex justify-content-between">
                                                                        <p v-if="item.meal_plan_id" class=" mb-0">
                                                                            = {{ item.meal_plan_price }} * {{
            item.noOfRooms }}
                                                                            * {{
            totalDays }}</p>
                                                                        <p v-if="item.meal_plan_id" class=" mb-0">
                                                                            =={{ (item.meal_plan_price *
            totalDays *
            item.noOfRooms)
            | twoPoints }}</p>
                                                                    </div>

                                                                    <div v-if="item.meal_plan_id"
                                                                        class="d-flex justify-content-between">
                                                                        <p class="mb-0 gst_box">
                                                                            GST
                                                                        </p><span>=</span>
                                                                        <p v-if="item.meal_plan_id" class=" mb-0">
                                                                            {{ (((item.meal_rate_extra_price *
            totalDays
            *
            item.noOfRooms)) *
            item.meal_sum_tax_rate / 100) |
            twoPoints }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <!-- <div v-if="item.meal_plan_id">
    <p class="mb-0">{{ ((item.meal_rate_extra_price *
        totalDays * item.noOfRooms) +
        (((item.meal_rate_extra_price *
            totalDays * item.noOfRooms)) *
            item.meal_sum_tax_rate /
            100)) | twoPoints
    }}</p>
</div> -->
                                                            </div>

                                                            <div class="main_calc_box">
                                                                <h6 class="ml-2 ">Extra Bed Rate</h6>
                                                                <div class="px-4 calc_box">
                                                                    <div v-if="item.extra_bed"
                                                                        class="d-flex justify-content-between">
                                                                        <p class="mb-0">{{ item.extra_bed }} *
                                                                            {{
            item.noOfRooms
        }}
                                                                            * {{ totalDays }} * {{
            item.extra_bed_rate | twoPoints }}
                                                                        </p>
                                                                        <span>=</span>
                                                                        <p class=" mb-0"> {{
            (item.extra_bed_rate_price *
                item.noOfRooms
                *
                totalDays)
            | twoPoints }}</p>
                                                                    </div>

                                                                    <div v-if="item.extra_bed"
                                                                        class="d-flex justify-content-between">
                                                                        <p class="mb-0 gst_box">
                                                                            GST</p><span>=</span>
                                                                        <p v-if="item.extra_bed" class=" mb-0">
                                                                            {{
            ((item.extra_bed_rate_price *
                totalDays *
                item.noOfRooms) *
                item.total_tax / 100) | twoPoints }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <!-- <div v-if="item.extra_bed">
    <p class="mb-0">{{ ((item.extra_bed_rate_price *
        totalDays * item.noOfRooms) +
        ((item.extra_bed_rate_price *
            totalDays * item.noOfRooms) * item.total_tax / 100))
        |
        twoPoints }}
    </p>
</div> -->
                                                            </div>

                                                            <div class="main_calc_box">
                                                                <h6 class="ml-2 ">Extra Person Rate</h6>
                                                                <div class="px-4 calc_box">
                                                                    <div v-if="item.extra_person"
                                                                        class="d-flex justify-content-between">
                                                                        <p class="mb-0">{{ item.per_person |
            twoPoints
                                                                            }} * {{
            item.noOfRooms }} * {{ totalDays }}
                                                                            * {{
            item.extra_person }}</p>
                                                                        <span>=</span>
                                                                        <p class=" mb-0">{{
            (item.extra_person_rate_price *
                item.noOfRooms *
                totalDays) | twoPoints }}</p>
                                                                    </div>

                                                                    <div v-if="item.extra_person"
                                                                        class="d-flex justify-content-between">
                                                                        <p class="mb-0 gst_box">
                                                                            GST</p><span>=</span>
                                                                        <p v-if="item.extra_person" class=" mb-0">
                                                                            {{ (((item.extra_person_rate_price *
            totalDays *
            item.noOfRooms)) *
            (item.total_tax) / 100) | twoPoints
                                                                            }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="px-2 mt-3">
                                                                <div class="d-flex justify-content-between">
                                                                    <h6 class="er">Sub Total</h6><span>=</span>

                                                                    <p class="er">{{ (((item.room_rate ||
            0)
            * item.noOfRooms * totalDays) +
            (((item.room_rate ||
                0)
                * item.noOfRooms * totalDays) *
                item.total_tax / 100) +
            (item.meal_rate_extra_price
                *
                totalDays * item.noOfRooms) +
            (((item.meal_rate_extra_price *
                totalDays *
                item.noOfRooms)
            ) * item.meal_sum_tax_rate / 100) +
            (item.facilities_rate_price * totalDays)
            +
            (((item.facilities_rate_price *
                totalDays) *
                item.facility_gst_rate_price) / 100) +
            (item.extra_bed_rate_price * totalDays *
                item.noOfRooms)
            +
            ((item.extra_bed_rate_price * totalDays
                *
                item.noOfRooms) *
                item.total_tax / 100) +
            (item.extra_person_rate_price *
                totalDays * item.noOfRooms) +
            (((item.extra_person_rate_price *
                totalDays * item.noOfRooms)) *
                (item.total_tax)
                /
                100))
            | withCurrency }} </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Close</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="btn_grp d-flex mt-2 justify-content-between">
                                        <button v-if="roomcategoryArray.length == (index + 1)" @click="addRoomCategory"
                                            type="button" :disabled="isButtonDisabled" class="btn btn-secondary">

                                            <i class="fas fa-times mr-2" style="transform: rotate(45deg);"></i>
                                            Add Rooms
                                        </button>
                                        <!-- <div class="form-group ml-3" v-if="index != 0"> -->
                                        <button @click="removeItem(index)" v-if="index != 0" type="button"
                                            class="btn text-danger float-right">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <!-- </div> -->









                                    </div>




                                </div>
                            </div>


                            <!-- {{ updatedRoomCategoryArray }}       CODE FOR CHECKIN PAGE  -->
                            <div v-if="slugdata" v-for="(items, index) in updatedRoomCategoryArray" :key="index + '1'"
                                class="form-group col-md-12 room-booking-form px-4 py-3 mt-3">

                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="room_hotel_cat_lsit" class="d-block">{{
            $t('sidebar.room_hotel_cat_lsit') }}</label>
                                        <input id="roomCategory" :value="items.room?.roomcategory?.room_category_name"
                                            type="text" class="form-control" name="bookingtypetitle" placeholder="Room
                                                 Category" readonly />
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="room_hotel_cat_lsit" class="d-block">Selected Room</label>
                                        <input id="selectedroom" :value="items.room?.room_name" type="text"
                                            class="form-control" name="bookingtypetitle" placeholder="Room Category"
                                            readonly />
                                    </div>
                                    <div class="form-group col-md-3"
                                        v-if="items?.roomNumber && items?.roomNumber.length">
                                        <label for="room_number" class="d-block">Assign New Room
                                            <span class="required">*</span></label>
                                        <multiselect v-model="selectedRooms[index]" :options="items.roomNumber"
                                            :show-labels="false" placeholder="Search a Room number" class="form-control"
                                            label="room_name" track-by="room_name"
                                            @select="checkRoomAssign(items.roomNumber, index)"
                                            :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }">
                                        </multiselect>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group col-md-12 row">
                                <div class="col-md-4" v-if="!slugdata">
                                    <div style="background-color: #f8f9fa; border-radius: 6px;"
                                        class="p-3 h-100 room-form_card">
                                        <div>
                                            <label for="totalRoom" class="font-weight-bold">{{
            $t('common.total_room_amount') }}</label>
                                            <p class="mb-0 ml-3">{{ final_room_rate_price | withCurrency }}</p>
                                        </div>
                                        <div class="mt-4">
                                            <label for="roomRate" class="font-weight-bold">{{
            $t('common.discount_room_rate') }}</label>
                                            <multiselect v-model="discount_room_rate" :options="discountRoomRateData"
                                                :show-labels="false" tag-placeholder=""
                                                placeholder="Search a Discount room rate" class="form-control"
                                                label="title" track-by="title"
                                                :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }">
                                            </multiselect>
                                        </div>
                                        <div class="mt-4 d-flex justify-content-between">
                                            <div class="col-md-6 ml-0">
                                                <label for="discount" class="font-weight-bold">{{
            $t('common.discount')
        }}</label>
                                                <input id="discount" v-model="discount" type="text" class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                    name="bookingtypetitle"
                                                    :placeholder="$t('common.discount_placeholder')" />
                                            </div>
                                            <div class="col-md-6">
                                                <label for="discount" class="font-weight-bold">{{
            $t('common.discount_amount') }}</label>

                                                <p class="mb-0 ml-2 mt-2">{{ discount_amount | withCurrency }}</p>
                                            </div>
                                        </div>

                                        <div class="border-top pt-2 mt-4">
                                            <label for="booked_by" class="font-weight-bold">{{
            $t('common.commission')
        }} {{ $t('common.booking_source') }}</label>
                                            <multiselect v-model="commissionTo" :options="bookedByList"
                                                :show-labels="false" tag-placeholder="Add this as new tag"
                                                :custom-label="customBookedByLabel" placeholder="Search a booking by"
                                                class="form-control" label="name" track-by="name"
                                                :class="{ 'is-invalid': form.errors.has('booked_by') }">
                                            </multiselect>
                                        </div>
                                        <div class="mt-4 d-flex justify-content-between">
                                            <div class="col-md-6">
                                                <label for="commission" class="font-weight-bold">{{
            $t('common.commission')
        }}</label>
                                                <input id="commission" v-model="commission" type="text"
                                                    class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                    name="bookingtypetitle"
                                                    :placeholder="$t('common.commission_placeholder')" />
                                            </div>
                                            <div class="col-md-6">
                                                <label for="commission" class="font-weight-bold">{{
            $t('common.commission_amount') }}</label>
                                                <p class="mb-0 ml-2 mt-2">{{ commission_amount | withCurrency }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label for="discount" class="font-weight-bold">{{
            $t('common.net_room_amount') }}</label>
                                            <p class="mb-0 ml-2 mt-2">{{ final_room_rate_price - discount_amount -
                                                commission_amount | withCurrency }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4" v-else>
                                    <div style="background-color: #f8f9fa; border-radius: 6px;"
                                        class="p-3 h-100 room-form_card">
                                        <div>
                                            <label for="totalRoom" class="font-weight-bold">{{
                                                $t('common.total_room_amount') }}</label>
                                            <p class="mb-0">{{ final_room_rate_price | withCurrency }}</p>
                                        </div>
                                        <div class="mt-4">
                                            <label for="roomRate" class="font-weight-bold">{{
                                                $t('common.discount_room_rate') }}</label>
                                            <p v-if="this.discount_room_rate.id == 1">{{ discount }}</p>
                                            <p v-else>{{ `${discount}%` }}</p>
                                        </div>
                                        <div class="mt-4 d-flex">
                                            <div>
                                                <label for="discount" class="font-weight-bold">{{
                                                    $t('common.discount_amount') }}</label>
                                                <p class="mb-0 mt-2">{{ discount_amount | withCurrency }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 d-flex">
                                            <div>
                                                <label for="commission" class="font-weight-bold">{{
                                                    $t('common.commission_amount') }}</label>
                                                <p class="mb-0 mt-2">{{ commission_amount | withCurrency }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label for="discount" class="font-weight-bold">{{
                                                $t('common.net_room_amount') }}</label>
                                            <p class="mb-0 mt-2">{{ final_room_rate_price - discount_amount -
                                                commission_amount | withCurrency }}</p>
                                        </div>
                                        <div class="mt-4" v-if="advance_credit_amount > 0">
                                            <label for="discount" class="font-weight-bold">{{
                                                $t('common.credit_amount') }}</label>
                                            <p class="mb-0 ml-2 mt-2">{{ advance_credit_amount | withCurrency }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div style="background-color: #f8f9fa; border-radius: 6px;"
                                        class="p-3 h-100 room-form_card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="font-weight-bold mb-0">
                                                {{ $t('common.room_amount') }}
                                            </p>
                                            <p class="mb-0">{{ final_room_rate_price - discount_amount -
                                                commission_amount | withCurrency }}</p>
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
                                                Add. Services Amount
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
                                                Commission Price
                                            </p>
                                            <p class="mb-0">{{ commission_amount | withCurrency }}</p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <p class="font-weight-bold mb-0">
                                                {{ $t('common.total_amount_after_discount') }}
                                            </p>
                                            <p v-if="final_room_rate_price + final_meal_rate_price + final_facility_rate_price"
                                                class="mb-0">
                                                {{ final_room_rate_price + final_meal_rate_price +
                                                final_facility_rate_price + final_ex_bed_person_rate_price -
                                                discount_amount | withCurrency }}</p>
                                            <p v-else class="mb-0">0</p>
                                        </div>
                                        <div class="border-top mt-4 pt-2" v-if="!slugdata">
                                            <input id="include_gst" type="checkbox" v-model="tax_included">
                                            <label for="include_gst">Inclusive GST</label>
                                        </div>
                                        <div class="mt-2">
                                            <p class="font-weight-bold mb-0">
                                                {{ $t('common.gst') }} </p>
                                            <p class="mb-0 text-muted font-weight-bold mt-3 mb-2"
                                                style="font-size: 14px;">
                                                Total Gst</p>
                                            <div v-if="final_gst_rate"
                                                v-for="(item, index) in Object.keys(final_gst_rate)" :key="index + '12'"
                                                class="ml-3 mt-1">
                                                <div v-if="final_gst_rate[item] > 0"
                                                    class="d-flex justify-content-between align-items-center">
                                                    <p class="font-weight-bold text-muted mb-0">{{ item }}</p>
                                                    <p class="mb-0">{{ final_gst_rate[item] | withCurrency }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <p class="font-weight-bold mb-0">
                                                {{ $t('common.net_payable_amount') }} </p>
                                            <p class="mb-0">{{ netPayableRound(total_room_amount - discount_amount)
                                                |
                                                withCurrency }}</p>
                                        </div>
                                        <div></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div style="background-color: #f8f9fa; border-radius: 6px;"
                                        class="p-3 h-100 room-form_card">
                                        <div>
                                            <label for="roomRate" class="font-weight-bold">{{
                                                $t('common.choose_payment_mode') }}</label>

                                            <div class="d-flex w-100">

                                                <multiselect @select="setDropDownData(payment_mode_id, paymentModeData)"
                                                    v-model="payment_mode_id" :options="paymentModeData"
                                                    :show-labels="false" tag-placeholder="Add this as new tag"
                                                    placeholder="Search an option" class="form-control"
                                                    label="ledger_name" track-by="ledger_name"
                                                    :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"
                                                    style="width:80%;"></multiselect>

                                                <div class="input-group-text create-btn"
                                                    @click="addExtraPaymentMethod = true">
                                                    <i class="fas fa-solid fa-plus-circle"></i>
                                                </div>
                                            </div>
                                            <!-- <div v-if="showError || !payment_mode_id" class="invalid-feedback">
                                                {{ showError ? 'Please select an option.' : 'This field is required.' }}
                                            </div> -->

                                        </div>
                                        <div class="mt-3">
                                            <label for="advance" class="font-weight-bold">{{
                                                $t('common.advance_amount') }}</label>
                                            <input id="advance" :value="advance_amount" @input="advanceAmountLimit"
                                                type="number" class="form-control" name="bookingtypetitle"
                                                :placeholder="$t('common.advance_amount_placeholder')" />
                                        </div>
                                        <div class="mt-3"
                                            v-if="(payment_mode_id && payment_mode_id.ledger_name !== 'Cash')">
                                            <label for="bank_charges" class="font-weight-bold">{{
                                                $t('common.bank_charges') }}</label>
                                            <input id="bank_charges" v-model="bank_charges" @input="bankChargesLimit"
                                                type="text" class="form-control" name="bookingtypetitle"
                                                :placeholder="$t('common.pending_amount_placeholder')" />
                                        </div>
                                        <div v-if="addExtraPaymentMethod">
                                            <div class="mt-3">
                                                <label for="roomRate1" class="font-weight-bold">{{
                                                    $t('common.choose_payment_mode') }}</label>
                                                <div class="d-flex w-100">
                                                    <multiselect v-model="payment_mode_id1" :options="paymentModeData1"
                                                        :show-labels="false" tag-placeholder="Add this as new tag"
                                                        placeholder="Search an option" class="form-control"
                                                        label="ledger_name" track-by="ledger_name"
                                                        :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"
                                                        style="width:80%;">
                                                    </multiselect>
                                                    <div class="input-group-text create-btn"
                                                        @click="addExtraPaymentMethod = false; payment_mode_id1 = ''; advance_amount1 = 0">
                                                        <i class="fas fa-solid fa-minus-circle"></i>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="mt-3">
                                                <label for="advance1" class="font-weight-bold">{{
                                                    $t('common.advance_amount') }}</label>
                                                <input id="advance1" :value="advance_amount1"
                                                    @input="advanceAmountLimit1" type="text" class="form-control"
                                                    name="bookingtypetitle"
                                                    :placeholder="$t('common.advance_amount_placeholder')" />
                                            </div>
                                            <div class="mt-3" v-if="payment_mode_id1.ledger_name !== 'Cash'">
                                                <label for="bank_charges1" class="font-weight-bold">{{
                                                    $t('common.bank_charges') }}</label>
                                                <input id="bank_charges1" v-model="bank_charges1"
                                                    @input="bankChargesLimit1" type="text" class="form-control"
                                                    name="bookingtypetitle"
                                                    :placeholder="$t('common.pending_amount_placeholder')" />
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label for="pending_amount" class="font-weight-bold">{{
                                                $t('common.pending_amount') }}</label>
                                            <input id="pending_amount" disabled :value="netPayableRound(pending_amount)"
                                                type="text" class="form-control" name="bookingtypetitle"
                                                :placeholder="$t('common.pending_amount_placeholder')" />
                                        </div>

                                        <div class="mt-3">
                                            <label for="advanceRemark" class="font-weight-bold">{{
                                                $t('common.advance_remarks') }}</label>
                                            <textarea id="advanceRemark" rows="2" v-model="form.advance_remarks"
                                                type="text" class="form-control" name="advanceRemark"
                                                :placeholder="$t('common.advance_remarks_placeholder')" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="form-group col-md-12 row">
                                    <div class="col-md-4">
                                        <div style="background-color: #f8f9fa; border-radius: 6px;" class="p-3 h-100">
                                      
                                            <div class="mt-4">
                                                <label for="booked_by" class="font-weight-bold">{{ $t('common.booking_source') }}</label>
                                            <multiselect v-model="bookedBy" :options="bookedByList" :show-labels="false" tag-placeholder="Add this as new tag" :custom-label="customBookedByLabel"
                                                 placeholder="Search a booking by" class="form-control" label="name" track-by="name" :class="{ 'is-invalid': form.errors.has('booked_by') }"></multiselect>
                                            </div>
                                            <div class="mt-4 d-flex justify-content-between">
                                                <div class="col-md-6">
                                                    <label for="commission" class="font-weight-bold">{{
                                                        $t('common.commission')
                                                        }}</label>
                                                    <input id="commission" v-model="commission" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                                           name="bookingtypetitle" :placeholder="$t('common.commission_placeholder')"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="commission" class="font-weight-bold">{{
                                                        $t('common.commission_amount') }}</label>
                                                    <p class="mb-0 ml-2 mt-2">{{ commission_amount | withCurrency }}</p>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div> -->
                            <div class="w-100">
                                <div class="mt-3 col-md-12">
                                    <label for="note" class="font-weight-bold">{{ $t('common.note') }}</label>
                                    <textarea v-model="form.comments" name="w3review" rows="2"
                                        class="w-100 note-textarea p-3" :placeholder="$t('common.note_placeholder')"
                                        style="border-radius: 4px; border-color: #ced4da;"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- /.card-body -->
                        <div class="card-footer">
                            <v-button :loading="form.busy" class="btn btn-primary">
                                <i class="fas fa-save" /> {{ slugdata ? 'Check In' : 'Book Now' }}
                            </v-button>
                            <button :loading="form.busy" v-if="!slugdata"
                                @click.stop.prevent="saveBooking($event, true)" class="btn btn-primary text-white"
                                :disabled="isCheckinDisabled">
                                Check In <i class="fas fa-arrow-right" />
                            </button>
                            <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                                <i class="fas fa-power-off" /> {{ $t('common.reset') }}
                            </button>
                        </div>
                    </form>

                    <div class="modal fade" id="oldClientEdit" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                                                <div v-if="isLoadingClient"
                                                    class="d-flex justify-content-center position-relative">
                                                    <div class="spinner-border z-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div v-else :class="items?.length > 0 ? 'bg-white p-3' : ''">
                                                    <div class="form-group">
                                                        <label for="customer" class="d-block">{{ 'Select customer'
                                                            }}</label>
                                                        <multiselect v-model="selectedOldCustomer"
                                                            @select="clientChange" @remove="clientChange"
                                                            :options="(allClientList) ? allClientList : []"
                                                            :show-labels="false" tag-placeholder="Add this as new tag"
                                                            placeholder="Search a customer" class="form-control"
                                                            :custom-label="({ name, phone }) => `${name} - ${phone}`"
                                                            label="name" track-by="id"></multiselect>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <v-button :loading="form.busy" class="btn btn-primary">
                                                    <i class="fas fa-save" /> {{ $t('common.save') }}
                                                </v-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="newClient" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-width modal-lg modal-dialog-centered" role="document"
                            style="max-width: 800px">
                            <div class="modal-content modal-lg" style="max-width: 800px">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Select a Client</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        <form role="form" @submit.prevent="saveClient"
                                            @keydown="clientForm.onKeydown($event)">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="customer" class="d-block">{{ 'Search number'
                                                            }}</label>
                                                        <multiselect @select="clientChange" @remove="clientChange"
                                                            v-model="selectedOldCustomer" :options="allClientList || []"
                                                            :taggable="true" @tag="addNewCustomer" :show-labels="false"
                                                            tag-placeholder="Add this as new number"
                                                            placeholder="Search a customer" class="form-control"
                                                            :custom-label="({ name, phone }) => `${name ? name + '-' : ''} ${phone}`"
                                                            label="name" track-by="id"></multiselect>
                                                        <p v-if="customerError" class="text-red">{{ customerError }}</p>
                                                        <has-error v-else :form="clientForm" field="phoneNumber" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="name">{{ $t('common.customer_name') }}
                                                            <span class="required">*</span></label>
                                                        <input id="name" v-model="clientForm.name" type="text"
                                                            class="form-control"
                                                            :class="{ 'is-invalid': clientForm.errors.has('name') }"
                                                            name="name" :placeholder="$t('common.name_placeholder')" />
                                                        <has-error :form="clientForm" field="name" />
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="identity">{{ $t('common.identity') }}
                                                        </label>
                                                        <input id="identity" v-model="clientForm.identity" type="text"
                                                            class="form-control"
                                                            :class="{ 'is-invalid': clientForm.errors.has('identity') }"
                                                            name="identity"
                                                            :placeholder="$t('common.identity_placeholder')" />
                                                        <has-error :form="clientForm" field="identity" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="email">{{ $t('common.email') }}</label>
                                                        <input id="email" v-model="clientForm.email" type="email"
                                                            class="form-control"
                                                            :class="{ 'is-invalid': clientForm.errors.has('email') }"
                                                            name="email"
                                                            :placeholder="$t('common.email_placeholder')" />
                                                        <has-error :form="clientForm" field="email" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label for="address">{{ $t('common.address') }}</label>
                                                        <textarea id="address" v-model="clientForm.address"
                                                            class="form-control"
                                                            :class="{ 'is-invalid': clientForm.errors.has('address') }"
                                                            :placeholder="$t('common.address_placeholder')" />
                                                        <has-error :form="clientForm" field="address" />
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label for="email">{{ $t('common.nationality') }}</label>
                                                        <input id="nationality" v-model="clientForm.nationality"
                                                            type="nationality" class="form-control"
                                                            :class="{ 'is-invalid': clientForm.errors.has('nationality') }"
                                                            name="nationality"
                                                            :placeholder="$t('common.nationality_placeholder')" />
                                                        <has-error :form="clientForm" field="nationality" />
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label for="gst">{{ $t('common.gst_number') }}</label>
                                                        <input id="gst" v-model="clientForm.gst" type="text"
                                                            class="form-control"
                                                            :class="{ 'is-invalid': clientForm.errors.has('nationality') }"
                                                            name="nationality"
                                                            :placeholder="$t('common.nationality_placeholder')" />
                                                        <has-error :form="clientForm" field="nationality" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label class="image_label" for="file-upload">Upload Id</label>
                                                        <input type="file" id="file-upload" multiple
                                                            @change="handleFileUpload">

                                                        <label class="image_label" for="file-camera"
                                                            @click="toggleCamera"><i class="fa fa-camera"></i></label>
                                                        <div v-if="isCameraOpen" v-show="!isLoading" class="camera-box"
                                                            :class="{ 'flash': isShotPhoto }">

                                                            <div class="camera-shutter"
                                                                :class="{ 'flash': isShotPhoto }">
                                                            </div>

                                                            <video v-show="!isPhotoTaken" ref="camera" :width="450"
                                                                :height="337.5" autoplay></video>
                                                            <canvas v-show="isPhotoTaken" id="photoTaken" ref="canvas"
                                                                :width="450" :height="337.5"></canvas>
                                                        </div>

                                                        <div v-if="isCameraOpen && !isLoading" class="camera-shoot">
                                                            <button type="button" class="btn btn-dark button"
                                                                @click="takePhoto">
                                                                Take Photo
                                                            </button>
                                                        </div>
                                                        <div v-if="images.length" class="image-container">
                                                            <div v-for="(image, index) in images" :key="index"
                                                                class="image-item">
                                                                <img :src="image.previewUrl" alt="Preview"
                                                                    class="profile-pic">
                                                                <button class="remove-button"
                                                                    @click.stop="removeImage(index)">X</button>
                                                            </div>
                                                        </div>
                                                        <div class="image-container"
                                                            v-if="photoData && photoData.length > 0">
                                                            <div v-for="(image, index) in photoData" :key="index"
                                                                class="image-item">
                                                                <img :src="image" alt="Preview" class="profile-pic">
                                                                <button class="remove-button"
                                                                    @click.stop="removeImagess(index)">X</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <v-button :loading="clientForm.busy"
                                                    class="btn btn-primary float-right">
                                                    <i class="fas fa-save" /> {{ $t('common.save') }}
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
import { mapGetters } from 'vuex';
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
    metaInfo() {
        return { title: this.$route?.params?.slug ? this.$t('booking.edit.page_title') : this.$t('booking.create.page_title') };
    },
    components: {
        DatePicker,
        Multiselect,
        DateRangePicker
    },
    computed: {
        ...mapGetters('operations',
            ['items', 'facilityItems', 'hotelItems', 'bedTypeItems', 'floorItems', 'mealItems', 'selectedHotel']),

        hotelCategoryItems() {
            console.log('hotel', this.hotel_id);
            // let roomCategory = this.hotel_id?.roomCategories ? _.uniqBy(this.hotel_id?.roomCategories, 'id') : [];
            // console.log('cat',roomCategory);
            let hotelRoomCat = [];
            if (this.hotel_id) {
                hotelRoomCat = this.hotel_id?.hotelRoomCategories.filter((data) => data.rooms.length > 0).map((cat) => ({ ...cat.room_category }));
            }
            console.log('roomCat', hotelRoomCat);
            return hotelRoomCat;

            // return roomCategory.filter((data) => data.rooms.length > 0);
        },
        isCheckinDisabled() {
            let checkIn = _.cloneDeep(this.check_in_date)
            checkIn = moment(checkIn).format('yyyy-MM-DD')
            let currentDate = moment().format('yyyy-MM-DD')
            let customerAvailable = false;
            this.roomcategoryArray.forEach((ele) => {
                ele.clientItemsData.forEach((element, index) => {
                    customerAvailable = true
                });
            });

            return !(checkIn && (checkIn == currentDate)) || !customerAvailable;
        },
        pageTitle() {
            return this.$route?.params?.slug ? this.$t('booking.edit.page_title') : this.$t('booking.create.page_title');
        },
        breadcrumbsCurrent() {
            return this.$route?.params?.slug ? this.$t('booking.create.breadcrumbs_checkIn') : this.$t('booking.create.breadcrumbs_current');
        },
        breadcrumbs() {
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
                    name: this.$route?.params?.slug ? this.$t('booking.edit.breadcrumbs_active') : this.$t('booking.create.breadcrumbs_active'),
                    url: '',
                },
            ]
        },
        final_room_rate_price() {
            if (this.$route?.params?.slug) {
                return _.sumBy(this.bookingData.booking_details, 'room_rate') || {}
            }
            let finalRoomPrice = this.roomcategoryArray.map((el) => el.room_rate * el.noOfRooms * this.totalDays);
            return _.sum(finalRoomPrice);
        },
        final_room_rate_with_gst() {
            let tax = this.roomcategoryArray.map((el) => el.totalRoomGst);
            tax = _.sum(tax, 'tax');
            return this.final_room_rate_price + tax;
        },
        room_price_with_gst() {
            if (this.$route?.params?.slug) {
                let roomPriceWithGst = this.roomcategoryArray.map(
                    (el) => (el.room_rate + el.room_tax));
                return _.sum(roomPriceWithGst);
            }
            let roomPriceWithGst = this.roomcategoryArray.map(
                (el) => el.room_rate * this.totalDays + (el.room_rate * this.totalDays) * el.total_tax / 100);
            return _.sum(roomPriceWithGst);
        },
        final_meal_rate_price() {
            if (this.$route?.params?.slug) {
                let finalMealRatePrice = this.mealPlanDetailsList.map((el, i) => {
                    let price = el.price * this.extraMealPersons[i] * this.totalDays;
                    console.log(price);
                    let tax = this.roomcategoryArray[i]?.meal_plan_tax || 0
                    return this.tax_included ? price - tax : price;
                });
                return isNaN(_.sum(finalMealRatePrice)) ? 0 : _.sum(finalMealRatePrice);;
            }
            let finalMealRatePrice = this.roomcategoryArray.map((el) => el.meal_rate_extra_price * el.noOfRooms);
            return _.sum(finalMealRatePrice);
        },
        final_facility_rate_price() {
            let finalFacilityRatePrice = this.roomcategoryArray.map((el) => {
                //  console.log(el);
                let complementrayTotalPrice = 0;
                // console.log(el.complementrays.length);
                if (typeof el.complementrays !== 'undefined') {
                    if (el.complementrays.length) {
                        el.complementrays.forEach((complementray) => {
                            complementrayTotalPrice += complementray.modified_rate * complementray.quantity;
                        });

                        return complementrayTotalPrice;
                    }
                }

                // let price = el.facilities_rate_price * this.totalDays
                // return this.tax_included && this.slugdata ? price - (el.facility_tax || 0) : price;
            });
            return isNaN(_.sum(finalFacilityRatePrice)) ? 0 : _.sum(finalFacilityRatePrice);
        },
        final_ex_bed_person_rate_price() {
            if (this.$route?.params?.slug) {
                let bedRate = _.sumBy(this.bookingData.booking_details, 'extra_bed_price') || 0;
                let personRate = _.sumBy(this.bookingData.booking_details, 'extra_person_price') || 0;

                return bedRate + personRate;
                // console.log('room',this.roomcategoryArray);
                // let finalBedPersonPrice = this.roomcategoryArray.map(
                //     (el) => {
                //         let bedRate = el.extra_bed * (el?.room?.extra_bed_rate || 0);
                //         let personRate = el.extra_person * (el?.room?.per_person || 0);
                //        return (((this.tax_included ? bedRate - (el.extra_bed_tax || 0) : bedRate)
                //          + (this.tax_included ? personRate - (el.extra_person_tax || 0) : personRate)) * this.totalDays)
                //     });
                // return isNaN(_.sum(finalBedPersonPrice)) ? 0 : _.sum(finalBedPersonPrice);
            }
            let finalBedPersonPrice = this.roomcategoryArray.map(
                (el) => (el.extra_bed_rate_price + el.extra_person_rate_price) * this.totalDays * el.noOfRooms);
            return _.sum(finalBedPersonPrice);
        },

        final_gst_rate() {
            let gst = {};
            if (this.$route?.params?.slug) {
                return this.bookingData.final_gst_rates || {}
            }
            _.map(this.allVatRates, 'name').forEach((val) => gst[val] = 0);
            this.roomcategoryArray.forEach((room, index) => {

                room.gstRate.forEach((rate) => {


                    if (room.room_rate) gst[rate.tax_name.name] += ((room.room_rate * room.noOfRooms * this.totalDays) - (room.discount * room.noOfRooms || 0) - (room.commission * room.noOfRooms || 0)) *
                        rate.tax_name.rate / 100;
                    if (room.extra_bed) gst[rate.tax_name.name] += (room.extra_bed_rate_price * room.noOfRooms * this.totalDays) *
                        rate.tax_name.rate / 100;
                    if (room.extra_person) gst[rate.tax_name.name] += (room.extra_person_rate_price * room.noOfRooms *
                        this.totalDays) * rate.tax_name.rate / 100;
                });

                room.meal_tax_rate.forEach((rate) => {
                    if (room.meal_plan_id) gst[rate.tax_name.name] += (room.meal_rate_extra_price *
                        this.totalDays * room.noOfRooms) * rate.tax_name.rate / 100;
                });

                if (room.room_facility_id?.length > 0) {
                    room.facilities_price.forEach((item) => {
                        item.tax_rate.forEach((rate) => {
                            gst[rate.taxname.name] += (room.facilities_rate_price * this.totalDays) *
                                rate.taxname.rate / 100;
                        });
                    });
                    this.roomcategoryArray[index]['totalFacilityGst'] = ((room.facilities_rate_price *
                        this.totalDays)) * room.facility_gst_rate_price / 100;
                }

                if (room.meal_rate_extra_price) this.roomcategoryArray[index]['totalMealGst'] = ((room.meal_rate_extra_price *
                    this.totalDays * room.noOfRooms)) * room.meal_sum_tax_rate / 100;
            });
            this.subTotal();
            this.pending_amount = ((this.total_room_amount - this.discount_amount) - this.advance_amount1 - this.advance_amount - this.bank_charges - this.bank_charges1);
            return gst;
        },
        updatedRoomCategoryArray() {
            return this.roomcategoryArray;
        }
    },
    data: () => ({
        requiredField: true,
        withCurrency: '',
        allClientList: [],
        initialLoading: false,
        addExtraPaymentMethod: false,
        selectedOldCustomer: {},
        customerError: '',
        isTextBoxShow: false,
        form: new Form({
            type: 1,
            customer: [
                {
                    customer_id: '',
                    customer_id_request: null,
                }],
            total_tax: '',
            booked_by: '',
            commission_to: '',
            total_room: 1,
            client_booking_status: '',
            advance_amount: '',
            advance_amount1: '',
            advance_remarks: '',
            bank_charges: '',
            bank_charges1: '',
            paid_amount: '',
            total_price: '',
            payment_method: '',
            payment_method1: '',
            remarks: '',
            discount_amount: null,
            commission_amount: null,
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
        total_room_amount: 0,
        allVatRates: [],
        bedCapacityError: false,
        personCapacityError: false,
        roomcategoryArray: [
            {
                id: 1,
                room_booking_id: '',
                room_number_id: '',
                roomNumber: [],
                noOfRooms: '',
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
                room_test_price: '',
                discount: 0,
                commission: 0,
            },
        ],
        counter: 1,
        bookedByList: [],
        bookedBy: '',
        commissionTo: '',
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
            // {
            //     id: 2,
            //     name: 'Hold',
            // },
            {
                id: 3,
                name: 'Confirmed',
            },
            // {
            //     id: 4,
            //     name: 'Available',
            // },
        ],
        discount_room_rate: 0,
        payment_method_form: new Form({
            ledger_type: '',
        }),
        paymentModeData: [],
        paymentModeData1: [],
        payment_mode_id: '',
        payment_mode_id1: '',
        showError: 'false',
        discount: 0,
        commission: 0,
        discount_amount: 0,
        commission_amount: 0,
        net_payable_amount: 0,
        advance_amount: 0,
        advance_amount1: 0,
        bank_charges: 0,
        bank_charges1: 0,
        advance_credit_amount: 0,
        pending_amount: 0,
        tax_included: false,
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
        totalDays: 1,
        totalDaysStatic: 0,
        clientForm: new Form({
            name: '',
            email: '',
            gst: '',
            phoneNumber: '',
            companyName: '',
            address: '',
            image: '',
            images: [],
            status: 1,
            identity: '',
            nationality: 'Indian',
            pastImages: [],
            hotel_id: { id: null },
            file: [],
        }),
        clientData: {},
        clientItemsData: [],
        isSuggestionBox: false,
        client_booking_status_id: '',
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
            advance_amount1: '',
            bank_charges: '',
            bank_charges1: '',
            advance_remarks: '',
            paid_amount: '',
            final_gst_rate: {
                'IGST 5%': 0,
                'SGST 5%': 0
            },
            total_price: '',
            payment_method: '',
            payment_method1: '',
            remarks: '',
            discount_amount: null,
            commission_amount: null,
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
        currency: 'Rs.',
        images: [],
        showModal: false,
        selectedImage: null,
        selectedRooms: [],
        isButtonDisabled: false,
        isCameraOpen: false,
        isPhotoTaken: false,
        isShotPhoto: false,
        isLoading: false,
        photoData: [],
    }),
    async created() {
        this.initialLoading = true;
        await this.getCurrency();
        await this.getBookingType();
        await this.getVatRates();
        await this.getHotelDataList();
        await this.getBookedBySupplier();
        await this.searchClientData();
        await this.getLedgerListData();
        this.slugdata = this.$route?.params?.slug;
        // await this.defaultHotelSelection();
        this.instantBooking = this.$route?.params?.instant;
        // console.log('slug',this.$route?.params?.slug);
        // console.log('category',this.$route?.params?.category);
        // console.log('room',this.$route?.params?.room);
        // Make room category selected if route parameters are there
        if (this.$route?.query?.category) {
            let item = this.roomcategoryArray[0];
            this.hotelCategoryItems?.forEach((value) => {
                if (value.id == this.$route?.query?.category) item.room_booking_id = value;
            })
            if (item?.room_booking_id) {
                this.roomCategorySelect(item);
            }

            if (this.$route?.query?.room) {

                setTimeout(() => {
                    this.roomcategoryArray[0].roomNumber?.forEach((value) => {
                        if (value.id == this.$route?.query?.room) this.roomcategoryArray[0].room_number_id = value;
                    })
                    if (this.roomcategoryArray[0].room_number_id) {
                        this.roomNumberSelect(this.roomcategoryArray[0], this.roomcategoryArray[0].room_number_id)
                    }
                }, 100)
            }
        }
        this.$route?.params?.slug && this.getBookingData();
        this.initialLoading = false;
    },
    watch: {
        selectedHotel: function () {
            this.getHotelDataList();
        },
        hotelItems() {
            this.defaultHotelSelection();
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
        discount(currentValue, oldValue) {
            if (this.discount_room_rate.title === "Percentage" && currentValue > 100) {
                this.discount = '';
            } else if (!this.slugdata) {
                if (this.discount_room_rate.id == 1) {
                    this.discount_amount = this.discount;
                } else {
                    this.discount_amount = ((this.final_room_rate_price * currentValue) / 100);
                }
                this.roomcategoryArray.forEach((el) => {
                    el.discount = this.discount_amount * ((el.room_rate * this.totalDays)) / this.final_room_rate_price ?? 0
                });
                this.pending_amount = ((this.total_room_amount - this.discount_amount) - this.advance_amount1 - this.advance_amount - this.advance_credit_amount);
                this.counter += 1;
            }
        },
        commission(currentValue, oldValue) {
            this.commission_amount = this.commission;
            this.roomcategoryArray.forEach((el) => {
                el.commission = this.commission_amount * ((el.room_rate * this.totalDays)) / this.final_room_rate_price ?? 0
            });
            this.subTotal();
        },
        discount_room_rate() {
            if (!this.slugdata) {
                this.discount = '';
                this.discount_amount = '';
            }
        },
        advance_amount(currentValue) {
            this.pending_amount = ((this.total_room_amount - this.discount_amount - this.advance_credit_amount - this.advance_amount1 - this.bank_charges - this.bank_charges1) - currentValue);
        },
        advance_amount1(currentValue) {
            this.pending_amount = ((this.total_room_amount - this.discount_amount - this.advance_credit_amount - this.advance_amount - this.bank_charges - this.bank_charges1) - currentValue);
        },
        bank_charges(currentValue) {
            this.pending_amount = ((this.total_room_amount - this.discount_amount - this.advance_credit_amount - this.advance_amount - this.advance_amount1 - this.bank_charges1) - currentValue);
        },
        bank_charges1(currentValue) {
            this.pending_amount = ((this.total_room_amount - this.discount_amount - this.advance_credit_amount - this.advance_amount - this.advance_amount1 - this.bank_charges) - currentValue);
        },
        hotel_id(currentValue) {
            this.getMealPlanData(currentValue?.id);
            this.getRoomFacilityData(currentValue?.id);
        },
        roomcategoryArray: {
            handler: async function () {
                // if(this.slugdata){
                //     this.roomcategoryArray = currentValue;
                //     console.log('enter',this.roomcategoryArray);
                // }
                await this.subTotal();
                this.pending_amount = ((this.total_room_amount - this.discount_amount) - this.advance_amount1 - this.advance_amount - this.bank_charges - this.bank_charges1 - this.advance_credit_amount);
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
    
        toggleVisibility(item,index) {
            // console.log(item);
            this.roomcategoryArray[index].visible = !this.roomcategoryArray[index].visible
            // var element = document.querySelector('.show_additional');

            // if (element.style.display === 'none' || element.style.display === '') {
            //     element.style.setProperty('display', 'flex', 'important');
            // } else {
            //     element.style.setProperty('display', 'none', 'important');
            // }
        },        /*Start Camera Capture*/
        toggleCamera() {
            if (this.isCameraOpen) {
                this.isCameraOpen = false;
                this.isPhotoTaken = false;
                this.isShotPhoto = false;
                this.stopCameraStream();
            } else {
                this.isCameraOpen = true;
                this.createCameraElement();
            }
        },

        createCameraElement() {
            this.isLoading = true;

            const constraints = (window.constraints = {
                audio: false,
                video: true
            });


            navigator.mediaDevices
                .getUserMedia(constraints)
                .then(stream => {
                    this.isLoading = false;
                    this.$refs.camera.srcObject = stream;
                })
                .catch(error => {
                    this.isLoading = false;
                    Swal.fire({
                        type: "error",
                        title: "<i class='fa fa-camera'></i>",
                        text: "There is some error while opening camera...",
                    });
                });
        },

        stopCameraStream() {
            let tracks = this.$refs.camera.srcObject.getTracks();

            tracks.forEach(track => {
                track.stop();
            });
        },

        takePhoto() {

            if (!this.isPhotoTaken) {
                this.isShotPhoto = true;
                setTimeout(() => {
                    this.isShotPhoto = false;

                }, 50);
            }

            this.isPhotoTaken = !this.isPhotoTaken;

            const context = this.$refs.canvas.getContext('2d');
            context.drawImage(this.$refs.camera, 0, 0, 450, 337.5);
            const dataURL = document.getElementById("photoTaken").toDataURL("image/jpeg")
            this.photoData.push(dataURL);

            //   document.getElementById("photoTaken").toDataURL('image/png');
            setTimeout(() => {
                if (dataURL) {
                    console.log(dataURL);
                    var blob = this.dataURLtoBlob(dataURL);
                    if (blob) {
                        this.clientForm.file.push(blob);
                        // this.clientForm.file = blob;
                        this.isCameraOpen = false;
                        this.isPhotoTaken = false;
                        this.isShotPhoto = false;
                    }
                }
            }, 1000);
        },
        dataURLtoBlob(dataURL) {
            const arr = dataURL.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);

            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }

            return new Blob([u8arr], { type: mime });
        },
        /*Stop Camera Capture*/

        checkRoomAssign(roomArray, index) {
            const hasDuplicates = this.selectedRooms.some((item, index) => {
                return this.selectedRooms.some((innerItem, innerIndex) => {
                    return index !== innerIndex && this.compareById(item, innerItem);
                });
            });

            if (hasDuplicates) {
                this.selectedRooms[index] = '';
                return toast.fire({ type: 'error', title: 'Room Already Allocated please select different Room' });
            }
        },

        compareById(obj1, obj2) {
            return obj1.id === obj2.id;
        },
        nameWithRoomCount({ room_category_name, count }) {
            return `${room_category_name} — [${count}]`
        },
        netPayableRound(amount) {
            return amount ? Math.round(amount) : 0;
        },
        defaultHotelSelection() {
            if (this.selectedHotel && this.selectedHotel !== 'all') {
                this.hotelItems?.forEach((hotel) => {
                    if (hotel.id == this.selectedHotel) {
                        this.hotel_id = hotel
                        this.changeHotel();
                    }
                })
            } else if (this.$route?.query?.hotel) {
                this.hotelItems?.forEach((hotel) => {
                    if (hotel.id == this.$route?.query?.hotel) {
                        this.hotel_id = hotel
                        this.changeHotel();
                    }
                })
            }
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
            if (this.tax_included) this.setTaxInclusivePrice();
            else this.setTaxExclusivePrice();
        },
        setTaxInclusivePrice() {
            this.tax_included && this.roomcategoryArray.forEach((el) => {
                el.room_rate = this.inclusiveTaxAmount(el.room_rate, el.total_tax);
                el.meal_plan_price = this.inclusiveTaxAmount(el.meal_plan_price, el.meal_sum_tax_rate);
                el.meal_rate_extra_price = this.inclusiveTaxAmount(el.meal_rate_extra_price, el.meal_sum_tax_rate)
                el.facilities_rate_price = this.inclusiveTaxAmount(el.facilities_rate_price || 0, el.facility_gst_rate_price || 0);
                el.extra_bed_rate_price = this.inclusiveTaxAmount(el.extra_bed_rate_price, el.total_tax);
                el.extra_person_rate_price = this.inclusiveTaxAmount(el.extra_person_rate_price, el.total_tax);
            })
        },
        setTaxExclusivePrice() {
            !this.tax_included && this.roomcategoryArray.forEach((el) => {
                el.room_rate = this.exclusiveTaxAmount(el.room_rate, el.total_tax);
                el.meal_plan_price = this.exclusiveTaxAmount(el.meal_plan_price, el.meal_sum_tax_rate);
                el.meal_rate_extra_price = this.exclusiveTaxAmount(el.meal_rate_extra_price, el.meal_sum_tax_rate)
                el.facilities_rate_price = this.exclusiveTaxAmount(el.facilities_rate_price || 0, el.facility_gst_rate_price || 0);
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
        removeImagess(index) {
            this.photoData.splice(index, 1);
        },
        async getCurrency() {
            await axios.get('/api/default-currency').then((response) => {
                this.currency = response?.data?.data?.symbol;
            });
        },
        customBookedByLabel({ name, phone }) {
            return `${name}  ( ${phone} )`
        },
        clientPlusClick(item = null) {
            if (item) {
                this.clientItemId = item.id;
            }
            this.selectedOldCustomer = null;
            this.images = [];
        },
        async subTotal() {
            if (this.$route?.params?.slug) {
                return
            }
            const test = [];
            this.roomcategoryArray.forEach((el) => {
                this.total_room_amount = 0;
                let rate = ((parseFloat(el.room_rate || 0)) + parseFloat(el.meal_rate_extra_price || 0) +
                    parseFloat(el.facilities_rate_price || 0) + parseFloat(el.extra_bed_rate_price || 0) +
                    parseFloat(el.extra_person_rate_price || 0)) * this.totalDays * el.noOfRooms;

                let taxRate = (((parseFloat(el.room_rate || 0) * el.noOfRooms * this.totalDays) - (el.discount * el.noOfRooms || 0) - (el.commission * el.noOfRooms || 0)) * el.total_tax / 100) +
                    ((el.meal_rate_extra_price * this.totalDays * el.noOfRooms) * el.meal_sum_tax_rate / 100) +
                    ((el.facilities_rate_price * this.totalDays) * el.facility_gst_rate_price / 100) +
                    ((el.extra_bed_rate_price * this.totalDays * el.noOfRooms) * el.total_tax / 100) +
                    ((el.extra_person_rate_price * this.totalDays * el.noOfRooms) * el.total_tax / 100);

                test.push(parseFloat(rate) + parseFloat(taxRate));
            });
            if (test) {
                test.forEach((el) => {
                    this.total_room_amount += el;
                });
            }
        },
        advanceAmountLimit(event) {
            if (this.$route?.params?.slug) {
                if (event.target.value > Math.round(this.bookingData.paid_amount - this.discount_amount - this.advance_amount1 - this.bank_charges - this.bank_charges1)) {
                    return this.advance_amount = Math.round(this.bookingData.paid_amount - this.discount_amount - this.advance_amount1 - this.bank_charges - this.bank_charges1)
                }
            } else {
                if (event.target.value > Math.round(this.total_room_amount - this.discount_amount - this.advance_amount1 - this.bank_charges - this.bank_charges1)) {
                    return this.advance_amount = Math.round(this.total_room_amount - this.discount_amount - this.advance_amount1 - this.bank_charges - this.bank_charges1)
                }
            }

            this.advance_amount = event.target.value;
        },
        advanceAmountLimit1(event) {
            if (this.$route?.params?.slug) {
                if (event.target.value > Math.round(this.bookingData.paid_amount - this.discount_amount - this.advance_amount - this.bank_charges - this.bank_charges1)) {
                    return this.advance_amount1 = Math.round(this.bookingData.paid_amount - this.discount_amount - this.advance_amount - this.bank_charges - this.bank_charges1)
                }
            } else {
                if (event.target.value > Math.round(this.total_room_amount - this.discount_amount - this.advance_amount - this.bank_charges - this.bank_charges1)) {
                    return this.advance_amount1 = Math.round(this.total_room_amount - this.discount_amount - this.advance_amount - this.bank_charges - this.bank_charges1)
                }
            }

            this.advance_amount1 = event.target.value;
        },
        bankChargesLimit(event) {
            if (this.$route?.params?.slug) {
                if (event.target.value > Math.round(this.bookingData.paid_amount - this.discount_amount - this.advance_amount1 - this.advance_amount - this.bank_charges1)) {
                    return this.bank_charges = Math.round(this.bookingData.paid_amount - this.discount_amount - this.advance_amount1 - this.advance_amount - this.bank_charges1)
                }
            } else {
                if (event.target.value > Math.round(this.total_room_amount - this.discount_amount - this.advance_amount1 - this.advance_amount - this.bank_charges1)) {
                    return this.bank_charges = Math.round(this.total_room_amount - this.discount_amount - this.advance_amount1 - this.advance_amount - this.bank_charges1)
                }
            }

            this.bank_charges = event.target.value;
        },
        bankChargesLimit1(event) {
            if (this.$route?.params?.slug) {
                if (event.target.value > Math.round(this.bookingData.paid_amount - this.discount_amount - this.advance_amount1 - this.advance_amount - this.bank_charges)) {
                    return this.bank_charges1 = Math.round(this.bookingData.paid_amount - this.discount_amount - this.advance_amount1 - this.advance_amount - this.bank_charges)
                }
            } else {
                if (event.target.value > Math.round(this.total_room_amount - this.discount_amount - this.advance_amount1 - this.advance_amount - this.bank_charges)) {
                    return this.bank_charges1 = Math.round(this.total_room_amount - this.discount_amount - this.advance_amount1 - this.advance_amount - this.bank_charges)
                }
            }

            this.bank_charges1 = event.target.value;
        },
        limitNoOfRoom(event, item, index) {

            let totalCategoryLimit = item.room_booking_id.count;
            let totalRoom = 0;
            if (this.roomcategoryArray.length > 1) {
                this.roomcategoryArray.forEach((el, roomIndex) => {
                    if (el.room_booking_id.id == item.room_booking_id.id) {
                        totalRoom += (roomIndex == index) ? parseInt(event.target.value) : parseInt(el.noOfRooms);
                    }
                });
            }

            if (totalRoom > totalCategoryLimit) {
                this.roomcategoryArray[index].noOfRooms = 0;
                this.isButtonDisabled = true;
                return toast.fire({ type: 'error', title: 'No Room Available In this Category Please Select Another Category, Total Available Room In this Category is ' + totalCategoryLimit + ' and you have selected total no of room is ' + totalRoom });

            } else {
                this.isButtonDisabled = false;
                this.roomcategoryArray[index].noOfRooms = event.target.value;
            }

            if (event.target.value > parseInt(item.noOfRoomsLimit)) {
                this.roomcategoryArray[index].noOfRooms = parseInt(item.noOfRoomsLimit);
            } else {
                this.roomcategoryArray[index].noOfRooms = parseInt(event.target.value);
            }
        },


        limitAdult(event, item, index) {
            if (event.target.value > parseInt(item.adultLimit)) {
                this.roomcategoryArray[index].adult = parseInt(item.adultLimit);
            } else {
                this.roomcategoryArray[index].adult = event.target.value;
            }
        },
        limitChildren(event, item, index) {
            if (event.target.value > parseInt(item.childrenLimit)) {
                this.roomcategoryArray[index].children = parseInt(item.childrenLimit);
            } else {
                this.roomcategoryArray[index].children = event.target.value;
            }
        },
        limitInfant(event, item, index) {
            if (event.target.value > parseInt(item.infantLimit)) {
                this.roomcategoryArray[index].infant = parseInt(item.infantLimit);
            } else {
                this.roomcategoryArray[index].infant = event.target.value;
            }
        },
        facilityGstSum(val, item) {
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
        extraBedGstSum(val, item) {
            const sum = val.reduce((total, el) => {
                return total + el.tax_name.rate;
            }, 0);
            this.roomcategoryArray.forEach((el) => {
                if (el.id === item.id) {
                    el.meal_sum_tax_rate = sum;
                }
            });
        },
        mealPersonChange(item) {
            this.loading = true;
            this.roomcategoryArray.forEach((el) => {
                if (el.id === item.id) {
                    el.meal_rate_extra_price = ((el.extra_meal_plan) * (el.meal_plan_price));
                }
            });
            this.loading = false;
        },
        changeRoomRateValue(event, item) {
            this.loading = true;
            let rateValue = event.target.value;
            this.setRoomRateValue(rateValue, item.id);
            this.loading = false;
        },
        setRoomRateValue(rateValue, itemId) {
            let gst = [];
            let indRate = 6;
            if (rateValue <= 7500) {
                indRate = 6;
            } else {
                indRate = 9;
            }

            this.allVatRates.forEach((value) => {
                if (value.rate == indRate) {
                    gst.push({ tax_name: value })
                }
            })

            this.roomcategoryArray.forEach((el) => {
                if (el.id === itemId) {
                    el.total_tax = 2 * indRate;
                    el.gstRate = gst;
                    el.room_rate = rateValue;
                    // el.totalRoomGst = (rateValue * this.totalDays) * 2 * indRate / 100;
                    el.totalRoomGst = (rateValue) * 2 * indRate / 100;
                }
            });
        },
        roomCategorySelect(item) {

            item.room_number_id = '';
            item.room_rate = 0;
            item.gstRate = [];
            item.adult = '';
            item.children = '';
            item.infant = '';
            item.total_tax = 0;

            this.room_category_form.cat_id = item?.room_booking_id?.id;
            this.room_category_form.hotel_id = this.hotel_id.id;
            this.room_category_form.check_in_date = moment(this.check_in_date).format('yyyy-MM-DD hh:MM:SS');

            this.getRoomCategoryListData(item.id);
        },
        roomNumberSelect(items, item) {
            this.room_number_form.id = item.id;
            this.getRoomNumberListData(item.id, items.id);
        },
        async roomFacilitySelect(item, facility_id) {
            const idArray = facility_id.map((el) => el.id);
            const idString = idArray.join(',');

            this.facilities_single_form.id = idString;
            const { data } = await this.facilities_single_form.post(
                window.location.origin +
                '/api/room/category/lsit/facility/price/hotel/singel',
            );
            const facilityData = await this.facilities_single_form.post(
                window.location.origin +
                '/api/room/category/lsit/facility/price/hotel/singel/price',
            );
            this.roomcategoryArray.forEach((el) => {
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
        },
        async mealPlanData(item, id) {
            this.meal_plan_form.meal_id = id;
            this.meal_plan_form.hotel_id = this.hotel_id.id;
            const { data } = await this.meal_plan_form.post(
                window.location.origin +
                '/api/hotel/room/meal/view',
            );
            const mealData = await this.meal_plan_form.post(
                window.location.origin +
                '/api/hotel/room/meal/view/price',
            );

            this.roomcategoryArray.forEach((el) => {
                if (el.id === item.id) {
                    let rate = this.tax_included
                        ? this.inclusiveTaxAmount(data.data.price, mealData.data?.data?.sumOfTax)
                        : data.data.price;
                    el.meal_sum_tax_rate = mealData.data?.data?.sumOfTax;
                    el.meal_plan_price = rate;
                    el.meal_tax_rate = data.data.tax_rate;
                    item.extra_meal_plan = el.adult;
                    el.meal_rate_extra_price = ((el.extra_meal_plan) * (el.meal_plan_price));
                }
            });
        },
        extraBedRateChange(event, item) {
            this.bedCapacityError = false;
            if (event.target.value > item.extra_bed_capacity) item.extra_bed = item.extra_bed_capacity;
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
        },
        extraPersonChange(event, item) {
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
        },
        notBeforeToday(date) {
            return date < new Date(new Date().setHours(0, 0, 0, 0));
        },
        notBeforeTodayTwelveOClock(date) {
            return date < new Date(new Date().setHours(12, 0, 0, 0));
        },
        notBeforeCheckIn(date) {
            return date <= this.check_in_date;
        },
        notBeforeCheckInTwelveOClock(date) {
            return date < this.check_in_date;
        },
        calculateDays() {
            if (this.processing) return;
            this.totalDays = moment(this.check_out_date).diff(moment(this.check_in_date), 'days')
            if (!this.slugdata) {
                this.getHotelDataList();
                setTimeout(() => {
                    this.hotelItems?.forEach((hotel) => {
                        if (hotel.id == this.hotel_id.id) {
                            this.hotel_id = hotel
                        }
                    });
                }, 500);
            }
            // const differenceInMilliseconds = this.check_out_date - this.check_in_date;
            // this.totalDays = Math.floor(differenceInMilliseconds / (1000 * 60 * 60 * 24));
        },
        defaultRoomCategoryArray(id) {
            return {
                id: id,
                room_booking_id: '',
                room_number_id: '',
                roomNumber: [],
                noOfRooms: '',
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
                visible:false,
            }
        },
        addRoomCategory() {
            this.roomcategoryArray.push(
                this.defaultRoomCategoryArray(this.roomcategoryArray.length + 1)
            );

            this.getMealPlanData(this.hotel_id.id);
            this.getRoomFacilityData(this.hotel_id.id);
        },
        changeHotel() {
            this.roomcategoryArray = [
                this.defaultRoomCategoryArray(1)
            ]
        },
        removeItem(index) {
            const newArray = [...this.roomcategoryArray];
            newArray.splice(index, 1);
            this.roomcategoryArray = newArray;
        },
        async getBookingType() {
            await this.$store.dispatch('operations/getFacilityData', {
                path: '/api/booking/type/list',
            });
        },
        async getVatRates() {
            await axios.get('/api/all-vat-rates').then((response) => {
                this.allVatRates = response.data.data;
            });
        },
        async getHotelDataList() {
            if (!this.slugdata) {
                await this.$store.dispatch('operations/getHotelData', {
                    path: '/api/hotel?check_in_date=' + this.check_in_date,
                });
                console.log('new', this.hotelItems);
            } else {
                await this.$store.dispatch('operations/getHotelData', {
                    path: '/api/hotel'
                });
            }

        },
        // async getRoomCategoryData () {
        //     await this.$store.dispatch('operations/getHotelCategoryData', {
        //         path: '/api/room/category',
        //     });
        // },
        async getMealPlanData(id) {
            this.meal_list_form.hotel_id = id;
            const { data } = await this.meal_list_form.post(
                window.location.origin +
                '/api/hotel/room/meal/view/all/name',
            );
            this.roomcategoryArray.forEach((el) => {
                el.mealListData = data.data;
            });
        },
        async getRoomFacilityData(id) {
            this.facilities_list_form.hotel_id = id;
            const { data } = await this.facilities_list_form.post(
                window.location.origin +
                '/api/room/category/lsit/facility/price/hotel',
            );

            this.roomcategoryArray.forEach((el) => {
                el.facilitiesListData = data.data;
            });
        },
        async getBookedBySupplier() {
            const { data } = await axios.get(
                window.location.origin +
                '/api/all-agents-client',
            );
            this.allClientList = data.data.filter((datum) => datum.type == 1); //get all clients
            this.bookedByList = data.data.filter((datum) => datum.type == 2); // get all agents
            this.loading = false;
        },
        async getLedgerListData() {
            this.payment_method_form.ledger_type = '1';
            this.payment_method_form.bank_only = 1;
            const { data } = await this.payment_method_form.post(
                window.location.origin + '/api/account/ledger/list');
            this.paymentModeData = data.data;
            this.payment_mode_id = '';

            this.paymentModeData1 = this.paymentModeData.filter(item => item.code !== this.paymentModeData[0].code);
            this.payment_mode_id1 = (typeof this.paymentModeData1[0] !== 'undefined') ? this.paymentModeData1[0] : '';
            this.bank_charges = 0;
            this.bank_charges1 = 0;
        },
        setDropDownData(paymentId, paymentData) {
            this.paymentModeData1 = paymentData.filter(item => item.code !== paymentId.code);
            this.payment_mode_id1 = (typeof this.paymentModeData1[0] !== 'undefined') ? this.paymentModeData[0] : '';
        },
        async getRoomCategoryListData(id) {

            const { data } = await this.room_category_form.post(
                window.location.origin +
                '/api/hotel/get-hotel-cat'
            );

            if (!this.slugdata) {
                if (!data.data.length) {
                    return toast.fire({ type: 'error', title: 'No Room Available In this category please select another category' });
                }
                // console.log('roomCate',this.roomcategoryArray);
                this.roomcategoryArray.forEach((el) => {
                    data.data = data.data.filter((ele) => el.room_number_id.id !== ele.id);
                    // roomNumber
                    if (el.id === id) {
                        el.roomNumber = data.data;
                        el.extra_bed_capacity = (data.data.length) ? data.data[0]?.hotel_room_category.extra_bed_capacity : '';
                        el.extra_person_capacity = (data.data.length) ? data.data[0]?.hotel_room_category.extra_person_capacity : '';
                        el.per_person = (data.data.length) ? data.data[0]?.hotel_room_category.per_person : '';
                        el.extra_bed_rate = (data.data.length) ? data.data[0]?.hotel_room_category.extra_bed_rate : '';
                        el.adult = (data.data.length) ? data.data[0]?.hotel_room_category.no_of_person : '';
                        el.adultLimit = (data.data.length) ? data.data[0]?.hotel_room_category.no_of_person : '';
                        el.children = (data.data.length) ? data.data[0]?.hotel_room_category.no_of_child : '';
                        el.childrenLimit = (data.data.length) ? data.data[0]?.hotel_room_category.no_of_child : '';
                        el.infant = (data.data.length) ? data.data[0]?.hotel_room_category.no_of_infant : '';
                        el.infantLimit = (data.data.length) ? data.data[0]?.hotel_room_category.no_of_infant : '';
                        el.noOfRooms = (data.data.length) ? (data.data.length > 1) ? 1 : data.data.length : '';
                        // el.minrooms = "1";
                        // console.log("min" + el.minrooms);
                        // console.log("max" + el.noOfRooms);
                        el.noOfRoomsLimit = (data.data.length) ? data.data.length : '';

                        if (data.data.length) {
                            this.setRoomRateValue(data.data[0].hotel_room_category.rate, el.id);
                        }
                    }
                });

                if (this.roomcategoryArray.length > 1) {
                    let item = this.roomcategoryArray[this.roomcategoryArray.length - 1];
                    let index = this.roomcategoryArray.length - 1;
                    this.checkRoomLimit(item, index);
                }

            } else {

                this.roomcategoryArray = this.roomcategoryArray.map(item => {

                    if (item.id === id) {
                        return {
                            ...item,
                            roomNumber: data.data
                        };
                    }
                    return item;
                });

            }
            // console.log('roomCategoryFinal',this.roomcategoryArray);
        },

        checkRoomLimit(item, index) {
            console.log('item', item);
            console.log('index', index);
            let totalCategoryLimit = item.room_booking_id.count;
            let totalRoom = 0;
            if (this.roomcategoryArray.length > 1) {
                this.roomcategoryArray.forEach((el, roomIndex) => {
                    if (el.room_booking_id.id == item.room_booking_id.id) {
                        totalRoom += parseInt(el.noOfRooms);
                    }
                });
            }

            console.log(totalRoom);
            console.log(totalCategoryLimit);

            if (totalRoom > totalCategoryLimit) {
                this.roomcategoryArray[index].noOfRooms = 0;
                this.isButtonDisabled = true;
                return toast.fire({ type: 'error', title: 'No Room Available In this Category Please Select Another Category, Total Available Room In this Category is ' + totalCategoryLimit + ' and you have selected total no of room is ' + totalRoom });

            } else {
                this.isButtonDisabled = false;
            }
        },

        // compareByCatId(obj1,obj2){

        //     if(obj1.room_booking_id.id == obj2.room_booking_id.id){
        //         if(obj1.noOfRoomsLimit < parseFloat(obj1.noOfRooms) + parseFloat(obj2.noOfRooms)){
        //             return {
        //                 'condition':true,
        //                 'total': obj1.noOfRoomsLimit - (obj1.noOfRoomsLimit - parseFloat(obj1.noOfRooms)),
        //             };
        //         }
        //     }
        // },

        async getRoomNumberListData(id, itemId) {

            const { data } = await this.room_number_form.post(
                window.location.origin +
                '/api/hotel/get-single-room',
            );

            // const rate_data = await this.room_number_form.post(
            //     window.location.origin +
            //     '/api/hotel/get-single-room-price',
            // );

            //   this.setRoomRateValue(data.data.room_rate, itemId);
            // this.roomcategoryArray.forEach((el) => {
            //     if (el.id === itemId) {
            // el.total_tax = rate_data.data.data.sumOfTax;
            // el.adult = data.data.no_of_person;
            // el.adultLimit = data.data.no_of_person;
            // el.children = data.data.no_of_child;
            // el.childrenLimit = data.data.no_of_child;
            // el.extra_bed_capacity = data.data.extra_bed_capacity;
            // el.extra_person_capacity = data.data.extra_person_capacity;
            // el.infant = data.data.no_of_infant;
            // el.infantLimit = data.data.no_of_infant;
            // el.room_rate = data.data.room_rate;
            // el.per_person = data.data.per_person;
            // el.extra_bed_rate = data.data.extra_bed_rate;
            // el.gstRate = data?.data?.tax_rate;
            // el.totalRoomGst = (data.data.room_rate * this.totalDays) * rate_data.data.data.sumOfTax / 100;
            // }
            // });
        },
        onFileChange(e) {
            const file = e.target.files[0];
            if (
                // file.size < 2111775 &&
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
        async saveClient() {
            let hotelId = this.hotel_id?.id || this.hotel_id || 1;
            this.clientForm.hotel_id = { id: hotelId };
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
        async createNewClient() {
            this.clientForm.images = [];
            this.clientForm.images = this.images && this.images.length ? _.map(this.images, 'file') : [];
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
        async searchClientData() {
            await this.searchData(this.$store, this.clientPhone);
        },
        async searchData(store, clientPhone) {
            store.dispatch('operations/searchData', {
                path: '/api/clients/search?customer_only=1',
            })
            this.isLoadingClient = false;
        },
        async handleOldClient() {
            await this.searchClientData();
            if (this.slugdata) return $('#oldClientEdit').modal('show');
            this.isSuggestionBox = true;
            // this.isLoadingClient = true;
            // this.selectedOldCustomer = this.items[0]
            $('#oldClient').modal('show');
        },
        async reload() {
            this.clientPhone = '';
            await this.searchClientData();
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
            this.clientForm.name = '';
            this.clientForm.email = '';
            this.clientForm.phoneNumber = data;
            this.clientForm.address = '';
            this.clientForm.image = '';
            this.clientForm.images = [];
            this.clientForm.status = 1;
            this.clientForm.identity = '';
            this.clientForm.nationality = 'Indian';
            this.clientForm.gst = '';
            this.clientForm.pastImages = [];
        },
        async clientChange(data) {
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
            this.clientForm.gst = data.gst;
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
                    file: null,
                    previewUrl: `/images/clients/${data.image}`,
                    name: data.image
                })
            }
        },
        saveOldClient(item = null) {
            if (!this.clientData.clientId) {
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
        removeClientItem(index, item) {
            this.roomcategoryArray.forEach((el) => {
                if (el.id === item.id) {
                    el.clientItemsData.splice(index, 1);
                }
            });
        },
        removeClientItemEdit(key) {
            this.clientItemsData.splice(key, 1);
        },
        async saveBooking(event, directCheckIn = false) {
            // if (!this.payment_mode_id) return toast.fire({ type: 'error', title: this.$t('booking.create.payment_error') });
            // if (this.advance_amount == 0) return toast.fire({ type: 'error', title: this.$t('booking.create.payment_advance_error') });
            this.form.customer = [];
            this.processing = true;
            let checkIn = _.cloneDeep(this.check_in_date)
            let checkOut = _.cloneDeep(this.check_out_date)
            checkIn = moment(checkIn).format('yyyy-MM-DD')
            checkOut = moment(checkOut).format('yyyy-MM-DD')
            if (checkIn === checkOut) return toast.fire({ type: 'error', title: this.$t('booking.create.date_error') });
            if (this.$route?.params?.slug) {
                this.bookingData.check_in_date = checkIn;
                this.bookingData.check_out_date = checkOut;
                this.bookingData.arrival_from = this.arrivalForm;
                this.bookingData.purpose = this.puposeOfVisit;
                this.bookingData.paid_amount = this.pending_amount;
                this.bookingData.remarks = this.remarks;
                this.bookingData.hotel = this.hotel_id;
                this.bookingData.payment_method = (this.payment_mode_id != '') ? this.payment_mode_id.id : '';
                this.bookingData.payment_method1 = (this.payment_mode_id1 != '') ? this.payment_mode_id1.id : '';
                this.bookingData.booking_details = this.roomcategoryArray;
                this.bookingData.discount_amount = this.discount_amount;
                this.bookingData.comments = this.form.comments;
                this.bookingData.advance_remarks = this.form.advance_remarks;
                this.bookingData.advance_amount = parseInt(this.advance_amount) + (parseInt(this.advance_credit_amount) || 0);
                this.bookingData.advance_amount1 = parseInt(this.advance_amount1) + parseInt(this.advance_amount) + (parseInt(this.advance_credit_amount) || 0);
                this.bookingData.bank_charges = this.bank_charges;
                this.bookingData.bank_charges1 = this.bank_charges1;
                this.bookingData.booking_source = this.bookedBy?.id;
                this.bookingData.boking_status = this.client_booking_status_id;
                this.bookingData.total_price = this.total_room_amount;
                this.bookingData.type = 2;
                this.bookingData.is_extra_payment_method = this.addExtraPaymentMethod;
                this.bookingData.client_booking_status = 3 //Client Status Change when it's Occupied
                this.bookingData.customer = [];
                if (this.clientItemsData.length <= 0) {
                    return toast.fire({ type: 'error', title: 'At-least one customer is required' })
                }
                try {
                    this.clientItemsData.forEach((element, index) => {
                        this.bookingData.customer.push({ id: null, customer_id: element.id, customer_id_request: null });
                        if (element?.checked) {
                            this.bookingData.booked_by = element.id;
                        }
                        if (!this.bookingData.booked_by && index == 0) {
                            this.bookingData.booked_by = element.id;
                        }
                    });
                } catch (error) {
                    toast.fire({ type: 'error', title: error.message })
                }
                let data = new Form(this.bookingData);

                this.roomcategoryArray.forEach((el, index) => {
                    el.room_id = (this.selectedRooms[index]) ? this.selectedRooms[index] : el.room_id;
                });
                data.rooms = this.roomcategoryArray;

                await data.post(window.location.origin + '/api/booking/add').then(() => {
                    toast.fire({
                        type: 'success',
                        title: 'The room check-in has been successful',
                    });
                    this.$router.push({ name: 'booking' });
                }).catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                });
            } else {
                this.form.type = directCheckIn ? 2 : 1;
                let customerAvailable = false;
                try {
                    this.roomcategoryArray.forEach((ele) => {
                        ele.clientItemsData.forEach((element, index) => {
                            customerAvailable = true
                            this.form.customer.push({ id: null, customer_id: element.id, customer_id_request: null });
                            if (element?.checked) {
                                this.form.booked_by = element.id;
                            }
                            if (!this.form.booked_by && index == 0) {
                                this.form.booked_by = element.id;
                            }
                        });
                    });
                } catch (error) {
                    toast.fire({ type: 'error', title: error.message })
                }
                if (!this.bookedBy?.id && !customerAvailable) return toast.fire({ type: 'error', title: this.$t('booking.create.customer_error') });
                if (!this.commissionTo?.id && this.commission_amount > 0) return toast.fire({ type: 'error', title: this.$t('booking.create.commission_error') });
                if (this.commissionTo?.id && this.commission_amount <= 0) return toast.fire({ type: 'error', title: this.$t('booking.create.commission_amount_error') });
                this.form.total_room = 1;
                this.form.id = null;
                this.form.client_booking_status = this.client_booking_status_id.id;
                this.form.advance_amount = this.advance_amount;
                this.form.advance_amount1 = this.advance_amount1;
                this.form.paid_amount = this.pending_amount;
                this.form.total_price = this.total_room_amount;
                this.form.payment_method = this.payment_mode_id.id;
                this.form.payment_method1 = (this.payment_mode_id1 != '') ? this.payment_mode_id1.id : '';
                this.form.remarks = this.remarks;
                this.form.discount_amount = this.discount_amount;
                this.form.commission_amount = this.commission_amount;
                this.form.bank_charges = this.bank_charges;
                this.form.bank_charges1 = this.bank_charges1;
                this.form.discount_reason = null;
                this.form.full_guest_name = null;
                this.form.special_request = null;
                this.form.check_in_date = checkIn;
                this.form.check_out_date = checkOut;
                this.form.purpose = this.puposeOfVisit;
                this.form.hotel_id = this.hotel_id?.id;
                this.form.booking_source = this.bookedBy?.id;
                this.form.commission_to = this.commissionTo?.id;
                this.form.booking_type_id = this.booking_source?.id;
                this.form.arrival_from = this.arrivalForm;
                this.form.discount_type = this.discount_room_rate?.id;
                this.form.final_gst_rates = this.final_gst_rate;
                this.form.is_extra_payment_method = this.addExtraPaymentMethod;
                this.form.rooms = [];
                this.form.tax_included = this.tax_included ? 1 : 0;

                if (this.roomcategoryArray.some(el => parseInt(el.noOfRooms) === 0 || el.noOfRooms === "" || el.room_booking_id?.id === "")) {
                    toast.fire({ type: 'error', title: 'No of rooms cannot be 0 OR blank. Please select a different category.' });
                    return false;
                }

                this.roomcategoryArray.forEach((el) => {

                    if (el.room_booking_id.id) {
                        const roomObj = {
                            id: null,
                            room_booking_id: null,
                            room_id: el.room_booking_id?.id,
                            room_no: el.room_number_id?.id,
                            noOfRooms: parseInt(el.noOfRooms),
                            adult: parseInt(el.adult),
                            children: parseInt(el.children),
                            infant: parseInt(el.infant),
                            meal_id: el.meal_plan_id?.id,
                            extra_bed: el.extra_bed,
                            extra_person: el.extra_person,
                            extra_child: null,
                            complementary: el.room_facility_id,
                            complementary_price: el.facilities_price,
                            extra_facility_days: this.totalDays,
                            // room_tax: this.tax_included ? (((parseFloat(el.room_rate || 0) * this.totalDays) - (el.discount || 0)) * el.total_tax / 100) : el.totalRoomGst,
                            room_tax: this.tax_included ? (((parseFloat(el.room_rate || 0) * this.totalDays) - (el.discount || 0)) * el.total_tax / 100) : el.totalRoomGst * this.totalDays,
                            meal_plan_tax: this.tax_included ? ((el.meal_rate_extra_price * this.totalDays) * el.meal_sum_tax_rate / 100) : el.totalMealGst,
                            facility_tax: this.tax_included ? ((el.facilities_rate_price * this.totalDays) * el.facility_gst_rate_price / 100) : el.totalFacilityGst,
                            extra_bed_tax: this.tax_included ? ((el.extra_bed_rate_price * this.totalDays) * el.total_tax / 100) : el.totalBedGst,
                            extra_bed_price: el.extra_bed_rate_price * this.totalDays,
                            extra_person_tax: this.tax_included ? ((el.extra_person_rate_price * this.totalDays) * el.total_tax / 100) : el.totalExtraPersonGst,
                            extra_person_price: el.extra_person_rate_price * this.totalDays,
                            room_rate: el.room_rate * this.totalDays,
                            modified_room_rate: el.room_rate,
                            extra_meal_plan: el.extra_meal_plan,
                            discount: el.discount,
                            commission: el.commission,
                            totalRoom: el.roomNumber
                        };
                        this.form.rooms.push(roomObj);
                    }
                });
                await this.form.post(window.location.origin + '/api/booking/add').then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('booking.create.success_msg'),
                    });
                    this.$router.push({ name: 'booking' });
                }).catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message });
                });
            }
            this.processing = false;
        },
        async getBookingData() {
            let hotelId = this.hotel_id?.id || this.hotel_id || 1;
            const { data } = await axios.get(
                window.location.origin + '/api/booking/view/' + this.$route.params.slug + '?hotel_id=' + hotelId);
            this.roomsList = [];
            this.mealPlanDetailsList = [];
            this.mealDataList = [];
            this.complementraysList = [];
            this.facilitiesListDataList = [];
            this.bookingData = data.data;

            this.check_in_date = new Date(this.bookingData.check_in_date.split(' ')[0]);
            this.check_out_date = new Date(this.bookingData.check_out_date.split(' ')[0]);
            this.dateRange = {
                startDate: this.check_in_date,
                endDate: this.check_out_date,
            };
            this.arrivalForm = this.bookingData.arrival_from;
            this.puposeOfVisit = this.bookingData.purpose;
            this.remarks = this.bookingData.remarks;
            this.hotel_id = this.bookingData.hotel;
            this.roomcategoryArray = this.bookingData.booking_details;
            this.discount_amount = this.bookingData.discount_amount;
            this.commission_amount = this.bookingData.commission_amount;
            this.form.comments = this.bookingData.comments;
            this.form.advance_remarks = this.bookingData.advance_remarks;
            this.advance_amount = 0;
            this.tax_included = this.bookingData.tax_included;
            this.advance_credit_amount = this.bookingData.advance_amount;
            this.booking_source = this.bookingData?.booking_type;
            this.bookedBy = this.bookingData.source;
            this.client_booking_status_id = this.bookingData.boking_status;
            this.total_room_amount = this.bookingData.total_price;
            this.paymentModeData.forEach((mode) => {
                if (mode.id == this.bookingData.payment_method) this.payment_mode_id = mode;
            })
            this.discountRoomRateData.forEach((type) => {
                if (type.id == this.bookingData.discount_type) {
                    this.discount_room_rate = type
                }
            })

            this.bookingData.booking_details.map((bookingDetail) => {

                if (bookingDetail.room) {
                    this.roomsList.push(bookingDetail.room);

                    this.room_category_form.cat_id = bookingDetail.room.room_categorary;
                    this.room_category_form.hotel_id = bookingDetail.room.hotel_id;
                    this.room_category_form.check_in_date = moment(this.bookingData.check_in_date).format('yyyy-MM-DD hh:MM:SS');

                    this.getRoomCategoryListData(bookingDetail.id);
                }
                if (bookingDetail.meal_plan_details) {
                    this.extraMealPersons.push(bookingDetail.meal_plan_persons)
                    this.mealPlanDetailsList.push(bookingDetail.meal_plan_details);

                }
                if (bookingDetail.complementrays) {
                    this.complementraysList.push(bookingDetail.complementrays);
                }
            });

            if (this.discount_room_rate.id == 1) {
                this.discount = this.discount_amount;
            } else {
                this.discount = ((100 * this.discount_amount) / (_.sumBy(this.bookingData.booking_details, 'room_rate')));
                this.discount = Math.round(this.discount);
            }

            this.bookingData.booking_details.forEach((item, index) => {
                let prices = [];
                item.complementrays.forEach((comp) => {
                    prices.push(parseInt(comp?.paid_facility?.facility_rate[0]?.price));
                })
                this.roomcategoryArray[index].facilities_rate_price = _.sum(prices)
            })
            this.clientItemsData = _.uniqBy(this.bookingData.customers, 'client_id');
            this.clientItemsData = this.clientItemsData.map((client) => {
                return {
                    id: client.id,
                    clientPhone: client.phone,
                    clientName: client.name,
                }
            })
        },
    },
    beforeDestroy() {
        $('.modal-backdrop').remove();
    },
    mounted() {
        // this.$route?.params?.slug && this.getBookingData();
        // this.selectedOldCustomer = this.items.length ? this.items[0] : null;
    },
};
</script>

<style src="../../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
/* #form-box {
    display: flex;
} */

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
    cursor: pointer;
}

.remove-button {
    background: none;
    border: none;
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

.create-btn {
    cursor: pointer;
}

.coustom_info_btn {
    border: 1px solid var(--primaryColor);
    margin-right: 10px;
    padding: 10px 10px;
    cursor: pointer;
    border-radius: 5px;
    color: var(--primaryColor);
    background-color: #ec366614;
    max-height: 45px;
}

.gst_box {
    width: 74px;
}

.calc_box {
    font-size: 14px;
}

.main_calc_box {
    min-height: 90px;
    height: 100%;
}

.room-booking-form label i {
    margin-right: 5px;
}

.close-icon i {
    margin: 0 !important;
}

/* .show_additional {
    display: none !important;
    cursor: pointer;
} */
</style>