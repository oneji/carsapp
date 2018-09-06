import axios from '@/axios'
import cookie from 'js-cookie'
import router from '@/router'
import * as types from './mutation-types'
import api from '@/api'

const actions = {
    fetch({ commit }) {        
        return new Promise((resolve, reject) => {
            axios.get('/me')
                .then(response => { 
                    commit(types.SET_USER, response.data.user);
                    window.localStorage.setItem('user', JSON.stringify(response.data.user));
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
            api.login(user).then(response => { 
                if(response.status === 200) { 
                    if(response.data.success) {
                        commit(types.SET_USER, response.data.user);

                        cookie.set('auth.client.token', response.data.token, { expires: 1 });
                        window.localStorage.setItem('user', JSON.stringify(response.data.user));
                        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
                        resolve(response);
                    } else {
                        reject(response);
                    }                                      
                } else {
                    reject(response);
                }
            });
        });
    },

    logout({ commit }) {
        api.logout().then(() => {
            commit(types.RESET_USER);
            cookie.remove('auth.client.token');
            window.localStorage.removeItem('user');
            router.push('/login');
        })
        .catch(error => console.log(error));
    },

    resetUser({ commit }) {
        commit(types.RESET_USER); 
        cookie.remove('auth.client.token');
        window.localStorage.removeItem('user');
    },

    setCar({ commit }, car) {
        commit(types.SET_CAR, car);
    },

    setDefectAct({ commit }, act) {
        commit(types.SET_DEFECT_ACT, act);
    },

    setDefectTypes({ commit }, defectTypes) {
        commit(types.SET_DEFECT_TYPES, defectTypes);
    },

    setEquipment({ commit }, equipment) {
        commit(types.SET_EQUIPMENT, equipment);
    },

    setPermissions({ commit }) {
        return new Promise((resolve, reject) => {
            api.getUserPermissions().then(response => {
                commit(types.SET_PERMISSIONS, response.data.rolePermissions);

                resolve();
            })
            .catch(error => reject(error));
        });        
    },

    setConsumable({ commit }, consumable) {
        commit(types.SET_CONSUMABLE, consumable);
    }
}

export default actions