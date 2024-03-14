<template>
    <li v-if="hotels && hotels.length >= 1" class="nav-item dropdown">
        <a
                class="nav-link dropdown-toggle"
                href="#"
                role="button"
                :data-toggle="hotels && hotels.length === 1 ? '' : 'dropdown'"
                aria-haspopup="true"
                aria-expanded="false"
        >
            {{ selectedHotel && selectedHotel === 'all' ? 'All' : (selectedHotel && Object.keys(selectedHotel).length > 0 ? selectedHotel.hotel_name : $t('common.select_hotel')) }}
        </a>
        <div class="dropdown-menu dropdown-menu-sm">
            <a
                    class="dropdown-item"
                    href="#"
                    @click.prevent="setHotel('all')"
            >
                All
            </a>
            <a
                    v-for="(value, key) in hotels"
                    :key="key"
                    class="dropdown-item"
                    :title="value[1]"
                    href="#"
                    @click.prevent="setHotel(value)"
            >
                {{value.hotel_name}}
            </a>
        </div>
    </li>
</template>

<script>
    import axios from 'axios';
    import {mapGetters} from "vuex";
    import Cookies from "js-cookie";

    export default {
        data() {
            return {
                hotels: null,
            }
        },
        computed: {
            ...mapGetters({ hotel: "operations/selectedHotel" }),

            selectedHotel() {
                let hotel = this.hotel;
                if (!hotel) hotel = Cookies.get('selectedHotel');
                if (hotel && hotel === 'all') return 'all';
                else if (hotel) {
                    return this.hotels.filter(v => v.id == hotel)[0];
                } else return null;
            },
        },
        async created() {
            await this.getHotels();
            await this.setDefaultHotel();
        },

        methods: {
            async getHotels() {
                await axios.get('/api/hotel/list-all').then((response) => {
                    this.hotels = response?.data?.data;
                });
            },
            setHotel(hotel) {
                this.$store.dispatch("operations/setHotel", { hotel });
            },
            async setDefaultHotel() {
                if (!Cookies.get('selectedHotel')) {
                    this.setHotel('all');
                } else {
                  await axios.get('/api/get-hotel').then((response) => {
                    this.setHotel(response.data)
                  });
                }
                if (this.hotels && this.hotels.length === 1) {
                  Cookies.remove('selectedHotel')
                  this.setHotel(this.hotels[0])
              }
            },
        },
    };
</script>
