<template>
    <div class="mb-50">
        <!-- Breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- Breadcrumbs End -->


        <div class="col-lg-12">
            <div class="card">
                <!-- form start -->
                <form role="form" @submit.prevent="getReportData" @keydown="form.onKeydown($event)">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="Hotel">Select Hotel</label>
                                <select id="month" v-model="form.month" class="form-control">
                                    <option value="All">All</option>
                                    <option value="Hotel One">Hotel One</option>
                                    <option value="Hotel Two">Hotel Two</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="Hotel">Select Date</label>
                                    <input type="date" class="form-control">

                                </div>


                            </div>
                            <!-- <div class="form-group col-md-6">
                                <label for="year">{{ $t("common.year") }}</label>
                                <select id="year" v-model="form.year" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('year') }">
                                    <option v-for="year in years" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                                <has-error :form="form" field="year" />
                            </div> -->
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="invoice">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ledgers</th>
                        <th class="text-right">Opening Balance</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Closing Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="pl-4">
                            Cash Book
                        </td>
                        <td class="text-right">0</td>
                        <td class="text-right">1,000</td>
                        <td class="text-right">0</td>
                        <td class="text-right">
                            1000
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-4">
                            Office Supplies
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-4">
                            Stationery
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-4">
                            Electricity
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-4">
                            Water
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-4">
                            Gas
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-4">
                            Internet and Phone
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-4">
                            Property Rent
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>

                    <tr>
                        <td class="pl-4">
                            Health Department Permit
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>

                    <tr>
                        <td class="pl-4">
                            Routine Maintenance
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>

                    <tr>
                        <td class="pl-4">
                            Equipment Maintenance
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>

                    <tr>
                        <td class="pl-4">
                            Advertising and Promotion
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>

                    <tr>
                        <td class="pl-4">
                            Website and Online Presence
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>

                    <tr>
                        <td class="pl-4">
                            Professional Fees (Consultants, Legal, Accounting)
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>

                    <tr>
                        <td class="pl-4">
                            Office Expense
                        </td>
                        <td class="text-right">11000</td>
                        <td class="text-right">5000</td>
                        <td class="text-right">3000</td>
                        <td class="text-right">
                            13000
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td colspan="6">
                            <div class="text-center py-8"><img src="/../../images/empty.png" alt="result-not-found"
                                    class="w-64 m-auto">
                                <p class="font-bold text-lg text-gray-600 dark:text-gray-200">
                                    Sorry 😔 No data found.
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script>

import Form from "vform";
export default {
    metaInfo() {
        return { title: this.$t("ledger_account.index.page_title") };
    },
    data: () => ({
        breadcrumbsCurrent: "common.daybook",
        breadcrumbs: [
            {
                name: "booking.index.breadcrumbs_first",
                url: "home",
            },
            {
                name: "Daybook.index.breadcrumbs_second",
                url: "",
            },
        ],
        form: {
            month: 'All',
        }
    }),
};
</script>