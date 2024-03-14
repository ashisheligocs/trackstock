<template>
  <div>
    <VModal
      v-model="showSupplierCreateModal"
      @close="showSupplierCreateModal = false"
    >
      <template v-slot:title>{{ $t("Create Supplier") }}</template>
      <div class="w-100">
        <!-- form start -->
        <form role="form" class="w-100" @keydown="form.onKeydown($event)">
          <div class="row">
            <div class="form-group col-md-12">
                <label for="hotel" class="d-block">{{ $t('sidebar.hotel') }}
                    <span class="required">*</span></label>
                <v-select
                        class="flex-grow-1"
                        v-model="form.hotel_id"
                        :options="hotelItems"
                        label="hotel_name"
                        :class="{ 'is-invalid': form.errors.has('hotel_id') }"
                        name="hotel_id"
                        placeholder="Search a hotel"
                />
                <has-error :form="form" field="hotel_id"/>
            </div>
          </div>
          
          <div class="row">
            <div class="form-group col-md-6">
              <label for="name"
                >{{ $t("common.name") }} <span class="required">*</span></label
              >
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('name') }"
                name="name"
                :placeholder="$t('common.name_placeholder')"
              />
              <has-error :form="form" field="name" />
            </div>
            <div class="form-group col-md-6">
              <label for="companyName">{{ $t("common.company_name") }}</label>
              <input
                id="companyName"
                v-model="form.companyName"
                type="companyName"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('companyName') }"
                name="companyName"
                :placeholder="$t('common.company_name_placeholder')"
              />
              <has-error :form="form" field="companyName" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="phoneNumber"
                >{{ $t("common.contact_number") }}
                <span class="required">*</span></label
              >
              <vue-tel-input
                :class="{ 'is-invalid': form.errors.has('phoneNumber') }"
                v-model="form.phoneNumber"
                :inputOptions="{
                  showDialCode: true,
                }"
              ></vue-tel-input>
              <has-error :form="form" field="phoneNumber" />
            </div>
            <div class="form-group col-md-6">
              <label for="email">{{ $t("common.email") }}</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('email') }"
                name="email"
                :placeholder="$t('common.email_placeholder')"
              />
              <has-error :form="form" field="email" />
            </div>
          </div>
          <div class="form-group">
            <label for="address">{{ $t("common.address") }}</label>
            <textarea
              id="address"
              v-model="form.address"
              class="form-control"
              :class="{ 'is-invalid': form.errors.has('address') }"
              :placeholder="$t('common.address_placeholder')"
            />
            <has-error :form="form" field="address" />
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="image">{{ $t("common.image") }}</label>
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
              </div>
              <has-error :form="form" field="image" />
              <div class="bg-light mt-4 w-25">
                <img
                  v-if="url"
                  :src="url"
                  class="img-fluid"
                  :alt="$t('common.image_alt')"
                />
              </div>
            </div>
            <div class="form-group col-12 d-flex flex-wrap">
              <div class="pr-5">
                <toggle-button v-model="form.isSendEmail" />
                {{ $t("Send To Email") }}
              </div>
            </div>
<!--            <div class="form-group col-12 d-flex flex-wrap">-->
<!--              <div class="pr-5">-->
<!--                <toggle-button v-model="form.isSendSMS" />-->
<!--                {{ $t("Send To SMS") }}-->
<!--              </div>-->
<!--            </div>-->
          </div>
        </form>
      </div>
      <div slot="modal-footer">
        <button
          @click="submitItem($event)"
          :loading="form.busy"
          class="btn btn-primary"
        >
          <i class="fas fa-save" /> {{ $t("common.save") }}
        </button>
      </div>
    </VModal>
    <a @click="toggleModal" class="create-button"><slot></slot></a>
  </div>
</template>

<script>
import Form from "vform";
import ModalMini from "./ModalMini.vue";
import { VueTelInput } from "vue-tel-input";
import { ToggleButton } from "vue-js-toggle-button";
import {mapGetters} from "vuex";

export default {
  name: "SupplierCreateModal",
  middleware: ["auth", "check-permissions"],
  components: {
    ModalMini,
    VueTelInput,
    ToggleButton,
  },

  data: () => ({
    form: new Form({
      hotel_id:"",
      name: "",
      email: "",
      phoneNumber: "",
      companyName: "",
      address: "",
      image: "",
      status: 1,
      isSendEmail: false,
      isSendSMS: false,
    }),
    loading: true,
    url: null,
    showSupplierCreateModal: false,
  }),
  computed: {
      ...mapGetters("operations", ["hotelItems"]),
  },
  methods: {
    toggleModal() {
      this.showSupplierCreateModal = !this.showSupplierCreateModal;
    },

    submitItem(evt) {
      evt.preventDefault();
      this.saveSupplier();
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

    // save supplier
    async saveSupplier() {
      await this.form
        .post(window.location.origin + "/api/suppliers")
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("suppliers.create.success_msg"),
          });
          this.form.reset();
          this.showSupplierCreateModal = false;
          this.$emit("reloadSuppliers");
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("common.error_msg") });
        });
    },
  },
};
</script>
<style src="vue-tel-input/dist/vue-tel-input.css"></style>
<style scoped>
.create-button {
  text-decoration: none;
  cursor: pointer;
}
.vue-tel-input {
  padding: 3px;
}
.ti__dropdown-list {
  z-index: 2;
}
</style>

