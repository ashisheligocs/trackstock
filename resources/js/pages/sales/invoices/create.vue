<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ $t("sales.invoices.create.form_title") }}
                        </h3>
                        <router-link :to="{ name: 'invoices.index' }" class="btn btn-secondary float-right">
                            <i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form role="form" @submit.prevent="saveInvoice" @keydown="form.onKeydown($event)">
                        <div class="card-body">
                            <div class="row" v-if="items.length">
                                <div class="form-group col-md-4">
                                    <label for="hotel" class="d-block">{{ $t('sidebar.hotel') }}
                                        <span class="required">*</span></label>
                                    <v-select class="flex-grow-1" v-model="form.hotel_id" :options="hotelItems"
                                        label="hotel_name" :class="{ 'is-invalid': form.errors.has('hotel_id') }"
                                        name="hotel_id" placeholder="Search a hotel" @input="getClients" />
                                    <has-error :form="form" field="hotel_id" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="client">{{ $t("common.client") }}
                                        <span class="required">*</span></label>
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex w-100">
                                                <v-select class="flex-grow-1" v-model="form.client" :options="clientItems"
                                                    label="name" :class="{ 'is-invalid': form.errors.has('client') }"
                                                    name="client" :placeholder="$t('common.client_placeholder')" />
                                                <ClientCreateModal :hotel_id="this.form.hotel_id" @reloadClients="getClients">
                                                    <div class="input-group-text create-btn">
                                                        <i class="fas fa-solid fa-plus-circle"></i>
                                                    </div>
                                                </ClientCreateModal>
                                            </div>
                                            <has-error :form="form" field="client" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="reference">
                                        {{ $t("common.reference") }}
                                    </label>
                                    <input id="reference" v-model="form.reference" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('reference') }" name="reference"
                                        :placeholder="$t('common.reference_placeholder')" />
                                    <has-error :form="form" field="reference" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="hotel" class="d-block">{{ $t('common.select_customer_type') }}
                                        <span class="required">*</span></label>
                                    <label>
                                        <input type="radio" v-model="customer_type" @change="handleRadioChange"
                                            value="other" /> Other Services
                                    </label>
                                    &nbsp;
                                    <label>
                                        <input type="radio" v-model="customer_type" @change="handleRadioChange"
                                            value="products" /> Products
                                    </label>
                                </div>
                            </div>
                            <div class="row mt-3 mb-4" v-if="customer_type == 'other'">
                                <div class="table-responsive table-custom w-95 m-auto">
                                    <table class="table table-hover table-sm text-center">
                                        <thead>
                                            <tr>
                                                <th>{{ $t("common.s_no") }}</th>
                                                <th>{{ $t("common.service_name") }}</th>
                                                <th>{{ $t("common.quantity") }}</th>
                                                <th>{{ $t("common.price") }}</th>
                                                <th>{{ $t("common.unit_price") }}</th>
                                                <th>{{ $t("common.tax") }}</th>
                                                <th>{{ $t("common.subtotal") }}</th>
                                                <th class="text-right">{{ $t("common.action") }}</th>
                                            </tr>   
                                        </thead>
                                        <tbody>
                                            <tr  v-for="(item, index) in rows" :key="index" >
                                                <td>{{ index+1 }}</td>
                                                <td>
                                                    <input type="text" v-model="item.service_name" class="form-control"
                                                        placeholedr="Enter Service Name" name="service_name[]">
                                                </td>
                                                <td>
                                                    <div class="input-group custom-qty-input">
                                                        <input type="button" value="-"
                                                            class="button-minus icon-shape icon-sm btn-danger"
                                                            data-field="quantity" @click="generateServiceTotal(index,'decrement')" />

                                                        <input type="number" step="any"   v-model="item.quantity"
                                                            name="quantity[]" class="quantity-field border-0 incrementor"
                                                            required    @change="generateServiceTotal(index,'')"   placeholder="Quantity" />

                                                        <input type="button" value="+"
                                                            class="button-plus icon-shape icon-sm btn-primary"
                                                            data-field="quantity" @click="generateServiceTotal(index,'increment')" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group custom-qty-input">
                                                        <input type="button" value="-"
                                                            class="button-minus icon-shape icon-sm btn-danger"
                                                            data-field="unitPrice" @click="generatePriceTotal(index,'decrement')" />
                                                        <input type="number" step="any" :id="`unitPrice`" v-model="item.price" @change="generatePriceTotal(index,'')" name="unitPrice[]" class="quantity-field border-0 incrementor" required min="0"/>
                                                        <input type="hidden"   :value="item.myTax" name="myTax[]"   />
                                                        <input type="hidden"   :value="item.productTotalTax" name="productTotalTax[]"   />
                                                        <input type="hidden"   :value="item.price" name="price[]"   />

                                                        <input type="button" value="+"
                                                            class="button-plus icon-shape icon-sm btn-primary"
                                                            data-field="unitPrice" @click="generatePriceTotal(index,'increment')" />
                                                    </div>
                                                </td>
                                                <td>{{ item.price  | withCurrency }}</td>
                                                <td>{{ item.myTax  | withCurrency }}</td>   
                                                <td>{{ item.price * item.quantity  | withCurrency }}</td>
                                                <td class="text-right">
                                                    <button v-if="index > 0" type="button" class="btn btn-danger" @click="cloneRow(index,'decrement')">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger" @click="cloneRow(index,'increment')">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td colspan="5" class="text-right">
                                                    <strong>{{ $t("common.total") }}</strong>
                                                </td>
                                                <td>
                                                    <strong> {{ form.productTotalTax | withCurrency }}</strong>
                                                </td>
                                                <td>
                                                    <strong> {{ form.subTotal | withCurrency}}</strong>
                                                </td>
                                                <td>
                                                    
                                                </td> 
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <div class="row" v-if="products && customer_type == 'products'">
                                <div class="form-group col-md-12">
                                    <label for="product">{{ $t("common.select_products") }}
                                        <span class="required">*</span></label>
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex w-100">
                                                <v-select class="flex-grow-1" v-model="form.product" :options="products"
                                                    label="label" :class="{
                                                        'is-invalid': form.errors.has('selectedProducts'),
                                                    }" name="product" :placeholder="$t('common.select_products_placeholder')
    " @input="storeProduct(form.product)" />
                                                <ProductCreateModal @reloadProducts="getProducts">
                                                    <div class="input-group-text create-btn">
                                                        <i class="fas fa-solid fa-plus-circle"></i>
                                                    </div>
                                                </ProductCreateModal>
                                            </div>
                                            <has-error :form="form" field="selectedProducts" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="form.selectedProducts && form.selectedProducts.length > 0" class="row mt-3 mb-4">
                                <div class="table-responsive table-custom w-95 m-auto">
                                    <table class="table table-hover table-sm text-center">
                                        <thead>
                                            <tr>
                                                <th>{{ $t("common.s_no") }}</th>
                                                <th>{{ $t("common.code") }}</th>
                                                <th>{{ $t("common.product_name") }}</th>
                                                <th>{{ $t("common.quantity") }}</th>
                                                <th>{{ $t("common.price") }}</th>
                                                <th>{{ $t("common.unit_price") }}</th>
                                                <th>{{ $t("common.tax") }}</th>
                                                <th>{{ $t("common.subtotal") }}</th>
                                                <th class="text-right">{{ $t("common.action") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in form.selectedProducts" :key="i">
                                                <td>{{ ++i }}</td>
                                                <td>
                                                    {{ item.code | withPrefix(prefix) }}
                                                </td>
                                                <td>
                                                    <span v-if="Number(item.inventoryCount) < Number(item.qty)
                                                        " v-tooltip="$t('common.insufficient_stock')"
                                                        class="badge badge-danger p-2">
                                                        <i class="fas fa-exclamation"></i>
                                                    </span>
                                                    <router-link v-if="$can('product-view')" :to="{
                                                        name: 'products.show',
                                                        params: { slug: item.slug },
                                                    }">
                                                        {{ item.name }}
                                                    </router-link>
                                                    <span v-else>{{ item.name }}</span>
                                                </td>
                                                <td>
                                                    <div class="input-group custom-qty-input">
                                                        <input type="button" value="-"
                                                            class="button-minus icon-shape icon-sm btn-danger"
                                                            data-field="quantity" @click="
                                                                generateItemTotal(item.qty,'qty',i - 1,'decrement')" />

                                                        <input type="number" step="any" :id="`Qty-${i}`" :value="item.qty"
                                                            name="quantity" class="quantity-field border-0 incrementor"
                                                            required min="1" :max="item.inventoryCount" @change="
                                                                generateItemTotal($event.target.value,'qty',i - 1,'')"
                                                                 @keyup="generateItemTotal($event.target.value,'qty',i - 1,'')"
                                                                  placeholder="Quantity" />

                                                        <input type="button" value="+"
                                                            class="button-plus icon-shape icon-sm btn-primary"
                                                            data-field="quantity" @click="generateItemTotal(item.qty,'qty',i - 1,'increment')" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group custom-qty-input">
                                                        <input type="button" value="-"
                                                            class="button-minus icon-shape icon-sm btn-danger"
                                                            data-field="unitPrice" @click="generateItemTotal(item.unitPrice,'price',i - 1,'decrement')" />
                                                        <input type="unitPrice" step="any" :id="`unitPrice-${i}`"
                                                            :value="item.unitPrice" name="unitPrice"
                                                            class="quantity-field border-0 incrementor" required min="0"
                                                            @change="generateItemTotal($event.target.value,'price',i - 1,'')"
                                                            @keyup="generateItemTotal($event.target.value,'price',i - 1,'')" />

                                                        <input type="button" value="+"
                                                            class="button-plus icon-shape icon-sm btn-primary"
                                                            data-field="unitPrice"
                                                             @click="generateItemTotal(item.unitPrice,'price',i - 1,'increment')" />
                                                    </div>
                                                </td>
                                                <td>{{ item.unitCost | withCurrency }}</td>
                                                <td>{{ item.totalTax | withCurrency }}</td>
                                                <td>{{ item.totalPrice | withCurrency }}</td>
                                                <td class="text-right">
                                                    <button type="button" class="btn btn-danger" @click="removeItem(item)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-right">
                                                    <div v-if="selectedProductTaxRates"
                                                    v-for="(item, index) in Object.keys(selectedProductTaxRates)" :key="index + '12'"
                                                    class="ml-3 mt-1">
                                                    <div v-if="selectedProductTaxRates[item] > 0"
                                                        class="d-flex justify-content-between align-items-center">
                                                        <p class="font-weight-bold text-muted mb-0">{{ item }}</p>
                                                        <p class="mb-0">{{ selectedProductTaxRates[item] | withCurrency }}</p>
                                                    </div>
                                                    </div>
                                                </td>
                                                <td colspan="3" class="text-right">
                                                    <strong>{{ $t("common.total") }}</strong>
                                                </td>
                                                <td>
                                                    <strong> {{ form.productTotalTax | withCurrency }}</strong>
                                                </td>
                                                <td>
                                                    <strong>{{ form.subTotal | withCurrency }}</strong>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="discountType">{{
                                        $t("common.discount_type")
                                    }}</label>
                                    <select id="discountType" v-model="form.discountType" step="any" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('discountType') }" name="discountType"
                                        @change="calculateSum" @keyup="calculateSum">
                                        <option value="0">{{ $t("common.fixed") }}</option>
                                        <option value="1">{{ $t("common.percentage") }}(%)</option>
                                    </select>
                                    <has-error :form="form" field="discountType" />
                                </div>
                                <div class="form-group" :class="form.discountType == 1 ? 'col-md-2' : 'col-md-4'">
                                    <label for="discount">{{ $t("common.discount") }}
                                        <span v-if="form.discountType == 1">(%)</span></label>
                                    <input id="discount" v-model="form.discount" type="number" step="any" min="1"
                                        :max="form.discountType == 1 ? 100 : form.subTotal" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('discount') }" name="discount"
                                        :placeholder="$t('common.discount_placeholder')" @change="calculateSum"
                                        @keyup="calculateSum" />
                                    <has-error :form="form" field="discount" />
                                </div>
                                <div v-if="form.discountType == 1" class="form-group col-md-2">
                                    <label for="totalDiscount">{{
                                        $t("common.total_discount")
                                    }}</label>
                                    <input id="totalDiscount" v-model="form.totalDiscount" type="number" step="any"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('totalDiscount') }"
                                        name="totalDiscount" readonly />
                                    <has-error :form="form" field="totalDiscount" />
                                </div>
                                <!-- <div class="form-group col-md-4">
                                    <label for="transportCost">{{
                                        $t("common.transport_cost")
                                    }}</label>
                                    <input id="transportCost" v-model="form.transportCost" type="number" step="any" min="1"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('transportCost') }"
                                        name="transportCost" :placeholder="$t('common.transport_cost_placeholder')"
                                        @change="calculateSum" @keyup="calculateSum" />
                                    <has-error :form="form" field="transportCost" />
                                </div> -->
                            </div>

                            <div class="row">
                                <div v-if="taxes && customer_type != 'products'" class="form-group col-md-4">
                                    <label for="orderTax">{{ $t("common.invoice_tax") }}
                                        <span class="required">*</span></label>
                                    <multiselect v-model="form.orderTax" :options="taxes"  
                                                    label="code" track-by="name" :multiple="true" :taggable="true"
                                                    :class="{ 'is-invalid': form.errors.has('orderTax') }" name="orderTax[]" 
                                                    placeholder="Select a tax type" @input="calculateSum"  :close-on-select="false"></multiselect>
                                    <has-error :form="form" field="orderTax" />
                                </div>
                                <div v-if="taxes" class="form-group col-md-4">
                                    <label for="totalTax">{{ $t("common.total_tax") }}</label>
                                    <input id="totalTax" v-model="form.totalTax" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('totalTax') }" name="totalTax" readonly />
                                    <has-error :form="form" field="totalTax" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="netTotal">{{ $t("common.net_total") }}</label>
                                    <input id="netTotal" v-model="form.netTotal" type="number" step="any"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('netTotal') }"
                                        name="netTotal" readonly />
                                    <has-error :form="form" field="netTotal" />
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="poReference">{{
                                        $t("common.po_reference")
                                    }}</label>
                                    <input id="poReference" v-model="form.poReference" type="text" step="any"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('poReference') }"
                                        name="poReference" :placeholder="$t('common.po_reference_placeholder')" />
                                    <has-error :form="form" field="poReference" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="paymentTerms">{{
                                        $t("common.payment_terms")
                                    }}</label>
                                    <input id="paymentTerms" v-model="form.paymentTerms" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('paymentTerms') }" name="paymentTerms"
                                        :placeholder="$t('common.payment_terms_placeholder')" />
                                    <has-error :form="form" field="paymentTerms" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="addPayment">{{ $t("common.add_payment") }}</label>
                                    <select id="addPayment" v-model="form.addPayment" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('addPayment') }" name="addPayment">
                                        <option value="" selected disabled>
                                            {{ $t("common.add_payment_placeholder") }}
                                        </option>
                                        <option :disabled="!form.selectedProducts" value="1">
                                            {{ $t("common.yes") }}
                                        </option>
                                        <option value="0">{{ $t("common.no") }}</option>
                                    </select>
                                    <has-error :form="form" field="addPayment" />
                                </div>
                            </div> -->
                            <div class="row" v-if="form.addPayment == 1 &&
                                accounts &&
                                form.selectedProducts &&
                                form.selectedProducts.length > 0
                                ">
                                <div class="form-group col-md-4">
                                    <label for="account">{{ $t("common.account") }}
                                        <span class="required">*</span></label>
                                    <v-select v-model="form.account" :options="accounts" label="ledgerName"
                                        :class="{ 'is-invalid': form.errors.has('account') }" name="account"
                                        :placeholder="$t('common.account_placeholder')" />
                                    <has-error :form="form" field="account" />
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="paidAmount">{{ $t("common.paid_amount")
                                    }}<span class="required">*</span></label>
                                    <input id="paidAmount" v-model="form.paidAmount" type="number" step="any"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('paidAmount') }"
                                        name="paidAmount" min="1" :max="form.netTotal"
                                        :placeholder="$t('common.paid_amount_placeholder')" />
                                    <has-error :form="form" field="paidAmount" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="chequeNo">{{ $t("common.cheque_no") }}</label>
                                    <input id="chequeNo" v-model="form.chequeNo" type="text" step="any" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('chequeNo') }" name="chequeNo"
                                        :placeholder="$t('common.cheque_placeholder')" />
                                    <has-error :form="form" field="chequeNo" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="receiptNo">{{ $t("common.receipt_no") }}</label>
                                    <input id="receiptNo" v-model="form.receiptNo" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('receiptNo') }" name="receiptNo"
                                        :placeholder="$t('common.receipt_no_placeholder')" />
                                    <has-error :form="form" field="receiptNo" />
                                </div>
                            </div>
                            <div class="row">
                                <!-- <div class="form-group col-md-4">
                                    <label for="deliveryPlace">{{
                                        $t("sales.common.delivery_place")
                                    }}</label>
                                    <input id="deliveryPlace" v-model="form.deliveryPlace" type="text" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('deliveryPlace') }" name="deliveryPlace"
                                        :placeholder="$t('sales.common.delivery_place_placeholder')" />
                                    <has-error :form="form" field="deliveryPlace" />
                                </div> -->
                                <div class="form-group col-md-4">
                                    <label for="date">{{ $t("common.date") }}</label>
                                    <input id="date" v-model="form.date" type="date" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('date') }" name="date" min="" />
                                    <has-error :form="form" field="date" />
                                </div>
                                <!-- <div class="form-group col-md-4">
                                    <label for="status">{{ $t("common.status") }}</label>
                                    <select id="status" v-model="form.status" class="form-  "
                                        :class="{ 'is-invalid': form.errors.has('status') }">
                                        <option value="1">{{ $t("common.active") }}</option>
                                        <option value="0">{{ $t("common.in_active") }}</option>
                                    </select>
                                    <has-error :form="form" field="status" />
                                </div> -->
                            </div>

                            <div class="form-group">
                                <label for="note">{{ $t("common.note") }}</label>
                                <textarea id="note" v-model="form.note" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('note') }"
                                    :placeholder="$t('common.note_placeholder')" />
                                <has-error :form="form" field="note" />
                            </div>

                            <div class="form-group col-12 d-flex flex-wrap">
                                <div class="pr-5">
                                    <toggle-button v-model="form.isSendEmail" />
                                    {{ $t("Send To Email") }}
                                </div>
                            </div>
                            <!--                            <div class="form-group col-12 d-flex flex-wrap">-->
                            <!--                                <div class="pr-5">-->
                            <!--                                    <toggle-button v-model="form.isSendSMS"/>-->
                            <!--                                    {{ $t("Send To SMS") }}-->
                            <!--                                </div>-->
                            <!--                            </div>-->
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <v-button :loading="form.busy" class="btn btn-primary">
                                <i class="fas fa-save" /> {{ $t("common.save") }}
                            </v-button>
                            <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                                <i class="fas fa-power-off" /> {{ $t("common.reset") }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>     

<script>
import Form from "vform";
import { mapGetters } from "vuex";
import { ToggleButton } from "vue-js-toggle-button";
import Multiselect from 'vue-multiselect'
export default {
    middleware: ["auth", "check-permissions"],
    metaInfo() {
        return { title: this.$t("sales.invoices.create.page_title") };
    },
    components: {
        ToggleButton,
        Multiselect,
    },
    data: () => ({
        breadcrumbsCurrent: "sales.invoices.create.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "sales.invoices.create.breadcrumbs_first",
                url: "home",
            },
            {
                name: "sales.invoices.create.breadcrumbs_second",
                url: "invoices.index",
            },
            {
                name: "sales.invoices.create.breadcrumbs_active",
                url: "",
            },
        ],
        form: new Form({
            invoiceNo: "",
            client: "",
            reference: "",
            selectedProducts: [],
            subTotal: 0,
            netTotal: 0,
            discountType: 0,
            discount: "",
            totalDiscount: "",
            transportCost: 0,
            orderTax: [],
            totalTax: 0,
            productTotalTax: 0,
            account: "",
            totalPaid: "",
            dueAmount: "",
            poReference: "",
            paymentTerms: "",
            deliveryPlace: "",
            addPayment: "",
            chequeNo: "",
            receiptNo: "",
            date: new Date().toISOString().slice(0, 10),
            note: "",
            status: 1,
            isSendEmail: false,
            isSendSMS: false,
            hotel_id: ''
        }),
        total_services: ['a'],
        clientItems:[],
        rows: [
        { service_name:'',price: 0, quantity: 1 ,myTax: 0} 
        ],
        products: "",
        accounts: "",
        taxes: "",
        prefix: "",
        customer_type: null,
        product_name: null,
        i: 1,  
        selectedProductTaxRate:[],
    }),
    // unitCost:0,
    // totalTax:0,
    // totalPrice:0,
    computed: {
        ...mapGetters("operations", ["items", "appInfo", "hotelItems", "selectedHotel"]),
        ...mapGetters({
            user: "auth/user",
        }),
        selectedProductTaxRates(){
            return this.selectedProductTaxRate;
        }
    },
    
    async created() {
        
        await this.getHotelDataList();
        await this.getClients();
        await this.getProducts();
        await this.getAccounts();
        await this.getTaxes();
        this.prefix = this.appInfo.productPrefix;
        if (this.selectedHotel && this.selectedHotel !== 'all') {
            this.hotelItems.forEach((hotel) => {
                if (hotel.id == this.selectedHotel) this.form.hotel_id = hotel
            })
        }

        if(this.user.back_days != '' && this.user.back_days != 'All'){
            var today = new Date();
            var minDate = new Date();

            minDate.setDate(today.getDate() - this.user.back_days);
            document.getElementById("date").min = minDate.toISOString().split("T")[0];
        }
    },
    methods: {
        handleRadioChange() {
            if (this.customer_type == 'other') { 
                this.form.selectedProducts=[];
                this.calculateSum();
                this.calculate_total() 
            }
        },
        async getHotelDataList() {
            await this.$store.dispatch('operations/getHotelData', {
                path: '/api/hotel',
            });
        },
        // get all clients
        async getClients() {
            
            await this.$store.dispatch("operations/allData", {
                path: (typeof this.form.hotel_id?.id === 'undefined') ? "/api/all-clients" : "/api/all-clients?hotel_id="+this.form.hotel_id?.id,
            });
            // assign default client
            if (this.items && this.items.length > 0) {
                let defaultClientSlug = this.appInfo.defaultClientSlug;
                this.form.client = this.items.find(
                    (item) => item.slug === defaultClientSlug
                );
            }
            this.clientItems = this.items;
        },

        // get products
        async getProducts() {
            const { data } = await this.form.get(
                window.location.origin + "/api/all-products"
            );
            
            this.products = data.data;
            this.products.sort(this.sortProducts);
           
        },

        // sort products
        sortProducts(a, b) {
            if (Number(a.code) < Number(b.code)) {
                return -1;
            }
            if (Number(a.code) > Number(b.code)) {
                return 1;
            }
            return 0;
        },
        // sort products
        cloneRow(index,type) {
            if(type == 'increment'){
                const lastRow = this.rows[this.rows.length - 1];
                const newRow = { ...lastRow }; // Shallow clone, you can use a deep clone if necessary
                this.rows.push(newRow);
                this.rows[this.rows.length - 1].quantity = 0;
                this.rows[this.rows.length - 1].price = 0; 
                this.rows[this.rows.length - 1].service_name = ''; 
            }else if(type == 'decrement'){
                const lastRow = this.rows[this.rows.length - 1];
                const newRow = { ...lastRow }; // Shallow clone, you can use a deep clone if necessary
                this.rows.pop(newRow);
                this.calculate_total()
            }
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
                    (item) => item.slug === defaultAccountSlug
                );
            }
        },

        // get taxes
        async getTaxes() {
            const { data } = await this.form.get(
                window.location.origin + "/api/all-vat-rates"
            );
            this.taxes = data.data; 
            // assign default vat rate
            if (this.taxes && this.taxes.length > 0) {
                let defaultVatRateSlug = this.appInfo.defaultVatRateSlug;
                this.form.orderTax = this.taxes.find(
                    (tax) => tax.slug === defaultVatRateSlug
                ); 
            } 
            this.calculateSum();
        },

        // store item in array
        storeProduct(product) {
            var index = this.form.selectedProducts.findIndex(
                (x) => x.id == product.id
            );
            let quantity = 1;
            if (index === -1) {
                let productTax = product.taxAmount;
                    // product.taxType == "Exclusive"
                    //     ? product.priceWithDiscount * (product.taxRate / 100)
                    //     : product.priceWithDiscount -
                    //     product.priceWithDiscount / (1 + product.taxRate / 100);
                let totalTax = productTax * quantity;

                this.form.selectedProducts.unshift({
                    id: product.id,
                    slug: product.slug,
                    name: product.name,
                    code: product.code,
                    taxType: product.taxType,
                    taxRate: product.taxRate,
                    qty: quantity,
                    inventoryCount: product.inventoryCount,
                    avgPurchasePrice: product.avgPurchasePrice,
                    unitPrice: product.priceWithDiscount,
                    unitCost:
                        product.taxType == "Exclusive"
                            ? product.priceWithDiscount + productTax
                            : product.priceWithDiscount,
                    totalPrice:
                        product.taxType == "Exclusive"
                            ? 1 * (product.priceWithDiscount + totalTax)
                            : 1 * product.priceWithDiscount,
                    // productTax: product.productTax > 0 ? product.productTax : 0,
                    // totalTax: totalTax,
                    productTax: productTax,
                    // totalTax: productTax * quantity,
                    productTaxRate : product.productTaxRate,
                    totalTax: parseFloat(product.avgPurchasePrice) * parseFloat(quantity) * parseFloat(product.productTaxRate[0].rate * 2) / 100,
                });
                this.calculateSum();
            }
            this.generateItemTotal(quantity, "qty", index, "");
            
            return;
        },

        // update array
        generateServiceTotal(index,action) {  
            if(action == 'increment'){
                this.rows[index].quantity++;
            }else if(action == 'decrement'){
                this.rows[index].quantity--;
            }  
            if(this.rows[index].quantity <= 0){
                this.rows[index].quantity = 1
            }
            this.calculate_total()
        },

        // update array
        generatePriceTotal(index,action) { 
            if(action == 'increment'){
                this.rows[index].price++;
            }else if(action == 'decrement'){
                this.rows[index].price--;
            }  
            if(this.rows[index].price <= 0){
                this.rows[index].price = 1
            } 
            this.calculate_total()
        },
        calculate_total(){  
            var total_price = 0;
            var mytotalTax = 0;
            this.rows.forEach(function(row){
                total_price += row.price * row.quantity;
                mytotalTax += row.myTax;
            });
            this.form.subTotal = total_price;
            this.form.productTotalTax = mytotalTax; 
            // this.calculateSum()
            var totalTax = 0;
            // if (this.form.orderTax) {
                //     this.form.totalTax =
                //         (this.form.orderTax.rate / 100) * this.form.subTotal;
                // }
                if (this.form.orderTax) {
                    this.form.orderTax.forEach(item => {
                        totalTax += (item.rate / 100) * this.form.subTotal; 
                    }); 
                    this.form.totalTax =  Number(totalTax.toFixed(2)); 
                }


                if (this.form.selectedProducts && this.form.selectedProducts.length) {
                let gst = {};

                this.form.selectedProducts.forEach(tax => {
                    tax.productTaxRate.forEach(taxRate => {
                        if(taxRate.amount != 0){
                            gst[taxRate.code] = 0;
                        }
                    });
                });
                
                this.form.selectedProducts.forEach(tax => {
                    
                    tax.productTaxRate.forEach(taxRate => {
                        if(taxRate.amount != 0){
                            this.form.totalTax += (tax.unitPrice * tax.qty * taxRate.rate) / 100;
                            gst[taxRate.code] += (tax.unitPrice * tax.qty * taxRate.rate) / 100;
                        }
                    });
                    
                });
                this.form.totalTax = Number(this.form.totalTax).toFixed(2);
                this.selectedProductTaxRate = gst;
            }
            

            if (this.form.subTotal > 0) {
                let discount = Number(this.form.discount);
                if (this.form.discountType == 1) {
                    discount = (discount / 100) * this.form.subTotal;
                    this.form.totalDiscount = Number(discount.toFixed(2));
                } else {
                    discount = Number(this.form.discount);
                }
                if (this.form.selectedProducts.length){
                    this.form.netTotal =
                    (this.form.subTotal +
                    Number(this.form.transportCost) -
                    discount).toFixed(2);
                } else {
                    this.form.netTotal =
                    (this.form.subTotal +
                    Number(this.form.transportCost) -
                    discount +
                    this.form.totalTax).toFixed(2);
                }
                
            }
        },
        generateItemTotal(value, type, index, action) {
            let item = this.form.selectedProducts[index];
           
            if (item) {
                if (type == "qty") {
                    item.qty = value;
                    if (action == "increment") {
                        item.qty = Number(item.qty) + 1;
                    } else if (action == "decrement") {
                        if (item.qty > 0) {
                            item.qty = Number(item.qty) - 1;
                        }
                    }
                } else {
                    item.unitPrice = value;
                    if (action == "increment") {
                        item.unitPrice = Number(item.unitPrice) + 1;
                    } else if (action == "decrement") {
                        if (item.unitPrice > 0) {
                            item.unitPrice = Number(item.unitPrice) - 1;
                        }
                    }
                }
                
                item.productTaxRate = item.productTaxRate;
                item.productTax = parseFloat(item.productTax);
                // item.productTax =
                //     item.taxType == "Exclusive"
                //         ? item.unitPrice * (item.taxRate / 100)
                //         : item.unitPrice - item.unitPrice / (1 + item.taxRate / 100);

                // item.totalTax = item.productTax * item.qty;
                item.totalTax = parseFloat(item.unitPrice) * parseFloat(item.qty) * parseFloat(item.productTaxRate[0].rate * 2) / 100;
                item.totalPrice =
                    item.taxType == "Exclusive"
                        ? item.qty * item.unitPrice + item.totalTax
                        : item.qty * item.unitPrice;
                item.unitCost =
                    item.taxType == "Exclusive"
                        ? Number(item.unitPrice) + Number(item.productTax)
                        : item.unitPrice;
                this.form.selectedProducts[index] = item;
                this.calculateSum();
            }
            return;
        },

        // remove item from array
        removeItem(item) {
            let index = this.form.selectedProducts.indexOf(item);
            if (index > -1) {
                this.form.selectedProducts.splice(index, 1);
            }
            this.calculateSum();
            this.calculate_total()
            return;
        },
        async disableOther(selectedTax){
            console.log(selectedTax)
            const { data } = await this.form.get(
                window.location.origin + "/api/all-vat-rates"
            );
            this.taxes = data.data; 
            // assign default vat rate
            if (this.taxes && this.taxes.length > 0) {
                let defaultVatRateSlug = this.appInfo.defaultVatRateSlug;
                this.form.orderTax = this.taxes.find(
                    (tax) => tax.slug === defaultVatRateSlug
                );
            } 
        },
        // calculate sum
        calculateSum() { 
            // calculate subtotal
            this.form.subTotal = this.form.selectedProducts.reduce(function (
                prev,
                cur
            ) {
                return Number((prev + cur.totalPrice).toFixed(2));
            },
                0);

            // calculate product tax
            this.form.productTotalTax = this.form.selectedProducts.reduce(function (
                prev,
                cur
            ) {
                return Number((prev + cur.totalTax).toFixed(2));
            },
                0);

            this.form.netTotal = this.form.subTotal; 
            // calculate quatation tax 
            // if(this.rows){

            //     var total_price = 0;
            //     var mytotalTax = 0;
            //     this.rows.forEach(function(row){   
            //         total_price += row.price * row.quantity;
            //         mytotalTax += row.myTax;
            //     });
            //     this.form.subTotal = total_price;
            // }
            // this.form.productTotalTax = mytotalTax.toFixed(2); 
            
            this.form.totalTax = 0;


            // if (this.form.orderTax) {
            //     this.form.totalTax =
            //         (this.form.orderTax.rate / 100) * this.form.subTotal;
            // }
            var totalTax = 0;
            if (this.form.orderTax) {
                this.form.orderTax.forEach(item => {
                    totalTax +=  ((item.rate / 100) * this.form.subTotal); 
                }); 
                this.form.totalTax = totalTax.toFixed(2) ;  
                // var selectedTax = this.form.orderTax[0].rate ?? '';
                // if(selectedTax){
                //     this.disableOther(selectedTax)
                // }
            }


            if (this.form.selectedProducts && this.form.selectedProducts.length) {
                let gst = {};

                this.form.selectedProducts.forEach(tax => {
                    tax.productTaxRate.forEach(taxRate => {
                        if(taxRate.amount != 0){
                            gst[taxRate.code] = 0;
                        }
                    });
                });
                
                this.form.selectedProducts.forEach(tax => {
                    
                    tax.productTaxRate.forEach(taxRate => {
                        if(taxRate.amount != 0){
                            this.form.totalTax += (tax.unitPrice * tax.qty * taxRate.rate) / 100;
                            gst[taxRate.code] += (tax.unitPrice * tax.qty * taxRate.rate) / 100;
                        }
                    });
                    
                });
                this.form.totalTax = Number(this.form.totalTax).toFixed(2);
                this.selectedProductTaxRate = gst;
            }

            // calculate discount and total
            if (this.form.subTotal > 0) {
                let discount = Number(this.form.discount);
                if (this.form.discountType == 1) {
                    discount = (discount / 100) * this.form.subTotal;
                    this.form.totalDiscount = Number(discount.toFixed(2));
                } else {
                    discount = Number(this.form.discount);
                }
                if (this.form.selectedProducts.length) {
                    var this__netTotal =   this.form.subTotal + Number(this.form.transportCost) - discount;
                } else {
                    var this__netTotal =   this.form.subTotal + Number(this.form.transportCost) - discount + parseFloat(this.form.totalTax);
                }
                 
                if(this__netTotal){
                    this.form.netTotal = this__netTotal;
                }
            }
            this.form.rows = this.rows;
            if(this.customer_type == 'other'){
                this.calculate_total()
            }
            return;
        },

        // vue file upload
        onFileChange(e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            if (
                file.size < 2111775 &&
                (file.type === "image/jpeg" ||
                    file.type === "image/png" ||
                    file.type === "image/gif")
            ) {
                reader.onloadend = (file) => {
                    this.formClient.image = reader.result;
                };
                reader.readAsDataURL(file);
                this.url = URL.createObjectURL(file);
            } else {
                Swal.fire(
                    this.$t("common.error"),
                    this.$t("common.image_error"),
                    "error"
                );
            }
        },

        // save invoice
        async saveInvoice() {  
            await this.form
                .post(window.location.origin + "/api/invoices")
                .then(({ data }) => {
                    toast.fire({
                        type: "success",
                        title: this.$t("sales.invoices.create.success_msg"),
                    });
                    this.$router.push({
                        name: "invoices.show",
                        params: { slug: data.data.slug },
                    });
                })
                .catch(() => {
                    toast.fire({ type: "error", title: this.$t("common.error_msg") });
                });
        },

        // save client
        async saveClient() {
            // this.formClient.hotel_id = this.form.hotel_id;
            // console.log(this.formClient);
            await this.formClient
                .post(window.location.origin + "/api/clients")
                .then(() => {
                    toast.fire({
                        type: "success",
                        title: this.$t("clients.create.success_msg"),
                    });
                    this.showClientCreateModal = false;
                    // this.getClients();
                    this.formClient.reset();
                    this.url = null;
                })
                .catch(() => {
                    toast.fire({ type: "error", title: this.$t("common.error_msg") });
                });
        },
    },
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
:deep(.multiselect__tags) {
    min-height: 38px !important;
    border: none !important;
    padding: 4px 40px 0 4px !important;
}
:deep(.multiselect__placeholder){
    margin-bottom: 4px !important;
    padding-top: 4px !important;
}
:deep(.multiselect__single) {
    margin-bottom: 0px !important;
    margin-top: 4px !important;
}
:deep(.multiselect){
    width: auto;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
    min-height: 38px !important;
}
.create-btn {
    padding: 11px;
}
</style>
