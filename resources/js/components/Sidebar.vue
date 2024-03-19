<template>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar">
    <!-- Brand Logo -->
    <router-link :to="{ name: 'home' }" class="brand-link mx-auto text-center">
      <img v-if="appInfo" :src="appInfo.blackLogo" :alt="appInfo.companyName" class="lg-logo light-logo"
        style="height: 3.5rem" />
    </router-link>

    <!-- Sidebar -->
    <div class="sidebar custom-sidebar">
      <!-- Sidebar Menu -->
      <nav class="py-3 pb-5">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">





          <!-----------dashboard-------------->
          <li class="nav-header text-uppercase text-bold">
            {{ $t("sidebar.dashboard") }}
          </li>
          <li class="nav-item">
            <router-link :to="{ name: 'home' }" class="nav-link">
              <i class="nav-icon fas fa-home" />
              <p>{{ $t("sidebar.dashboard") }}</p>
            </router-link>
          </li>
          <!-------------Hotel----------------->
          <li class="nav-header text-uppercase text-bold" v-if="$can('shops-create') ||
      $can('shops-list') ||
      $can('shops-edit') ||
      $can('shops-delete')
      ">{{ $t('sidebar.shops') }}</li>
          <!-- <li class="nav-item">
            <router-link :to="{ name: 'hotel' }" class="nav-link">
              <i class="fas fa-hotel nav-icon" />
              <p>{{ $t('sidebar.hotel') }}</p>
            </router-link>
          </li> -->
          <!-- <li class="nav-item" v-if="$can('hotel-booking-list')">
            <router-link :to="{ name: 'booking' }" class="nav-link">
              <i class="nav-icon fas fa-solid fa-address-book" />
              <p>{{ $t('sidebar.booking') }}</p>
            </router-link>
          </li>
          <li class="nav-item" v-if="$can('hotel-booking-list')">
            <router-link :to="{ name: 'past-bookings', params: { slug: 'past' } }" class="nav-link">
              <i class="nav-icon fas fa-solid fa-book" />
              <p>Past Booking</p>
            </router-link>
          </li> -->
          <li class="nav-item" v-if="$can('shops-create')">
            <router-link :to="{ name: 'shops' }" class="nav-link">
              <i class="fas fa-hotel nav-icon" />
              <p>{{ $t('sidebar.shops') }}</p>
            </router-link>
          </li>
          <!-------------sales-------------->
          <li class="nav-header text-bold" v-if="$can('shops-create')">SALES</li>
          <li v-if="$can('client-list') ||
      $can('client-create') ||
      $can('client-view') ||
      $can('client-edit') ||
      $can('client-delete')
      " class="nav-item">
            <router-link :to="{ name: 'shops' }" class="nav-link">
              <i class="nav-icon fas fa-users" />
              <p>Shops</p>
            </router-link>
          </li>
          <!-- <li v-if="$can('invoice-list') ||
      $can('invoice-create') ||
      $can('invoice-view') ||
      $can('invoice-edit') ||
      $can('invoice-delete')
      " class="nav-item">
            <router-link :to="{ name: 'invoices.index' }" class="nav-link">
              <i class="fas fa-file-invoice nav-icon" />
              <p>{{ $t("sidebar.invoices_list") }}</p>
            </router-link>
          </li> -->

          <!-- Invoice Payment Route -->
          <!-- <li v-if="$can('invoice-payment-list') ||
      $can('invoice-payment-create') ||
      $can('invoice-payment-view') ||
      $can('invoice-payment-edit')
      " class="nav-item">
            <router-link :to="{ name: 'invoicePayments.index' }" class="nav-link">
              <i class="fas fa-money-check-alt nav-icon" />
              <p>{{ $t("sidebar.invoices_payment_list") }}</p>
            </router-link>
          </li> -->

          <!-- <li v-if="$can('coupon-edit') || $can('coupon-create') || $can('coupon-list') || $can('coupon-delete')" class="nav-item">
            <router-link :to="{ name: 'coupon.index' }" class="nav-link">
              <i class="fas fa-tag nav-icon" />
              <p>{{ $t("sidebar.coupon_list") }}</p>
            </router-link>
          </li> -->
          <!--------------------------Accounting---------------------------------->
          <li class="nav-header text-bold">{{ $t("sidebar.accounting") }}</li>

          <!-- <li class="nav-item"
          v-if="$can('account-list') || $can('ledger-group-create') ||
            $can('account-create') || $can('ledger-group-list') ||
            $can('account-view') || $can('ledger-group-edit') ||
            $can('account-edit') || $can('ledger-group-delete') ||
            $can('account-delete')">
            <router-link :to="{ name: 'ledger-group' }" class="nav-link">
              <i class="fas fa-truck-loading nav-icon" />
              <p>{{ $t('sidebar.ledger_group') }}</p>
            </router-link>
          </li> -->
          <li class="nav-item" v-if="$can('account-list') || $can('ledger-account-create') ||
      $can('account-create') || $can('ledger-account-list') ||
      $can('account-view') || $can('ledger-account-edit') || $can('ledger-account-delete') ||
      $can('account-edit') || $can('ledger-account-transaction') ||
      $can('account-delete')">
            <router-link :to="{ name: 'ledger-account' }" class="nav-link">
              <i class="fas fa-truck-loading nav-icon" />
              <p>{{ $t('sidebar.ledger_account') }}</p>
            </router-link>
          </li>
          <li v-if="$can('account-transfer-balance-list') ||
      $can('account-transfer-balance-create') ||
      $can('account-transfer-balance-edit') ||
      $can('account-transfer-balance-view') ||
      $can('account-transfer-balance-delete')
      " class="nav-item">
            <router-link :to="{ name: 'transferBalances.index' }" class="nav-link">
              <i class="fas fa-exchange-alt nav-icon" />
              <p>{{ $t("sidebar.balance_transfers") }}</p>
            </router-link>
          </li>
          <li v-if="$can('transaction-history')" class="nav-item">
            <router-link :to="{ name: 'transactions.index' }" class="nav-link">
              <i class="fas fa-history nav-icon" />
              <p>{{ $t("sidebar.transaction_history") }}</p>
            </router-link>
          </li>
          <li class="nav-item" v-if="$can('journal-entry-list')">
            <router-link :to="{ name: 'journalEntry.index' }" class="nav-link">
              <i class="fas fa-book nav-icon" />
              <p>{{ $t("sidebar.journal_entry") }}</p>
            </router-link>
          </li>

          <!--------------------------Inventory---------------------------------->
          <li v-if="$can('adjustment-create') ||
      $can('adjustment-view') ||
      $can('adjustment-edit') ||
      $can('adjustment-delete')
      " class="nav-header text-bold">STOCK</li>
          <!--          <li-->
          <!--            v-if="-->
          <!--              $can('inventory') ||-->
          <!--              $can('adjustment-create') ||-->
          <!--              $can('adjustment-view') ||-->
          <!--              $can('adjustment-edit') ||-->
          <!--              $can('adjustment-delete')-->
          <!--            "-->
          <!--            class="nav-item has-treeview"-->
          <!--            :class="-->
          <!--              menuOpen('inventory') ||-->
          <!--              menuOpen('adjustments') ||-->
          <!--              menuOpen('nonzeroInventory')-->
          <!--                ? 'menu-is-opening menu-open'-->
          <!--                : ''-->
          <!--            "-->
          <!--          >-->
          <!--            <a href="" class="nav-link">-->
          <!--              <i class="nav-icon fas fa-warehouse"/>-->
          <!--              <p>-->
          <!--                Stock-->
          <!--                <i class="right fas fa-angle-left"/>-->
          <!--              </p>-->
          <!--            </a>-->
          <!--            <ul-->
          <!--              class="nav nav-treeview"-->
          <!--              :style="-->
          <!--                menuOpen('inventory') ||-->
          <!--                menuOpen('adjustments') ||-->
          <!--                menuOpen('nonzeroInventory')-->
          <!--                  ? 'display: block'-->
          <!--                  : 'display: none'-->
          <!--              "-->
          <!--            >-->
          <!--              <li v-if="$can('inventory-history')" class="nav-item">-->
          <!--                <router-link :to="{ name: 'inventory.index' }" class="nav-link">-->
          <!--                  <i class="fas fa-pallet nav-icon"/>-->
          <!--                  <p>View Stock</p>-->
          <!--                </router-link>-->
          <!--              </li>-->
          <li v-if="$can('adjustment-create') ||
      $can('adjustment-view') ||
      $can('adjustment-edit') ||
      $can('adjustment-delete')
      " class="nav-item">
            <router-link :to="{ name: 'adjustments.index' }" class="nav-link">
              <i class="fas fa-sliders-h nav-icon" />
              <p>Stock Transfer</p>
            </router-link>
          </li>

          <!-- ------------------------Community-------------------------------- -->
          <li v-if="$can('purchase-payment-list') ||
      $can('purchase-payment-create') ||
      // $can('purchase-payment-edit') ||
      // $can('purchase-payment-view') ||
      $can('purchase-payment-delete') ||
      // $can('non-purchase-payment-list') ||
      $can('non-purchase-payment-create') ||
      $can('non-purchase-payment-edit') ||
      $can('non-purchase-payment-view') ||
      $can('non-purchase-payment-delete') ||
      // $can('purchase-list') ||
      $can('purchase-create') ||
      $can('purchase-edit') ||
      // $can('purchase-view') ||
      $can('purchase-delete') ||
      $can('purchase-return-list') ||
      $can('purchase-return-create') ||
      $can('purchase-return-edit') ||
      $can('purchase-return-view') ||
      $can('purchase-return-delete')
      " class="nav-header text-bold">COMMUNITY</li>
          <li v-if="
          // $can('purchase-payment-list') ||
      $can('purchase-payment-create') ||
      // $can('purchase-payment-edit') ||
      // $can('purchase-payment-view') ||
      $can('purchase-payment-delete') ||
      // $can('non-purchase-payment-list') ||
      $can('non-purchase-payment-create') ||
      $can('non-purchase-payment-edit') ||
      // $can('non-purchase-payment-view') ||
      $can('non-purchase-payment-delete') ||
      // $can('purchase-list') ||
      $can('purchase-create') ||
      $can('purchase-edit') ||
      // $can('purchase-view') ||
      $can('purchase-delete') ||
      // $can('purchase-return-list') ||
      $can('purchase-return-create') ||
      $can('purchase-return-edit') ||
      $can('purchase-return-view') ||
      $can('purchase-return-delete')
      " class="nav-item has-treeview" :class="menuOpen('nonPurchasePayments') ||
      menuOpen('purchasePayments')
      ? 'menu-is-opening menu-open'
      : ''
      ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-people-carry" />
              <p>
                {{ $t("sidebar.suppliers") }}
                <i class="fas fa-angle-left right" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="menuOpen('nonPurchasePayments') ||
      menuOpen('purchasePayments') || menuOpen('purchases') || menuOpen('purchaseReturns')
      ? 'display: block'
      : 'display: none'
      ">
              <li v-if="$can('supplier-list') ||
      $can('supplier-create') ||
      // $can('supplier-view') ||
      // $can('supplier-edit') ||
      $can('supplier-delete')
      " class="nav-item">
                <router-link :to="{ name: 'suppliers.index' }" href="#" class="nav-link">
                  <i class="nav-icon fas fa-people-carry" />
                  <p>{{ $t("sidebar.suppliers") }}</p>
                </router-link>
              </li>
              <li v-if="
              // $can('purchase-payment-list') ||
      $can('purchase-payment-create') ||
      // $can('purchase-payment-edit') ||
      // $can('purchase-payment-view') ||
      $can('purchase-payment-delete')
      " class="nav-item">
                <router-link :to="{ name: 'purchasePayments.index' }" class="nav-link">
                  <i class="fas fa-plane-departure nav-icon" />
                  <p>{{ $t("sidebar.purchase") }}</p>
                </router-link>
              </li>
              <li v-if="
              // $can('purchase-list') ||
      $can('purchase-create') ||
      // $can('purchase-edit') ||
      // $can('purchase-view') ||
      $can('purchase-delete')
      " class="nav-item">
                <router-link :to="{ name: 'purchases.index' }" class="nav-link">
                  <i class="fas fa-truck-loading nav-icon" />
                  <p>{{ $t("sidebar.purchases_list") }}</p>
                </router-link>
              </li>
              <li v-if="$can('purchase-return-list') ||
      $can('purchase-return-create') ||
      $can('purchase-return-edit') ||
      $can('purchase-return-view') ||
      $can('purchase-return-delete')
      " class="nav-item">
                <router-link :to="{ name: 'purchaseReturns.index' }" class="nav-link">
                  <i class="fas fa-undo-alt nav-icon" />
                  <p>{{ $t("sidebar.returns_list") }}</p>
                </router-link>
              </li>
              <li v-if="$can('non-purchase-payment-list') ||
      $can('non-purchase-payment-create') ||
      $can('non-purchase-payment-edit') ||
      $can('non-purchase-payment-view') ||
      $can('non-purchase-payment-delete')
      " class="nav-item">
                <router-link :to="{ name: 'nonPurchasePayments.index' }" class="nav-link">
                  <i class="fas fa-truck-pickup nav-icon" />
                  <p>{{ $t("sidebar.non_purchase") }}</p>
                </router-link>
              </li>
            </ul>
          </li>
          <li v-if="$can('department-list') ||
      $can('department-create') ||
      $can('department-edit') ||
      $can('department-delete') ||
      $can('employee-list') ||
      $can('employee-create') ||
      $can('employee-edit') ||
      $can('employee-delete') ||
      $can('employee-view') ||
      $can('increment-list') ||
      $can('increment-create') ||
      $can('increment-edit') ||
      $can('increment-view') ||
      $can('increment-delete')
      " class="nav-item has-treeview" :class="menuOpen('departments') ||
      menuOpen('employees') ||
      menuOpen('increments')
      ? 'menu-is-opening menu-open'
      : ''
      ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users-cog" />
              <p>
                {{ $t("sidebar.employees") }}
                <i class="fas fa-angle-left right" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="menuOpen('departments') ||
      menuOpen('employees') ||
      menuOpen('increments')
      ? 'display: block'
      : 'display: none'
      ">
              <li v-if="$can('department-list') ||
      $can('department-create') ||
      $can('department-edit') ||
      $can('department-delete')
      " class="nav-item">
                <router-link :to="{ name: 'departments.index' }" class="nav-link">
                  <i class="fas fa-server nav-icon" />
                  <p>{{ $t("sidebar.departments") }}</p>
                </router-link>
              </li>
              <li v-if="$can('employee-list') ||
      $can('employee-create') ||
      $can('employee-edit') ||
      $can('employee-delete') ||
      $can('employee-view')
      " class="nav-item">
                <router-link :to="{ name: 'employees.index' }" class="nav-link">
                  <i class="fas fa-list-ul nav-icon" />
                  <p>{{ $t("sidebar.employees_list") }}</p>
                </router-link>
              </li>
              <li v-if="$can('increment-list') ||
      $can('increment-create') ||
      $can('increment-edit') ||
      $can('increment-view') ||
      $can('increment-delete')
      " class="nav-item">
                <router-link :to="{ name: 'increments.index' }" class="nav-link">
                  <i class="fas fa-list-ul nav-icon" />
                  <p>{{ $t("sidebar.increments") }}</p>
                </router-link>
              </li>
              <li v-if="$can('payroll-list') ||
      $can('payroll-create') ||
      $can('payroll-view') ||
      $can('payroll-edit') ||
      $can('payroll-delete')
      " class="nav-item">
                <router-link :to="{ name: 'payroll.index' }" class="nav-link">
                  <i class="nav-icon fas fa-clipboard-list" />
                  <p>{{ $t("sidebar.payroll") }}</p>
                </router-link>
              </li>
            </ul>
          </li>


          <!--            </ul>-->
          <!--          </li>-->

          <!--------------------------Reports---------------------------------->
          <li v-if="$can('balance-sheet') || $can('summary-report') || $can('profit-loss')
      || $can('expense-report') || $can('item-report') || $can('item-report') || $can('inventory-report')"
            class="nav-header text-bold">{{ $t("sidebar.reports") }}</li>
          <li v-if="$can('balance-sheet')" class="nav-item">
            <router-link :to="{ name: 'reports.balanceSheet' }" class="nav-link">
              <i class="fas fa-file-invoice-dollar nav-icon" />
              <p>{{ $t("sidebar.balance_sheet") }}</p>
            </router-link>
          </li>
          <li v-if="$can('summary-report')" class="nav-item">
            <router-link :to="{ name: 'reports.summary' }" class="nav-link">
              <i class="fas fa-file-contract nav-icon" />
              <p>{{ $t("sidebar.summary_report") }}</p>
            </router-link>
          </li>
          <li v-if="$can('profit-loss')" class="nav-item">
            <router-link :to="{ name: 'reports.profitLoss' }" class="nav-link">
              <i class="fas fa-chart-line nav-icon" />
              <p>{{ $t("sidebar.profit_loss_report") }}</p>
            </router-link>
          </li>
          <li v-if="$can('expense-report')" class="nav-item">
            <router-link :to="{ name: 'reports.expenses' }" class="nav-link">
              <i class="fas fa-chart-pie nav-icon" />
              <p>{{ $t("sidebar.expense_report") }}</p>
            </router-link>
          </li>
          <li v-if="$can('item-report')" class="nav-item">
            <router-link :to="{ name: 'reports.items' }" class="nav-link">
              <i class="fas fa-chart-bar nav-icon" />
              <p>{{ $t("sidebar.product_report") }}</p>
            </router-link>
          </li>
          <li v-if="$can('inventory-report')" class="nav-item">
            <router-link :to="{ name: 'reports.inventory' }" class="nav-link">
              <i class="fas fa-chart-pie nav-icon" />
              <p>{{ $t("sidebar.stock_report") }}</p>
            </router-link>
          </li>
          <li v-if="$can('summary-report')" class="nav-item">
            <router-link :to="{ name: 'reports.Trialbalance' }" class="nav-link">
              <i class="fas fa-chart-pie nav-icon" />
              <p>{{ $t("sidebar.trial_balance") }}</p>
            </router-link>
          </li>

          <!--------------------------Accounts---------------------------------->
          <!-- <li class="nav-header text-bold">{{ $t("sidebar.account") }}</li> -->
          <!-- <li
            v-if="
              $can('role-permissions') ||
              $can('units') ||
              $can('currencies') ||
              $can('general-settings')
            "
            class="nav-item"
          >
            <router-link :to="{ name: 'setup.general' }" class="nav-link">
              <i class="nav-icon fas fa-cogs"/>
              <p>{{ $t("sidebar.setup") }}</p>
            </router-link>
          </li> -->
          <!-- <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user"/>
              <p>
                {{ $t("sidebar.account_sm") }}
                <i class="right fas fa-angle-left"/>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li v-if="$can('update-profile')" class="nav-item">
                <router-link :to="{ name: 'profile' }" class="nav-link">
                  <i class="nav-icon fas fa-user-circle"/>
                  <p>{{ $t("sidebar.profile") }}</p>
                </router-link>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link admin-logout"
                  href="#"
                  @click.prevent="logout"
                >
                  <i class="nav-icon fas fa-power-off"/>
                  {{ $t("sidebar.logout") }}
                </a>
              </li>
            </ul>
          </li> -->

          <!--------------------------Resource---------------------------------->
          <!-- <li v-if="$can('role-permissions') ||
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
          </li> -->


          <li data-v-6dde423b="" class="nav-item dropdown d-block d-md-none sidebar_user"><a data-v-6dde423b=""
              data-toggle="dropdown" href="#" class="nav-link user-profile">
              <div data-v-6dde423b=""><img data-v-6dde423b=""
                  src="https://www.gravatar.com/avatar/f74c5c5688adcb97bed501aecfa6209e.jpg?s=200&amp;d=https%3A%2F%2Fui-avatars.com%2Fapi%2FSuper+Admin"
                  alt="Super Admin"></div>
              <div data-v-6dde423b="">
                <p data-v-6dde423b="" class="mb-0 ml-2 d-block d-md-none">Super Admin</p>
              </div> <span data-v-6dde423b="" class="mt-1 ml-1"><i data-v-6dde423b=""
                  class="fas fa-angle-down"></i></span>
            </a>
            <div data-v-6dde423b="" class="dropdown-menu dropdown-menu-sm dropdown-menu-right"><a data-v-6dde423b=""
                href="/profile" class="dropdown-item dropdown-icon-center"><svg data-v-6dde423b=""
                  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  stroke-width="2" class="h-6 w-6">
                  <path data-v-6dde423b="" stroke-linecap="round" stroke-linejoin="round"
                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                  </path>
                </svg>
                Profile
              </a>
              <div data-v-6dde423b="" class="dropdown-divider"></div> <a data-v-6dde423b="" href="/setup/general"
                class="dropdown-item dropdown-icon-center"><svg data-v-6dde423b="" xmlns="http://www.w3.org/2000/svg"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-6 w-6">
                  <path data-v-6dde423b="" stroke-linecap="round" stroke-linejoin="round"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                  </path>
                  <path data-v-6dde423b="" stroke-linecap="round" stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Setup
              </a>
              <div data-v-6dde423b="" class="dropdown-divider"></div> <a data-v-6dde423b="" href="#"
                class="dropdown-item dropdown-icon-center"><svg data-v-6dde423b="" xmlns="http://www.w3.org/2000/svg"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-6 w-6">
                  <path data-v-6dde423b="" stroke-linecap="round" stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                  </path>
                </svg>
                Logout
              </a>
            </div>
          </li>
          <!--sidebar complete-->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  data: () => ({
    appName: window.config.appName,
    toggle: null
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },
  mounted() {
    $('[data-widget="treeview"]').Treeview("init");
  },
  methods: {

    menuOpen(routeName) {
      if (this.$route.name) {
        return this.$route.name.indexOf(routeName) > -1 ? true : false;
      }
      return false;
    },

    async executeAction(command) {
      await axios
        .get("/api/server?command=" + command)
        .then(({ data }) => {
          toast.fire({
            type: "success",
            title: "Cache cleared successfully!",
          });
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("common.error_msg") });
        });
    },

    async logout() {
      // Log out the user.
      await this.$store.dispatch("auth/logout");
      // Redirect to login.
      this.$router.push({ name: "login" });
    },
  },
};
</script>
<style scoped>
.sticky-sidebar {
  width: 249px;
  z-index: 999;
  display: none;
  background-color: #212529;
  color: white;
    ition: fixed;
  bottom: 5%;
}


.add-sidebar-animation {
  display: block;
  animation: AddAnimateSidebar 1s forwards alternate;
}

@keyframes AddAnimateSidebar {
  0% {
    height: 0px;
  }

  100% {
    height: 50vh;
  }
}

.remove-sidebar-animation {
  display: block;
  animation: removeAnimateSidebar 1s forwards alternate ease-out;
}

@keyframes removeAnimateSidebar {
  0% {
    height: 50vh;
  }

  100% {
    height: 0px;
  }
}
</style>
