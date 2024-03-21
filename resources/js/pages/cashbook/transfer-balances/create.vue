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
              {{ $t('cashbook.transfers.create.form_title') }}
            </h3>
            <router-link :to="{ name: 'transferBalances.index' }" class="btn btn-secondary float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="saveTransfer" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row" v-if="!readonly">
                <div class="form-group col-md-6">
                  <label for="hotel" class="d-block">{{ $t('sidebar.shops') }}
                    <span class="required">*</span></label>
                  <v-select
                    class="flex-grow-1"
                    v-model="selectedHotelId"
                    :options="hotelItems"
                    label="shop_name"
                    name="shop_id"
                    placeholder="Search a Shop"
                    @input="getAccoutns"
                  />
                </div>
                <div class="form-group col-md-6">
                  <label for="transferReason">{{ $t('cashbook.common.transfer_reason') }}
                    <span class="required">*</span></label>
                  <input type="text" id="transferReason" v-model="form.transferReason" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('transferReason') }"
                    :placeholder="$t('common.return_reason_placeholder')" name="transferReason" />
                  <has-error :form="form" field="transferReason" />
                </div>
              </div>
              <div class="row" v-if="items && !readonly">
                <div class="form-group col-md-6">
                  <label for="fromAccount">{{ $t('cashbook.common.from_account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.fromAccount" :options="items" label="ledgerName"
                    :class="{ 'is-invalid': form.errors.has('fromAccount') }" name="fromAccount"
                    :placeholder="$t('common.account_placeholder')" @input="updateBalance"/>
                  <has-error :form="form" field="fromAccount" />
                </div>
                <div class="form-group col-md-6">
                  <label for="toAccount">{{ $t('cashbook.common.to_account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.toAccount" :options="items" label="ledgerName"
                    :class="{ 'is-invalid': form.errors.has('toAccount') }" name="toAccount"
                    :placeholder="$t('common.account_placeholder')"/>
                  <has-error :form="form" field="toAccount" />
                </div>
              </div>
              <div class="row" v-if="form.fromAccount">
                <div class="form-group col-md-6">
                  <label for="availableBalance">{{
                      $t('common.available_balance')
                  }}</label>
                  <input id="availableBalance" v-model="form.availableBalance" type="number" step="any"
                    class="form-control" :class="{
                      'is-invalid': form.errors.has('availableBalance'),
                    }" name="availableBalance" readonly />
                  <has-error :form="form" field="availableBalance" />
                </div>
                <div class="form-group col-md-6">
                  <label for="amount">{{ $t('common.amount') }}
                    <span class="required">*</span></label>
                  <input id="amount" v-model="form.amount" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('amount') }" name="amount" placeholder="Enter an amount" />
                  <has-error :form="form" field="amount" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="date">{{ $t('common.date') }}</label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" min="" :readonly="readonly"/>
                  <has-error :form="form" field="date" />
                </div>
                <div class="form-group col-md-6" v-if="!readonly">
                  <label for="status">{{ $t('common.status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">
                      {{ $t('common.active') }}
                    </option>
                    <option value="0">
                      {{ $t('common.in_active') }}
                    </option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
              </div>
              <div class="form-group" v-if="!readonly">
                <label for="note">{{ $t('common.note') }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('common.note_placeholder')" />
                <has-error :form="form" field="note" />
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
   
    <Modal v-if="showPrintModal">
            <h5 slot="header" class="no-print">Receipt</h5>
            <div class="w-100" slot="body">
                <div id="invoice-POS">
                    <div style="max-width: 400px; margin: 0px auto">
                        <div class="info">
                            
                              <p class="text-bold text-md" style="text-align:center;">Eligo creative services</p> 
                              <p class="text-bold text-md" style="text-align:center;">Shop Name : {{ selectedHotelId.shop_name }}</p> 
                              <p class="text-bold text-md" style="text-align:center;"> Date : {{ form.date }}</p>
                              <p class="text-bold text-md" style="text-align:center;">Cash Received : {{ form.amount }}</p>
                              <p class="text-bold text-md" style="text-align:center;">Cash Balance : {{ form.availableBalance }}</p>
                              <p class="text-bold text-md" style="text-align:center;">By : {{ user.name }}</p>
                              
                        </div>
                    </div>
                </div>
            </div>
            <div class="pos-modal-footer no-print" slot="modal-footer">
                <div>
                  <div id="cards">
                   
                    <!-- <textarea v-model="billContent" placeholder="Enter bill content"></textarea> -->
                    <button @click="connectAndPrint">Print Bill</button>
                  </div>
                    <!-- <button @click="printInvoice" class="modal-default-button btn btn-info">
                        {{ $t("common.print") }}
                    </button> -->
                    <!-- <paper-card heading="Bluetooth Printer">
                      <div class="card-content">
                        <paper-progress id="progress" indeterminate></paper-progress>
                      </div>
                    </paper-card>
                    <paper-card>
                      <div class="card-content">
                        <paper-textarea id="message" label="Enter Message">
                          Shop Name : {{ selectedHotelId.shop_name }} <br>
                          Date : {{ form.date }} <br>
                          Cash Received : {{ form.amount }} <br>
                          By : {{ user.name }}
                        </paper-textarea>
                      </div>
                    </paper-card>

                    <paper-card>
                      <div class="card-content">
                        <paper-button id="print" raised class="colorful">Print</paper-button>
                      </div>
                    </paper-card>

                    <paper-dialog id="dialog">
                      <h2>Error</h2>
                      <p>Could not connect to bluetooth device!</p>
                    </paper-dialog> -->
                </div>
                <button class="modal-default-button btn btn-danger" @click="closeReceiptModal">
                    {{ $t("common.close") }}
                </button>
            </div>
        </Modal>
  </div>

</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'
import VModal from "../../../components/VModal"; 
// import PaperCard from './PaperCard.vue';
export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('cashbook.transfers.create.page_title') }
  },
  components: {
    VModal,
  },
  data: () => ({
      billContent: '',
      printerServiceUUID: '000018f0-0000-1000-8000-00805f9b34fb', 
      device: null,
      server: null,
      characteristic: null,
      breadcrumbsCurrent: 'cashbook.transfers.create.breadcrumbs_current',
    breadcrumbs: [
      {
        name: 'cashbook.transfers.create.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'cashbook.transfers.create.breadcrumbs_second',
        url: '',
      },
      {
        name: 'cashbook.transfers.create.breadcrumbs_third',
        url: 'transferBalances.index',
      },
      {
        name: 'cashbook.transfers.create.breadcrumbs_active',
        url: '',
      },
    ],
    form: new Form({
      fromAccount: null,
      toAccount: null,
      transferReason: '',
      availableBalance: 0,
      amount: '',
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
      shop_id: '',
    }),
    selectedHotelId: null,
    loading: true,
    readonly: false,
    showPrintModal:false,
  }),
  watch:{
      selectedHotel: function () {
          this.getHotelDataList();
          this.getAccoutns();
          this.form.reset();
      },
  },
  computed: {
    ...mapGetters('operations', ['items', 'appInfo', "selectedHotel", "hotelItems"]),
    ...mapGetters({
            user: "auth/user",
        }),
  },
  async created() {


    'use strict';
      document.addEventListener('WebComponentsReady', function() {
        let progress = document.querySelector('#progress');
        let dialog = document.querySelector('#dialog');
        let message = document.querySelector('#message');
        let printButton = document.querySelector('#print');
        let printCharacteristic;
        let index = 0;
        let data;
        progress.hidden = true;

        let image = document.querySelector('#image');
        // Use the canvas to get image data
        let canvas = document.createElement('canvas');
        // Canvas dimensions need to be a multiple of 40 for this printer
        canvas.width = 120;
        canvas.height = 120;
        let context = canvas.getContext("2d");
        context.drawImage(image, 0, 0, canvas.width, canvas.height);
        let imageData = context.getImageData(0, 0, canvas.width, canvas.height).data;

        function getDarkPixel(x, y) {
          // Return the pixels that will be printed black
          let red = imageData[((canvas.width * y) + x) * 4];
          let green = imageData[((canvas.width * y) + x) * 4 + 1];
          let blue = imageData[((canvas.width * y) + x) * 4 + 2];
          return (red + green + blue) > 0 ? 1 : 0;
        }

        function getImagePrintData() {
          if (imageData == null) {
            console.log('No image to print!');
            return new Uint8Array([]);
          }
          // Each 8 pixels in a row is represented by a byte
          let printData = new Uint8Array(canvas.width / 8 * canvas.height + 8);
          let offset = 0;
          // Set the header bytes for printing the image
          printData[0] = 29;  // Print raster bitmap
          printData[1] = 118; // Print raster bitmap
          printData[2] = 48; // Print raster bitmap
          printData[3] = 0;  // Normal 203.2 DPI
          printData[4] = canvas.width / 8; // Number of horizontal data bits (LSB)
          printData[5] = 0; // Number of horizontal data bits (MSB)
          printData[6] = canvas.height % 256; // Number of vertical data bits (LSB)
          printData[7] = canvas.height / 256;  // Number of vertical data bits (MSB)
          offset = 7;
          // Loop through image rows in bytes
          for (let i = 0; i < canvas.height; ++i) {
            for (let k = 0; k < canvas.width / 8; ++k) {
              let k8 = k * 8;
              //  Pixel to bit position mapping
              printData[++offset] = getDarkPixel(k8 + 0, i) * 128 + getDarkPixel(k8 + 1, i) * 64 +
                          getDarkPixel(k8 + 2, i) * 32 + getDarkPixel(k8 + 3, i) * 16 +
                          getDarkPixel(k8 + 4, i) * 8 + getDarkPixel(k8 + 5, i) * 4 +
                          getDarkPixel(k8 + 6, i) * 2 + getDarkPixel(k8 + 7, i);
            }
          }
          return printData;
        }

        function handleError(error) {
          console.log(error);
          progress.hidden = true;
          printCharacteristic = null;
          dialog.open();
        }

        function sendNextImageDataBatch(resolve, reject) {
          // Can only write 512 bytes at a time to the characteristic
          // Need to send the image data in 512 byte batches
          if (index + 512 < data.length) {
            printCharacteristic.writeValue(data.slice(index, index + 512)).then(() => {
              index += 512;
              sendNextImageDataBatch(resolve, reject);
            })
            .catch(error => reject(error));
          } else {
            // Send the last bytes
            if (index < data.length) {
              printCharacteristic.writeValue(data.slice(index, data.length)).then(() => {
                resolve();
              })
              .catch(error => reject(error));
            } else {
              resolve();
            }
          }
        }

        function sendImageData() {
          index = 0;
          data = getImagePrintData();
          return new Promise(function(resolve, reject) {
            sendNextImageDataBatch(resolve, reject);
          });
        }

        function sendTextData() {
          // Get the bytes for the text
          let encoder = new TextEncoder("utf-8");
          // Add line feed + carriage return chars to text
          let text = encoder.encode(message.value + '\u000A\u000D');
          return printCharacteristic.writeValue(text).then(() => {
            console.log('Write done.');
          });
        }

        function sendPrinterData() {
          // Print an image followed by the text
          sendImageData()
          .then(sendTextData)
          .then(() => {
            progress.hidden = true;
          })
          .catch(handleError);
        }

        printButton.addEventListener('click', function () {
          progress.hidden = false;
          if (printCharacteristic == null) {
            navigator.bluetooth.requestDevice({
              filters: [{
                services: ['000018f0-0000-1000-8000-00805f9b34fb']
              }]
            })
            .then(device => {
              console.log('> Found ' + device.name);
              console.log('Connecting to GATT Server...');
              return device.gatt.connect();
            })
            .then(server => server.getPrimaryService("000018f0-0000-1000-8000-00805f9b34fb"))
            .then(service => service.getCharacteristic("00002af1-0000-1000-8000-00805f9b34fb"))
            .then(characteristic => {
              // Cache the characteristic
              printCharacteristic = characteristic;
              sendPrinterData();
            })
            .catch(handleError);
          } else {
            sendPrinterData();
          }
        });
      });
   
    await this.getHotelDataList();
    if (this.selectedHotel && this.selectedHotel !== 'all') {
        this.hotelItems.forEach((hotel) => {
            if (hotel.id == this.selectedHotel) this.selectedHotelId = hotel
        })
    }
    if(this.user.back_days != '' && this.user.back_days != 'All'){
            var today = new Date();
            var minDate = new Date();

            minDate.setDate(today.getDate() - this.user.back_days);
            document.getElementById("date").min = minDate.toISOString().split("T")[0];
        }
      if(this.user?.roles[0] == "incharge"){
        this.readonly = true;
    } 
    await this.getAccoutns();   
  },
  methods: {

    async connectAndPrint() {
        try {
            // Get the content from the invoice HTML structure
            const companyName = document.querySelector('#invoice-POS .text-bold:nth-of-type(1)').innerText.trim();
            const shopName = document.querySelector('#invoice-POS .text-bold:nth-of-type(2)').innerText.trim();
            const date = document.querySelector('#invoice-POS .text-bold:nth-of-type(3)').innerText.trim();
            const cashReceived = document.querySelector('#invoice-POS .text-bold:nth-of-type(4)').innerText.trim();
            const cashBalance = document.querySelector('#invoice-POS .text-bold:nth-of-type(5)').innerText.trim();
            const by = document.querySelector('#invoice-POS .text-bold:nth-of-type(6)').innerText.trim();
            
            // Construct the printable content
            let printableContent = '';
            if (companyName) printableContent += `${companyName.trim()}\n`;
            if (shopName) printableContent += `${shopName.trim()}\n`;
            if (date) printableContent += `${date.trim()}\n`;
            if (cashReceived) printableContent += `${cashReceived.trim()}\n`;
            if (cashBalance) printableContent += `${cashBalance.trim()}\n`;
            if (by) printableContent += `${by.trim()}\n`;
            
            // Send the content to the printer
            this.device = await navigator.bluetooth.requestDevice({
                filters: [{ services: [this.printerServiceUUID] }]
            });
            this.server = await this.device.gatt.connect();
            this.characteristic = await this.server.getPrimaryService(this.printerServiceUUID)
                .then(service => service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb'));
            let encoder = new TextEncoder("utf-8");
            let encodedPrintableContent = encoder.encode(printableContent + '\u000A\u000D');
            await this.characteristic.writeValue(encodedPrintableContent);
            console.log('Bill sent to printer successfully.');
        } catch (error) {
            console.error('Error printing bill:', error);
        }
    },


    async getHotelDataList () {
      await this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
    // get all accounts
    async getAccoutns() {
      await this.$store.dispatch('operations/allData', {
        path: (typeof this.selectedHotelId?.id === 'undefined') ? '/api/all-accounts' : '/api/all-accounts?shop_id='+this.selectedHotelId?.id,
      })
      // assign default account
      if (this.items && this.items.length > 0) {
        let defaultAccountSlug = this.appInfo.defaultAccountSlug;
        
        this.form.fromAccount = this.items.find(account => account.slug == defaultAccountSlug);
        this.form.toAccount = this.items.find(account => account.slug == 'in-charge');
                
        this.updateBalance()
      }
    },

    // update available balance
    updateBalance() {
      return (this.form.availableBalance =
        this.form.fromAccount.availableBalance)
    },

    // save transfer
    async saveTransfer() {
      if (!this.selectedHotelId) return toast.fire({ type: 'error', title: 'Please select hotel' })
      if(this.form.amount > this.form.availableBalance){
        return toast.fire({ type: 'error', title: 'Please enter valid amount' })
      }

      this.form.shop_id = this.selectedHotelId?.id;
      await this.form
        .post(window.location.origin + '/api/balance-transfers')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('cashbook.transfers.create.success_msg'),
          })
          this.showPrintModal = true;
          // this.$router.push({ name: 'transferBalances.index' })
        })
        .catch((err) => {
          let message = err.response?.data?.message || this.$t('common.error_msg');
          toast.fire({ type: 'error', title: message })
        })
    },

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

            closeReceiptModal() {
              this.showPrintModal = false;
              this.$router.push({ name: 'transferBalances.index' })
            },
  },

  
}
</script>
<style scoped>

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

#invoice-POS .info>p {
    margin-top: 20px;
}
</style>