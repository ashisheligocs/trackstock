<template>
  
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :current="breadcrumbsCurrent" />
    <div class="row">
    <router-link :to="{ name: 'home' }" class="btn btn-secondary float-right mt-2 mb-2">
            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
      </router-link>
      </div>
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <!-- <h3 class="card-title">
              {{ $t('cashbook.transfers.create.form_title') }}
            </h3> -->
           
          </div>


          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="saveTransfer" @keydown="form.onKeydown($event)">
            <div class="card-body">
              
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="availableBalanceInQr">Available Balance In QR</label>
                  <input id="availableBalanceInQr" v-model="form.availableBalanceQr" type="number" step="any"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('availableBalanceInQr'),}" name="availableBalanceInQr" readonly />
                  <has-error :form="form" field="availableBalanceInQr" />
                </div>
                <div class="form-group col-md-6">
                  <label for="amount">{{ $t('common.amount') }}
                    <span class="required">*</span></label>
                  <input id="amount" v-model="form.amountqr" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('amount') }" name="amount" placeholder="Enter an amount" readonly/>
                  <has-error :form="form" field="amount" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="availableBalance">{{ $t('common.available_balance') }} In Cash</label>
                  <input id="availableBalance" v-model="form.availableBalanceCash" type="number" step="any"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('availableBalance'), }" 
                    name="availableBalance" readonly />
                  <has-error :form="form" field="availableBalance" />
                </div>
                <div class="form-group col-md-6">
                  <label for="amount">{{ $t('common.amount') }}
                    <span class="required">*</span></label>
                  <input id="amount" v-model="form.amountcash" type="number"   step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('amount') }" name="amount" placeholder="Enter an amount" />
                  <has-error :form="form" field="amount" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="date">{{ $t('common.date') }}</label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" min="" :readonly="readonly" />
                  <has-error :form="form" field="date" />
                </div>
               
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

              <p class="text-bold text-md" style="text-align:center;">Receipt Number : 0005</p>
              <p class="text-bold text-md" style="text-align:center;">Incharge Name : {{ user.name }}</p>
              <p class="text-bold text-md" style="text-align:center;">Date : {{ this.todaydate }}</p>
              <p class="text-bold text-md" style="text-align:center;">Shop Name : {{ selectedHotelId.shop_name }}</p>
              <p class="text-bold text-md" style="text-align:center;">Cash Received : {{ form.amount }}</p>
              <p class="text-bold text-md" style="text-align:center;">Remarks : <textarea class="form-control"
                  v-model="billContent"></textarea></p>
              <p class="text-bold text-md" style="text-align:center;">Sign : ______________ </p>

            </div>
          </div>
        </div>
      </div>
      <div class="pos-modal-footer no-print" slot="modal-footer">
        <button @click="connectAndPrint">Print Bill</button>
        <!-- <textarea v-model="billContent" placeholder="Enter bill content"></textarea> -->
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
    todaydate: new Date().toLocaleDateString() + ' ' + new Date().toLocaleTimeString(),
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
      cashAccount: null,
      bankAccount: null,
      toAccount: null,
      transferReason: '',
      availableBalanceCash: 0,
      availableBalanceQr: 0,
      amountcash: '',
      amountqr: '',
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
      shop_id: '',
    }),
    selectedHotelId: null,
    loading: true,
    readonly: false,
    showPrintModal: false,
  }),
  watch: {
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
    await this.getHotelDataList();
    if (this.selectedHotel && this.selectedHotel !== 'all') {
      this.hotelItems.forEach((hotel) => {
        if (hotel.id == this.selectedHotel) this.selectedHotelId = hotel
      })
    }
    if (this.user.back_days != '' && this.user.back_days != 'All') {
      var today = new Date();
      var minDate = new Date();

      minDate.setDate(today.getDate() - this.user.back_days);
      document.getElementById("date").min = minDate.toISOString().split("T")[0];
    }
    if (this.user?.roles[0] == "incharge") {
      this.readonly = true;
    }
    await this.getAccoutns();
  },
  methods: {

    async connectAndPrint() {
      const receiptheader = document.querySelector('#invoice-POS .info .text-bold:nth-of-type(1)').innerText.trim();
      const shopName = document.querySelector('#invoice-POS .info .text-bold:nth-of-type(2)').innerText.trim();
      const date = document.querySelector('#invoice-POS .info  .text-bold:nth-of-type(3)').innerText.trim();
      const cashReceived = document.querySelector('#invoice-POS .info  .text-bold:nth-of-type(4)').innerText.trim();
      const cashBalance = document.querySelector('#invoice-POS .info  .text-bold:nth-of-type(5)').innerText.trim();
      const by = document.querySelector('#invoice-POS .text-bold:nth-of-type(6)').innerText.trim();
      const billContent = this.billContent;
      const sign = document.querySelector('#invoice-POS .text-bold:nth-of-type(7)').innerText.trim();
      var printableContent = '';
      if (receiptheader) printableContent += `${receiptheader.trim()}\n`;
      if (shopName) printableContent += `${shopName.trim()}\n`;
      if (date) printableContent += `${date.trim()}\n`;
      if (cashReceived) printableContent += `${cashReceived.trim()}\n`;
      if (cashBalance) printableContent += `${cashBalance.trim()}\n`;
      if (by) printableContent += `${by.trim()}\n ${this.billContent.trim()}`;
      if (billContent) printableContent += `\n`;
      if (sign) printableContent += `${sign.trim()}\n`;
      console.log(printableContent)
      try {
        this.device = await navigator.bluetooth.requestDevice({
          filters: [{ services: [this.printerServiceUUID] }]
        });
        this.server = await this.device.gatt.connect();
        this.characteristic = await this.server.getPrimaryService(this.printerServiceUUID)
          .then(service => service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb'));
        let encoder = new TextEncoder("utf-8");
        let encodedBillContent = encoder.encode(printableContent + '\u000A\u000D');
        await this.characteristic.writeValue(encodedBillContent);
        console.log('Bill sent to printer successfully.');
      } catch (error) {
        console.error('Error printing bill:', error);
      }
    },
    async getHotelDataList() {
      await this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
    // get all accounts
    async getAccoutns() {
      await this.$store.dispatch('operations/allData', {
        path: (typeof this.selectedHotelId?.id === 'undefined') ? '/api/all-accounts' : '/api/all-accounts?shop_id=' + this.selectedHotelId?.id,
      })
      // assign default account
      if (this.items && this.items.length > 0) {
        let defaultAccountSlug = this.appInfo.defaultAccountSlug;

        this.form.cashAccount = this.items.find(account => account.slug == defaultAccountSlug);
        this.form.bankAccount = this.items.find(account => account.slug == 'bank');
        this.form.toAccount = this.items.find(account => account.slug == 'in-charge');

        this.updateBalance()
      }
    },

    // update available balance
    updateBalance() {
      this.form.availableBalanceCash = this.form.cashAccount.availableBalance;
      this.form.availableBalanceQr = this.form.bankAccount.availableBalance;
      this.form.amountqr = this.form.bankAccount.availableBalance;
    },

    // save transfer
    async saveTransfer() {
      if (!this.selectedHotelId) return toast.fire({ type: 'error', title: 'Please select hotel' })
      if (this.form.amountcash > this.form.availableBalanceCash) {
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