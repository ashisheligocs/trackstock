<template>
  <table class="table table-bordered">
    <tbody>
      <tr class="thead-light">
        <th colspan="2">(A) Revenue</th>
        <th class="text-right">{{ (total_revenue) | forBalanceSheetCurrency}}</th>
      </tr>
    </tbody>
    <tbody>
      <template v-for="(group, i) in revenuedata">
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
  </table>
</template>

<script>
export default {
  metaInfo() {
    return { title: this.$t("reports.profit_loss.page_title") };
  },
  props:['revenuedata','total_revenue'],
};
</script>
<style scoped>
.tablerow-background {
  background-color: rgba(0, 0, 0, 0.05);
}
</style>