import axios from 'axios'
import * as types from '../mutation-types'
import Cookies from "js-cookie";

// state
export const state = {
    loading: true,
    items: null,
    appInfo: null,
    facilityItems: null,
    checkOutItems: null,
    checkoutRoomsDetails: null,
    hotelCategoryItems: null,
    transactions: null,
    hotelItems: null,
    bedTypeItems: null,
    floorItems: null,
    restaurant: null,
    selectedHotel: Cookies.get('selectedHotel') || '1',
}

// getters
export const getters = {
    loading: state => state.loading,
    items: state => state.items ? state.items.data : '',
    facilityItems: state => state.facilityItems ? state.facilityItems.data.data : [],
    checkOutItems: state => state.checkOutItems ? state.checkOutItems.data.data : [],
    checkoutRoomsDetails: state => state.checkoutRoomsDetails ? state.checkoutRoomsDetails.data.data : [],
    hotelCategoryItems: state => state.hotelCategoryItems ? state.hotelCategoryItems.data.data : [],
    transactions: state => state.transactions ? state.transactions.data.data : [],
    hotelItems: state => state.hotelItems ? state.hotelItems.data.data : [],
    bedTypeItems: state => state.bedTypeItems ? state.bedTypeItems.data.data : [],
    floorItems: state => state.floorItems ? state.floorItems.data.data : [],
    restaurant: state => state.restaurant ? state.restaurant.data : [],
    pagination: (state) => {
        if (state.items) {
            return state.items.meta;
        } else if (state.hotelCategoryItems && state.hotelCategoryItems?.data) {
            return state.hotelCategoryItems?.data?.meta
        } else if (state.transactions && state.transactions?.data) {
            return state.transactions?.data?.meta
        }
         return { current_page: 1 }
    },
    appInfo: state => state.appInfo,
    selectedHotel: state => state.selectedHotel,
}

// mutations
export const mutations = {
    // Items Mutations
    [types.FETCH_DATA] (state, { items, loading }) {
        state.items = items
        state.loading = loading || false
    },
    [types.FETCH_RESTRO_DATA] (state, { restaurant, loading }) {
        state.restaurant= restaurant
        state.loading = loading || false
    },
    [types.DELETE_DATA] (state, { slug }) {
        if (state.items) {
            state.items.data = state.items.data.filter(data => data.slug !== slug)
        }
    },

    [types.DELETE_DATA] (state, { slug }) {
        if (state.hotelCategoryItems) {
            state.hotelCategoryItems.data = state.hotelCategoryItems?.data?.data?.filter(data => data.slug !== slug)
        }
    },
    [types.DELETE_DATA] (state, { slug }) {
        if (state.transactions) {
            state.transactions.data = state.transactions?.data?.data?.filter(data => data.slug !== slug)
        }
    },
    [types.FETCH_APPINFO] (state, { appInfo  }) {
        state.appInfo = appInfo
    },
    [types.SET_HOTEL] (state, { hotel }) {
        state.selectedHotel = hotel
    }
}

// Actions
export const actions = {
    // Fetch Data
    async fetchData ({ commit }, { path, currentPage, hotel = '', search = '' }) {
        const { data } = await axios.get(window.location.origin + path + currentPage + hotel + search)
        commit(types.FETCH_DATA, { items: data, loading: false })
    },

    async fetchDataWithDate ({ commit }, { path, currentPage, startDate = '', endDate = '' }) {
        const { data } = await axios.get(window.location.origin + path + currentPage + startDate + endDate)
        commit(types.FETCH_DATA, { items: data, loading: false })
    },

    // APPINFO Data
    async fetchSettingData ({ commit }) {
        const { data } = await axios.get(window.location.origin + '/api/general-settings')
        commit(types.FETCH_APPINFO, { appInfo: data })
    },

    // Search Data
    async searchData ({ commit }, { query, path, currentPage, term = '', startDate = '', endDate = '' }) {
        const { data } = await axios.get(window.location.origin + path + '?term= ' + term + '&page=' + (currentPage || 1) + '&startDate=' + startDate + '&endDate=' + endDate)
        commit(types.FETCH_DATA, { items: data })
    },
    // Get All Data
    async allData ({ commit }, { path }) {
        const { data } = await axios.get(window.location.origin + path)
        commit(types.FETCH_DATA, { items: data })
    },

    async getFacilityData ({ commit }, { path }) {
        state.facilityItems = await axios.get(window.location.origin + path)
    },

    async getCheckOutData ({ commit }, { path }) {
        state.checkOutItems = await axios.get(window.location.origin + path)
        commit(types.FETCH_DATA, { checkOutItems: state.checkOutItems , loading: false })
    },

    async getCheckoutRoomsDetails ({ commit }, { path }) {
        state.checkoutRoomsDetails = await axios.get(window.location.origin + path)
        commit(types.FETCH_DATA, { checkoutRoomsDetails: state.checkoutRoomsDetails , loading: false })
    },

    async getHotelCategoryData ({ commit }, { path, search = '' }) {
        state.hotelCategoryItems = await axios.get(window.location.origin + path + search)
        commit(types.FETCH_DATA, { hotelCategoryItems: state.hotelCategoryItems , loading: false })
    },
    async transactions ({ commit }, { path, search = '' }) {
        state.transactions = await axios.get(window.location.origin + path + search)
        commit(types.FETCH_DATA, { transactions: state.transactions , loading: false })
    },

    async getHotelCategoryCheckInData ({ commit }, { path }) {
        state.hotelCategoryItems = await axios.post(window.location.origin + path)
        commit(types.FETCH_DATA, { hotelCategoryItems: state.hotelCategoryItems , loading: false })
    },

    async getHotelData ({ commit }, { path }) {
        state.hotelItems = await axios.get(window.location.origin + path)
    },

    async getRestaurantData ({ commit }, { path }) {
        const { data } = await axios.get(window.location.origin + path)
        commit(types.FETCH_RESTRO_DATA, { restaurant: data, loading: false })
    },

    async getBedTypeData ({ commit }, { path }) {
        state.bedTypeItems = await axios.get(window.location.origin + path)
    },

    async getFloorData ({ commit }, { path }) {
        state.floorItems = await axios.get(window.location.origin + path)
    },

    // Delete Data
    async deleteData ({ commit }, { path, slug }) {
        try {
            const { data } = await axios.delete(window.location.origin + path + slug)
            commit(types.DELETE_DATA, { slug: slug })
            return data.success
        } catch (error) {
            return error
        }
    },

    async hotelDeleteData ({ commit }, { path, slug, query = '' }) {
        try {
            const { data } = await axios.post(window.location.origin + path + slug + query)
            commit(types.DELETE_DATA, { slug: slug })
            return data.success
        } catch (error) {
            return error
        }
    },

    async getDeleteData ({ commit }, { path, slug }) {
        try {
            const { data } = await axios.get(window.location.origin + path + slug)
            commit(types.DELETE_DATA, { slug: slug })
            return data.success
        } catch (error) {
            return error
        }
    },

    async setHotel ({ commit }, { hotel }) {
        let hotelId = hotel.id || hotel;
        await axios.post(window.location.origin + '/api/set-shop', {hotel: hotelId})
        commit(types.SET_HOTEL, { hotel: hotelId })
        Cookies.set('selectedHotel', hotelId, { expires: 365 })
    }
}
