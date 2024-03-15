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
                            {{ $t('hotel.hotelAdd.create.form_title') }}
                        </h3>
                        <router-link :to="{ name: 'facility' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveFacility" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="hotel_name">{{ $t('hotel.common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="hotel_name" v-model="form.hotel_name" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('hotel_name') }" name="hotel_name"
                                        :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="hotel_name" />
                                </div>
                                <div v-if="hotelCategoryItems" class="form-group col-md-6">
                                    <label for="hotelcategory_id">{{ $t('hotel.common.hotelcategory') }}
                                        <span class="required">*</span></label>
                                    <multiselect v-model="hotel_category_id" :options="hotelCategoryItems"
                                        :show-labels="false" tag-placeholder="Add this as new tag"
                                        placeholder="Search a hotel category" class="form-control" label="category_name"
                                        track-by="category_name"
                                        :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                    <has-error :form="form" field="hotelcategory_id" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="state">{{
            $t("common.state")
        }}</label>
                                    <v-select class="flex-grow-1" v-model="form.state" :options="states" label="label"
                                        :class="{ 'is-invalid': form.errors.has('state') }" name="state"
                                        placeholder="Search State"></v-select>
                                    <has-error :form="form" field="state" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="state">{{
            $t("common.city")
        }}</label>
                                    <input id="city" v-model="form.city" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('city') }" name="city"
                                        :placeholder="$t('common.city_placeholder')" />
                                    <has-error :form="form" field="city" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="hotel_phone">{{ $t('hotel.common.hotel_phone') }}
                                        <span class="required">*</span></label>
                                    <input id="hotel_phone" v-model="form.hotel_phone" type="tel" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('hotel_phone') }" name="hotel_phone"
                                        :placeholder="$t('hotel.common.number_placeholder')" pattern="[0-9]{10}" />
                                    <has-error :form="form" field="hotel_phone" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="hotel_email">{{ $t('hotel.common.hotel_email') }}
                                        <span class="required">*</span></label>
                                    <input id="hotel_email" v-model="form.hotel_email" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('hotel_email') }" name="hotel_email"
                                        :placeholder="$t('hotel.common.hotel_email')" />
                                    <has-error :form="form" field="hotel_email" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="hotel_prefix">{{ $t('hotel.common.hotel_head') }}
                                        <span class="required">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">INV</span>
                                        </div>
                                        <input id="hotel_prefix" v-model="form.hotel_prefix" type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.has('hotel_prefix') }"
                                            name="hotel_prefix" :placeholder="$t('hotel.common.hotel_prefix')" />
                                        <has-error :form="form" field="hotel_prefix" />

                                    </div>
                                </div>
                                <!--                                <div class="form-group col-md-6">-->
                                <!--                                    <label for="hotel_phone1">{{ $t('hotel.common.hotel_phone1') }} </label>-->
                                <!--                                    <input id="hotel_phone1" v-model="form.hotel_phone1" type="tel" class="form-control"-->
                                <!--                                        :class="{ 'is-invalid': form.errors.has('hotel_phone1') }" name="hotel_phone1"-->
                                <!--                                        :placeholder="$t('hotel.common.number_placeholder')" pattern="[0-9]{10}" />-->
                                <!--                                    <has-error :form="form" field="hotel_phone1" />-->
                                <!--                                </div>-->
                                <!--                                <div class="form-group col-md-6">-->
                                <!--                                    <label for="total_no_of_rooms">{{ $t('hotel.common.total_no_of_rooms') }}-->
                                <!--                                        <span class="required">*</span></label>-->
                                <!--                                    <input id="total_no_of_rooms" v-model="form.total_no_of_rooms" type="text"-->
                                <!--                                        class="form-control" :class="{ 'is-invalid': form.errors.has('total_no_of_rooms') }"-->
                                <!--                                        name="total_no_of_rooms" :placeholder="$t('hotel.common.total_no_of_rooms')" />-->
                                <!--                                    <has-error :form="form" field="total_no_of_rooms" />-->
                                <!--                                </div>-->
                                <!--                                <div class="form-group col-md-6">-->
                                <!--                                    <label for="no_of_floor">{{ $t('hotel.common.no_of_floor') }}-->
                                <!--                                        <span class="required">*</span></label>-->
                                <!--                                    <input id="no_of_floor" v-model="form.no_of_floor" type="number" class="form-control"-->
                                <!--                                        :class="{ 'is-invalid': form.errors.has('no_of_floor') }" name="no_of_floor" min="1"-->
                                <!--                                        :placeholder="$t('hotel.common.no_of_floor')" />-->
                                <!--                                    <has-error :form="form" field="no_of_floor" />-->
                                <!--                                </div>-->
                                <!-- <div class="form-group col-md-6">
                                    <label for="hotel_facility_ids">{{ $t('hotel.common.hotel_facility_ids') }}</label>
                                    <multiselect v-model="hotel_facility_id" :options="facilityItems" :multiple="true"
                                        :close-on-select="false" :clear-on-select="false" :preserve-search="true"
                                        tag-placeholder="Add this as new tag" placeholder="Search a hotel facility"
                                        label="facility_title" track-by="facility_title" :preselect-first="false" :class="{ 'is-invalid': form.errors.has('hotel_facility_ids') }" class="form-control"></multiselect>
                                    <has-error :form="form" field="hotel_facility_ids" />
                                </div> -->
                                <!-- <div class="form-group col-md-6">
                                    <label for="contact_phone">{{ $t('hotel.common.contact_phone') }}
                                    </label>
                                    <input id="contact_phone" v-model="form.contact_phone" type="tel"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('contact_phone') }"
                                        name="contact_phone" :placeholder="$t('hotel.common.contact_phone')"
                                        pattern="[0-9]{10}" />
                                    <has-error :form="form" field="contact_phone" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="contact_name">{{ $t('hotel.common.contact_name') }}
                                    </label>
                                    <input id="contact_name" v-model="form.contact_name" type="text"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('contact_name') }"
                                        name="contact_name" :placeholder="$t('hotel.common.contact_name')" />
                                    <has-error :form="form" field="contact_name" />
                                </div> -->
                                <div class="form-group col-md-12">
                                    <label for="hotel_address">{{ $t('hotel.common.hotel_address') }} <span
                                            class="required">*</span></label>
                                    <textarea id="hotel_address" v-model="form.hotel_address" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('hotel_address') }"
                                        :placeholder="$t('hotel.common.hotel_address')" />
                                    <has-error :form="form" field="hotel_address" />
                                </div>
                                <div class="form-group col-md-12">
                                  
                                    <label class="image_label" for="file-upload">  <i class="fa fa-upload" aria-hidden="true"></i> Upload Hotel Image</label>
                                    <input type="file" id="file-upload" multiple @change="handleFileUpload">

                                    <div class="image-container">
                                        <div v-for="(image, index) in images" :key="index" class="image-item">
                                            <img :src="image.previewUrl" alt="Preview" class="profile-pic"
                                                @click="showImage(index)">
                                            <button class="remove-button" @click="removeImage(index)">X</button>
                                        </div>
                                    </div>
                                    <div v-if="showModal" class="modal" @click.self="closeModal">
                                        <div class="modal-content">
                                            <span class="close" @click="closeModal">&times;</span>
                                            <img :src="selectedImage.previewUrl" alt="Preview" class="modal-image">
                                        </div>
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
</template>

