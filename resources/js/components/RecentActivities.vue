<template>
    <div v-if="(invoices && invoices.length) ||
        (purchases && purchases.length) ||
        (expenses && expenses.length) ||
        (transactions && transactions.length)
        " class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $t("dashboard.recent_activities") }}</h3>
        </div>
        <div class="card-body mb-3">
            <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="invoices-tab" data-toggle="pill" href="#invoices" role="tab"
                        aria-controls="invoices-tab" aria-selected="true">{{ $t("common.invoices") }}</a>
                </li>
                <li class="nav-item">
                    <a @click="getPurchases()" class="nav-link" id="purchases-tab" data-toggle="pill" href="#purchases"
                        role="tab" aria-controls="purchases-tab" aria-selected="false">{{ $t("sidebar.purchases") }}</a>
                </li>
                <li class="nav-item">
                    <a @click="getExpenses()" class="nav-link" id="expenses-tab" data-toggle="pill" href="#expenses"
                        role="tab" aria-controls="expenses-tab" aria-selected="false">{{ $t("sidebar.expenses") }}</a>
                </li>
                <li class="nav-item">
                    <a @click="getTransactions()" class="nav-link" id="transactions-tab" data-toggle="pill"
                        href="#transactions" role="tab" aria-controls="transactions-tab" aria-selected="false">{{
                            $t("common.transactions") }}</a>
                </li>
            </ul>
            <div class="tab-content" id="custom-content-below-tabContent">
                <div class="tab-pane fade active show" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                    <table-loading v-show="loading" />
                    <div class="table-responsive table-custom">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ $t("common.s_no") }}</th>
                                    <th>{{ $t("common.invoice_date") }}</th>
                                    <th>{{ $t("sidebar.hotel") }}</th>
                                    <th>{{ $t("common.invoice_no") }}</th>
                                    <th>{{ $t("common.client") }}</th>
                                    <th class="text-right">{{ $t("common.subtotal") }}</th>
                                    <th class="text-right">{{ $t("common.net_total") }}</th>
                                    <th class="text-right">{{ $t("common.total_due") }}</th>
                                    <th>{{ $t("common.status") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, i) in invoices" :key="i">
                                    <td>{{ ++i }}</td>
                                    <td>
                                        <span v-if="data.invoiceDate">{{
                                            data.invoiceDate | moment("Do MMM, YYYY")
                                        }}</span>
                                    </td>
                                    <td>
                                        <span v-if="data?.hotel?.hotel_name">{{
                                            data?.hotel?.hotel_name
                                        }}</span>
                                    </td>
                                    <td>
                                        <router-link :to="{
                                                    name: 'invoices.show',
                                                    params: { slug: data.slug },
                                                }">
                                            {{ data.invoiceNo }}
                                        </router-link>
                                    </td>
                                    <td>{{ data.client }}</td>
                                    <td class="text-right">{{ data.subTotal | twoPoints }}</td>
                                    <td class="text-right">{{ data.invoiceTotal | twoPoints }}</td>
                                    <td class="text-right">{{ data.due | twoPoints }}</td>
                                    <td>
                                        <span v-if="data.status === 1" class="badge badge-success">{{
                                            $t("common.active")
                                        }}</span>
                                        <span v-else class="badge bg-danger">{{
                                            $t("common.in_active")
                                        }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="tab-pane fade" id="purchases" role="tabpanel" aria-labelledby="purchases-tab">
                    <table-loading v-show="loading" />
                    <div class="table-responsive table-custom">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ $t("common.s_no") }}</th>
                                    <th>{{ $t("common.date") }}</th>
                                    <th>{{ $t("purchases.list.common.purchase_no") }}</th>
                                    <th>{{ $t("common.supplier") }}</th>
                                    <th>{{ $t("common.subtotal") }}</th>
                                    <th class="text-right">{{ $t("common.net_total") }}</th>
                                    <th class="text-right">{{ $t("common.total_due") }}</th>
                                    <th class="text-right">{{ $t("common.status") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, i) in purchases" :key="i">
                                    <td>{{ ++i }}</td>
                                    <td>
                                        <span v-if="data.purchaseDate">{{
                                            data.purchaseDate | moment("Do MMM, YYYY")
                                        }}</span>
                                    </td>
                                    <td>
                                        <span v-if="data?.hotel?.hotel_name">{{
                                            data?.hotel?.hotel_name
                                        }}</span>
                                    </td>
                                    <td>
                                        <router-link :to="{
                                                    name: 'purchases.show',
                                                    params: { slug: data.slug },
                                                }">
                                            {{ data.code }}
                                        </router-link>
                                        <br />
                                    </td>
                                    <td>{{ data.supplierName }}</td>
                                    <td class="text-right">{{ data.subTotal | twoPoints }}</td>
                                    <td class="text-right">{{ data.purchaseTotal | twoPoints }}</td>
                                    <td class="text-right">{{ data.due | twoPoints }}</td>
                                    <td>
                                        <span v-if="data.status === 1" class="badge badge-success">{{
                                            $t("common.active")
                                        }}</span>
                                        <span v-else class="badge bg-danger">{{
                                            $t("common.in_active")
                                        }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">
                    <table-loading v-show="loading" />
                    <div class="table-responsive table-custom">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ $t("common.s_no") }}</th>
                                    <th>{{ $t("common.sub_category") }}</th>
                                    <th>{{ $t("expenses.list.common.expense_reason") }}</th>
                                    <th class="text-right">{{ $t("common.amount") }}</th>
                                    <th>{{ $t("common.account") }}</th>
                                    <th>{{ $t("common.date") }}</th>
                                    <th>{{ $t("common.status") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, i) in expenses" :key="i">
                                    <td>{{ ++i }}</td>
                                    <td>
                                        <span v-if="data.subCategory">
                                            {{ data.subCategory.name }} [{{
                                                data.subCategory.code
                                            }}]
                                        </span>
                                    </td>
                                    <td>
                                        <router-link :to="{
                                                    name: 'expenses.show',
                                                    params: { slug: data.slug },
                                                }">
                                            {{ data.reason }}
                                        </router-link>
                                    </td>
                                    <td class="text-right">
                                        <span v-if="data.transaction">{{
                                            data.transaction.amount
                                        }}</span>
                                    </td>
                                    <td>
                                        <span v-if="data.account">{{ data.account.label }} </span>
                                    </td>
                                    <td>
                                        <span v-if="data.date">{{
                                            data.date | moment("Do MMM, YYYY")
                                        }}</span>
                                    </td>
                                    <td>
                                        <span v-if="data.status === 1" class="badge badge-success">{{
                                            $t("common.active")
                                        }}</span>
                                        <span v-else class="badge bg-danger">{{
                                            $t("common.in_active")
                                        }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                    <table-loading v-show="loading" />
                    <div class="table-responsive table-custom">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ $t("common.s_no") }}</th>
                                    <th>{{ $t("common.date") }}</th>
                                    <th>{{ $t("sidebar.hotel") }}</th>
                                    <th>{{ $t("common.reason") }}</th>
                                    <th>Account</th>
                                    <th>{{ $t("common.type") }}</th>
                                    <th class="text-right">{{ $t("common.debit") }}</th>
                                    <th class="text-right">{{ $t("common.credit") }}</th>
                                    <!--                                <th>{{ $t("common.amount") }}</th>-->
                                    <!--                                <th class="text-right">{{ $t("common.status") }}</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, i) in transactions" :key="i">
                                    <td>{{ ++i }}</td>
                                    <td>
                                        <span v-if="data.transactionDate">{{
                                            data.transactionDate | moment("Do MMM, YYYY")
                                        }}</span>
                                    </td>
                                    <td>
                                        <span v-if="data?.hotel?.hotel_name">{{
                                            data?.hotel?.hotel_name
                                        }}</span>
                                    </td>
                                    <td>{{ data.reason }}</td>
                                    <td v-if="data.account">{{ data.account.label }}</td>
                                    <td>
                                        <span v-if="data.type === 1" class="badge "
                                            :class="data.isAsset ? 'bg-danger' : 'badge-success'">{{
                                                data.isAsset ? $t("common.debit") : $t("common.credit")
                                            }}</span>
                                        <span v-else class="badge badge-danger"
                                            :class="data.isAsset ? 'badge-success' : 'bg-danger'">{{
                                                data.isAsset ? $t("common.credit") : $t("common.debit")
                                            }}</span>
                                    </td>
                                    <td v-if="!data.isAsset" class="text-right">
                                        <span v-if="data.type === 1">{{ 0 }}</span>
                                        <span v-else>{{ data.amount | twoPoints }}</span>
                                    </td>
                                    <td v-else class="text-right">
                                        <span v-if="data.type === 1">{{
                                            data.amount | twoPoints
                                        }}</span>
                                        <span v-else>{{ 0 }}</span>
                                    </td>
                                    <td v-if="!data.isAsset" class="text-right">
                                        <span v-if="data.type === 1">{{
                                            data.amount | twoPoints
                                        }}</span>
                                        <span v-else>{{ 0 }}</span>
                                    </td>
                                    <td v-else class="text-right">
                                        <span v-if="data.type === 1">{{ 0 }}</span>
                                        <span v-else>{{ data.amount | twoPoints }}</span>
                                    </td>
                                    <!--                                <td class="text-right">-->
                                    <!--                    <span v-if="data.status === 1" class="badge bg-success">{{-->
                                    <!--                      $t("common.active")-->
                                    <!--                    }}</span>-->
                                    <!--                                    <span v-else class="badge bg-danger">{{-->
                                    <!--                      $t("common.in_active")-->
                                    <!--                    }}</span>-->
                                    <!--                                </td>-->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <small data-v-d7f4c2e6="" class="ml-2"><i data-v-d7f4c2e6="">
                        All prices are in (₹)
                    </i></small>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
    name: "RecentActivities",
    data: () => ({
        invoices: "",
        purchases: "",
        expenses: "",
        transactions: "",
        prefix: "",
        subCatPrefix: "",
        loading: false,
    }),
    // Map Getters
    computed: {
        ...mapGetters("operations", ["appInfo"]),
    },
    created() {
        this.getInvoices();
        this.prefix = this.appInfo.productPrefix;
        this.subCatPrefix = this.appInfo.expSubCatPrefix;
    },
    methods: {
        // get invoices
        async getInvoices() {
            this.loading = true;
            const { data } = await axios.get(
                window.location.origin + "/api/dashboard/recent-invoices"
            );
            this.invoices = data.data;
            this.loading = false;
        },
        // get purchases
        async getPurchases() {
            this.loading = true;
            const { data } = await axios.get(
                window.location.origin + "/api/dashboard/recent-purchases"
            );
            this.purchases = data.data;
            this.loading = false;
        },
        // get expenses
        async getExpenses() {
            this.loading = true;
            const { data } = await axios.get(
                window.location.origin + "/api/dashboard/recent-expenses"
            );
            this.expenses = data.data;
            this.loading = false;
        },
        // get transactions
        async getTransactions() {
            this.loading = true;
            const { data } = await axios.get(
                window.location.origin + "/api/dashboard/recent-transactions"
            );
            this.transactions = data.data;
            this.loading = false;
        },
    },
};
</script>
