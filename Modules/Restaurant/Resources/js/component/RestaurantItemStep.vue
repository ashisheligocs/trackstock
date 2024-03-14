<template>
    <div class="card p-4">

        <div class="d-flex justify-content-between">
            <button @click="$emit('currentTabPre')" class="btn btn-secondary mr-3">
                <i class="fas fa-long-arrow-alt-left"/> {{ $t('common.back') }}
            </button>
        </div>

        <div class="card-body p-0 position-relative">
            <table-loading v-show="loading"/>
            <div id="printMe" class="table-responsive table-custom mt-3" style="overflow: visible !important;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ $t('common.s_no') }}</th>
                        <th>{{ $t('common.name') }}</th>
                        <th>{{ $t('common.variant') }}</th>
                        <th>{{ $t('common.price') }}</th>
                        <th>{{ $t('common.status') }}</th>
                        <!--                            <th>{{ $t("common.tax_rates") }}</th>-->
                        <th class="text-right no-print">
                            {{ $t('common.action') }}
                        </th>
                    </tr>
                    </thead>
                    
                    <tbody>
                    <tr v-show="itemList.length" v-for="(data, i) in itemList" :key="i">
                        <td style="width: 150px;">
                            <span>{{ i + 1 }}</span>
                        </td>
                        <td style="width: 350px;">
                            <div class="d-flex align-items-center">
                                {{ data.name }}
                            </div>
                        </td>
                        <td style="width: 350px;" class="min-max">
                            <div v-if="isOpenModal && i == selectedIndex" class="form-group mb-0 w-100">
                                <multiselect v-model="selectedVariant" :options="variants" placeholder="Select variant" label="varient_name" track-by="id" :multiple="false" class="form-control min-w-12" selectLabel="" deselectLabel=""></multiselect>
                            </div>
                            <div v-else>
                                {{ data.multi_variant}}
                                <!-- {{variantValue(data)}} -->
                            </div>
                        </td>
                        
                        <td>
                            <div class="d-flex align-items-center">
                                <input v-if="isOpenModal && i == selectedIndex" type="number" v-model="selectedItemPrice">
                                <span v-else>{{ data.multi_price }}</span>
                            </div>
                        </td>
                        <td>
                        <div v-if="data.multi_price != 0">
                        <label class="switch">
                          <input type="checkbox" :checked="data.active=='1'" @change="toggleCheckbox($event,data.id)"/>
                          <div class="slider round"></div>
                        </label>
                        </div>
                        <div v-else>
                            -
                        </div>
                      </td>
                        <td style="width: 150px" class="text-right no-print">
                            <a class="btn btn-primary plus-icon" @click="handleEditClick(data)" data-toggle="modal" data-target="#configureItem">
                                <i class="fas fa-edit"/>
                            </a>
                        </td>
                    </tr>
                    <tr v-show="!loading && !itemList.length">
                        <td colspan="12">
                            <EmptyTable/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-top mt-4">
                <div class="mt-4 d-flex align-items-center justify-content-between w-full">
                    <label for="gst_tax">Above Price is Including GST Tax</label>
                    <multiselect id="gst_tax" v-model="selectedTax" :options="facilityItems" placeholder="" label="name" track-by="name" :multiple="true" :taggable="false" class="form-control w-50 ml-4" selectLabel="" deselectLabel=""></multiselect>
                    <button @click="saveTax" class="btn btn-primary">
                        <i class="fas fa-save"/> {{ $t('common.save') }}
                    </button>
                </div>
            </div>

            <!--modal-->
            <div class="modal fade" id="configureItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-width modal-lg modal-dialog-centered" role="document" style="max-width: 550px">
                    <div class="modal-content modal-lg" style="max-width: 550px">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">{{ currentItem?.name || 'Configure item' }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <!--                                <form role="form" @submit.prevent="">-->
                                <h6>Select variants</h6>
                                <div class="card-body">
                                    <div class="row" v-for="(data, index) in currentVariants" :key="index">
                                        <div class="form-group col-md-7">
                                            <multiselect id="variant" v-model="data.id" :options="variants || []" :multiple="false" :show-labels="false" tag-placeholder="Add this variant" placeholder="Select variant" class="form-control" label="varient_name" track-by="id"></multiselect>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <input id="price" v-model="data.price" type="text" class="form-control" style="padding: 1.1rem 0.5rem" name="price" placeholder="Enter price"/>
                                        </div>
                                        <div class="col-md-1">
                                            <button v-if="index === (currentVariants.length - 1)" @click.stop="addVariant" class="btn btn-primary model_add_btn">
                                                <i class="fas fa-plus-circle"/>
                                            </button>
                                            <button v-else @click.stop="removeVariant(index)" class="btn btn-secondary">
                                                <i class="fas fa-trash"/>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="border-top pt-2">Select optional items</h6>
                                <div class="card-body">
                                    <div class="row" v-for="(data, index) in currentOptionalItems" :key="index">
                                        <div class="form-group col-md-7">
                                            <multiselect id="optionalItem" v-model="data.id" :options="optionalItems || []" :multiple="false" taggable @tag="tagOptionalItem($event, index)" :show-labels="false" tag-placeholder="Add this item" placeholder="Select item" class="form-control" label="name" track-by="id"></multiselect>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <input id="itemPrice" v-model="data.price" type="text" class="form-control" style="padding: 1.1rem 0.5rem" name="item_price" placeholder="Enter price"/>
                                        </div>
                                        <div class="col-md-1">
                                            <button v-if="index === (currentOptionalItems.length - 1)" @click.stop="addOptionalItem" class="btn btn-primary model_add_btn">
                                                <i class="fas fa-plus-circle"/>
                                            </button>
                                            <button v-else @click.stop="removeOptionalItem(index)" class="btn btn-danger">
                                                <i class="fas fa-trash"/>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button @click="saveConfiguration" class="btn btn-primary float-right">
                                        <i class="fas fa-save"/> {{ $t('common.save') }}
                                    </button>
                                </div>
                                <!--                                </form>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import Form from 'vform';
    import Multiselect from 'vue-multiselect';
    import axios from 'axios';
    import _ from 'lodash';

    export default {
        components: {
            Multiselect,
        },
        computed: {
            ...mapGetters('operations', ['items', 'facilityItems']),
        },
        data () {
            return {
                loading: true,
                itemList: [],
                restId: '',
                form: {},
                isOpenModal: false,
                itemPriceForm: new Form({
                    restaurant_id: '',
                    item_id: '',
                    varient_id: '',
                    price: '',
                }),
                itemConfigForm: new Form({}),
                currentItem: null,
                taxForm: new Form({
                    taxes: [],
                }),
                selectedItemPrice: '',
                selectedVariant: null,
                selectedTax: [],
                modalTitle: '',
                selectedIndex: -1,
                restaurant_facility_id: '',
                hotel_facility_array: [],
                variants: [],
                optionalItems: [],
                update_status: new Form({
                    active : '',
                    item_id: '',
                }),
                currentVariants: [
                    {
                        id: null,
                        price: '',
                    }],
                currentOptionalItems: [
                    {
                        id: null,
                        name: '',
                        price: '',
                    }],
            };
        },
        async created () {
            this.loading = true;
            await this.getItems();
            await this.getFacilityItems();
            await this.getVariants();
            await this.getTaxes();
            await this.getOptionalItems();
            this.loading = false;
        },
        methods: {

        async toggleCheckbox(event,id){
            
          this.update_status.active = (event.target.checked) ? '1' : '0',
          this.update_status.item_id = id;
          
          console.log(this.update_status);

          await this.update_status
                .post(window.location.origin + `/api/restaurant/${this.$route.params.slug}/update-item-status`)
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Coupon Status Update Successfully'),
                    })
                })
                .catch((error) => {
                    let message = error.response?.data?.message || this.$t('common.error_msg');
                    toast.fire({ type: 'error', title: message })
                })
        },

            async prepareItemRate (data, i) {
                this.selectedIndex = i;
                this.isOpenModal = true;
                this.selectedVariant = null;
                this.variants.forEach((v) => {
                    if (v.id == data?.restaurant_item_price[0]?.varient_id) {
                        this.selectedVariant = v;
                    }
                });
                this.selectedItemPrice = data?.restaurant_item_price[0]?.price || 0;
            },
            addVariant () {
                this.currentVariants.push({
                    id: null,
                    price: '',
                });
            },
            removeVariant (i) {
                this.currentVariants.splice(i, 1);
            },
            addOptionalItem () {
                this.currentOptionalItems.push({
                    id: null,
                    price: '',
                });
            },
            removeOptionalItem (i) {
                this.currentOptionalItems.splice(i, 1);
            },
            tagOptionalItem (value, index) {
                this.currentOptionalItems[index].id = { name: value, id: null };
            },

            async getItems () {
                this.restId = this.$route?.params?.slug;
                try {
                    const { data } = await axios.get(window.location.origin + '/api/food/item/list?restaurant='+this.restId);
                    this.itemList = data.data;
                } catch (e) {
                    console.log(e);
                }
            },

            async getTaxes () {
                try {
                    const { data } = await axios.get(
                        window.location.origin + `/api/restaurant/${this.$route.params.slug}/taxes`);
                    this.selectedTax = data.data;
                } catch (e) {
                    console.log(e);
                }
            },

            async getFacilityItems () {
                await this.$store.dispatch('operations/getFacilityData', {
                    path: '/api/all-vat-rates',
                });
            },

            async getVariants () {
                try {
                    const { data } = await axios.get(window.location.origin + '/api/food/varient/list');
                    this.variants = data.data;
                } catch (e) {
                    console.log(e);
                    toast.fire({ type: 'error', title: 'Something went wrong. Please try again.' });
                }
            },

            async getOptionalItems () {
                try {
                    const { data } = await axios.get(window.location.origin + '/api/food/optional-items');
                    this.optionalItems = data.data;
                } catch (e) {
                    console.log(e);
                    toast.fire({ type: 'error', title: 'Something went wrong. Please try again.' });
                }
            },

            handleEditClick (data) {
                this.currentVariants =  [{ id: null, price: ''}];
                if (data.restaurant_item_price && data.restaurant_item_price.length > 0) {
                    this.currentVariants = [];
                    let variantIds = _.map(data.restaurant_item_price, 'varient_id') || [];
                    this.variants?.forEach((item) => {
                        if (variantIds.includes(item.id)) {
                            let varIndex = _.findIndex(variantIds, (variant) => variant == item.id);
                            this.currentVariants.push({ id: item, price: data.restaurant_item_price[varIndex].price });
                        }
                    });
                }

                this.currentOptionalItems = [
                    {
                        id: null,
                        name: '',
                        price: '',
                    }];
                if (data.optional_items_prices && data.optional_items_prices.length > 0) {
                    this.currentOptionalItems = [];
                    let optionalIds = _.map(data.optional_items_prices, 'optional_item_id') || [];
                    this.optionalItems?.forEach((item) => {
                        if (optionalIds.includes(item.id)) {
                            let varIndex = _.findIndex(optionalIds, (optional) => optional == item.id);
                            this.currentOptionalItems.push(
                                { id: item, price: data.optional_items_prices[varIndex].price });
                        }
                    });
                }

                this.currentItem = data;
            },

            variantValue (data) {
                let variant = '';
                this.variants.forEach((v) => {
                    if (v.id == data?.restaurant_item_price[0]?.varient_id) {
                        variant = v?.varient_name;
                    }
                });
                return variant;
            },

            async saveTax () {
                if (this.selectedTax.length >= 0) {
                    this.taxForm.taxes = _.map(this.selectedTax, 'id');
                }
                await this.taxForm.post(window.location.origin + `/api/restaurant/${this.$route.params.slug}/tax`).
                    then(() => {
                        toast.fire({
                            type: 'success',
                            title: this.$t('restaurant.edit.tax_success_msg'),
                        });
                    }).
                    catch((err) => {
                        console.log(err.response);
                        let message = this.$t('common.error_msg');
                        if (err?.response?.data?.message) message = err?.response?.data?.message;
                        toast.fire({ type: 'error', title: message });
                    });
            },
            hasDuplicates (a) {
                return _.uniq(a).length !== a.length;
            },
            async saveConfiguration () {
                let optionItems = _.cloneDeep(this.currentOptionalItems);
                let variantsData = _.cloneDeep(this.currentVariants);

                optionItems = optionItems?.map((item) => {
                    item.name = item.id?.name;
                    delete item.id;
                    return item;
                });

                //for validations unique data
                let optionalDataForCheck = _.map(optionItems, 'name');
                if (this.hasDuplicates(optionalDataForCheck)) {
                    toast.fire({ type: 'error', title: 'Please select unique optional items.' });
                    return false;
                }

                let variantsDataForCheck = variantsData?.map((item) => {
                    if (item.name = item.id?.varient_name){
                        return item;
                    }
                });

                 variantsDataForCheck = _.map(variantsData, 'name');
                if (this.hasDuplicates(variantsDataForCheck)) {
                    toast.fire({ type: 'error', title: 'Please select unique variants.' });
                    return false;
                }

                variantsData = variantsData?.map((item) => {
                    item.id = item.id?.id;
                    return item;
                });

                let formData = {
                    optionItems: optionItems,
                    variants: variantsData,
                    item_id: this.currentItem.id,
                };

                axios.post(window.location.origin + `/api/restaurant/${this.$route.params.slug}/variant-options`,
                    formData).then((res) => {
                    $('#configureItem').modal('hide');
                    this.getItems();
                    this.getOptionalItems();
                }).catch(err => {
                    console.log(err);
                });
            },

            async saveRestItem (data) {
                this.itemPriceForm.restaurant_id = this.$route.params.slug;
                this.itemPriceForm.item_id = data.id;
                this.itemPriceForm.varient_id = this.selectedVariant?.id || null;
                this.itemPriceForm.price = this.selectedItemPrice;
                this.itemPriceForm.tax_rate = [];

                await this.itemPriceForm.post(window.location.origin + '/api/restaurant/price').then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('restaurant.edit.success_msg'),
                    });
                    this.isOpenModal = false;
                    this.getItems();
                }).catch((err) => {
                    console.log(err.response);
                    let message = this.$t('common.error_msg');
                    if (err?.response?.data?.message) message = err?.response?.data?.message;
                    toast.fire({ type: 'error', title: message });
                });
            },
        },
    };

</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
    .min-max {
        min-width: 12rem;
    }

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

    :deep(.multiselect__input, .multiselect__single) {
        background-color: transparent !important;
    }

    input {
        border-radius: 3px;
        border: 1px solid #9f9d9d;
        height: 30px;
    }
    
    .switch {
  position: relative;
  display: inline-block;
  width: 43px;
  height: 17px;
}

.switch input {
  display: none;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 15px;
  width: 15px;
  left: 1px;
  bottom: 1px;
  background-color: white;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

input:checked + .slider {
  background-color: #101010;
}

input:focus + .slider {
  box-shadow: 0 0 1px #101010;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
    </style>
