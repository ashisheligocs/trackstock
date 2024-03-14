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
                            {{ $t('food_category.create.form_title') }}
                        </h3>
                        <router-link :to="{ name: 'food-category' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="updateMealPlan" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="category_name">{{ $t('common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="category_name" v-model="form.category_name" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('category_name') }"
                                        name="category_name" :placeholder="$t('common.name_placeholder')" />
                                    <input id="id" v-model="form.id" type="hidden" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('id') }" name="id"
                                        :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="category_name" />
                                </div>
<!--                                <div class="form-group col-md-6">-->
<!--                                    <label for="image">{{ $t('common.image') }}</label>-->
<!--                                    <div class="custom-file">-->
<!--                                        <input id="image" type="file" class="custom-file-input" name="image"-->
<!--                                        :class="{ 'is-invalid': form.errors.has('image') }" @change="onFileChange" />-->
<!--                                        <label class="custom-file-label" for="image">{{-->
<!--                                            $t('common.choose_file')-->
<!--                                        }}</label>-->
<!--                                    </div>-->
<!--                                    <has-error :form="form" field="image" />-->
<!--                                    <div class="bg-light mt-4 w-25">-->
<!--                                        <img v-if="url" :src="url" class="img-fluid" :alt="$t('common.image_alt')" />-->
<!--                                    </div>-->
<!--                                </div>-->
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
import Form from 'vform'
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('food_category.edit.page_title') }
    },
    data: () => ({
        breadcrumbsCurrent: 'food_category.edit.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'food_category.edit.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'food_category.edit.breadcrumbs_second',
                url: 'food-category',
            },
            {
                name: 'food_category.edit.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            image: '',
            category_name: '',
            cat_id: '',
        }),
        loading: true,
        url: ''
    }),

    mounted() {
        this.getMealPlan()
    },
    methods: {
        onFileChange(e) {
            const file = e.target.files[0]
            // const reader = new FileReader()
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                file.type === 'image/png' ||
                file.type === 'image/gif')
            ) {
                this.form.image = file
                this.url = URL.createObjectURL(file)
            } else {
                Swal.fire(
                    this.$t('common.error'),
                    this.$t('common.image_error'),
                    'error'
                )
            }
        },

        // get category
        async getMealPlan() {
            const { data } = await this.form.get(window.location.origin + '/api/food/category/view/' + this.$route.params.slug)
            this.form.category_name = data.data.category_name
            this.form.cat_id = data.data.id
        },
        // update category
        async updateMealPlan() {
            await this.form
                .post(
                    window.location.origin +
                    '/api/food/category/update'
                )
                .then((response) => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('food_category.edit.success_msg'),
                    })
                    this.$router.push({ name: 'food-category' })
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

