<template>
  <div class="mb-50">
      <!-- breadcrumbs Start -->
      <!-- <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" /> -->
      <!-- breadcrumbs end -->
      <div class="row">
          <div class="col-lg-12">
              <div class="card">
                  <!-- /.card-header -->
                  <div class="card-body position-relative">


                      <div class="row">

              <div class="col-6 col-xl-8 mb-2 date_icon">
                              <i class="fa-regular fa-calendar-days"></i>
                <date-range-picker ref="picker" opens="right" :singleDatePicker="false" :showWeekNumbers="false"
                                      :showDropdowns="true" :autoApply="true" v-model="dateRange" @update="updateValues"
                                      :linkedCalendars="true" class="c-w-100">
                                  <template v-slot:input="picker" style="min-width: 350px">
                                      {{ picker.startDate | startDate }} -
                                      {{ picker.endDate | endDate }}
                                  </template>
                              </date-range-picker>

                          </div>
                      </div>

                      <table-loading v-show="loading" />
                      <div id="printMe" class="table-responsive table-custom mt-3">
                          <table class="table">
                              <thead>
                              <tr>
                                  <th>{{ $t("common.s_no") }}</th>
                                  <th>Order Date</th>
                                  <th>Shop</th>
                                  <th>Order Id</th>
                                  <th class="text-right">Amount</th>
                                  <!-- <th class="text-right">Order Status</th>
                                  <th class="text-right">Payment Status</th> -->
                                  <!-- <th>Type</th>
                                  <th>Source</th>
                                  <th class="text-right no-print">
                                      {{ $t("common.action") }}
                                  </th> -->
                              </tr>
                              </thead>
                              <tbody>

                              <tr v-if="restaurantOrders && restaurantOrders?.length" v-for="(data, i) in restaurantOrders" :key="i">
                                  <template v-if="!selectedType || (data.customer && selectedType?.id == 1) || (data.room && selectedType?.id == 2)">
                                      <td>
                                          <span>{{ i + 1 }}</span>
                                      </td>
                                      <td>{{ data?.date | moment("Do MMM, YYYY") }}</td>
                                      <td>{{ data?.shop?.shop_name }}</td>
                                      <td>{{ data?.orderId }}</td>
                                      <td class="text-right">{{ data?.totalAmount | forBalanceSheetCurrencyDecimalOnly }}</td>
                                  </template>
                              </tr>
                              <tr v-show="!loading && !restaurantOrders?.length">
                                  <td colspan="12">
                                      <EmptyTable />
                                  </td>
                              </tr>
                              </tbody>
                          </table>
                          <small class="ml-2">
                      <i>
                      All prices are in {{'' | defaultwithCurrency}}
                      </i>
                  </small>
                      </div>
                  </div>

                  <div class="dtable-footer pb-3">
                      <!-- pagination-start -->
                      <div></div>
                      <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
                                  class="justify-flex-end" @paginate="paginate" />
                      <!-- pagination-end -->
                  </div>

              </div>
          </div>
      </div>

      <Modal v-if="showSmallInvoiceModal && allData" class="restaurant_bill">

          <h5 slot="header" class="no-print">Restaurant Bill</h5>
          <div class="w-100" slot="body">
              <div id="invoice-POS">
                  <div style="max-width: 400px; margin: 0px auto">
                      <div class="info">
                          <div v-if="appInfo.blackLogo" class="pos-logo">
                              <img :src="appInfo.blackLogo" width="100px"/>
                          </div>
                          <h2 v-else class="text-center">{{ appInfo.companyName }}</h2>
                          <div class="text-bold text-center text-lg" v-show="allData.hotel.hotel_name"
                          > {{ allData.hotel.hotel_name }} <br
                          /></div>
                          <p>
                              <span v-show="appInfo.gstPrefix">GST NO : {{ appInfo.gstPrefix}} <br/></span>
                              <span
                              >{{ $t("common.date") }} : {{ allData.date }} <br
                              /></span>
                              <span v-show="allData.hotel.hotel_address"
                              >{{ $t("common.address") }} : {{ allData.hotel.hotel_address }} <br
                              /></span>
                              <span v-show="allData.hotel.hotel_email"
                              >{{ $t("common.phone") }} : {{ allData.hotel.hotel_phone }} <br
                              /></span>
                              <span v-show="allData.hotel.hotel_phone"
                              >{{ $t("common.email") }} : {{ allData.hotel.hotel_email }} <br
                              /></span>
                              <span v-show="allData?.customer?.name"
                              >{{ $t("common.client") }} : {{ allData.customer?.name }} <br
                              /></span>
                          </p>
                      </div>

                      <table class="table_data">
                          <tbody>
                             <template v-for="(data, i) in allData.items.items">

                          <tr :key="i">
                              <td colspan="3">
                                  <span>
                                    <span>{{ data.name }}</span><br/>
                                    <span class="pqty">{{ data.quantity }} x {{ data.price | withCurrency }}
                                        {{addonPrice(data) > 0 ? '+' + addonPrice(data) : ''}} </span>
                                  </span>
                              </td>
                              <td style="text-align: right; vertical-align: bottom">
                                  {{ itemSubtotal(data) | withCurrency }}
                              </td>
                          </tr>
                          <br/>

                              <span v-if="data.addOn.length">AddOn Items</span>
                              <tr v-for="(addon, index) in data.addOn" :key="addon.item_name + index" v-if="data.addOn.length">
                                  <td colspan="3">
                                      <span>
                                    <span>{{ addon?.item_name }}</span><br/>
                                    <span class="pqty">{{ data.quantity }} x {{ addon?.item_price | withCurrency }}
                                        {{addonPrice(data) > 0 ? '+' + addonPrice(data) : ''}} </span>
                                  </span>
                                      </td>
                                  <td class="text-right">{{ data.quantity * addon?.item_price | withCurrency }} </td>
                              </tr>

                          </template>
                          <tr style="margin-top: 10px">
                              <td colspan="3" class="total">{{ $t("common.subtotal") }}</td>
                              <td style="text-align: right" class="total">
                                  {{ allData.items.subtotal | withCurrency }}
                              </td>
                          </tr>
                          <tr v-if="allData.items.discount" style="margin-top: 10px">
                              <td colspan="3" class="total">{{ $t("common.discount") }}</td>
                              <td style="text-align: right" class="total">
                                  {{ allData.items.discount | withCurrency }}
                              </td>
                          </tr>
                          <tr v-if="allData.items.tax" style="margin-top: 10px">
                              <td colspan="3" class="total">{{ $t("common.tax") }}</td>
                              <td style="text-align: right" class="total">
                                  {{ allData.items.tax | withCurrency }}
                              </td>
                          </tr>
                          <tr style="margin-top: 10px">
                              <td colspan="3" class="total">{{ $t("common.total") }}</td>
                              <td style="text-align: right" class="total">
                                  {{ allData.items.total | withCurrency }}
                              </td>
                          </tr>
                          <tr>
                              <td colspan="3" class="total">{{ $t("common.paid") }}</td>
                              <td style="text-align: right" class="total" v-if="allData.payment_status == 1">
                                  -{{ allData.items.total | withCurrency }}
                              </td>
                              <td style="text-align: right" class="total" v-else="allData.payment_status == 0">
                                  0
                              </td>
                          </tr>
                          <tr>
                              <td colspan="3" class="total">{{ $t("common.due") }}</td>
                              <td style="text-align: right" class="total" v-if="allData.payment_status == 1">
                                  {{ parseFloat(allData.items.total) - parseFloat(allData.items.total) | withCurrency }}
                              </td>
                              <td style="text-align: right" class="total" v-else="allData.payment_status == 0">
                                  {{ parseFloat(allData.items.total) }}
                              </td>
                          </tr>
                          </tbody>
                      </table>
                      <div id="legalcopy" class="ml-2 mb-4">
                          <p class="legal">
                              <strong>{{ $t("pos.receipt_text") }}</strong>
                          </p>
                          <div id="bar" style="overflow: hidden">
                              <barcode
                                      width="2"
                                      height="25"
                                      fontSize="15"
                                      :value="allData.orderId"
                              >
                                  {{ $t("common.rendering_fails") }}
                              </barcode>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="pos-modal-footer no-print flex" slot="modal-footer">

                  <button
                          @click="printInvoice"
                          class="modal-default-button btn btn-info"
                  >
                      {{ $t("common.print") }}
                  </button>

              <button
                      class="modal-default-button btn btn-danger"
                      @click="closeReceiptModal"
              >
                  {{ $t("common.close") }}
              </button>
          </div>
      </Modal>

      <Modal v-if="showSmallKotModal && allData" class="KOTPOS">

          <h5 slot="header" class="no-print">KOT Receipt</h5>
          <div class="w-80" slot="body">
              <div id="KOT-POS">
                  <div style="max-width: 400px; margin: 0px auto">
                      <div class="info">

                          <!-- <div v-if="appInfo.blackLogo" class="pos-logo">
                              <img :src="appInfo.blackLogo" width="100px"/>
                          </div>
                          <h2 v-else class="text-center">{{ appInfo.companyName }}</h2> -->
                          <!-- <div class="text-bold text-center text-lg" v-show="allData.hotel.hotel_name"
                          > {{ allData.hotel.hotel_name }} <br
                          /></div> -->
                          <p class="d-flex justify-content-between">
                              <!-- <span v-show="appInfo.gstPrefix">GST NO : {{ appInfo.gstPrefix}} <br/></span> -->
                              <span
                              >{{ $t("common.date") }} : {{ allData.date }} <br
                              /></span>

                              <!-- <span v-show="allData.hotel.hotel_email"
                              >{{ $t("common.phone") }} : {{ allData.hotel.hotel_phone }} <br
                              /></span> -->
                              <!-- <span v-show="allData.hotel.hotel_phone"
                              >{{ $t("common.email") }} : {{ allData.hotel.hotel_email }} <br
                              /></span> -->
                              <span v-show="allData?.customer?.name"
                              >{{ $t("common.client") }} : {{ allData.customer?.name }} <br
                              /></span>
                              <span v-show="allData.orderId"
                              >Order No : {{ allData.orderId }} <br
                              /></span>
                          </p>
                      </div>

                      <table class="table_data">
                          <tbody>
                              <template v-for="(data, i) in allData.items.items">

                              <tr :key="i">
                                  <td colspan="3">
                                      <span>{{ data.name }}</span>
                                  </td>
                                  <td style="text-align: right; vertical-align: bottom">
                                      {{ data.quantity }}
                                  </td>
                              </tr>
                              <br/>
                                  <span v-if="data.addOn.length">AddOn Items</span>
                                  <tr v-for="(addon, index) in data.addOn" :key="addon.item_name + index" v-if="data.addOn.length">
                                      <td colspan="3">{{ addon?.item_name }}</td>
                                      <td class="text-right">{{ data.quantity }} </td>
                                  </tr>

                          </template>
                          </tbody>
                      </table>
                      <!-- <div id="legalcopy" class="ml-2 mb-4">
                          <p class="legal">
                              <strong>{{ $t("pos.receipt_text") }}</strong>
                          </p>
                          <div id="bar" style="overflow: hidden">
                              <barcode
                                      width="2"
                                      height="25"
                                      fontSize="15"
                                      :value="allData.orderId"
                              >
                                  {{ $t("common.rendering_fails") }}
                              </barcode>
                          </div>
                      </div> -->
                  </div>
              </div>
          </div>
          <div class="pos-modal-footer no-print flex" slot="modal-footer">
                  <button
                          @click="kotInvoice"
                          class="modal-default-button btn btn-info"
                  >
                      {{ $t("common.print") }}
                  </button>

              <button
                      class="modal-default-button btn btn-danger"
                      @click="closeKOTModal"
              >
                  {{ $t("common.close") }}
              </button>
          </div>
      </Modal>

      <VModal v-if="showCancelModal" v-model="showCancelModal" @close="showCancelModal = false">

          <h3 slot="title" class="text-center">Are you sure?</h3>

          <div class="mt-1">
              <p class="text-center">Are you sure you want to cancel this order?</p>

              <table class="table_data">
                          <tbody>

                          <tr v-for="(data, i) in allData.items.items" :key="i">
                              <td colspan="3">
                                  <span>
                                    <span>{{ data.name }}</span><br/>
                                    <span class="pqty">{{ data.quantity }} x {{ data.price | withCurrency }}
                                        {{addonPrice(data) > 0 ? '+' + addonPrice(data) : ''}} </span>
                                  </span>
                              </td>
                              <td style="text-align: right; vertical-align: bottom">
                                  {{ itemSubtotal(data) | withCurrency }}
                              </td>
                          </tr>

                          <tr style="margin-top: 10px">
                              <td colspan="3" class="total">{{ $t("common.subtotal") }}</td>
                              <td style="text-align: right" class="total">
                                  {{ allData.items.subtotal | withCurrency }}
                              </td>
                          </tr>
                          <tr v-if="allData.items.discount" style="margin-top: 10px">
                              <td colspan="3" class="total">{{ $t("common.discount") }}</td>
                              <td style="text-align: right" class="total">
                                  {{ allData.items.discount | withCurrency }}
                              </td>
                          </tr>
                          <tr v-if="allData.items.tax" style="margin-top: 10px">
                              <td colspan="3" class="total">{{ $t("common.tax") }}</td>
                              <td style="text-align: right" class="total">
                                  {{ allData.items.tax | withCurrency }}
                              </td>
                          </tr>
                          <tr style="margin-top: 10px">
                              <td colspan="3" class="total">{{ $t("common.total") }}</td>
                              <td style="text-align: right" class="total">
                                  {{ allData.items.total | withCurrency }}
                              </td>
                          </tr>
                          <tr>
                              <td colspan="3" class="total">{{ $t("common.paid") }}</td>
                              <td style="text-align: right" class="total" v-if="allData.payment_status == 1">
                                  -{{ allData.items.total | withCurrency }}
                              </td>
                              <td style="text-align: right" class="total" v-else="allData.payment_status == 0">
                                  0
                              </td>
                          </tr>
                          <tr>
                              <td colspan="3" class="total">{{ $t("common.due") }}</td>
                              <td style="text-align: right" class="total" v-if="allData.payment_status == 1">
                                  {{ parseFloat(allData.items.total) - parseFloat(allData.items.total) | withCurrency }}
                              </td>
                              <td style="text-align: right" class="total" v-else="allData.payment_status == 0">
                                  {{ parseFloat(allData.items.total) }}
                              </td>
                          </tr>
                          </tbody>
                      </table>
                      <div class="form-group">
                          <label for="note">{{ $t("common.note") }}</label>
                          <textarea id="note" v-model="cancel_note" class="form-control" :placeholder="$t('common.note_placeholder')" />
                      </div>

          </div>
          <div slot="modal-footer">
              <button @click="cancelOrder(allData.id)" class="btn btn-primary">Cancel Order</button>
          </div>
      </VModal>


  </div>
