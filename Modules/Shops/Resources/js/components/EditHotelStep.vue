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
                        <multiselect :disabled="readOnly || false" v-model="hotel_category_id" :options="hotelCategoryItems"
                            :show-labels="false" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"
                            class="form-control" tag-placeholder="Add this as new tag" placeholder="Search a hotel category"
                            label="category_name" track-by="id"></multiselect>
                        <has-error :form="form" field="hotelcategory_id" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="state">{{
                            $t("common.state")
                        }}</label> 
                        <v-select
                            class="flex-grow-1"
                            v-model="form.state"
                            :options="states"
                            label="label"
                            :class="{ 'is-invalid': form.errors.has('state') }"
                            name="state"
                            @input="handleChange"
                            placeholder="Search State"
                        ></v-select> 
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
                            :placeholder="$t('hotel.common.number_placeholder')" pattern="[0-9]{10}"
                            :readonly="readOnly || false" />
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
                        <label for="hotel_prefix">{{ $t('hotel.common.hotel_prefix') }}
                            <span class="required">*</span></label>
                        <input id="hotel_prefix" v-model="form.hotel_prefix" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('hotel_prefix') }" name="hotel_prefix"
                            :placeholder="$t('hotel.common.hotel_prefix')" />
                        <has-error :form="form" field="hotel_prefix" />
                    </div>
                    <!--                    <div class="form-group col-md-6">-->
                    <!--                        <label for="hotel_phone1">{{ $t('hotel.common.hotel_phone1') }}</label>-->
                    <!--                        <input id="hotel_phone1" v-model="form.hotel_phone1" type="tel" class="form-control"-->
                    <!--                            :class="{ 'is-invalid': form.errors.has('hotel_phone1') }" name="hotel_phone1"-->
                    <!--                            :placeholder="$t('hotel.common.number_placeholder')" pattern="[0-9]{10}" :readonly="readOnly || false" />-->
                    <!--                        <has-error :form="form" field="hotel_phone1" />-->
                    <!--                    </div>-->
                    <!--                    <div class="form-group col-md-6">-->
                    <!--                        <label for="total_no_of_rooms">{{ $t('hotel.common.total_no_of_rooms') }}-->
                    <!--                            <span class="required">*</span></label>-->
                    <!--                        <input id="total_no_of_rooms" v-model="form.total_no_of_rooms" type="text"-->
                    <!--                            class="form-control" :class="{ 'is-invalid': form.errors.has('total_no_of_rooms') }"-->
                    <!--                            name="total_no_of_rooms" :placeholder="$t('hotel.common.total_no_of_rooms')" :readonly="readOnly || false" />-->
                    <!--                        <has-error :form="form" field="total_no_of_rooms" />-->
                    <!--                    </div>-->
                    <!--                    <div class="form-group col-md-6">-->
                    <!--                        <label for="no_of_floor">{{ $t('hotel.common.no_of_floor') }}-->
                    <!--                            <span class="required">*</span></label>-->
                    <!--                        <input id="no_of_floor" v-model="form.no_of_floor" type="number" class="form-control"-->
                    <!--                            :class="{ 'is-invalid': form.errors.has('no_of_floor') }" min="0" name="no_of_floor"-->
                    <!--                            :placeholder="$t('hotel.common.no_of_floor')" :readonly="readOnly || false" />-->
                    <!--                        <has-error :form="form" field="no_of_floor" />-->
                    <!--                    </div>-->
                    <!-- <div v-if="facilityItems" class="form-group col-md-6">
                        <label for="hotel_facility_ids">{{ $t('hotel.common.hotel_facility_ids') }}</label>
                        <multiselect v-model="hotel_facility_id" :options="facilityItems" :multiple="true"
                            :close-on-select="false" :clear-on-select="false" :preserve-search="true" class="form-control" tag-placeholder="Add this as new tag" placeholder="Search a hotel facility" label="facility_title" track-by="facility_title" :preselect-first="false" :class="{ 'is-invalid': form.errors.has('hotel_facility_ids') }"></multiselect>
                        <has-error :form="form" field="hotel_facility_ids" />
                    </div> -->
                    <div class="form-group col-md-6">
                        <label for="contact_phone">{{ $t('hotel.common.contact_phone') }}
                        </label>
                        <input id="contact_phone" v-model="form.contact_phone" type="tel" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('contact_phone') }" name="contact_phone"
                            :placeholder="$t('hotel.common.contact_phone')" pattern="[0-9]{10}"
                            :readonly="readOnly || false" />
                        <has-error :form="form" field="contact_phone" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contact_name">{{ $t('hotel.common.contact_name') }}
                        </label>
                        <input id="contact_name" v-model="form.contact_name" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('contact_name') }" name="contact_name"
                            :placeholder="$t('hotel.common.contact_name')" :readonly="readOnly || false" />
                        <has-error :form="form" field="contact_name" />
                    </div>
                    <div class="form-group col-md-12">
                        <label for="hotel_address">{{ $t('hotel.common.hotel_address') }}
                            <span class="required">*</span> </label>
                        <textarea id="hotel_address" v-model="form.hotel_address" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('hotel_address') }"
                            :placeholder="$t('hotel.common.hotel_address')" :readonly="readOnly || false" />
                        <has-error :form="form" field="hotel_address" />
                    </div>
                    <div class="form-group">
                        <label class="image_label" for="file-upload">Choose Image</label>
                        <input type="file" id="file-upload" multiple @change="handleFileUpload">
                        <div class="image-container" v-if="images && images.length > 0">
                                        <div v-for="(image, index) in images" :key="index" class="image-item">
                                            <img :src="image.previewUrl" alt="Preview" class="profile-pic"
                                                @click="showImage(index)">
                                            <button class="remove-button" @click.stop="removeImage(index)">X</button>
                                        </div>
                                    </div>
                        <div v-if="showModal" class="modal" @click.self="closeModal">
                            <div class="modal-content">
                                <span class="close" @click="closeModal">&times;</span>
                                <img :src="selectedImage.previewUrl" alt="modalImage" class="modal-image">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <v-button :loading="form.busy" class="btn btn-primary">
                    <i class="fas fa-edit" /> {{ $t('common.save') }}
                </v-button>
                <button class="btn btn-secondary float-right" @submit.prevent.stop="updateHotel">
                    {{ $t('common.next') }} <i class="fas fa-long-arrow-alt-right" />
                </button>
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
    props: ["readOnly"],
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems']),
    },
    data() {
        return {
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
            editId: '',
            form: new Form({
                hotel_name: '',
                hotelcategory_id: '',
                hotel_phone: '',
                hotel_email: '',
                hotel_prefix:'',
                hotel_phone1: '',
                total_no_of_rooms: '',
                no_of_floor: '',
                // hotel_facility_ids: '',
                hotel_address: '',
                pastImages: [],
                contact_phone: '',
                contact_name: '',
                hotel_id: '',
                images: [],
                image_path: [],
                deletedImages: [],
                state: '',
                city: '',
            }),
            images: [], 
            url: null,
            loading: true,
            showModal: false,
            // hotel_facility_id: '',
            hotel_category_id: '',
            // hotel_facility_array: [],
            currentTab: 1,
            selectedImage: null,
            removedImages: [],
            deletedImages: [],
        }
    },
    created() {
        this.getHotelView()
        this.getSubCatgories()
        this.getHotelCategoryItems()
        this.form.state = "AP";
        // this.getFacilityItems()
    },
    methods: {
        handleChange(){
            this.form.state = this.form.state.label;
        },
        getImagePath(image) {
            // Check if 'image' is an object or a string
            if (typeof image === 'object') {
                // For newly uploaded images
                return URL.createObjectURL(image.file);
            } else {
                // For existing images
                const basePath = "/images/hotel/";
                return basePath + image;
            }
        },
        removeImage(index) {
            const removedImage = this.images[index];
            console.log(removedImage);

            // If the removed image has a file (indicating it's a newly uploaded image), remove the file
            if (removedImage.file) {
                // Assuming the file property holds the File object
                const fileIndex = this.form.images.indexOf(removedImage.file);
                if (fileIndex !== -1) {
                    this.form.images.splice(fileIndex, 1);
                    console.log("image deleted..")
                }
            } else {
                // If it's an existing image, add it to the pastImages array for removal on form submit
                this.form.pastImages.push(removedImage.name);
                console.log("image not deleted..")
                // Mark the image for deletion on the server
                this.form.deletedImages.push(removedImage.name);
            }

            // Remove the image from the images array
            this.images.splice(index, 1);
            this.$forceUpdate();
        },


        handleFileUpload(event) {
            const files = event.target.files;
            let error = false;

            // Clear existing images array
            this.images = [];
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();

                if (
                    //files[i].size < 2111775 &&
                    (files[i].type === "image/jpeg" ||
                        files[i].type === "image/png" ||
                        files[i].type === "image/gif")
                ) {
                    reader.onload = (e) => {
                        // Add the new image to the images array with the full path
                        this.images.push({
                            file: files[i],
                            previewUrl: e.target.result,
                            name: files[i].name
                        });
                    };
                    console.log(files[i]);
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


        showImage(index) {
            this.selectedImage = this.images[index];
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.selectedImage = null;
        },
        async getHotelView() {
            const { data } = await this.form.get( window.location.origin + '/api/hotel/view/' + this.$route.params.slug )
            console.log('Received data:', data);
 
            this.form.hotel_id = data.data.id
            this.form.hotel_name = data.data.hotel_name
            this.hotel_category_id = data.data.category
            this.form.hotel_phone = data.data.hotel_phone
            this.form.hotel_email = data.data.hotel_email
            this.form.hotel_prefix = data.data.hotel_prefix
            this.form.hotel_phone1 = data.data.hotel_phone1
            this.form.total_no_of_rooms = data.data.total_no_of_rooms
            this.form.no_of_floor = data.data.no_of_floor
            this.form.state = data.data.state
            this.form.city = data.data.city
            // data.data.facilities.forEach((el) => {
            //     this.hotel_facility_array.push(el)
            // })
            // this.hotel_facility_id = this.hotel_facility_array
            this.form.hotel_address = data.data.hotel_address
            this.form.contact_phone = data.data.contact_phone
            this.form.contact_name = data.data.contact_name
            console.log( data.data?.images)
            const basePath = "/images/hotel/";  
            this.images = data.data?.images?.map((image) => {
                return {
                    file: null,
                    previewUrl: `/images/hotel/${image}`,
                    name: image
                }
            })
            if (!this.images) this.images = [];
            if ((!data.data.images || !data.data.images?.length) && data.data.image) {
                this.images.push({
                    file: null,
                    previewUrl: `/images/hotel/${data.data.image}`,
                    name: data.data.image
                })
            }
        },

        async getSubCatgories() {
            await this.$store.dispatch('operations/allData', {
                path: '/api/all-expense-sub-categories',
            })
        },

        // save category
        async updateHotel() {
            this.form.images = [];
            this.form.pastImages = [];

            if (this.images && this.images.length) {
                this.images.forEach((image) => {
                    if (image.file) this.form.images.push(image.file);
                    else this.form.pastImages.push(image.name)
                })
            }

            this.form.deletedImages = this.deletedImages;

            if (this.readOnly) {
                this.$emit('currentTab')
            } else {
                if (this.hotel_category_id) {
                    this.form.hotelcategory_id = this.hotel_category_id.id
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
</style>
