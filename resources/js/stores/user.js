import {defineStore} from 'pinia';

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
        isLoggedIn: false
    }),
    getters: {

    },
    actions: {
        setUser(user) {
            this.user = user;
        },
        setLoggedIn(state) {
            this.isLoggedIn = state;
        }
    }
})
