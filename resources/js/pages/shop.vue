<template>
  <div>
    <div class="container-fluid">
      <div class="row">
        <div>
          <button @click="goBack">Back</button>
        </div>


        <div class="col-md-12">
          <div class="my-4">
            <div class="">
              <div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'pos.create' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Shop Sales
                        </h3>
                      </div>
                    </div>
                  </router-link>
                </div>
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'reports.inventory' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Shop Stock
                        </h3>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
              <div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'shop_balance' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Shop Balance
                        </h3>
                      </div>
                    </div>
                  </router-link>
                </div>
                <div class="col-md-3 col-6 mb-4">
                  <router-link :to="{ name: 'shop_salesman' }" class="small-box-footer">
                    <div class="small-box mb-0">
                      <div class="inner p-4 text-center">
                        <h3>
                          Shop Salesman
                        </h3>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>


import axios from "axios";
import { mapGetters } from "vuex";


export default {
  middleware: "auth",
  metaInfo() {
    return { title: this.$t("dashboard.page_title") };
  },
  data: () => ({

  }),

  methods:{
    goBack() {
      this.$router.go(-1);
    }
  }
};
</script>
