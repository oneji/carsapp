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
    }
}

export default getters