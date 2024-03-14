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
                {{ $t('cashbook.journal_entry.update.page_title') }}
              </h3>
              <router-link :to="{ name: 'journalEntry.index' }" class="btn btn-secondary float-right">
                <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
              </router-link>
            </div>
            <!-- /.card-header -->
            <form role="form" @submit.prevent="updateJournalEntry" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="date">{{ $t('common.date') }}</label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" min=""/>
                  <has-error :form="form" field="date" />
                </div>

                <div class="form-group col-md-6">
                  <label for="hotel" class="d-block">{{ $t('sidebar.hotel') }}
                    <span class="required">*</span></label>
                  <v-select
                    class="flex-grow-1"
                    v-model="form.hotel_id"
                    :options="hotelItems"
                    label="hotel_name"
                    name="hotel_id"
                    placeholder="Search a hotel"
                  />
                </div>
              </div>

            <template>
                <hot-table :settings="hotSettings" ref="hotTable"></hot-table>
            </template>

            <div class="mt-2 col-md-12">
                <table class="table text-right">
                    <tbody>
                        <tr>
                        <td></td>
                        <td class="text-right"><strong>Debit</strong></td>
                        <td class="text-right"><strong>Credit</strong></td>
                        </tr>
                    <tr>
                        <td><strong>Total :</strong>
                        </td>
                        <td class="total_debit">{{ (totalDebit) | forBalanceSheetCurrency}}</td>
                        <td class="total_credit">{{ (totalCredit) | forBalanceSheetCurrency }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <label for="note">{{ $t('common.note') }}</label>
                <input type="hidden" name="totalamount" v-model="form.amount">
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
    </div>
  </template>

  <script>
  import Form from 'vform'
  import { mapGetters } from 'vuex'

  import { HotTable } from '@handsontable/vue';
  import { registerAllModules } from 'handsontable/registry';
  import 'handsontable/dist/handsontable.full.css';

  registerAllModules();

  export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
      return { title: this.$t('cashbook.journal_entry.update.page_title') }
    },
    components: {
      HotTable
    },
    data: () => ({
      breadcrumbsCurrent: 'cashbook.journal_entry.update.breadcrumbs_current',
      breadcrumbs: [
        {
          name: 'cashbook.journal_entry.update.breadcrumbs_first',
          url: 'home',
        },
        {
          name: 'cashbook.journal_entry.update.breadcrumbs_second',
          url: '',
        },
        {
          name: 'cashbook.journal_entry.update.breadcrumbs_third',
          url: 'journalEntry.index',
        },
        {
          name: 'cashbook.journal_entry.update.breadcrumbs_active',
          url: '',
        },
      ],
      
      loading: true,
      form: new Form({
        date: new Date().toISOString().slice(0, 10),
        id: '',
        note: '',
        hotel_id: null,
        amount:0,
        tableData : []
      }),
      totalDebit:0,
      totalCredit:0,
      hotSettings: {
        contextMenu: true,
        manualRowMove: true,
        autoWrapRow: true,
        rowHeights: 30,
        stretchH: 'all',
        defaultRowHeight: 100,
        minRows: 10,
        licenseKey: 'non-commercial-and-evaluation',
        rowHeaders: true,
        colHeaders: true,
        autoColumnSize: {
          samplingRatio: 23
        },
        filters: true,
        manualRowResize: true,
        manualColumnResize: true,
        columnHeaderHeight: 40,
        colWidths: [300, 100, 100, 325],
        rowHeights: 30,
        rowHeaderWidth: [44],
        columns: [
            {
                data: 'account',
                title: 'Account',
                type: 'autocomplete',
                source(query, process) {
                    fetch(window.location.origin + '/api/account/journal')
                    .then(response => response.json())
                    .then(response => process(response.data));
                },
                // strict: true,
                // allowInvalid: false
            },
            {
                data: 'debit',
                title: 'Debit',
                type: 'numeric',
                numericFormat: {
                    pattern: '0,0.00',
                },
            },
            {
                data: 'credit',
                title: 'Credit',
                type: 'numeric',
                numericFormat: {
                    pattern: '0,0.00',
                },
            },
            {
                data: 'description',
                title: 'Description',
                type: 'text',
            },
        ],
        data:[],     
      },
    }),
    watch:{
        selectedHotel: function () {
            this.getHotelDataList();
            this.getTransaction();
            this.form.reset();
        },
    },
    computed: {
      ...mapGetters('operations', ['items', 'appInfo', "selectedHotel", "hotelItems"]),
      ...mapGetters({
            user: "auth/user",
        }),
    },
    mounted(){
      if(this.user.back_days != '' && this.user.back_days != 'All'){
            var today = new Date();
            var minDate = new Date();

            minDate.setDate(today.getDate() - this.user.back_days);
            document.getElementById("date").min = minDate.toISOString().split("T")[0];
        }
        
      var reg = /^\d+$/;
      const hotInstance = this.$refs.hotTable.hotInstance;
      
      hotInstance.addHook('beforeKeyDown', (e) => {
        let col = hotInstance.getSelectedLast()[1];
        if(col == 1 || col == 2){
          if (!reg.test(e.key)) {
            e.preventDefault()
          }
        }
      });

      hotInstance.addHook('afterChange', (changes, source) => {
        
        if(source === 'edit' && changes[0][1] == "account"){
          this.handleAfterChange(changes);
        }

        if (source === 'edit' && changes[0][1] == "debit") {
          const debitColumnData = hotInstance.getDataAtCol(1);
          const sum = debitColumnData.reduce((total, value) => {
            if (typeof value !=='string' && value !== null) {
              return total + parseFloat(value);
            }
            return total;
          }, 0);
          this.totalDebit = sum;
          this.form.amount = sum;
        }

        if (source === 'edit' && changes[0][1] == "credit") {
          const creditColumnData = hotInstance.getDataAtCol(2);
          const sumcredit = creditColumnData.reduce((totalsum, valuecredit) => {
            if (typeof valuecredit !=='string' && valuecredit !== null) {
              return totalsum + parseFloat(valuecredit);
            }
            return totalsum;
          }, 0);
          this.totalCredit = sumcredit;
          this.form.amount = sumcredit;
        }

      });

    },
    async created() {
      await this.getTransaction();
      await this.getHotelDataList();
      if (this.selectedHotel && this.selectedHotel !== 'all') {
          this.hotelItems.forEach((hotel) => {
              if (hotel.id == this.selectedHotel) {
                this.form.hotel_id = hotel;
              }
          })
      }
  },
    methods: {
      
      handleAfterChange(changes) {
        const hotInstance = this.$refs.hotTable.hotInstance;
        for (let i = 0; i < changes.length; i++) {
          const change = changes[i];
          const [row, col, oldValue, newValue] = change;
       
          if (col === 'account' && (newValue != '' && newValue !== null)) { // Check if the change is in Column 3 (0-indexed)
            if(typeof newValue !== 'object'){
              return hotInstance.setDataAtCell(row, 0, null);
            }
          }
        }
      },

      async getHotelDataList () {
        await this.$store.dispatch('operations/getHotelData', {
          path: '/api/hotel',
        });
      },

      // get journal & account transaction detail
      async getTransaction() {
        this.hotSettings.data = [];
        const { data } = await this.form.get(
          window.location.origin + '/api/account/journal/transaction/' + this.$route.params.slug
        )
        
        this.form.hotel_id = data.journaldData.hotel;
        this.form.date = data.journaldData.date;
        this.form.note = data.journaldData.note;
        this.form.id = data.journaldData.id;
        this.form.amount = data.journaldData.amount;
        this.totalDebit = data.journaldData.amount;
        this.totalCredit = data.journaldData.amount;
        if(data.tableData.length){
            let tData = data.tableData.reverse()
            for(var i=0; i<tData.length; i++){
              this.hotSettings.data.unshift(tData[i]);
          }
        }
      },

      async updateJournalEntry (){
        if(this.totalCredit == this.totalDebit){
          const hotInstance = this.$refs.hotTable.hotInstance;
          this.form.tableData = hotInstance.getData();
          
          await this.form
            .post(window.location.origin + '/api/account/journal/edit')
            .then(() => {
              toast.fire({
                type: 'success',
                title: this.$t('cashbook.journal_entry.update.success_msg'),
              })
              this.$router.push({ name: 'journalEntry.index' })
            })
            .catch(() => {
              toast.fire({ type: 'error', title: this.$t('common.error_msg') })
            })

        } else {
          toast.fire({ type: 'error', title: 'Please Match Debit and Credit' })
        }
      }

    },
  }
  </script>