<script>
import { mapGetters } from 'vuex'
import Form from 'vform'
import Multiselect from 'vue-multiselect'
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('hotel.hotelAdd.create.page_title') }
    },
    components: {
        Multiselect
    },
    data: () => ({
        states: [
            { value: "AN", label: "Andaman and Nicobar Islands" },
            { value: "AP", label: "Andhra Pradesh" },
            { value: "AR", label: "Arunachal Pradesh" },
            { value: "AS", label: "Assam" },
            { value: "BR", label: "Bihar" },
            { value: "CH", label: "Chandigarh" },
            { value: "CT", label: "Chhattisgarh" },
            { value: "DN", label: "Dadra and Nagar Haveli" },
            { value: "DD", label: "Daman and Diu" },
            { value: "DL", label: "Delhi" },
            { value: "GA", label: "Goa" },
            { value: "GJ", label: "Gujarat" },
            { value: "HR", label: "Haryana" },
            { value: "HP", label: "Himachal Pradesh" },
            { value: "JK", label: "Jammu and Kashmir" },
            { value: "JH", label: "Jharkhand" },
            { value: "KA", label: "Karnataka" },
            { value: "KL", label: "Kerala" },
            { value: "LA", label: "Ladakh" },
            { value: "LD", label: "Lakshadweep" },
            { value: "MP", label: "Madhya Pradesh" },
            { value: "MH", label: "Maharashtra" },
            { value: "MN", label: "Manipur" },
            { value: "ML", label: "Meghalaya" },
            { value: "MZ", label: "Mizoram" },
            { value: "NL", label: "Nagaland" },
            { value: "OR", label: "Odisha" },
            { value: "PY", label: "Puducherry" },
            { value: "PB", label: "Punjab" },
            { value: "RJ", label: "Rajasthan" },
            { value: "SK", label: "Sikkim" },
            { value: "TN", label: "Tamil Nadu" },
            { value: "TG", label: "Telangana" },
            { value: "TR", label: "Tripura" },
            { value: "UP", label: "Uttar Pradesh" },
            { value: "UT", label: "Uttarakhand" },
            { value: "WB", label: "West Bengal" },
        ],
        value: null,
        breadcrumbsCurrent: 'hotel.hotelAdd.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'hotel.hotelAdd.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'hotel.hotelAdd.create.breadcrumbs_second',
                url: 'hotel.category',
            },
            {
                name: 'hotel.hotelAdd.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            hotel_name: '',
            hotelcategory_id: '',
            hotel_phone: '',
            hotel_email: '',
            hotel_phone1: '',
            total_no_of_rooms: '',
            no_of_floor: '',
            images: [],
            // hotel_facility_ids: '',
            hotel_address: '',
            contact_phone: '',
            contact_name: '',
            state: '',
            city: '',
        }),
        loading: true,
        // hotel_facility_id: '',
        showModal: false,
        images: [],
        hotel_category_id: '',
        hotel_facility_array: [],
    }),
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems']),
    },
    created() {
        this.getSubCatgories()
        // this.getFacilityItems()
        this.getHotelCategoryItems()
    },
    methods: {
        handleFileUpload(event) {
            const files = event.target.files;
            let error = false;
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                if (
                    files[i].size < 2111775 &&
                    (files[i].type === "image/jpeg" ||
                        files[i].type === "image/png"
                        //|| files[i].type === "image/gif"
                        )

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
        // get all expense categories
        async getSubCatgories() {
            await this.$store.dispatch('operations/allData', {
                path: '/api/all-expense-sub-categories',
            })
        },

        removeImage(index) {
            this.images.splice(index, 1);
        },
        showImage(index) {
            this.selectedImage = this.images[index];
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.selectedImage = null;
        },
        // save category
        async saveFacility() {
            this.form.images = [];
            this.form.images = this.images && this.images.length ? _.map(this.images, 'file') : [];
            this.form.hotelcategory_id = this.hotel_category_id.id
            // if (this.hotel_facility_id) {
            //     this.hotel_facility_id.forEach((el) => {
            //         if (!this.hotel_facility_array.includes(el.id)) {
            //             this.hotel_facility_array.push(el.id)
            //         }
            //     });
            // }
            // this.form.hotel_facility_ids = this.hotel_facility_array.toString()
            await this.form
                .post(window.location.origin + '/api/hotel/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('hotel.hotelAdd.create.success_msg'),
                    })
                    this.$router.push({ name: 'hotel' })
                })
                .catch((err) => {
                    let message = err.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },
        // async getFacilityItems() {
        //     await this.$store.dispatch('operations/getFacilityData', {
        //         path: '/api/hotel/facility'
        //     })
        // },

        async getHotelCategoryItems() {
            await this.$store.dispatch("operations/getHotelCategoryData", {
                path: "/api/hotel/category",
            });
        },
    },
}
</script>

<style src="../../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
input[type="file"] {
    display: none;
}

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

.image-container {
    display: flex;
    flex-wrap: wrap;
    margin-top: 20px;
}

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

.profile-pic {
    width: 130px;
    height: 120px;
    margin-left: 20px;
}

/* .image-item {
    position: relative;
    margin-top: 20px;
} */

/* .remove-button {
    position: absolute;
    top: 0px;
    right: 2px;
    border: none;
    background: none;
} */
</style>
