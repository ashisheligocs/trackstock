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
              {{ $t('employees.list.edit.form_title') }}
            </h3>
            <router-link :to="{ name: 'employees.index' }" class="btn btn-secondary float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updateEmployee" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-12 col-xl-4">
                  <label for="hotel" class="d-block">{{ $t('sidebar.shops') }}
                    <span class="required">*</span></label>
                  <v-select
                    class="flex-grow-1"
                    v-model="chosenHotel"
                    :options="hotelItems"
                    label="shop_name"
                    name="hotel_id"
                    placeholder="Search a Shop"
                  />
                </div>
                <div class="form-group col-md-4">
                  <label for="employeeName">{{ $t('employees.common.employee_name') }}
                    <span class="required">*</span></label>
                  <input id="employeeName" v-model="form.employeeName" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('employeeName') }" name="employeeName" :placeholder="
                      $t('employees.common.employee_name_placeholder')
                    " />
                  <has-error :form="form" field="employeeName" />
                </div>
                <!-- <div class="form-group col-md-4">
                  <label for="department">{{ $t('common.department') }}
                    <span class="required">*</span></label>
                  <v-select v-if="items" v-model="form.department" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('department') }" name="department"
                    :placeholder="$t('common.department_placeholder')" />
                  <has-error :form="form" field="department" />
                </div> -->
              </div>
              <div class="row">

                <div class="form-group col-md-3">
                  <label for="mobileNumber">{{ $t('common.contact_number') }}
                    <span class="required">*</span></label>
                  <input id="mobileNumber" v-model="form.mobileNumber" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('mobileNumber') }" name="mobileNumber"
                    :placeholder="$t('common.contact_number_placeholder')" />
                  <has-error :form="form" field="mobileNumber" />
                </div>


              </div>

              <div class="row">
                <div class="form-group col-md-6">
                  <label for="status">{{ $t('common.status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">{{ $t('common.active') }}</option>
                    <option value="0">{{ $t('common.in_active') }}</option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
              </div>
              <div class="form-check">
                <input v-model="form.allowLogin" type="checkbox" class="form-check-input" id="allowLogin" />
                <label class="form-check-label" for="allowLogin">{{
                    $t('employees.common.allow_login')
                }}</label>
              </div>
              <div v-if="form.allowLogin" class="row mt-3">
                <div class="form-group col-md-6">
                  <label for="text">User Name
                    <span class="required">*</span></label>
                  <input id="text" v-model="form.email" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('text') }" name="text"
                    :placeholder="$t('common.email_placeholder')" />
                  <has-error :form="form" field="text" />
                </div>
                <div class="form-group col-md-6">
                  <label for="password">{{ $t('common.password') }} </label>
                  <input id="password" v-model="form.password" type="password" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('password') }" name="password"
                    placeholder="Enter a password" />
                  <has-error :form="form" field="password" />
                </div>
                <div class="form-group col-md-6">
                  <label for="role">{{ $t('common.role')
                  }}<span class="required">*</span></label>
                  <v-select v-if="roles" v-model="form.role" :options="roles" label="name"
                    :class="{ 'is-invalid': form.errors.has('role') }" name="role"
                    :placeholder="$t('common.role_placeholder')" />
                  <has-error :form="form" field="role" />
                </div>
                <div class="form-group col-md-6">
                  {{  hotelItems }}
                  <label for="hotel" class="d-block">{{ $t('sidebar.shops') }}
                    <span class="required">*</span></label>
                  <multiselect id="hotel" v-model="selectedHotels" :options="hotelItems" :show-labels="false" tag-placeholder="" :taggable="false" placeholder="Search an hotel"
                               class="form-control" label="shop_name" track-by="shop_name" :allowEmpty="false" :multiple="true"/>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" /> {{ $t('common.save_changes') }}
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
import Form from 'vform'
import Multiselect from 'vue-multiselect';
import _ from 'lodash'
import {ToggleButton} from "vue-js-toggle-button";

export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('employees.list.edit.page_title') }
  },
  data: () => ({
    breadcrumbsCurrent: 'employees.list.edit.breadcrumbs_current',
    breadcrumbs: [
      {
        name: 'employees.list.edit.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'employees.list.edit.breadcrumbs_second',
        url: 'employees.index',
      },
      {
        name: 'employees.list.edit.breadcrumbs_active',
        url: '',
      },
    ],
    selectedHotels: [],
    chosenHotel: null,
    form: new Form({
      hotel_id: [],
      hotel: '',
      employeeName: '',
      department: '',
      designation: '',
      salary: '',
      commission: '',
      mobileNumber: '',
      gender: '',
      birthDate: '',
      bloodGroup: '',
      religion: '',
      appointmentDate: '',
      joiningDate: '',
      address: '',
      status: 1,
      image: '',
      allowLogin: false,
      text: '',
      password: '',
      role: '',
    }),
    options: [],
    roles: '',
    url: null,
  }),
  computed: {
    ...mapGetters('operations', ['items', 'hotelItems', 'selectedHotel']),
  },
  components: {
    Multiselect
  },
  async created() {
    await this.getHotelDataList();
    await this.getDepartments();
    await this.getRoles();
    await this.getEmployee();
  },
  methods: {
    // get all departments
    async getDepartments() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-departments',
      })
    },

    async getHotelDataList () {
      await this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },

    // get roles
    async getRoles() {
      const { data } = await this.form.get(
        window.location.origin + '/api/all-roles'
      )
      this.roles = data.data
    },

    // get employee
    async getEmployee() {
      const { data } = await this.form.get(
        window.location.origin + '/api/employees/' + this.$route.params.slug
      )
      console.log(data.data.shops);
      console.log(data.data);
      this.form.employeeName = data.data.name
      this.form.department = data.data.department
      this.form.designation = data.data.designation
      this.form.salary = data.data.salary
      this.form.commission = data.data.commission
      this.form.mobileNumber = data.data.mobileNumber
      this.form.gender = data.data.gender
      this.form.birthDate = data.data.birthDate
      this.form.bloodGroup = data.data.bloodGroup
      this.form.religion = data.data.religion
      this.form.appointmentDate = data.data.appointmentDate
      this.form.joiningDate = data.data.joiningDate
      this.form.address = data.data.address
      this.form.status = data.data.status
      this.form.allowLogin = data.data.allowLogin
      this.form.email = data.data.email
      this.form.role = data.data.role
      this.form.back_days = data.data.back_days
      this.selectedHotels = data.data.shops
      this.chosenHotel = data.data.shop
      this.url = data.data.image
    },

    // vue file upload
    onFileChange(e) {
      const file = e.target.files[0]
      const reader = new FileReader()
      if (
        file.size < 2111775 &&
        (file.type === 'image/jpeg' ||
          file.type === 'image/png' ||
          file.type === 'image/gif')
      ) {
        reader.onloadend = (file) => {
          this.form.image = reader.result
        }
        reader.readAsDataURL(file)
        this.url = URL.createObjectURL(file)
      } else {
        Swal.fire(
          this.$t('common.error'),
          this.$t('common.image_error'),
          'error'
        )
      }
    },

    // update employee
    async updateEmployee() {
      if (this.form.allowLogin) {
        if (this.selectedHotels.length <= 0) return toast.fire({ type: 'error', title: 'select atleast one hotel' })
        this.form.hotel_id = _.map(this.selectedHotels, 'id');
      }
      if (!this.chosenHotel) return toast.fire({ type: 'error', title: 'select hotel' })

      this.form.hotel = this.chosenHotel?.id;
      await this.form
        .patch(
          window.location.origin + '/api/employees/' + this.$route.params.slug
        )
        .then((response) => {
          toast.fire({
            type: 'success',
            title: this.$t('employees.list.edit.success_msg'),
          })
          this.$router.push({ name: 'employees.index' })
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
<style src="../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="vue-tel-input/dist/vue-tel-input.css"></style>
<style scoped>
  .vue-tel-input {
    padding: 3px;
  }
  :deep(.multiselect__tags) {
    min-height: 38px !important;
    border: none !important;
    padding: 4px 40px 0 4px !important;
  }

  :deep(.multiselect__placeholder) {
    margin-bottom: 4px !important;
    padding-top: 4px !important;
  }

  :deep(.multiselect__single) {
    margin-bottom: 0px !important;
    margin-top: 4px !important;
  }

  :deep(.multiselect) {
    width: auto;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
    min-height: 38px !important;
  }

  :deep(.multiselect__input, .multiselect__single) {
    background-color: transparent !important;
  }
</style>

