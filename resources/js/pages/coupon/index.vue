<template>
    <div class="mb-50">
      <!-- breadcrumbs Start -->
      <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
      <!-- breadcrumbs end -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card custom-card w-100">
            <div class="card-header setings-header">
              <div class="col-xl-4 col-4">
                <h3 class="card-title">
                  {{ $t("coupon.index.page_title") }}
                </h3>
              </div>
              <div class="col-xl-8 col-8 float-right text-right">
                <div class="btn-group c-w-100">
                    <!-- @click="refreshTable()"  -->
                  <a href="#" v-tooltip="'Refresh'" class="btn btn-success">
                    <i class="fas fa-sync"></i>
                  </a>
                  <!-- @click="print" -->
                  <a v-tooltip="$t('common.print_table')" class="btn btn-info">
                    <i class="fas fa-print"></i>
                  </a>
                  <a @click="addCoupon" class="btn btn-primary" v-if="$can('coupon-create')">
                    {{ $t("common.create") }}
                    <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                  </a>
                </div>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body position-relative">
              <div class="row">
                <div class="col-6 col-xl-4 mb-2">
                    <!-- @reset-pagination="resetPagination()" @reload="reload"  -->
                  <search v-model="query" />
                </div>
              </div>
              <table-loading v-show="loading" />
              <div class="table-responsive table-custom mt-3" id="printMe">
                <table class="table">
                  <thead>
                    <tr>
                      <th>{{ $t("common.s_no") }}</th>
                      <th>{{ $t("common.agent_name") }}</th>
                      <th>{{ $t("common.coupon_code") }}</th>
                      <th>{{ $t("common.discount_type") }}</th>
                      <th>{{ $t("common.discount") }}</th>
                      <th>{{ $t("common.hotel") }}</th>
                      <th>{{ $t("common.time") }}</th>
                      <th>Status</th>
                      <th v-if="$can('coupon-edit') || $can('client-delete')" class="text-right no-print">
                        {{ $t("common.action") }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-show="items?.length" v-for="(data, i) in items" :key="i">
                      <td>
                        <span v-if="pagination && pagination.current_page > 1">
                          {{
                            pagination.per_page * (pagination.current_page - 1) +
                            (i + 1)
                          }}
                        </span>
                        <span v-else>{{ i + 1 }}</span>
                      </td>
                      <td>
                        {{ data.client_name}}
                      </td>
                      <td>
                        {{ data.coupon}}
                      </td>
                      <td>
                        {{ (data.discount_type == '2') ? 'Percentage' : 'Fixed' }}
                      </td>
                      <td>{{ data.discount_value }}</td>
                      <td>{{ data.hotels }}</td>
                      <td>
                        {{ data.expiry == null ? `${data.start_time} - ${data.end_time}` : 
                          (data.expiry == 'Never End')  ? `${data.start_time} - ${data.expiry}` : 
                          (data.expiry == 'All Time') ? `${data.expiry}` : '' }}
                      </td>
                      <td>
                        <label class="switch">
                          <input type="checkbox" :checked="data.status=='active'" @change="toggleCheckbox($event,data.id)"/>
                          <div class="slider round"></div>
                        </label>
                      </td>
                      <td v-if="$can('coupon-edit') || $can('coupon-delete')" class="text-right no-print">
                        <div class="btn-group">
                         
                          <a v-if="$can('coupon-edit')" v-tooltip="$t('common.edit')" href="#" class="btn btn-info btn-sm"
                            @click="editCoupon(data.id)">
                            <i class="fas fa-edit" />
                          </a>
                          <a v-if="$can('coupon-delete')" v-tooltip="$t('common.delete')" href="#" class="btn btn-danger btn-sm"
                            @click="deleteCoupon(data.id)">
                            <i class="fas fa-trash" />
                          </a>
                        </div>
                      </td>
                    </tr>
                    <tr v-show="!loading && !items?.length">
                      <td colspan="9">
                        <EmptyTable />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <div class="dtable-footer">
                <div class="form-group row display-per-page" v-if="pagination && pagination.last_page > 1">
                  <label>{{ $t("per_page") }} </label>
                  <div>
                    <select @change="updatePerPager" v-model="perPage" class="form-control form-control-sm ml-1">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                  </div>
                </div>
                <!-- pagination-start -->
                <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
                  class="justify-flex-end" @paginate="paginate" />
                <!-- pagination-end -->
  
              </div>
            </div>
          </div>
        </div>


        <div class="modal" id="newCouponAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-width modal-lg modal-dialog-centered" role="document" style="max-width: 800px">
                    <div class="modal-lg modal-content" style="max-width: 800px;">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">{{ modalTitle }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <form role="form" @submit.prevent="saveCoupons()" @keydown="coupon_form.onKeydown($event)">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <label for="client_id">Select Agent
                                                    <span class="required">*</span></label>
                                                 <multiselect @select="changeClient" v-model="coupon_form.client_id" :options="agents" :show-labels="false" tag-placeholder=""
                                                             placeholder="Search a Agent" class="form-control" label="name" track-by="name"
                                                             :class="{ 'is-invalid': coupon_form?.errors.has('client_id') }"></multiselect>
                                                <has-error :form="coupon_form" field="client_id" />
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="coupon_code">Coupon Code
                                                    <span class="required">*</span></label>
                                                <input step="0.1" :class="{ 'is-invalid': coupon_form?.errors.has('coupon_code') }" type="text" 
                                                v-model="coupon_form.coupon_code" class="px-2 form-control" placeholder="Enter Coupon Code">
                                                <has-error :form="coupon_form" field="coupon_code" />
                                            </div>
                                        </div>
                                        <div class="row">
                                          <div class="form-group col-lg-6">
                                            <label for="discountType">{{ $t('common.discount_type') }}</label>
                                                <multiselect v-model="coupon_form.discount_type" :options="discountType" :show-labels="false" tag-placeholder=""
                                                             placeholder="Search a Discount Type" class="form-control" label="title" track-by="title"
                                                             :class="{ 'is-invalid': coupon_form?.errors.has('discount_type') }"></multiselect>
                                          </div>
                                          <div class="form-group col-lg-6">
                                            <label for="discount_value">{{ $t('common.discount') }}</label>
                                              <input id="discount_value" v-model="coupon_form.discount_value" type="text" class="form-control" :class="{ 'is-invalid': coupon_form?.errors.has('discount_value') }"
                                                      name="discount_value" :placeholder="$t('common.discount_placeholder')"/>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="form-group col-lg-4">
                                            
                                                <label for="expiry">Select Duration
                                                    <span class="required">*</span></label>
                                                <select :class="{ 'is-invalid': coupon_form?.errors.has('expiry') }" class="form-control" v-model="coupon_form.expiry" placeholder="Select Expiry" @change="setExpiry">
                                                      <option value="">Select Expiry</option>
                                                      <option value="All Time">All Time</option>
                                                      <option value="Never End">Never End</option>
                                                 </select>
                                                <has-error :form="coupon_form" field="client_id" />
                                            
                                          </div>
                                          <div class="form-group col-lg-4">
                                            <label for="start_date">Start Date</label>
                                              <input id="start_date" v-model="coupon_form.start_date" type="date" class="form-control" :class="{ 'is-invalid': coupon_form?.errors.has('start_date') }"
                                                      name="start_date" placeholder="Start Date" :disabled="startDate"/>
                                          </div>
                                          <div class="form-group col-lg-4">
                                            <label for="end_date">End Date</label>
                                              <input id="end_date" v-model="coupon_form.end_date" type="date" class="form-control" :class="{ 'is-invalid': coupon_form?.errors.has('end_date') }"
                                                      name="end_date" placeholder="End Date" :disabled="endDate"/>
                                          </div>    
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                              <label for="hotel" class="d-block"></label>
                                              <input type="checkbox" v-model="isCheck" @change="setHotelVal"> Select All Hotels
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                              <label for="hotel" class="d-block">Select Hotel
                                              <span class="required">*</span></label>
                                            <multiselect id="hotel" v-model="coupon_form.hotel_id" :options="hotelItems" :show-labels="false" tag-placeholder="" :taggable="false" placeholder="Search an hotel"
                                              class="form-control" label="hotel_name" track-by="hotel_name" :allowEmpty="false" :multiple="true"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <v-button :loading="coupon_form.busy" class="btn btn-primary">
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
  import Form from "vform";
  import axios from "axios";
  import moment from "moment";
  import { mapGetters } from "vuex";
  import Multiselect from 'vue-multiselect';
  import i18n from "~/plugins/i18n";
  
  
  export default {
    middleware: ["auth", "check-permissions"],
    // middleware: ["auth"],
    metaInfo() {
      return { title: this.$t("coupon.index.page_title") };
    },
    components: {
      Multiselect,
    },
   data: () => ({
      query: "",
      perPage: 10,
      breadcrumbsCurrent: "coupon.index.breadcrumbs_current",
      agents:[],
      breadcrumbs: [
        {
          name: "coupon.index.breadcrumbs_first",
          url: "home",
        },
        {
          name: "coupon.index.breadcrumbs_active",
          url: "",
        },
      ],
      discountType: [
          {
              title: 'Fixed',
              id: 1,
          },
          {
              title: 'Percentage',
              id: 2,
          },
      ],
      coupon_form: new Form({
         hotel_id:[],
         client_id:'',
         coupon_code:'',
         discount_type:'',
         discount_value:'',
         start_date:'',
         end_date:'',
         expiry:'',
      }),
      update_status: new Form({
        status : '',
      }),
      coupon_id:'',
      startDate:false,
      endDate:false,
      modalTitle:"Add Coupon",
      isCheck:false,
    }),
    computed: {
        ...mapGetters("operations", ["items", "loading", "pagination", "appInfo","selectedHotel","hotelItems"]),
    },
    created(){
        this.getAgents();
        this.getHotels();
        this.getCoupons();
    },
    watch:{
      query: function (newQ, oldQ) {
      if (newQ !== "") {
         this.searchData();
      }else{
        this.getCoupons();
      }
    },
    },
    methods: {
        changeClient(){
          
          const filteredData = this.items.filter(item => item.client_id == this.coupon_form.client_id.clientID)[0];
          if(typeof filteredData !== 'undefined'){
            const discountType = this.discountType.filter(item => item.id == filteredData.discount_type)
            const selectedHotel = this.hotelItems.filter(item => filteredData.hotel_id.includes(item.id));

            this.coupon_form.hotel_id = selectedHotel;
            this.coupon_form.coupon_code = filteredData.coupon;
            this.coupon_form.discount_type = discountType;
            this.coupon_form.discount_value = filteredData.discount_value;
            this.coupon_form.start_date = filteredData.start_time;
            this.coupon_form.end_date = filteredData.end_time;
            this.coupon_form.expiry = filteredData.expiry;
            this.coupon_id = filteredData.id;
            this.modalTitle = "Edit Coupon";

            if(filteredData.expiry == ''){
              this.startDate=false;
              this.endDate=false;
            } else if(filteredData.expiry == 'All Time'){
              this.startDate=true;
              this.endDate=true;
            } else if(filteredData.expiry == 'Never End'){
              this.startDate=false;
              this.endDate=true;
            }

          } else {
            this.coupon_form.hotel_id = [];
            this.coupon_form.coupon_code = '';
            this.coupon_form.discount_type = '';
            this.coupon_form.discount_value = '';
            this.coupon_form.start_date = '';
            this.coupon_form.end_date = '';
            this.coupon_form.expiry = '';
            this.coupon_id = '';
            this.startDate=false;
            this.endDate=false;
            this.modalTitle = "Add Coupon";
          }
        },
        setHotelVal(){
          if(this.isCheck){
            this.coupon_form.hotel_id = this.hotelItems;
          } else {
            this.coupon_form.hotel_id = [];
          }
        },
        async getAgents() {
          await axios.get('/api/coupon/agents').then((response) => {
              this.agents = response?.data?.data;
          });
        },
        async getHotels() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/hotel',
            });
            this.$store.state.operations.loading = false;
        },
        async getCoupons(){
          this.$store.state.operations.loading = true;
            let currentPage = this.pagination ? this.pagination.current_page : 1;
            await this.$store.dispatch("operations/fetchData", {
              path: "/api/coupon?page=",
              currentPage: currentPage + "&perPage=" + this.perPage,
            });
        },
        async toggleCheckbox(event,id){
          
          this.update_status.status = (event.target.checked) ? 'active' : 'inactive',

          await this.update_status
                .post(window.location.origin + '/api/coupon/update-status/' + id)
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Coupon Status Update Successfully'),
                    })
                })
                .catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },
        async searchData() {
          this.$store.state.operations.loading = true;
          let currentPage = this.pagination ? this.pagination.current_page : 1;
          await this.$store.dispatch("operations/searchData", {
            path: "/api/coupon/search",
            term: this.query,
            currentPage: currentPage + "&perPage=" + this.perPage,
          });
        },
        updatePerPager() {
          this.pagination.current_page = 1;
          this.getCoupons();
        },
        closeModal() {
            $('#newCouponAdd').modal('hide');
        },
        addCoupon(){
          this.coupon_form.hotel_id = [];
          this.coupon_form.client_id = '';
          this.coupon_form.coupon_code = '';
          this.coupon_form.discount_type = '';
          this.coupon_form.discount_value = '';
          this.coupon_form.start_date = '';
          this.coupon_form.end_date = '';
          this.coupon_form.expiry = '';
          this.coupon_id = '';
          this.startDate=false;
          this.endDate=false;
          this.modalTitle = "Add Coupon";

          $('#newCouponAdd').modal('show');
        },
        editCoupon(id){
          const filteredData = this.items.filter(item => item.id == id)[0];
          const discountType = this.discountType.filter(item => item.id == filteredData.discount_type)
          const selectedHotel = this.hotelItems.filter(item => filteredData.hotel_id.includes(item.id));
          const selectedAgent = this.agents.filter(item => item.id == filteredData.client_id);

          this.coupon_form.hotel_id = selectedHotel;
          // this.coupon_form.client_id = filteredData.client_id;
          this.coupon_form.client_id = selectedAgent
          this.coupon_form.coupon_code = filteredData.coupon;
          this.coupon_form.discount_type = discountType;
          this.coupon_form.discount_value = filteredData.discount_value;
          this.coupon_form.start_date = filteredData.start_time;
          this.coupon_form.end_date = filteredData.end_time;
          this.coupon_form.expiry = filteredData.expiry;
          this.coupon_id = id;
          this.modalTitle = "Edit Coupon";

          if(filteredData.expiry == ''){
            this.startDate=false;
            this.endDate=false;
          } else if(filteredData.expiry == 'All Time'){
            this.startDate=true;
            this.endDate=true;
          } else if(filteredData.expiry == 'Never End'){
            this.startDate=false;
            this.endDate=true;
          }

          $('#newCouponAdd').modal('show');
        },

        setExpiry(event){
          if(event.target.value == ''){
            this.startDate=false;
            this.endDate=false;
          } else if(event.target.value == 'All Time'){
            this.startDate=true;
            this.endDate=true;
          } else if(event.target.value == 'Never End'){
            this.startDate=false;
            this.endDate=true;
          }
        },
        async saveCoupons(){
          // 
          if(this.coupon_id == ''){
            await this.coupon_form
                .post( window.location.origin + '/api/coupon')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Coupon Add Successfully'),
                    })
                   
                    this.closeModal()
                    this.getCoupons()
                })
                .catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
          } else {
            await this.coupon_form
                .patch(window.location.origin + '/api/coupon/' + this.coupon_id)
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Coupon Edit Successfully'),
                    })
                   
                    this.closeModal()
                    this.getCoupons()
                })
                .catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
          }
            
        },

        async deleteCoupon(id){
          Swal.fire({
            title: this.$t("common.delete_title"),
            text: this.$t("clients.index.delete_warning"),
            type: "warning",
            showCancelButton: true,
            confirmButtonText: this.$t("common.delete_confirm_text"),
          }).then((result) => {
            // Send request to the server
            if (result.value) {
              this.$store
                .dispatch("operations/deleteData", {
                  path: "/api/coupon/",
                  slug: id,
                })
                .then((response) => {
                  if (response === true) {
                    Swal.fire(
                      this.$t("common.deleted"),
                      this.$t("common.delete_success"),
                      "success"
                    );
                    this.getCoupons();
                  } else {
                    Swal.fire(
                      this.$t("common.failed"),
                      this.$t("clients.index.delete_failed"),
                      "warning"
                    );
                  }
                });
            }
          });
        }
    },
  };
  </script>
<style src="../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
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

:deep(.multiselect__single) {
    text-overflow: ellipsis !important;
    overflow: hidden !important;
}

@media (min-width: 1200px) {
    .modal-dialog.modal-width {
        max-width: 1000px;
    }
}

:deep(.search-btn) {
    left: 10px !important;
}

:deep(.dropdown-toggle::after) {
    display: none !important;
}
.switch {
  position: relative;
  display: inline-block;
  width: 43px;
  height: 17px;
}

.switch input {
  display: none;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 15px;
  width: 15px;
  left: 1px;
  bottom: 1px;
  background-color: white;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

input:checked + .slider {
  background-color: #101010;
}

input:focus + .slider {
  box-shadow: 0 0 1px #101010;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>