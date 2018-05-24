import HomeLayout from '@/layouts/HomeLayout'
import CompanyLayout from '@/layouts/CompanyLayout'
import Login from '@/pages/login'
import Home from '@/pages/index'
// Company pages
import CompanyHome from '@/pages/company/index'
import CompanyCars from '@/pages/company/cars/index'
import CompanyCarsCreate from '@/pages/company/cars/create'
import CompanyCarsShapes from '@/pages/company/cars/shapes'
import CompanyCarsModels from '@/pages/company/cars/models'
import CompanyCarsBrands from '@/pages/company/cars/brands'
import CompanyDrivers from '@/pages/company/drivers/index'
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
            { path: 'cars/shapes', name: 'CompanyCarsShapes', component: CompanyCarsShapes, meta: { requiresAuth: true } },
            { path: 'cars/models', name: 'CompanyCarsModels', component: CompanyCarsModels, meta: { requiresAuth: true } },
            { path: 'cars/brands', name: 'CompanyCarsBrands', component: CompanyCarsBrands, meta: { requiresAuth: true } },
            { path: 'drivers', name: 'CompanyDrivers', component: CompanyDrivers, meta: { requiresAuth: true } },
        ]
    }
]

export default routes