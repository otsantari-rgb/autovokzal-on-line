
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
    }),
    actions: {
        setUser(user) {
            if (user && typeof user === 'object' && 'id' in user) {
                this.user = user;
            } else {
                this.user = null;
            }
        },
        logout() {
            this.user = null;
        },
    },
    getters: {
        getUser: (state) => state.user,
    },
});
