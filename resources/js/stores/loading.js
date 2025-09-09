import { defineStore } from 'pinia';

export const useLoadingStore = defineStore('loading', {
    state: () => ({
        loading: false,
    }),
    actions: {
        setLoading(value) {
            this.loading = value;
        },
    },
    getters: {
        isLoading: (state) => state.loading,
    },
});
