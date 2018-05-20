import CompanyLayout from '@/layouts/CompanyLayout'
import Login from '@/pages/login'
import Home from '@/pages/index'
import CompanyHome from '@/pages/company/index'

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/', component: CompanyLayout, 
        children: [
            { path: '/', name: 'Index', component: Home, meta: { requiresAuth: true, } },
            { path: '/c/:slug', name: 'CompanyHome', component: CompanyHome, meta: { requiresAuth: true, forCompany: true } },
        ],
        meta: { requiresAuth: true },
    },
]

export default routes