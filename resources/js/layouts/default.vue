<template>
  <div class="wrapper">
    <navbar />
    <sidebar />
    <!-- Main content -->
    <section class="content-wrapper">
      <div class="container-fluid page-padding">
        <child />
      </div>
      <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <!-- <footer class="main-footer no-print">
      <strong v-if="appInfo">{{ appInfo.copyright }}.</strong>
      Developed by
      <a href="https://www.eligocs.com/" target="__blank">EligoCS</a>
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0.0
      </div>
    </footer> -->

    <!-- Control Sidebar -->
    <sidebar-controll />
    <!-- /.control-sidebar -->
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Navbar from "~/components/Navbar";
import Sidebar from "~/components/Sidebar";
import SidebarControll from "~/components/SidebarControll";

if (localStorage.getItem("isRTL") === "true") {
  require("~/rtl-css/acculance-rtl-custom.css");
  require("~/rtl-css/main-rtl-styles.css");
}

export default {
  name: "MainLayout",
  data: () => ({
    year: new Date().getFullYear(),
  }),
  components: {
    Navbar,
    Sidebar,
    SidebarControll,
  },
  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },
  methods: {
    addBodyClass(className) {
      document.body.classList.toggle(className);
    },
  },
};
</script>
