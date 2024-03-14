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
                            {{ $t('floor.create.form_title') }}
                        </h3>
                        <router-link :to="{ name: 'floor' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="updateMealPlan" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="floorsName">{{ $t('common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="floorsName" v-model="form.floorsName" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('floorsName') }"
                                        name="floorsName" :placeholder="$t('common.name_placeholder')" />
                                    <input id="id" v-model="form.id" type="hidden" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('id') }" name="id"
                                        :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="floorsName" />
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
        return { title: this.$t('floor.edit.page_title') }
    },
    data: () => ({
        breadcrumbsCurrent: 'floor.edit.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'floor.edit.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'floor.edit.breadcrumbs_second',
                url: 'floor',
            },
            {
                name: 'floor.edit.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            floorsName: '',
            id: '',
        }),
        loading: true,
    }),

    mounted() {
        this.getMealPlan()
    },
    methods: {
        // get category
        async getMealPlan() {
            const { data } = await this.form.get(window.location.origin + '/api/floor/view/' + this.$route.params.slug)  
            this.form.floorsName = data.data.floorsName
            this.form.id = data.data.id
        },
        // update category
        async updateMealPlan() {
            await this.form
                .post(
                    window.location.origin +
                    '/api/floor/edit'
                )
                .then((response) => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('floor.edit.success_msg'),
                    })
                    this.$router.push({ name: 'floor' })
                })
                .catch((err) => {
                    let message = err.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },
    },
}
</script>
  
  