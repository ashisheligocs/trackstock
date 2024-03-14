<template>
    <div class="card p-4">

        <div>
            <div class="row d-flex justify-content-between">
                <button @click="currentTabPre()" class="btn btn-secondary mr-3">
                    <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                </button>

                <button @click="createRoom" class="btn btn-primary">
                    {{ $t("common.add_room") }}
                    <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                </button>
            </div>
        </div>

        <div class="card-body p-0 position-relative">
            <table-loading v-show="loading" />
            <div id="printMe" class="table-responsive table-custom mt-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ $t("rooms.common.room_category") }}</th>
                            <th>{{ $t("sidebar.bedType") }}</th>
                            <th style="position: sticky; left: 0;" class="text-right">{{ $t("rooms.common.name") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-show="addRoomList.length > 0" v-for="(data, i) in addRoomList" :key="i">
                            <!-- <td style="position: sticky; left: 0; background-color: #fff;">
                                <div class="form-group mb-0">
                                    <div class="d-flex align-items-center ">
                                        <input :class="{ 'is-invalid': room_form?.errors.has('room_name') }" v-if="editRoom && i == selectedIndex" type="text" v-model="data.room_name" class="px-2 form-control" style="width: 150px">
                                        <span v-else>{{ data.room_name }}</span>
                                    </div>
                                    <has-error v-if="editRoom && i == selectedIndex" :form="room_form" field="room_name" />
                                </div>
                                <div class="btn-group">
                                        <i  @click="editData(data, i)" class="fas fa-edit cursor-pointer" style="cursor:pointer"/>
                                        <i class="fas fa-trash ml-2" style="cursor:pointer" @click="deleteData(data.id)"/>
                                </div>
                            </td> -->
                            <td>
                                <div class="form-group mb-0">
                                    <div class="d-flex align-items-center">
                                        <select :class="{ 'is-invalid': room_form?.errors.has('room_categorary') }" class="form-control" v-if="editRoom && i == selectedIndex" v-model="data.room_categorary">
                                            <option v-for="(item) in hotelCategoryItems" :key="item.id" :value="item.id">{{ item?.room_category_name }}</option>
                                        </select>
                                        <span v-else>{{ data?.roomcategory?.room_category_name }}</span>
                                    </div>
                                    <has-error v-if="editRoom && i == selectedIndex" :form="room_form" field="room_categorary" />
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <select v-if="editRoom && i == selectedIndex" v-model="data.floor_id" class="form-control">
                                        <option v-for="(item) in floorItems" :key="item.id" :value="item.id">{{ item.floorsName }}</option>
                                    </select>
                                    <span v-else>{{ data?.floor?.floorsName }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group mb-0">
                                    <div class="d-flex align-items-center">
                                        <select :class="{ 'is-invalid': room_form?.errors.has('bed_type_id') }"  v-if="editRoom && i == selectedIndex" v-model="data.bed_type_id" class="form-control">
                                            <option v-for="(item) in bedTypeItems" :key="item.id" :value="item.id">{{ item.bedtypetitle }}</option>
                                        </select>
                                        <span v-else>{{ data?.bed?.bedtypetitle }}</span>
                                    </div>
                                    <has-error v-if="editRoom && i == selectedIndex" :form="room_form" field="bed_type_id" />
                                </div>
                            </td>
                            <td style="position: sticky; left: 0; background-color: #fff;" class="text-right">
                                <div class="form-group mb-0">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <input :class="{ 'is-invalid': room_form?.errors.has('room_name') }" v-if="editRoom && i == selectedIndex" type="text" v-model="data.room_name" class="px-2 form-control" style="width: 150px">
                                        <span v-else>{{ data.room_name }}</span>
                                    </div>
                                    <has-error v-if="editRoom && i == selectedIndex" :form="room_form" field="room_name" />
                                </div>
                                <div class="btn-group">
                                        <i  @click="editData(data, i)" class="fas fa-edit cursor-pointer btn btn-info btn-sm" style="cursor:pointer"/>
                                        <i class="fas fa-trash btn btn-danger btn-sm" style="cursor:pointer" @click="deleteData(data.id)"/>
                                </div>
                            </td>
                        </tr>
                        <tr v-show="!loading && addRoomList.length==0">
                            <td colspan="12">
                                <EmptyTable />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <router-link :to="{ name: 'hotel' }" class="btn btn-primary float-right">
                    {{ $t('common.submit') }}
                </router-link>
            </div>

            <div class="modal" id="newRoomAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div v-if="currentRoom" class="modal-dialog modal-width modal-lg modal-dialog-centered" role="document" style="max-width: 800px">
                    <div class="modal-lg modal-content" style="max-width: 800px;">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Edit room</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <form role="form" @submit.prevent="saveRoomData(currentRoom.id , currentRoom)" @keydown="room_form.onKeydown($event)">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-lg-3">
                                                <label for="name">{{ $t('rooms.common.name') }}
                                                    <span class="required">*</span></label>
                                                <input :class="{ 'is-invalid': room_form?.errors.has('room_name') }" type="text" v-model="currentRoom.room_name" class="px-2 form-control">
                                                <has-error :form="room_form" field="room_name" />
                                            </div>
                                            <div class="form-group col-lg-3">
                                                <label for="name">{{ $t('rooms.common.room_category') }}
                                                    <span class="required">*</span></label>
                                                    
                                                <select :class="{ 'is-invalid': room_form?.errors.has('room_categorary') }" class="form-control" v-model="currentRoom.hotel_room_category_id">
                                                    <option v-for="(item) in hotelCategoryItems" :key="item.id" :value="item.id">{{ item.room_category.room_category_name }}</option>
                                                 </select>
                                                <has-error :form="room_form" field="room_categorary" />
                                            </div>
                                           
                                            <div class="form-group col-lg-3">
                                                <label for="name">{{ $t('common.floor_no') }}
                                                    <span class="required">*</span></label>
                                                <select v-model="currentRoom.floor_id" class="form-control">
                                                    <option v-for="(item) in floorItems" :key="item.id" :value="item.id">{{ item.floorsName }}</option>
                                                </select>
                                                <has-error :form="room_form" field="floor_id" />
                                            </div>
                                            <div class="form-group col-lg-3">
                                                <label for="name">{{ $t('sidebar.bedType') }}
                                                    <span class="required">*</span></label>
                                                <select :class="{ 'is-invalid': room_form?.errors.has('bed_type_id') }"  v-model="currentRoom.bed_type_id" class="form-control">
                                                    <option v-for="(item) in bedTypeItems" :key="item.id" :value="item.id">{{ item.bedtypetitle }}</option>
                                                </select>
                                                <has-error :form="room_form" field="bed_type_id" />
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="name">{{ $t('common.room_description') }}
                                                    </label>
                                                <textarea type="text" v-model="currentRoom.roomdescription" class="px-2 form-control" />
                                                <has-error :form="room_form" field="extra_person_capacity" />
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <v-button :loading="room_form.busy" class="btn btn-primary">
                                            <i class="fas fa-save"/> {{ $t('common.save') }}
                                        </v-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from 'vuex'
import Form from 'vform'
import Multiselect from 'vue-multiselect'
import _ from 'lodash';

export default {
    components: {
        Multiselect,
    },
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems', 'bedTypeItems', 'floorItems']),
    },
    data() {
        return {
            room_form: new Form({
                room_name: '',
                room_categorary: '',
                hotel_room_category_id:'',
                hotel_id: '',
                bed_type_id: '',
                roomdescription: '',
                floor_id: '',
                room_id: '',
            }),
            currentRoom: null,
            room_category_id: '',
            room_facility_id: '',
            room_hotel_id: '',
            room_bed_type_id: '',
            room_floor_id: '',
            addRoomList: [],
            loading: true,
            isOpenModal: false,
            modalTitle: '',
            facilityRatePrice: '',
            editRoom: false,
            selectedIndex: -1,
            room_category_id: '',
            newRoomCount: 1,
        }
    },
    created() {
        this.getHotelCategoryItems()
    },
    mounted() {
        this.getRoomList()
        this.getRoomBedType()
        this.getRoomFloor()
    },
    methods: {
        currentTabPre() {
            this.$emit('currentTabPre')
        },

        async getHotelCategoryItems() {
            await this.$store.dispatch("operations/getHotelCategoryData", {
                path: "/api/room/category/hotel?hotel_id="+this.$route.params.slug,
            });
        },

        async getRoomList() {
            axios.get(
                window.location.origin +
                '/api/room?perPage=100&hotelId='+this.$route.params.slug
            ).then((res)=>{
                this.addRoomList = res.data.data
                this.loading = false
            })
        },

        async getRoomBedType() {
            await this.$store.dispatch("operations/getBedTypeData", {
                path: "/api/bed/list",
            });
        },

        async getRoomFloor() {
            await this.$store.dispatch("operations/getFloorData", {
                path: "/api/floor/list",
            });
        },

        editData(data, i) {
            this.currentRoom = data;
            this.selectedIndex = i;
            // this.editRoom = true;
            
            $('#newRoomAdd').modal('show');
        },

        closeModal() {
            $('#newRoomAdd').modal('hide');
            this.currentRoom = null;
        },

        async saveRoomData(id, data) {
            this.room_form.room_id = data.id
            this.room_form.room_name = data.room_name
            this.room_form.room_categorary = data.hotel_room_category_id
            this.room_form.hotel_id = this.$route.params.slug
            this.room_form.bed_type_id = data.bed_type_id
            this.room_form.roomdescription = data.roomdescription
            this.room_form.floor_id = data.floor_id

            await this.room_form
                .post(window.location.origin + '/api/room/edit')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('rooms.roomAdd.edit.success_msg'),
                    })
                    this.editRoom = false
                    this.selectedIndex = -1
                    this.closeModal()
                    this.getRoomList()
                })
                .catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },

        async deleteData(slug) {
            Swal.fire({
                title: this.$t("common.delete_title"),
                text: this.$t("purchases.list.index.delete_warning"),
                type: "warning",
                showCancelButton: true,
                confirmButtonText: this.$t("common.delete_confirm_text"),
            }).then((result) => {
                // Send request to the server
                if (result.value) {
                    this.$store
                        .dispatch("operations/hotelDeleteData", {
                            path: "/api/room/delete/",
                            slug: slug,
                        })
                        .then((response) => {
                            if (response === true) {
                                Swal.fire(
                                    this.$t("common.deleted"),
                                    this.$t("common.delete_success"),
                                    "success"
                                );
                                this.getRoomList()
                            } else {
                                Swal.fire(
                                    this.$t("common.failed"),
                                    this.$t(response.message),
                                    "warning"
                                );
                            }
                        });
                }
            });
        },
        async createRoom() {
            const lastObject = this.addRoomList[0];
            const copiedObject = { ...lastObject };
            this.room_form.room_id = null;
            this.room_form.room_name = `Room ${this.addRoomList.length + 1}`
            this.room_form.room_categorary = copiedObject.hotel_room_category_id
            this.room_form.hotel_id = this.$route.params.slug
            this.room_form.bed_type_id = copiedObject.bed_type_id
            this.room_form.roomdescription = copiedObject.roomdescription
            this.room_form.floor_id = copiedObject.floor_id

            await this.room_form
                .post(window.location.origin + '/api/room/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('rooms.roomAdd.create.success_msg'),
                    })
                    this.getRoomList()
                })
                .catch((error) => {
                    console.log(error.response)
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        }
    },
}

</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
.modal-body {
    max-height: 85vh !important;
    overflow: none !important;
}
.modal-content{
    margin: 0 !important;
}

:deep(.multiselect__tags) {
    min-height: 38px !important;
    border: none !important;
    padding: 4px 40px 0 4px !important;
}
:deep(.multiselect__placeholder){
    margin-bottom: 4px !important;
    padding-top: 4px !important;
}
:deep(.multiselect__single) {
    margin-bottom: 0px !important;
    margin-top: 4px !important;
}
:deep(.multiselect){
    width: auto;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
    min-height: 38px !important;
}

.room-input{
    width: 150px;
}
</style>