</template>

<script>
  import { mapGetters } from "vuex";
  import axios from "axios";
  import Form from "vform";
  import moment from "moment";
  import i18n from "~/plugins/i18n";
  import DateRangePicker from "vue2-daterange-picker";
  import VueBarcode from "vue-barcode";

  export default {
      metaInfo() {
          return { title: this.$t("restaurant.index.page_title") };
      },
        components: {
           DateRangePicker,
           barcode: VueBarcode,
       },
      data: () => ({
          breadcrumbsCurrent: "restaurant.order.breadcrumbs_current",
          breadcrumbs: [
              {
                  name: "restaurant.order.breadcrumbs_first",
                  url: "home",
              },
              {
                  name: "Restaurant",
                  url: "",
              },
          ],
          perPage: 10,
          pagination: {currentPage: 1},
          restaurantOrders: [],
          loading: false,
          minDate: moment(new Date()).format("YYYY-MM-DD"),
          maxDate: '',
          dateRange: {
              startDate: "",
              endDate: "",
          },
          selectedType: null,
          showSmallInvoiceModal: false,
          showSmallKotModal:false,
          showCancelModal:false,
          allData:[],
          cancel_note:'',
          orderType:  [
              {
                  id: 1,
                  name: 'Direct customer orders'
              },
              {
                  id: 2,
                  name: 'Room orders'
              }
          ],
          showModal:false,
          accounts:[],
          cancel_form: new Form({
              cancel_note:'',
          }),
          payment_form: new Form({
              hotel_id: '',
              invoice_slug: "",
              invoice_id:"",
              client: "",
              netTotal: 0,
              tax: 0,
              subTotal: 0,
              selectedProducts: [],
              isRestaurantOrder: 1,
              paidAmount: 0,
              account: "",
              totalPaid: "",
              dueAmount: "",
              poReference: "",
              paymentTerms: "",
              addPayment: "",
              chequeNo: "",
              receiptNo: "",
              date: new Date().toISOString().slice(0, 10),
              note: "",
              status: 1,
          }),
          showOrderStatusModal:false,
          order_status_form: new Form({
              order_status:'',
          }),
          order_status:'',
          orderStatus:[
              {
                  'status_name' : 'Pending',
                  'id':0,
              },
              {
                  'status_name' : 'In-Process',
                  'id':3,
              },
              {
                  'status_name' : 'Completed',
                  'id':1,
              },
              {
                  'status_name' : 'Cancel',
                  'id':2,
              }
          ]
      }),
      watch: {
          async selectedHotel() {
              this.loading = true;
              await this.getData();
              this.loading = false;
          },
      },
      filters: {
          startDate(val) {
              return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.from");
          },
          endDate(val) {
              return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.to");
          },
      },
      // Map Getters
      computed: {
          ...mapGetters("operations", ["appInfo", "restaurant", "selectedHotel"]),
      },
      created() {
          this.getData(1);
         
      },
      methods: {
          
         
          async cancelOrder(id){
              if(this.cancel_note == ''){
                  return toast.fire({ type: "error", title: 'Please enter Cancel note' });
              }else{
                  this.cancel_form.cancel_note = this.cancel_note;
              }
              await this.cancel_form
              .post(window.location.origin + '/api/food/order/cancel/'+id)
              .then((response) => {

                  if (response.data.success === true) {
                      Swal.fire(
                          this.$t("common.canceled"),
                          this.$t("common.cancel_success"),
                          "success"
                      );
                      this.showCancelModal = false
                      this.getData(1);
                  } else {
                      Swal.fire(
                          this.$t("common.failed"),
                          this.$t(response.message),
                          "warning"
                      );
                      this.showCancelModal = false
                  }
              });
          },
          async updateValues() {
              this.dateRange.startDate = moment(this.dateRange.startDate).format(
                  "YYYY-MM-DD"
              );
              this.dateRange.endDate = moment(this.dateRange.endDate).format(
                  "YYYY-MM-DD"
              );
              await this.getData();
          },
          sourceName(data) {
            if (data?.customer) {
                return {
                    name: data?.customer?.name,
                    slug: data?.customer?.slug,
                    type: 'client'
                }
            } if (data?.room) {
                return {
                    name: data?.room?.room_name,
                    slug: data?.bookingId,
                    type: 'room'
                }
              }
          },
          async getData(page) {
              this.pagination.currentPage = page;
              try {
                  const { data } = await axios.get(
                      window.location.origin + "/api/food/order?page=" + (this.pagination?.currentPage || 1)
                      +"&startDate="+this.dateRange.startDate+"&endDate="+this.dateRange.endDate+"&shop_id="+this.selectedHotel
                  );

                  this.restaurantOrders = data.data;
                  this.pagination = data.meta;

              } catch (e) {
                  console.log(e)
              }
          },
           // print invoice
           printInvoice() {
              var divContents = document.getElementById("invoice-POS").innerHTML;
              var a = window.open("", "", "height=500, width=500");
              a.document.write(
                  '<link rel="stylesheet" href="/css/pos_print.css"><html>'
              );
              a.document.write("<body >");
              a.document.write(divContents);
              a.document.write("</body></html>");
              a.document.close();
              a.print();
          },
          kotInvoice() {
              var divContents = document.getElementById("KOT-POS").innerHTML;
              var a = window.open("", "", "height=500, width=500");
              a.document.write(
                  '<link rel="stylesheet" href="/css/pos_print.css"><html>'
              );
              a.document.write("<body >");
              a.document.write(divContents);
              a.document.write("</body></html>");
              a.document.close();
              a.print();
          },
          closeReceiptModal() {
              this.showSmallInvoiceModal = false;
              this.allData = null;
          },
          closeKOTModal() {
              this.showSmallKotModal = false;
              this.allData = null;
          },
          addonPrice(item) {
              let addonTotal = 0;
              if (item.addon?.length > 0) {
                  item.addon?.forEach(add => {
                      addonTotal += parseFloat(add.price);
                  })
              }

              return addonTotal;
          },

          itemSubtotal(item) {
              let addonTotal = 0;
              if (item.addon?.length > 0) {
                  item.addon?.forEach(add => {
                      addonTotal += parseFloat(add.price);
                  })
              }
              return parseFloat(item.quantity * item.price || 0) + addonTotal;
          },

          async paginate(page) {
              this.pagination.currentPage = page;
              await this.getData(page);
          },
      },
  };
