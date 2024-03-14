<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {{ $t('hotel.hotelAdd.edit.form_title') }}
            </h3>
            <button class="btn btn-secondary float-right" @click="updateHotel">
                {{ $t('common.next') }} <i class="fas fa-long-arrow-alt-right" />
            </button>
        </div>
        <!-- /.card-header -->

        <!-- form start -->
        <form role="form" @submit.prevent="updateHotel" @keydown="form.onKeydown($event)">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="hotel_name">{{ $t('hotel.common.name') }}
                            <span class="required">*</span></label>
                        <input id="hotel_name" v-model="form.hotel_name" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('hotel_name') }" name="hotel_name"
                            :placeholder="$t('common.name_placeholder')" :readonly="readOnly || false" />
                        <has-error :form="form" field="hotel_name" />
                    </div>
                    <div v-if="hotelCategoryItems" class="form-group col-md-6">
                        <label for="hotelcategory_id">{{ $t('hotel.common.hotelcategory') }}
                            <span class="required">*</span></label>
                            <multiselect :disabled="readOnly || false" v-model="hotel_category_id" :options="hotelCategoryItems" :show-labels="false" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }" class="form-control" tag-placeholder="Add this as new tag" placeholder="Search a hotel category" label="category_name" track-by="id"></multiselect>
                        <has-error :form="form" field="hotelcategory_id" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="hotel_phone">{{ $t('hotel.common.hotel_phone') }}
                            <span class="required">*</span></label>
                        <input id="hotel_phone" v-model="form.hotel_phone" type="tel" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('hotel_phone') }" name="hotel_phone"
                            :placeholder="$t('hotel.common.number_placeholder')" pattern="[0-9]{10}" :readonly="readOnly || false"  />
                        <has-error :form="form" field="hotel_phone" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="hotel_email">{{ $t('hotel.common.hotel_email') }}
                            <span class="required">*</span></label>
                        <input id="hotel_email" v-model="form.hotel_email" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('hotel_email') }" name="hotel_email"
                            :placeholder="$t('hotel.common.hotel_email')" :readonly="readOnly || false" />
                        <has-error :form="form" field="hotel_email" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="hotel_phone1">{{ $t('hotel.common.hotel_phone1') }}</label>
                        <input id="hotel_phone1" v-model="form.hotel_phone1" type="tel" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('hotel_phone1') }" name="hotel_phone1"
                            :placeholder="$t('hotel.common.number_placeholder')" pattern="[0-9]{10}" :readonly="readOnly || false" />
                        <has-error :form="form" field="hotel_phone1" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="total_no_of_rooms">{{ $t('hotel.common.total_no_of_rooms') }}
                            <span class="required">*</span></label>
                        <input id="total_no_of_rooms" v-model="form.total_no_of_rooms" type="text"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('total_no_of_rooms') }"
                            name="total_no_of_rooms" :placeholder="$t('hotel.common.total_no_of_rooms')" :readonly="readOnly || false" />
                        <has-error :form="form" field="total_no_of_rooms" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="no_of_floor">{{ $t('hotel.common.no_of_floor') }}
                            <span class="required">*</span></label>
                        <input id="no_of_floor" v-model="form.no_of_floor" type="number" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('no_of_floor') }" min="0" name="no_of_floor"
                            :placeholder="$t('hotel.common.no_of_floor')" :readonly="readOnly || false" />
                        <has-error :form="form" field="no_of_floor" />
                    </div>
                    <!-- <div v-if="facilityItems" class="form-group col-md-6">
                        <label for="hotel_facility_ids">{{ $t('hotel.common.hotel_facility_ids') }}</label>
                        <multiselect v-model="hotel_facility_id" :options="facilityItems" :multiple="true"
                            :close-on-select="false" :clear-on-select="false" :preserve-search="true" class="form-control" tag-placeholder="Add this as new tag" placeholder="Search a hotel facility" label="facility_title" track-by="facility_title" :preselect-first="false" :class="{ 'is-invalid': form.errors.has('hotel_facility_ids') }"></multiselect>
                        <has-error :form="form" field="hotel_facility_ids" />
                    </div> -->
                    <div class="form-group col-md-12">
                        <label for="hotel_address">{{ $t('hotel.common.hotel_address') }}
                            <span class="required">*</span> </label>
                        <textarea id="hotel_address" v-model="form.hotel_address" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('hotel_address') }"
                            :placeholder="$t('hotel.common.hotel_address')" :readonly="readOnly || false" />
                        <has-error :form="form" field="hotel_address" />
                    </div>
                    <hr>
                    <div class="form-group col-md-6">
                        <label for="contact_phone">{{ $t('hotel.common.contact_phone') }}
                            <span class="required">*</span></label>
                        <input id="contact_phone" v-model="form.contact_phone" type="tel"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('contact_phone') }"
                            name="contact_phone" :placeholder="$t('hotel.common.contact_phone')" pattern="[0-9]{10}" :readonly="readOnly || false" />
                        <has-error :form="form" field="contact_phone" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contact_name">{{ $t('hotel.common.contact_name') }}
                            <span class="required">*</span></label>
                        <input id="contact_name" v-model="form.contact_name" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('contact_name') }" name="contact_name"
                            :placeholder="$t('hotel.common.contact_name')" :readonly="readOnly || false" />
                        <has-error :form="form" field="contact_name" />
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
</template>
<script>
import { mapGetters } from 'vuex'
import Form from 'vform'
import Multiselect from 'vue-multiselect'

