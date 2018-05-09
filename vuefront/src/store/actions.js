import axios from '../axios'
import cookie from 'js-cookie'

const actions = {
    fetch({ commit }) {        
        axios.get('/me')
            .then(response => { 
                commit('setUser', response.data.user);
            })
            .catch(error => {
                console.log(error);
            });
    },

    login({ commit }, user) {
        return new Promise((resolve, reject) => {
            axios.post('/auth/login', {
                'email': user.email,
                'password': user.password
            })
            .then(response => {   
                commit('setUser', response.data.user);
                cookie.set('auth.token', response.data.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;

                resolve(response);
            })
            .catch(error => {
                commit('resetUser');
                cookie.remove('auth.token');

                reject(error);
            });
        });
    },

    logout({ commit }) {
        return new Promise((resolve, reject) => {
            axios.post('/auth/logout')
                .then(response => {
                    commit('resetUser');
                    cookie.remove('auth.token');
                    
                    resolve();
                })
                .catch(error => {
                    reject(error);
                })
        });
    },

    resetUser({ commit }) {
        commit('resetUser');
    }
}

export default actions