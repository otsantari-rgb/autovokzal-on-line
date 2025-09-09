import { defineStore } from 'pinia';

export const useOrderDetailsStore = defineStore('orderDetails', {
    state: () => ({
        passengerData: [],       // Данные пассажиров
        selectedSeats: [],       // Выбранные места
        contactInfo: {           // Контактная информация
            email: '',
            phone: ''
        },
    }),
    actions: {
        setPassengerData(data) {
            this.passengerData = data;
        },
        setSelectedSeats(seats) {
            this.selectedSeats = seats;
        },
        setContactInfo(contact) {
            this.contactInfo = contact;
        },
        clear() {
            this.passengerData = []
            this.selectedSeats = []
            this.contactInfo = {
                email: '',
                phone: ''
            }
        }
    },
    getters: {
        getPassengerData: (state) => state.passengerData,
        getSelectedSeats: (state) => state.selectedSeats,
        getContactInfo: (state) => state.contactInfo,
    },
    persist: typeof window !== 'undefined'
        ? {
            key: 'order-details-store',
            storage: window.sessionStorage,
        }
        : false,
});
