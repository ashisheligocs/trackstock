<template>
  <div class="p-0 position-relative mt-2 ledger-account">
    <div id="tabs" class="justify-content-end">
      <div class="w-100 pa-2 d-flex justify-content-between">
        
        
          <button class="btn btn-secondary mt-2 mb-2" @click="goBack"><i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}</button>
        

      </div>
      <div v-if="activetab" class="tabcontent">
        <div class="mt-3">
          <CreateSale v-if="activetab === 1" />
          <RestaurantOrders v-if="activetab === 2" />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import RestaurantOrders from "../../../../../Modules/Restaurant/Resources/js/pages/restaurant/order/index";
import CreateSale from './create-sale';

export default {
  components: {
    RestaurantOrders,
    CreateSale
  },
  created() {
    this.$route.params?.order && (this.activetab = 2);
  },
  data: () => ({
    activetab: 1,
  }),
  methods: {
    goBack() {
      this.$router.go(-1);
    }
  }
}
</script>
<style lang="scss" scoped>
.tabs a {
  float: left;
  cursor: pointer;
  padding: 10px 20px;
  transition: background-color 0.2s;
  border-radius: 4px;
  font-weight: bold;
  border: 1px solid transparent;
  color: #000000;
  margin-top: 10px;
  margin-right: 10px;
}



.ledger-account .tabs a {
  padding: 5px 25px;
}

.ledger-account .tabs {
  padding-bottom: 20px;
  float: left;

}

.ledger-account .tabs span {
  background: #9b9999;
  margin-left: 5px;
  font-size: 10px;
  padding: 2px 4px;
  border-radius: 2px;
}

.ledger-account div#tabs a.btn.btn-primary {
  height: fit-content;
}
</style>
