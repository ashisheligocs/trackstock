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
                <table class="table facilitiy_table">
                    <thead>
                        <tr>
                            <th>{{ $t("common.s_no") }}</th>
                            <th>{{ $t("common.icons") }}</th>
                            <th>{{ $t("common.name") }}</th>
                            <th>{{ $t("common.description") }}</th>
                            <th>{{ $t("common.tax_rates") }}</th>
                            <th>{{ $t("common.price") }}</th>
                            <th class="text-right no-print">
                                {{ $t("common.action") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-show="facilityList.length" v-for="(data, i) in facilityList" :key="i">
                            <td style="width: 80px;">
                                <span>{{ i + 1 }}</span>
                            </td>
                            <td style="width: 220px;">  
                                <img height="20px;" width="20px;" :src="data.facility_img" alt="SVG Image"/>
                            </td>
                            <td style="width: 220px;">
                                <div class="d-flex align-items-center">
                                    {{ data?.room_facility_title }}
                                </div>
                            </td>
                            <td style="width: 300px;">
                                <div class="d-flex align-items-center">
                                    {{ data?.facility_description }}
                                </div>
                            </td>
                            <td style="width: 280px;">
                                <div class="d-flex align-items-center">
                                    <div v-if="isOpenModal && i == selectedIndex" class="form-group mb-0 w-100">
                                        <multiselect v-model="hotel_facility_id" :options="facilityItems" placeholder=""
                                            label="name" track-by="name" :multiple="true" :taggable="true"
                                            class="form-control" selectLabel="" deselectLabel="" :close-on-select="false">
                                        </multiselect>
                                        <!--                                        <multiselect v-model="hotel_facility_id" placeholder="" :options="facilityItems" :show-labels="false" :multiple="true"-->
                                        <!--                                                     :close-on-select="false" :clear-on-select="false" :preserve-search="true" class="form-control" tag-placeholder=""-->
                                        <!--                                                     label="name" track-by="name" :preselect-first="false"></multiselect>-->
                                    </div>
                                    <div v-else>
                                        <div v-for="(item, main_index) in data?.facility_rate" :key="main_index + '1'"
                                            class="d-flex flex-wrap">
                                            <div v-for="(items, index) in item.tax_rate" :key="index + '2'">
                                                {{ items?.tax_name?.name }}
                                                <span class="mx-1" v-if="item.tax_rate?.length != index + 1">,</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input v-if="isOpenModal && i == selectedIndex" type="number"
                                        v-model="facilityRatePrice">
                                    <span v-else>{{ data?.facility_rate[0]?.price }}</span>
                                </div>
                            </td>
                            <td style="width: 100px" class="text-right no-print">
                                <div v-if="!isOpenModal || i != selectedIndex" class="btn-group">
                                    <a v-if="$can('hotel-edit')" v-tooltip="$t('common.edit')" href="#"
                                        class="btn btn-info btn-sm"
                                        @click="roomFacilityPrice(data, i, data?.facility_rate[0]?.price)">
                                        <i class="fas fa-edit" />
                                    </a>
                                </div>
                                <div v-else>
                                    <button @click="saveFacilityPrice(data.id, facilityRatePrice)" class="btn btn-primary">
                                        <i class="fas fa-save" /> {{ $t('common.save') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-show="!loading && !facilityList.length">
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
        ...mapGetters('operations', ['facilityItems']),
    },
    data() {
        return {
            facilityList: [],
            facility_form: new Form({
                hotel_id: ''
            }),
            facility_rate_price: new Form({
                hotel_id: '',
                facility_id: '',
                price: '',
                taxRate: '',
            }),
            facilityRatePrice: '',
            isOpenModal: false,
            modalTitle: '',
            loading: true,
            selectedIndex: -1,
            hotel_facility_id: '',
        }
    },
    created() {
        this.getFacilityPrice()
    },
    methods: {
        currentTab() {
            this.$emit('currentTab')
        },
        currentTabPre() {
            this.$emit('currentTabPre')
        },

        async getFacilityPrice() {
            this.facility_form.hotel_id = this.$route.params.slug
            const { data } = await this.facility_form.post(
                window.location.origin +
                '/api/room/category/lsit/facility/price'
            )
            this.facilityList = data.data
            console.log(this.facilityList);
            this.loading = false
        },

        async roomFacilityPrice(data, i, price) {
            this.isOpenModal = true;
            this.selectedIndex = i;
            this.facilityRatePrice = price;
            data.facility_rate?.[0]?.tax_rate.map((item) => (item.name = item.tax_name.name, item.id = item.tax_name.id));
            this.hotel_facility_id = data.facility_rate?.[0]?.tax_rate;
        },

        async saveFacilityPrice(id, price) {
            this.facility_rate_price.facility_id = id
            this.facility_rate_price.hotel_id = this.$route.params.slug
            this.hotel_facility_array = []
            if (this.hotel_facility_id) {
                this.hotel_facility_id.forEach((el) => {
                    this.hotel_facility_array.push(el.id)
                });
                this.facility_rate_price.taxRate = this.hotel_facility_array.toString()
            }
            this.facility_rate_price.price = price

            await this.facility_rate_price
                .post(window.location.origin + '/api/room/category/add/facility')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('rooms.facility.edit.success_msg'),
                    })
                    this.isOpenModal = false
                    this.getFacilityPrice()
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

input {
    border-radius: 3px;
    border: 1px solid #9f9d9d;
    height: 30px;
}</style>
