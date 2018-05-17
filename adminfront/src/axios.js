import axios from 'axios'
import cookie from 'js-cookie'
import { isTokenExpired, refreshToken } from '@/utils/jwt'
import store from '@/store'
import router from '@/router'
import config from '@/config'

const instance = axios.create({
    baseURL: config.apiURL,
});

const token = cookie.get('auth.token');


if (token !== undefined) {
    instance.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

instance.interceptors.response.use(function(response) {
    return response;
}, function(error) {
    if(error.response.status === 401) {
        store.dispatch('resetUser');
    }
});

export default instance