</script>
<style lang="scss" scoped>

#KOT-POS p {
  font-size: 12px;
  color: #000;
  line-height: 17PX;
  margin-bottom: 0PX;
  font-weight: 500;
}
#KOT-POS td,
#KOT-POS th,
#KOT-POS tr,
#KOT-POS table {
      border-collapse: collapse;
  }

  #KOT-POS tr {
      border-bottom: 2px dotted #05070b;
  }

  #KOT-POS table {
      width: 100%;
  }

  #KOT-POS tfoot tr th:first-child {
      text-align: left;
  }

  #KOT-POS .info {
      margin-bottom: 20px;
  }

  #KOT-POS .info > p {
      margin-top: 20px;
  }

#invoice-POS td,
#invoice-POS th,
#invoice-POS tr,
#invoice-POS table {
      border-collapse: collapse;
  }

  #invoice-POS tr {
      border-bottom: 2px dotted #05070b;
  }

  #invoice-POS table {
      width: 100%;
  }

  #invoice-POS tfoot tr th:first-child {
      text-align: left;
  }

  #invoice-POS .info {
      margin-bottom: 20px;
  }

  #invoice-POS .info > p {
      margin-top: 20px;
  }

  #legalcopy {
      margin-top: 5mm;
  }

  #legalcopy p {
      text-align: center;
  }

  #bar {
      text-align: center;
  }

  .total {
      font-weight: bold;
      font-size: 12px;
  }

  span.pqty {
      display: block;
      line-height: 15px;
      font-size: 12px;
      font-weight: 500;
      margin-bottom: 5px;
  }
</style>
