<template>
    <table class="table">
        <thead>
            <tr>
                <th>{{ $t("common.s_no") }}</th>
                <th>{{ $t("common.name") }}</th>
                <th>{{ $t("common.system_name") }}</th>
                <th>{{ $t("common.group_type") }}</th>
                <th>{{ $t("sidebar.ledger_group") }}</th>
                <th>{{ $t("common.available_balance") }}</th>
                <th v-if="$can('ledger-account-edit') ||
                    $can('ledger-account-transaction') ||
                    $can('ledger-account-delete')
                    " class="text-right no-print">
                    {{ $t("common.action") }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-show="hotelCategoryItems.length" v-for="(data, i) in hotelCategoryItems" :key="i">
                <td>
                    <span>{{ ((page - 1) * 10) + i + 1 }}</span>
                </td>
                <td>

                    <router-link v-if="$can('client-view')" :to="{
                        name: 'accounts.show',
                        params: { slug: data?.get_accoutnbalance?.slug },
                        // params: { slug: data.slug },
                      }">
                        {{ data.ledger_name }}
                    </router-link>
                    <span v-else>{{ data.ledger_name }}</span>
                </td>
                <td>{{ data.system_name }}</td>
                <td>{{ data?.ledger?.name }}</td>
                <td>{{ data?.ledger_category?.name }}</td>
                <td class="text-right">{{ data?.get_accoutnbalance?.available_balance | forBalanceSheetCurrencyDecimalOnly }}</td>
                <td v-if="$can('ledger-account-edit') ||
                    $can('ledger-account-transaction') ||
                    $can('ledger-account-delete')
                    " class="text-right no-print">
                    <div class="btn-group">
                        <router-link
                                v-if="$can('account-view') || $can('ledger-account-transaction')"
                                v-tooltip="$t('cashbook.common.transactions')"
                                :to="{
                            name: 'accounts.show',
                            params: { slug: data?.get_accoutnbalance?.slug },
                          }"
                                class="btn btn-primary btn-sm"
                        >
                            <i class="fas fa-list-ol" />
                        </router-link>
                        <router-link v-if="!data.disabled && $can('ledger-account-edit')" v-tooltip="$t('common.edit')" :to="{
                            name: 'ledger-account.edit',
                            params: { slug: data.id },
                        }" class="btn btn-info btn-sm">
                            <i class="fas fa-edit" />
                        </router-link>
                        <a v-if="$can('ledger-account-delete') && (!data.disabled && data.editable)" v-tooltip="$t('common.delete')" href="#"
                            class="btn btn-danger btn-sm" @click="deleteData(data.id)">
                            <i class="fas fa-trash" />
                        </a>
                    </div>
                </td>
            </tr>
            <tr v-show="!loading && !hotelCategoryItems.length">
                <td colspan="12">
                    <EmptyTable />
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>

export default {
    props:{
        hotelCategoryItems:{
            type: Array,
            required: false
        },
        loading: {
            type: Boolean,
            required: false
        },
      page: {
          type: [String, Number],
        default: 1,
      }
    },
    methods: {
        async deleteData(slug) {
            Swal.fire({
                title: this.$t("common.delete_title"),
                text: this.$t("purchases.list.index.delete_warning"),
                type: "warning",
                showCancelButton: true,
                confirmButtonText: this.$t("common.delete_confirm_text"),
            }).then((result) => {
                // Send request to the server
                if (result.value) {
                    this.$store
                        .dispatch("operations/getDeleteData", {
                            path: "/api/account/ledger/delete/",
                            slug: slug,
                        })
                        .then((response) => {
                            if (response === true) {
                                Swal.fire(
                                    this.$t("common.deleted"),
                                    this.$t("common.delete_success"),
                                    "success"
                                );
                                this.$emit("refreshData")
                            } else {
                                Swal.fire(
                                    this.$t("common.failed"),
                                    this.$t(response.message),
                                    "warning"
                                );
                            }
                        });
                }
            });
        },
    },
}
</script>

<!-- <style>
td{
  text-align: right !important;
}
</style> -->