<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {{ $t('shop.shopAdd.edit.page_title') }}
            </h3>
        </div>
        <!-- /.card-header -->

        <!-- form start -->
        <form role="form" @submit.prevent="updateHotel" @keydown="form.onKeydown($event)">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="shop_name">{{ $t('shop.common.name') }}
                            <span class="required">*</span></label>
                        <input id="shop_name" v-model="form.shop_name" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('shop_name') }" name="shop_name"
                            :placeholder="$t('common.name_placeholder')" :readonly="readOnly || false" />
                        <has-error :form="form" field="shop_name" />
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
                        <label for="shop_phone">{{ $t('shop.common.hotel_phone') }}
                            <span class="required">*</span></label>
                        <input id="shop_phone" v-model="form.shop_phone" type="tel" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('shop_phone') }" name="shop_phone"
                            :placeholder="$t('shop.common.number_placeholder')" pattern="[0-9]{10}"
                            :readonly="readOnly || false" />
                        <has-error :form="form" field="shop_phone" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="shop_email">{{ $t('shop.common.hotel_email') }}
                            <span class="required">*</span></label>
                        <input id="shop_email" v-model="form.shop_email" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('shop_email') }" name="shop_email"
                            :placeholder="$t('shop.common.shop_email')" :readonly="readOnly || false" />
                        <has-error :form="form" field="shop_email" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="shop_prefix">{{ $t('shop.common.hotel_prefix') }}
                            <span class="required">*</span></label>
                        <input id="shop_prefix" v-model="form.shop_prefix" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('shop_prefix') }" name="shop_prefix"
                            :placeholder="$t('shop.common.hotel_prefix')" />
                        <has-error :form="form" field="shop_prefix" />
                    </div>
                  
                    <div class="form-group col-md-6">
                        <label for="contact_phone">{{ $t('shop.common.contact_phone') }}
                        </label>
                        <input id="contact_phone" v-model="form.contact_phone" type="tel" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('contact_phone') }" name="contact_phone"
                            :placeholder="$t('shop.common.contact_phone')" pattern="[0-9]{10}"
                            :readonly="readOnly || false" />
                        <has-error :form="form" field="contact_phone" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contact_name">{{ $t('shop.common.contact_name') }}
                        </label>
                        <input id="contact_name" v-model="form.contact_name" type="text" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('contact_name') }" name="contact_name"
                            :placeholder="$t('shop.common.contact_name')" :readonly="readOnly || false" />
                        <has-error :form="form" field="contact_name" />
                    </div>
                    <div class="form-group col-md-12">
                        <label for="shop_address">{{ $t('shop.common.hotel_address') }}
                            <span class="required">*</span> </label>
                        <textarea id="shop_address" v-model="form.shop_address" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('shop_address') }"
                            :placeholder="$t('shop.common.hotel_address')" :readonly="readOnly || false" />
                        <has-error :form="form" field="shop_address" />
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
                <!-- <button class="btn btn-secondary float-right" @submit.prevent.stop="updateHotel">
                    {{ $t('common.next') }} <i class="fas fa-long-arrow-alt-right" />
                </button> -->
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
                shop_name: '',
                shop_phone: '',
                shop_email: '',
                shop_prefix:'',
                shop_phone1: '',
                shop_address: '',
                pastImages: [],
                contact_phone: '',
                contact_name: '',
                shop_id: '',
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
            currentTab: 1,
            selectedImage: null,
            removedImages: [],
            deletedImages: [],
        }
    },
    created() {
        this.getHotelView()
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
                const basePath = "/images/shop/";
                return basePath + image;
            }
        },
        removeImage(index) {
            const removedImage = this.images[index];
            

            // If the removed image has a file (indicating it's a newly uploaded image), remove the file
            if (removedImage.file) {
                // Assuming the file property holds the File object
                const fileIndex = this.form.images.indexOf(removedImage.file);
                if (fileIndex !== -1) {
                    this.form.images.splice(fileIndex, 1);
                   
                }
            } else {
                // If it's an existing image, add it to the pastImages array for removal on form submit
                this.form.pastImages.push(removedImage.name);
              
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
            const { data } = await this.form.get( window.location.origin + '/api/shop/view/' + this.$route.params.slug )
           
            this.form.shop_id = data.data.id
            this.form.shop_name = data.data.shop_name
            this.form.shop_phone = data.data.shop_phone
            this.form.shop_email = data.data.shop_email
            this.form.shop_prefix = data.data.shop_prefix
            this.form.shop_phone1 = data.data.shop_phone1
          
            this.form.state = data.data.state
            this.form.city = data.data.city
            this.form.shop_address = data.data.shop_address
            this.form.contact_phone = data.data.contact_phone
            this.form.contact_name = data.data.contact_name
            
            const basePath = "/images/shop/";  
            
            this.images = JSON.parse(data.data?.images)?.map((image) => {
                return {
                    file: null,
                    previewUrl: `/images/shop/${image}`,
                    name: image
                }
            });

            if (!this.images) this.images = [];
            if ((!data.data.images || !data.data.images?.length) && data.data.image) {
                this.images.push({
                    file: null,
                    previewUrl: `/images/shop/${data.data.image}`,
                    name: data.data.image
                })
            }
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
               
                await this.form
                    .post(window.location.origin + '/api/shop/edit')
                    .then(() => {
                        toast.fire({
                            type: 'success',
                            title: this.$t('shop.shopAdd.edit.success_msg'),
                        })
                        this.$emit('currentTab')
                    })
                    .catch(() => {
                        toast.fire({ type: 'error', title: this.$t('common.error_msg') })
                    })
            }

        },

    }
}
</script>


<style scoped>


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
