<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent"/>
        <!-- breadcrumbs end -->
        <div class="row no-print mb-2">
            <div class="w-100 text-right float-right">
                <div class="btn-group" v-if="allData">
                    <a @click="generatePDF()" href="#" class="btn btn-primary">
                        <i class="fas fa-download"></i> {{ $t("download") }}
                    </a>
                    <a @click="printWindow()" href="#" class="btn btn-secondary">
                        <i class="fas fa-print"></i> {{ $t("common.print") }}
                    </a>
                    <router-link
                            :to="{ name: 'pos.create', params: {order: 1} }"
                            class="btn btn-dark float-right"
                    >
                        <i class="fas fa-long-arrow-alt-left"/> {{ $t("common.back") }}
                    </router-link>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Main content -->
            <div class="invoice px-3 mb-3 w-100" id="content-to-pdf">
                <table-loading v-show="loading"/>
                <!-- info row -->

                <div class="invoice-info row">
                    <div class="w-30 col-6 d-flex" style="gap: 10px">
                        <img
                                v-if="appInfo"
                                :src="appInfo.blackLogo"
                                :alt="appInfo.companyName"
                                class="lg-logo"
                                style="width: 10rem"
                        />
                        <CompanyInfo :hotel="allData.hotel" :showImage="false"/>
                    </div>
                    <div class="col-2">

                    </div>
                    <div class="col-4 d-flex flex-column align-items-end justify-content-between">
                        <div>
                            <div v-if="orderClient">
                                <span v-if="orderClient.companyName"><strong>{{ $t('common.client_id') }}:</strong>
                                  {{ orderClient.clientID | withPrefix(clientPrefix) }}<br/></span>
                                <strong>{{ $t('common.client_name') }}:</strong>
                                {{ orderClient.name }}<br/>
