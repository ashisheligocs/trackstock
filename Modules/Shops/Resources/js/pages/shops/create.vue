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
                            {{ $t('shop.shopAdd.create.form_title') }}
                        </h3>
                        

                        <button class="btn btn-secondary mt-2 mb-2"  @click="goBack"><i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}</button>

                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveFacility" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="shop_name">{{ $t('shop.common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="shop_name" v-model="form.shop_name" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('shop_name') }" name="shop_name"
                                        :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="shop_name" />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="hotel_address">{{ $t('shop.common.hotel_address') }} <span
                                            class="required">*</span></label>
                                    <textarea id="shop_address" v-model="form.shop_address" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('shop_address') }"
                                        :placeholder="$t('shop.common.hotel_address')" />
                                    <has-error :form="form" field="shop_address" />
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
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('shop.shopAdd.create.page_title') }
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
        breadcrumbsCurrent: 'shop.shopAdd.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'shop.shopAdd.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'shop.shopAdd.create.breadcrumbs_second',
                url: 'shop.category',
            },
            {
                name: 'shop.shopAdd.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            shop_name: '',
            shop_phone: '',
            shop_email: '',
            shop_phone1: '',
            images: [],
            shop_address: '',
            contact_phone: '',
            contact_name: '',
            state: '',
            city: '',
        }),
        loading: true,
        showModal: false,
        images: [],
        hotel_category_id: '',
    }),
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems']),
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

            await this.form
                .post(window.location.origin + '/api/shop/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('shop.shopAdd.create.success_msg'),
                    })
                    this.$router.push({ name: 'shops' })
                })
                .catch((err) => {
                    let message = err.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },

    },
}
</script>

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
