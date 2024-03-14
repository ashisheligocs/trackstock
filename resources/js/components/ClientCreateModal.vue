<template>
    <div>
        <VModal
                v-model="showClientCreateModal"
                @close="showClientCreateModal = false"
        >
            <template v-slot:title>{{ $t("Create Client") }}</template>
            <template>
                <form
                        id="myForm"
                        class="w-100"
                        role="form"
                        @keydown="form.onKeydown($event)"
                >
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
                            <has-error :form="form" field="name"/>
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
                            <has-error :form="form" field="email"/>
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
                            <has-error :form="form" field="phoneNumber"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Identity</label>
                            <input
                                    id="identity"
                                    v-model="form.identity"
                                    type="identity"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('identity') }"
                                    name="identity"
                                    placeholder="Enter ID card"
                            />
                            <has-error :form="form" field="identity"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="email">Nationality</label>
                            <input
                                    id="nationality"
                                    v-model="form.nationality"
                                    type="nationality"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('nationality') }"
                                    name="nationality"
                                    placeholder="Enter nationality"
                            />
                            <has-error :form="form" field="nationality"/>
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
                        <has-error :form="form" field="address"/>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="image_label" for="file-upload">Upload Id</label>
                            <input type="file" id="file-upload" multiple @change="handleFileUpload">
                            <div v-if="images.length" class="image-container">
                                <div v-for="(image, index) in images" :key="index" class="image-item">
                                    <img :src="image.previewUrl" alt="Preview" class="profile-pic">
                                    <button class="remove-button" @click.stop="removeImage(index)">X</button>
                                </div>
                            </div>
                        </div>
<!--                        <div class="form-group col-12 d-flex flex-wrap">-->
<!--                            <div class="pr-5">-->
<!--                                <toggle-button v-model="form.isSendEmail"/>-->
<!--                                {{ $t("Send Welcome Email") }}-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="form-group col-12 d-flex flex-wrap">-->
<!--                            <div class="pr-5">-->
<!--                                <toggle-button v-model="form.isSendSMS"/>-->
<!--                                {{ $t("Send Welcome SMS") }}-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                </form>
                <div slot="modal-footer">
                    <button
                            @click="submitItem($event)"
                            :loading="form.busy"
                            class="btn btn-primary"
                    >
                        <i class="fas fa-save"/> {{ $t("common.save") }}
                    </button>
                </div>
            </template>
        </VModal>
        <a @click="toggleModal" class="create-button">
            <slot></slot>
        </a>
    </div>
</template>

<script>
    import Form from "vform";
    import ModalMini from "./ModalMini.vue";
    import {VueTelInput} from "vue-tel-input";
    import {ToggleButton} from "vue-js-toggle-button";
    import _ from "lodash";

    export default {
        name: "ClientCreateModal",
        middleware: ["auth", "check-permissions"],
        components: {
            ModalMini,
            ToggleButton,
            VueTelInput,
        },
        props:['hotel_id'],
        data: () => ({
            showClientCreateModal: false,
            form: new Form({
                name: "",
                email: "",
                phoneNumber: "",
                nationality: "Indian",
                identity: '',
                address: "",
                image: "",
                status: 1,
                isSendEmail: false,
                isSendSMS: false,
                images: [],
            }),
            images: [],
            loading: true,
            url: null,
        }),
        methods: {
            // vue file upload
            handleFileUpload(event) {
                const files = event.target.files;
                let error = false;
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    if (
                        files[i].size < 2111775 &&
                        (files[i].type === "image/jpeg" ||
                            files[i].type === "image/png" ||
                            files[i].type === "image/gif")
                    ) {
                        reader.onload = (e) => {
                            this.images.push({
                                file: files[i],
                                previewUrl: e.target.result
                            });
                        };
                        reader.readAsDataURL(files[i]);
                    } else {
                        error = true;
                        Swal.fire(
                            this.$t("common.error"),
                            this.$t("common.image_error"),
                            "error"
                        );
                    }
                }
                if (error) this.images = [];
            },
            removeImage(index) {
                this.images.splice(index, 1);
            },
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

            // save client
            async saveClient() {
                // console.log(this.hotel_id);
                this.form.hotel_id = (typeof this.form.hotel_id !== 'undefined') ? this.form.hotel_id : (typeof this.hotel_id !== 'undefined') ? this.hotel_id : ''; 
                this.form.images = [];
                this.form.images = this.images && this.images.length ? _.map(this.images, 'file') : [];
                await this.form
                    .post(window.location.origin + "/api/clients")
                    .then((response) => {
                        toast.fire({
                            type: "success",
                            title: this.$t("clients.create.success_msg"),
                        });
                        this.$emit("reloadClients", response?.data?.data?.id);
                        this.form.reset();
                        this.showClientCreateModal = false;
                    })
                    .catch(() => {
                        toast.fire({ type: "error", title: this.$t("common.error_msg") });
                    });
            },

            toggleModal() {
                this.images = [];
                this.showClientCreateModal = !this.showClientCreateModal;
            },

            submitItem(evt) {
                evt.preventDefault();
                this.saveClient();
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
    input[type="file"] {
        display: none;
    }

    /* Style for the file input label */
    .image_label {
        padding: 10px 20px;
        background-color: #6c788b;
        color: #ffffff;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .image_label:hover {
        background-color: #3367d6;
    }

    .image-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .image-item {
        position: relative;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .profile-pic {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        cursor: pointer;
    }

    .remove-button {
        background:none;
        border:none;
        position: absolute;
        top: 1px;
        right: 1px;
        padding: 5px;
        color: red;
        cursor: pointer;
        font-weight: bold;
    }
</style>

