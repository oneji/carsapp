import Vue from 'vue'
import Router from 'vue-router'
import routes from './routes'
import { hasRole } from '@/utils/roles'
import store from '@/store' 
import cookie from 'js-cookie'

Vue.use(Router)

const router = new Router({
    mode: 'history',
    routes,
})

router.beforeEach((to, from, next) => {    
    const token = cookie.get('auth.token');

    if(to.name.toLowerCase() !== 'login')  {
        store.dispatch('fetch')
            .then(() => {
                const user = store.getters.user;
                if(to.meta.roles !== undefined) {                
                    let requiredRoles = to.meta.roles.split('|');
            
                    if(hasRole(user.roles, requiredRoles)) {
                        next()
                    } else {
                        console.log('Route is not allowed');
                    }
                }
            })        
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
  
  
