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
                            {{ $t('rooms.roomAdd.create.page_title') }}
                        </h3>
                        <router-link :to="{ name: 'room' }" class="btn btn-dark float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveMealPlan()" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="room_name">{{ $t('common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="room_name" v-model="form.room_name" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('room_name') }"
                                        name="room_name" :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="room_name" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="room_categorary">{{ $t('sidebar.room_hotel_cat_lsit') }}
                                        <span class="required">*</span></label>
                                        <multiselect v-model="room_category_id" :options="hotelCategoryItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Room category" class="form-control" label="room_category_name" track-by="room_category_name" :class="{ 'is-invalid': form.errors.has('room_categorary') }"></multiselect>
                                    <has-error :form="form" field="room_categorary" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="facilityId">{{ $t('sidebar.room_facility') }}
                                        <span class="required">*</span></label>
                                        <multiselect v-model="room_facility_id" :options="facilityItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Room facility" class="form-control" label="room_facility_title" track-by="room_facility_title" :class="{ 'is-invalid': form.errors.has('facilityId') }"></multiselect>
                                    <has-error :form="form" field="facilityId" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="hotel_id">{{ $t('sidebar.hotel') }}
                                        <span class="required">*</span></label>
                                        <multiselect v-model="room_hotel_id" :options="hotelItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Hotel" class="form-control" label="hotel_name" track-by="hotel_name" :class="{ 'is-invalid': form.errors.has('hotel_id') }"></multiselect>
                                    <has-error :form="form" field="hotel_id" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="bed_type_id">{{ $t('sidebar.bedType') }}
                                        <span class="required">*</span></label>
                                        <multiselect v-model="room_bed_type_id" :options="bedTypeItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Bed Type" class="form-control" label="bedtypetitle" track-by="bedtypetitle" :class="{ 'is-invalid': form.errors.has('bed_type_id') }"></multiselect>
                                    <has-error :form="form" field="bed_type_id" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="roomdescription">{{ $t('common.description') }}
                                        <span class="required">*</span></label>
                                    <input id="roomdescription" v-model="form.roomdescription" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('roomdescription') }"
                                        name="roomdescription" :placeholder="$t('common.description_placeholder')" />
                                    <has-error :form="form" field="roomdescription" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="no_of_person">{{ $t('common.no_of_person') }}
                                        <span class="required">*</span></label>
                                    <input id="no_of_person" v-model="form.no_of_person" type="number"
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.has('no_of_person') }"
                                        name="no_of_person" :placeholder="$t('common.no_of_person_placeholder')" />
                                    <has-error :form="form" field="no_of_person" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="extra_bed_capacity">{{ $t('common.extra_bed_capacity') }}
                                        <span class="required">*</span></label>
                                    <input id="extra_bed_capacity" v-model="form.extra_bed_capacity" type="text"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('extra_bed_capacity') }" name="extra_bed_capacity" :placeholder="$t('common.extra_bed_capacity_placeholder')" />
                                    <has-error :form="form" field="extra_bed_capacity" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="no_of_child">{{ $t('common.no_of_child') }}
                                        <span class="required">*</span></label>
                                    <input id="no_of_child" v-model="form.no_of_child" type="number"
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.has('no_of_child') }"
                                        name="no_of_child" :placeholder="$t('common.no_of_child_placeholder')" />
                                    <has-error :form="form" field="no_of_child" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="room_rate">{{ $t('common.room_rate') }}
                                        <span class="required">*</span></label>
                                    <input id="room_rate" v-model="form.room_rate" type="number"
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.has('room_rate') }"
                                        name="room_rate" :placeholder="$t('common.room_rate_placeholder')" />
                                    <has-error :form="form" field="room_rate" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="extra_bed_rate">{{ $t('common.extra_bed_rate') }}
                                        <span class="required">*</span></label>
                                    <input id="extra_bed_rate" v-model="form.extra_bed_rate" type="number"
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.has('extra_bed_rate') }"
                                        name="extra_bed_rate" :placeholder="$t('common.extra_bed_rate_placeholder')" />
                                    <has-error :form="form" field="extra_bed_rate" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="per_person">{{ $t('common.per_person') }}
                                        <span class="required">*</span></label>
                                    <input id="per_person" v-model="form.per_person" type="number"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('per_person') }"
                                        name="per_person" :placeholder="$t('common.per_person_placeholder')" />
                                    <has-error :form="form" field="per_person" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="floor_id">{{ $t('sidebar.floor') }}
                                        <span class="required">*</span></label>
                                        <multiselect v-model="room_floor_id" :options="floorItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a floor" class="form-control" label="floorsName" track-by="floorsName" :class="{ 'is-invalid': form.errors.has('floor_id') }"></multiselect>
                                    <has-error :form="form" field="floor_id" />
                                </div>
    
                                <div class="form-group col-md-6">
                                    <label for="no_of_infant">{{ $t('common.no_of_infant') }}
                                        <span class="required">*</span></label>
                                    <input id="no_of_infant" v-model="form.no_of_infant" type="number"
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.has('no_of_infant') }"
                                        name="no_of_infant" :placeholder="$t('common.no_of_infact')" />
                                    <has-error :form="form" field="no_of_infant" />
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
</template>

