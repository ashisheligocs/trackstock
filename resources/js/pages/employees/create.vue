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
              {{ $t("employees.list.create.form_title") }}
            </h3>
            <router-link :to="{ name: 'employees.index' }" class="btn btn-secondary float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="saveEmployee" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-xl-4">
                  <label for="hotel" class="d-block">{{ $t('sidebar.shops') }}
                    <span class="required">*</span></label>
                  <v-select class="flex-grow-1" v-model="chosenHotel" :options="hotelItems" label="shop_name"
                    name="shop_id" placeholder="Search a shop" />
                </div>
                <div class="form-group col-md-4">
                  <label for="employeeName">{{ $t("employees.common.employee_name") }}
                    <span class="required">*</span></label>
                  <input id="employeeName" v-model="form.employeeName" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('employeeName') }" name="employeeName" :placeholder="$t('employees.common.employee_name_placeholder')
      " />
                  <has-error :form="form" field="employeeName" />
                </div>
                <div class="form-group col-md-4">
                  <label for="department">{{ $t("common.department") }}
                    <span class="required">*</span></label>
                  <v-select v-if="items" v-model="form.department" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('department') }" name="department"
                    :placeholder="$t('common.department_placeholder')" />
                  <has-error :form="form" field="department" />
                </div>

                <!-- <div class="form-group col-md-3">
                  <label for="designation"
                  >{{ $t("common.designation") }}
                    <span class="required">*</span></label
                  >
                  <input
                    id="designation"
                    v-model="form.designation"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('designation') }"
                    name="designation"
                    :placeholder="$t('common.designation_placeholder')"
                  />
                  <has-error :form="form" field="designation" />
                </div> -->

                <div class="form-group col-md-3">
                  <label for="mobileNumber">{{ $t("common.contact_number") }}
                    <span class="required">*</span></label>
                  <vue-tel-input :class="{ 'is-invalid': form.errors.has('mobileNumber') }" v-model="form.mobileNumber"
                    :inputOptions="{
      showDialCode: true,
    }"></vue-tel-input>
                  <has-error :form="form" field="mobileNumber" />
                </div>
                <!-- <div class="form-group col-md-3">
                  <label for="salary"
                    >{{ $t("common.salary") }}
                    <span class="required">*</span></label
                  >
                  <input
                    id="salary"
                    v-model="form.salary"
                    type="number"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('salary') }"
                    name="salary"
                    :placeholder="$t('common.salary_placeholder')"
                    min="0"
                  />
                  <has-error :form="form" field="salary" />
                </div> -->
                <!-- <div class="form-group col-md-3">
                  <label for="commission"
                    >{{ $t("payroll.common.commission") }}(%)
                  </label>
                  <input
                    id="commission"
                    v-model="form.commission"
                    type="number"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('commission') }"
                    name="commission"
                    :placeholder="$t('payroll.common.commission_placeholder')"
                    max="100"
                  />
                  <has-error :form="form" field="commission" />
                </div> -->

                <!-- <div class="form-group col-md-3">
                  <label for="birthDate">{{
                    $t("employees.common.birth_date")
                  }}<span class="required">*</span></label>
                  <input
                    id="birthDate"
                    v-model="form.birthDate"
                    type="date"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('birthDate') }"
                    name="birthDate"
                  />
                  <has-error :form="form" field="birthDate" />
                </div> -->
                <div class="form-group col-md-3">
                  <label for="gender">{{ $t("employees.common.gender") }}
                    <span class="required">*</span></label>
                  <select v-model="form.gender" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('gender') }" name="gender">
                    <option value="" selected disabled>
                      {{ $t("employees.common.gender_placeholder") }}
                    </option>
                    <option value="Male">
                      {{ $t("employees.common.male") }}
                    </option>
                    <option value="Female">
                      {{ $t("employees.common.female") }}
                    </option>
                    <option value="Transgender">
                      {{ $t("employees.common.transgender") }}
                    </option>
                    <option value="Other">
                      {{ $t("employees.common.other") }}
                    </option>
                  </select>
                  <has-error :form="form" field="gender" />
                </div>

                <!-- <div class="form-group col-md-6">
                  <label for="appointmentDate">{{
                    $t("employees.common.appointment_date")
                  }}<span class="required">*</span></label>
                  <input
                    id="appointmentDate"
                    v-model="form.appointmentDate"
                    type="date"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('appointmentDate'),
                    }"
                    name="appointmentDate"
                  />
                  <has-error :form="form" field="appointmentDate" />
                </div> -->
                <div class="form-group col-md-6">
                  <label for="joiningDate">{{
      $t("employees.common.join_date")
    }}<span class="required">*</span></label>
                  <input id="joiningDate" v-model="form.joiningDate" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('joiningDate') }" name="joiningDate" />
                  <has-error :form="form" field="joiningDate" />
                </div>

              <div class="form-group col-md-6">
                <label for="address">{{ $t("common.address") }}</label>
                <textarea id="address" v-model="form.address" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('common.address_placeholder')" />
                <has-error :form="form" field="address" />
              </div>

                <div class="form-group col-md-6">
                  <label for="status">{{ $t("common.status") }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">{{ $t("common.active") }}</option>
                    <option value="0">{{ $t("common.in_active") }}</option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
                <div class="form-group col-md-6">
                  <!-- <label for="image">{{ $t("common.profile_picture") }}</label>
                  <div class="custom-file">
                    <input
                      id="image"
                      type="file"
                      class="custom-file-input"
                      name="image"
                      :class="{ 'is-invalid': form.errors.has('image') }"
                      @change="onFileChange"
                    />
                    <label class="custom-file-label" for="image">{{
                      $t("common.choose_file")
                    }}</label>
                  </div> -->
                  <has-error :form="form" field="image" />
                  <div class="bg-light mt-4 w-25">
                    <img v-if="url" :src="url" class="img-fluid" :alt="$t('common.image_alt')" />
                  </div>
                </div>
              </div>
              <div class="form-check">
                <input v-model="form.allowLogin" type="checkbox" class="form-check-input" id="allowLogin" />
                <label class="form-check-label" for="allowLogin">{{
      $t("employees.common.allow_login")
    }}</label>
              </div>
              <div class="row mt-3">
                <div class="form-group col-md-6">
                  <label for="email">{{ $t("common.email") }}
                    <span class="required">*</span></label>
                  <input id="email" v-model="form.email" type="email" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('email') }" name="email"
                    :placeholder="$t('common.email_placeholder')" />
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-group col-md-6">
                  <label for="password">{{ $t("common.password") }}
                    <span class="required">*</span></label>
                  <input id="password" v-model="form.password" type="password" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('password') }" name="password"
                    :placeholder="$t('common.password_placeholder')" />
                  <has-error :form="form" field="password" />
                </div>
                <div class="form-group col-md-6">
                  <label for="role">{{ $t("common.role")
                    }}<span class="required">*</span></label>
                  <v-select v-if="roles" v-model="form.role" :options="roles" label="name"
                    :class="{ 'is-invalid': form.errors.has('role') }" name="role"
                    :placeholder="$t('common.role_placeholder')" />
                  <has-error :form="form" field="role" />
                </div>
                <div class="form-group col-md-6">
                  <label for="hotel" class="d-block">Hotel Access
                    <span class="required">*</span></label>
                  <multiselect id="hotel" v-model="selectedHotels" :options="hotelItems" :show-labels="false"
                    tag-placeholder="" :taggable="false" placeholder="Search a Shop" class="form-control"
                    label="shop_name" track-by="shop_name" :allowEmpty="false" :multiple="true" />
                </div>
                <!-- <div class="form-group col-md-6">
                  <label for="back_days">Back Days <span class="required">*</span></label>
                  <input id="back_days" v-model="form.back_days" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('back_days') }" name="back_days"
                    placeholder="Enter a back days" />
                  <has-error :form="form" field="back_days" />
                </div> -->
              </div>
            </div>
                        <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-save" /> {{ $t("common.save") }}
              </v-button>
              <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                <i class="fas fa-power-off" /> {{ $t("common.reset") }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import { VueTelInput } from "vue-tel-input";
import { ToggleButton } from "vue-js-toggle-button";
import Multiselect from 'vue-multiselect';
import _ from 'lodash';

import Form from "vform";
export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("employees.list.create.page_title") };
  },
  components: {
    VueTelInput,
    ToggleButton,
    Multiselect
  },
  data: () => ({
    breadcrumbsCurrent: "employees.list.create.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "employees.list.create.breadcrumbs_first",
        url: "home",
      },
      {
        name: "employees.list.create.breadcrumbs_second",
        url: "employees.index",
      },
      {
        name: "employees.list.create.breadcrumbs_active",
        url: "",
      },
    ],
    url: null,
    selectedHotels: [],
    chosenHotel: null,
    form: new Form({
      shop_id: [],
      shop: '',
      employeeName: "",
      department: "",
      designation: "",
      salary: "",
      commission: "",
      mobileNumber: "",
      gender: "",
      birthDate: "",
      bloodGroup: "",
      religion: "",
      appointmentDate: "",
      joiningDate: "",
      address: "",
      status: 1,
      image: "",
      allowLogin: false,
      email: "",
      password: "",
      role: "Employee",
      back_days: '',
    }),
    options: [],
    roles: "",
  }),
  computed: {
    ...mapGetters("operations", ["items", "hotelItems", "selectedHotel"]),
  },
  async created() {
    await this.getDepartments();
    await this.getRoles();
    await this.getHotelDataList();
    if (this.selectedHotel && this.selectedHotel !== 'all') {
      this.hotelItems.forEach((hotel) => {
        if (hotel.id == this.selectedHotel) {
          this.selectedHotels.push(hotel)
          this.chosenHotel = hotel
        }
      })
    } else {
      this.hotelItems && this.selectedHotels.push(this.hotelItems[0])
    }
  },
  methods: {
    // get all departments
    async getDepartments() {
      await this.$store.dispatch("operations/allData", {
        path: "/api/all-departments",
      });
    },
    async getHotelDataList() {
      await this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
    // get roles
    async getRoles() {
      const { data } = await this.form.get(
        window.location.origin + "/api/all-roles"
      );
      this.roles = data.data
      // this.roles = [{'name':'Employeee','slug':'employee'}];
    },

    // vue file upload
    onFileChange(e) {
      const file = e.target.files[0];
      const reader = new FileReader();
      if (
        file.size < 2111775 &&
        (file.type === "image/jpeg" ||
          file.type === "image/png" ||
          file.type === "image/gif")
      ) {
        reader.onloadend = (file) => {
          this.form.image = reader.result;
        };
        reader.readAsDataURL(file);
        this.url = URL.createObjectURL(file);
      } else {
        Swal.fire(
          this.$t("common.error"),
          this.$t("common.image_error"),
          "error"
        );
      }
    },

    // save employee
    async saveEmployee() {
      if (this.form.allowLogin) {
        if (this.selectedHotels.length <= 0) return toast.fire({ type: 'error', title: 'select atleast one access hotel' })
        this.form.shop_id = _.map(this.selectedHotels, 'id');
      }
      if (!this.chosenHotel) return toast.fire({ type: 'error', title: 'select hotel' })

      this.form.shop = this.chosenHotel?.id;
      await this.form
        .post(window.location.origin + "/api/employees")
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("employees.list.create.success_msg"),
          });
          this.$router.push({ name: "employees.index" });
        })
        .catch((err) => {
          let message = err.response?.data?.message || this.$t('common.error_msg');
          toast.fire({ type: 'error', title: message })
        });
    },
  },
};
</script>
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

<style src="../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
