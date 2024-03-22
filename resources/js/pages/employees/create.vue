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
                <div class="form-group col-md-3">
                  <label for="mobileNumber">{{ $t("common.contact_number") }}
                    <span class="required">*</span></label>
                  <vue-tel-input :class="{ 'is-invalid': form.errors.has('mobileNumber') }" v-model="form.mobileNumber"
                    :inputOptions="{
      showDialCode: true,
    }"></vue-tel-input>
                  <has-error :form="form" field="mobileNumber" />
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
                  <label for="email">Username
                    <span class="required">*</span></label>
                  <input id="email" v-model="form.username" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('username') }" name="username"
                    placeholder="Enter Your Username" />
                  <has-error :form="form" field="username" />
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
                  <label for="hotel" class="d-block">Shop Access
                    <span class="required">*</span></label>

                  <multiselect id="hotel" v-model="selectedHotels" :options="hotelItems" :show-labels="false"
                    tag-placeholder="" :taggable="false" placeholder="Search a Shop" class="form-control"
                    label="shop_name" track-by="shop_name" :allowEmpty="false" :multiple="true" />
                </div>
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
      username: "",
      password: "",
      role: "Roles",
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
     this.run();

    await this.getHotelDataList();
    if (this.selectedHotel && this.selectedHotel !== 'all') {
      // console.log('total',this.hotelItems);
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

    run(){
      console.log(this.selectedHotel);
    }
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
