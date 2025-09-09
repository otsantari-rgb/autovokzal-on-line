import { defineStore } from 'pinia';

export const useSearchStore = defineStore('search', {
    state: () => ({
        from: '',
        to: '',
        date: '',
        sheets: []
    }),
    actions: {
        setSearchQuery(query) {
            this.from = query.from || '';
            this.to = query.to || '';
            this.date = query.date || '';
        },
        setSearchResults(data) {
            this.from = data.from;
            this.to = data.to;
            this.date = data.date;
            this.sheets = data.sheets;
        },
    },
    getters: {
        getResults: (state) => state
    },
    persist: typeof window !== 'undefined'
        ? {
            key: 'search-store',
            storage: window.sessionStorage,
        }
        : false,
});
