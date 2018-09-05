import Vue from 'vue'
import Router from 'vue-router'
import routes from './routes'
import store from '@/store'
import cookie from 'js-cookie'
import api from '@/api'
import { checkPermissions } from '@/utils/permissions'

Vue.use(Router)

const router = new Router({
    routes,
})

router.beforeEach((to, from, next) => {       
    const token = cookie.get('auth.client.token');
    const user = window.localStorage.getItem('user');

    if(to.name.toLowerCase() !== 'login')  {
        if(token === undefined || user === null) {
            store.dispatch('resetUser')
            next('/login');
        }

        if(store.getters.permissions.length === 0) {
            api.getUserPermissions().then(response => {
                store.dispatch('setPermissions', response.data.rolePermissions)
                next()
            })
            .catch(error => {
                console.log(error);
                next()
            })
        } else {
            if(checkPermissions(to.meta.permissions)) {
                next()
            } else {
                next('/403')
            }
        }   
    } else {
        next()
    } 
});

export default router
  
  
