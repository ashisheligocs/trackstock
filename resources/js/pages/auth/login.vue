<template>
  <div class="container-fluid">
    <div class="row no-gutter">
      <!-- The image half -->

      <!-- The content half -->
      <div class="col-12 bg-light">
        <div class="auth-wrapper d-flex align-items-center py-5">
          <!-- Demo content-->
          <div class="container">
            <div class="row">
              <div class="col-lg-10 col-xl-7 mx-auto text-center" style="max-width: 500px;">

                <!-- <img
                  v-if="appInfo"
                  :src="appInfo.blackLogo"
                  :alt="appInfo.companyName"
                  class="lg-logo img-fluid"
                  style="width: 10rem"
                /> -->
                <h2 class="text-muted mb-4 mt-2">Login</h2>
                <form @submit.prevent="login" @keydown="form.onKeydown($event)">
                  <div class="form-group mb-3">
                    <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }"
                      class="form-control rounded-pill border-0 shadow-sm px-4 text-primary" type="text" name="email"
                      placeholder="Enter Username" />
                    <has-error :form="form" field="email" />
                  </div>

                  <div class="form-group mb-3 d-flex">
                    <input :type="shownewPassword ? 'text' : 'password'" v-model="form.password"
                      :class="{ 'is-invalid': form.errors.has('password') }"
                      class="form-control rounded-pill border-0 shadow-sm px-4 text-primary" type="password"
                      name="password" placeholder="Enter Password" />
                    <has-error :form="form" field="password" />
                    <div id="icon" @click="show_new_Password">
                      <i class="fa-solid fa-eye-slash"></i>
                    </div>
                  </div>
                  <!-- Submit Button -->
                  <v-button :loading="form.busy"
                    class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm">
                    <i class="fas fa-sign-in-alt" />
                    <strong>{{ $t("login") }}</strong>
                  </v-button>
                </form>
              </div>
              <!-- Login  Credentials For Demo -->
              <div class="col-12 mt-4">
                <!-- <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <h3 class="text-center font-bold font-up danger-text">
                          Login Credentials
                        </h3>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table
                        class="table table-bordered red-border text-center"
                      >
                        <thead>
                          <tr>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <th scope="row">Super Admin</th>
                            <td>superadmin@acculance.com</td>
                            <td>acculance2022</td>
                            <td scope="row">
                              <button
                                v-tooltip="$t('Login as super admin')"
                                class="btn"
                                @click="
                                  loginCredential(
                                    'superadmin@acculance.com',
                                    'acculance2022'
                                  )
                                "
                              >
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  class="h-6 w-6"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor"
                                  stroke-width="2"
                                >
                                  <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                  />
                                </svg>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row">Manager</th>
                            <td>manager@acculance.com</td>
                            <td>manager2022</td>
                            <td scope="row">
                              <button
                                v-tooltip="$t('Login as super manager')"
                                class="btn"
                                @click="
                                  loginCredential(
                                    'manager@acculance.com',
                                    'manager2022'
                                  )
                                "
                              >
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  class="h-6 w-6"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor"
                                  stroke-width="2"
                                >
                                  <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                  />
                                </svg>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row">Salesman</th>
                            <td>sales@acculance.com</td>
                            <td>sales2022</td>
                            <td scope="row">
                              <button
                                v-tooltip="$t('Login as super salesman')"
                                class="btn"
                                @click="
                                  loginCredential(
                                    'sales@acculance.com',
                                    'sales2022'
                                  )
                                "
                              >
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  class="h-6 w-6"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor"
                                  stroke-width="2"
                                >
                                  <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                  />
                                </svg>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div> -->
                <!-- <div class="w-100 text-center mt-4">
                  <p>
                    {{ $t("TrackHotel v.1.0.0. Developed by") }}
                    <a href="https://www.eligocs.com/" class="font-italic text-muted" target="__blank">
                      <u>EligoCS</u>
                    </a>
                  </p>
                </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End -->
    </div>
  </div>
</template>
<script>
import Form from "vform";
import Cookies from "js-cookie";
import { mapGetters } from "vuex";
import Button from "../../components/Button.vue";
export default {
  components: { Button },
  layout: "basic",
  middleware: "guest",
  metaInfo() {
    return { title: this.$t("login") };
  },
  data: () => ({
    form: new Form({
      email: "",
      password: "",
    }),
    shownewPassword: false,
    remember: false,
    appName: window.config.appName,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
    ...mapGetters({
            user: "auth/user",
        }),
  },
  methods: {
    show_new_Password() {
      this.shownewPassword = !this.shownewPassword;
    },
    async login() {
      // Submit the form.
      const { data } = await this.form.post("/api/login");
      // Save the token.
      this.$store.dispatch("auth/saveToken", {
        token: data.token,
        remember: this.remember,
      });
      // Fetch the user.
        await this.$store.dispatch("auth/fetchUser");
      // Redirect home.
      this.redirect();
    },
    redirect() {
      const intendedUrl = Cookies.get("intended_url");
      if (intendedUrl) {
        Cookies.remove("intended_url");
        this.$router.push({ path: intendedUrl });
      } else {
        this.$router.push({ name: "home" });
      }
    },
    loginCredential(email, pass) {
      this.form.email = email;
      this.form.password = pass;
      this.login();
    },
  },
};
</script>
<style>
#icon {
  position: relative;
  right: 42px;
  top: 5px;
}
</style>
