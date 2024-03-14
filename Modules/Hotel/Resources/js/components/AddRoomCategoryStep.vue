<template>
    <div class="card p-4">

        <div>
            <div class="row d-flex justify-content-between">
                <button @click="currentTabPre()" class="btn btn-secondary mr-3">
                    <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                </button>
                <div class="d-flex">
                <button @click="createRoomCategory" class="btn btn-primary" v-if="!loading && addRoomList.length == 0">
                    {{ $t("common.add_room_category") }}
                    <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                </button>

                <button @click="currentTab()" class="btn btn-secondary ml-3">
                    {{ $t('common.next') }} <i class="fas fa-long-arrow-alt-right mt-1" />
                </button>
            </div>
            </div>
        </div>

        <div class="card-body p-0 position-relative">
            <table-loading v-show="loading" />
            <div id="printMe" class="table-responsive table-custom mt-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ $t("rooms.common.room_category") }}</th>
                            <th>{{ $t("common.room_category_price") }}</th>
                            <th>{{ $t("common.extra_bed_capacity") }}</th>
                            <th>{{ $t("common.extra_person_capacity") }}</th>
                            <th>Persons</th>
                            <th>{{ $t("rooms.common.tax_rates") }}</th>
                            <th style="position: sticky; left: 0;" class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-show="addRoomList.length > 0" v-for="(data, i) in addRoomList" :key="i">
                            <!-- <td style="position: sticky; left: 0; background-color: #fff;">
                               
                                <div class="btn-group">
                                        <i  @click="editData(data, i)" class="fas fa-edit cursor-pointer" style="cursor:pointer"/>
                                        
                                </div>
                            </td> -->
                            <td>
                                <div class="form-group mb-0">
                                    <div class="d-flex align-items-center">
                                        <select :class="{ 'is-invalid': room_form?.errors.has('room_categorary') }" class="form-control" v-if="editRoom && i == selectedIndex" v-model="data.room_categorary">
                                            <option v-for="(item) in hotelCategoryItems" :key="item.id" :value="item.id">{{ item?.room_category_name }}</option>
                                        </select>
                                        <span v-else> {{ data?.room_category.room_category_name }}</span>
                                    </div>
                                    <has-error v-if="editRoom && i == selectedIndex" :form="room_form" field="room_categorary" />
                                </div>
                                <!-- <br /> -->
                                <!-- <div class="d-flex align-items-center">
                                    <select v-if="editRoom && i == selectedIndex" v-model="data.floor_id" class="form-control">
                                        <option v-for="(item) in floorItems" :key="item.id" :value="item.id">{{ item.floorsName }}</option>
                                    </select>
                                    <span v-else>{{ data?.floor?.floorsName }}</span>
                                </div> -->
                            </td>
                            <td>
                                
                                <div class="form-group mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class='fas fa-rupee-sign mr-1'></i>
                                        <input :class="{ 'is-invalid': room_form?.errors.has('room_rate') }" v-if="editRoom && i == selectedIndex" type="text" v-model="data.rate" class="px-2 form-control room-input">
                                        <span v-else>{{ data.rate }}</span>
                                    </div>
                                    <has-error v-if="editRoom && i == selectedIndex" :form="room_form" field="room_rate" />
                                </div>
                            </td>
                            
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-bed ml-2"></i>
                                    <input v-if="editRoom && i == selectedIndex" type="text" v-model="data.extra_bed_capacity" class="px-2 room-input">
                                    <span v-else class="ml-2">{{ data.extra_bed_capacity }}</span>
                                    <span class="ml-3">|</span>
                                    <div class="d-flex gap-2 align-items-center ml-3">
                                    <i class='fas fa-rupee-sign'></i>
                                        <input v-if="editRoom && i == selectedIndex" type="text" v-model="data.extra_bed_rate" class="px-2 ml-2 room-input">
                                        <span v-else class="ml-1">{{ data.extra_bed_rate }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="d-flex gap-5">
                                <div class="d-flex align-items-center ml-3">
                                    <i class="fas fa-male"></i>
                                    <input v-if="editRoom && i == selectedIndex" type="number" v-model.number="data.extra_person_capacity" class="px-2 room-input">
                                    <span v-else class="ml-2">{{ data.extra_person_capacity }}</span>
                                </div>
                                <span class="ml-3">|</span>
                                <div class="d-flex align-items-center ml-3">
                                    <i class='fas fa-rupee-sign'></i>
                                    <input v-if="editRoom && i == selectedIndex" type="text" v-model="data.per_person" class="px-2 room-input">
                                    <span v-else class="ml-1">{{ data.per_person }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group mb-0 d-flex justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-male"></i>
                                        <input :class="{ 'is-invalid': room_form?.errors.has('no_of_person') }" v-if="editRoom && i == selectedIndex" type="text" v-model="data.no_of_person" class="px-2 form-control room-input">
                                        <span v-else>{{ data.no_of_person }}</span>
                                    </div>
                                    <has-error v-if="editRoom && i == selectedIndex" :form="room_form" field="no_of_person" />
                                    <div>
                                        <i class="fas fa-child"></i>
                                        <input v-if="editRoom && i == selectedIndex" type="text" v-model="data.no_of_child" class="px-2 room-input">
                                        <span v-else>{{ data.no_of_child }}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-baby"></i>
                                        <input v-if="editRoom && i == selectedIndex" type="text" v-model="data.no_of_infant" class="px-2 room-input">
                                        <span v-else>{{ data.no_of_infant }}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="min-width: 250px">

                                <div class="d-flex">
                                    <span v-for="(item,index) in data?.tax_rate" :key="index+'11'">
                                        {{ item?.tax_name?.name }}
                                        <span class="mx-1" v-if="data?.tax_rate?.length != index + 1">,</span>
                                    </span>
                                </div>

                            </td>
                            <td style="position: sticky; left: 0; background-color: #fff;" class="text-right">
                               
                               <div class="btn-group btn btn-info btn-sm ">
                                       <i  @click="editData(data, i)" class="fas fa-edit cursor-pointer" style="cursor:pointer"/>
                                       
                               </div>
                           </td>
                        </tr>
                        <tr v-show="!loading && addRoomList.length==0">
                            <td colspan="12">
                                <EmptyTable />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- <div class="mt-3">
                <router-link :to="{ name: 'hotel' }" class="btn btn-primary float-right">
                    {{ $t('common.submit') }}
                </router-link>
            </div> -->

            <div class="modal" id="newRoomAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div v-if="currentRoom" class="modal-dialog modal-width modal-lg modal-dialog-centered" role="document" style="max-width: 600px">
                    <div class="modal-lg modal-content" style="max-width: 600px;">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Edit room category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <form role="form" @submit.prevent="saveRoomData(currentRoom.id , currentRoom)" @keydown="room_form.onKeydown($event)">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <label for="name">{{ $t('rooms.common.room_category') }}
                                                    <span class="required">*</span></label>
                                                <select :class="{ 'is-invalid': room_form?.errors.has('room_category') }" class="form-control" v-model="currentRoom.room_category.id" disabled>
                                                    <option v-for="(item) in hotelCategoryItems" :key="item.id" :value="item.id">{{ item.room_category_name }}</option>
                                                 </select>
                                                <has-error :form="room_form" field="room_category" />
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="name">{{ $t('rooms.common.room_price') }}
                                                    <span class="required">*</span></label>
                                                <input step="0.1" :class="{ 'is-invalid': room_form?.errors.has('rate') }" type="text" v-model="currentRoom.rate" class="px-2 form-control">
                                                <has-error :form="room_form" field="room_rate" />
                                            </div>
                                            <!-- <div class="form-group col-md-4">
                                                <label for="name">{{ $t('common.extra_bed_capacity') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.extra_bed_capacity" class="px-2 form-control">
                                                <has-error :form="room_form" field="extra_bed_capacity" />
                                            </div> -->
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4">
                                                <label for="name">{{ $t('common.adult') }}
                                                    <span class="required">*</span></label>
                                                <input :class="{ 'is-invalid': room_form?.errors.has('no_of_person') }"  type="text" v-model="currentRoom.no_of_person" class="px-2 form-control">
                                                <has-error  :form="room_form" field="no_of_person" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="name">{{ $t('common.children') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.no_of_child" class="px-2 form-control">
                                                <has-error :form="room_form" field="no_of_child" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="name">{{ $t('common.infant') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.no_of_infant" class="px-2 form-control">
                                                <has-error  :form="room_form" field="no_of_infant" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="name">{{ $t('common.extra_bed_capacity') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.extra_bed_capacity" class="px-2 form-control">
                                                <has-error :form="room_form" field="extra_bed_capacity" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="name">{{ $t('common.extra_bed_price') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.extra_bed_rate" class="px-2 form-control">
                                                <has-error  :form="room_form" field="extra_bed_rate" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <!-- <div class="form-group col-md-4">
                                                <label for="name">{{ $t('common.extra_bed_price') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.extra_bed_rate" class="px-2 form-control">
                                                <has-error  :form="room_form" field="extra_bed_rate" />
                                            </div> -->
                                            <div class="form-group col-md-6">
                                                <label for="name">{{ $t('common.extra_person_capacity') }}
                                                    <span class="required">*</span></label>
                                                <input type="number" v-model.number="currentRoom.extra_person_capacity" class="px-2 form-control">
                                                <has-error :form="room_form" field="extra_person_capacity" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="name">{{ $t('common.extra_person_price') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.per_person" class="px-2 form-control">
                                                <has-error  :form="room_form" field="per_person" />
                                            </div>
                                        </div>
                                        <!-- <div class="row">
                                            <div class="form-group col-lg-4">
                                                <label for="name">{{ $t('common.adult') }}
                                                    <span class="required">*</span></label>
                                                <input :class="{ 'is-invalid': room_form?.errors.has('no_of_person') }"  type="text" v-model="currentRoom.no_of_person" class="px-2 form-control">
                                                <has-error  :form="room_form" field="no_of_person" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="name">{{ $t('common.children') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.no_of_child" class="px-2 form-control">
                                                <has-error :form="room_form" field="no_of_child" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="name">{{ $t('common.infant') }}
                                                    <span class="required">*</span></label>
                                                <input type="text" v-model="currentRoom.no_of_infant" class="px-2 form-control">
                                                <has-error  :form="room_form" field="no_of_infant" />
                                            </div>
                                        </div> -->

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="name">{{ $t('rooms.common.tax_rates') }}
                                                    <span class="required">*</span></label>
                                                <multiselect v-model="hotel_facility_id" :options="facilityItems" :show-labels="false" :multiple="true" placeholder=""
                                            :close-on-select="false" :clear-on-select="false" :preserve-search="true" class="form-control" tag-placeholder="Add this as new tag" label="name" track-by="name" :preselect-first="false"></multiselect>
                                                <has-error  :form="room_form" field="per_person" />
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <v-button :loading="room_form.busy" class="btn btn-primary">
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
</template>

<script>
import axios from "axios";
import { mapGetters } from 'vuex'
import Form from 'vform'
import Multiselect from 'vue-multiselect'
import _ from 'lodash';

export default {
    components: {
        Multiselect,
    },
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems', 'bedTypeItems', 'floorItems']),
    },
    data() {
        return {
            room_form: new Form({
                id:'',
                room_category: '',
                hotel_id: '',
                extra_bed_capacity: '',
                extra_person_capacity: '',
                rate: '',
                extra_bed_rate: '',
                per_person: '',
                no_of_child:'',
                no_of_infant:'',
                no_of_person:'',
                taxRate: '',
            }),
            create_room_category: new Form({
                hotel_id:'',
                room_category: [],
            }),
            currentRoom: null,
            room_category_id: '',
            room_hotel_id: '',
            room_bed_type_id: '',
            addRoomList: [],
            loading: true,
            isOpenModal: false,
            modalTitle: '',
            facilityRatePrice: '',
            editRoom: false,
            selectedIndex: -1,
            hotel_facility_id: '',
            hotel_facility_array: [],
            newRoomCount: 1,
        }
    },
    created() {
        this.getHotelCategoryItems()
        this.getFacilityItems()
    },
    mounted() {
        this.getRoomCategoryList()
    },
    methods: {
        currentTab() {
            this.$emit('currentTab')
        },
        currentTabPre() {
            this.$emit('currentTabPre')
        },

        async getHotelCategoryItems() {
            await this.$store.dispatch("operations/getHotelCategoryData", {
                path: "/api/room/category",
            });

            console.log('hotelItem',this.hotelCategoryItems);
        },

        async getFacilityItems() {
            await this.$store.dispatch('operations/getFacilityData', {
                path: '/api/all-vat-rates'
            })
        },

        async getRoomCategoryList() {
            axios.get(
                window.location.origin +
                '/api/room/category/hotels?perPage=100&hotelId='+this.$route.params.slug
            ).then((res)=>{
                this.addRoomList = res.data.data
                console.log(res.data.data);
                this.loading = false
            })
        },

       

        editData(data, i) {
            this.currentRoom = data;
            this.selectedIndex = i;
            // this.editRoom = true;
            this.hotel_facility_id = _.map(data?.tax_rate, 'tax_name');
            $('#newRoomAdd').modal('show');
        },

        closeModal() {
            $('#newRoomAdd').modal('hide');
            this.currentRoom = null;
        },

        async saveRoomData(id, data) {
            this.room_form.id = id
            this.room_form.room_category = data.room_category
            this.room_form.hotel_id = this.$route.params.slug
            this.room_form.extra_bed_capacity = data.extra_bed_capacity
            this.room_form.extra_person_capacity = data.extra_person_capacity || 0
            this.room_form.rate = data.rate
            this.room_form.extra_bed_rate = data.extra_bed_rate
            this.room_form.per_person = data.per_person
            this.room_form.no_of_child = data.no_of_child
            this.room_form.no_of_infant = data.no_of_infant
            this.room_form.no_of_person = data.no_of_person
            
            this.hotel_facility_array = []
            if(this.hotel_facility_id) {
                this.hotel_facility_id.forEach((el) => {
                    this.hotel_facility_array.push(el.id)
                });
                this.room_form.taxRate = this.hotel_facility_array.toString()
            }
            // console.log(this.room_form); return false;
            await this.room_form
                .post(window.location.origin + '/api/room/category/edit_category')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Hotel Room Category Edit Successfully'),
                    })
                    this.editRoom = false
                    this.selectedIndex = -1
                    this.closeModal()
                    this.getRoomCategoryList()
                })
                .catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },

        async createRoomCategory() {
            
            this.create_room_category.room_category = this.hotelCategoryItems;
            this.create_room_category.hotel_id = this.$route.params.slug
           

            await this.create_room_category
                .post(window.location.origin + '/api/room/category/add_category')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Hotel Room Category Add Successfully'),
                    })
                    this.getRoomCategoryList()
                })
                .catch((error) => {
                    console.log(error.response)
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        }
    },
}

</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
.modal-body {
    max-height: 85vh !important;
    overflow: none !important;
}
.modal-content{
    margin: 0 !important;
}

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

.room-input{
    width: 150px;
}
</style>