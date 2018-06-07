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
import CompanyStoRequests from '@/pages/company/requests/index'
// STO pages
import StoHome from '@/pages/sto/index'
import StoRequests from '@/pages/sto/requests/index'
import StoCompanies from '@/pages/sto/companies/index'
import StoCompany from '@/pages/sto/companies/company'
import StoPricelist from '@/pages/sto/pricelist/index'
import StoServices from '@/pages/sto/services/index'

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/', component: HomeLayout, meta: { requiresAuth: true },
        children: [
            { path: '/', name: 'Home', component: Home, meta: { requiresAuth: true, } },
        ],
    },
    // Company routes
    { path: '/c/:slug', name: 'CompanyHome', component: CompanyHome, meta: { requiresAuth: true },
        children: [
            { path: 'cars', name: 'CompanyCars', component: CompanyCars, meta: { requiresAuth: true } },
            { path: 'cars/create', name: 'CompanyCarsCreate', component: CompanyCarsCreate, meta: { requiresAuth: true } },
            { path: 'cars/body', name: 'CompanyCarsBody', component: CompanyCarsBody, meta: { requiresAuth: true } },
            { path: 'drivers', name: 'CompanyDrivers', component: CompanyDrivers, meta: { requiresAuth: true } },
            { path: 'drivers/create', name: 'CompanyDriversCreate', component: CompanyDriversCreate, meta: { requiresAuth: true } },
            { path: 'requests', name: 'CompanyStoRequests', component: CompanyStoRequests, meta: { requiresAuth: true } },
        ]
    },
    // Sto routes
    { path: '/s/:slug', name: 'StoHome', component: StoHome, meta: { requiresAuth: true },
        children: [
            { path: 'requests', name: 'StoRequests', component: StoRequests, meta: { requiresAuth: true } },
            { path: 'companies', name: 'StoCompanies', component: StoCompanies, meta: { requiresAuth: true } },
            { path: 'companies/:company', name: 'StoCompany', component: StoCompany, meta: { requiresAuth: true } },
            { path: 'pricelist', name: 'StoPricelist', component: StoPricelist, meta: { requiresAuth: true } },
            { path: 'services', name: 'StoServices', component: StoServices, meta: { requiresAuth: true } },
        ]
    }
]

export default routes