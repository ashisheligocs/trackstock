<template>
  <aside ref="asideElement" class="control-sidebar control-sidebar-dark px-3 py-4 shadow-lg">
    <!-- Control sidebar content goes here -->
    <div class="d-flex justify-content-between sidebarc-top border-bottom">
      <h5>{{ $t("sidebar_control.customize_acculance") }}</h5>
      <button @click="closeSideBarControl" class="btn btn-danger close-sidebar-btn">x</button>
    </div>

    <div class="sidebarc-body pb-0">
      <!-- <div>
        <div class="custom-control custom-checkbox">
          <input class="custom-control-input" type="checkbox" id="dark-modee" value="1" :checked="isDark ? true : false"
            @click="addBodyClass('isDark', 'dark-mode')" />
          <label for="dark-modee" class="custom-control-label">{{
        $t("sidebar_control.dark_mode")
      }}</label>
        </div>
      </div> -->
      <!-- Dark Mode -->

      <!-- Sidebar Menu -->
      <nav class="py-3  extra_sidebar" style="background: transparent !important;">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
          style="color: white !important;">

          <!-----------------------------Product------------------------>
          <li v-if="$can('product-category-create') ||
        $can('product-category-edit') ||
        $can('product-category-delete') ||
        $can('product-sub-category-create') ||
        $can('product-sub-category-edit') ||
        $can('product-sub-category-delete') ||
        $can('product-create') ||
        $can('product-view') ||
        $can('product-edit') ||
        $can('product-delete') ||
        $can('print-barcode')
        " class="nav-item has-treeview" :class="menuOpen('productCats') ||
        menuOpen('productSubCats') ||
        menuOpen('products') ||
        menuOpen('barcode')
        ? 'menu-is-opening menu-open'
        : ''
        ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-boxes" />
              <p>
                {{ $t("sidebar.products") }}
                <i class="fas fa-angle-left right" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="menuOpen('productCats') ||
        menuOpen('productSubCats') ||
        menuOpen('products') ||
        menuOpen('barcode')">
              <li v-if="$can('product-sub-category-create') ||
        $can('product-sub-category-edit') ||
        $can('product-sub-category-delete')
        " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'productSubCats.index' }"
                  class="nav-link">
                  <i class="fas fa-code-branch nav-icon" />
                  <p>Liquor Type</p>
                </router-link>
              </li>
              <li v-if="$can('product-create') ||
        $can('product-view') ||
        $can('product-edit') ||
        $can('product-delete')
        " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'products.index' }" class="nav-link">
                  <i class="fas fa-list-ul nav-icon" />
                  <p>{{ $t("sidebar.products_list") }}</p>
                </router-link>
              </li>
            </ul>
          </li>

          <li v-if="$can('employee-list')
        " class="nav-item">
            <router-link :to="{ name: 'employees.index' }" class="nav-link">
              <i class="fas fa-list-ul nav-icon" />
              <p>{{ $t("sidebar.employees_list") }}</p>
            </router-link>
          </li>
          <!-----------------------------Shop---------------------------------->
          <li v-if="$can('shop-create') ||
        $can('shop-list') ||
        $can('shop-edit')
        " class="nav-item has-treeview">
                <router-link :to="{ name: 'shops' }" class="nav-link">
                  <i class="fas fa-hotel nav-icon" />
                  <p>Shops</p>
                </router-link>
          </li>

          <!--------------------------Resource---------------------------------->
          <!-- <li v-if="$can('role-permissions') ||
        $can('units') ||
        $can('currencies') ||
        $can('general-settings')" class="nav-header text-bold d-none">{{ $t("sidebar.resources") }}</li>
          <li v-if="$can('role-permissions') ||
        $can('units') ||
        $can('currencies') ||
        $can('general-settings')
        " class="nav-item">
            <router-link :to="{ name: 'setup.general' }" class="nav-link">
              <i class="nav-icon fas fa-cogs" />
              <p>{{ $t("sidebar.setup") }}</p>
            </router-link>
          </li> -->
          <li>
            <SettingsSidebar />
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script>
export default {
  name: "sidebar-controll",
  data: () => ({
    isDark: false,
    isRTL: false,
    isBorderBtm: false,
    isNavFixed: false,
    isSidebarCollasped: false,
    isLayoutFixed: false,
    isSidebarMini: false,
    isNavFlat: false,
    isSidebarDark: false,
    isNavChildIndent: false,
    isNavLegacy: false,
    isDisableHoverExpand: false,
    clickCount: 0,
  }),
  created() {
    document.addEventListener('click', this.handleClickOutside);
  },
  mounted() {
    this.getLocalStorageData();
    // document.addEventListener('click', function(event) {
    //     var aside = document.querySelector('aside');
    //     console.log('as',aside);
    //     console.log('et',event.target);
    //     var isClickInsideAside = aside.contains(event.target);

    //     // Check if the click occurred inside or outside of the aside element
    //     if (!isClickInsideAside) {
    //         // Click occurred outside of the aside element
    //         console.log('Click is outside of <aside>');
    //     } else {
    //       console.log('Click is inside of <aside>');
    //     }
    // });

  },
  destroyed() {
    // Remove the click event listener when the component is destroyed
    document.removeEventListener('click', this.handleClickOutside);
  },


  methods: {
    handleClickOutside(event) {

      if (!this.$refs.asideElement.contains(event.target)) {

        if (this.clickCount > 0) {
          let classExist = document.body.classList.contains('control-sidebar-slide-open');
          if (classExist) {
            document.body.classList.remove("control-sidebar-slide-open");
            this.clickCount = 0;
            return false;
          }
        }
        this.clickCount++;
      }
    },

    menuOpen(routeName) {
      if (this.$route.name) {
        return this.$route.name.indexOf(routeName) > -1 ? true : false;
      }
      return false;
    },
    rtlHandler() {
      localStorage.setItem("isRTL", !this.isRTL);
      this.isRTL = !this.isRTL;
      window.location.reload();
    },
    // ADD OR REMOVE VALUE TO LOCALSTORAGE AND SET OR REMOVE IN BODY CLASS
    addBodyClass(varVal, className) {
      this[varVal] = !this[varVal];
      if (this[varVal]) {
        localStorage[varVal] = true;
        document.body.classList.add(className);
      } else {
        localStorage.removeItem(varVal);
        document.body.classList.remove(className);
      }
    },

    // ADD OR REMOVE CLASS TO MAIN-SIDEBAR
    addSidebarClass(varVal, className) {
      this[varVal] = !this[varVal];

      if (this[varVal]) {
        localStorage[varVal] = true;
        var data = document.querySelector(".main-sidebar");
        data.classList.add(className);
      } else {
        localStorage.removeItem(varVal);
        var data = document.querySelector(".main-sidebar");
        data.classList.remove(className);
      }
    },

    // ADD CLASS TO SIDEBAR NAV
    addNavSidebarClass(varVal, className) {
      this[varVal] = !this[varVal];

      if (this[varVal]) {
        localStorage[varVal] = true;
        var data = document.querySelector(".nav-sidebar");
        data.classList.add(className);
      } else {
        localStorage.removeItem(varVal);
        var data = document.querySelector(".nav-sidebar");
        data.classList.remove(className);
      }
    },

    // Dark mode check from localstorage
    getLocalStorageData() {
      // Check if isDark true in localstorage
      if (localStorage.isDark) {
        document.body.classList.add("dark-mode");
        this.isDark = true;
      }
      if (localStorage.getItem("isRTL") === "true") {
        this.isRTL = true;
      }

      // Check if isDark true in localstorage
      if (localStorage.isBorderBtm) {
        document.body.classList.add("border-bottom-0");
        this.isBorderBtm = true;
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isNavFixed) {
        document.body.classList.add("layout-navbar-fixed");
        this.isNavFixed = true;
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isSidebarCollasped) {
        document.body.classList.add("sidebar-collapse");
        this.isSidebarCollasped = true;
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isLayoutFixed) {
        document.body.classList.add("layout-fixed");
        this.isLayoutFixed = true;
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isSidebarMini) {
        document.body.classList.add("sidebar-mini");
        this.isSidebarMini = true;
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isNavFlat) {
        this.isNavFlat = true;
        var navSidebar = document.querySelector(".nav-sidebar");
        navSidebar.classList.add("nav-flat");
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isSidebarDark) {
        this.isSidebarDark = true;
        var sideBarDark = document.querySelector(".main-sidebar");
        sideBarDark.classList.add("dark-sidebar");
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isNavChildIndent) {
        this.isNavChildIndent = true;
        var navChildIndent = document.querySelector(".nav-sidebar");
        navChildIndent.classList.add("nav-child-indent");
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isNavLegacy) {
        this.isNavLegacy = true;
        var navLegacy = document.querySelector(".nav-sidebar");
        navLegacy.classList.add("nav-legacy");
      }

      // Check if headerFixed true in localstorage
      if (localStorage.addSidebarClass) {
        this.addSidebarClass = true;
        var sidebarClass = document.querySelector(".main-sidebar");
        sidebarClass.classList.add("sidebar-no-expand");
      }
    },

    closeSideBarControl() {
      document.body.classList.toggle("control-sidebar-slide-open");
    },

  },
};
</script>

<style>
.extra_sidebar .nav-link {
  padding: 0.5rem 0;
}
</style>
