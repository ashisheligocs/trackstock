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
                            {{ $t('meals.create.page_title') }}
                        </h3>
                        <router-link :to="{ name: 'meals' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveMealPlan" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="plan_name">{{ $t('common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="plan_name" v-model="form.plan_name" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('plan_name') }"
                                        name="plan_name" :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="plan_name" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="short_name">{{ $t('common.short_name') }}
                                        <span class="required">*</span></label>
                                    <input id="short_name" v-model="form.short_name" type="text"
                                           class="form-control"
                                           :class="{ 'is-invalid': form.errors.has('short_name') }"
                                           name="short_name" :placeholder="$t('common.short_name_placeholder')" />
                                    <has-error :form="form" field="short_name" />
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
        return { title: this.$t('meals.create.page_title') }
    },
    data: () => ({
        breadcrumbsCurrent: 'meals.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'meals.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'meals.create.breadcrumbs_second',
                url: 'meals',
            },
            {
                name: 'meals.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            plan_name: '',
            short_name: ''
        }),
        loading: true,
    }),
    methods: {
        // save category
        async saveMealPlan() {
            await this.form
                .post(window.location.origin + '/api/meal/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('meals.create.success_msg'),
                    })
                    this.$router.push({ name: 'meals' })
                })
                .catch((err) => {
                    let message = err.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },
    },
}
</script>