<script>
import { mapGetters } from 'vuex'
import Form from 'vform'
import Multiselect from 'vue-multiselect'
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('rooms.roomAdd.create.page_title') }
    },
    components: {
        Multiselect
    },
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems', 'hotelItems', 'bedTypeItems', 'floorItems']),
    },
    created() {
        this.getFacilityItems()
        this.getHotelCategoryItems()
        this.getRoomCategory()
        this.getRoomBedType()
        this.getRoomFloor()
    },
    data: () => ({
        breadcrumbsCurrent: 'rooms.roomAdd.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'rooms.roomAdd.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'rooms.roomAdd.create.breadcrumbs_second',
                url: 'rooms.roomAdd',
            },
            {
                name: 'rooms.roomAdd.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            room_name: '',
            room_categorary: '',
            hotel_id: '',
            bed_type_id: '',
            roomdescription: '',
            no_of_person: '',
            extra_bed_capacity: '',
            no_of_child: '',
            facilityId: '',
            room_rate: '',
            extra_bed_rate: '',
            per_person: '',
            floor_id: '',
            no_of_infant: '',
        }),
        loading: true,
        room_category_id: '',
        room_facility_id: '',
        room_hotel_id: '',
        room_bed_type_id: '',
        room_floor_id: '',
    }),
    methods: {
        // save category
        async saveMealPlan() {

            this.form.room_categorary =  this.room_category_id.id
            this.form.facilityId =  this.room_facility_id.id
            this.form.hotel_id =  this.room_hotel_id.id
            this.form.bed_type_id = this.room_bed_type_id.id
            this.form.floor_id = this.room_floor_id.id

            await this.form
                .post(window.location.origin + '/api/room/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('rooms.roomAdd.create.success_msg'),
                    })
                    this.$router.push({ name: 'room' })
                })
                .catch((err) => {
                    let message = err.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },

        async getFacilityItems() {
            await this.$store.dispatch('operations/getFacilityData', {
                path: '/api/room/facility/list'
            })
        },

        async getHotelCategoryItems() {
            await this.$store.dispatch("operations/getHotelData", {
                path: "/api/hotel",
            });
        },

        async getRoomCategory() {
            await this.$store.dispatch("operations/getHotelCategoryData", {
                path: "/api/room/category",
            });
        },

        async getRoomBedType() {
            await this.$store.dispatch("operations/getBedTypeData", {
                path: "/api/bed/list",
            });
        },

        async getRoomFloor() {
            await this.$store.dispatch("operations/getFloorData", {
                path: "/api/floor/list",
            });
        },
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
</style>
