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
                            {{ $t('restaurant_item.edit.form_title') }}
                        </h3>
                        <router-link :to="{ name: 'items' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="updateMealPlan" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="category_id">{{ $t('common.food_category') }}
                                        <span class="required">*</span></label>
                                    <multiselect v-model="food_category_id" :options="hotelItems" :show-labels="false" tag-placeholder="Add this as new tag" placeholder="Search a Food category" class="form-control" label="category_name" track-by="category_name" :class="{ 'is-invalid': form.errors.has('hotelcategory_id') }"></multiselect>
                                    <has-error :form="form" field="category_id" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="item_name">{{ $t('common.item_name') }}
                                        <span class="required">*</span></label>
                                    <input id="item_name" v-model="form.item_name" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('item_name') }"
                                        name="item_name" :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="item_name" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="description">{{ $t('common.description') }}
                                       </label>
                                    <input id="description" v-model="form.description" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('description') }"
                                        name="description" :placeholder="$t('common.description_placeholder')" />
                                    <has-error :form="form" field="description" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="notes">{{ $t('common.note') }}
                                       </label>
                                    <input id="notes" v-model="form.notes" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('notes') }"
                                        name="notes" :placeholder="$t('common.note_placeholder')" />
                                    <has-error :form="form" field="notes" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="image">{{ $t('common.image') }}
                                        <span class="required">*</span></label>
                                    <div class="custom-file">
                                        <input id="image" type="file" class="custom-file-input" name="image"
                                        :class="{ 'is-invalid': form.errors.has('image') }" @change="onFileChange" />
                                        <label class="custom-file-label" for="image">{{
                                            $t('common.choose_file')
                                        }}</label>
                                    </div>
                                    <has-error :form="form" field="image" />
                                    <div class="bg-light mt-4 w-25">
                                        <img v-if="url" :src="url" srcset="" class="img-fluid" :alt="$t('common.image_alt')" />
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="image">{{ $t('common.status') }}
                                        <span class="required">*</span></label>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                            <input id="active" v-model="form.status" type="radio" class="form-control w-25" value="1" :class="{ 'is-invalid': form.errors.has('status') }"
                                            name="status" :placeholder="$t('common.name_placeholder')" />
                                            <label for="active" class="ml-2 mb-0">{{ $t('common.active') }}</label>
                                            <has-error :form="form" field="status" />
                                        </div>

                                        <div class="d-flex align-items-center ml-4">
                                            <input id="inActive" v-model="form.status" type="radio" class="form-control w-25" value="0" :class="{ 'is-invalid': form.errors.has('status') }"
                                            name="status" :placeholder="$t('common.name_placeholder')" />
                                            <label for="inActive" class="ml-2 mb-0">{{ $t('common.in_active') }}</label>
                                            <has-error :form="form" field="status" />
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
import Multiselect from 'vue-multiselect'
import Form from 'vform'
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('restaurant_item.edit.page_title') }
    },
    computed: {
        ...mapGetters('operations', ['items', "hotelItems"]),
    },
    components:{
        Multiselect
    },
    data: () => ({
        breadcrumbsCurrent: 'restaurant_item.edit.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'restaurant_item.edit.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'restaurant_item.edit.breadcrumbs_second',
                url: 'items',
            },
            {
                name: 'restaurant_item.edit.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            category_id: '',
            item_name: '',
            item_image: '',
            description: '',
            notes: '',
            status: '',
            cat_id: '',
            url: '',
        }),
        loading: true,
        food_category_id: '',
        itemEditId: '',
        url: ''
    }),

    created() {
        this.getFoodItems()
        this.getFoodCategoryData()
    },
    methods: {
        onFileChange(e) {
            const file = e.target.files[0]
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                file.type === 'image/png' ||
                file.type === 'image/gif')
            ) {
                this.form.item_image = file
                this.url = URL.createObjectURL(file)
            } else {
                Swal.fire(
                    this.$t('common.error'),
                    this.$t('common.image_error'),
                    'error'
                )
            }
        },

        async getFoodCategoryData() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/food/category/list'
            })
        },

        // get category
        async getFoodItems() {
            this.itemEditId = this.$route.params.slug
            const { data } = await this.form.get(window.location.origin + '/api/food/item/view/' + this.itemEditId)  
            this.form.item_name = data.data.item_name
            this.form.item_image = data.data.item_image
            this.form.description = data.data.description
            this.form.notes = data.data.notes
            this.form.status = data.data.status
            this.food_category_id = data.data.food_category
            this.form.cat_id = data.data.id
            this.form.url = data.data.item_image
            this.url = this.form?.url?.replace('storage/', '/storage/');
            // this.url = this.form.url
            // console.log(this.url);
        },
        // update category
        async updateMealPlan() {
            if(this.food_category_id) {
                this.form.category_id =  this.food_category_id.id
            }
            await this.form
                .post(
                    window.location.origin +
                    '/api/food/item/update'
                )
                .then((response) => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('restaurant_item.edit.success_msg'),
                    })
                    this.$router.push({ name: 'items' })
                })
                .catch(() => {
                    toast.fire({
                        type: 'error',
                        title: this.$t('common.error_msg'),
                    })
                })
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