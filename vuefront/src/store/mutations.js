import { SET_USER, RESET_USER, REFRESH_TOKEN } from './mutation-types'
import router from '@/router'

const mutations = {
    [SET_USER] (state, user) {
        state.user = user;
        state.isLogged = true;
    },

    [RESET_USER] (state) {
        state.user = null;
        state.isLogged = false;
        router.push('/login');
    },

    [REFRESH_TOKEN] (state, token) {
        state.token = token;
    } 
}

export default mutations