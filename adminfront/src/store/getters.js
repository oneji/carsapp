const getters = {
    user: state => {
        return state.user;
    },

    isLogged: state => {
        return state.isLogged;
    },

    token: state => {
        return state.token;
    }
}

export default getters