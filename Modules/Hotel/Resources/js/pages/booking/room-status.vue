<template>
  <div class="tabcontent">
    <div data-v-0c28a3e6="" id="printMe" class="table-responsive table-custom mt-3">
      <div class="d-flex flex-wrap pt-3 room_status_head" style="
          background: #fff;
        ">
        <div class="form-group col-md-2">
          <date-range-picker
            ref="roomStatusPicker"
            opens="right"
            :singleDatePicker="true"
            :autoApply="true"
            v-model="date"
            :ranges="false"
            @update="updateValues"
            class="w-100"
          >
            <template v-slot:input="picker" style="min-width: 350px">
              {{ filterDate | startDate }}
            </template>
          </date-range-picker>
        </div>
        
        <div class="form-group col-md-2">
          <v-select
            class="flex-grow-1"
            v-model="room_status"
            :options="roomStatus"
            name="room_status"
            label="room_status_name"
            placeholder="Search a Room Status"
          />
        </div>
        <div class="form-group col-md-2">
          <v-select
            class="flex-grow-1"
            v-model="selectedCategory"
            :options="roomCategories"
            name="room_category"
            label="room_category_name"
            placeholder="Search a category"
          />
        </div>
        <div class="form-group col-md-3">
          <v-select
            class="flex-grow-1"
            v-model="selectedFloor"
            :options="roomFloors"
            name="room_category"
            label="floorsName"
            placeholder="Search a floor"
          />
        </div>

        <div class="col-3 mb-2">
          <div class="d-flex flex-column flex-column-reverse search-area">
            <div class="d-inline-block float-right search-btn">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input type="text" v-model="search" placeholder="Search Room..." class="search-input form-control"> <label
            class="search-btn d-none"><i class="fas fa-times"></i></label>
          </div>
        </div>
      </div>
      <div data-v-0c28a3e6="" class="row common-row two">
        <div v-if="loading" class="position-absolute w-100 h-100 opacity-50 z-10 tbl-loading">
          <div class="loader07"></div>
        </div>
        <div v-else class="col-md-3" v-for="room in roomsData" :key="room.id">
          <div class="card border p-2">
            <div class="">
              <div class="d-flex flex-wrap p-1">
                <div class="col-md-6"><strong class="name_client">{{room.room_category.room_category_name}}</strong></div>
                <div class="col-md-6"><span class="float-right">
                  <span v-if="room.occupied" class="badge btn-danger float-right fs-7">Occupied</span>
                  <span v-else class="badge btn btn-success float-right fs-7">Available</span>
                </span>
                </div>
                <div class="col-md-12 text-center">
                  <div class="-right badge-hotel h3 ">
                    {{room.room_name}}
                  </div>
                </div>
              </div>
            </div>
            <div class="card-bottom-header"></div>
            <div class="card-body box-profile">
              <div class="d-flex mb-3 row">
                <div class="col-md-12"> <span class="text-center">
                      <div class="mt-2"><span title="Adult"
                                              class="p-0  fs-7 me-1 pb-0 text-dark"><i
                        class="fas fa-male"></i> {{room.persons || 0}}
                          </span> <span title="Children"
                                        class="p-0  fs-7 me-1 me-1 text-dark"><i
                        class="fas fa-child"></i> {{room.children || 0}}</span> <span title="Infant"
                                                                 class=" me-1 p-0 text-dark"><i
                        class="fas fa-baby"></i> {{room.infants || 0}}
                          </span></div>
                  </span></div>
                <div class="col-md-12 mt-1"> <span class="text-center">{{room.floor?.floorsName}}</span></div>
              </div>
              <div v-if="!room.occupied" class="d-flex" style="gap: 4px;">
                <a :href="`/booking/create?hotel=${room.hotel.id}&category=${room.room_category.id}&room=${room.id}`" class="btn-block btn btn-outline-primary">Book Now</a>
              </div>
              <div v-else class="d-flex" style="gap: 4px;">
                <a v-if="[2, 5].includes(room.status && room.status)" :href="`/booking/create/${room.booking_id}`" class="btn-block btn btn-outline-primary">Check In</a>
                <a v-if="[3, 4].includes(room.status && room.status)" :href="`/check-out/${room.booking_id}`" class="btn-block btn btn-outline-primary">Check Out</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from "vuex";
  import Multiselect from 'vue-multiselect';
  import axios from "axios";
  import DateRangePicker from "vue2-daterange-picker";
  import DatePicker from "vue2-datepicker";
  import moment from "moment";

  export default {
    metaInfo() {
      return { title: 'Hotel room status' };
    },
    components: {
      DatePicker,
      Multiselect,
      DateRangePicker
    },

    filters: {
      startDate(val) {
        return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.from");
      },
    },

    data: () => ({
        rooms: [],
        date: { startDate: moment().format("YYYY-MM-DD") },
        filterDate: moment().format("YYYY-MM-DD"),
        loading: false,
        roomCategories: [],
        selectedCategory: '',
        roomFloors: [],
        search: '',
        selectedFloor: '',
        roomStatus: [{ room_status_name: 'Available' },{ room_status_name: 'Occupied' }],
        room_status: '',
     }),

    computed: {
      ...mapGetters("operations", ["selectedHotel"]),

      roomsData(){
        // console.log(this.room_status);
        if(this.room_status != null){
          if(typeof this.room_status.room_status_name !== 'undefined'){
              if(this.room_status.room_status_name == 'Available'){
                  return this.rooms.filter((roomData) => roomData.occupied === false);
              } else {
                return this.rooms.filter((roomData) => roomData.occupied === true);
              }
          } else {
            return this.rooms;
          }
        } else {
          return this.rooms;
        }
      }
    },

    watch: {
      selectedCategory() {
        this.getRooms();
      },
      selectedFloor() {
        this.getRooms();
      },
      search() {
        this.getRooms();
      },
      selectedHotel(){
        this.getRooms();
      }
    },

    methods: {
      async updateValues() {
        this.filterDate = moment(this.date?.startDate).format("YYYY-MM-DD");
        await this.getRooms();
      },
      async getRooms() {
        this.loading = true;
        try {
          let category = this.selectedCategory ? this.selectedCategory.id : '';
          let floor = this.selectedFloor ? this.selectedFloor.id : '';
          let hotel = '';
          if (this.selectedHotel && this.selectedHotel !== 'all') {
            hotel = this.selectedHotel
          }
          let {data} = await axios.get(window.location.origin + `/api/room/list-with-status?date=${this.filterDate}&category=${category}&floor=${floor}&search=${this.search}&hotel=${hotel}`)
          this.rooms = data.data;
        } catch (e) {
          console.log(e)
        }

        this.loading = false;
      }
    },

    async created() {
      await this.getRooms();
      let rooms = _.cloneDeep(this.rooms)
      this.roomCategories = rooms.map(val => val.room_category)
      this.roomCategories = [...new Map(this.roomCategories.map(item => [item['id'], item])).values()];
      this.roomFloors = rooms.map(val => val.floor)
      this.roomFloors = [...new Map(this.roomFloors.map(item => [item['id'], item])).values()];
    }
  }

</script>
