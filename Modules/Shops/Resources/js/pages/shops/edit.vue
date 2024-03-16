<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->

        <div class="row">

            <div v-if="currentTab == 1" class="col-lg-12">
                <EditHotelStep @currentTab="currentTabEmit" />
            </div>

        </div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import EditHotelStep from '../../components/EditHotelStep.vue'
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('shop.shopAdd.edit.page_title') }
    },
    components: {
        Multiselect,
        EditHotelStep,
    },
    data: () => ({ 
        breadcrumbsCurrent: 'shop.shopAdd.edit.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'shop.shopAdd.edit.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'shop.shopAdd.edit.breadcrumbs_second',
                url: 'shop',
            },
            {
                name: 'shop.shopAdd.edit.breadcrumbs_active',
                url: '',
            },
        ],
        currentTab: 1,
        stepWizard: [
            {
                id: 1,
                title: 'Add Hotel',
            },
            {
                id: 2,
                title: 'Meal Plan',
            },
            
        ],
    }),
    methods: {
        currentTabEmit() {
            if (this.currentTab == 1) {
                // this.currentTab = 2
                this.$router.push({ name: 'shops' });
            }else if (this.currentTab == 2) {
                this.currentTab = 3
                window.scrollTo(0, 0)
            }
        },
        currentTabPre() {
            if (this.currentTab == 2) {
                this.currentTab = 1
                window.scrollTo(0, 0)
            }else if (this.currentTab == 3) {
                this.currentTab = 2
                window.scrollTo(0, 0)
            }
        }
    },
}
</script>
<style src="../../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
:deep(.multiselect__tags) {
    min-height: 38px !important;
    border: none !important;
    padding: 4px 40px 0 4px !important;
}
:deep(.multiselect__placeholder){
    margin-bottom: 4px !important;
    padding-top: 4px !important;
}
:deep(.multiselect__single) {
    margin-bottom: 0px !important;
    margin-top: 4px !important;
}
:deep(.multiselect){
    width: auto;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
    min-height: 38px !important;
}

</style>
