import AdminLayout from '@/layouts/AdminLayout'
import Login from '@/pages/login'
import Home from '@/pages/index'
import Companies from '@/pages/companies/index'
import Stos from '@/pages/stos/index'
import UsersIndex from '@/pages/users/index'
import UsersCreate from '@/pages/users/create'
import ACL from '@/pages/acl/index'

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/', component: AdminLayout, 
        children: [
            { path: '/', name: 'Index', component: Home, meta: { requiresAuth: true, } },
            { path: '/companies', name: 'Companies', component: Companies, meta: { requiresAuth: true, } },
            { path: '/stos', name: 'Stos', component: Stos, meta: { requiresAuth: true } },
            { path: '/users', name: 'Users', component: UsersIndex, meta: { requiresAuth: true } },
            { path: '/users/create', name: 'UsersCreate', component: UsersCreate, meta: { requiresAuth: true } },
            { path: '/acl', name: 'ACL', component: ACL, meta: { requiresAuth: true } },
        ],
        meta: { requiresAuth: true },
    },
]

export default routes