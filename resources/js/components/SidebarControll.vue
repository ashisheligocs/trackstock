<template>
  <aside ref="asideElement" class="control-sidebar control-sidebar-dark px-3 py-4 shadow-lg">
    <!-- Control sidebar content goes here -->
    <div class="d-flex justify-content-between sidebarc-top border-bottom">
      <h5>{{ $t("sidebar_control.customize_acculance") }}</h5>
      <button @click="closeSideBarControl" class="btn btn-danger close-sidebar-btn">x</button>
    </div>

    <div class="sidebarc-body pb-0">
      <div>
        <div class="custom-control custom-checkbox">
          <input class="custom-control-input" type="checkbox" id="dark-modee" value="1" :checked="isDark ? true : false"
            @click="addBodyClass('isDark', 'dark-mode')" />
          <label for="dark-modee" class="custom-control-label">{{
            $t("sidebar_control.dark_mode")
          }}</label>
        </div>
      </div>
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
              <li v-if="$can('product-category-create') ||
                $can('product-category-edit') ||
                $can('product-category-delete')
                " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'productCats.index' }" class="nav-link">
                  <i class="fas fa-tags nav-icon" />
                  <p>{{ $t("sidebar.categories") }}</p>
                </router-link>
              </li>
              <li v-if="$can('product-sub-category-create') ||
                $can('product-sub-category-edit') ||
                $can('product-sub-category-delete')
                " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'productSubCats.index' }" class="nav-link">
                  <i class="fas fa-code-branch nav-icon" />
                  <p>{{ $t("sidebar.sub_categories") }}</p>
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

