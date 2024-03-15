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
                            {{ $t('rooms.facility.create.page_title') }}
                        </h3>
                        <router-link :to="{ name: 'room.facility' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveFacility" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="room_facility_title">{{ $t('common.name') }}
                                        <span class="required">*</span></label>
                                    <input id="room_facility_title" v-model="form.room_facility_title" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('room_facility_title') }"
                                        name="room_facility_title" :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="room_facility_title" />
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="facility_description">{{ $t('common.description') }}
                                        <span class="required">*</span></label>
                                    <input id="facility_description" v-model="form.facility_description" type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('facility_description') }"
                                        name="facility_description" :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="facility_description" />
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
        return { title: this.$t('rooms.facility.create.page_title') }
    },
    data: () => ({
        breadcrumbsCurrent: 'rooms.facility.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'rooms.facility.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'rooms.facility.create.breadcrumbs_second',
                url: 'room.facility',
            },
            {
                name: 'rooms.facility.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            room_facility_title: '',
            facility_description: '',
        }),
        loading: true,
    }),
    methods: {
        // save category
        async saveFacility() {
            await this.form
                .post(window.location.origin + '/api/room/facility/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('rooms.facility.create.success_msg'),
                    })
                    this.$router.push({ name: 'room.facility' })
                })
                .catch((err) => {
                    let message = err.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },
    },
}
</script>
  