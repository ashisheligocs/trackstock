<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row customer_cont">
      <div class="col-md-12 col-lg-3" v-show="invisible">
        <div class="card">
          <div class="card-body box-profile">
            <div class="text-center mb-2">
              <a v-if="allData.image" href="#" id="show-modal" @click="previewModal(allData.image)">
                <img :src="allData.image" class="profile-user-img img-fluid img-circle" loading="lazy" />
              </a>
              <div v-else class="bg-secondary no-preview-lg">
                <small>{{ $t("common.no_preview") }}</small>
              </div>
            </div>
            <h3 class="profile-username text-center">{{ allData.name }}</h3>
            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <strong>{{ $t("sidebar.hotel") }}</strong>
                <span class="float-right">{{
                  allData.hotel?.hotel_name
                }}</span>
              </li>
              <li class="list-group-item">
                <strong>{{ $t("common.client_id") }}</strong>
                <span class="float-right">{{
                  allData.clientID | withPrefix(clientPrefix)
                }}</span>
              </li>
              <li class="list-group-item">
                <strong>{{ $t("common.name") }}</strong>
                <span class="float-right">{{ allData.name }}</span>
              </li>
              <li class="list-group-item">
                <strong>{{ $t("common.email") }}</strong>
                <span class="float-right">{{ allData.email }}</span>
              </li>
              <li class="list-group-item">
                <strong>{{ $t("common.contact_number") }}</strong>
                <span class="float-right">{{ allData.phone }}</span>
              </li>
              <!--              <li class="list-group-item">-->
              <!--                <strong>{{ $t("common.company_name") }}</strong>-->
              <!--                <span class="float-right">{{ allData.companyName }}</span>-->
              <!--              </li>-->
              <li class="list-group-item">
                <strong>{{ $t("common.address") }}</strong>
                <span class="float-right">{{ allData.address }}</span>
              </li>
            </ul>
            <span v-if="allData.status === 1" class="btn-block btn btn-primary">{{ $t("common.active") }}</span>
            <span v-else class="btn-block btn bg-danger">{{
              $t("common.in_active")
            }}</span>
          </div>
          <!-- /.card-body -->
        </div>
      </div>
      <!-- /.col -->

      <div :class="currentClass">
        <div class="row">
          <div class="col-lg-6 col-md-4 col-12">
            <div class="card bg-info">
              <div class="card-content">
                <div class="card-body pb-1">

                  <div class="row">

                    <div class="col-6">
                      <h6 class="text-white">
                        {{ $t("common.invoice_total") }}
                      </h6>
                      <h6 class="text-white">
                        {{ $t("payments.common.non_invoice_total") }}
                      </h6>
                      <hr />
                      <h4 class="text-white mb-1">{{ $t("common.total") }}</h4>
                    </div>
                    <div class="col-6 text-right">
                      <h6 class="text-white">
                        {{ (allData.clientInvoiceTotal) ? allData.clientInvoiceTotal : 0 | withCurrency }}
                      </h6>
                      <h6 class="text-white">
                        {{ (allData.nonInvoiceDue) ? allData.nonInvoiceDue : 0 | withCurrency }}
                      </h6>
                      <hr />
                      <h4 class="text-white mb-1">
                        {{
                          (allData.clientInvoiceTotal || allData.nonInvoiceDue) ? (allData.clientInvoiceTotal +
                            allData.nonInvoiceDue) : 0
                        | withCurrency
                        }}
                      </h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-4 col-12">
            <div class="card bg-danger">
              <div class="card-content">
                <div class="card-body pb-1">
                  <div class="row">
                    <div class="col-6">
                      <h6 class="text-white">{{ $t("common.invoice_due") }}</h6>
                      <h6 class="text-white">
                        {{ $t("common.non_invoice_due") }}
                      </h6>
                      <hr />
                      <h4 class="text-white mb-1">
                        {{ $t("common.total_due") }}
                      </h4>
                    </div>
                    <div class="col-6 text-right">
                      <h6 class="text-white">
                        {{ (allData.clientDue) ? allData.clientDue : 0 | withCurrency }}
                      </h6>
                      <h6 class="text-white">
                        {{ (allData.nonInvoiceCurrentDue) ? allData.nonInvoiceCurrentDue : 0 | withCurrency }}
                      </h6>
                      <hr />
                      <h4 class="text-white mb-1">
                        {{
                          (allData.clientDue || allData.nonInvoiceCurrentDue) ? (allData.clientDue +
                            allData.nonInvoiceCurrentDue) : 0
                        | withCurrency
                        }}
                      </h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="$can('invoice-list') ||
          $can('invoice-return-list') ||
          $can('invoice-payment-list') ||
          $can('non-invoice-payment-list')
          " class="card">
          <div class="card-header p-2">
            <div class="row">
              <div class="col-md-9">
                <ul class="nav nav-pills">
                    <li class="nav-item bg-transparent">
                      <a class="nav-link outline_btn" href="" v-on:click="ShowDetail">
                        <i class="fa-regular fa-id-card-clip"></i>
                        Show Customer Details
                      </a>
                    </li>
                  <li v-if="$can('invoice-list')" class="nav-item bg-transparent">
                    <a class="nav-link outline_btn active" href="#invoices" data-toggle="tab"
                      @click="activeTab = 'invoices'">
                      <i class="fas fa-file-invoice"></i>
                      {{ $t("sales.invoices.index.page_title") }}
                      <span v-if="invoicePagination" class="badge badge-dark">{{
                        invoicePagination.total
                      }}</span>
                    </a>
                  </li>
                  <li v-if="$can('invoice-return-list')" class="nav-item bg-transparent">
                    <a class="nav-link outline_btn" href="#invoice-returns" data-toggle="tab" @click="getInvoiceReturns">
                      <i class="fas fa-undo-alt"></i>
                      {{ $t("sales.returns.index.page_title") }}
                      <span v-if="invoiceReturnPagination" class="badge badge-dark">{{ invoiceReturnPagination.total
                      }}</span>
                    </a>
                  </li>
                  <li v-if="$can('invoice-payment-list')" class="nav-item bg-transparent">
                    <a class="nav-link outline_btn" href="#invoice-payments" data-toggle="tab"
                      @click="getInvoicePayments">
                      <i class="fas fa-receipt"></i>
                      {{ $t("payments.clients.invoice.index.page_title") }}
                      <span v-if="paymentPagination" class="badge badge-dark">{{
                        paymentPagination.total
                      }}</span>
                    </a>
                  </li>
                  <li v-if="$can('non-invoice-payment-list')" class="nav-item bg-transparent">
                    <a class="nav-link outline_btn" href="#non-invoice-transactions" data-toggle="tab"
                      @click="nonInvoiceTransactions">
                      <i class="fas fa-money-bill"></i>
                      {{ $t("clients.common.non_invoice_transactions") }}
                      <span v-if="nonInvoicePagination" class="badge badge-dark">{{ nonInvoicePagination.total }}</span>
                    </a>
                  </li>
                  <li v-if="$can('invoice-list')" class="nav-item bg-transparent">
                    <a class="nav-link outline_btn" href="#ledger" data-toggle="tab" @click="getLedger">
                      <i class="fas fa-list-ul"></i>
                      {{ $t("common.ledger") }}
                    </a>
                  </li>


                </ul>
              </div>
              <div class="col-md-3 text-right mt-2">
                <div class="btn-group">
                  <a @click.prevent="openPaymentModal(allData.id, allData.hotel_id)" href="#"
                    v-tooltip="$t('common.add_payment')" class="btn btn-info btn-sm">
                    <i class="fas fa-plus" />
                  </a>
                  <a @click.prevent="openAddPaymentModal(finalBalance, allData.id, allData.hotel_id)" href="#"
                    v-tooltip="$t('common.pay_payment')" class="btn btn-info btn-sm">
                    <i class="fas fa-money-check-alt" />
                  </a>
                  <a @click="generatePDF()" href="#" class="btn btn-dark btn-sm" v-tooltip="$t('download')">
                    <i class="fas fa-download"></i>
                  </a>
                  <a @click="print()" href="#" class="btn btn-info btn-sm" v-tooltip="$t('print')">
                    <i class="fas fa-print"></i>
                  </a>
                  <a @click="refreshTable(activeTab)" href="#" v-tooltip="'Refresh'" class="btn btn-success btn-sm">
                    <i class="fas fa-sync"></i>
                  </a>

                  <router-link :to="{ name: 'clients.index' }" class="btn btn-dark float-right btn-sm" title="Back"
                    v-tooltip="$t('common.back')">
                    <i class="fas fa-long-arrow-alt-left" />
                  </router-link>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content" id="content-to-pdf">
              <div class="col-md-12" v-if="headerShow">
                <h4 class="text-capitalize">
                  {{ activeTab.replace(/-/g, " ") }}
                </h4>
                <strong> {{ $t("common.date") }}</strong> :
                {{ date | moment("Do MMM, YYYY") }}<br />
                <strong>{{ $t("common.name") }}</strong> : {{ allData.name
                }}<br />
                <strong>{{ $t("common.contact_number") }}</strong> :
                {{ allData.phoneNumber }}<br />
                <strong>{{ $t("common.email") }}</strong> : {{ allData.email
                }}<br />
                <hr />
              </div>
              <!-- Invoices -->
              <div class="tab-pane active" id="invoices">
                <div class="row no-print" id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="col-12 col-md-9 mb-2">
                    <search v-model="query" @reset-pagination="resetPagination" @reload="reload" />
                  </div>
                  <div class="col-12 col-md-3 text-right pull-right">
                    <date-range-picker ref="picker" opens="left" :locale-data="locale" :minDate="minDate"
                      :maxDate="maxDate" :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true"
                      :autoApply="true" v-model="dateRange" @update="updateValues('invoice')" :linkedCalendars="true"
                      class="c-w-100">
                      <template v-slot:input="picker" style="min-width: 350px">
                        {{ picker.startDate | startDate }} -
                        {{ picker.endDate | endDate }}
                      </template>
                    </date-range-picker>
                  </div>
                </div>

                <table-loading v-show="invoiceLoading" />
                <div class="table-responsive table-custom mt-3">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>{{ $t("common.s_no") }}</th>
                        <th>Hotel</th>
                        <th>{{ $t("common.invoice_no") }}</th>
                        <th>{{ $t("common.invoice_date") }}</th>
                        <th>{{ $t("common.net_total") }} </th>
                        <th>{{ $t("common.total_paid") }} </th>
                        <th>{{ $t("common.total_due") }} </th>
                        <th>{{ $t("common.status") }}</th>
                        <th v-if="$can('invoice-view') ||
                          $can('invoice-edit') ||
                          $can('invoice-delete')
                          " class="text-right no-print" id="element-to-hide" data-html2canvas-ignore="true">
                          {{ $t("common.action") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>

                      <tr v-show="allInvoices && allInvoices.length" v-for="(data, i) in allInvoices" :key="i">
                        <td>
                          <span v-if="invoicePagination && invoicePagination.current_page > 1">
                            {{
                              invoicePagination.per_page *
                              (invoicePagination.current_page - 1) +
                              (i + 1)
                            }}
                          </span>
                          <span v-else>{{ i + 1 }}</span>
                        </td>
                        <td>{{ allInvoices[0].hotel.hotel_name ? allInvoices[0].hotel.hotel_name : "no hotel" }}</td>
                        <td>
                          <router-link v-if="$can('invoice-view')" :to="{
                            name: 'invoices.show',
                            params: { slug: data.slug },
                          }">
                            {{ data.invoiceNo | withPrefix(invoicePrefix) }}
                          </router-link>
                          <span v-else>{{
                            data.invoiceNo | withPrefix(invoicePrefix)
                          }}</span>
                        </td>
                        <td>
                          <span v-if="data.invoiceDate">{{
                            data.invoiceDate | moment("Do MMM, YYYY")
                          }}</span>
                        </td>

                        <td>{{ data.invoiceTotal | forBalanceSheetCurrencyDecimalOnly }}</td>
                        <td>{{ data.totalPaid | forBalanceSheetCurrencyDecimalOnly }}</td>
                        <td>{{ data.due | forBalanceSheetCurrencyDecimalOnly }}</td>
                        <td>
                          <span v-if="data.status === 1" class="badge bg-success">{{ $t("common.active") }}</span>
                          <span v-else class="badge bg-danger">{{
                            $t("common.in_active")
                          }}</span>
                        </td>
                        <td v-if="$can('invoice-view') ||
                            $can('invoice-edit') ||
                            $can('invoice-delete')
                            " class="text-right no-print" id="element-to-hide" data-html2canvas-ignore="true">
                          <div class="btn-group">
                            <router-link v-if="$can('invoice-view')" v-tooltip="$t('common.view')" :to="{
                              name: 'invoices.show',
                              params: { slug: data.slug },
                            }" class="btn btn-primary btn-sm">
                              <i class="fas fa-eye" />
                            </router-link>
                            <router-link v-if="$can('invoice-edit')" v-tooltip="$t('common.edit')" :to="{
                              name: 'invoices.edit',
                              params: { slug: data.slug },
                            }" class="btn btn-info btn-sm">
                              <i class="fas fa-edit" />
                            </router-link>
                            <a v-if="$can('invoice-delete')" v-tooltip="$t('common.delete')" href="#"
                              class="btn btn-danger btn-sm" @click="deleteInvoiceData(data.slug)">
                              <i class="fas fa-trash" />
                            </a>
                          </div>
                        </td>
                      </tr>
                      <tr v-show="!allInvoices.length">
                        <td colspan="8">
                          <EmptyTable />
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- NEW PAGINATION -->
                <div v-if="invoicePagination && invoicePagination.total > 0" class="dtable-footer no-print"
                  id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="form-group row display-per-page">
                    <label>{{ $t("per_page") }} </label>
                    <div>
                      <select @change="updatePerPager('invoice')" v-model="perPage"
                        class="form-control form-control-sm ml-1">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </div>
                  </div>
                  <!-- pagination-start -->
                  <pagination v-if="invoicePagination && invoicePagination.last_page > 1" :pagination="invoicePagination"
                    :offset="5" class="justify-flex-end" @paginate="paginate" />
                  <!-- pagination-end -->
                </div>
                <!-- NEW PAGINATION END -->
              </div>
              <!-- Invoices Returns -->
              <div class="tab-pane" id="invoice-returns">
                <div class="row no-print" id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="col-12 col-md-9 mb-2">
                    <search v-model="invoiceReturnQuery" @reset-pagination="resetReturnPagination"
                      @reload="returnReload" />
                  </div>
                  <div class="col-12 col-md-3 text-right pull-right">
                    <date-range-picker ref="picker" opens="left" :locale-data="locale" :minDate="minDate"
                      :maxDate="maxDate" :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true"
                      :autoApply="true" v-model="dateRange" @update="updateValues('invoice-returns')"
                      :linkedCalendars="true" class="c-w-100">
                      <template v-slot:input="picker" style="min-width: 350px">
                        {{ picker.startDate | startDate }} -
                        {{ picker.endDate | endDate }}
                      </template>
                    </date-range-picker>
                  </div>
                </div>
                <table-loading v-show="invoiceReturnLoading" />
                <div class="table-responsive table-custom mt-3">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>{{ $t("common.s_no") }}</th>
                        <th>{{ $t("common.return_no") }}</th>
                        <th>{{ $t("common.invoice_no") }}</th>
                        <th>{{ $t("common.return_reason") }}</th>
                        <th>{{ $t("common.return_cost") }}</th>
                        <th>{{ $t("common.date") }}</th>
                        <th>{{ $t("common.status") }}</th>
                        <th v-if="$can('invoice-return-edit') ||
                          $can('invoice-return-view') ||
                          $can('invoice-return-delete')
                          " class="text-right no-print" id="element-to-hide" data-html2canvas-ignore="true">
                          {{ $t("common.action") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-show="allReturns.length" v-for="(data, i) in allReturns" :key="i">
                        <td>
                          <span v-if="pagination && pagination.current_page > 1">
                            {{
                              pagination.per_page *
                              (pagination.current_page - 1) +
                              (i + 1)
                            }}
                          </span>
                          <span v-else>{{ i + 1 }}</span>
                        </td>
                        <td>
                          <router-link v-if="$can('invoice-return-view')" :to="{
                            name: 'invoiceReturns.show',
                            params: { slug: data.invoiceSlug },
                          }">
                            {{
                              data.returnNo | withPrefix(invoiceReturnPrefix)
                            }}
                          </router-link>
                          <span v-else>{{
                            data.returnNo | withPrefix(invoiceReturnPrefix)
                          }}</span>
                        </td>
                        <td>
                          {{ data.invoiceNo | withPrefix(invoicePrefix) }}
                        </td>
                        <td>{{ data.reason }}</td>
                        <td>{{ data.totalReturn | withCurrency }}</td>
                        <td>
                          <span v-if="data.returnDate">{{
                            data.returnDate | moment("Do MMM, YYYY")
                          }}</span>
                        </td>
                        <td>
                          <span v-if="data.status === 1" class="badge bg-success">{{ $t("common.active") }}</span>
                          <span v-else class="badge bg-danger">{{
                            $t("common.in_active")
                          }}</span>
                        </td>
                        <td v-if="$can('invoice-return-edit') ||
                            $can('invoice-return-view') ||
                            $can('invoice-return-delete')
                            " class="text-right no-print" id="element-to-hide" data-html2canvas-ignore="true">
                          <div class="btn-group">
                            <router-link v-if="$can('invoice-return-view')" v-tooltip="$t('common.view')" :to="{
                              name: 'invoiceReturns.show',
                              params: { slug: data.slug },
                            }" class="btn btn-primary btn-sm">
                              <i class="fas fa-eye" />
                            </router-link>
                            <router-link v-if="$can('invoice-return-edit')" v-tooltip="$t('common.edit')" :to="{
                              name: 'invoiceReturns.edit',
                              params: { slug: data.slug },
                            }" class="btn btn-info btn-sm">
                              <i class="fas fa-edit" />
                            </router-link>
                            <a v-if="$can('invoice-return-delete')" v-tooltip="$t('common.delete')" href="#"
                              class="btn btn-danger btn-sm" @click="deleteInvoiceReturnData(data.slug)">
                              <i class="fas fa-trash" />
                            </a>
                          </div>
                        </td>
                      </tr>
                      <tr v-show="!allReturns.length">
                        <td colspan="8">
                          <EmptyTable />
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- NEW PAGINATION -->
                <div v-if="invoiceReturnPagination && invoiceReturnPagination.total > 0
                  " class="dtable-footer no-print" id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="form-group row display-per-page">
                    <label>{{ $t("per_page") }} </label>
                    <div>
                      <select @change="updatePerPager('invoice-returns')" v-model="perPage"
                        class="form-control form-control-sm ml-1">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </div>
                  </div>
                  <!-- pagination-start -->
                  <pagination v-if="invoiceReturnPagination &&
                    invoiceReturnPagination.last_page > 1
                    " :pagination="allReturns ? invoiceReturnPagination : { current_page: 1 }
    " :offset="5" class="justify-flex-end" @paginate="invoiceReturnPaginate" />
                  <!-- pagination-end -->
                </div>
                <!-- NEW PAGINATION END -->
              </div>
              <!-- Invoices Payments -->
              <div class="tab-pane" id="invoice-payments">
                <div class="row no-print" id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="col-12 col-md-9 mb-2">
                    <search v-model="paymentsQuery" @reset-pagination="resetPaymentsPagination"
                      @reload="paymentsReload" />
                  </div>
                  <div class="col-12 col-md-3 text-right pull-right">
                    <date-range-picker ref="picker" opens="left" :locale-data="locale" :minDate="minDate"
                      :maxDate="maxDate" :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true"
                      :autoApply="true" v-model="dateRange" @update="updateValues('invoice-payments')"
                      :linkedCalendars="true" class="c-w-100">
                      <template v-slot:input="picker" style="min-width: 350px">
                        {{ picker.startDate | startDate }} -
                        {{ picker.endDate | endDate }}
                      </template>
                    </date-range-picker>
                  </div>
                </div>
                <table-loading v-show="paymentsLoading" />
                <div class="table-responsive table-custom mt-3">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>{{ $t("common.s_no") }}</th>
                        <th>{{ $t("common.invoice_no") }}</th>
                        <th>{{ $t("common.total") }}</th>
                        <th>{{ $t("common.paid_amount") }}</th>
                        <th>{{ $t("common.account") }}</th>
                        <th>{{ $t("common.payment_date") }}</th>
                        <th>{{ $t("common.status") }}</th>
                        <th v-if="$can('invoice-payment-edit') ||
                          $can('invoice-payment-view') ||
                          $can('invoice-payment-delete')
                          " class="text-right no-print" id="element-to-hide" data-html2canvas-ignore="true">
                          {{ $t("common.action") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-show="allPayments.length" v-for="(data, i) in allPayments" :key="i">
                        <td>
                          <span v-if="pagination && pagination.current_page > 1">
                            {{
                              pagination.per_page *
                              (pagination.current_page - 1) +
                              (i + 1)
                            }}
                          </span>
                          <span v-else>{{ i + 1 }}</span>
                        </td>
                        <td v-if="data.invoice && invoicePrefix">
                          <router-link v-if="$can('invoice-view')" :to="{
                            name: 'invoices.show',
                            params: { slug: data.invoice.slug },
                          }">
                            {{
                              data.invoice.invoiceNo | withPrefix(invoicePrefix)
                            }}
                          </router-link>
                          <span v-else>{{
                            data.invoice.invoiceNo | withPrefix(invoicePrefix)
                          }}</span>
                        </td>
                        <td v-if="data.invoice">
                          {{ data.invoice.invoiceTotal | withCurrency }}
                        </td>
                        <td>{{ data.amount | withCurrency }}</td>
                        <td>
                          <span v-if="data.account">{{
                            data.account.label
                          }}</span>
                        </td>
                        <td>
                          <span v-if="data.date">{{
                            data.date | moment("Do MMM, YYYY")
                          }}</span>
                        </td>
                        <td>
                          <span v-if="data.status === 1" class="badge bg-success">{{ $t("common.active") }}</span>
                          <span v-else class="badge bg-danger">{{
                            $t("common.in_active")
                          }}</span>
                        </td>
                        <td v-if="$can('invoice-payment-edit') ||
                            $can('invoice-payment-view') ||
                            $can('invoice-payment-delete')
                            " class="text-right no-print" id="element-to-hide" data-html2canvas-ignore="true">
                          <div class="btn-group">
                            <router-link v-if="$can('invoice-payment-view')" v-tooltip="$t('common.view')" :to="{
                              name: 'invoicePayments.show',
                              params: { slug: data.slug },
                            }" class="btn btn-primary btn-sm">
                              <i class="fas fa-eye" />
                            </router-link>
                            <router-link v-if="$can('invoice-payment-edit')" v-tooltip="$t('common.edit')" :to="{
                              name: 'invoicePayments.edit',
                              params: { slug: data.slug },
                            }" class="btn btn-info btn-sm">
                              <i class="fas fa-edit" />
                            </router-link>
                            <a v-if="$can('invoice-payment-delete')" v-tooltip="$t('common.delete')" href="#"
                              class="btn btn-danger btn-sm" @click="deletePaymentData(data.slug)">
                              <i class="fas fa-trash" />
                            </a>
                          </div>
                        </td>
                      </tr>
                      <tr v-show="!allPayments.length">
                        <td colspan="9">
                          <EmptyTable />
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- NEW PAGINATION -->
                <div v-if="paymentPagination && paymentPagination.total > 0" class="dtable-footer no-print"
                  id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="form-group row display-per-page">
                    <label>{{ $t("per_page") }} </label>
                    <div>
                      <select @change="updatePerPager('invoice-payments')" v-model="perPage"
                        class="form-control form-control-sm ml-1">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </div>
                  </div>
                  <!-- pagination-start -->
                  <pagination v-if="paymentPagination && paymentPagination.last_page > 1" :pagination="paymentPagination"
                    :offset="5" class="justify-flex-end" @paginate="paymentsPaginate" />
                  <!-- pagination-end -->
                </div>
                <!-- NEW PAGINATION END -->
              </div>
              <!-- Invoices Transactions -->
              <div class="tab-pane" id="non-invoice-transactions">
                <div class="row no-print" id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="col-12 col-md-9 mb-2">
                    <search v-model="nonInvoiceQuery" @reset-pagination="resetNonInvoiceTransPagination"
                      @reload="nonInvoiceTransReload" />
                  </div>
                  <div class="col-12 col-md-3 text-right pull-right">
                    <date-range-picker ref="picker" opens="left" :locale-data="locale" :minDate="minDate"
                      :maxDate="maxDate" :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true"
                      :autoApply="true" v-model="dateRange" @update="updateValues('non-invoice-transactions')"
                      :linkedCalendars="true" class="c-w-100">
                      <template v-slot:input="picker" style="min-width: 350px">
                        {{ picker.startDate | startDate }} -
                        {{ picker.endDate | endDate }}
                      </template>
                    </date-range-picker>
                  </div>
                </div>
                <table-loading v-show="nonInvoiceTransLoading" />
                <div class="table-responsive table-custom mt-3">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>{{ $t("common.s_no") }}</th>
                        <th>{{ $t("common.payment_type") }}</th>
                        <th>{{ $t("common.paid_amount") }}</th>
                        <th>{{ $t("common.account") }}</th>
                        <th>{{ $t("common.payment_date") }}</th>
                        <th>{{ $t("common.status") }}</th>
                        <th v-if="$can('non-invoice-payment-edit') ||
                          $can('non-invoice-payment-view') ||
                          $can('non-invoice-payment-delete')
                          " class="text-right no-print" id="element-to-hide" data-html2canvas-ignore="true">
                          {{ $t("common.action") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-show="allNonInvoiceTrans.length" v-for="(data, i) in allNonInvoiceTrans" :key="i">
                        <td>
                          <span v-if="pagination && pagination.current_page > 1">
                            {{
                              pagination.per_page *
                              (pagination.current_page - 1) +
                              (i + 1)
                            }}
                          </span>
                          <span v-else>{{ i + 1 }}</span>
                        </td>
                        <td>
                          <span v-if="data.type === 1" class="badge bg-primary">{{ $t("payments.common.due_paid")
                          }}</span>
                          <span v-else class="badge bg-danger">{{
                            $t("payments.common.due_added")
                          }}</span>
                        </td>
                        <td>{{ data.amount | withCurrency }}</td>
                        <td>
                          <span v-if="data.account">{{
                            data.account.label
                          }}</span>
                        </td>
                        <td>
                          <span v-if="data.date">{{
                            data.date | moment("Do MMM, YYYY")
                          }}</span>
                        </td>
                        <td>
                          <span v-if="data.status === 1" class="badge bg-success">{{ $t("common.active") }}</span>
                          <span v-else class="badge bg-danger">{{
                            $t("common.in_active")
                          }}</span>
                        </td>
                        <td v-if="$can('non-invoice-payment-edit') ||
                            $can('non-invoice-payment-view') ||
                            $can('non-invoice-payment-delete')
                            " class="text-right no-print" id="element-to-hide" data-html2canvas-ignore="true">
                          <div class="btn-group">
                            <router-link v-if="$can('non-invoice-payment-edit')" v-tooltip="$t('common.edit')" :to="{
                              name: 'nonInvoicePayments.edit',
                              params: { slug: data.slug },
                            }" class="btn btn-info btn-sm">
                              <i class="fas fa-edit" />
                            </router-link>
                            <a v-if="$can('non-invoice-payment-delete')" v-tooltip="$t('common.delete')" href="#"
                              class="btn btn-danger btn-sm" @click="deleteNonInvoicePayment(data.slug)">
                              <i class="fas fa-trash" />
                            </a>
                          </div>
                        </td>
                      </tr>
                      <tr v-show="!allNonInvoiceTrans.length">
                        <td colspan="7">
                          <EmptyTable />
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- NEW PAGINATION -->
                <div v-if="nonInvoicePagination && nonInvoicePagination.total > 0" class="dtable-footer"
                  id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="form-group row display-per-page">
                    <label>{{ $t("per_page") }} </label>
                    <div>
                      <select @change="updatePerPager('non-invoice-transactions')" v-model="perPage"
                        class="form-control form-control-sm ml-1">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </div>
                  </div>
                  <!-- pagination-start -->
                  <pagination v-if="nonInvoicePagination && nonInvoicePagination.last_page > 1
                    " :pagination="nonInvoicePagination" :offset="5" class="justify-flex-end"
                    @paginate="nonInvoiceTransPaginate" />
                  <!-- pagination-end -->
                </div>
                <!-- NEW PAGINATION END -->
              </div>
              <!--ledger-->
              <div class="tab-pane print-area" id="ledger">
                <div class="row no-print" id="element-to-hide" data-html2canvas-ignore="true">
                  <div class="col-12 col-md-9 mb-2">
                    <!-- <search v-model="query" @reset-pagination="resetPagination" @reload="reload" /> -->
                  </div>
                  <div class="col-12 col-md-3 text-right pull-right">
                    <date-range-picker ref="picker" opens="left" :locale-data="locale" :minDate="minDate"
                      :maxDate="maxDate" :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true"
                      :autoApply="true" v-model="ledgerDateRange" @update="updateValues('ledger')" :linkedCalendars="true"
                      class="c-w-100">
                      <template v-slot:input="Lpicker" style="min-width: 350px">
                        {{ Lpicker.startDate | startDate }} -
                        {{ Lpicker.endDate | endDate }}
                      </template>
                    </date-range-picker>
                  </div>
                </div>
                <table-loading v-show="ledgerLoading" />
                <div class="table-responsive table-custom mt-3">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>{{ $t("common.s_no") }}</th>
                        <th>Hotel</th>
                        <th>{{ $t("common.date") }}</th>
                        <th>{{ $t("common.particular") }}</th>
                        <th>{{ $t("common.debit") }}</th>
                        <th>{{ $t("common.credit") }}</th>
                        <!-- <th>{{ $t("common.discount") }}</th> -->
                        <th>{{ $t("common.balance") }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(data, i) in ledgerItems" :key="i">
                        <td>{{ i + 1 }}</td>
                        <td>{{ allInvoices[0]?.hotel.hotel_name ?? 'hotel' }}</td>
                        <td>
                          {{ data.date | moment("Do MMM, YYYY") }}
                        </td>
                        <td>
                          <router-link v-if="$can('invoice-view') &&
                            data.action_type == 'invoice'
                            ":to="{ name: 'invoices.show', params: { slug: data.slug },}">
                            {{ data.particulars }}
                          </router-link>

                          <span v-if="data.action_type == 'invoice-advance-payment' || data.action_type == 'invoice-return'">{{ data.particulars }}</span>
                        </td>
                        <td>{{ data.debit | forBalanceSheetCurrencyDecimalOnly }}</td>
                        <td>{{ data.credit | forBalanceSheetCurrencyDecimalOnly }}</td>
                        <!-- <td>{{ data.discount }}</td> -->
                        <td>{{ data.balance | forBalanceSheetCurrencyDecimalOnly }}</td>
                      </tr>
                      <tr v-show="!ledgerItems.length">
                        <td colspan="7">
                          <EmptyTable />
                        </td>
                      </tr>
                      <!-- <tr v-if="ledgerItems[ledgerItems.length - 1]">
                        <td>{{ ledgerItems.length + 1 }}</td>
                        <td>{{ date | moment("Do MMM, YYYY") }}</td>
                        <td>{{ $t("common.non_invoice_due") }}</td>
                        <td>{{ 0 | withCurrency }}</td>
                        <td>
                          {{ allData.nonInvoiceCurrentDue | withCurrency }}
                        </td>
                        <td>{{ 0 | withCurrency }}</td>
                        <td>
                          {{
                            (ledgerItems[ledgerItems.length - 1].balance +
                              allData.nonInvoiceCurrentDue)
                              | withCurrency
                          }}
                        </td>
                      </tr> -->
                    </tbody>
                    <tfoot>
                      <tr v-if="ledgerItems[ledgerItems.length - 1]">
                        <td colspan="4">{{ $t("common.summery") }}</td>

                        <td>
                          {{
                            (ledgerTotalDebit + allData.nonInvoiceCurrentDue)
                            | forBalanceSheetCurrencyDecimalOnly
                          }}
                        </td>
                        <td>{{ ledgerTotalCredit | forBalanceSheetCurrencyDecimalOnly }}</td>
                        <!-- <td>{{ ledgerTotalDiscount   }}</td> -->
                        <td>
                          {{
                            finalBalance | forBalanceSheetCurrencyDecimalOnly
                          }}
                          <!-- [{{ $t("common.total_due") }}] -->
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <!-- <div class="card-footer">
                    <div class="dtable-footer">
                      <div class="form-group row display-per-page">
                        <label>{{ $t("per_page") }} </label>
                        <div>
                          <select
                            @change="updatePerPager('ledger-information')"
                            v-model="perPage"
                            class="form-control form-control-sm ml-1"
                          >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                          </select>
                        </div>
                      </div>
                   
                      <pagination
                        v-if="ledgerPagination && ledgerPagination.last_page > 1"
                        :pagination="ledgerPagination"
                        :offset="10"
                        class="justify-flex-end"
                        @paginate="ledgerPaginate"
                      />
                     
                    </div>
                  </div> -->
              </div>
            </div>
            <small class="ml-2">
              <i>
                All prices are in {{ '' | defaultwithCurrency }}
              </i>
            </small>
          </div>
        </div>
      </div>
    </div>
    <!-- use the modal component, pass in the prop -->
    <!-- <Modal v-if="showModal" @close="previewModal()">
      <h5 slot="header">{{ $t("common.modal_header") }}</h5>
      <div class="w-100" slot="body"> -->
        <Modal class="profile_img_model" v-if="showModal" @close="previewModal()">
  <h5 slot="header">{{ $t("common.modal_header") }}</h5>
  <div class="model_img_box w-100" slot="body">
    <img v-for="(image, index) in allData.images" :key="index" :src="`http://127.0.0.1:8000/images/clients/${image}`" class="img-fluid" loading="lazy" />
    <!-- <img data-v-d7f4c2e6="" src="http://127.0.0.1:8000/images/clients/17092911510VKSlJ5uD6nx.png" loading="lazy" class="profile-user-img img-fluid img-circle"> -->
  </div>
</Modal>

<!-- {{ allData.images[0] }}

        <img :src="allData.image" class="img-fluid" loading="lazy" />
        <img :src="allData.image" class="img-fluid" loading="lazy" />
        <img :src="allData.images" class="img-fluid" loading="lazy" />
      </div>
    </Modal> -->

    <!-- <Modal v-if="showModal" @close="previewModal()">
  <h5 slot="header">{{ $t("common.modal_header") }}</h5>
  <div class="w-100" slot="body">
    <img :src="allData.image1" class="img-fluid" loading="lazy" />
    <img :src="allData.image2" class="img-fluid" loading="lazy" />
    <img :src="allData.image3" class="img-fluid" loading="lazy" />
  </div>
</Modal> -->



    <Modal v-if="showAddPaymentModal" @close="showAddPaymentModal = false">
      <h5 slot="header">
        Pay Payment to {{ allData.name }}
        <!-- {{ $t("payments.common.commission_payment") }} -->
      </h5>
      <div slot="body" class="row">
        <form role="form" @submit.prevent="savePayment" @keydown="form.onKeydown($event)" class="w-100">
          <div class="row">
            <div class="form-group col-md-6">
              <label for="clientInvoiceTotal">{{ $t('common.net_payable_amount') }}</label>
              <input type="text" class="form-control" readonly v-model="form.netTotal" />
            </div>
            <div class="form-group col-md-6">
              <label for="paidAmount">{{ $t("common.paid_amount") }}</label>
              <input v-model="form.paidAmount" type="number" step="any" class="form-control"
                :placeholder="$t('common.paid_amount_placeholder')" required min="1" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <input type="checkbox" v-model="form.commission" />
              <label for="commission">Commission Paid</label>
            </div>
            <div class="form-group col-md-6">

            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="account">{{ $t("common.account") }} <span class="required">*</span></label>
              <v-select :class="{ 'is-invalid': form.errors.has('account') }" v-model="form.account" :options="accounts"
                label="label" name="account" :placeholder="$t('common.account_placeholder')" />
              <has-error :form="form" field="account" />
            </div>
            <div class="form-group col-md-6">
              <label for="paymentDate">{{ $t("common.payment_date") }}</label>
              <input v-model="form.paymentDate" :class="{ 'is-invalid': form.errors.has('paymentDate') }" id="paymentDate"
                type="date" class="form-control" name="paymentDate" />
              <has-error :form="form" field="paymentDate" />
            </div>
          </div>

          <div class="form-group">
            <label for="note">{{ $t("common.note") }}</label>
            <textarea id="note" v-model="form.note" class="form-control"
              :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('common.note_placeholder')" />
            <has-error :form="form" field="note" />
          </div>
        </form>

      </div>
      <template #modal-footer>
        <div class="mt-1">
          <button class="modal-default-button btn btn-primary" @click="savePayment">
            <i class="fas fa-save" /> {{ $t("common.save") }}
          </button>
          <button class="modal-default-button btn btn-secondary" @click="showAddPaymentModal = false">
            <i class="fas fa-times"></i> {{ $t("common.close") }}
          </button>
        </div>
      </template>
    </Modal>

    <Modal v-if="showPaymentModal" @close="showPaymentModal = false">
      <h5 slot="header">
        Add Payment to {{ allData.name }}
        <!-- {{ $t("payments.common.commission_payment") }} -->
      </h5>
      <div slot="body" class="row">
        <form role="form" @submit.prevent="savePaymentForClient" @keydown="form.onKeydown($event)" class="w-100">
          <div class="row">
            <div class="form-group col-md-6">
              <label for="paidAmount">{{ $t("common.paid_amount") }}</label>
              <input v-model="advancePayForm.paidAmount" type="number" step="any" class="form-control"
                :placeholder="$t('common.paid_amount_placeholder')" required min="1" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="account">{{ $t("common.account") }} <span class="required">*</span></label>
              <v-select :class="{ 'is-invalid': advancePayForm.errors.has('account') }" v-model="advancePayForm.account"
                :options="accounts" label="label" name="account" :placeholder="$t('common.account_placeholder')" />
              <has-error :form="advancePayForm" field="account" />
            </div>
            <div class="form-group col-md-6">
              <label for="paymentDate">{{ $t("common.payment_date") }}</label>
              <input v-model="advancePayForm.paymentDate"
                :class="{ 'is-invalid': advancePayForm.errors.has('paymentDate') }" id="paymentDate" type="date"
                class="form-control" name="paymentDate" />
              <has-error :form="advancePayForm" field="paymentDate" />
            </div>
          </div>

          <div class="form-group">
            <label for="note">{{ $t("common.note") }}</label>
            <textarea id="note" v-model="advancePayForm.note" class="form-control"
              :class="{ 'is-invalid': advancePayForm.errors.has('note') }" :placeholder="$t('common.note_placeholder')" />
            <has-error :form="advancePayForm" field="note" />
          </div>
        </form>

      </div>
      <template #modal-footer>
        <div class="mt-1">
          <button class="modal-default-button btn btn-primary" @click="savePaymentForClient">
            <i class="fas fa-save" /> {{ $t("common.save") }}
          </button>
          <button class="modal-default-button btn btn-secondary" @click="showPaymentModal = false">
            <i class="fas fa-times"></i> {{ $t("common.close") }}
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import moment from "moment";
import i18n from "~/plugins/i18n";
import { mapGetters } from "vuex";
import html2pdf from "html2pdf.js";
import DateRangePicker from "vue2-daterange-picker";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("clients.view.page_title") };
  },
  components: {
    DateRangePicker,
  },
  data: () => ({
    breadcrumbsCurrent: "clients.view.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "clients.view.breadcrumbs_first",
        url: "home",
      },
      {
        name: "clients.view.breadcrumbs_second",
        url: "clients.index",
      },
      {
        name: "clients.view.breadcrumbs_active",
        url: "",
      },
    ],
    paymentsLoading: false,
    invoiceReturnLoading: false,
    invoiceLoading: false,
    creditsLoading: false,
    debitsLoading: false,
    currentClass: 'col-md-12 col-lg-12',
    invisible: false,
    nonInvoiceTransLoading: false,
    url: null,
    showModal: false,
    showPaymentModal: false,
    allData: "",
    allPayments: "",
    allReturns: "",
    allNonInvoiceTrans: "",
    allInvoices: "",
    paymentPagination: "",
    invoicePagination: "",
    invoiceReturnPagination: "",
    nonInvoicePagination: "",
    ledgerPagination: "",
    query: "",
    invoiceReturnQuery: "",
    paymentsQuery: "",
    nonInvoiceQuery: "",
    clientPrefix: "",
    invoicePrefix: "",
    invoiceReturnPrefix: "",
    perPage: 10,
    minDate: moment(new Date("01-01-2021")).format("YYYY-MM-DD"),
    maxDate: moment().add(1, "days").format("YYYY-MM-DD"),
    dateRange: {
      startDate: "",
      endDate: "",
    },
    ledgerDateRange: {
      startDate: moment().subtract(7, 'd').format('YYYY-MM-DD'),
      endDate: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss.SSS'),
    },
    locale: {
      direction: "ltr",
      format: "YYYY-MM-DD",
      separator: " - ",
      applyLabel: "Apply",
      cancelLabel: "Cancel",
      weekLabel: "W",
      customRangeLabel: "Custom Range",
      daysOfWeek: moment.weekdaysMin(),
      monthNames: moment.monthsShort(),
      firstDay: 1,
    },
    ledgerItems: [],
    ledgerTotalDiscount: 0,
    ledgerTotalDebit: 0,
    ledgerTotalCredit: 0,
    finalBalance: 0,
    headerShow: false,
    date: new Date().toISOString().slice(0, 10),
    activeTab: "invoices",
    accounts: [],
    showAddPaymentModal: false,
    filterOpen: false,
    form: new Form({
      paidAmount: 0,
      customerId: "",
      paymentDate: new Date().toISOString().slice(0, 10),
      hotelId: "",
      account: "",
      note: "",
      netTotal: 0,
      status: 1,
      bank_only: 0,
      commission: false,
    }),
    advancePayForm: new Form({
      paidAmount: 0,
      customerId: "",
      paymentDate: new Date().toISOString().slice(0, 10),
      hotelId: "",
      account: "",
      note: "",
    }),
  }),
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
    ...mapGetters("operations", ["items", "loading", "pagination", "appInfo", "selectedHotel"]),
  },
  watch: {
    selectedHotel: function () {
      this.getLedger();
    },
    // watch invoice search data
    query: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchInvoicesData();
        } else {
          this.getInvoices();
        }
      } else {
        this.searchInvoicesData();
      }
    },
    // watch invoice return search data
    invoiceReturnQuery: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchReturnData();
        } else {
          this.getInvoiceReturns();
        }
      } else {
        this.searchReturnData();
      }
    },
    // watch payment search data
    paymentsQuery: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchPaymentData();
        } else {
          this.getInvoicePayments();
        }
      } else {
        this.searchPaymentData();
      }
    },

    // watch non invoice transaction search data
    nonInvoiceQuery: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchNonInvoiceTransactions();
        } else {
          this.nonInvoiceTransactions();
        }
      } else {
        this.searchNonInvoiceTransactions();
      }
    },
  },
  created() {
    this.getClient();
    this.getInvoices();
    this.getAccounts();
    this.getLedger();
    this.clientPrefix = this.appInfo.clientPrefix;
    this.invoicePrefix = this.appInfo.invoicePrefix;
    this.invoiceReturnPrefix = this.appInfo.invoiceReturnPrefix;

    Fire.$on("AfterDelete", () => {
      this.getInvoices();
      this.getInvoiceReturns();
      this.getInvoicePayments();
      this.nonInvoiceTransactions();
    });
    Fire.$on("AfterAddPayment", () => {
      this.getLedger();
    });
  },
  methods: {
    ShowDetail(e) {
      e.preventDefault()
      if (this.invisible) {
        this.currentClass = 'col-md-12 col-lg-12';
      } else {
        this.currentClass = 'col-md-12 col-lg-9';
      }
      this.invisible = !this.invisible;
    },

    switchTab(tabName) {
      switch (tabName) {
        case "invoice":
          this.searchInvoicesData();
          break;
        case "invoice-returns":
          this.searchReturnData();
          break;
        case "invoice-payments":
          this.searchPaymentData();
          break;
        case "non-invoice-transactions":
          this.searchNonInvoiceTransactions();
          break;
        case "ledger":
          this.getLedger();
          break;
      }
    },
    // filter data for selected date range
    async updateValues(tabName) {
      if (tabName == 'ledger') {

        this.ledgerDateRange.startDate = moment(this.ledgerDateRange.startDate).format(
          "YYYY-MM-DD"
        );
        this.ledgerDateRange.endDate = moment(this.ledgerDateRange.endDate).format(
          "YYYY-MM-DD"
        );
      } else {
        this.dateRange.startDate = moment(this.dateRange.startDate).format(
          "YYYY-MM-DD"
        );
        this.dateRange.endDate = moment(this.dateRange.endDate).format(
          "YYYY-MM-DD"
        );
      }

      await this.switchTab(tabName);
    },
    // refresh table
    refreshTable(tabName) {
      this.query = "";
      this.dateRange.startDate = null;
      this.dateRange.endDate = null;
      setTimeout(
        function () {
          this.dateRange.startDate = "";
          this.dateRange.endDate = "";
          this.switchTab(tabName);
        }.bind(this),
        1000
      );
    },

    // get the client
    async getClient() {
      const { data } = await axios.get(
        window.location.origin + "/api/clients/" + this.$route.params.slug
      );
      this.allData = data.data;
      console.log(this.allData);
    },

    // update per page count
    updatePerPager(tabName) {
      this.pagination.current_page = 1;
      this.invoicePagination.hasOwnProperty("current_page")
        ? (this.invoicePagination.current_page = 1)
        : "";
      this.invoiceReturnPagination.hasOwnProperty("current_page")
        ? (this.invoiceReturnPagination.current_page = 1)
        : "";
      this.paymentPagination.hasOwnProperty("current_page")
        ? (this.paymentPagination.current_page = 1)
        : "";
      this.nonInvoicePagination.hasOwnProperty("current_page")
        ? (this.nonInvoicePagination.current_page = 1)
        : "";
      this.ledgerPagination.hasOwnProperty("current_page")
        ? (this.ledgerPagination.current_page = 1)
        : "";
      this.switchTab(tabName);
    },

    // get the client invoices
    async getInvoices() {
      this.activeTab = "invoices";
      this.invoiceLoading = true;
      let currentPage = this.invoicePagination ? this.invoicePagination.current_page : 1;
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/all-invoices?page=" +
        currentPage +
        "&perPage=" +
        this.perPage
      );
      // const { data } =  await this.$store.dispatch("operations/fetchData", {
      //   path: "/api/client/" + this.$route.params.slug + "/all-invoices/?page=",
      //   currentPage: currentPage + "&perPage=" + this.perPage,
      // });
      this.allInvoices = data.data;
      // this.hotel_name = this.allInvoices[0]?.hotel?.hotel_name;
      // console.log(this.hotel_name);
      // console.log(this.allInvoices);
      this.invoicePagination = data.meta;
      this.invoiceLoading = false;
    },

    // search invoices
    async searchInvoicesData() {
      this.invoiceLoading = true;
      let currentPage = this.invoicePagination ? this.invoicePagination.current_page : 1;
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/all-invoices/search?page=" +
        currentPage +
        "&perPage=" +
        this.perPage +
        "&term=" +
        this.query +
        "&startDate=" +
        this.dateRange.startDate +
        "&endDate" +
        this.dateRange.endDate
      );
      // const { data } =  await this.$store.dispatch("operations/searchData", {
      //   path: "/api/client/" + this.$route.params.slug + "/all-invoices/search",
      //   term: this.query,
      //   currentPage: currentPage + "&perPage=" + this.perPage,
      //   startDate: this.dateRange.startDate,
      //   endDate: this.dateRange.endDate,
      // });
      this.allInvoices = data.data;
      this.invoicePagination = data.meta;
      this.invoiceLoading = false;
    },

    // pagination
    async paginate(page) {
      this.invoicePagination.current_page = page;
      this.query === "" ? this.getInvoices() : this.searchInvoicesData();
    },

    // reset purchase pagination
    async resetPagination() {
      this.invoicePagination.current_page = 1;
    },

    // reload purchases after search
    async reload() {
      this.query = "";
      await this.searchInvoicesData();
    },

    // get client invoice returns
    async getInvoiceReturns() {
      this.activeTab = "invoice-returns";
      this.invoiceReturnLoading = true;
      let currentPage = this.allReturns
        ? this.invoiceReturnPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/invoice-returns?page=" +
        currentPage +
        "&perPage=" +
        this.perPage
      );
      this.allReturns = data.data;
      this.invoiceReturnPagination = data.meta;
      this.invoiceReturnLoading = false;
    },

    // search invoice returns
    async searchReturnData() {
      this.invoiceReturnLoading = true;
      let currentPage = this.allReturns
        ? this.invoiceReturnPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/invoice-returns/search" +
        "?term=" +
        this.invoiceReturnQuery +
        "&page=" +
        currentPage +
        "&perPage=" +
        this.perPage +
        "&startDate=" +
        this.dateRange.startDate +
        "&endDate=" +
        this.dateRange.endDate
      );
      this.allReturns = data.data;
      this.invoiceReturnPagination = data.meta;
      this.invoiceReturnLoading = false;
    },

    // invoice return pagination
    async invoiceReturnPaginate() {
      this.query === "" ? this.getInvoiceReturns() : this.searchReturnData();
    },

    // reset invoice return pagination
    async resetReturnPagination() {
      this.invoiceReturnPagination.current_page = 1;
    },

    // Reload purchases after search
    async returnReload() {
      this.invoiceReturnQuery = "";
      await this.searchReturnData();
    },

    // Get the invoice payments
    async getInvoicePayments() {
      this.activeTab = "invoice-payments";
      this.paymentsLoading = true;
      let currentPage = this.allPayments
        ? this.paymentPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/invoice-payments?page=" +
        currentPage +
        "&perPage=" +
        this.perPage
      );
      this.allPayments = data.data;
      this.paymentPagination = data.meta;
      this.paymentsLoading = false;
    },

    // search payments
    async searchPaymentData() {
      this.paymentsLoading = true;
      let currentPage = this.allPayments
        ? this.paymentPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/invoice-payments/search" +
        "?term=" +
        this.paymentsQuery +
        "&page=" +
        currentPage +
        "&perPage=" +
        this.perPage +
        "&startDate=" +
        this.dateRange.startDate +
        "&endDate=" +
        this.dateRange.endDate
      );
      this.allPayments = data.data;
      this.paymentPagination = data.meta;
      this.paymentsLoading = false;
    },

    // Payments pagination
    async paymentsPaginate(page) {
      this.paymentPagination.current_page = page
      this.query === this.getInvoicePayments() ? this.searchPaymentData() : "";
    },

    // Reset payments pagination
    async resetPaymentsPagination() {
      this.paymentPagination.current_page = 1;
    },

    // Reload payments after search
    async paymentsReload() {
      this.paymentsQuery = "";
      await this.searchPaymentData();
    },

    // Get the non invoice transactions
    async nonInvoiceTransactions() {
      this.activeTab = "non-invoice-transactions";
      this.nonInvoiceTransLoading = true;
      let currentPage = this.allNonInvoiceTrans
        ? this.nonInvoicePagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/non-invoice-payments?page=" +
        currentPage +
        "&perPage=" +
        this.perPage
      );
      this.allNonInvoiceTrans = data.data;
      this.nonInvoicePagination = data.meta;
      this.nonInvoiceTransLoading = false;
    },

    // search non invoice transactions
    async searchNonInvoiceTransactions() {
      this.nonInvoiceTransLoading = true;
      let currentPage = this.allNonInvoiceTrans
        ? this.nonInvoicePagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/non-invoice-payments/search" +
        "?term=" +
        this.nonInvoiceQuery +
        "&page=" +
        currentPage +
        "&perPage=" +
        this.perPage +
        "&startDate=" +
        this.dateRange.startDate +
        "&endDate=" +
        this.dateRange.endDate
      );
      this.allNonInvoiceTrans = data.data;
      this.nonInvoicePagination = data.meta;
      this.nonInvoiceTransLoading = false;
    },

    // non invoice transactions pagination
    async nonInvoiceTransPaginate() {
      this.query === this.nonInvoiceTransactions()
        ? this.searchNonInvoiceTransactions()
        : "";
    },

    // Reset non invoice transactions pagination
    async resetNonInvoiceTransPagination() {
      this.nonInvoicePagination.current_page = 1;
    },

    // Reload non invoice transactions after search
    async nonInvoiceTransReload() {
      this.nonInvoiceQuery = "";
      await this.searchNonInvoiceTransactions();
    },

    // display modal
    previewModal(image) {
      this.imagePath = image;
      if (this.showModal) {
        return (this.showModal = false);
      }
      return (this.showModal = true);
    },

    // get ledger
    async getLedger() {
      this.ledgerLoading = true;
      this.activeTab = "ledger";
      const { data } = await axios.get(
        window.location.origin +
        "/api/client/" +
        this.$route.params.slug +
        "/ledger?startDate=" +
        this.ledgerDateRange.startDate +
        "&endDate=" +
        this.ledgerDateRange.endDate
      );
      let totalBalance = 0;
      // this.ledgerItems.sort((a, b) => new Date(b.date) - new Date(a.date));
      this.ledgerItems = data.items.map((transaction) => {

        totalBalance =
          transaction.debit == 0
            ? totalBalance + transaction.credit
            : totalBalance - transaction.debit; // Debit subtracts, Credit adds
        return { ...transaction, balance: totalBalance };
      });

      // this.ledgerItems.sort((a, b) => new Date(b.date) - new Date(a.date));
      this.ledgerItems.reverse();

      this.ledgerTotalDiscount = data.totalDiscount;
      this.ledgerTotalDebit = data.totalDebit;
      this.ledgerTotalCredit = data.totalCredit;
      this.finalBalance = data.finalBalance;
      this.ledgerLoading = false;
    },

    // non invoice transactions pagination
    // async ledgerPaginate(page) {
    //   this.pagination.current_page = page;
    //   this.query === "" ? this.getLedger() : "";
    // },

    // generate pdf
    async generatePDF() {
      // Get the HTML content to be converted
      this.headerShow = true;
      const element = document.getElementById("content-to-pdf");
      setTimeout(async () => {
        // Options for PDF generation
        const options = {
          margin: 5,
          filename: this.activeTab + ".pdf",
          image: { type: "jpeg", quality: 0.98 },
          html2canvas: { scale: 2 },
          jsPDF: { unit: "mm", format: "a4", orientation: "landscape" },
        };

        // Generate PDF from HTML content
        html2pdf().from(element).set(options).save();
        this.headerShow = false;
      }, 2000);
    },

    // print table
    async print() {
      this.headerShow = true;
      await this.$htmlToPaper(this.activeTab);
      setTimeout(async () => {
        this.headerShow = false;
      }, 2000);
    },

    // delete invoice data
    async deleteInvoiceData(slug) {
      Swal.fire({
        title: this.$t("common.delete_title"),
        text: this.$t("sales.invoices.index.delete_warning"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("common.delete_confirm_text"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/invoices/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("common.deleted"),
                  this.$t("common.delete_success"),
                  "success"
                );
                Fire.$emit("AfterDelete");
              } else {
                Swal.fire(
                  this.$t("common.failed"),
                  this.$t("sales.invoices.index.delete_failed"),
                  "warning"
                );
              }
            });
        }
      });
    },

    // delete invoice return data
    async deleteInvoiceReturnData(slug) {
      Swal.fire({
        title: this.$t("common.delete_title"),
        text: this.$t("sales.returns.index.delete_warning"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("common.delete_confirm_text"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/invoice-returns/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("common.deleted"),
                  this.$t("common.delete_success"),
                  "success"
                );
                Fire.$emit("AfterDelete");
              } else {
                Swal.fire(
                  this.$t("common.failed"),
                  this.$t("sales.returns.index.delete_failed"),
                  "warning"
                );
              }
            });
        }
      });
    },

    // delete invoice payment data
    async deletePaymentData(slug) {
      Swal.fire({
        title: this.$t("common.delete_title"),
        text: this.$t("payments.clients.invoice.index.delete_warning"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("common.delete_confirm_text"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/payments/invoice/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("common.deleted"),
                  this.$t("common.delete_success"),
                  "success"
                );
                Fire.$emit("AfterDelete");
              } else {
                Swal.fire(
                  this.$t("common.failed"),
                  this.$t("payments.clients.invoice.index.delete_failed"),
                  "warning"
                );
              }
            });
        }
      });
    },

    // delete non invoice payment data
    async deleteNonInvoicePayment(slug) {
      Swal.fire({
        title: this.$t("common.delete_title"),
        text: this.$t("payments.clients.non_invoice.index.delete_warning"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("common.delete_confirm_text"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/payments/non-invoice/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("common.deleted"),
                  this.$t("common.delete_success"),
                  "success"
                );
                Fire.$emit("AfterDelete");
              } else {
                Swal.fire(
                  this.$t("common.failed"),
                  this.$t("payments.clients.non_invoice.index.delete_failed"),
                  "warning"
                );
              }
            });
        }
      });
    },

    async savePayment() {

      if (this.form.note == '') {
        return toast.fire({ type: 'error', title: 'Please enter note' });
      }
      await this.form.post(window.location.origin + '/api/client/payment').then(() => {
        toast.fire({
          type: 'success',
          title: 'Payment Paid Successfully',
        });
        Fire.$emit('AfterAddPayment');
        this.showAddPaymentModal = false;
      }).catch((error) => {
        let message = error.response?.data?.message || this.$t('common.error_msg');
        toast.fire({ type: 'error', title: message });
      });
    },

    async savePaymentForClient() {
      if (this.advancePayForm.note == '') {
        return toast.fire({ type: 'error', title: 'Please enter note' });
      }
      await this.advancePayForm.post(window.location.origin + '/api/client/add-payment').then(() => {
        toast.fire({
          type: 'success',
          title: 'Payment Add Successfully',
        });
        Fire.$emit('AfterAddPayment');
        this.showPaymentModal = false;
      }).catch((error) => {
        let message = error.response?.data?.message || this.$t('common.error_msg');
        toast.fire({ type: 'error', title: message });
      });
    },

    async getAccounts() {
      this.form.bank_only = 1
      const { data } = await this.form.get(window.location.origin + "/api/all-accounts");
      this.accounts = data.data;

      // assign default account
      if (this.accounts && this.accounts.length > 0) {
        let defaultAccountSlug = this.appInfo.defaultAccountSlug;
        this.form.account = this.accounts.find(
          (account) => account.slug == defaultAccountSlug
        );
      }
    },
    openAddPaymentModal(netTotal, clientId, hotelId) {
      this.showAddPaymentModal = true;
      this.form.netTotal = Number(netTotal).toFixed(2);
      this.form.customerId = clientId;
      this.form.hotelId = hotelId;
    },
    openPaymentModal(clientId, hotelId) {
      this.showPaymentModal = true;
      this.advancePayForm.customerId = clientId;
      this.advancePayForm.hotelId = hotelId;
    },
  },
};
</script>

<style scoped>
tfoot {
  font-weight: 700;
}

td {
  text-align: right !important;
}

.nav-pills .nav-item {
  background: #ddd;
  margin: 2px;
  border-radius: 0.25rem;
}

.nav-link {
  padding: 0.5rem 0.5rem;
}

.model_img_box img {
    width: 125px;
    height: 100px;
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}

.model_img_box {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    /* justify-content: center; */
}

</style>
