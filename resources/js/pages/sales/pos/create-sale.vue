<template>
  <div id="pos">

    <div class="row sm-col-reverse">

      <!-- POS Right area start -->
      <div class="col-12">
        <div class="card">
          <div class="card-body-l p-0">
            <div class="form-group pl-3 pt-3 pr-3 d-none">
              <div class="d-flex w-100">
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
            </div>

            <div class="table-responsive table-wrap">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Sr.No</th>
                    <th scope="col">Batch No.</th>
                    <th scope="col">{{ $t("common.product") }}</th>
                    <th class="text-center">
                      {{ $t("common.quantity") }}
                    </th>
                    <th class="text-right"> Rate </th>
                    <th scope="col" class="text-center">
                      {{ $t("common.action") }}
                    </th>
                  </tr>
                </thead>
                <!-- {{ selectedItemList }} -->
                <tbody v-if="selectedItemList && selectedItemList.length > 0">
                  <tr v-for="(singleItem, i) in selectedItemList" :key="i">
                    <td>{{ i + 1 }}</td>
                    <td>
                      <div class="form-group mb-0">
                        <div>
                          <input autofocus v-model="singleItem.inputText" @input="getSuggestions(singleItem)"
                            class="form-control" />

                          <ul v-if="singleItem.showSuggestions">
                            <li v-for="(suggestion, index) in singleItem.suggestions" :key="index"
                              @click="selectSuggestion(singleItem, suggestion)">
                              {{ suggestion.code }}
                            </li>
                          </ul>
                        </div>
                      </div>
                    </td>
                    <td>
                      {{ singleItem.name }}
                      <span v-if="singleItem.addonString != ''" style="font-size: 11px;"><br />{{
        singleItem.addonString }}</span>
                    </td>
                    <!-- <td>{{ parseFloat(singleItem?.price) | withCurrency }}</td> -->
                    <td>
                      <div class="text-center">
                        <!-- <input type="button" value="-" class="button-minus icon-shape icon-sm btn-danger"
                          data-field="quantity" @click="adjustQuantity($event, i, 'decrement')" /> -->
                        <input type="number" step="any" :id="`Qty-${i}`" :value="singleItem.quantity" name="quantity"
                          class="quantity-field border-0 incrementor" placeholder="Quantity" readonly />
                        <!-- required @input="adjustQuantity($event, i)"
                          @change="preventZeroValue($event, i)"  -->
                        <!-- <input type="button" value="+" class="button-plus icon-shape icon-sm btn-primary"
                          data-field="quantity" @click="adjustQuantity($event, i, 'increment')" /> -->
                      </div>
                    </td>
                    <td class="text-right">{{ itemSubtotal(singleItem) }}</td>
                    <td class="text-center">
                      <button type="button" class="btn btn-danger" @click="removeItem(i)">
                        <i class="fas fa-times"></i>
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3"></td>
                    <td class="text-right">Net Total:</td>
                    <td class="text-right">{{ foodItemNetTotal}}</td>
                    <td></td>

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

              <div class="row">
                <button class="btn btn-primary btn-block col-12 fs22" @click="saveOrder($event, true)">
                  <i class="fas fa-credit-card" /> &nbsp;Payment
                </button>
              </div>

            </div>
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

    <Modal class="pay-modal" v-if="showcash" :form="form">
      <!-- <h5 slot="header" style="margin: 1rem">{{ $t("pos.add_payment") }}</h5> -->
      <div slot="header" style="margin: 1rem" class="d-flex font-weight-bold justify-content-between w-100">
        <span>Net payable amount : <span>{{ form.netTotal | forBalanceSheetCurrencyDecimalOnly }}</span></span>
        <span @click=closeModalAndClearFormData>X</span>
      </div>
      <div class="w-100" slot="body">
        <div>

          <div class="row mb-3">
            <div class="form-group col-md-6">
              Date :
            </div>
            <div class="form-group col-md-6">
              {{ form.date }}
              <input id="date" v-model="form.date" type="hidden" class="form-control" />
            </div>
          </div>

          <div class="row" v-if="form.selectedProducts.length > 0 && isBank">
            <div class="form-group col-md-6">
              <input type="hidden" v-model="form.account">
              QR :

            </div>
            <div class="form-group col-md-6">
              <input ref="paidAmountInput" id="paidAmount" v-model="form.paidAmount" type="number" step="any"
                class="form-control" :class="{ 'is-invalid': form.errors.has('paidAmount') }" name="paidAmount" min="1"
                :max="form.netTotal" :placeholder="$t('common.paid_amount_placeholder')" />
              <has-error :form="form" field="paidAmount" />
            </div>
          </div>

          <div class="row" v-if="form.selectedProducts.length > 0 && isCash">
            <div class="form-group col-md-6">
              <input type="hidden" v-model="form.account">
              Cash :
            </div>
            <div class="form-group col-md-6">
              <input type="number" step="any" class="form-control"
                :value="(isCash && isBank) ? form.netTotal - form.paidAmount : form.netTotal"
                :class="{ 'is-invalid': form.errors.has('paidAmount') }" name="paidAmount" min="1" :max="form.netTotal"
                :placeholder="$t('common.paid_amount_placeholder')" :readonly="(isCash && isBank) ? true : false" />
              <has-error :form="form" field="paidAmount" />
            </div>
          </div>


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

    <Modal class="this" v-if="showbtn">
      <div slot="header" style="margin: 1rem" class="d-flex font-weight-bold justify-content-between w-100">
        <span>Net payable amount : <span>{{ form.netTotal | forBalanceSheetCurrencyDecimalOnly }}</span></span>
        <span @click=additional_modal>X</span>
        <!-- <button class="modal-default-button btn btn-danger" @click="additional_modal">
          X
        </button> -->
      </div>
      <div slot="body" class="d-flex flex-column p-3 w-100">
        <button @click="go_cash('cash')" class="btn btn-primary fs2 mb-3">Cash</button>
        <button @click="go_cash('bank')" class="btn btn-primary fs2 mb-3">QR</button>
        <button @click="go_cash('both')" class="btn btn-primary fs2 mb-3">Both</button>
      </div>
      <div class="payment-modal-footer" slot="modal-footer">
      </div>
    </Modal>
    <!-- kjkjkjbkj -->
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
                      <span class="pqty">{{ data.quantity }}
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
                  <td colspan="3" class="total">Bank</td>
                  <td style="text-align: right" class="total">
                    -{{ allData.paidAmount | withCurrency }}
                  </td>
                </tr>
                <tr>
                  <td colspan="3" class="total">Cash</td>
                  <td style="text-align: right" class="total">
                    -{{ parseFloat(allData.netTotal) - parseFloat(allData.paidAmount) | withCurrency
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
          <!-- <button @click="printInvoice" class="modal-default-button btn btn-info">
            {{ $t("common.print") }}
          </button> -->
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
          billContent: '',
      printerServiceUUID: '000018f0-0000-1000-8000-00805f9b34fb',
      device: null,
      server: null,
      characteristic: null,

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
            currentDate: '',
      currentTime: '',
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
                mode:'',
            }),
            audio: "",
            products: "",
            currentProduct: null,
            subCategoryOptions: [],
      selectedSubCategory: null,
            accounts: "",
            categories: [],
            productPrefix: "",
            invoicePrefix: "",
            showcash: false,
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
            selectedItemList: [{
                name: '' ,
                id: 0,
                addon: '',
                addonString: '',
                quantity: 0,
                price: 0,
                available_qty: '',
                inputText: '',
                suggestions: [],
                showSuggestions: false
            }],
            taxRate: 0,
            listBackup: null,
            formBackup: null,
            tax_included: false,
            loading:false,
            showbtn:false,
            isCash:false,
            isBank:false,

            // showSuggestions: false,
            // inputText:'',
            // suggestions: [],
        }),
        computed: {
            ...mapGetters("operations", ["items", "appInfo", "hotelItems", "selectedHotel"]),

            filteredProducts() {
              if (!this.selectedSubCategory) {
                return this.products;
              } else {
                return this.products.filter(product => product.subCategory.name === this.selectedSubCategory);
              }
            },

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
          go_cash(type){
            this.showbtn = false;
            if(type == 'cash'){
              this.isBank = false;
              this.isCash = true;
            } else if(type == 'bank'){
              this.isBank = true;
              this.isCash = false;
            } else {
              this.isCash = true;
              this.isBank = true;
            }
            return this.completeOrderAndAddPayment();

          },
          getSuggestions(item) { 
      
 
              item.suggestions = this.products.filter(suggestion =>
                  suggestion.available_qty > 0 && // Filtering by available quantity
                  suggestion.code && suggestion.code.toLowerCase().includes(item.inputText.toLowerCase())
              );
              item.showSuggestions = true;

            },

            selectSuggestion(index,suggestion){
              if(suggestion.available_qty == 0){
                    return toast.fire({
                        type: "error",
                        title: 'Insufficient Stock !',
                    });
              }

                this.currentProduct = suggestion;
                index.inputText = '';
                index.showSuggestions = false;
                toast.fire({ type: "success", title: "Order Added Successfully" });
                return this.addItemInList(suggestion.code);
            },
          async   order_recipt(){
      try {
        this.device = await navigator.bluetooth.requestDevice({
          filters: [{ services: [this.printerServiceUUID] }]
        });
        this.server = await this.device.gatt.connect();
        this.characteristic = await this.server.getPrimaryService(this.printerServiceUUID)
          .then(service => service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb'));
          let encoder = new TextEncoder("utf-8");
          // Add line feed + carriage return chars to text
          let encodedBillContent = encoder.encode(this.billContent + '\u000A\u000D');
        //const encodedBillContent = new TextEncoder().encode();
        await this.characteristic.writeValue(encodedBillContent);
        console.log('Bill sent to printer successfully.');
      } catch (error) {
        console.error('Error printing bill:', error);
      }
          },



          setTaxInclusivePrice() {

            let finalPrice = this.inclusiveTaxAmount(this.foodItemSubTotal,this.taxRate)
            return finalPrice;
          },
          setTaxExclusivePrice() {

            let finalPrice = this.exclusiveTaxAmount(this.foodItemSubTotal,this.taxRate)
            return finalPrice;
          },

          inclusiveTaxAmount(amount, taxRate) {
                let price = parseFloat(amount || 0);
                let rate = parseFloat(taxRate || 0);

                return  price * (100 / (100 + rate));
            },
          exclusiveTaxAmount(amount, taxRate) {
            let price = parseFloat(amount || 0);
            let rate = parseFloat(taxRate || 0);

            return  price * ((100 + rate)/100);
          },

            async changeHotel() {
                this.selectedItemList = [
                {
                name: '' ,
                id: 0,
                addon: '',
                addonString: '',
                quantity: 0,
                price: 0,
                available_qty: '',
                inputText: '',
                suggestions: [],
                showSuggestions: false
            }
                ];
                this.form.category = null;
                await this.getProducts();
            },
            async getFoodCategoryData() {
                const { data } = await axios.get(
                    window.location.origin + "/api/food/category/list"
                );
                console.log(data?.data);
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
              if(product.available_qty == 0){
                    return toast.fire({
                        type: "error",
                        title: 'Insufficient Stock !',
                    });
              }

                this.currentProduct = product;
                toast.fire({ type: "success", title: "Order Added Successfully" });
                return this.addItemInList();
            },
            addItemInList(code) {

                const addonNames = this.currentAddon?.map(add => add.name);
                const addonString = addonNames ? addonNames.join(' + ') : '';

                let alreadyAddedItem = _.findIndex(this.selectedItemList, (item) => {
                    return item.id == this.currentProduct.id && item.variant == this.currentVariant
                })

                let quantitySumByCode = {};

                this.selectedItemList.forEach(item => {
                  if (!quantitySumByCode[item.code]) {
                      quantitySumByCode[item.code] = 0;
                  }
                  quantitySumByCode[item.code] += item.quantity;
                });

                  if(alreadyAddedItem >= 0){
                    if(quantitySumByCode[this.selectedItemList[alreadyAddedItem].code]  >= this.selectedItemList[alreadyAddedItem].available_qty){
                        return toast.fire({
                            type: "error",
                            title: 'Insufficient Stock ! you can not added more than '+this.selectedItemList[alreadyAddedItem].available_qty+' Quantity',
                        });
                      }
                  }


                //   this.$set(this.selectedItemList, alreadyAddedItem, {
                //       name: `${this.currentProduct?.name}`,
                //       id: this.currentProduct.id,
                //       addon: this.currentAddon,
                //       addonString:addonString,
                //       quantity: parseFloat(this.selectedItemList[alreadyAddedItem].quantity) + 1,
                //       price: parseFloat(this.currentProduct?.sellingPrice || 0),
                //       total: parseFloat(this.currentProduct?.sellingPrice || 0),
                //       available_qty: this.currentProduct?.available_qty ?? 0,
                //   });

                // } else {

                    this.selectedItemList.unshift({
                        name: `${this.currentProduct?.name}`,
                        id: this.currentProduct.id,
                        addon: this.currentAddon,
                        addonString:addonString,
                        quantity: 1,
                        price: parseFloat(this.currentProduct?.sellingPrice || 0),
                        available_qty: this.currentProduct?.available_qty ?? 0,
                        inputText : code,

                    })
                // }
                this.currentVariant = null;
                this.currentAddon = [];

                this.showProductModal = false;
            },
            itemSubtotal(item) {
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
                this.form.hotel_id = (this.selectedHotel !== 'all') ? this.selectedHotel : this.hotel?.id || 1;
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
                const subCategoryNames = Array.from(new Set(data.data.map(product => product.subCategory.name)));

                this.subCategoryOptions = subCategoryNames.map(name => ({ label: name, value: name }));
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

                  if(this.selectedItemList[i].quantity >= this.selectedItemList[i].available_qty){
                    return toast.fire({
                        type: "error",
                        title: 'Insufficient Stock ! you can not added more than '+this.selectedItemList[i].available_qty+' Quantity',
                    });
                  }
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
                  return this.btn_pop();
                    // return this.completeOrderAndAddPayment();
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
                this.selectedItemList = [
                        {
                        name: '' ,
                        id: 0,
                        addon: '',
                        addonString: '',
                        quantity: 0,
                        price: 0,
                        available_qty: '',
                        inputText: '',
                        suggestions: [],
                        showSuggestions: false
                    }
                ];
                this.form.discount = 0;
                this.form.netTotal = this.foodItemNetTotal;
                this.form.orderTax = this.foodItemTax;
                this.form.tax = this.foodItemTax;
                this.form.subtotal = this.foodItemSubTotal;
                this.form.items = this.selectedItemList;
                this.form.client = null;
                this.form.room = null;
            },

            async addPayment() {

              if(this.form.paidAmount > this.form.netTotal){
                return toast.fire({ type: "error", title: "Max Amount should be "+this.form.netTotal });
              }
              this.form.hotel_id = (this.selectedHotel !== 'all') ? this.selectedHotel : this.hotel?.id || 1;
              this.form.mode = (this.isBank && this.isCash) ? 'both' :
                 (this.isBank) ? 'bank' :
                 (this.isCash) ? 'cash' :
                 '';

              await this.form
                    .post(window.location.origin + "/api/food/order/invoice")
                    .then(({ data }) => {
                        this.form.invoice_id = data.data.id;
                        this.form.invoice_slug = data.data.order_id_uniq;
                        this.form.receiptNo = data.data.order_id_uniq;

                        if (this.form.invoice_id != null) {
                           this.form
                            .post(window.location.origin + "/api/food/order/invoice/pay")
                            .then(() => {
                               toast.fire({ type: "success", title: 'Order Place Successfully' });
                                this.showcash = false;
                                this.allData = _.cloneDeep(this.form);
                                this.getProducts();
                                this.resetForm();
                                this.form.reset();
                                this.againDefaultSettings();

                            })
                            .catch(() => {
                                toast.fire({ type: "error", title: this.$t("common.error_msg") });
                            });
                        }
                    })
                    .catch(() => {
                        toast.fire({ type: "error", title: this.$t("common.error_msg") });
                    });

            },

            // close add payment modal and clear form data
            additional_modal(){
this.showbtn = false;
            },
            closeModalAndClearFormData() {
                this.showcash = false;
                // this.generateOrder = false;
                // this.resetForm();
                // this.form.reset();
                // this.againDefaultSettings();
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
                if (this.form.invoice_id != null) {
                    this.showcash = true;
                    this.form.paidAmount = _.clone(this.form.netTotal);

                    this.$nextTick(() => this.$refs.paidAmountInput && this.$refs.paidAmountInput.focus());
                }
            },
            async btn_pop(){
  if (this.form.invoice_id != null) {
                    this.showbtn = true;

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
                this.showcash = false;
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
  grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
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

span.stock_no {
  position: absolute;
  top: 0;
  right: 0;
  background: #ec3666;
  height: auto;
  display: inline;
  line-height: 20px;
  color: #fff;
  z-index: 9;
  padding: 1px 4px;
}

.table-responsive.table-wrap {
  min-height: 400px;
}

.table thead th {
  border-bottom: 0;
  font-size: 12px;
  padding-top: 5px !important;
  padding-bottom: 5px !important;
}

/* Style the suggestions list */
ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
  border: 1px solid #ccc;
}

li {
  padding: 8px;
  cursor: pointer;
}

li:hover {
  background-color: #f1f1f1;
}

.size {
  font-size: larger;
}

.pay_btn {
  height: 60px;
  margin-left: 166px;
}
</style>