<li class="nav-item">
                <router-link :to="{ name: 'barcode.print' }" class="nav-link">
                  <i class="fas fa-barcode nav-icon" />
                  <p>Barcode</p>
                </router-link>
              </li>

          <!-----------------------------Expenses------------------------>
          <li v-if="$can('expense-category-list') ||
            $can('expense-category-create') ||
            $can('expense-category-edit') ||
            $can('expense-category-delete') ||
            $can('expense-sub-category-list') ||
            $can('expense-sub-category-create') ||
            $can('expense-sub-category-edit') ||
            $can('expense-sub-category-delete') ||
            $can('expense-list') ||
            $can('expense-create') ||
            $can('expense-edit') ||
            $can('expense-view') ||
            $can('expense-delete')
            " class="nav-item has-treeview" :class="menuOpen('expenseCats') ||
    menuOpen('expenseSubCats') ||
    menuOpen('expenses')
    ? 'menu-is-opening menu-open'
    : ''
    ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-calculator" />
              <p>
                {{ $t("sidebar.expenses") }}
                <i class="fas fa-angle-left right" />
              </p>
            </a>
            <ul class="nav nav-treeview" :class="menuOpen('expenseCats') ||
              menuOpen('expenseSubCats') ||
              menuOpen('expenses')
              ? 'display: block'
              : 'display: none'
              ">
              <li v-if="$can('expense-category-list') ||
                $can('expense-category-create') ||
                $can('expense-category-edit') ||
                $can('expense-category-delete')
                " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'expenseCats.index' }" class="nav-link">
                  <i class="fas fa-tags nav-icon" />
                  <p>{{ $t("sidebar.categories") }}</p>
                </router-link>
              </li>
              <li v-if="$can('expense-sub-category-list') ||
                $can('expense-sub-category-create') ||
                $can('expense-sub-category-edit') ||
                $can('expense-sub-category-delete')
                " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'expenseSubCats.index' }" class="nav-link">
                  <i class="fas fa-code-branch nav-icon" />
                  <p>{{ $t("sidebar.sub_categories") }}</p>
                </router-link>
              </li>
              <li v-if="$can('expense-list') ||
                $can('expense-create') ||
                $can('expense-edit') ||
                $can('expense-view') ||
                $can('expense-delete')
                " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'expenses.index' }" class="nav-link">
                  <i class="fas fa-list-ul nav-icon" />
                  <p>{{ $t("sidebar.expenses_list") }}</p>
                </router-link>
              </li>
            </ul>
          </li>

          <!-----------------------------Shop---------------------------------->
          <li v-if="$can('shop-create') ||
            $can('shop-list') ||
            $can('shop-edit') ||
            $can('shop-delete')
            " class="nav-item has-treeview" :class="menuOpen('shops') ? 'menu-is-opening menu-open' : ''">

            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-hotel" />
              <p>
                {{ $t('shops') }}
                <i class="right fas fa-angle-left" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="menuOpen('shops') ? 'display: block' : 'display: none'">
              <li class="nav-item">
                <router-link :to="{ name: 'shops' }" class="nav-link">
                  <i class="fas fa-hotel nav-icon" />
                  <p>{{ $t('shops') }}</p>
                </router-link>
              </li>
            </ul>
          </li>

          <!-----------------------------restaurant------------------------>

          <li v-if="$can('restaurant-create') ||
            $can('restaurant-list') ||
            $can('restaurant-edit') ||
            $can('restaurant-delete') ||
            $can('restaurant-order')
            " class="nav-item has-treeview" :class="menuOpen('restaurant') || menuOpen('varient')
    || menuOpen('food-category') || menuOpen('items')
    ? 'menu-is-opening menu-open'
    : ''">

            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-hotel" />
              <p>
                Restaurant Setting
                <i class="right fas fa-angle-left" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="menuOpen('restaurant') || menuOpen('varient')
              || menuOpen('food-category') || menuOpen('items')
              ? 'display: block'
              : 'display: none'
              ">
              <li class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'restaurant' }" class="nav-link">
                  <i class='nav-icon fab fa-resolving'></i>
                  <p>{{ $t('sidebar.restaurant') }}</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'varient' }" class="nav-link">
                  <i class="nav-icon fa fa-solid fa-bars" />
                  <p>{{ $t('sidebar.verient') }}</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'food-category' }" class="nav-link">
                  <i class="nav-icon fa fa-exclamation-circle" />
                  <p>{{ $t('sidebar.foodCategory') }}</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'items' }" class="nav-link">
                  <i class='nav-icon fas fa-hamburger'></i>
                  <p>{{ $t('sidebar.foodItem') }}</p>
                </router-link>
              </li>
            </ul>
          </li>

          <!-----------------------------Customer------------------------>
          <li class="nav-item has-treeview" :class="menuOpen('restaurant') || menuOpen('varient')
            || menuOpen('food-category') || menuOpen('items')
            ? 'menu-is-opening menu-open'
            : ''">

            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-circle" />
              <p>
                Customer
                <i class="right fas fa-angle-left" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="menuOpen('restaurant') || menuOpen('varient')
              || menuOpen('food-category') || menuOpen('items')
              ? 'display: block'
              : 'display: none'
              ">
              <!-- <li v-if="$can('non-invoice-payment-list') ||
                $can('non-invoice-payment-create') ||
                $can('non-invoice-payment-edit') ||
                $can('non-invoice-payment-view') ||
                $can('non-invoice-payment-delete')
                " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'nonInvoicePayments.index' }"
                  class="nav-link">
                  <i class="fas fa-file-alt nav-icon" />
                  <p>{{ $t("sidebar.non_invoice") }}</p>
                </router-link>
              </li> -->
              <li v-if="$can('invoice-return-list') ||
                $can('invoice-return-create') ||
                $can('invoice-return-view') ||
                $can('invoice-return-edit') ||
                $can('invoice-return-delete')
                " class="nav-item">
                <router-link @click.native="closeSideBarControl" :to="{ name: 'invoiceReturns.index' }" class="nav-link">
                  <i class="fas fa-undo-alt nav-icon" />
                  <p>{{ $t("sidebar.returns_list") }}</p>
                </router-link>
              </li>
            </ul>
          </li>
          <li v-if="$can('coupon-edit') || $can('coupon-create') || $can('coupon-list') || $can('coupon-delete')"
            class="nav-item">
            <router-link :to="{ name: 'coupon.index' }" class="nav-link">
              <i class="fas fa-tag nav-icon" />
              <p>{{ $t("sidebar.coupon_list") }}</p>
            </router-link>
          </li>

          <li class="nav-item" v-if="$can('account-list') || $can('ledger-group-create') ||
            $can('account-create') || $can('ledger-group-list') ||
            $can('account-view') || $can('ledger-group-edit') ||
            $can('account-edit') || $can('ledger-group-delete') ||
            $can('account-delete')">
            <router-link :to="{ name: 'ledger-group' }" class="nav-link">
              <i class="fas fa-truck-loading nav-icon" />
              <p>{{ $t('sidebar.ledger_group') }}</p>
            </router-link>
          </li>
          <!--------------------------Resource---------------------------------->
          <li v-if="$can('role-permissions') ||
            $can('units') ||
            $can('currencies') ||
            $can('general-settings')" class="nav-header text-bold">{{ $t("sidebar.resources") }}</li>
          <li v-if="$can('role-permissions') ||
            $can('units') ||
            $can('currencies') ||
            $can('general-settings')
            " class="nav-item">
            <router-link :to="{ name: 'setup.general' }" class="nav-link">
              <i class="nav-icon fas fa-cogs" />
              <p>{{ $t("sidebar.setup") }}</p>
            </router-link>
          </li>
          <li class="nav-item">
            <a @click="executeAction('optimize:clear')" class="nav-link cursor-pointer" href="#">
              <i class="nav-icon fas fa-trash" />
              <span>{{ $t("sidebar.clear_cache") }}</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>

    <div>
      <!-- <h6>{{ $t("sidebar_control.header_options") }}</h6>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="layout-navbar-fixed" value="1"
          :checked="isNavFixed ? true : false" @click="addBodyClass('isNavFixed', 'layout-navbar-fixed')" />
        <label for="layout-navbar-fixed" class="custom-control-label">{{
          $t("sidebar_control.fixed")
        }}</label>
      </div> -->
      <!--          <input class="custom-control-input" type="checkbox" id="border-bottom-0" value="1"-->
      <div class="custom-control custom-checkbox mb-4">
        <!--            :checked="isBorderBtm ? true : false" @click="addBodyClass('isBorderBtm', 'border-bottom-0')" />-->
        <!--          <label for="border-bottom-0" class="custom-control-label">{{-->
        <!--            $t("sidebar_control.no_border")-->
        <!--          }}</label>-->
        <!--        </div>-->
        <!--      </div>-->


        <!-- &lt;!&ndash; Header-options &ndash;&gt; -->
        <!-- <h6>{{ $t("sidebar_control.sidebar_options") }}</h6> -->
        <div>
          <!-- <div class="custom-control custom-checkbox mb-1">
            <input class="custom-control-input" type="checkbox" id="sidebar-dark" value="1"
              :checked="isSidebarDark ? true : false" @click="addSidebarClass('isSidebarDark', 'dark-sidebar')" />
            <label for="sidebar-dark" class="custom-control-label">{{
              $t("sidebar_control.sidebar_dark")
            }}</label>
          </div> -->
          <!-- <div class="custom-control custom-checkbox mb-1">
            <input class="custom-control-input" type="checkbox" id="collapsed" value="1"
              :checked="isSidebarCollasped ? true : false"
              @click="addBodyClass('isSidebarCollasped', 'sidebar-collapse')" />
            <label for="collapsed" class="custom-control-label">{{
              $t("sidebar_control.collapsed")
            }}</label>
          </div> -->
          <div class="custom-control custom-checkbox mb-1" style="display:none">
            <input class="custom-control-input" type="checkbox" id="layout-fixed" value="1"
              :checked="isLayoutFixed ? true : false" @click="addBodyClass('isLayoutFixed', 'layout-fixed')" />
            <label for="layout-fixed" class="custom-control-label">{{
              $t("sidebar_control.sidebar_fixed")
            }}</label>
          </div>
          <!-- <div class="custom-control custom-checkbox mb-1">
            <input class="custom-control-input" type="checkbox" id="sidebar-mini" value="1"
              :checked="isSidebarMini ? true : false" @click="addBodyClass('isSidebarMini', 'sidebar-mini')" />
            <label for="sidebar-mini" class="custom-control-label">{{
              $t("sidebar_control.sidebar_mini")
            }}</label>
          </div> -->
          <!-- <div class="custom-control custom-checkbox mb-1">
            <input class="custom-control-input" type="checkbox" id="nav-flat" value="1"
              :checked="isNavFlat ? true : false" @click="addNavSidebarClass('isNavFlat', 'nav-flat')" />
            <label for="nav-flat" class="custom-control-label">{{
              $t("sidebar_control.nav_flat_style")
            }}</label>
          </div> -->
          <!-- <div class="custom-control custom-checkbox mb-1">
            <input class="custom-control-input" type="checkbox" id="nav-legacy" value="1"
              :checked="isNavLegacy ? true : false" @click="addNavSidebarClass('isNavLegacy', 'nav-legacy')" />
            <label for="nav-legacy" class="custom-control-label">{{
              $t("sidebar_control.nav_legacy_style")
            }}</label>
          </div> -->
          <!-- <div class="custom-control custom-checkbox mb-1">
            <input class="custom-control-input" type="checkbox" id="nav-child-indent" value="1"
              :checked="isNavChildIndent ? true : false"
              @click="addNavSidebarClass('isNavChildIndent', 'nav-child-indent')" />
            <label for="nav-child-indent" class="custom-control-label">{{
              $t("sidebar_control.nav_child_indent")
            }}</label>
          </div> -->
          <!-- <div class="custom-control custom-checkbox mb-4">
            <input class="custom-control-input" type="checkbox" id="disableHoverExpand" value="1"
              :checked="isDisableHoverExpand ? true : false"
              @click="addSidebarClass('isDisableHoverExpand', 'sidebar-no-expand')" />
            <label for="disableHoverExpand" class="custom-control-label">{{
              $t("sidebar_control.disable_hover")
            }}</label>
          </div> -->
        </div>
        <!-- Header-options -->
      </div>
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
