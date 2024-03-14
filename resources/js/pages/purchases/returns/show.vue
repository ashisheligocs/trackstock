<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <!-- Main content -->
        <div class="invoice p-3 mb-3">
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              <CompanyInfo />
            </div>
            <!-- /.col -->
            <div class="col-sm-6 offset-sm-2 invoice-col float-right text-md-right">
              <h5>{{ $t('common.supplier_details') }}</h5>
              <div v-if="allData.purchase && allData.supplier">
                <span v-if="allData.supplier.companyName"><strong>{{ $t('common.supplier_id') }}:</strong>
                  {{ allData.supplier.supplierID | withPrefix(supplierPrefix)
                  }}<br /></span>
                <strong>{{ $t('common.supplier_name') }}:</strong>
                {{ allData.supplier.name }}<br />
                <span v-if="allData.supplier.companyName"><strong>{{ $t('common.company_name') }}:</strong>
                  {{ allData.supplier.companyName }}<br /></span>
                <span v-if="allData.supplier.email"><strong>{{ $t('common.email') }}:</strong>
                  {{ allData.supplier.email }}<br /></span>
                <span v-if="allData.supplier.phoneNumber"><strong>{{ $t('common.contact_number') }}:</strong>
                  {{ allData.supplier.phoneNumber }}<br /></span>
                <span v-if="allData.supplier.address"><strong>{{ $t('common.address') }}:</strong>
                  {{ allData.supplier.address }}<br /></span>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <div class="row mt-3">
            <div class="col-12">
              <div class="table-responsive table-custom">
                <table v-if="allData.purchase" class="table">
                  <thead>
                    <tr>
                      <th v-if="allData.purchase.code">
                        {{ $t('purchases.list.common.purchase_no') }}
                      </th>
                      <th v-if="allData.returnNo">
                        {{ $t('purchases.list.common.return_no') }}
                      </th>
                      <th v-if="allData.purchase.purchaseDate">
                        {{ $t('purchases.list.common.purchase_date') }}
                      </th>
                      <th v-if="allData.returnDate">
                        {{ $t('purchases.returns.common.return_date') }}
                      </th>
                      <th v-if="allData.reason">
                        {{ $t('purchases.returns.common.return_reason') }}
                      </th>
                      <th v-if="allData.note">{{ $t('common.note') }}</th>
                      <th>{{ $t('common.status') }}</th>
                      <th v-if="allData.createdBy" class="text-right">
                        {{ $t('common.created_by') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left" v-if="allData.purchase.code">
                        {{ allData.purchase.code | withPrefix(purchasePrefix) }}
                      </td>
                      <td class="text-left" v-if="allData.returnNo">
                        {{ allData.returnNo | withPrefix(returnPrefix) }}
                      </td>
                      <td v-if="allData.purchase.purchaseDate">
                        {{
                            allData.purchase.purchaseDate | moment('Do MMM, YYYY')
                        }}
                      </td>
                      <td v-if="allData.returnDate">
                        {{ allData.returnDate | moment('Do MMM, YYYY') }}
                      </td>
                      <td v-if="allData.reason">{{ allData.reason }}</td>
                      <td v-if="allData.note">{{ allData.note }}</td>
                      <td>
                        <span v-if="allData.status === 1" class="badge bg-success">{{ $t('common.active') }}</span>
                        <span v-else class="badge bg-danger">{{
                            $t('common.in_active')
                        }}</span>
                      </td>
                      <td v-if="allData.createdBy" class="text-right">
                        {{ allData.createdBy }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Table row -->
          <div class="row position-relative mt-4">
            <table-loading v-show="loading" />
            <div v-if="allData.purchase" class="col-12 table-responsive">
              <strong class="mb-2 d-block">{{ $t('purchases.returns.common.return_products') }}:</strong>
              <div class="table-custom table-responsive text-center">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>{{ $t('common.s_no') }}</th>
                      <th>{{ $t('common.code') }}</th>
                      <th>{{ $t('common.name') }}</th>
                      <th>{{ $t('purchases.list.common.purchased_qty') }}</th>
                      <th>{{ $t('purchases.list.common.returned_qty') }}</th>
                      <th>{{inr}} {{ $t('common.purchase_price') }}</th>
                      <th>{{inr}} {{ $t('common.total') }}</th>
                      <th class="text-right">
                        {{inr}} {{ $t('common.total_return') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody v-if="returnProducts">
                    <tr v-for="(data, i) in returnProducts" :key="i">
                      <td>{{ ++i }}</td>
                      <td class="text-left" v-if="data.product">
                        {{ data.product.code | withPrefix(productPrefix) }}
                      </td>
                      <td class="text-left" v-if="data.product">{{ data.product.name }}</td>
                      <td class="text-right" v-if="data.product">
                        {{ data.purchasedQty }}
                        <span v-if="data.product.itemUnit">{{
                            data.product.itemUnit.code
                        }}</span>
                      </td>
                      <td class="text-right" v-if="data.product">
                        {{ data.returnQty }}
                        <span v-if="data.product.itemUnit">{{
                            data.product.itemUnit.code
                        }}</span>
                      </td>
                      <td class="text-right">{{ data.purchasePrice }}</td>
                      <td class="text-right">
                        {{
                            (data.purchasePrice * data.purchasedQty)
                            
                        }}
                      </td>
                      <td class="text-right">
                        {{
                            (data.purchasePrice * data.returnQty)
                        }}
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6" class="text-right">
                        <strong>{{ $t('common.subtotal') }}</strong>
                      </td>
                      <td>
                        <strong>{{ purchaseSubTotal}}</strong>
                      </td>
                      <td class="text-right">
                        <strong>{{ purchaseReturn}}</strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- /.row -->
          <div v-if="allData.purchase" class="row mt-3">
            <div class="offset-xl-8 col-lg-12 col-xl-4 text-xl-right">
              <div class="table-responsive table-custom table-border-y-0">
                <table class="table">
                  <tbody>
                    <tr class="bg-sub-light text-bold">
                      <th>{{ $t('common.subtotal') }}:</th>
                      <td>{{ allData.purchase.subTotal }}</td>
                    </tr>
                    <tr>
                      <th>{{ $t('common.return_cost') }}:</th>
                      <td>
                        <span class="minus-sign">-</span>
                        {{ purchaseReturn  }}
                      </td>
                    </tr>
                    <tr>
                      <th>{{ $t('common.discount') }}:</th>
                      <td>
                        <span class="minus-sign">-</span>
                        {{ allData.purchase.totalDiscount  }}
                      </td>
                    </tr>
                    <tr>
                      <th>{{ $t('common.transport') }}:</th>
                      <td>
                        <span class="plus-sign">+</span>
                        {{ allData.purchase.transport  }}
                      </td>
                    </tr>
                    <tr>
                      <th>
                        {{ $t('common.tax') }}
                        <span>({{ allData.purchase.taxRate }}%):</span>
                      </th>
                      <td>
                        <span class="plus-sign">+</span>
                        {{ allData.purchase.tax  }}
                      </td>
                    </tr>
                    <tr class="bg-indigo-light">
                      <th>{{ $t('common.total') }}:</th>
                      <td>
                        <span class="equal-sign">=</span>
                        {{ allData.purchase.purchaseTotal  }}
                      </td>
                    </tr>
                    <tr>
                      <th>{{ $t('common.total_paid') }}:</th>
                      <td>
                        <span class="minus-sign">-</span>
                        {{ allData.purchase.totalPaid  }}
                      </td>
                    </tr>
                    <tr class="bg-red-light">
                      <th>{{ $t('common.due') }}:</th>
                      <td>{{ allData.purchase.due  }}</td>
                    </tr>
                    <tr class="bg-green-light" v-if="allData.accountReceivable">
                      <th>{{ $t('common.account_receivable') }}:</th>
                      <td>
                        {{ allData.accountReceivable.amount }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- /.row -->

          <!-- this row will not appear when printing -->
          <div class="row no-print mt-5">
            <div class="col-12">
              <router-link :to="{ name: 'purchaseReturns.index' }" class="btn btn-secondary float-right">
                <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
              </router-link>
              <a href="#" @click="printWindow" class="btn btn-default"><i class="fas fa-print"></i> {{
                  $t('common.print')
              }}</a>
            </div>
          </div>
        </div>
        <!-- /.invoice -->
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('purchases.returns.view.page_title') }
  },
  data: () => ({
    breadcrumbsCurrent: 'purchases.returns.view.breadcrumbs_current',
    breadcrumbs: [
      {
        name: 'purchases.returns.view.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'purchases.returns.view.breadcrumbs_second',
        url: 'purchaseReturns.index',
      },
      {
        name: 'purchases.returns.view.breadcrumbs_active',
        url: '',
      },
    ],
    allData: '',
    purchaseSubTotal: 0,
    purchaseReturn: 0,
    returnProducts: [],
    productPrefix: '',
    inr: "",
    purchasePrefix: '',
    returnPrefix: '',
    supplierPrefix: '',
    loading: false,
  }),
  computed: mapGetters({
    appInfo: 'operations/appInfo',
  }),
  created() {
    this.getInvoiceReturn()
    this.productPrefix = this.appInfo.productPrefix
    this.purchasePrefix = this.appInfo.purchasePrefix
    this.returnPrefix = this.appInfo.purchaseReturnPrefix
    this.supplierPrefix = this.appInfo.supplierPrefix
    this.inr = this.appInfo.currency.symbol;
  },
  methods: {
    // get the return
    async getInvoiceReturn() {
      this.loading = true
      const { data } = await axios.get(
        window.location.origin +
        '/api/purchase-returns/' +
        this.$route.params.slug
      )
      this.allData = data.data
      this.returnProducts = this.allData.returnProducts
      this.returnProducts.sort(this.sortProducts)
      this.calculateTotalAmount()
      this.loading = false
    },

    sortProducts(a, b) {
      if (a.product.code < b.product.code) {
        return -1
      }
      if (a.product.code > b.product.code) {
        return 1
      }
      return 0
    },

    // calculate total return
    calculateTotalAmount() {
      let purchaseSubTotal = 0
      let purchaseReturn = 0
      if (this.returnProducts) {
        purchaseSubTotal = this.returnProducts.reduce(function (prev, next) {
          return prev + Number(next.purchasedQty) * Number(next.purchasePrice)
        }, 0)
        purchaseReturn = this.returnProducts.reduce(function (prev, next) {
          return prev + Number(next.returnQty) * Number(next.purchasePrice)
        }, 0)
      }
      this.purchaseSubTotal = purchaseSubTotal
      this.purchaseReturn = purchaseReturn
      return
    },

    // print
    printWindow() {
      window.print()
    },
  },
}
</script>
