import axios from '@/axios'
import cookie from 'js-cookie'
import router from '@/router'
import * as mTypes from './mutation-types'

const actions = {
    fetch({ commit }) {        
        return new Promise((resolve, reject) => {
            axios.get('/me')
                .then(response => { 
                    commit(mTypes.SET_USER, response.data.user);
                    cookie.set('admin.user', JSON.stringify(response.data.user), { expires: 1 });
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
                        commit(mTypes.SET_USER, response.data.user);
                        cookie.set('auth.token', response.data.token, { expires: 1 });
                        cookie.set('admin.user', JSON.stringify(response.data.user), { expires: 1 });
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
                commit(mTypes.RESET_USER);
                cookie.remove('auth.token');
                cookie.remove('admin.user')
            })
            .catch(error => console.log(error));
    },

    resetUser({ commit }) {
        commit(mTypes.RESET_USER); 
        cookie.remove('auth.token');
        cookie.remove('admin.user')       
    },

    userToEdit({ commit }, user) {
        return new Promise((resolve, reject) => {
            commit(mTypes.USER_TO_EDIT, user);
            resolve();
        })
    }
}

export default actions