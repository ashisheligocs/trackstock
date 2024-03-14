<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $t('clients.edit.form_title') }}</h3>
                        <router-link :to="{ name: 'clients.index' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form role="form" @submit.prevent="saveClient" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="hotel" class="d-block">{{ $t('sidebar.hotel') }}
                                        <span class="required">*</span></label>
                                    <v-select class="flex-grow-1" v-model="form.hotel_id" :options="hotelItems"
                                        label="hotel_name" :class="{ 'is-invalid': form.errors.has('hotel_id') }"
                                        name="hotel_id" placeholder="Search a hotel" />
                                    <has-error :form="form" field="hotel_id" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">Customer / Company Name
                                        <span class="required">*</span></label>
                                    <input id="name" v-model="form.name" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                                        :placeholder="$t('common.name_placeholder')" />
                                    <has-error :form="form" field="name" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="email">{{ $t('common.email') }}</label>
                                    <input id="email" v-model="form.email" type="email" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('email') }" name="email"
                                        :placeholder="$t('common.email_placeholder')" />
                                    <has-error :form="form" field="email" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phoneNumber">{{ $t('common.contact_number') }}
                                        <span class="required">*</span></label>
                                    <input id="phoneNumber" v-model="form.phoneNumber" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('phoneNumber') }" name="phoneNumber"
                                        :placeholder="$t('common.contact_number_placeholder')" />
                                    <has-error :form="form" field="phoneNumber" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">GST Number
                                        <span class="required">*</span>
                                    </label>
                                    <input id="name" v-model="form.gst" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('gst') }" name="name"
                                        placeholder="GST Number" />
                                    <has-error :form="form" field="name" />
                                </div>
                            </div>
                            <div class="row mb-4">
                                <!--                                <div class="form-group col-md-6">-->
                                <!--                                    <label for="companyName">{{-->
                                <!--                                        $t('common.company_name')-->
                                <!--                                        }}</label>-->
                                <!--                                    <input id="companyName" v-model="form.companyName" type="companyName"-->
                                <!--                                           class="form-control"-->
                                <!--                                           :class="{ 'is-invalid': form.errors.has('companyName') }" name="companyName"-->
                                <!--                                           :placeholder="$t('common.company_name_placeholder')"/>-->
                                <!--                                    <has-error :form="form" field="companyName"/>-->
                                <!--                                </div>-->
                                <div class="col-md-6">
                                    <label for="identity">{{
                                        $t("common.identity")
                                    }}</label>
                                    <input id="identity" v-model="form.identity" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('identity') }" name="identity"
                                        :placeholder="$t('common.identity_placeholder')" />
                                    <has-error :form="form" field="identity" />
                                </div>

                               

                                <div class="form-group col-md-6">
                                    <label for="address">{{ $t('common.address') }}</label>
                                    <textarea id="address" v-model="form.address" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('address') }"
                                        :placeholder="$t('common.address_placeholder')" />
                                    <has-error :form="form" field="address" />
                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label for="nationality">{{
                                        $t("common.nationality")
                                    }}</label>
                                    <input id="nationality" v-model="form.nationality" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('nationality') }" name="nationality"
                                        :placeholder="$t('common.nationality_placeholder')" />
                                    <has-error :form="form" field="nationality" />
                                </div>
                                <!--                              <div class="form-group col-md-6">-->
                                <!--                                <label for="status">{{ $t("common.status") }}</label>-->
                                <!--                                <select-->
                                <!--                                  id="status"-->
                                <!--                                  v-model="form.status"-->
                                <!--                                  class="form-control"-->
                                <!--                                  :class="{ 'is-invalid': form.errors.has('status') }"-->
                                <!--                                >-->
                                <!--                                  <option value="1">{{ $t("common.active") }}</option>-->
                                <!--                                  <option value="0">{{ $t("common.in_active") }}</option>-->
                                <!--                                </select>-->
                                <!--                                <has-error :form="form" field="status"/>-->
                                <!--                              </div>-->
                                <div class="form-group col-md-6">
                                    <label for="type">{{ $t("common.type") }}</label>
                                    <select id="type" v-model="form.type" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('type') }">
                                        <option value="1">{{ $t("common.customer") }}</option>
                                        <option value="2">{{ $t("common.agent") }}</option>
                                    </select>
                                    <has-error :form="form" field="type" />
                                </div>
                            </div>
                            <div class="row">



                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="image_label" for="file-upload">Choose Image</label>
                                    <input type="file" id="file-upload" multiple @change="handleFileUpload">

                                    <label class="image_label" for="file-camera" @click="toggleCamera" ><i class="fa fa-camera"></i></label>
                                    <div v-if="isCameraOpen" v-show="!isLoading" class="camera-box" :class="{ 'flash': isShotPhoto }">

                                    <div class="camera-shutter" :class="{ 'flash': isShotPhoto }"></div>

                                    <video v-show="!isPhotoTaken" ref="camera" :width="450" :height="337.5" autoplay></video>
                                    <canvas v-show="isPhotoTaken" id="photoTaken" ref="canvas" :width="450" :height="337.5"></canvas>
                                    </div>

                                    <div v-if="isCameraOpen && !isLoading" class="camera-shoot">
                                        <button type="button" class="btn btn-dark button" @click="takePhoto">
                                            Take Photo
                                        </button>
                                    </div>

                                    
                                    <div class="image-container" v-if="images && images.length > 0">
                                        <div v-for="(image, index) in images" :key="index" class="image-item">
                                            <img :src="image.previewUrl" alt="Preview" class="profile-pic"
                                                @click="showImage(index)">
                                            <button class="remove-button" @click.stop="removeImage(index)">X</button>
                                        </div>
                                    </div>
                                    <div class="image-container" v-if="photoData && photoData.length > 0">
                                        <div v-for="(image, index) in photoData" :key="index"  class="image-item">
                                            <img :src="image" alt="Preview" class="profile-pic">
                                            <button class="remove-button" @click.stop="removeImagess(index)">X</button>
                                        </div>
                                    </div>
                                    
                                    <div v-if="showModal" class="modal" @click.self="closeModal">
                                        <div class="modal-content">
                                            <span class="close" @click="closeModal">&times;</span>
                                            <img :src="selectedImage.previewUrl" alt="Preview" class="modal-image">
                                        </div>
                                    </div>
                                </div>
                                <!--                                <div class="form-group col-12 d-flex flex-wrap">-->
                                <!--                                    <div class="pr-5">-->
                                <!--                                        <toggle-button v-model="form.isSendEmail"/>-->
                                <!--                                        {{ $t("Send Welcome Email") }}-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
                                <!--                                <div class="form-group col-12 d-flex flex-wrap">-->
                                <!--                                    <div class="pr-5">-->
                                <!--                                        <toggle-button v-model="form.isSendSMS"/>-->
                                <!--                                        {{ $t("Send Welcome SMS") }}-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
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
import Form from 'vform'
import _ from "lodash";
import { mapGetters } from "vuex";

