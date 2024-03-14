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
                            {{ $t('ledger_account.create.page_title') }}
                        </h3>
                        <router-link :to="{ name: 'ledger-account' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form role="form" @submit.prevent="saveLedgerGroup" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="ledger_type">{{ $t('common.group_type') }}
                                        <span class="required">*</span></label>
                                    <multiselect v-model="coa_account_id" :options="groupOptions" :show-labels="false"
                                        tag-placeholder="Add this as new tag" placeholder="Search a Food category"
                                        class="form-control" label="name" track-by="name"
                                        :class="{ 'is-invalid': form.errors.has('ledger_type') }"></multiselect>
                                    <has-error :form="form" field="ledger_type" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="ledger_group">{{ $t('sidebar.ledger_group') }}</label>
                                    <multiselect v-model="ledger_group_id" :options="hotelCategoryItems"
                                        :show-labels="false" tag-placeholder="Add this as new tag"
                                        placeholder="Search a Food category" class="form-control" label="name"
                                        track-by="name"></multiselect>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="ledger_name">{{ $t('common.display_name') }}
                                        <span class="required">*</span></label>
                                    <input id="ledger_name" v-model="form.ledger_name" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('ledger_name') }" name="ledger_name"
                                        :placeholder="$t('common.display_name_placeholder')" />
                                    <has-error :form="form" field="ledger_name" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="system_name">{{ $t('common.system_name') }}
                                        <span class="required">*</span></label>
                                    <input id="system_name" v-model="form.system_name" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('system_name') }" name="system_name"
                                        :placeholder="$t('common.system_name_placeholder')" />
                                    <has-error :form="form" field="system_name" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="code">{{ $t('common.code') }}
                                        <span class="required">*</span></label>
                                    <input id="code" v-model="form.code" type="text"
                                        :class="{ 'is-invalid': form.errors.has('code') }" class="form-control" name="code"
                                        :placeholder="$t('common.code_placeholder')" />
                                    <has-error :form="form" field="code" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="is_bank">{{ $t('common.is_bank_account') }}</label>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                            <input id="yes_bank_account" v-model="form.is_bank" type="radio"
                                                class="form-control" value="1" name="is_bank"
                                                :placeholder="$t('common.name_placeholder')" />
                                            <label for="yes_bank_account" class="ml-2 mb-0">{{ $t('common.yes') }}</label>
                                        </div>

                                        <div class="d-flex align-items-center ml-4">
                                            <input id="no_bank_account" v-model="form.is_bank" type="radio"
                                                class="form-control" value="0" name="is_bank"
                                                :placeholder="$t('common.name_placeholder')" />
                                            <label for="no_bank_account" class="ml-2 mb-0">{{ $t('common.no') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="false" class="form-group col-md-12"
                                    style="border-top: 1px solid rgba(0, 0, 0, 0.125)">
                                    <h3 class="card-title form-group col-md-12">
                                        {{ 'Bank account details' }}
                                    </h3>

                                    <div class="mt-5">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="bankName">{{ $t('cashbook.common.bank_name') }}
                                                    <span class="required">*</span></label>
                                                <input id="bankName" v-model="form.bankName" type="text"
                                                    class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('bankName') }" name="bankName"
                                                    :placeholder="$t('cashbook.common.bank_name_placeholder')" />
                                                <has-error :form="form" field="bankName" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="branchName">{{ $t('cashbook.common.branch_name') }}
                                                </label>
                                                <input id="branchName" v-model="form.branchName" type="text"
                                                    class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('branchName') }"
                                                    name="branchName"
                                                    :placeholder="$t('cashbook.common.branch_name_placeholder')" />
                                                <has-error :form="form" field="branchName" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="accountNumber">{{ $t('cashbook.common.account_number') }}
                                                    <span class="required">*</span></label>
                                                <input id="accountNumber" v-model="form.accountNumber" type="text"
                                                    class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('accountNumber') }"
                                                    name="accountNumber" :placeholder="$t('cashbook.common.account_number_placeholder')
                                                        " />
                                                <has-error :form="form" field="accountNumber" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="date">{{ $t('common.date') }}</label>
                                                <input id="date" v-model="form.date" type="date" class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" />
                                                <has-error :form="form" field="date" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="status">{{ $t('common.status') }}</label>
                                                <select id="status" v-model="form.status" class="form-control"
                                                    :class="{ 'is-invalid': form.errors.has('status') }">
                                                    <option value="1">{{ $t('common.active') }}</option>
                                                    <option value="0">{{ $t('common.in_active') }}</option>
                                                </select>
                                                <has-error :form="form" field="status" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="note">{{ $t('common.note') }}</label>
                                            <textarea id="note" v-model="form.note" class="form-control"
                                                :class="{ 'is-invalid': form.errors.has('note') }"
                                                :placeholder="$t('common.note_placeholder')" />
                                            <has-error :form="form" field="note" />
                                        </div>
                                    </div>
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
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Multiselect from 'vue-multiselect'
import Form from 'vform'
import axios from "axios";
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('ledger_account.create.page_title') }
    },
    computed: {
        ...mapGetters('operations', ['items', "hotelItems", "hotelCategoryItems", "hotelItems", "selectedHotel"]),
    },
    components: {
        Multiselect
    },
    async created() {
        await this.getCoaListData()
        await this.getLedgerGroupData()
    },
    data: () => ({
        breadcrumbsCurrent: 'ledger_account.create.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'ledger_account.create.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'ledger_account.create.breadcrumbs_second',
                url: 'ledger-account',
            },
            {
                name: 'ledger_account.create.breadcrumbs_active',
                url: '',
            },
        ],
        form: new Form({
            ledger_type: '',
            ledger_group: '',
            ledger_name: '',
            system_name: '',
            code: '',
            is_bank: '0',
            show_in_day_book: '1',
            bankName: '',
            branchName: '',
            accountNumber: '',
            date: new Date().toISOString().slice(0, 10),
            note: '',
            status: 1,
            hotel_id: '',
        }),
        loading: true,
        coa_account_id: '',
        ledger_group_id: '',
        groupOptions: [],
    }),

    methods: {
        async getCoaListData() {
            let { data } = await axios.get(window.location.origin + '/api/account/coa')
            this.groupOptions = data.data;
        },

        async getLedgerGroupData() {
            await this.$store.dispatch('operations/getHotelCategoryData', {
                path: '/api/account/ledger/group?perPage=100'
            })
        },

        // save category
        async saveLedgerGroup() {
            this.form.ledger_type = this.coa_account_id.id
            this.form.ledger_group = this.ledger_group_id.id
            await this.form
                .post(window.location.origin + '/api/account/ledger/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('ledger_account.create.success_msg'),
                    })
                    this.$router.push({ name: 'ledger-account' })
                })
                .catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message });
                })
        },
    },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
:deep(.multiselect__tags) {
    min-height: 38px !important;
    border: none !important;
    padding: 4px 40px 0 4px !important;
}

:deep(.multiselect__placeholder) {
    margin-bottom: 4px !important;
    padding-top: 4px !important;
}

:deep(.multiselect__single) {
    margin-bottom: 0px !important;
    margin-top: 4px !important;
}

:deep(.multiselect) {
    width: auto;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
    min-height: 38px !important;
}</style>
