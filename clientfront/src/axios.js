import axios from 'axios'
import cookie from 'js-cookie'
import { isTokenExpired, refreshToken } from './utils/jwt'
import store from './store'
import router from './router'
import config from './config'

const instance = axios.create({
    baseURL: config.apiURL,
});

const token = cookie.get('auth.client.token');

if (token !== undefined) {
    instance.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Response interceptor
instance.interceptors.response.use(function(response) {
    return response;
}, function(error) {
    if(error.response.status === 401) {
        store.dispatch('resetUser');
        router.push('/login');
    }

    if(error.response.status === 500) {
        store.dispatch('showSnackbar', {
            color: 'error',
            active: true,
            text: 'Неизвестная ошибка на сервере.'
        });
    }
});

export default instance