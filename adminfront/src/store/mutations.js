import { SET_USER, RESET_USER, REFRESH_TOKEN, USER_TO_EDIT } from './mutation-types'
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

    [USER_TO_EDIT] (state, user) {
        state.userToEdit = user;
    }
}

export default mutations