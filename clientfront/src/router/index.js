import Vue from 'vue'
import Router from 'vue-router'
import routes from './routes'
import { hasRole } from '@/utils/roles'
import store from '@/store' 
import cookie from 'js-cookie'

Vue.use(Router)

const router = new Router({
    routes,
})

router.beforeEach((to, from, next) => {    
    const token = cookie.get('auth.client.token');    

    if(to.name.toLowerCase() !== 'login')  {
        if(token === undefined) {
            next('/login');
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
  
  
