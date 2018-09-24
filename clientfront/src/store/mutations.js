import * as mutationTypes from './mutation-types'
import router from '@/router'

const mutations = {
    [mutationTypes.SET_USER] (state, user) {
        state.user = user;
        state.isLogged = true;
    },

    [mutationTypes.RESET_USER] (state) {
        state.user = null;
        state.isLogged = false;
        state.permissions = null;
    },

    [mutationTypes.REFRESH_TOKEN] (state, token) {
        state.token = token;
    },
    
    [mutationTypes.SHOW_SNACKBAR] (state, options) {
        state.snackbar = options;
    },

    [mutationTypes.HIDE_SNACKBAR] (state) {
        state.snackbar.active = false;
    },

    [mutationTypes.SET_CAR] (state, car) {
        state.car = car;
    },
    
    [mutationTypes.SET_DEFECT_ACT] (state, act) {
        state.defectAct = act;
    },

    [mutationTypes.SET_DEFECT_TYPES] (state, defectTypes) {
        state.defectTypes = defectTypes;
    },
 
    [mutationTypes.SET_EQUIPMENT] (state, equipment) {
        state.equipment = equipment;
    },

    [mutationTypes.SET_PERMISSIONS] (state, permissions) {
        state.permissions = permissions;
    },

    [mutationTypes.SET_CONSUMABLE] (state, consumable) {
        state.consumable  = consumable;
    }
}

export default mutations