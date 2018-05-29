import HomeLayout from '@/layouts/HomeLayout'
import CompanyLayout from '@/layouts/CompanyLayout'
import Login from '@/pages/login'
import Home from '@/pages/index'
// Company pages
import CompanyHome from '@/pages/company/index'
import CompanyCars from '@/pages/company/cars/index'
import CompanyCarsCreate from '@/pages/company/cars/create'
import CompanyCarsBody from '@/pages/company/cars/body'
import CompanyDrivers from '@/pages/company/drivers/index'
import CompanyDriversCreate from '@/pages/company/drivers/create'
// STO pages

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/', component: HomeLayout, meta: { requiresAuth: true },
        children: [
            { path: '/', name: 'Home', component: Home, meta: { requiresAuth: true, } },
        ],
    },
    { path: '/c/:slug', name: 'CompanyHome', component: CompanyHome, meta: { requiresAuth: true },
        children: [
            { path: 'cars', name: 'CompanyCars', component: CompanyCars, meta: { requiresAuth: true } },
            { path: 'cars/create', name: 'CompanyCarsCreate', component: CompanyCarsCreate, meta: { requiresAuth: true } },
            { path: 'cars/body', name: 'CompanyCarsBody', component: CompanyCarsBody, meta: { requiresAuth: true } },
            { path: 'drivers', name: 'CompanyDrivers', component: CompanyDrivers, meta: { requiresAuth: true } },
            { path: 'drivers/create', name: 'CompanyDriversCreate', component: CompanyDriversCreate, meta: { requiresAuth: true } },
        ]
    }
]

export default routes