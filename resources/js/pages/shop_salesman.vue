<template>
  <div>
    <div class="card-body position-relative">
      <table-loading v-show="loading" />
      <div class="table-responsive table-custom mt-3" id="printMe">
        <table class="table">
          <thead>
            <tr>
              <th>{{ $t("common.s_no") }}</th>
              <th>{{ $t("common.name") }}</th>
              <th>{{ $t("common.contact_number") }}</th>
              <th class="text-right no-print">
                Action
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- <tr v-show="items.length" v-for="(data, i) in items" :key="i">
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
                {{ data.name }}
              </td>
              <td>{{ data.mobileNumber }}</td>
              <td>
                <div v-if="data.slug" class="btn-group">
                  <button>Change</button>
                </div>
              </td>
            </tr> -->
            <tr>
              <td>1</td>
              <td>Ramesh</td>
              <td>1234567898</td>
              <td> <button>Change</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- <div class="card-footer">
      <div class="dtable-footer"> -->
    <!-- <div class="form-group row display-per-page">
          <label>{{ $t("per_page") }} </label>
          <div>
            <select @change="updatePerPager" v-model="perPage" class="form-control form-control-sm ml-1">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div> -->
    <!-- pagination-start -->
    <!-- <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
          class="justify-flex-end" @paginate="paginate" /> -->
    <!-- pagination-end -->
    <!-- </div>
    </div> -->
  </div>
</template>

<script>


import axios from "axios";
import { mapGetters } from "vuex";


export default {
  middleware: "auth",
  metaInfo() {
    return { title: this.$t("dashboard.page_title") };
  },
  data: () => ({

  }),
};
</script>
