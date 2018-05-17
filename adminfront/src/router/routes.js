import AdminLayout from '@/layouts/AdminLayout'
import Login from '@/pages/login'
import Home from '@/pages/index'
import Companies from '@/pages/companies'
import Stos from '@/pages/stos'
import Users from '@/pages/users'

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/', component: AdminLayout, 
        children: [
            { path: '/', name: 'Index', component: Home, meta: { requiresAuth: true, } },
            { path: '/companies', name: 'Companies', component: Companies, meta: { requiresAuth: true, } },
            { path: '/stos', name: 'Stos', component: Stos, meta: { requiresAuth: true } },
            { path: '/users', name: 'Users', component: Users, meta: { requiresAuth: true } },
        ],
        meta: { requiresAuth: true },
    },
]

export default routes