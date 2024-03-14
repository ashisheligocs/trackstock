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
              {{ $t('hotel.categories.create.form_title') }}
            </h3>
            <router-link :to="{ name: 'hotel.category' }" class="btn btn-secondary float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
            </router-link>
          </div>
          <!-- /.card-header -->

          <!-- form start -->
          <form role="form" @submit.prevent="updateCategory" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="category_name">{{ $t('common.name') }}
                    <span class="required">*</span></label>
                  <input id="category_name" v-model="form.category_name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('category_name') }" name="category_name"
                    :placeholder="$t('common.name_placeholder')" />
                  <input id="id" v-model="form.id" type="hidden" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('id') }" name="id"
                    :placeholder="$t('common.name_placeholder')" />
                  <has-error :form="form" field="category_name" />
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
    return { title: this.$t('hotel.categories.edit.page_title') }
  },
  data: () => ({
    breadcrumbsCurrent: 'hotel.categories.edit.breadcrumbs_current',
    breadcrumbs: [
      {
        name: 'hotel.categories.edit.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'hotel.categories.edit.breadcrumbs_second',
        url: 'hotel.category',
      },
      {
        name: 'hotel.categories.edit.breadcrumbs_active',
        url: '',
      },
    ],
    form: new Form({
      category_name: ''
    }),
    loading: true,
  }),

  mounted() {
    this.getCategory()
  },
  methods: {
    // get category
    async getCategory() {
      const { data } = await this.form.get( window.location.origin + '/api/hotel/category/edit/' + this.$route.params.slug)
      this.form.category_name = data.data.category_name
      this.form.id = data.data.id
    },
    // update category
    async updateCategory() {
      await this.form
        .post(
          window.location.origin +
          '/api/hotel/category/update'
        )
        .then((response) => {
          toast.fire({
            type: 'success',
            title: this.$t('expenses.categories.edit.success_msg'),
          })
          this.$router.push({ name: 'hotel.category' })
        })
        .catch((error) => {
            let message = error.response?.data?.message || this.$t('common.error_msg');
            toast.fire({ type: 'error', title: message })
        })
    },
  },
}
</script>

