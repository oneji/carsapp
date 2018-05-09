import axios from 'axios'
import cookie from 'js-cookie'

const instance = axios.create({
    baseURL: process.env.API_URL,
});

const token = cookie.get('auth.token');

if (token !== undefined) {
    instance.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

instance.interceptors.request.use(function (config) {
    let token = cookie.get('auth.token');
    if(token !== undefined) {
        
    }

    return config;
});

export default instance