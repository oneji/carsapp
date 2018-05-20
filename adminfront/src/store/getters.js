const getters = {
    user: state => {
        return state.user;
    },

    isLogged: state => {
        return state.isLogged;
    },

    userToEdit: state => {
        return state.userToEdit;
    }
}

export default getters