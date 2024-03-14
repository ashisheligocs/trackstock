<template>
    <div class="mb-50">
      <!-- breadcrumbs Start -->
      <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
      <!-- breadcrumbs end -->
      <div class="row">
        <div class="col-lg-12">
        <div class="card custom-card w-100">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("cashbook.journal_entry.index.page_title") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a
                  @click="refreshTable()"
                  href="#"
                  v-tooltip="'Refresh'"
                  class="btn btn-success"
                >
                  <i class="fas fa-sync"></i>
                </a>
                <a
                  @click="print"
                  v-tooltip="$t('common.print_table')"
                  class="btn btn-info"
                >
                  <i class="fas fa-print"></i>
                </a>
                <router-link
                  v-if="$can('journal-entry-create')"
                  :to="{ name: 'journalEntry.create' }"
                  class="btn btn-primary"
                >
                  {{ $t("cashbook.journal_entry.index.add_journal_entry") }}
                  <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                </router-link>
              </div>
            </div>
          </div>
             <!-- /.card-header -->
      <div class="card-body position-relative">
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search
                  v-model="query"
                  @reset-pagination="resetPagination()"
                  @reload="reload"
                />
              </div>
            </div>
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.date") }}</th>
                    <th>{{ $t("common.note") }}</th>
                    <th>{{ $t("common.amount") }}</th>
                    <th class="text-right no-print">{{ $t("common.action") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                    <td>
                      <span v-if="pagination && pagination.current_page > 1">
                        {{
                          pagination.per_page * (pagination.current_page - 1) +
                          (i + 1)
                        }}
                      </span>
                      <span v-else>{{ i + 1 }}</span>
                    </td>
                    <td>
                      <span v-if="data.date">{{
                        data.date | moment("Do MMM, YYYY")
                      }}</span>
                    </td>
                    <td>{{ data.note }}</td>
                    <td>{{ data.amount | withCurrency }}</td>
                    <td class="text-right no-print">
                      <div class="btn-group" v-if="$can('journal-entry-edit') || $can('journal-entry-delete')">
                        <router-link v-tooltip="$t('common.edit')" v-if="$can('journal-entry-edit')"
                          :to="{
                            name: 'journalEntry.edit',
                            params: { slug: data.id },
                          }"
                          class="btn btn-info btn-sm"
                        >
                          <i class="fas fa-edit" />
                        </router-link>
                        <a v-tooltip="$t('common.delete')" v-if="$can('journal-entry-delete')"
                          href="#"
                          class="btn btn-danger btn-sm"
                          @click="deleteData(data.id)"
                        >
                          <i class="fas fa-trash" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="8">
                      <EmptyTable />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer">
            <div class="dtable-footer p-0">
              <div class="form-group row display-per-page mb-0">
                <label>{{ $t("per_page") }} </label>
                <div>
                  <select
                    @change="updatePerPager"
                    v-model="perPage"
                    class="form-control form-control-sm ml-1"
                  >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </div>
              </div>
              <!-- pagination-start -->
              <pagination
                v-if="pagination && pagination.last_page > 1"
                :pagination="pagination"
                :offset="5"
                class="justify-flex-end"
                @paginate="paginate"
              />
              <!-- pagination-end -->
            </div>
          </div>
          </div>
          </div>
      </div>

   
    </div>
  </template>
  
  <script>
  import moment from "moment";
  import { mapGetters } from "vuex";
  
  export default {
    // middleware: ["auth", "check-permissions"],
    metaInfo() {
      return { title: this.$t("cashbook.journal_entry.index.page_title") };
    },
    data: () => ({
      breadcrumbsCurrent: "cashbook.journal_entry.index.breadcrumbs_current",
      breadcrumbs: [
        {
          name: "cashbook.journal_entry.index.breadcrumbs_first",
          url: "home",
        },
        {
          name: "cashbook.journal_entry.index.breadcrumbs_second",
          url: "",
        },
        {
          name: "cashbook.journal_entry.index.breadcrumbs_active",
          url: "",
        },
      ],
      query: "",
      perPage: 10,
      currentHotel:'',
    }),
    
    // Map Getters
    computed: {
      ...mapGetters("operations", ["items","loading", "pagination", "selectedHotel", "hotelItems"]),
    },

    async created() {
        // await this.getLedgerAccount();
      await this.getJournalEntryData();
      await this.getHotelDataList();
    if (this.selectedHotel && this.selectedHotel !== 'all') {
        this.hotelItems.forEach((hotel) => {
            if (hotel.id == this.selectedHotel) {
              this.currentHotel = hotel;
            }
        })
    }
  },

  watch:{
    query: function (newQ, oldQ) {
        if (newQ === "") {
          this.getData();
        } else {
          this.searchData();
        }
      },
      selectedHotel(newData){
        this.getJournalEntryData();
          if (newData && newData !== 'all') {
              this.hotelItems.forEach((hotel) => {
                  if (hotel.id == newData) this.currentHotel = hotel
              })
          }
      }
    },
    methods:{
      
      async getHotelDataList () {
        await this.$store.dispatch('operations/getHotelData', {
          path: '/api/hotel',
        });
      },

      async getJournalEntryData () {
        await this.$store.dispatch('operations/allData', {
          path: '/api/account/journal/get-journal',
        });
      },

      // update per page count
      updatePerPager() {
        this.pagination.current_page = 1;
        this.query === "" ? this.getData() : this.searchData();
      },

      // Pagination
      async paginate(page) {
        this.pagination.current_page = page;
        this.query === "" ? this.getData() : this.searchData();
      },

      // Reset pagination
      async resetPagination() {
        this.pagination.current_page = 1;
      },

      async reload() {
        this.query = "";
       // await this.searchData();
      },

      // print table
      async print() {
        await this.$htmlToPaper("printMe");
      },

      refreshTable() {
        this.query = "";
        // this.dateRange.startDate = null;
        // this.dateRange.endDate = null;

        // this.query === "" ? this.getData() : this.searchData();

        setTimeout(
          function () {
            this.dateRange.startDate = "";
            this.dateRange.endDate = "";
          }.bind(this),
          500
        );
      },

      // delete journal data

      async deleteData(id) {
        Swal.fire({
          title: this.$t("common.delete_title"),
          text: this.$t("cashbook.journal_entry.index.delete_warning"),
          type: "warning",
          showCancelButton: true,
          confirmButtonText: this.$t("common.delete_confirm_text"),
        }).then((result) => {
          // Send request to the server
          if (result.value) {
            this.$store
              .dispatch("operations/deleteData", {
                path: "/api/account/journal/delete/",
                slug: id,
              })
              .then((response) => {
                if (response === true) {
                  this.getJournalEntryData();
                  Swal.fire(
                    this.$t("common.deleted"),
                    this.$t("common.delete_success"),
                    "success"
                  );
                } else {
                  Swal.fire(
                    this.$t("common.failed"),
                    this.$t("cashbook.journal_entry.index.delete_failed"),
                    "warning"
                  );
                }
              });
          }
        });
      },

    }
  };
  </script>
  