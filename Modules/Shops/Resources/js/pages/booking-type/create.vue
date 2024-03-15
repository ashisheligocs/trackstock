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
                            {{ $t('bookingType.create.page_title') }}
                        </h3>
                        <router-link :to="{ name: 'booking-type' }" class="btn btn-dark float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveBookingType" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="bookingtypetitle">{{ $t('common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="bookingtypetitle" v-model="form.bookingtypetitle" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('bookingtypetitle') }"
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
        return { title: this.$t('bookingType.create.page_title') }
    },
    data: () => ({
        breadcrumbsCurrent: 'bookingType.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'bookingType.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'bookingType.create.breadcrumbs_second',
                url: 'booking-type',
            },
            {
                name: 'bookingType.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            bookingtypetitle: ''
        }),
        loading: true,
    }),
    methods: {
        // save category
        async saveBookingType() {
            await this.form
                .post(window.location.origin + '/api/booking/type/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('bookingType.create.success_msg'),
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
