import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import getters from './getters'
import mutations from './mutations'
import actions from './actions'

export default new Vuex.Store({
    state: {
        user: null,
        isLogged: false,
        car: {
            brand_name: '',
            drivers: [],
            engine_capacity: '',
            engine_type_name: '',
            milage: '',
            model_name: '',
            number: '',
            shape_name: '',
            transmission_name: '',
            vin_code: '',
            year: ''
        },
        defectAct: {
            car_card_id: '',
            defect_act_date: '',
            defect_options: [],
            equipment: []
        },
        
    },
    getters,
    mutations,
    actions
})