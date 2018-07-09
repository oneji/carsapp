import Vue from 'vue'
import Router from 'vue-router'
import routes from './routes'
import { hasRole } from '@/utils/roles'
import { isTokenExpired } from '@/utils/jwt'
import store from '@/store'
import cookie from 'js-cookie'

Vue.use(Router)

const router = new Router({
    routes,
})

router.beforeEach((to, from, next) => {    
    const token = cookie.get('auth.client.token');    
    const user = window.localStorage.getItem('user');    

    if(to.name.toLowerCase() !== 'login')  {
        if(token === undefined) {
            store.dispatch('resetUser')
            next('/login');
        }

        if(user === null) {
            store.dispatch('resetUser')
            next('/login')
        }

        if(token !== undefined) {
            if(!isTokenExpired(token)) {
                next()
            }            
            next()
        }

        next()     
    }

    next()   
});

router.beforeResolve((to, from, next) => {
    if (to.name) {
        NProgress.start()
    }
    next()
})
  
router.afterEach((to, from) => {
    NProgress.done()
})

export default router
  
  
