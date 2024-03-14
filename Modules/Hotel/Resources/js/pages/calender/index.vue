<template>
  <div>
    <div class="d-flex flex-wrap pt-3" style="
          background: #fff;
        ">
<!--      <div data-v-bebfb8d0="" class="form-group col-md-3">-->
<!--        <date-range-picker-->
<!--          ref="roomStatusPicker"-->
<!--          opens="right"-->
<!--          :singleDatePicker="true"-->
<!--          :autoApply="true"-->
<!--          v-model="date"-->
<!--          :ranges="false"-->
<!--          @update="updateValues"-->
<!--          class="w-100"-->
<!--        >-->
<!--          <template v-slot:input="picker" style="min-width: 350px">-->
<!--            {{ filterDate | startDate }}-->
<!--          </template>-->
<!--        </date-range-picker>-->
<!--      </div>-->
      <div class="form-group col-md-4">
        <v-select
          class="flex-grow-1"
          v-model="selectedCategory"
          :options="roomCategories"
          name="room_category"
          label="room_category_name"
          placeholder="Search a category"
        />
      </div>
      <div class="form-group col-md-4">
        <v-select
          class="flex-grow-1"
          v-model="selectedFloor"
          :options="roomFloors"
          name="room_category"
          label="floorsName"
          placeholder="Search a floor"
        />
      </div>

      <div class="col-4 mb-2">
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
    <div>
      <div class="d-flex justify-content-center text-bold my-3 text-lg" style="gap: 1rem">
        <span @click.prevent="changeMonth(false)" style="cursor: pointer"> < </span>
        <span>{{`${currentMonth} ${currentYear}`}}</span>
        <span @click.prevent="changeMonth(true)" style="cursor: pointer"> > </span>
      </div>
      <div class="overflow-auto">
        <table class="track_calender_table table w-80">
          <tbody>
          <tr>
            <th>Room Number</th>
            <th v-for="n in daysInMonth" :key="n">{{n}}</th>
          </tr>
          <tr v-for="room in rooms" :key="room.id">
            <th>{{room.room_name}}</th>
            <template v-for="(n, i) in daysInMonth">
              <td :colspan="colspan(room, n) ? room.days + 1 : 1"  @click="dateClick(room, n)">&nbsp;

              </td>
            </template>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from "vuex";
  import Multiselect from 'vue-multiselect';
  import axios from "axios";
  import DateRangePicker from "vue2-daterange-picker";
  import moment from "moment";
  import _ from 'lodash';

  export default {
    metaInfo() {
      return { title: 'Hotel room status' };
    },
    components: {
      Multiselect,
      DateRangePicker
    },
    data: () => ({
      rooms: [],
      today: moment(),
      dateContext: moment(),
      currentMonth: moment().format('MMMM'),
      currentYear: moment().format('YYYY'),
      currentDate: moment().get('date'),
      daysInMonth: moment().daysInMonth(),
      days: moment.weekdaysShort(),
      date: { startDate: moment().format("YYYY-MM-DD") },
      filterDate: moment().format("YYYY-MM-DD"),
      loading: false,
      roomCategories: [],
      selectedCategory: '',
      roomFloors: [],
      search: '',
      selectedFloor: '',
    }),

    computed: {
      ...mapGetters("operations", ["selectedHotel"]),

      firstDayOfMonth(){
        return moment(this.dateContext).subtract((this.dateContext.get('date') - 1), 'days').weekday();
      },

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
      selectedHotel()
      {
        this.getRooms();
      }
    },
    async created() {
      await this.getRooms();
      let rooms = _.cloneDeep(this.rooms)
      this.roomCategories = rooms.map(val => val.room_category)
      this.roomCategories = [...new Map(this.roomCategories.map(item => [item['id'], item])).values()];
      this.roomFloors = rooms.map(val => val.floor)
      this.roomFloors = [...new Map(this.roomFloors.map(item => [item['id'], item])).values()];
    },
    methods: {
      dateClick(room, date) {
        console.log(room, date)
      },
      async colspan(room, n) {
        return (room.days && room.check_in && moment(room.check_in).format('DD') == n);
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
          console.log(_.map(this.rooms, 'days'))
        } catch (e) {
          console.log(e)
        }

        this.loading = false;
      },

      changeMonth(NextPrev = false) {
        if (NextPrev) {
          this.dateContext = moment(this.dateContext).add(1, 'month');
        } else {
          this.dateContext = moment(this.dateContext).subtract(1, 'month');
        }
        this.currentMonth = this.dateContext.format('MMMM');
        this.currentYear = this.dateContext.format('YYYY');
        this.daysInMonth = this.dateContext.daysInMonth();
      },
    }
  }

</script>

<style>
  .track_calender_table, .track_calender_table th, .track_calender_table td {
    border:1px solid gray;
    border-collapse: collapse;
    font-size:14px;
    text-align: center;
  }
  th{

  }

</style>
