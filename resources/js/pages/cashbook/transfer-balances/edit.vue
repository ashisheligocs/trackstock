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
              {{ $t('cashbook.transfers.edit.form_title') }}
            </h3>
            <router-link :to="{ name: 'transferBalances.index' }" class="btn btn-secondary float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updateTransfer" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="hotel" class="d-block">{{ $t('sidebar.shops') }}
                    <span class="required">*</span></label>
                  <v-select
                    class="flex-grow-1"
                    v-model="selectedHotelId"
                    :options="hotelItems"
                    label="shop_name"
                    name="hotel_id"
                    placeholder="Search a hotel"
                    disabled
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
              <div class="row" v-if="items">
                <div class="form-group col-md-6">
                  <label for="fromAccount">{{ $t('cashbook.common.from_account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.fromAccount" :options="items" label="ledgerName"
                    :class="{ 'is-invalid': form.errors.has('fromAccount') }" name="fromAccount"
                    :placeholder="$t('common.account_placeholder')" @input="updateBalance" />
                  <has-error :form="form" field="fromAccount" />
                </div>
                <div class="form-group col-md-6">
                  <label for="toAccount">{{ $t('cashbook.common.to_account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.toAccount" :options="items" label="ledgerName"
                    :class="{ 'is-invalid': form.errors.has('toAccount') }" name="toAccount"
                    :placeholder="$t('common.account_placeholder')" disabled />
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
                    :class="{ 'is-invalid': form.errors.has('amount') }" name="amount"
                    :placeholder="$t('common.amount_placeholder')" :min="form.minBalance" />
                  <has-error :form="form" field="amount" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="date">{{ $t('common.date') }}</label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" min=""/>
                  <has-error :form="form" field="date" />
                </div>
                <div class="form-group col-md-6">
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
              <div class="form-group">
                <label for="note">{{ $t('common.note') }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('common.note_placeholder')" />
                <has-error :form="form" field="note" />
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
import { mapGetters } from 'vuex'
export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('cashbook.transfers.edit.page_title') }
  },
  data: () => ({
    breadcrumbsCurrent: 'cashbook.transfers.edit.breadcrumbs_current',
    breadcrumbs: [
      {
        name: 'cashbook.transfers.edit.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'cashbook.transfers.edit.breadcrumbs_second',
        url: '',
      },
      {
        name: 'cashbook.transfers.edit.breadcrumbs_third',
        url: 'transferBalances.index',
      },
      {
        name: 'cashbook.transfers.edit.breadcrumbs_active',
        url: '',
      },
    ],
    form: new Form({
      fromAccount: null,
      toAccount: null,
      transferReason: '',
      availableBalance: 0,
      amount: '',
      minBalance: '',
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
      shop_id: null,
    }),
    selectedHotelId: null,
    loading: true,
  }),
  watch:{
    selectedHotelId: function (newval, oldval){
       this.getAccoutns(newval.id);
    }
    // selectedHotel: function () {
    //    this.getHotelDataList();
    //    this.form.reset();
    // },
  },
  computed: {
    ...mapGetters('operations', ['items','appInfo', "selectedHotel", "hotelItems"]),
    ...mapGetters({
            user: "auth/user",
        }),
  },
  created() {
    // console.log(this.selectedHotelId);
    this.getAccoutns()
    this.getTransfer()
    this.getHotelDataList();

    if(this.user.back_days != '' && this.user.back_days != 'All'){
            var today = new Date();
            var minDate = new Date();

            minDate.setDate(today.getDate() - this.user.back_days);
            document.getElementById("date").min = minDate.toISOString().split("T")[0];
        }
  },
  methods: {
    async getHotelDataList () {
      await this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
    // get all accounts
    async getAccoutns(hotelId = '') {

      await this.$store.dispatch('operations/allData', {
        path: (hotelId == '') ? '/api/all-accounts' : '/api/all-accounts?shop_id='+hotelId,
      })

      if (this.items && this.items.length > 0 && hotelId != "") {
        let defaultAccountSlug = this.appInfo.defaultAccountSlug;
        this.form.fromAccount = this.items.find(account => account.slug == defaultAccountSlug);
        this.updateBalance()
      }
    },

    // get the transfer
    async getTransfer() {
      const { data } = await this.form.get(
        window.location.origin +
        '/api/balance-transfers/' +
        this.$route.params.slug
      )
      this.form.transferReason = data.data.reason
      this.form.fromAccount = data.data.fromAccount
      this.form.toAccount = data.data.toAccount
      this.form.availableBalance = data.data.fromAccount.availableBalance
      this.form.minBalance = data.data.fromAccount.amount
      this.form.amount = data.data.amount
      this.form.date = data.data.date
      this.form.note = data.data.note
      this.form.status = data.data.status
      this.selectedHotelId = data.data.shop
    },

    // update available balance
    updateBalance() {
      return (this.form.availableBalance =
        this.form.fromAccount.availableBalance)
    },

    // update transfer
    async updateTransfer() {
      if (!this.selectedHotelId) toast.fire({ type: 'error', title: 'Please select hotel' })
      this.form.shop_id = this.selectedHotelId?.id;
      await this.form
        .patch(
          window.location.origin +
          '/api/balance-transfers/' +
          this.$route.params.slug
        )
        .then((response) => {
          toast.fire({
            type: 'success',
            title: this.$t('cashbook.transfers.edit.success_msg'),
          })
          this.$router.push({ name: 'transferBalances.index' })
        })
        .catch((err) => {
          let message = err.response?.data?.message || this.$t('common.error_msg');
          toast.fire({ type: 'error', title: message })
        })
    },
  },
}
</script>
