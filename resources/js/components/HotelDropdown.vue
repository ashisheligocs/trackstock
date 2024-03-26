<template>
  <li v-if="shops && shops.length > 1" class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button"
      :data-toggle="shops && shops.length === 1 ? '' : 'dropdown'" aria-haspopup="true" aria-expanded="false">
      {{ selectedHotel && selectedHotel === 'all' ? 'All' : (selectedHotel && Object.keys(selectedHotel).length > 0 ?
    selectedHotel.shop_name : $t('common.select_hotel')) }}
    </a>
    <div class="dropdown-menu dropdown-menu-sm">
      <a
                    class="dropdown-item"
                    href="#"
                    @click.prevent="setHotel('all')"
            >
                All
            </a>

      <a v-for="(value, key) in shops" :key="key" class="dropdown-item" :title="value[1]" href="#" 
        @click.prevent="setHotel(value)">
        {{ value.shop_name }}
      </a>
    </div>
  </li>
</template>

<script>
import axios from 'axios';
import { mapGetters } from "vuex";
import Cookies from "js-cookie";

export default {
  data() {
    return {
      shops: null,
    }
  },
  computed: {
    ...mapGetters({ shop: "operations/selectedHotel" }),

    selectedHotel() {
      let shop = this.shop;
      if (!shop) shop = Cookies.get('selectedHotel');
      if (shop && shop === 'all') return 'all';
      else if (shop) {
        return this.shops.filter(v => v.id == shop)[0];
      } else return null;
    },
  },
  async created() {
    await this.getShops();
    await this.setDefaultShop();
  },

  methods: {
    async getShops() {
      await axios.get('/api/shop/list-all').then((response) => {
        this.shops = response?.data?.data;
      });
    },
    setHotel(hotel) {
      this.$store.dispatch("operations/setHotel", { hotel });
    },
    async setDefaultShop() {
      if (!Cookies.get('selectedHotel')) {
        this.setHotel('1');
      } else {
        await axios.get('/api/get-shop').then((response) => {
          this.setHotel(response.data)
        });
      }

      if (this.shops && this.shops.length === 1) {
        Cookies.remove('selectedHotel')
        this.setHotel(this.shops[0])
      }
    },
  },
};
</script>
