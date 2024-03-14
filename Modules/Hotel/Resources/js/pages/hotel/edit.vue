<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->

        <div class="container-fluid">
            <br /><br />
            <ul class="list-unstyled multi-steps">
                <li v-for="(item) in stepWizard" :key="item.id" :class="currentTab == item.id ? 'is-active' : ''" @click="currentTab == (item.id) + 1 ? currentTab = item.id : ''">{{ item.title }}</li>
            </ul>
        </div>

        <div class="row">

            <div v-if="currentTab == 1" class="col-lg-12">
                <EditHotelStep @currentTab="currentTabEmit" />
            </div>

            <div v-if="currentTab == 2" class="col-lg-12">
                <MealPlanStep @currentTab="currentTabEmit" @currentTabPre="currentTabPre" />
            </div>

            <div v-if="currentTab == 3" class="col-lg-12">
                <FacilitiesPriceStep @currentTab="currentTabEmit" @currentTabPre="currentTabPre" />
            </div>

            <div v-if="currentTab == 4" class="col-lg-12">
                <AddRoomCategoryStep @currentTab="currentTabEmit" @currentTabPre="currentTabPre" />
            </div>

            <div v-if="currentTab == 5" class="col-lg-12">
                <AddRoomStep @currentTabPre="currentTabPre" />
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import EditHotelStep from '../../components/EditHotelStep.vue'
import MealPlanStep from '../../components/MealPlanStep.vue'
import FacilitiesPriceStep from '../../components/FacilitiesPriceStep.vue'
import AddRoomCategoryStep from '../../components/AddRoomCategoryStep.vue'
import AddRoomStep from '../../components/AddRoomStep.vue'
export default {
    // middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('hotel.hotelAdd.edit.page_title') }
    },
    components: {
        Multiselect,
        EditHotelStep,
        MealPlanStep,
        FacilitiesPriceStep,
        AddRoomCategoryStep,
        AddRoomStep
    },
    data: () => ({ 
        breadcrumbsCurrent: 'hotel.hotelAdd.edit.breadcrumbs_current',
        breadcrumbs: [
            {
                name: 'hotel.hotelAdd.edit.breadcrumbs_first',
                url: 'home',
            },
            {
                name: 'hotel.hotelAdd.edit.breadcrumbs_second',
                url: 'hotel',
            },
            {
                name: 'hotel.hotelAdd.edit.breadcrumbs_active',
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
            {
                id: 3,
                title: 'Facilities Price',
            },
            {
                id: 4,
                title: 'Room Category',
            },
            {
                id: 5,
                title: 'Add Rooms',
            },
        ],
    }),
    methods: {
        currentTabEmit() {
            if (this.currentTab == 1) {
                this.currentTab = 2
                window.scrollTo(0, 0)
            }else if (this.currentTab == 2) {
                this.currentTab = 3
                window.scrollTo(0, 0)
            }else if (this.currentTab == 3) {
                this.currentTab = 4
                window.scrollTo(0, 0)
            }else if (this.currentTab == 4) {
                this.currentTab = 5
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
            }else if (this.currentTab == 4) {
                this.currentTab = 3
                window.scrollTo(0, 0)
            }else if (this.currentTab == 5) {
                this.currentTab = 4
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

.multi-steps > li.is-active ~ li:before, .multi-steps > li.is-active:before {
  content: counter(stepNum);
  font-family: inherit;
  font-weight: 700;
}
.multi-steps > li.is-active ~ li:after, .multi-steps > li.is-active:after {
  background-color: #ededed;
}

.multi-steps {
  display: table;
  table-layout: fixed;
  width: 100%;
}
.multi-steps > li {
  counter-increment: stepNum;
  text-align: center;
  display: table-cell;
  position: sticky;
  color: #6366f1;
}
.multi-steps > li:before {
  content: "";
  content: "✓;";
  content: "𐀃";
  content: "𐀄";
  content: "✓";
  display: block;
  margin: 0 auto 4px;
  background-color: #fff;
  width: 36px;
  height: 36px;
  line-height: 32px;
  text-align: center;
  font-weight: bold;
  border-width: 2px;
  border-style: solid;
  border-color: #6366f1;
  border-radius: 50%;
}
.multi-steps > li:after {
  content: "";
  height: 13px;
  width: 100%;
  background-color: #6366f1;
  position: absolute;
  top: 12px;
  left: 50%;
  z-index: -1;
}
.multi-steps > li:last-child:after {
  display: none;
}
.multi-steps > li.is-active:before {
  background-color: #fff;
  border-color: #6366f1;
}
.multi-steps > li.is-active ~ li {
  color: #808080;
}
.multi-steps > li.is-active ~ li:before {
  background-color: #ededed;
  border-color: #ededed;
}
</style>