export default {
    components: {
        Multiselect,
    },
    props:["readOnly"],
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems']),
    },
    data() {
        return {
            editId: '',
            form: new Form({
                hotel_name: '',
                hotelcategory_id: '',
                hotel_phone: '',
                hotel_email: '',
                hotel_phone1: '',
                total_no_of_rooms: '',
                no_of_floor: '',
                // hotel_facility_ids: '',
                hotel_address: '',
                contact_phone: '',
                contact_name: '',
                hotel_id: '',
            }),
            loading: true,
            // hotel_facility_id: '',
            hotel_category_id: '',
            // hotel_facility_array: [],
            currentTab: 1,
        }
    },
    created() {
        this.getHotelView()
        this.getSubCatgories()
        this.getHotelCategoryItems()
        // this.getFacilityItems()
    },
    methods: {
        async getHotelView() {
            const { data } = await this.form.get(
                window.location.origin +
                '/api/hotel/view/' +
                this.$route.params.slug
            )
            this.form.hotel_id = data.data.id
            this.form.hotel_name = data.data.hotel_name
            this.hotel_category_id = data.data.category
            this.form.hotel_phone = data.data.hotel_phone
            this.form.hotel_email = data.data.hotel_email
            this.form.hotel_phone1 = data.data.hotel_phone1
            this.form.total_no_of_rooms = data.data.total_no_of_rooms
            this.form.no_of_floor = data.data.no_of_floor
            // data.data.facilities.forEach((el) => {
            //     this.hotel_facility_array.push(el)
            // })
            // this.hotel_facility_id = this.hotel_facility_array
            this.form.hotel_address = data.data.hotel_address
            this.form.contact_phone = data.data.contact_phone
            this.form.contact_name = data.data.contact_name
        },

        async getSubCatgories() {
                await this.$store.dispatch('operations/allData', {
                    path: '/api/all-expense-sub-categories',
                })
            },

        // save category
        async updateHotel() {
            if(this.readOnly){
                this.$emit('currentTab')
            }
            else{
                if(this.hotel_category_id) {
                    this.form.hotelcategory_id =  this.hotel_category_id.id
                }
                // this.hotel_facility_array = []
                // if(this.hotel_facility_id) {
                //     this.hotel_facility_id.forEach((el) => {
                //         this.hotel_facility_array.push(el.id)
                //     });
                //     this.form.hotel_facility_ids = this.hotel_facility_array.toString()
                // }   
                await this.form
                    .post(window.location.origin + '/api/hotel/edit')
                    .then(() => {
                        toast.fire({
                            type: 'success',
                            title: this.$t('hotel.hotelAdd.edit.success_msg'),
                        })
                        this.$emit('currentTab')
                    })
                    .catch(() => {
                        toast.fire({ type: 'error', title: this.$t('common.error_msg') })
                    })
                }
            
        },

        async getHotelCategoryItems() {
            await this.$store.dispatch("operations/getHotelCategoryData", {
                path: "/api/hotel/category",
            });
        },

        // async getFacilityItems() {
        //     await this.$store.dispatch('operations/getFacilityData', {
        //         path: '/api/hotel/facility'
        //     })
        // },
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

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