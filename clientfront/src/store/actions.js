import axios from '@/axios'
import cookie from 'js-cookie'
import router from '@/router'
import * as types from './mutation-types'

const actions = {
    fetch({ commit }) {        
        return new Promise((resolve, reject) => {
            axios.get('/me')
                .then(response => { 
                    commit('setUser', response.data.user);
                    cookie.set('user', JSON.stringify(response.data.user), { expires: 1 });
                    resolve(response);
                })
                .catch(error => {
                    console.log(error);
                    reject(error);
                });
        }) 
    },

    login({ commit }, user) {
        return new Promise((resolve, reject) => {
            axios.post('/auth/login', {
                'email': user.email,
                'password': user.password,
                'type': user.type
            })
            .then(response => { 
                if(response.status === 200) { 
                    if(response.data.success) {
                        commit('setUser', response.data.user);
                        cookie.set('auth.client.token', response.data.token, { expires: 1 });
                        cookie.set('user', JSON.stringify(response.data.user), { expires: 1 });
                        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
                        resolve(response);
                    } else {
                        reject(response);
                    }                                      
                }                 
            });
        });
    },

    logout({ commit }) {
        axios.post('/auth/logout')
            .then(response => {
                commit('resetUser');
                cookie.remove('auth.client.token');
                cookie.remove('user');
                router.push('/login');
            })
            .catch(error => console.log(error));
    },

    resetUser({ commit }) {
        commit('resetUser'); 
        cookie.remove('auth.client.token');
        cookie.remove('user')       
    },

    refreshToken({ commit }) {
        axios.get('/token/refresh')
            .then(response => {
                console.log(axios.defaults.headers.common);
            })
            .catch(error => console.log(error));
           
    },

    setCar({ commit }, car) {
        commit(types.SET_CAR, car);
    },

    setDefectAct({ commit }, act) {
        commit(types.SET_DEFECT_ACT, act);
    }
}

export default actions