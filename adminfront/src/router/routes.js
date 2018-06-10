import AdminLayout from '@/layouts/AdminLayout'
import Login from '@/pages/login'
import Home from '@/pages/index'
import Companies from '@/pages/companies/index'
import Stos from '@/pages/stos/index'
import UsersIndex from '@/pages/users/index'
import UserCreate from '@/pages/users/create'
import UserEdit from '@/pages/users/edit'
import ACL from '@/pages/acl/index'
import CarBody from '@/pages/cars/body'

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/', component: AdminLayout, meta: { requiresAuth: true },
        children: [
            { path: '/', name: 'Index', component: Home, meta: { requiresAuth: true, } },
            { path: '/companies', name: 'Companies', component: Companies, meta: { requiresAuth: true, } },
            { path: '/stos', name: 'Stos', component: Stos, meta: { requiresAuth: true } },
            { path: '/users', name: 'Users', component: UsersIndex, meta: { requiresAuth: true } },
            { path: '/users/create', name: 'UserCreate', component: UserCreate, meta: { requiresAuth: true } },
            { path: '/users/edit/:id', name: 'UserEdit', component: UserEdit, meta: { requiresAuth: true } },
            { path: '/acl', name: 'ACL', component: ACL, meta: { requiresAuth: true } },
            { path: '/car-body', name: 'CarBody', component: CarBody, meta: { requiresAuth: true } },
        ],
    },
]

export default routes