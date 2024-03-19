<template>
  <div id="pos">
    <!-- breadcrumbs Start -->
    <!-- <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" /> -->
    <!-- breadcrumbs end -->

    <div class="row sm-col-reverse">
      <!-- pos left area start -->
      <div class="col-12 col-md-7">
        <div class="card bg-transparent">
          <div class="pos-r-head bg-white">
            <div class="row">
              <!-- <div v-if="hotelItems.length" class="form-group col-md-6">
                <v-select class="flex-grow-1" v-model="hotel" :options="hotelItems" label="shop_name" name="hotel"
                  placeholder="Select a Shops" :clearable="false" />
                <has-error :form="form" field="category" />

              </div> -->
              <div v-if="products" class="col-md-12 form-group">
                <div class="d-flex w-100">
                  <search class="flex-grow-1" :isPosSearch="true" v-model="query" @reset-pagination="resetPagination()"
                    @reload="reload" />
                </div>
                <has-error :form="form" field="selectedProducts" />
              </div>
              <div v-if="categoryOptions.length" class="form-group col-md-6">
                <v-select v-model="selectedCategory" :options="categoryOptions" label="category_name"
                  :class="{ 'is-invalid': form.errors.has('category') }" name="category" placeholder="Select a Brand" />
                <has-error :form="form" field="category" />
              </div>

              <div v-if="categoryOptions.length" class="form-group col-md-6">
                <v-select v-model="selectedCategory" :options="categoryOptions" label="category_name"
                  :class="{ 'is-invalid': form.errors.has('category') }" name="category" placeholder="Select a Brand" />
                <has-error :form="form" field="category" />
              </div>

            </div>
          </div>

          <div class="card-body bg-white mt-3 pos-body">
            <table-loading v-show="loading" />
            <div class="pos-item-grid">
              <div v-if="product.status == 1" v-for="product in products" :key="product.id">
                <div>
                  <div class="pos-box" @click="openProductModal(product)">
                    <div class="relative">
                      <div class="pos-box-img">
                        <div v-if="product?.image">
                          <img class="pos-box-icon" :src="product?.image?.replace('storage/', '/storage/')"
                            alt="product image" />
                        </div>
                        <div v-else>{{ $t("common.no_preview") }}</div>
                      </div>
                    </div>
                    <div class="pos-box-content">
                      <p class="pos-box-text">{{ product.name }} ({{ product.inventoryCount ?? 0
                        }}
                        In stock)</p>
                      <span class="text-bold text-lg">{{ product.regularPrice | withCurrency
                        }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="row">
              <div class="col-12 d-flex justify-content-center">
                <!-- pagination-start -->
                <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
                  class="justify-flex-end mt-3" @paginate="paginate" />
                <!-- pagination-end -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- pos left area end -->

      <!-- POS Right area start -->
      <div class="col-12 col-md-5">
        <div class="card">
          <div class="card-body-l p-0">
            <div class="form-group pl-3 pt-3 pr-3 d-none">
              <div class="d-flex w-100">
                <!-- <Multiselect v-model="form.client" :options="clients" :taggable="false" :show-labels="false"
                                    tag-placeholder="" :placeholder="$t('common.client_placeholder')" class="form-control"
                                    @select="form.room = null" :custom-label="({ name, phone }) => `${name} (${phone})`"
                                    label="name" track-by="id" style="min-width: 85%"></Multiselect> -->
                <v-select v-model="form.client" :options="clients" label="name"
                  :class="{ 'is-invalid': form.errors.has('client') }" name="client"
                  :placeholder="$t('common.client_placeholder')" />
                <has-error :form="form" field="client" />
                <ClientCreateModal @reloadClients="getClients">
                  <div class="input-group-text create-btn">
                    <i class="fas fa-solid fa-plus-circle"></i>
                  </div>
                </ClientCreateModal>
              </div>
              <has-error :form="form" field="client" />
              <!-- <div class="w-100 mt-2">
                                <Multiselect v-model="form.room" :options="occupiedRooms" :taggable="false"
                                    :show-labels="false" tag-placeholder="" placeholder="Select a room" class="form-control"
                                    @select="form.client = null" label="room_name" track-by="id" style="min-width: 80%">
                                </Multiselect>
                            </div> -->
            </div>

            <div class="table-responsive table-wrap">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th scope="col">{{ $t("common.product") }}</th>
                    <th scope="col">{{ $t("common.price") }}</th>
                    <th scope="col" class="text-center">
                      {{ $t("common.quantity") }}
                    </th>
                    <th scope="col" class="text-center">
                      Total
                    </th>
                    <th scope="col" class="text-center">
                      {{ $t("common.action") }}
                    </th>
                  </tr>
                </thead>
                <tbody v-if="selectedItemList && selectedItemList.length > 0">
                  <tr v-for="(singleItem, i) in selectedItemList" :key="i">
                    <td>
                      {{ singleItem.name }}
                      <span v-if="singleItem.addonString != ''" style="font-size: 11px;"><br />{{
                singleItem.addonString }}</span>
                    </td>
                    <!-- <td>{{ parseFloat(product.variant?.price) + parseFloat(product.addonAmount) | withCurrency }}</td> -->
                    <td>{{ parseFloat(singleItem?.price) | withCurrency }}</td>
                    <td>
                      <div class="d-flex custom-qty-input">
                        <input type="button" value="-" class="button-minus icon-shape icon-sm btn-danger"
                          data-field="quantity" @click="adjustQuantity($event, i, 'decrement')" />
                        <input type="number" step="any" :id="`Qty-${i}`" :value="singleItem.quantity" name="quantity"
                          class="quantity-field border-0 incrementor" required @input="adjustQuantity($event, i)"
                          @change="preventZeroValue($event, i)" placeholder="Quantity" />
                        <input type="button" value="+" class="button-plus icon-shape icon-sm btn-primary"
                          data-field="quantity" @click="adjustQuantity($event, i, 'increment')" />
                      </div>
                    </td>
                    <td class="text-right">{{ itemSubtotal(singleItem) | withCurrency }}</td>
                    <td class="text-right">
                      <button type="button" class="btn btn-danger" @click="removeItem(i)">
                        <i class="fas fa-times"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr class="text-center">
                    <td colspan="5">{{ $t("no_data_found") }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="pos-card-footer bg-white">
          <div>

            <div class="pos-net-total noi-print">
              <!-- <div>
                Subtotal: {{ foodItemFinalSubtotal | withCurrency }}
              </div>
              <div>
                Discount: {{ foodItemDiscount | withCurrency }}
              </div>
              <div>
                GST: {{ foodItemTax | withCurrency }}
              </div> -->
              <div class="row">
                <button class="btn btn-primary btn-block col-6" @click="saveOrder($event, true)"
                  :disabled="selectedItemList.length <= 0">
                  <i class="fas fa-credit-card" />
                  Save & Payment
                </button>
                <div class="col-6">
                  Net Total: {{ foodItemNetTotal | withCurrency }}
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="row no-print">
          <div class="col-12 col-lg-6 mb-1">
            <button class="btn btn-primary btn-block" @click="saveOrder($event, false)"
              @keydown="form.onKeydown($event)" :disabled="selectedItemList.length <= 0">
              <i class="fas fa-save" /> Save
            </button>
          </div>
          <div class="col-12 col-lg-6 mb-1">
            <button class="btn btn-primary btn-block" @click="saveOrder($event, true)"
              :disabled="selectedItemList.length <= 0">
              <i class="fas fa-credit-card" />
              Save & Payment
            </button>
          </div>
        </div>
      </div>
      <!-- POS Right area end -->
    </div>

    <!-- use the modal component, pass in the prop -->

    <VModal v-if="showProductModal" v-model="showProductModal" @close="showProductModal = false">
      <h3 slot="title">{{ currentProduct.name }}</h3>
      <div>
        <div v-if="currentProduct?.image" style="width: 100%; height: 17rem">
          <img class="card-img-top p-3 rounded" :src="currentProduct?.image?.replace('storage/', '/storage/')"
            alt="product image" style="width:100%;height: 100%;object-fit:contain" />
        </div>
        <div v-else>{{ $t("common.no_preview") }}</div>
        <div class="card-body border-top p-4">
          <div>{{ currentProduct.name }}</div>
          <span class="text-bold text-lg">{{ currentProduct.price | withCurrency }}</span>

          <div v-if="currentProduct?.variants?.length > 0" class="mt-4">
            <h5 class="card-text text-bold mb-2">Select Variant</h5>
            <div class="form-check" v-for="variant in currentProduct?.variants" :key="variant.id">
              <input type="radio" class="form-check-input" v-model="currentVariant" :id="`variant${variant.id}`" name=""
                :value="variant">
              <label class="form-check-label d-flex justify-content-between" :for="`variant${variant.id}`"
                style="max-width: 60%">
                <span>{{ variant.name }}</span>
                <span>{{ variant.price }}</span>
              </label>
            </div>
          </div>

          <div v-if="currentProduct?.optionalItems?.length > 0" class="mt-4">
            <h5 class="card-text text-bold mb-2">Select Addon (Optional)</h5>
            <div class="form-check" v-for="optionalItem in currentProduct?.optionalItems" :key="optionalItem.id">
              <input type="checkbox" class="form-check-input" :id="`optionalItem${optionalItem.id}`" name="option1"
                :value="optionalItem" v-model="currentAddon">
              <label class="form-check-label d-flex justify-content-between" :for="`optionalItem${optionalItem.id}`"
                style="max-width: 60%">
                <span>{{ optionalItem.name }}</span>
                <span>+{{ optionalItem.price }}</span>
              </label>
            </div>
          </div>

          <div class="mt-4">
            <h5 class="card-text text-bold mb-2">Amount</h5>
            <h4 class="my-2">{{ currentItemAmount | withCurrency }}</h4>
          </div>

        </div>
      </div>
      <div slot="modal-footer">
        <button @click="addItemInList" class="btn btn-primary">Add Now</button>
        <button @click="showProductModal = false" class="btn btn-primary">Cancel</button>
      </div>
    </VModal>

    <Modal class="pay-modal" v-if="showModal" :form="form">
      <h5 slot="header" style="margin: 1rem">{{ $t("pos.add_payment") }}</h5>
      <div class="w-100" slot="body">
        <div>
          <div class="font-weight-bold">
            <span>Net payable amount :</span>
            <span>{{ form.netTotal | forBalanceSheetCurrencyDecimalOnly }}</span>
          </div>
          <div class="row" v-if="accounts &&
                form.selectedProducts &&
                form.selectedProducts.length > 0
                ">
            <div class="form-group col-md-6">
              <!-- <label for="account">{{ $t("common.account") }}
                <span class="required">*</span></label> -->
                <input type="hidden" v-model="form.account">
                Cash : 
              <!-- <v-select v-model="form.account" :options="accounts" label="ledgerName"
                :class="{ 'is-invalid': form.errors.has('account') }" name="account"
                :placeholder="$t('common.account_placeholder')" />
              <has-error :form="form" field="account" /> -->
            </div>
            <div class="form-group col-md-6">
              <!-- <label for="paidAmount">{{ $t("common.amount") }}<span class="required">*</span></label> -->
              <input ref="paidAmountInput" id="paidAmount" v-model="form.paidAmount" type="number" step="any"
                class="form-control" :class="{ 'is-invalid': form.errors.has('paidAmount') }" name="paidAmount" min="1"
                :max="form.netTotal" :placeholder="$t('common.paid_amount_placeholder')" />
              <has-error :form="form" field="paidAmount" />
            </div>
          </div>

          <div class="row" v-if="accounts &&
                form.selectedProducts &&
                form.selectedProducts.length > 0
                ">
            <div class="form-group col-md-6">
              <!-- <label for="account">{{ $t("common.account") }}
                <span class="required">*</span></label> -->
                <input type="hidden" v-model="form.account">
                Bank : 
            </div>
            <div class="form-group col-md-6">
              <!-- <label for="paidAmount">{{ $t("common.amount") }}<span class="required">*</span></label> -->
              <input type="number" step="any" class="form-control" :class="{ 'is-invalid': form.errors.has('paidAmount') }" name="paidAmount" min="1"
                :max="form.netTotal" :placeholder="$t('common.paid_amount_placeholder')" disabled/>
              <has-error :form="form" field="paidAmount" />
            </div>
          </div>
          
          <div class="row">
            <!-- <div class="form-group col-md-6">
              <label for="receiptNo">Order No.</label>
              <input id="receiptNo" v-model="form.receiptNo" type="text" class="form-control"
                :class="{ 'is-invalid': form.errors.has('receiptNo') }" name="receiptNo"
                :placeholder="$t('common.receipt_no_placeholder')" disabled />
              <has-error :form="form" field="receiptNo" />
            </div> -->
            <div class="form-group col-md-6">
              <label for="date">{{ $t("common.date") }}</label>
              <input id="date" v-model="form.date" type="date" class="form-control"
                :class="{ 'is-invalid': form.errors.has('date') }" name="date" />
              <has-error :form="form" field="date" />
            </div>
          </div>
          <!-- <div class="form-group">
            <label for="note">{{ $t("common.note") }}</label>
            <textarea id="note" v-model="form.note" class="form-control"
              :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('common.note_placeholder')" />
            <has-error :form="form" field="note" />
          </div> -->
        </div>
      </div>
      <div class="payment-modal-footer" slot="modal-footer">
        <div class="pos-modal-footer no-print">
          <button class="btn btn-primary" @click="addPayment" @keydown="form.onKeydown($event)">
            <i class="fas fa-save" /> {{ $t("common.save") }}
          </button>
          <button class="modal-default-button btn btn-danger" @click="closeModalAndClearFormData">
            {{ $t("common.close") }}
          </button>
        </div>
      </div>
    </Modal>

    <Modal v-if="showSmallInvoiceModal && allData">
      <h5 slot="header" class="no-print">Order Receipt</h5>
      <div class="w-100" slot="body">
        <div id="invoice-POS">
          <div style="max-width: 400px; margin: 0px auto">
            <div class="info">
              <div v-if="appInfo.blackLogo" class="pos-logo">
                <img :src="appInfo.blackLogo" width="100px" />
              </div>
              <h2 v-else class="text-center">{{ appInfo.companyName }}</h2>
              <div class="text-bold text-center text-lg" v-show="hotel.hotel_name"> {{ hotel.hotel_name }}
                <br />
              </div>
              <p>
                <span>{{ $t("common.date") }} : {{ allData.date }} <br /></span>
                <span v-show="hotel.hotel_address">{{ $t("common.address") }} : {{ hotel.hotel_address
                  }}
                  <br /></span>
                <span v-show="hotel.hotel_email">{{ $t("common.phone") }} : {{ hotel.hotel_phone }}
                  <br /></span>
                <span v-show="hotel.hotel_phone">{{ $t("common.email") }} : {{ hotel.hotel_email }}
                  <br /></span>
                <span v-show="allData?.client?.name">{{ $t("common.client") }} : {{ allData.client.name
                  }}
                  <br /></span>
              </p>
            </div>

            <table class="table_data">
              <tbody>
                <tr v-for="(data, i) in allData.selectedProducts" :key="i">
                  <td colspan="3">
                    <span>
                      <span>{{ data.name }}</span><br />
                      <span class="pqty">{{ data.quantity }} x {{ data?.variant?.price |
                withCurrency
                        }}
                        {{ addonPrice(data) > 0 ? '+' + addonPrice(data) : '' }} </span>
                    </span>
                  </td>
                  <td style="text-align: right; vertical-align: bottom">
                    {{ itemSubtotal(data) | withCurrency }}
                  </td>
                </tr>

                <tr style="margin-top: 10px">
                  <td colspan="3" class="total">{{ $t("common.subtotal") }}</td>
                  <td style="text-align: right" class="total">
                    {{ allData.subTotal | withCurrency }}
                  </td>
                </tr>
                <tr v-if="allData.discountAmount" style="margin-top: 10px">
                  <td colspan="3" class="total">{{ $t("common.discount") }}</td>
                  <td style="text-align: right" class="total">
                    {{ allData.discountAmount | withCurrency }}
                  </td>
                </tr>
                <tr v-if="allData.tax" style="margin-top: 10px">
                  <td colspan="3" class="total">{{ $t("common.tax") }}</td>
                  <td style="text-align: right" class="total">
                    {{ allData.tax }}
                  </td>
                </tr>
                <tr style="margin-top: 10px">
                  <td colspan="3" class="total">{{ $t("common.total") }}</td>
                  <td style="text-align: right" class="total">
                    {{ allData.netTotal | withCurrency }}
                  </td>
                </tr>
                <tr>
                  <td colspan="3" class="total">{{ $t("common.paid") }}</td>
                  <td style="text-align: right" class="total">
                    -{{ allData.paidAmount | withCurrency }}
                  </td>
                </tr>
                <tr>
                  <td colspan="3" class="total">{{ $t("common.due") }}</td>
                  <td style="text-align: right" class="total">
                    {{ parseFloat(allData.netTotal) - parseFloat(allData.paidAmount) | withCurrency
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
            <div id="legalcopy" class="ml-2 mb-4">
              <p class="legal">
                <strong>{{ $t("pos.receipt_text") }}</strong>
              </p>
              <div id="bar" style="overflow: hidden">
                <barcode width="2" height="25" fontSize="15" :value="allData.invoice_slug">
                  {{ $t("common.rendering_fails") }}
                </barcode>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="pos-modal-footer no-print" slot="modal-footer">
        <div>
          <button @click="printInvoice" class="modal-default-button btn btn-info">
            {{ $t("common.print") }}
          </button>
        </div>
        <button class="modal-default-button btn btn-danger" @click="closeReceiptModal">
          {{ $t("common.close") }}
        </button>
      </div>
    </Modal>
  </div>
</template>
<script>

</script>
<script>
    import Form from "vform";
    import axios from "axios";
    import {mapGetters} from "vuex";
    import VueBarcode from "vue-barcode";
    import sound from "../../../audio/beep.wav";
    import Multiselect from 'vue-multiselect';
    import VModal from "../../../components/VModal";

    export default {
        middleware: ["auth"],
        metaInfo() {
            return { title: this.$t("pos.page_title") };
        },
        components: {
            VModal,
            barcode: VueBarcode,
            Multiselect
        },
        data: () => ({
            breadcrumbsCurrent: "pos.breadcrumbs_current",
            breadcrumbs: [
                {
                    name: "pos.breadcrumbs_first",
                    url: "home",
                },
                {
                    name: "pos.breadcrumbs_second",
                    url: "invoices.index",
                },
                {
                    name: "pos.breadcrumbs_active",
                    url: "",
                },
            ],
            showProductModal: false,
            form: new Form({
                search: '',
                invoice_id: "",
                client: "1",
                discount: null,
                discountType: null,
                category: "",
                netTotal: 0,
                tax: 0,
                subTotal: 0,
                room: null,
                subtotal: 0,
                discountAmount: 0,
                selectedProducts: [],
                isRestaurantOrder: 1,
                paidAmount: 0,
                account: "",
                totalPaid: "",
                dueAmount: "",
                poReference: "",
                paymentTerms: "",
                addPayment: "",
                chequeNo: "",
                receiptNo: "",
                date: new Date().toISOString().slice(0, 10),
                note: "",
                status: 1,
            }),
            audio: "",
            products: "",
            currentProduct: null,
            accounts: "",
            categories: [],
            productPrefix: "",
            invoicePrefix: "",
            showModal: false,
            occupiedRooms: [],
            allData: null,
            showSmallInvoiceModal: false,
            printMe: false,
            perPage: 10,
            pagination: "",
            query: "",
            generateOrder: false,
            clickCount: 0,
            clients: [],
            categoryOptions: [],
            selectedCategory: null,
            hotel: null,
            currentVariant: null,
            currentAddon: [],
            selectedItemList: [],
            taxRate: 0,
            listBackup: null,
            formBackup: null,
            tax_included: false,
            loading:false,
        }),
        computed: {
            ...mapGetters("operations", ["items", "appInfo", "hotelItems", "selectedHotel"]),

            currentItemAmount() {
                return parseFloat(this.currentVariant?.price || 0) + this.currentAddonAmount;
            },
            currentAddonAmount() {
                let addonTotal = 0;
                if (this.currentAddon?.length > 0) {
                    this.currentAddon?.forEach(add => {
                        addonTotal += parseFloat(add.price);
                    })
                }

                return addonTotal;
            },
            foodItemSubTotal() {
    let amount = 0;
    this.selectedItemList.forEach((item) => {
        amount += parseFloat(item.quantity * item.price);
    });
    return amount;
},
            foodItemDiscount() {
                let discount = 0;
                if (this.form.discount) {
                    if (this.form.discountType == 1) {

                        discount = this.foodItemSubTotal * parseFloat(this.form.discount) / 100;
                    } else {
                        discount = this.form.discount;
                    }
                }
                return discount;
            },
            foodItemTax() {
                let price = 0;
                console.log(this.tax_included);
                if (this.tax_included) {
                    price = this.setTaxInclusivePrice()
                    console.log(price);
                } else {
                    price = this.foodItemSubTotal;
                    console.log(price);
                }
                let taxRate = 0;
                console.log("Price:", price);
                console.log("Tax Rate:", taxRate);
                return parseFloat(price)*parseFloat(taxRate)/100;
                // console.log(this.taxRate);
            },
            foodItemNetTotal() {
                let price = 0;
                if (this.tax_included) {
                    price = this.setTaxInclusivePrice();
                    return price - this.foodItemDiscount + this.foodItemTax;
                } else {
                    price = this.foodItemSubTotal;
                    return price - this.foodItemDiscount + this.foodItemTax;
                }

            },
            foodItemFinalSubtotal() {
                let price = 0;
                if (this.tax_included) {
                    price = this.setTaxInclusivePrice();
                    return price;
                } else {
                    price = this.setTaxExclusivePrice();
                    return this.foodItemSubTotal;
                }
                // return this.foodItemSubTotal - this.foodItemTax;
            }
        },
        async created() {

            await this.getHotelDataList();

            if (this.selectedHotel && this.selectedHotel !== 'all') {
                this.hotelItems.forEach((hotel) => {
                    if (hotel.id == this.selectedHotel) this.hotel = hotel
                })
            }
            if (!this.hotel) this.hotel = this.hotelItems[0];
            await this.getProducts();
            await this.getFoodCategoryData();
            await this.getClients();
            await this.getAccounts();
            this.audio = new Audio(sound);
            this.productPrefix = this.appInfo.productPrefix;
            this.invoicePrefix = this.appInfo.invoicePrefix;
            document.body.classList.add("sidebar-collapse");
        },
        watch: {
            // watch search data
            query: function () {
                this.getProducts();
            },
            hotel() {
                this.changeHotel();
            },
            selectedCategory(value) {
                this.getProducts()
            },
            async selectedHotel(value) {
                console.log(value)
                if (this.selectedHotel && this.selectedHotel !== 'all') {
                    this.hotelItems.forEach((hotel) => {
                        if (hotel.id == this.selectedHotel) this.hotel = hotel
                    })
                }
                if (!this.hotel) this.hotel = this.hotelItems[0];
                await this.getProducts();
                await this.getFoodCategoryData();
                await this.getClients();
                await this.getAccounts();
            },
            // tax_included: {
            //   handler: 'setNewPrices',
            // },
        },
        methods: {
        //   setNewPrices() {
        //     if (this.tax_included) this.setTaxInclusivePrice();
        //     else this.setTaxExclusivePrice();
        //   },
          setTaxInclusivePrice() {

            let finalPrice = this.inclusiveTaxAmount(this.foodItemSubTotal,this.taxRate)
            return finalPrice;
          },
          setTaxExclusivePrice() {

            let finalPrice = this.exclusiveTaxAmount(this.foodItemSubTotal,this.taxRate)
            return finalPrice;
          },

          inclusiveTaxAmount(amount, taxRate) {
                taxRate = 0;
                let price = parseFloat(amount || 0);
                let rate = parseFloat(taxRate || 0);

                return  price * (100 / (100 + rate));
            },
          exclusiveTaxAmount(amount, taxRate) {
            taxRate = 0;
            let price = parseFloat(amount || 0);
            let rate = parseFloat(taxRate || 0);

            return  price * ((100 + rate)/100);
          },

            async changeHotel() {
                this.selectedItemList = [];
                this.form.category = null;
                await this.getProducts();
            },
            async getFoodCategoryData() {
                const { data } = await axios.get(
                    window.location.origin + "/api/food/category/list"
                );
                this.categoryOptions = data?.data;
            },
            async getHotelDataList() {
                await this.$store.dispatch('operations/getHotelData', {
                    path: '/api/shop',
                });
            },
            doThis() {
                console.log("do this");
            },
            openProductModal(product) {
              if(product.inventoryCount == 0 || product.inventoryCount == null){
                    return toast.fire({
                        type: "error",
                        title: 'Insufficient Stock !',
                    });
              }


                this.currentProduct = product;
                console.log(product)
                toast.fire({ type: "success", title: "Order Added Successfully" });
                // this.currentVariant = product.variants[0]
                // if (product.optionalItems.length <= 0 && product.variants.length <= 1) {
                    return this.addItemInList();
                // }
                // this.showProductModal = true;
            },
            addItemInList() {
                // if (!this.currentVariant || this.currentVariant.length <= 0) {
                //     return toast.fire({
                //         type: "error",
                //         title: 'Select at-least one variant',
                //     });
                // }
                console.log(this.selectedItemList)
                let alreadyAddedItem = _.findIndex(this.selectedItemList, (item) => {
                    return item.id == this.currentProduct.id && item.variant == this.currentVariant
                })

                const addonNames = this.currentAddon?.map(add => add.name);

                const addonString = addonNames ? addonNames.join(' + ') : '';

                if (alreadyAddedItem >= 0) {
                    this.selectedItemList[alreadyAddedItem] = {
                        name: `${this.currentProduct?.name}`,
                        id: this.currentProduct.id,
                        // variant: this.currentVariant,
                        addon: this.currentAddon,
                        // addonAmount: this.currentAddonAmount,
                        addonString:addonString,
                        quantity: 1,
                        // total: parseFloat(this.currentProduct?.sellingPrice || 0) + this.currentAddonAmount
                        price: parseFloat(this.currentProduct?.regularPrice || 0),
                        total: parseFloat(this.currentProduct?.regularPrice || 0),
                        inventoryCount: this.currentProduct?.inventoryCount ?? 0,
                    }

                } else {
                    this.selectedItemList.push({
                        name: `${this.currentProduct?.name}`,
                        id: this.currentProduct.id,
                        // variant: this.currentVariant,
                        addon: this.currentAddon,
                        // addonAmount: this.currentAddonAmount,
                        addonString:addonString,
                        quantity: 1,
                        // total: parseFloat(this.currentProduct?.sellingPrice || 0) + this.currentAddonAmount
                        price: parseFloat(this.currentProduct?.regularPrice || 0),
                        inventoryCount: this.currentProduct?.inventoryCount ?? 0,
                    })
                }
                this.currentVariant = null;
                this.currentAddon = [];

                this.showProductModal = false;
                console.log(this.selectedItemList, 'list')
            },
            itemSubtotal(item) {
                // let addonTotal = 0;
                // if (item.addon?.length > 0) {
                //     item.addon?.forEach(add => {
                //         addonTotal += parseFloat(add.price);
                //     })
                // }
                // return parseFloat(item.quantity * (parseFloat(item.variant?.price) + parseFloat(addonTotal)) || 0);

                return parseFloat(item.quantity * parseFloat(item?.price));
            },
            addonPrice(item) {
                let addonTotal = 0;
                if (item.addon?.length > 0) {
                    item.addon?.forEach(add => {
                        addonTotal += parseFloat(add.price);
                    })
                }

                return addonTotal;
            },

            // get all clients
            async getClients(id = null) {
                await axios
                    .get("/api/all-clients")
                    .then(({ data }) => {
                        this.clients = data.data;
                        this.clients.sort((a, b) => {
                            return a.id - b.id;
                        });
                    })
                    .catch((error) => console.log(error));
            },

            // get accounts
            async getAccounts() {
                const { data } = await this.form.get(
                    window.location.origin + "/api/all-accounts?bank_only=1"
                );
                this.accounts = data.data;

                // assign default account
                if (this.accounts && this.accounts.length > 0) {
                    let defaultAccountSlug = this.appInfo.defaultAccountSlug;
                    this.form.account = this.accounts.find(
                        (account) => account.slug == defaultAccountSlug
                    );
                }
                let extraAccount = {
                        'id': 0,
                        'ledgerName':'Pay Later'
                    }

                    this.accounts.push(extraAccount)

            },

            // get products
            async getProducts() {
                this.loading = true;
                this.form.hotel_id = this.hotel?.id || 1;
                this.form.category = this.selectedCategory?.id || ''
                this.form.search = this.query || '';
                let currentPage = this.pagination ? this.pagination.current_page : 1;
                const { data } = await this.form.get(
                    window.location.origin +
                    "/api/products?page=" +
                    currentPage
                    );

                this.taxRate = data.data?.length > 0 ? data.data[0].taxRate : 0;
                this.products = data.data;
                this.pagination = data.meta;
                this.loading = false;
            },

            // pagination
            async paginate(page) {
                this.pagination.current_page = page
                let catSlug = this.form.category?.slug;

                if (this.query === "") {
                    await this.getProducts();
                } else {
                    await this.getProducts();
                }
            },

            // Reset pagination
            async resetPagination() {
                this.pagination ? (this.pagination.current_page = 1) : "";
                await this.getProducts();
            },

            // Reload after search
            async reload() {
                this.query = "";
                await this.getProducts();
            },

            // update array
            adjustQuantity(event, i, type) {
                if (type === 'increment') {
                    this.selectedItemList[i].quantity += 1;
                } else if (type === 'decrement') {
                    this.selectedItemList[i].quantity -= 1;
                } else {
                    this.selectedItemList[i].quantity = event.target.value;
                }
                if (this.selectedItemList[i].quantity == 0) this.selectedItemList[i].quantity = 1;

                this.selectedItemList[i].total = this.itemSubtotal(this.selectedItemList[i]);

            },
            preventZeroValue(event, i) {
                // if (event.target.value == 0) event.target.value = 1;
                this.selectedItemList[i].quantity = event.target.value;
            },

            // remove item from array
            removeItem(index) {
                toast.fire({ type: "success", title: "Order Remove Successfully" });
                this.selectedItemList.splice(index, 1);
            },

            async saveOrder(event, isPayment = '')
            {
                this.form.hotel_id = this.hotel?.id || 1;
                this.form.discountAmount = this.foodItemDiscount;
                this.form.netTotal = this.foodItemNetTotal;
                this.form.tax = this.foodItemTax;
                this.form.orderTax = this.foodItemTax;
                this.form.subtotal = this.foodItemFinalSubtotal;
                this.form.selectedProducts = this.selectedItemList;
                this.form.subTotal = this.foodItemFinalSubtotal;

                if (this.form.client && isPayment) {
                    return this.completeOrderAndAddPayment();
                }
                await this.form
                    .post(window.location.origin + "/api/restaurant/order")
                    .then(({ data }) => {
                        this.resetForm();
                        toast.fire({ type: "success", title: "Order Created Successfully" });
                    })
                    .catch((error) => {
                        console.log(error)
                        toast.fire({ type: "error", title: this.$t("common.error_msg") });
                    });
            },

            resetForm() {
                this.listBackup = _.cloneDeep(this.selectedItemList)
                this.formBackup = _.cloneDeep(this.form)
                this.selectedItemList = [];
                this.form.discount = 0;
                this.form.netTotal = this.foodItemNetTotal;
                this.form.orderTax = this.foodItemTax;
                this.form.tax = this.foodItemTax;
                this.form.subtotal = this.foodItemSubTotal;
                this.form.items = this.selectedItemList;
                this.form.client = null;
                this.form.room = null;
            },

            // save invoice
            async saveInvoice(isDirect = true) {
                await this.form
                    .post(window.location.origin + "/api/food/order/invoice")
                    .then(({ data }) => {
                        this.form.invoice_id = data.data.id;
                        this.form.invoice_slug = data.data.order_id_uniq;
                        this.form.receiptNo = data.data.order_id_uniq;
                        if (isDirect) {
                            this.showInvoiceAndPrint();
                            this.resetForm();
                        }
                    })
                    .catch(() => {
                        toast.fire({ type: "error", title: this.$t("common.error_msg") });
                    });
            },

            // save payment
            async addPayment() {
                if (this.form.invoice_id != null) {
                    await this.form
                        .post(window.location.origin + "/api/food/order/invoice/pay")
                        .then(async () => {
                            this.showModal = false;
                            this.allData = _.cloneDeep(this.form);
                            await this.showInvoiceAndPrint();
                            this.resetForm();
                            this.form.reset();
                            this.againDefaultSettings();
                        })
                        .catch(() => {
                            toast.fire({ type: "error", title: this.$t("common.error_msg") });
                        });
                } else {
                    await toast.fire({ type: "error", title: this.$t("common.error_msg") });
                }
            },

            // close add payment modal and clear form data
            closeModalAndClearFormData() {
                this.showModal = false;
                this.generateOrder = false;
                this.resetForm();
                this.form.reset();
                this.againDefaultSettings();
            },

            // close receipt modal
            closeReceiptModal() {
                this.showSmallInvoiceModal = false;
                this.form.reset();
                this.allData = null;
                this.againDefaultSettings();
                this.clickCount = 0; // reset click count
                console.log("from close" + this.clickCount);
            },

            // complete order and add payment
            async completeOrderAndAddPayment() {
                await this.saveInvoice(false);
                if (this.form.invoice_id != null) {
                    this.showModal = true;
                    this.form.paidAmount = _.clone(this.form.netTotal.toFixed(2));

                    this.$nextTick(() => this.$refs.paidAmountInput && this.$refs.paidAmountInput.focus());
                }
            },

            // show invoice and print
            async showInvoiceAndPrint() {
                this.form.reset();
                this.againDefaultSettings();

                this.showSmallInvoiceModal = true;
                setTimeout(() => this.printInvoice(), 500);
            },

            // print invoice
            printInvoice() {
                var divContents = document.getElementById("invoice-POS").innerHTML;
                var a = window.open("", "", "height=500, width=500");
                a.document.write(
                    '<link rel="stylesheet" href="/css/pos_print.css"><html>'
                );
                a.document.write("<body >");
                a.document.write(divContents);
                a.document.write("</body></html>");
                a.document.close();
                a.print();
            },
            // again default settings
            againDefaultSettings() {
                this.getAccounts();
                this.getClients();
                this.showModal = false;
                this.generateOrder = false;
            },
        },
    };
</script>
<style src="../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss" scoped>
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

.pos-r-head {
  box-shadow: 0px 0px 3px #0003;
  padding: 20px;
  box-sizing: border-box;
  border-radius: 5px;
  border-bottom: 1px solid #f3f3f3;
}

.pos-logo {
  text-align: center;
}

.pos-item-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
  grid-gap: 10px;
}

.pos-item-grid>div {
  border: 0;
  border-radius: 10px;
  box-shadow: 0 4px 20px 1px rgb(0 0 0 / 6%), 0 1px 4px rgb(0 0 0 / 8%);
  overflow: hidden;
  cursor: pointer;
  border: 1px solid #fff;
  position: relative;
}

.pos-item-grid>div:hover {
  border-color: #6366f1;
}

.pos-item-grid>div .box-qty {
  position: absolute;
  width: 50px;
  height: 30px;
  display: block;
  background: #6366f1;
  top: 0;
  left: 0px;
  text-align: center;
  line-height: 30px;
  font-size: 12px;
  font-weight: bold;
  color: #fff;
  border-bottom-right-radius: 10px;
}

.qty-red {
  background: red !important;
}

.pos-body {
  border-radius: 5px;
  min-height: 240px;
}

.pos-box-img {
  width: 100%;
  height: 100px;
  border-bottom: 1px solid #f1f1f1;
  background: #ebebeb;
  line-height: 100px;
  text-align: center;
  font-size: 13px;
  font-weight: bold;
}

.pos-box-img img {
  width: 100%;
  height: 100px;
  object-fit: cover;
}

.pos-box-content p {
  font-size: 14px;
  font-weight: bold;
  margin-bottom: 0px;
}

.pos-box-content span {
  font-size: 12px;
  margin-bottom: 2px;
}

.pos-box-content {
  padding: 5px 10px;
}

.pos-item-grid-red {
  border-color: red !important;
}

.card-client-search {
  padding: 20px;
  border-bottom: 1px solid #ddd;
}

.table-wrap {
  padding: 15px;
}

.table-responsive.table-wrap>table {
  border: 1px solid #ddd;
}

.table-wrap .table thead tr {
  border-bottom: 0;
  background: #6366f11f !important;
}

.table-wrap .incrementor {
  width: 80px;
}

.table-wrap .custom-qty-input {
  display: inline-flex !important;
  justify-content: center;
  border: 1px solid #ececfdb8;
  padding: 0;
  border-radius: 18px;
  /* background: #ddd; */
}

.table-wrap .btn-danger {
  width: 25px;
  height: 25px;
  font-size: 10px;
  padding: 0px;
}

.table-wrap .icon-sm {
  width: 25px;
  height: 25px;
  line-height: 23px;
}

.pos-card-footer {
  border: 1px solid #ddd;
  background: #fff;
  border-radius: 4px;
  margin-bottom: 15px;
}

.pos-net-total {
  background: #6366f133;
  width: 100%;
  padding: 10px 10px;
  text-align: right;
  font-size: 18px;
  font-weight: bold;
  display: flex;
  flex-direction: column;
  gap: 5px
}

.product {
  cursor: pointer;

  .info-box {
    &:hover {
      background: #e0e0e0;
    }
  }
}

.dark-mode .pos-body,
.dark-mode .pos-r-head {
  background: #111827 !important;
  border-color: #000;
}

.dark-mode .pos-item-grid>div {
  border-color: #6c757d !important;
}

.dark-mode .pos-box-content {
  padding: 5px 10px;
  color: #fff;
}

.dark-mode .pos-item-grid>div.pos-item-grid-red {
  border-color: red !important;
}

.dark-mode .card-client-search {
  border-color: #6c757d;
}

.dark-mode .table-striped tbody tr:nth-of-type(odd) {
  background-color: #1f2937;
}

.dark-mode .table-responsive.table-wrap>table {
  border: 1px solid #6c757d;
}

.dark-mode .table-wrap .incrementor {
  border: none !important;
}

.dark-mode .pos-card-footer.bg-white {
  background: #111827 !important;
  border-color: #6c757d;
}

.dark-mode .pos-card-footer label {
  color: #fff;
}

.dark-mode .pos-net-total {
  background: rgb(99 169 241);
  color: #fff;
}

#invoice-POS td,
#invoice-POS th,
#invoice-POS tr,
#invoice-POS table {
  border-collapse: collapse;
}

#invoice-POS tr {
  border-bottom: 2px dotted #05070b;
}

#invoice-POS table {
  width: 100%;
}

#invoice-POS tfoot tr th:first-child {
  text-align: left;
}

#invoice-POS .info {
  margin-bottom: 20px;
}

#invoice-POS .info>p {
  margin-top: 20px;
}

#legalcopy {
  margin-top: 5mm;
}

#legalcopy p {
  text-align: center;
}

#bar {
  text-align: center;
}

.total {
  font-weight: bold;
  font-size: 12px;
}

span.pqty {
  display: block;
  line-height: 15px;
  font-size: 12px;
  font-weight: 500;
  margin-bottom: 5px;
}

@media only screen and (max-width: 1250px) {
  .pos-item-grid {
    grid-template-columns: 1fr 1fr 1fr 1fr;
  }
}

@media only screen and (max-width: 991px) {
  .pos-item-grid {
    grid-template-columns: 1fr 1fr 1fr;
  }
}

@media only screen and (max-width: 767px) {
  .sm-col-reverse {
    flex-direction: column-reverse;
  }

  .pos-item-grid {
    grid-template-columns: 1fr 1fr 1fr 1fr;
  }
}

.create-btn {
  padding: 11px;
}

.create-btn-2 {
  padding: 10px;
}
</style>
