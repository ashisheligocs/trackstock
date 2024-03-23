<template>
  <div>
    <div>
      
      <button class="btn btn-secondary mt-2 mb-2" @click="goback"><i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}</button>
    </div>
    <div class="card-body position-relative">
      <!-- <table-loading v-show="loading" /> -->
      <div class="table-responsive table-custom mt-3" id="printMe">
        <table class="table">
          <thead>
            <tr>
              <th>{{ $t("common.s_no") }}</th>
              <th>{{ $t("common.name") }}</th>
              <th>{{ $t("common.contact_number") }}</th>
              <th class="text-right no-print">
                Action
              </th>
            </tr>
          </thead>
          <tbody>
            
            <tr v-show="salePerson.length" v-for="(data, i) in salePerson" :key="i">
              <td>
                <span>{{ i + 1 }}</span>
              </td>
              <td>
                {{ data.name }}
              </td>
              <td>{{ data.mobile_number }}</td>
              <td>
                <div class="btn-group">
                  <button @click="changeShop(data.id)" class="btn btn-secondary mt-1 mb-1">Change Shop</button>
                </div>
              </td>
            </tr>
           
          </tbody>
        </table>
      </div>
    </div>
   
    <VModal v-if="changeShopModal" v-model="changeShopModal" @close="changeShopModal = false">
<div style="min-height: 400px;">
            <h3 slot="title" class="text-center">Change Shop</h3>
            <div class="mt-1">    
                <div class="form-group">
                    <label for="note">Change Shop</label>
                    <v-select
                        v-mode="form.change_shop"
                        label="shop_name"
                        name="shop"
                        :options="shopTransfer"
                        placeholder="Select Shop"
                    />
                </div>
            </div>
            <div slot="modal-footer">
                <button @click="updateShop()" class="btn btn-primary">Change Shop</button>
            </div>
 </div>
        </VModal>
  </div>
</template>

<script>

import Form from "vform";
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  middleware: "auth",
  metaInfo() {
    return { title: this.$t("dashboard.page_title") };
  },
  computed: {
    ...mapGetters("operations", ["selectedHotel", "appInfo","hotelItems"]),

    shopTransfer(){
        return this.hotelItems.filter(shop => shop.id != this.selectedHotel);      
    }
  },
  created(){
    this.getShopDataList();
    this.getSalesPerson();
  },
  data: () => ({
    salePerson : [],
    changeShopModal:false,
    form: new Form ({
      change_shop: '',
      user_id:"",
    })
  }),
  methods:{
    goback() {
      this.$router.go(-1);
    },
    getShopDataList(){
      this.$store.dispatch('operations/getHotelData', {
        path: '/api/shop',
      });
    },
    async getSalesPerson(){
      const { data } = await axios.get(
        window.location.origin + "/api/shop-sales-man/" + this.selectedHotel
      );

      this.salePerson = data.data;
    },
    changeShop(id){
      this.changeShopModal = true;
      this.form.user_id = id;
    },
    async updateShop(){
      if(this.form.change_shop)
      await this.form
        .post(window.location.origin + "/api/shop-sales-man-transfer")
        .then((response) => {
          console.log(response);
          this.changeShopModal = false;
          toast.fire({ type: "success", title: 'Sales man transfer successfully to another shop' })
        })
        .catch(() => {
          toast.fire({ type: "error", title: 'Something Went Wrong' });
        });
      // 
    }
  },
  watch:{
    selectedHotel(){
      this.getSalesPerson();
    }
  }
};
</script>
