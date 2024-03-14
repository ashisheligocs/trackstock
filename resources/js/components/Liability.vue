<template>
    <table class="table table-bordered">
      <tbody>
        <tr class="thead-light">
          <th colspan="3">(B) Liability</th>
          </tr>
      </tbody>
      <tbody>
        <template v-for="(group, i) in liabilitydata">
          <tr class="tablerow-background" :key="i">
            
            <th colspan="2"><strong>{{ group[0].group_name }}</strong></th>
            <th class="text-right"><strong>{{ (group[0].total_amount) | forBalanceSheetCurrencyDecimalOnly }}</strong></th>
          </tr>
          <tr v-for="(gd, index) in group[0].items" :key="group[0].group_name + index">
            <td> </td>
            <td>{{ gd?.name }}</td>
            <td class="text-right">{{ (gd?.amount) | forBalanceSheetCurrencyDecimalOnly }}</td>
          </tr>
        </template>
      </tbody>
      <tbody>
      <tr class="thead-light">
        <th colspan="2">Total</th>
        <th class="text-right">{{ (total_liability) | forBalanceSheetCurrency}}</th>
      </tr>
    </tbody>
    </table>
</template>

<script>
export default {
  metaInfo() {
    return { title: this.$t("reports.balance_sheet.page_title") };
  },
  props:['liabilitydata','total_liability'],
};
</script>
<style scoped>
.tablerow-background {
  background-color: rgba(0, 0, 0, 0.05);
}
</style>