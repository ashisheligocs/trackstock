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
                            {{ $t('rooms.categories.create.page_title') }}
                        </h3>
                        <router-link :to="{ name: 'room.category' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveCategory" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="room_category_name">{{ $t('common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="room_category_name" v-model="form.room_category_name" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('room_category_name') }"
                                        name="room_category_name" :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="room_category_name" />
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
        return { title: this.$t('rooms.categories.create.page_title') }
    },
    data: () => ({
        breadcrumbsCurrent: 'rooms.categories.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'rooms.categories.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'rooms.categories.create.breadcrumbs_second',
                url: 'room.category',
            },
            {
                name: 'rooms.categories.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            room_category_name: ''
        }),
        loading: true,
    }),
    methods: {
        // save category
        async saveCategory() {
            await this.form
                .post(window.location.origin + '/api/room/category/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('rooms.categories.create.success_msg'),
                    })
                    this.$router.push({ name: 'room.category' })
                })
                .catch((err) => {
                    let message = err.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },
    },
}
</script>
