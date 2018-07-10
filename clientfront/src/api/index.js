import axios from '@/axios'

const api = {
    login(user) {
        return axios.post('/auth/login', {
            'email': user.email,
            'password': user.password,
            'type': user.type
        })
    },

    logout() {
        return axios.post('/auth/logout')
    },

    getUserPermissions() {
        return axios.get('/user/acl')
    }
}

export default api