<template>
  <div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="my-4">
            <div class="">
              <div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4">

                  <div class="small-box mb-0">
                    <div class="inner p-4 text-center">
                      <h3>
                        Shop Balance
                      </h3>
                    </div>
                  </div>

                </div>

              </div>
              <div class="row justify-content-center">
                <div class="col-md-3 col-6 mb-4">
                  <div class="small-box mb-0">
                    <div class="inner p-4 text-center">
                      <h3>
                        QR
                      </h3>
                      <span>20,000</span>
                    </div>
                  </div>
                </div>

                <div class="col-md-3 col-6 mb-4">
                  <div class="small-box mb-0">
                    <div class="inner p-4 text-center">
                      <h3>
                        Cash
                      </h3>
                      <span>50,000</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row justify-content-center">
                <button class="btn btn-primary">Collect Cash</button>
              </div>

              <v-modal>
                <h4>Receipt Number</h4>
                <tabel>
                  <tr>
                    <td>
                      Incharge Name
                    </td>
                    <td>
                      Krishna
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Date
                    </td>
                    <td>
                      21-03-2024
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Time
                    </td>
                    <td>
                      3:00 pm
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Shop No.
                    </td>
                    <td>
                      5
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Cash Received
                    </td>
                    <td>
                      5000
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                     Remarks
                    </td>

                  </tr>
                  <tr>
                    <td colspan="2">
                      <textarea>
                        Remarks
                      </textarea>
                    </td>
                  </tr>

                  <tr>
                    <td colspan="2">
                     <div>
                      
                     </div>
                    </td>
                  </tr>
                </tabel>
              </v-modal>
            </div>
          </div>
        </div>
      </div>
    </div>
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