<!--                                <span v-if="orderClient.companyName"><strong>{{ $t('common.company_name') }}:</strong>-->
<!--                                  {{ orderClient.companyName }}<br/></span>-->
                                <span v-if="orderClient.email"><strong>{{ $t('common.email') }}:</strong>
                                  {{ orderClient.email }}<br/></span>
                                <span v-if="orderClient.phone"><strong>{{ $t('common.contact_number') }}:</strong>
                                  {{ orderClient.phone }}<br/></span>
                                <span v-if="orderClient.address"><strong>{{ $t('common.address') }}:</strong>
                                  {{ orderClient.address }}<br/></span>
                                <span v-if="orderClient.nationality"
                                ><strong>{{ $t("common.nationality") }}:</strong>
                                     {{ orderClient.nationality }}<br
                                    /></span>
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
                                    <th v-if="allData.orderId">
                                        Order Number
                                    </th>
                                    <th v-if="allData.date">
                                        Order Date
                                    </th>
                                    <th>{{ $t("common.status") }}</th>
                                    <th class="text-right">{{ $t("common.created_by") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td v-if="allData.orderId">
                                        {{ allData.orderId  }}
                                    </td>
                                    <td v-if="allData.date">
                                        {{ allData.date | moment("Do MMM, YYYY") }}
                                    </td>
                                    <td>
                                      <span style="float: left !important;" v-html="allData.order_status_name"></span>
                                    </td>
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
                        <strong class="mb-2 d-block"
                        >{{ $t("common.invoice_products") }}:</strong
                        >
                        <div class="table-responsive table-custom">
                            <!----For invoice products-->
                            <table class="table table-sm text-center" v-if="allData.items">
                                <thead>
                                <tr>
                                    <th>{{ $t("common.s_no") }}</th>
                                    <th class="text-left">Item Name</th>
                                    <th class="text-right">{{ $t("common.invoice_qty") }}</th>
                                    <th class="text-right">{{ $t("common.unit_price") }}</th>
                                    <th class="text-right">{{ $t("common.total") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="allData?.items?.items" v-for="(data, i) in allData?.items?.items" :key="i">
                                    <td>{{ ++i }}</td>
                                    <td class="text-left">{{ data.name }}</td>
                                    <td class="text-right">{{ data.quantity }}</td>
                                    <td class="text-right">{{ data.price | withCurrency }}</td>
                                    <td class="text-right">{{ data.total | withCurrency }}</td>
                                </tr>
                                <tr>
                                    <td>{{ allData?.items?.items?.length + 1 }}</td>
                                    <td class="text-left">Addon items</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{ allData?.items?.addon | withCurrency}}</td>
                                </tr>
                                <tr>
                                    <td
                                            colspan="4"
                                            class="text-right"
                                    >
                                        <strong>{{ $t("common.subtotal") }} </strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>
                                            {{ allData?.items?.subtotal | withCurrency }}
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
                        <div v-if="allData.transactionAmount">
                            <strong class="mb-2 d-block">{{ $t("common.payment_history") }}:</strong>
                            <div class="table-responsive table-custom">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>{{ $t("common.s_no") }}</th>
                                        <th>{{ $t("common.payment_date") }}</th>
                                        <th class="text-right">{{ $t("common.paid_amount") }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <span v-if="allData.transactionAmount">{{ allData.date | moment("Do MMM, YYYY")}}</span>
                                        </td>
                                        <td class="text-right">{{ allData.transactionAmount | twoPoints }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="2">
                                            <strong>{{ $t("common.total_paid") }}</strong>
                                        </td>
                                        <td colspan="5" class="text-right">
                                            <strong>{{ allData.transactionAmount | withCurrency }}</strong>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else-if="this.allData?.bookingId && invoiceDueAmount <= 1">
                            <strong class="mb-2 d-block">{{ $t("common.payment_history") }}:</strong>
                            <div class="table-responsive table-custom">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>{{ $t("common.s_no") }}</th>
                                        <th>{{ $t("common.payment_date") }}</th>
                                        <th class="text-right">{{ $t("common.paid_amount") }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <span v-if="allData.checkOutDate">{{ allData.checkOutDate | moment("Do MMM, YYYY")}}</span>
                                        </td>
                                        <td class="text-right">{{ (allData.items?.total) | twoPoints }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="2">
                                            <strong>{{ $t("common.total_paid") }}</strong>
                                        </td>
                                        <td colspan="5" class="text-right">
                                            <strong>{{ (allData.items?.total) | withCurrency }}</strong>
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
                                    <td>{{ allData?.items?.subtotal | withCurrency}}</td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ $t("common.discount") }} :
                                    </th>
                                    <td>
                                        <span class="minus-sign">-</span>
                                        {{ (allData.items?.discount) | withCurrency }}
                                    </td>
                                </tr>
                                <template>
                                    <tr>
                                        <th>
                                            {{ $t("common.tax") }}
                                            <span v-if="allData.items?.tax"
                                            ></span>:
                                        </th>
                                        <td>
                                            <span class="plus-sign">+</span>
                                            {{ allData.items?.tax | withCurrency }}
                                        </td>
                                    </tr>
                                </template>
                                <tr class="bg-indigo-light">
                                    <th>{{ $t("common.total") }}:</th>
                                    <td>
                                        <span class="equal-sign">=</span>
                                        {{
                                        (allData.items?.total)
                                        | withCurrency
                                        }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ $t("common.total_paid") }}:</th>
                                    <td>
                                        <span class="minus-sign">-</span>
                                        {{ invoiceTransactionAmount  | withCurrency }}
                                    </td>
                                </tr>
                                <tr class="bg-red-light">
                                    <th>{{ $t("common.due") }}:</th>
                                    <td>{{ invoiceDueAmount | withCurrency }}</td>
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
    </div>
</template>

<script>
    import Form from "vform";
    import axios from "axios";
    import {mapGetters} from "vuex";
    import html2pdf from "html2pdf.js";
    import _ from 'lodash';

    export default {
        middleware: ["auth"],
        metaInfo() {
            return { title: 'Orders' };
        },
        data: () => ({
            allData: "",
            breadcrumbsCurrent: "restaurant.show.breadcrumbs_current",
            breadcrumbs: [
                {
                    name: "restaurant.show.breadcrumbs_first",
                    url: "home",
                },
                {
                    name: "restaurant.show.breadcrumbs_second",
                    url: "invoices.index",
                },
                {
                    name: "Restaurant",
                    url: "",
                },
            ],
            invoiceProducts: [],
            productPrefix: "",
            clientPrefix: "",
            invoicePrefix: "",
            loading: false,
            form: new Form({

            }),
            bookingProducts: null,
            hotelDetails:{},
        }),
        // Map Getters
        computed: {
            ...mapGetters("operations", ["appInfo"]),
            orderClient() {
                return this.allData?.customer || this.allData?.roomCustomer;
            },
            invoiceDueAmount() {
                if (this.allData?.bookingId) {
                   return this.allData?.bookingPaid ? 0 : parseFloat(this.allData.items?.total);
                } else {
                    return parseFloat(this.allData.items?.total || 0) - parseFloat(this.allData.transactionAmount || 0)
                }
            },
            invoiceTransactionAmount() {
                if (this.allData?.bookingId) {
                    return this.allData?.bookingPaid ? parseFloat(this.allData.items?.total || 0) : 0;
                } else {
                    return this.allData.transactionAmount
                }
            }
        },
        created() {
            this.getInvoice();
            this.clientPrefix = this.appInfo.clientPrefix;
        },
        methods: {
            // get the invoice
            async getInvoice() {
                this.loading = true;
                const { data } = await axios.get(
                    window.location.origin + "/api/food/order/view/" + this.$route.params.slug
                );
                this.allData = data.data;
                this.loading = false;
            },
            // download pdf
            generatePDF(slug) {
                // Get the HTML content to be converted
                const element = document.getElementById("content-to-pdf");
                // Options for PDF generation
                const options = {
                    margin: 5,
                    filename: "order-" + this.$route.params.slug + ".pdf",
                    image: { type: "jpeg", quality: 0.98 },
                    pagebreak: { mode: "avoid-all", before: "#page-break" },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: "mm", format: "a4", orientation: "landscape" },
                };

                // Generate PDF from HTML content
                html2pdf().from(element).set(options).save();
            },
            // print
            printWindow() {
                window.print();
            },
        },
    };
</script>

<style scoped>
    .table th, .table td {
        padding: 0.50rem;
    }

    .break-all{
        word-break: break-all;
    }
</style>
