import HomeLayout               from '@/layouts/HomeLayout'
import Login                    from '@/pages/login'
// Home pages   
import Home                     from '@/pages/home/index'
import HomeCompanies            from '@/pages/home/companies/index'
import HomeCars                 from '@/pages/home/cars/index'
import HomeReservedCars         from '@/pages/home/cars/reserved'
import HomeDriversQueue         from '@/pages/home/drivers/queue'
import HomeUsersIndex           from '@/pages/home/users/index'
import HomeUserCreate           from '@/pages/home/users/create'
import HomeUserEdit             from '@/pages/home/users/edit'
// Company pages    
import CompanyHome              from '@/pages/company/index'
import CompanyCars              from '@/pages/company/cars/index'
import CompanySoldCars          from '@/pages/company/cars/sold'
import CompanyCarsCard          from '@/pages/company/cars/card'
import CompanyCarsCreate        from '@/pages/company/cars/create'
import CompanyCarsEdit          from '@/pages/company/cars/edit'
import CompanyDrivers           from '@/pages/company/drivers/index'
import CompanyDriversCreate     from '@/pages/company/drivers/create'
import CompanyDriversEdit       from '@/pages/company/drivers/edit'
import CompanyStoRequests       from '@/pages/company/requests/index'
import CompanyRepairRequests    from '@/pages/company/requests/repair'
import CompanyArchivedRequests  from '@/pages/company/requests/archived'
// STO pages    
import StoHome                  from '@/pages/sto/index'
import StoRequests              from '@/pages/sto/requests/index'
import StoCompanies             from '@/pages/sto/companies/index'
import StoCompany               from '@/pages/sto/companies/company'
import StoPricelist             from '@/pages/sto/pricelist/index'
import StoServices              from '@/pages/sto/services/index'
import StoDefects               from '@/pages/sto/defects/index'
import StoCarCard               from '@/pages/sto/companies/cars/card'
import StoCarCreate             from '@/pages/sto/companies/cars/create'
import StoRepairRequests        from '@/pages/sto/requests/repair'
// Error pages
import NotFound                 from '@/pages/errors/404'
import NoAccess                 from '@/pages/errors/403'

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/403', name: 'NoAccess', component: NoAccess },
    { path: '/404', name: 'NotFound', component: NotFound },
    // Home routes
    { path: '/', component: HomeLayout, meta: { requiresAuth: true },
        children: [
            { path: '/', name: 'Home', component: Home, meta: { requiresAuth: true, } },
            { path: '/companies', name: 'HomeCompanies', component: HomeCompanies, meta: { requiresAuth: true, } },
            { path: '/cars', name: 'HomeCars', component: HomeCars, meta: { requiresAuth: true, } },            
            { path: '/cars/reserved', name: 'HomeReservedCars', component: HomeReservedCars, meta: { requiresAuth: true, } },            
            { path: '/drivers/queue', name: 'HomeDriversQueue', component: HomeDriversQueue, meta: { requiresAuth: true, } },            
            { path: '/users', name: 'HomeUsers', component: HomeUsersIndex, 
                meta: { 
                    requiresAuth: true,
                    permissions: ['read-users'] 
                } 
            },
            { path: '/users/create', name: 'HomeUserCreate', component: HomeUserCreate, meta: { requiresAuth: true } },
            { path: '/users/edit/:id', name: 'HomeUserEdit', component: HomeUserEdit, meta: { requiresAuth: true } },            
        ],
    },
    // Company routes
    { path: '/c/:slug', name: 'CompanyHome', component: CompanyHome, meta: { requiresAuth: true },
        children: [
            { path: 'cars', name: 'CompanyCars', component: CompanyCars, meta: { requiresAuth: true } },
            { path: 'cars/:car/card', name: 'CompanyCarsCard', component: CompanyCarsCard, meta: { requiresAuth: true } },
            { path: 'cars/sold', name: 'CompanySoldCars', component: CompanySoldCars, meta: { requiresAuth: true } },
            { path: 'cars/create', name: 'CompanyCarsCreate', component: CompanyCarsCreate, meta: { requiresAuth: true } },
            { path: 'cars/:car/edit', name: 'CompanyCarsEdit', component: CompanyCarsEdit, meta: { requiresAuth: true } },
            { path: 'drivers', name: 'CompanyDrivers', component: CompanyDrivers, meta: { requiresAuth: true } },
            { path: 'drivers/create', name: 'CompanyDriversCreate', component: CompanyDriversCreate, meta: { requiresAuth: true } },
            { path: 'drivers/:driver/edit', name: 'CompanyDriversEdit', component: CompanyDriversEdit, meta: { requiresAuth: true } },
            { path: 'sto-list', name: 'CompanyStoRequests', component: CompanyStoRequests, meta: { requiresAuth: true } },
            { path: 'requests/repair', name: 'CompanyRepairRequests', component: CompanyRepairRequests, meta: { requiresAuth: true } },
            { path: 'requests/archive', name: 'CompanyArchivedRequests', component: CompanyArchivedRequests, meta: { requiresAuth: true } },
        ]
    },
    // Sto routes
    { path: '/s/:slug', name: 'StoHome', component: StoHome, meta: { requiresAuth: true },
        children: [
            { path: 'c-list', name: 'StoRequests', component: StoRequests, meta: { requiresAuth: true } },
            { path: 'requests/repair', name: 'StoRepairRequests', component: StoRepairRequests, meta: { requiresAuth: true } },
            { path: 'companies', name: 'StoCompanies', component: StoCompanies, meta: { requiresAuth: true } },
            { path: 'companies/:company', name: 'StoCompany', component: StoCompany, meta: { requiresAuth: true } },
            { path: 'companies/:company/cars/:car/card', name: 'StoCarCard', component: StoCarCard, meta: { requiresAuth: true } },
            { path: 'companies/:company/cars/create', name: 'StoCarCreate', component: StoCarCreate, meta: { requiresAuth: true } },

            { path: 'pricelist', name: 'StoPricelist', component: StoPricelist, meta: { requiresAuth: true } },
            { path: 'services', name: 'StoServices', component: StoServices, meta: { requiresAuth: true } },
            { path: 'defects', name: 'StoDefects', component: StoDefects, meta: { requiresAuth: true } },
        ]
    }
]

export default routes