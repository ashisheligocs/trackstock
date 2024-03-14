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
                            {{ $t('bookingType.edit.form_title') }}
                        </h3>
                        <router-link :to="{ name: 'booking-type' }" class="btn btn-dark float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="updateBookingType" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="bookingtypetitle">{{ $t('common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="bookingtypetitle" v-model="form.bookingtypetitle" type="text"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
                                        name="bookingtypetitle" :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="bookingtypetitle" />
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
import Form from 'vform'
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('bookingType.edit.page_title') }
    },
    data: () => ({
        breadcrumbsCurrent: 'bookingType.edit.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'bookingType.edit.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'bookingType.edit.breadcrumbs_second',
                url: 'booking-type',
            },
            {
                name: 'bookingType.edit.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            bookingtypetitle: '',
            cat_id: '',
        }),
        loading: true,
    }),

    mounted() {
        this.getBookingType()
    },
    methods: {
        // get category
        async getBookingType() {
            const { data } = await this.form.get(window.location.origin + '/api/booking/type/view/' + this.$route.params.slug)  
            this.form.bookingtypetitle = data.data.bookingtypetitle
            this.form.cat_id = data.data.id
        },
        // update category
        async updateBookingType() {
            await this.form
                .post(
                    window.location.origin +
                    '/api/booking/type/edit'
                )
                .then((response) => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('bookingType.edit.success_msg'),
                    })
                    this.$router.push({ name: 'booking-type' })
                })
                .catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },
    },
}
</script>