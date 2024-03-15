<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row no-print mb-2">
      <div class="w-100 text-right float-right card-header border-bottom-0">
        <div class="btn-group" v-if="allData">
          <!--                    <a v-if="!allData.booking && !allData.restaurantOrderDetails"-->
          <!--                            @click="notify((form.isSendSMS = true))"-->
          <!--                            href="#"-->
          <!--                            class="btn btn-secondary"-->
          <!--                    >-->
          <!--                        <i class="fas fa-sms"></i> {{ $t("common.sms") }}-->
          <!--                    </a>-->
          <a v-if="allData.due && allData.due.toFixed(1) > 0" @click="handleModal(allData)" href="#"
            class="btn btn-dark"><i class="fas fa-money-check-alt"></i> {{ $t('common.add_payment') }}</a>
          <a @click="notify((form.isSendEmail = true))" href="#" class="btn btn-success"><i
              class="fas fa-paper-plane"></i> {{ $t("email") }}</a>
          <!-- <a @click="generatePDF()" href="#" class="btn btn-primary"> -->

          <a @click="generatePDF()" href="#" class="btn btn-dark">
            <i class="fas fa-download"></i> {{ $t("download") }}
          </a>
          <a @click="printWindow()" href="#" class="btn btn-dark">
            <i class="fas fa-print"></i> {{ $t("common.print") }}
          </a>

          <router-link v-if="$can('invoice-edit') && !allData.booking && !allData.restaurantOrderDetails" :to="{
      name: 'invoices.edit',
      params: { slug: allData.slug },
    }" class="btn btn-info">
            <i class="fas fa-edit" /> {{ $t("common.edit") }}
          </router-link>
          <router-link :to="{ name: 'invoices.index' }"
            class="btn btn-secondary rounded-top rounded-bottom float-right">
            <i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}
          </router-link>
          <!-- <router-link
            :to="{ name: 'invoices.index' }"
            class="btn btn-dark outline_btn active float-right"
          >
            <i class="fas fa-long-arrow-alt-left"/> {{ $t("common.back") }}
          </router-link> -->
        </div>
      </div>
    </div>
    <div class="row">
      <!-- Main content -->
      <div class="invoice px-3 mb-3 w-100" id="content-to-pdf">
        <table-loading v-show="loading" />
        <!-- info row -->

        <div class="invoice-info row">
          <div class="w-30 col-6 d-flex align-items-center" style="gap: 10px">
            <img v-if="appInfo" :src="appInfo.blackLogo" :alt="appInfo.companyName" class="lg-logo"
              style="max-width: 160px" />
            <div>
              <CompanyInfo :hotel="hotelDetails" :showImage="false" />
              <!-- <b>Comany address:</b><span>{{ appInfo.address }}</span><br> -->
              <b>GST: </b><span>{{ appInfo.gstPrefix }}</span><br>
              <small>{{ appInfo.companyTagline }}</small>
            </div>
            <!-- <div>
              
            </div> -->
          </div>
          <div class="col-2">

          </div>
          <div class="col-4 d-flex flex-column align-items-end justify-content-between">
            <div>
              <div v-if="allData.client">
                <span v-if="allData.client.companyName">
                  <strong>{{$t('common.client_id') }}:</strong>
                  {{ allData.client.clientID | withPrefix(clientPrefix) }}<br /></span>
                  <strong v-if="allData.client.type == 1" >{{ $t('common.client_name') }}:</strong>
                  <strong v-else>Agent:</strong>
                  {{ allData.client.name }}<br />
                <!--                <span v-if="allData.client.companyName"><strong>{{ $t('common.company_name') }}:</strong>-->
                <!--                                  {{ allData.client.companyName }}<br/></span>-->
                <span v-if="allData.client.email"><strong>{{ $t('common.email') }}:</strong>
                  {{ allData.client.email }}<br /></span>
                <span v-if="allData.client.phone"><strong>{{ $t('common.contact_number') }}:</strong>
                  {{ allData.client.phone }}<br /></span>
                <span v-if="allData.client.address"><strong>{{ $t('common.address') }}:</strong>
                  {{ allData.client.address }}<br /></span>
                <span v-if="allData.client.nationality"><strong>{{ $t("common.nationality") }}:</strong>
                  {{ allData.client.nationality }}<br /></span>
                <span v-if="allData.client.gst"><strong>{{ $t("common.gst_number") }}:</strong>
                  {{ allData.client.gst }}<br /></span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <div class="row mt-3">
          <div class="col-12">
            <div class="table-responsive table-custom">
              <table class="table">
                <thead>
                  <tr>
                    <th v-if="allData.invoiceNo">
                      {{ $t("common.invoice_no") }}
                    </th>
                    <th v-if="allData.invoiceDate">
                      {{ $t("common.invoice_date") }}
                    </th>
                    <th v-if="allData.reference">
                      {{ $t("common.reference") }}
                    </th>
                    <th v-if="allData.poReference">
                      {{ $t("common.po_reference") }}
                    </th>
                    <th v-if="allData.paymentTerms">
                      {{ $t("common.payment_terms") }}
                    </th>
                    <th v-if="allData.deliveryPlace">
                      {{ $t("sales.common.delivery_place") }}
                    </th>
                    <th v-if="allData.note">{{ $t("common.note") }}</th>
                    <!-- <th>{{ $t("common.status") }}</th> -->
                    <th class="text-right">{{ $t("common.created_by") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td v-if="allData.invoiceNo">
                      {{ allData.invoiceNo | withPrefix(invoicePrefix) }}
                    </td>
                    <td v-if="allData.invoiceDate">
                      {{ allData.invoiceDate | moment("Do MMM, YYYY") }}
                    </td>
                    <td v-if="allData.reference">
                      {{ allData.reference }}
                    </td>
                    <td v-if="allData.poReference">
                      {{ allData.poReference }}
                    </td>
                    <td v-if="allData.paymentTerms">
                      {{ allData.paymentTerms }}
                    </td>
                    <td v-if="allData.deliveryPlace">
                      {{ allData.deliveryPlace }}
                    </td>
                    <td v-if="allData.note">{{ allData.note }}</td>
                    <!-- <td>
                                      <span v-if="allData.status === 1"
                                            class="badge bg-success"
                                      >{{ $t("common.active") }}</span>
                    <span v-else class="badge bg-danger">{{$t("common.in_active")}}</span>
                  </td> -->
                    <td class="text-right">
                      {{ allData.createdBy }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Table row -->
        <div class="row mt-4">
          <div class="col-12">
            <strong v-if="invoiceProducts && invoiceProducts.length > 0" class="mb-2 d-block">{{
      $t("common.invoice_products") }}:</strong>
            <strong v-if="invoiceServices && invoiceServices.length > 0" class="mb-2 d-block">{{
      $t("common.invoice_services") }}:</strong>
            <div class="table-responsive table-custom">
              <!--For normal products-->
              <table class="table table-sm" v-if="!allData.booking && !allData.restaurantOrderDetails">
                <thead>
                  <tr v-if="invoiceProducts && invoiceProducts.length > 0">
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.code") }}</th>
                    <th>{{ $t("common.product_name") }}</th>
                    <th>{{ $t("common.invoice_qty") }}</th>
                    <th v-if="allData.totalInvoiceReturn">
                      {{ $t("common.return_qty") }}
                    </th>
                    <th class="text-right">{{ inr }}{{ $t("common.unit_price") }}</th>
                    <th class="text-right">{{ inr }}{{ $t("common.unit_tax") }}</th>
                    <th class="text-right">{{ inr }}{{ $t("common.unit_cost") }}</th>
                    <th class="text-right">{{ inr }}{{ $t("common.total") }}</th>
                    <th v-if="allData.totalInvoiceReturn" class="text-right">
                      {{ $t("common.total_return") }}
                    </th>
                  </tr>
                </thead>

                <tbody v-if="invoiceProducts && invoiceProducts.length > 0">
                  <tr v-for="(data, i) in invoiceProducts" :key="i">
                    <td>{{ ++i }}</td>
                    <td>
                      {{ data.productCode | withPrefix(productPrefix) }}
                    </td>
                    <td>{{ data.productName }}</td>
                    <td>{{ data.quantity }} {{ data.productUnit }}</td>
                    <td v-if="allData.totalInvoiceReturn">
                      {{ data.returnQty }} {{ data.productUnit }}
                    </td>
                    <td class="text-right">{{ data.salePrice | withCurrency }}</td>
                    <td class="text-right">{{ data.unitTax | withCurrency }}</td>
                    <td class="text-right">{{ data.unitCost | withCurrency }}</td>
                    <td class="text-right">
                      {{ data.purchasePricetotal | withCurrency }}
                    </td>
                    <td v-if="allData.totalInvoiceReturn" class="text-right">
                      {{ (data.salePrice * data.returnQty) | withCurrency }}
                    </td>
                  </tr>
                  <tr>
                    <td :colspan="allData.totalInvoiceReturn ? 8 : 7" class="text-right">
                      <strong>{{ $t("common.subtotal") }} </strong>
                    </td>
                    <td class="text-right">
                      <strong>
                        {{ allData.subTotal | withCurrency }}
                      </strong>
                    </td>
                    <td v-if="allData.totalInvoiceReturn" class="text-right">
                      <strong>{{
      allData.totalInvoiceReturn | withCurrency
    }}</strong>
                    </td>
                  </tr>
                </tbody>
                <thead>
                  <tr v-if="invoiceServices && invoiceServices.length > 0">
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.service_name") }}</th>
                    <th>{{ $t("common.quantity") }}</th>
                    <th v-if="allData.totalInvoiceReturn">
                      {{ $t("common.return_qty") }}
                    </th>
                    <th class="text-right">{{ $t("common.unit_price") }}</th>
                    <th class="text-right">{{ $t("common.tax") }}</th>
                    <th class="text-right">{{ $t("common.subtotal") }}</th>
                    <th v-if="allData.totalInvoiceReturn" class="text-right">
                      {{ $t("common.total_return") }}
                    </th>
                  </tr>
                </thead>

                
                <tbody v-if="invoiceServices && invoiceServices.length > 0">
                  <tr v-for="(data, i) in invoiceServices" :key="i">
                    <td>{{ ++i }}</td>
                    <td>{{ data.service_name }}</td>
                    <td>{{ data.quantity }} </td>
                    <td v-if="allData.totalInvoiceReturn">
                      {{ data.returnQty }} 
                    </td>
                    <td class="text-right">{{ data.price | withCurrency }}</td>
                    <td class="text-right">{{ (data.price * data.quantity * allData.invoiceServiceTax[0].rate * 2) / 100
      | withCurrency }}</td>
                    <td class="text-right">
                      {{ (data.price * data.quantity) | withCurrency }}
                    </td>
                    <td v-if="allData.totalInvoiceReturn" class="text-right">
                      {{ (data.price * data.returnQty) | withCurrency }}
                    </td>
                  </tr>
                  <tr>
                    <td :colspan="allData.totalInvoiceReturn ? 6 : 5" class="text-right">
                      <strong>{{ $t("common.subtotal") }} </strong>
                    </td>
                    <td class="text-right">
                      <strong>
                        {{ allData.subTotal | withCurrency }}
                      </strong>
                    </td>
                    <td v-if="allData.totalInvoiceReturn" class="text-right">
                      <strong>{{
      allData.totalInvoiceReturn | withCurrency
    }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!--For booking products-->
              <table class="table table-sm" v-else-if="allData.booking">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.product_name") }}</th>
                    <!--                                    <th>{{ $t("check_in.index.from_date") }}</th>-->
                    <!--                                    <th>{{ $t("check_in.index.to_date") }}</th>-->
                    <th class="text-right"> {{ $t("common.total_days") }}</th>
                    <th class="text-right">{{ inr }} {{ $t("common.price") }}</th>
                    <th class="text-right">{{ inr }} {{ $t("common.tax") }}</th>
                    <th class="text-right">{{ inr }} {{ $t("common.total") }}</th>
                  </tr>
                </thead>
                <tbody v-if="bookingProducts && bookingProducts.length > 0">
                  <tr v-for="(data, i) in bookingProducts" :key="i">
                    <td>{{ ++i }}</td>
                    <td>{{ data.name }}</td>
                    <td class="text-right">{{ data.days }}</td>
                    <td class="text-right">{{ data.price | twoPoints }}</td>
                    <td class="text-right">{{ data.tax | twoPoints }}</td>
                    <td class="text-right">
                      {{ data.total | twoPoints }}
                    </td>
                  </tr>
                  <tr v-if="allData?.bookingRestOrders">
                    <td>{{ bookingProducts.length + 1 }}</td>
                    <td>Order</td>
                    <td></td>
                    <td class="text-right">{{ (allData?.bookingRestOrders?.total || 0) -
      (allData?.bookingRestOrders?.tax
        || 0) | twoPoints }}
                    </td>
                    <td class="text-right">{{ (allData?.bookingRestOrders?.tax || 0) | twoPoints }}</td>
                    <td class="text-right">
                      {{ (allData?.bookingRestOrders?.total || 0) | twoPoints }}
                    </td>
                  </tr>
                  <tr>
                    <td :colspan="5" class="text-right">
                      <strong>{{ $t("common.total") }} </strong>
                    </td>
                    <td class="text-right">
                      <strong>
                        {{ bookingSubtotal }}
                      </strong>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!----For invoice products-->
              <table class="table table-sm" v-else-if="allData.restaurantOrderDetails">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>Item Name</th>
                    <th class="text-right">{{ $t("common.invoice_qty") }}</th>
                    <th class="text-right">{{ $t("common.unit_price") }}</th>
                    <th class="text-right">{{ $t("common.total") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="allData?.restaurantOrderDetails?.items"
                    v-for="(data, i) in allData?.restaurantOrderDetails?.items" :key="i">
                    <td>{{ ++i }}</td>
                    <td>{{ data.name }}</td>
                    <td class="text-right">{{ inr }} {{ data.quantity }}</td>
                    <td class="text-right">{{ inr }} {{ data.price }}</td>
                    <td class="text-right">{{ inr }} {{ data.total }}</td>
                  </tr>
                  <tr>
                    <td>{{ allData?.restaurantOrderDetails?.items?.length + 1 }}</td>
                    <td>Addon items</td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{ allData?.restaurantOrderDetails?.addon | withCurrency }}</td>
                  </tr>
                  <tr>
                    <td colspan="4" class="text-right">
                      <strong>{{ $t("common.subtotal") }} </strong>
                    </td>
                    <td class="text-right">
                      <strong>
                        {{ allData?.restaurantOrderDetails?.subtotal }}
                      </strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- /.row -->
        <div class="row mt-4" id="page-break">
          <div class="col-lg-12 col-xl-8">
            <div v-if="allData.invoicePayments && allData.invoicePayments.length > 0">
              <strong class="mb-2 d-block">{{ $t("common.payment_history") }}:</strong>
              <div class="table-responsive table-custom">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>{{ $t("common.s_no") }}</th>
                      <th>{{ $t("common.payment_date") }}</th>
                      <th>Payment Type</th>
                      <th class="text-right">{{ inr }} {{ $t("common.paid_amount") }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(data, i) in allData.invoicePayments" :key="i">
                      <td>{{ ++i }}</td>
                      <td>
                        <span v-if="data.date">{{ data.date | moment("Do MMM, YYYY") }}</span>
                      </td>
                      <td>
                        {{ data.account?.ledgerName }}
                      </td>
                      <td class="text-right">{{ data.amount | twoPoints }}</td>
                    </tr>
                    <tr>
                      <td class="text-right" colspan="2">
                        <strong>{{ $t("common.total_paid") }}</strong>
                      </td>
                      <td colspan="5" class="text-right">
                        <strong>{{ allData.totalPaid }}</strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="no-print callout callout-danger mt-4 w-100" v-else>
              <h5>{{ $t("common.empty_payment") }}</h5>
              <p>{{ $t("common.empty_payment_msg") }}</p>
            </div>
          </div>
          <div class="col-lg-12 col-xl-4 text-lg-right mt-4">
            <div class="table-responsive table-custom table-border-y-0">
              <table class="table">
                <tbody>
                  <tr class="bg-sub-light text-bold">
                    <th>{{ $t("common.subtotal") }}:</th>
                    <td>{{ allData.subTotal + (allData.bookingDiscount || 0) - (allData?.booking?.commission_amount ||
      0)
      | withCurrency }}</td>
                  </tr>
                  <tr v-if="allData.totalInvoiceReturn">
                    <th>{{ $t("common.return_cost") }}:</th>
                    <td>
                      <span class="minus-sign">-</span>
                      {{ allData.totalInvoiceReturn | withCurrency }}
                    </td>
                  </tr>
                  <tr>
                    <th>
                      {{ $t("common.discount") }}
                      <span v-if="allData.discountType == 1">({{ allData.discountPercentage }}%)</span>
                      :
                    </th>
                    <td>
                      <span class="minus-sign">-</span>
                      {{ (allData.bookingDiscount ? allData.bookingDiscount : allData.discount) | withCurrency }}
                    </td>
                  </tr>
                  <tr>
                    <th>{{ $t("common.commission") }}</th>
                    <td>
                      <span class="plus-sign">+</span>
                      {{ (allData.booking ? allData.booking.commission_amount : 0) | withCurrency }}
                    </td>
                  </tr>
                  <tr v-if="!allData.booking">
                    <th>{{ $t("common.transport") }}:</th>
                    <td>
                      <span class="plus-sign">+</span>
                      {{ allData.transport | withCurrency }}
                    </td>
                  </tr>
                  
                  <tr v-if="allData.bookingRestOrders">
                    <th>Taxes:</th>
                    <td>
                      <span class="plus-sign">+</span>
                      {{ allData.tax | withCurrency }}
                    </td>
                  </tr>

                  <template v-else-if="finalGstRate">
                   <tr v-for="(item, index) in Object.keys(finalGstRate)" :key="index + '12'">
                      <template v-if="finalGstRate && finalGstRate[item]">
                        <th>
                          {{ item }}
                          <!--                                            {{ item?.split(' ')?.length ? item?.split(' ')[0] : item }}-->
                        </th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ finalGstRate ? finalGstRate[item] : 0 | withCurrency }}
                        </td>
                      </template>
                    </tr>
                  </template>

                  <template v-else-if="allData.invoiceServices?.length">
                      <tr v-for="(item, index) in  allData.invoiceServiceTax ">
                      <template>
                        <th>
                          {{ item.name ?? '' }}
                        </th>
                        <td v-if="!allData.totalInvoiceReturn">
                          <span class="plus-sign">+</span>
                          {{ item.amount ? item.amount : 0 | withCurrency }}
                        </td>
                        <td v-else="!allData.totalInvoiceReturn">
                          <span class="plus-sign">+</span>
                            {{ allData.tax /2 | withCurrency }}
                        </td>
                      </template>
                    </tr>
                  </template>

                  <template v-else-if="Object.keys(selectedProductTaxRate).length !== 0">
                    <tr v-for="(item, index) in Object.keys(selectedProductTaxRate)">
                      <th>
                        {{ item }}
                      </th>
                      <td>
                        <span class="plus-sign">+</span>
                        {{ selectedProductTaxRate[item] | withCurrency }}
                      </td>
                    </tr>
                  </template>

                  <template v-else>
                    <tr>
                      <th>
                        {{ $t("common.tax") }}
                        <span v-if="allData.taxRate">({{ allData.taxRate.rate }}%)</span>:
                      </th>
                      <td>
                        <span class="plus-sign">+</span>
                        {{ allData.tax | withCurrency }}
                      </td>
                    </tr>
                  </template>
                  <tr class="bg-indigo-light">
                    <th>{{ $t("common.total") }}:</th>
                    <td>
                      <span class="equal-sign">=</span>
                      {{
      (allData.subTotal -
        allData.totalInvoiceReturn -
        allData.discount +
        allData.transport +
        allData.tax)
      | withCurrency
    }}
                    </td>
                  </tr>
                  <tr v-if="allData.invoicePayments">
                    <th>{{ $t("common.total_paid") }}:</th>
                    <td>
                      <span class="minus-sign">-</span>
                      {{ allData.totalPaid | withCurrency }}
                    </td>
                  </tr>
                  <tr class="bg-red-light">
                    <th>{{ $t("common.due") }}:</th>
                    <td>{{ allData.due | withCurrency }}</td>
                  </tr>
                  <tr class="bg-green-light" v-if="allData.accountPayable">
                    <th>{{ $t("common.account_payable") }}:</th>
                    <td>{{ allData.accountPayable | withCurrency }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.invoice -->
    </div>
    <Modal v-if="showModal">
      <h5 slot="header">
        {{ $t("payments.clients.invoice.create.form_title") }} :
        {{ paymentform.selectedInvoice.invoiceNo | withPrefix(invoicePrefix) }}
      </h5>
      <div slot="body" class="row">
        <form role="paymentform" @submit.prevent="savePayment" @keydown="paymentform.onKeydown($event)" class="w-100">
          <div class="row">
            <div class="form-group col-md-6">
              <label for="clientInvoiceTotal">{{
      $t("common.invoice_total")
    }}</label>
              <input type="text" class="form-control" readonly
                v-model="paymentform.selectedInvoice.invoiceTotal.toFixed(2)" />
            </div>
            <div class="form-group col-md-6">
              <label for="clientInvoiceDue">{{
      $t("common.invoice_due")
    }}</label>
              <input type="text" class="form-control" readonly v-model="paymentform.selectedInvoice.due.toFixed(2)" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="paidAmount">{{ $t("common.paid_amount") }}</label>
              <input type="number" step="any" class="form-control" :placeholder="$t('common.paid_amount_placeholder')"
                required min="1" v-model="paymentform.paidAmount" :max="paymentform.selectedInvoice.due" />
            </div>
            <div class="form-group col-md-6">
              <label for="account">{{ $t("common.account") }}
                <span class="required">*</span></label>
              <v-select v-model="paymentform.account" :options="accounts" label="label"
                :class="{ 'is-invalid': paymentform.errors.has('account') }" name="account"
                :placeholder="$t('common.account_placeholder')" />
              <has-error :form="paymentform" field="account" />
            </div>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label for="chequeNo">{{ $t("common.cheque_no") }}</label>
              <input type="text" v-model="form.chequeNo" class="form-control"
                :class="{ 'is-invalid': form.errors.has('chequeNo') }" id="chequeNo"
                :placeholder="$t('common.cheque_placeholder')" />
              <has-error :form="form" field="chequeNo" />
            </div>
            <div class="form-group col-md-6">
              <label for="receiptNo">{{ $t("common.receipt_no") }}</label>
              <input type="text" v-model="paymentform.receiptNo" class="form-control"
                :class="{ 'is-invalid': paymentform.errors.has('receiptNo') }" id="receiptNo"
                :placeholder="$t('common.receipt_no_placeholder')" />
              <has-error :form="form" field="receiptNo" />
            </div>
            <div class="form-group col-md-6">
              <label for="paymentDate">{{ $t("common.payment_date") }}</label>
              <input id="paymentDate" v-model="paymentform.paymentDate" type="date" class="form-control"
                :class="{ 'is-invalid': paymentform.errors.has('paymentDate') }" name="paymentDate" />
              <has-error :form="paymentform" field="paymentDate" />
            </div>
            <div class="form-group col-md-6">
              <label for="status">{{ $t("common.status") }}</label>
              <select id="status" v-model="paymentform.status" class="form-control"
                :class="{ 'is-invalid': paymentform.errors.has('status') }">
                <option value="1">{{ $t("common.active") }}</option>
                <option value="0">{{ $t("common.in_active") }}</option>
              </select>
              <has-error :form="paymentform" field="status" />
            </div>
          </div>
          <div class="form-group">
            <label for="note">{{ $t("common.note") }}</label>
            <textarea id="note" v-model="paymentform.note" class="form-control"
              :class="{ 'is-invalid': paymentform.errors.has('note') }" :placeholder="$t('common.note_placeholder')" />
            <has-error :form="paymentform" field="note" />
          </div>
          <div class="form-group col-12 d-flex flex-wrap">
            <div class="pr-5">
              <toggle-button v-model="paymentform.isSendEmail" />
              {{ $t("Send Email Notification") }}
            </div>
          </div>
          <div class="form-group col-12 d-flex flex-wrap">
            <div class="pr-5">
              <toggle-button v-model="paymentform.isSendSMS" />
              {{ $t("Send SMS Notification") }}
            </div>
          </div>
          <v-button :loading="paymentform.busy" class="btn btn-primary">
            <i class="fas fa-save" /> {{ $t("common.save") }}
          </v-button>
        </form>

      </div>

      <template #modal-footer>
        <div class="mt-1">
          <button class="modal-default-button btn btn-primary" @click="savePayment">
            <i class="fas fa-save" /> {{ $t("common.save") }}
          </button>
          <button class="modal-default-button btn btn-secondary" @click="showModal = false">
            <i class="fas fa-times"></i>
            {{ $t("common.close") }}
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import { mapGetters } from "vuex";
import html2pdf from "html2pdf.js";
import _ from 'lodash';

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("sales.invoices.view.page_title") };
  },
  data: () => ({
    allData: "",
    breadcrumbsCurrent: "sales.invoices.view.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "sales.invoices.view.breadcrumbs_first",
        url: "home",
      },
      {
        name: "sales.invoices.view.breadcrumbs_second",
        url: "invoices.index",
      },
      {
        name: "sales.invoices.view.breadcrumbs_active",
        url: "",
      },
    ],
    invoiceProducts: [],
    invoiceServices: [],
    productPrefix: "",
    clientPrefix: "",
    invoicePrefix: "",
    loading: false,
    form: new Form({
      isSendEmail: false,
      isSendSMS: false,
    }),
    paymentform: new Form({
      selectedInvoice: "",
      paidAmount: 1,
      invoice_id: "",
      paymentDate: new Date().toISOString().slice(0, 10),
      date: new Date().toISOString().slice(0, 10),
      account: "",
      chequeNo: "",
      receiptNo: "",
      note: "",
      netTotal: 0,
      status: 1,
      isSendEmail: false,
      inr: "",
      isSendSMS: false,
      bank_only: 0,
    }),
    showModal: false,
    accounts: [],
    bookingProducts: null,
    hotelDetails: {},
    selectedProductTaxRate: [],
  }),
  mounted() {
    // Assuming you have elements with the class "bg-red-light"
    var elementsWithClass = document.getElementsByClassName('bg-red-light');

    // Attach the function to the dblclick event of each element with the class "bg-red-light"
    for (var i = 0; i < elementsWithClass.length; i++) {
      elementsWithClass[i].addEventListener('dblclick', this.makeDivEditable);
    }
  },
  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
    totalRoomRentsDetails() {
      let totalBedAmt = 0;
      let totalExtraPersonAmt = 0;
      let mealPlanCharge = 0;
      let mealTax = 0;
      let bedTax = 0;
      let extraPersonTax = 0;
      let facilityTax = 0;

      this.bookingDetails.forEach((room) => {
        let days = room.room_rent_details?.days || 0;
        mealPlanCharge += room.mealPlanPrice * days * room.mealPersons
        totalBedAmt += room.bedPrice;
        totalExtraPersonAmt += room.perPersonPrice;
        mealTax += room.mealTax;
        bedTax += room.bedTax;
        extraPersonTax += room.extraPersonTax;
        facilityTax += room.facilityTax;
      })

      return {
        'totalBedAmt': totalBedAmt,
        'totalExtraPersonAmt': totalExtraPersonAmt,
        'mealPlanCharge': mealPlanCharge,
        'mealTax': mealTax,
        'bedTax': bedTax,
        'extraPersonTax': extraPersonTax,
        'facilityTax': facilityTax
      }
    },
    complimentaryItems() {
      let compPrices = {};
      let days = 0;
      this.bookingDetails.forEach((room) => {
        days = room.room_rent_details?.days || 0;
        room.complementary.forEach((comp) => {
          compPrices[comp.title] = compPrices[comp.title] ? (compPrices[comp.title] + comp.price)
            : comp.price;
        })
      })
      // Object.keys(compPrices).forEach((c) => {
      //   compPrices[c] = compPrices[c] * days
      // })

      return compPrices;
    },
    bookingSubtotal() {
      let restOrderTotal = 0;
      if (this.allData?.bookingRestOrders) {
        restOrderTotal = this.allData?.bookingRestOrders?.total || 0
      }
      let total = _.map(this.bookingProducts, 'total');
      if (total && total.length) return _.sum(total) + parseFloat(restOrderTotal)
    },
    finalGstRate() {
      return this.allData?.booking?.final_gst_rates && Object.keys(this.allData?.booking?.final_gst_rates).length > 0 ?
        this.allData?.booking?.final_gst_rates : null;
    },
    totalBookingGst() {
      return this.allData?.booking?.final_gst_rates && Object.values(this.allData?.booking?.final_gst_rates).length > 0 ?
        _.sum(Object.values(this.allData?.booking?.final_gst_rates)) : 0;
    }
  },
  created() {
    // this.makeDivEditable();
    this.getInvoice();
    this.productPrefix = this.appInfo.productPrefix;
    this.clientPrefix = this.appInfo.clientPrefix;
    this.invoicePrefix = this.appInfo.invoicePrefix;
    this.inr = this.appInfo.currency.symbol;
  },
  methods: {
    makeDivEditable() {
      var contentDiv = document.getElementById('content-to-pdf');
      if (contentDiv) {
        contentDiv.contentEditable = true;
        contentDiv.designMode = 'on';
      } else {
        console.error('Element with id "content-to-pdf" not found.');
      }
    },

    async getAccounts() {
      this.paymentform.bank_only = 1
      const { data } = await this.form.get(
        window.location.origin + "/api/all-accounts"
      );
      this.accounts = data.data;

      // assign default account
      if (this.accounts && this.accounts.length > 0) {
        let defaultAccountSlug = this.appInfo.defaultAccountSlug;
        this.paymentform.account = this.accounts.find(
          (account) => account.slug == defaultAccountSlug
        );
      }
    },
    // get the invoice
    async getInvoice() {
      this.loading = true;
      const { data } = await axios.get(
        window.location.origin + "/api/invoices/" + this.$route.params.slug
      );
      this.allData = data.data;
      this.invoiceProducts = this.allData.invoiceProducts;
      this.invoiceServices = this.allData.invoiceServices;
      this.bookingDetails = this.allData.bookingDetails;
      let days = 1;
      this.bookingProducts = this.bookingDetails.map((item) => {
        days = item?.room_rent_details?.days;
        return {
          from: item?.check_in,
          to: item?.check_out,
          name: `Room - ${item?.room_no}`,
          days: days,
          price: item?.room_rent_details?.rent_per_day,
          tax: this.totalTax(item?.room_rent_details),
          total: this.totalAmount(item?.room_rent_details)
        }
      })

      this.hotelDetails = this.allData?.hotel;

      let totalRoomFacilityDetails = this.totalRoomRentsDetails;
      if (totalRoomFacilityDetails.mealPlanCharge > 0) {
        this.bookingProducts.push({
          name: 'Meal plan',
          price: totalRoomFacilityDetails.mealPlanCharge / days,
          days: days,
          tax: totalRoomFacilityDetails.mealTax,
          total: totalRoomFacilityDetails.mealPlanCharge + totalRoomFacilityDetails.mealTax
        })
      }

      if (totalRoomFacilityDetails.totalBedAmt > 0) {
        this.bookingProducts.push({
          name: 'Extra beds',
          price: totalRoomFacilityDetails.totalBedAmt / days,
          days: days,
          tax: totalRoomFacilityDetails.bedTax,
          total: totalRoomFacilityDetails.totalBedAmt + totalRoomFacilityDetails.bedTax
        })
      }

      if (totalRoomFacilityDetails.totalExtraPersonAmt > 0) {
        this.bookingProducts.push({
          name: 'Extra persons',
          price: totalRoomFacilityDetails.totalExtraPersonAmt / days,
          days: days,
          tax: totalRoomFacilityDetails.extraPersonTax,
          total: totalRoomFacilityDetails.totalExtraPersonAmt + totalRoomFacilityDetails.extraPersonTax
        })
      }

      if (Object.keys(this.complimentaryItems).length > 0) {
        let FacilityName = `Facilities (${Object.keys(this.complimentaryItems).toString()})`;
        let FacilityPrice = _.sum(Object.values(this.complimentaryItems))
        this.bookingProducts.push({
          name: FacilityName,
          price: FacilityPrice,
          days: '-',
          tax: totalRoomFacilityDetails.facilityTax,
          total: FacilityPrice + totalRoomFacilityDetails.facilityTax
        })
      }

      let extraCharge = this.allData?.booking?.extra_charge;

      if (extraCharge && parseInt(extraCharge) > 0) {
        this.bookingProducts.push({
          name: "Extra charges",
          price: extraCharge,
          days: '-',
          tax: '0',
          total: extraCharge
        })
      }

      this.invoiceProducts.sort(this.sortProducts);

      let gst = {};
      this.invoiceProducts.forEach(tax => {
        tax.product_tax.forEach(taxRate => {
          if (taxRate.amount != 0) {
            gst[taxRate.code] = 0;
          }
        });
      });

      this.invoiceProducts.forEach(tax => {
        tax.product_tax.forEach(taxRate => {
          if (taxRate.amount != 0 && typeof taxRate.rate !== 'undefined') {
            gst[taxRate.code] += (parseFloat(tax.salePrice) * (parseFloat(tax.quantity) - parseFloat(tax.returnQty)) * parseFloat(taxRate.rate)) / 100;
          }
        });
      })

      this.selectedProductTaxRate = gst;
      this.loading = false;
    },
    totalRent(room) {
      return room.rent_per_day * room.days;
    },
    totalTax(room) {
      let total = 0;
      room.tax_details && room.tax_details.forEach(tax => {
        total += tax.amount * room.days;
      })
      return total;
    },
    totalAmount(room) {
      return room ? room.rent_per_day * room.days + this.totalTax(room) : 0;
    },
    sortProducts(a, b) {
      if (a.productCode < b.productCode) {
        return -1;
      }
      if (a.productCode > b.productCode) {
        return 1;
      }
      return 0;
    },
    // download pdf
    generatePDF(slug) {
      const element = document.getElementById("content-to-pdf");
      const options = {
        margin: 5,
        filename: "Sales Invoice-" + this.$route.params.slug + ".pdf",
        image: { type: "jpeg", quality: 0.98 },
        pagebreak: { mode: "avoid-all", before: "#page-break" },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "mm", format: "a4", orientation: "landscape" },
      };

      // Generate PDF from HTML content
      html2pdf().from(element).set(options).save();
    },
    // notify
    async notify() {
      this.loading = true;
      await this.form
        .post(
          window.location.origin +
          "/api/invoice/notify/" +
          this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("Notification sent successfully."),
          });
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("common.error_msg") });
        });
      this.loading = false;

      // action for demo
      // toast.fire({
      //   type: "warning",
      //   title: this.$t("You are not allowed to do this in demo version."),
      // });
    },

    handleModal(item) {
      this.getAccounts();
      this.paymentform.selectedInvoice = item;
      this.paymentform.invoice_id = item.id;
      this.paymentform.netTotal = item.due;
      this.showModal = true;
    },

    // save  payment
    async savePayment() {
      await this.paymentform
        .post(window.location.origin + "/api/invoices-pay")
        .then(({ data }) => {
          toast.fire({
            type: "success",
            title: this.$t("sales.invoices.create.success_msg"),
          });
          this.$router.push({
            name: "invoices.index",
          });
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("common.error_msg") });
        });
    },
    // print
    printWindow() {
      window.print();
    },
  },
};
</script>

<style scoped>
.table th,
.table td {
  padding: 0.50rem;
}

.break-all {
  word-break: break-all;
}
</style>
