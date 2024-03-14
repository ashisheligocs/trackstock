<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <!-- Main content -->
        <div class="invoice p-3 mb-3">
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-6 invoice-col">
              <CompanyInfo :hotel="allData.hotel"/>
            </div>
            <!-- /.col -->
            <div
              v-if="allData.category && allData.subCategory"
              class="col-sm-4 offset-sm-2 invoice-col float-right text-md-right"
            >
              <h5>{{ $t("expenses.list.view.expense_details") }}</h5>
              <strong v-if="allData.date">{{ $t("common.date") }}:</strong>
              {{ allData.date | moment("Do MMM, YYYY") }}<br />
              <strong>{{ $t("common.category") }}:</strong>
              {{ allData.category.name }} [{{
                allData.category.code | withPrefix(catPrefix)
              }}]
              <br />
              <strong>{{ $t("common.sub_category") }}:</strong>
              {{ allData.subCategory.name }} [{{
                allData.subCategory.code | withPrefix(subCatPrefix)
              }}]
              <br />
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <!-- Table row -->
          <div class="row mt-5">
            <div class="table-responsive table-custom mt-3 text-center">
              <table class="table" v-if="allData.transaction">
                <thead>
                  <tr>
                    <th class="text-left">{{ $t("common.image") }}</th>
                    <th class="text-left">{{ $t("expenses.list.common.expense_reason") }}</th>
                    <th class="text-right">{{ $t("common.amount") }}</th>
                    <th class="text-left">{{ $t("common.account") }}</th>
                    <th class="text-left" v-if="allData.transaction.cheque_no">
                      {{ $t("common.cheque_no") }}
                    </th>
                    <th class="text-left" v-if="allData.transaction.receipt_no">
                      {{ $t("common.voucher_no") }}
                    </th>
                    <th class="text-left" v-if="allData.note">{{ $t("common.note") }}</th>
                    <th class="text-left">{{ $t("common.status") }}</th>
                    <th class="text-right">{{ $t("common.created_by") }}</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td class="text-left">
                      <a
                        v-if="allData.image"
                        href="#"
                        id="show-modal"
                        @click="showModal = true"
                      >
                        <img
                          :src="allData.image"
                          class="rounded preview-sm m-auto"
                          loading="lazy"
                        />
                      </a>
                      <div
                        v-else
                        class="bg-secondary rounded no-preview-sm m-auto"
                      >
                        <small>{{ $t("common.no_preview") }}</small>
                      </div>
                    </td>
                    <td class="text-left">{{ allData.reason }}</td>
                    <td class="text-right" v-if="allData.transaction">
                      {{ allData.transaction.amount | withCurrency }}
                    </td>
                    <td v-if="allData.account" class="text-left">
                      {{ allData.account.label }}
                    </td>
                    <td class="text-left" v-if="allData.transaction.cheque_no">
                      {{ allData.transaction.cheque_no }}
                    </td>
                    <td class="text-left" v-if="allData.transaction.receipt_no">
                      {{ allData.transaction.receipt_no }}
                    </td>
                    <td class="text-left" v-if="allData.note">{{ allData.note }}</td>
                    <td class="text-left">
                      <span
                        v-if="allData.status === 1"
                        class="badge bg-success"
                        >{{ $t("common.active") }}</span
                      >
                      <span v-else class="badge bg-danger">{{
                        $t("common.in_active")
                      }}</span>
                    </td>
                    <td class="text-right">{{ allData.createdBy }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.row -->

          <!-- this row will not appear when printing -->
          <div class="row no-print mt-5">
            <div class="col-12">
              <router-link
                :to="{ name: 'expenses.index' }"
                class="btn btn-secondary float-right"
              >
                <i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}
              </router-link>
              <a href="#" @click="printWindow" class="btn btn-default"
                ><i class="fas fa-print"></i> {{ $t("common.print") }}</a
              >
            </div>
          </div>
        </div>
        <!-- /.invoice -->
      </div>
    </div>
    <!-- use the modal component, pass in the prop -->
    <Modal v-if="showModal" @close="showModal = false">
      <h5 slot="header">{{ $t("common.modal_header") }}</h5>
      <div class="w-100" slot="body">
        <img :src="allData.image" class="rounded img-fluid" loading="lazy" />
      </div>
    </Modal>
  </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("expenses.list.view.page_title") };
  },
  data: () => ({
    breadcrumbsCurrent: "expenses.list.view.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "expenses.list.view.breadcrumbs_first",
        url: "home",
      },
      {
        name: "expenses.list.view.breadcrumbs_second",
        url: "expenses.index",
      },
      {
        name: "expenses.list.view.breadcrumbs_active",
        url: "",
      },
    ],
    showModal: false,
    allData: "",
    catPrefix: "",
    subCatPrefix: "",
  }),
  computed: mapGetters({
    appInfo: "operations/appInfo",
  }),

  created() {
    this.getExpense();
    this.catPrefix = this.appInfo.expCatPrefix;
    this.subCatPrefix = this.appInfo.expSubCatPrefix;
  },
  methods: {
    // get the expense
    async getExpense() {
      const { data } = await axios.get(
        window.location.origin + "/api/expenses/" + this.$route.params.slug
      );
      this.allData = data.data;
    },

    // print
    printWindow() {
      window.print();
    },
  },
};
</script>
