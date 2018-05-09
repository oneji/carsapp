import { SET_USER, RESET_USER } from './mutation-types'

const mutations = {
    [SET_USER] (state, user) {
        state.user = user;
        state.isLogged = true;
    },

    [RESET_USER] (state) {
        state.user = null;
        state.isLogged = false;
    }
}

export default mutations