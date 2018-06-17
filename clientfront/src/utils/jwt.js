import axios from '@/axios'
import VueJwtDecode from 'vue-jwt-decode'

export function refreshToken(token) {
    return new Promise((resolve, reject) => {
        axios.get('/token/refresh').then((response) => {
            resolve(response);
        })
        .catch(error => reject(error));
    });  
}

export function isTokenExpired(token) {
    const parsedToken = VueJwtDecode.decode(token.split(' ')[1]);
    let expirationTime = new Date(parsedToken.exp * 1000);    
    let todayTime = new Date(Date.now());    
    let issueTime = new Date(parsedToken.iat * 1000);

    if(expirationTime < todayTime) {
        return true;
    } else {
        return false;
    }
}