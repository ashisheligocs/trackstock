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
              {{ $t('hotel.facility.create.form_title') }}
            </h3>
            <router-link :to="{ name: 'facility' }" class="btn btn-secondary float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
            </router-link>
          </div>
          <!-- /.card-header -->

          <!-- form start -->
          <form role="form" @submit.prevent="saveFacility" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="facility_title">{{ $t('common.name') }}
                    <span class="required">*</span></label>
                  <input id="facility_title" v-model="form.facility_title" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('facility_title') }" name="facility_title"
                    :placeholder="$t('common.name_placeholder')" />
                  <has-error :form="form" field="facility_title" />
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
    return { title: this.$t('hotel.facility.create.page_title') }
  },
  data: () => ({
    breadcrumbsCurrent: 'hotel.facility.create.breadcrumbs_current',
    breadcrumbs: [
      {
        name: 'hotel.facility.create.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'hotel.facility.create.breadcrumbs_second',
        url: 'hotel.category',
      },
      {
        name: 'hotel.facility.create.breadcrumbs_active',
        url: '',
      },
    ],
    form: new Form({
      facility_title: ''
    }),
    loading: true,
  }),
  methods: {
    // save category
    async saveFacility() {
      await this.form
        .post(window.location.origin + '/api/hotel/facility/add')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('hotel.categories.create.success_msg'),
          })
          this.$router.push({ name: 'facility' })
        })
        .catch((err) => {
            let message = err.response?.data?.message || this.$t('common.error_msg');
            toast.fire({ type: 'error', title: message })
        })
    },
  },
}
</script>