export default {
    middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('clients.edit.page_title') }
    },
    data: () => ({
        breadcrumbsCurrent: 'clients.edit.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'clients.edit.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'clients.edit.breadcrumbs_second',
                url: 'clients.index',
            },
            {
                name: 'clients.edit.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            name: '',
            email: '',
            phoneNumber: '',
            gst: '',
            companyName: '',
            address: '',
            images: [],
            status: 1,
            type: 1,
            pastImages: [],
            identity: '',
            nationality: 'Indian',
            hotel_id: '',
            file: [],
        }),
        isCameraOpen: false,
        isPhotoTaken: false,
        isShotPhoto: false,
        isLoading: false,
        loading: true,
        url: null,
        images: [],
        showModal: false,
        selectedImage: null,
        photoData : [],
    }),
    computed: {
        ...mapGetters("operations", ["hotelItems", "selectedHotel"]),
    },
    mounted() {
        this.getClient()
    },
    created() {
        this.getHotelDataList();
    },
    methods: {

      toggleCamera() {
        if (this.isCameraOpen) {
            this.isCameraOpen = false;
            this.isPhotoTaken = false;
            this.isShotPhoto = false;
            this.stopCameraStream();
        } else {
            this.isCameraOpen = true;
            this.createCameraElement();
        }
      },

    createCameraElement() {
      this.isLoading = true;

      const constraints = (window.constraints = {
        audio: false,
        video: true
      });


      navigator.mediaDevices
        .getUserMedia(constraints)
        .then(stream => {
          this.isLoading = false;
          this.$refs.camera.srcObject = stream;
        })
        .catch(error => {
          this.isLoading = false;
          Swal.fire({
                type: "error",
                title: "<i class='fa fa-camera'></i>",
                text: "There is some error while opening camera...",
            });
        });
    },

    stopCameraStream() {
      let tracks = this.$refs.camera.srcObject.getTracks();

      tracks.forEach(track => {
        track.stop();
      });
    },

    takePhoto() {
        
      if (!this.isPhotoTaken) {
        this.isShotPhoto = true;

        const FLASH_TIMEOUT = 50;

        setTimeout(() => {
          this.isShotPhoto = false;
        }, FLASH_TIMEOUT);
      }
      this.isPhotoTaken = !this.isPhotoTaken;


      

      const context = this.$refs.canvas.getContext('2d');
      context.drawImage(this.$refs.camera, 0, 0, 450, 337.5);
    //   this.photoData = document.getElementById("photoTaken").toDataURL('image/png');

      const dataURL = document.getElementById("photoTaken").toDataURL("image/jpeg");
      this.photoData.push(dataURL);

      setTimeout(() => {
          if(dataURL){
              var blob = this.dataURLtoBlob(dataURL);
              
              if(blob){
                // this.form.file = blob;
                this.form.file.push(blob);
                this.isCameraOpen = false;
                this.isPhotoTaken = false;
                this.isShotPhoto = false;
              }
          }
      }, 1000);
    },
    dataURLtoBlob(dataURL) {
      const arr = dataURL.split(',');
      const mime = arr[0].match(/:(.*?);/)[1];
      const bstr = atob(arr[1]);
      let n = bstr.length;
      const u8arr = new Uint8Array(n);

      while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
      }

      return new Blob([u8arr], { type: mime });
    },

        async getHotelDataList() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/hotel',
            });
        },
        // get client
        async getClient() {
            const { data } = await this.form.get(
                window.location.origin + '/api/clients/' + this.$route.params.slug
            )
            this.form.name = data.data.name
            this.form.clientID = data.data.clientID
            this.form.email = data.data.email
            this.form.phoneNumber = data.data.phone
            this.form.gst = data.data.gst
            this.form.companyName = data.data.companyName
            this.form.address = data.data.address
            this.form.status = data.data.status
            this.form.type = data.data.type
            this.form.identity = data.data.identity;
            this.form.nationality = data.data.nationality;
            this.url = data.data.image
            console.log(data.data.GST_Number);
            this.form.hotel_id = data.data.hotel
            this.images = data.data?.images?.map((image) => {
                return {
                    file: null,
                    previewUrl: `/images/clients/${image}`,
                    name: image
                }
            })
            console.log( data.data?.images)
            if (!this.images) this.images = [];
            if ((!data.data.images || !data.data.images?.length) && data.data.image) {
                this.images.push({
                    file: null,
                    previewUrl: `/images/clients/${data.data.image}`,
                    name: data.data.image
                })
            }
        },

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
        removeImagess(index) {
            this.photoData.splice(index, 1);
        },
        showImage(index) {
            this.selectedImage = this.images[index];
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.selectedImage = null;
        },

        // update client
        async saveClient() {
            this.form.images = [];
            this.form.pastImages = [];
            // this.form.images = this.images && this.images.length ? _.map(this.images, 'file') : [];
            if (this.images && this.images.length) {
                this.images.forEach((image) => {
                    if (image.file) this.form.images.push(image.file);
                    else this.form.pastImages.push(image.name)
                })
            }

            await this.form
                .post(
                    window.location.origin + '/api/clients/' + this.$route.params.slug
                )
                .then((response) => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('clients.edit.success_msg'),
                    })
                    this.$router.push({ name: 'clients.index' })
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

<style scoped>
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
    background: none;
    border: none;
    position: absolute;
    top: 1px;
    right: 1px;
    padding: 5px;
    color: red;
    cursor: pointer;
    font-weight: bold;
}

/* Modal styles */
.modal {
    display: block;
    position: fixed;
    z-index: 1;
    padding-top: 50px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.8);
}

.modal-content {
    margin: auto;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 80%;
    max-width: 800px;
    height: 80%;
}

.modal-image {
    width: 100%;
    height: auto;
    object-fit: contain;
}

.close {
    position: absolute;
    top: 20px;
    right: 30px;
    font-size: 35px;
    font-weight: bold;
    color: #f1f1f1;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}
</style>

