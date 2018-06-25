import { SET_USER, RESET_USER, REFRESH_TOKEN, SET_CAR, SET_DEFECT_ACT, SET_DEFECT_TYPES, SET_EQUIPMENT } from './mutation-types'
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
    },

    [SET_DEFECT_TYPES] (state, defectTypes) {
        state.defectTypes = defectTypes;
    },
 
    [SET_EQUIPMENT] (state, equipment) {
        state.equipment = equipment;
    }
}

export default mutations