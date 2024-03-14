<template>
    <div class="card p-4">

        <div class="d-flex justify-content-between">
            <button @click="currentTabPre()" class="btn btn-secondary mr-3">
                <i class="fas fa-long-arrow-alt-left" /> {{ $t('common.back') }}
            </button>
            <button @click="currentTab()" class="btn btn-secondary">
                {{ $t('common.next') }} <i class="fas fa-long-arrow-alt-right mt-1" />
            </button>
        </div>

        <div class="card-body p-0 position-relative">
            <table-loading v-show="loading" />
            <div id="printMe" class="table-responsive table-custom mt-3">
                <table class="table border">
                    <thead>
                        <tr>
                            <th>{{ $t("common.s_no") }}</th>
                            <th>{{ $t("common.name") }}</th>
<!--                            <th>{{ $t("common.tax_rates") }}</th>-->
                            <th>{{ $t("meals.edit.price") }}</th>
                            <th class="text-right no-print">
                                {{ $t("common.action") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-show="mealsList.length" v-for="(data, i) in mealsList" :key="i">
                            <td style="width: 150px;">
                                <span>{{ i + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    {{ data.plan_name }}
                                </div>
                            </td>
                            <td style="width: 300px">
                                <div class="d-flex align-items-center">
                                    <input v-if="isOpenModal && i == selectedIndex" type="number" v-model="MealRatePrice">
                                    <span v-else>{{ data?.rate_hotel[0]?.price }}</span>
                                </div>
                            </td>

                            <td style="width: 150px" class="text-right no-print">
                                <div v-if="!isOpenModal || i != selectedIndex" class="btn-group">
                                    <a v-if="$can('hotel-edit')" v-tooltip="$t('common.edit')" href="#"
                                        class="btn btn-info btn-sm"
                                        @click="mealRatePrice(data, i, data?.rate_hotel[0]?.price)">
                                        <i class="fas fa-edit" />
                                    </a>
                                </div>
                                <div v-else>
                                    <button @click="saveMealRatePrice(data.id, MealRatePrice)" class="btn btn-primary">
                                        <i class="fas fa-save" /> {{ $t('common.save') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-show="!loading && !mealsList.length">
                            <td colspan="12">
                                <EmptyTable />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Form from 'vform'
import Multiselect from 'vue-multiselect'

export default {
    components: {
        Multiselect,
    },
    computed: {
        ...mapGetters('operations', ['items', 'facilityItems', 'hotelCategoryItems']),
    },
    data() {
        return {
            loading: true,
            mealsList: [],
            meal_form: new Form({
                hotel_id: '',
            }),
            meal_rate_price: new Form({
                hotel_id: '',
                meal_id: '',
                price: '',
                taxRate: '',
            }),
            isOpenModal: false,
            modalTitle: '',
            MealRatePrice: '',
            selectedIndex: -1,
            hotel_facility_id: '',
            hotel_facility_array: [],
        }
    },
    created() {
        this.getMealPlan()
        this.getFacilityItems()
    },
    methods: {
        currentTab() {
            this.$emit('currentTab')
        },
        currentTabPre() {
            this.$emit('currentTabPre')
        },
        async mealRatePrice(data, i, price) {
            this.selectedIndex = i;
            this.isOpenModal = true;
            this.MealRatePrice = price;
            data.rate_hotel?.[0]?.tax_rate.map((item) => (item.name = item.tax_name.name,item.id = item.tax_name.id));
            this.hotel_facility_id = data.rate_hotel?.[0]?.tax_rate;
        },

        async getMealPlan() {
            this.meal_form.hotel_id = this.$route.params.slug

            const { data } = await this.meal_form.post(
                window.location.origin +
                '/api/hotel/room/meal'
            )
            this.mealsList = data.data
            this.loading = false
        },

        async saveMealRatePrice(id, price) {
            this.meal_rate_price.meal_id = id;
            this.meal_rate_price.hotel_id = this.$route.params.slug;
            this.hotel_facility_array = [];
            // if (this.hotel_facility_id) {
            //     this.hotel_facility_id.forEach((el) => {
            //         this.hotel_facility_array.push(el.id)
            //     });
            //     this.meal_rate_price.taxRate = this.hotel_facility_array.toString();
            // }
            this.meal_rate_price.price = price;
            await this.meal_rate_price
                .post(window.location.origin + '/api/hotel/room/meal/add')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('meals.edit.success_msg'),
                    })
                    this.isOpenModal = false
                    this.getMealPlan()
                })
                .catch(() => {
                    toast.fire({ type: 'error', title: this.$t('common.error_msg') })
                })
        },

        async getFacilityItems() {
            await this.$store.dispatch('operations/getFacilityData', {
                path: '/api/all-vat-rates'
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
}
:deep(.multiselect__input, .multiselect__single){
    background-color: transparent !important;
}

input {
    border-radius: 3px;
    border: 1px solid #9f9d9d;
    height: 30px;
}</style>
