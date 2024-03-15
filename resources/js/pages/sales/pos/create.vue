<template>
    <div class="p-0 position-relative mt-2 ledger-account">
        <div id="tabs" class="justify-content-end">
            <div class="tabs w-100 pa-2 d-flex justify-content-between">
<div>
<h4 class="m-3">Create Sale</h4>
</div>
<div>
    <a @click="activetab = 1" :class="[activetab === 1 ? 'active' : '']" class="outline_btn">Create Orders</a>
    <a @click="activetab = 2" :class="[activetab === 2 ? 'active' : '']" class="outline_btn">Orders</a>
</div>
            </div>
            <div v-if="activetab" class="tabcontent">
                <div class="mt-3">
                    <CreateSale v-if="activetab === 1" />
                    <RestaurantOrders v-if="activetab === 2" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import RestaurantOrders from "../../../../../Modules/Restaurant/Resources/js/pages/restaurant/order/index";
    import CreateSale from './create-sale';

    export default {
        components: {
            RestaurantOrders,
            CreateSale
        },
        created() {
          this.$route.params?.order && (this.activetab = 2);
        },
        data: () => ({
            activetab: 1,
        })
    }
</script>
<style lang="scss" scoped>

    .tabs a {
        float: left;
        cursor: pointer;
        padding: 10px 20px;
        transition: background-color 0.2s;
        border-radius: 4px;
        font-weight: bold;
        border: 1px solid transparent;
        color: #000000;
        margin-top: 10px;
        margin-right: 10px;
    }

    .ledger-account .tabs a.active {
        background: #007bff;
        color: #fff;
        border-radius: 5px;
        cursor: pointer;
        hyphenate-character: revert;

    }

    .ledger-account .tabs a {
        padding: 5px 25px;
    }

    .ledger-account .tabs a:hover {
        background: #007bff;
        color: #fff;
    }

    .ledger-account .tabs {
        padding-bottom: 20px;
        float: left;

    }

    .ledger-account .tabs span {
        background: #9b9999;
        margin-left: 5px;
        font-size: 10px;
        padding: 2px 4px;
        border-radius: 2px;
    }

    .ledger-account div#tabs a.btn.btn-primary {
        height: fit-content;
    }
</style>
