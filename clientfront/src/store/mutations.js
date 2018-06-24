import { SET_USER, RESET_USER, REFRESH_TOKEN, SET_CAR, SET_DEFECT_ACT } from './mutation-types'
import router from '@/router'

const mutations = {
    [SET_USER] (state, user) {
        state.user = user;
        state.isLogged = true;
    },

    [RESET_USER] (state) {
        state.user = null;
        state.isLogged = false;
    },

    [REFRESH_TOKEN] (state, token) {
        state.token = token;
    },
    
    [SET_CAR] (state, car) {
        state.car = car;
    },
    
    [SET_DEFECT_ACT] (state, act) {
        state.defectAct = act;
    }
}

export default mutations