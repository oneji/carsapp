const getters = {
    user: state => {
        return state.user;
    },

    isLogged: state => {
        return state.isLogged;
    },

    car: state => {
        return state.car;
    },

    defectAct: state => {
        return state.defectAct;
    },

    defectTypes: state => {
        return state.defectTypes;
    },

    equipment: state => {
        return state.equipment;
    }
}

export default getters