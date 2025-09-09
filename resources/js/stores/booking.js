import { defineStore } from 'pinia';

export const useBookingStore = defineStore('booking', {
    state: () => ({
        bookingData: null,
        needDocs: false
    }),
    actions: {
        setBookingData(bookingData) {
            this.bookingData = bookingData;
        },
        setNeedDocs(needDocs) {
            this.needDocs = needDocs;
        }
    },
    getters: {
        getBookingData: (state) => state.bookingData,
        getNeedDocs: (state) => state.needDocs,
    },
    persist: typeof window !== 'undefined'
        ? {
            key: 'booking-store',
            storage: window.sessionStorage,
        }
        : false,
});
