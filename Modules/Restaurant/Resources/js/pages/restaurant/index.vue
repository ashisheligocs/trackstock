<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <div v-if="$can('restaurant-create') ||
                    $can('restaurant-list') ||
                    $can('restaurant-edit') ||
                    $can('restaurant-delete') ||
                    $can('restaurant-order')
                    ">

                    <!-- /.card-header -->
                    <div class="card-body p-0 position-relative">
                        <table-loading v-show="loading" />
                        <div id="printMe" class="table-responsive table-custom mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ $t("common.s_no") }}</th>
                                        <th>{{ $t("common.name") }}</th>
                                        <th>{{ $t("common.phone") }}</th>
                                        <th>{{ $t("common.email") }}</th>
                                        <th v-if="$can('restaurant-edit') ||
                                            $can('restaurant-view') ||
                                            $can('restaurant-delete')
                                            " class="text-right no-print">
                                            {{ $t("common.action") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <tr v-show="restaurant?.length" v-for="(data, i) in restaurant" :key="i">
                                        <td>
                                            <span>{{ i + 1 }}</span>
                                        </td>
                                        <td>{{ data?.restaurant_name }}</td>
                                        <td>{{ data?.restaurant_phone }}</td>
                                        <td>{{ data?.restaurant_email }}</td>
                                        <td v-if="$can('restaurant-edit') ||
                                            $can('restaurant-view') ||
                                            $can('restaurant-delete')
                                            " class="text-right no-print">
                                            <div class="btn-group">
                                                <router-link v-if="$can('restaurant-edit')" v-tooltip="'Edit Food Price'" :to="{
                                                    name: 'restaurant.edit',
                                                    params: { slug: data.restaurant_id },
                                                }" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit" />
                                                </router-link>
<!--                                                <a v-if="$can('restaurant-delete')" v-tooltip="$t('common.delete')" href="#"-->
<!--                                                    class="btn btn-danger btn-sm" @click="deleteData(data.id)">-->
<!--                                                    <i class="fas fa-trash" />-->
<!--                                                </a>-->
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-show="!loading && !restaurant?.length">
                                        <td colspan="12">
                                            <EmptyTable />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
    metaInfo() {
        return { title: this.$t("restaurant.index.page_title") };
    },
    data: () => ({
        breadcrumbsCurrent: "restaurant.index.breadcrumbs_current",
        breadcrumbs: [
            {
                name: "restaurant.index.breadcrumbs_first",
                url: "home",
            },
            {
                name: "restaurant.index.breadcrumbs_second",
                url: "",
            },
        ],
        perPage: 10,
    }),
    watch: {
        selectedHotel: function () {
            this.getData();
        },
    },
    // Map Getters
    computed: {
        ...mapGetters("operations", ["items", "loading", "pagination", "appInfo", "restaurant", "selectedHotel"]),
    },
    created() {
        this.getData(1);
        this.prefix = this.appInfo.purchasePrefix;
    },
    methods: {
        // filter data for selected date range
        async getData(page) {
            this.$store.state.operations.loading = true;
            await this.$store.dispatch('operations/getRestaurantData', {
                path: '/api/restaurant?page='+ page,
            });
        },
    },
};
</script>
