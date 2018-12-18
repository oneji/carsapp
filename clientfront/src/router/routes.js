const HomeLayout               = () => import('@/layouts/HomeLayout')
const Login                    = () => import('@/pages/login')
// Admin pages
const AdminACL                 = () => import('@/pages/admin/acl/index')
const AdminUsers               = () => import ('@/pages/admin/users/index')
const AdminUserCreate          = () => import ('@/pages/admin/users/create')
const AdminUserEdit            = () => import ('@/pages/admin/users/edit')
const AdminCompanies           = () => import ('@/pages/admin/companies/index')
// Home pages   
const Home                     = () => import ('@/pages/home/index')
const HomeCompanies            = () => import ('@/pages/home/companies/index')
const HomeCars                 = () => import ('@/pages/home/cars/index')
const HomeReservedCars         = () => import ('@/pages/home/cars/reserved')
const HomeDriversIndex         = () => import ('@/pages/home/drivers/index')
const HomeDriversQueue         = () => import ('@/pages/home/drivers/queue')
// Company pages    
const CompanyHome              = () => import ('@/pages/company/index')
const CompanyCars              = () => import ('@/pages/company/cars/index')
const CompanySoldCars          = () => import ('@/pages/company/cars/sold')
const CompanyCarsCard          = () => import ('@/pages/company/cars/card')
const CompanyCarsCreate        = () => import ('@/pages/company/cars/create')
const CompanyCarsEdit          = () => import ('@/pages/company/cars/edit')
const CompanyDrivers           = () => import ('@/pages/company/drivers/index')
const CompanyDriversCreate     = () => import ('@/pages/company/drivers/create')
const CompanyDriversEdit       = () => import ('@/pages/company/drivers/edit')
const CompanyStoRequests       = () => import ('@/pages/company/requests/index')
const CompanyRepairRequests    = () => import ('@/pages/company/requests/repair')
const CompanyArchivedRequests  = () => import ('@/pages/company/requests/archived')
const CompanyDefectAct         = () => import ('@/pages/company/defect_act/index')
const CompanyDoneAct           = () => import ('@/pages/company/done_acts/index')
// STO pages
const StoLayout                = () => import ('@/pages/sto/index')
const StoHome                  = () => import ('@/pages/sto/home/index')
const StoRequests              = () => import ('@/pages/sto/requests/index')
const StoCompanies             = () => import ('@/pages/sto/companies/index')
const StoCompany               = () => import ('@/pages/sto/companies/company')
const StoPricelist             = () => import ('@/pages/sto/pricelist/index')
const StoServices              = () => import ('@/pages/sto/services/index')
const StoDefects               = () => import ('@/pages/sto/defects/index')
const StoCarCard               = () => import ('@/pages/sto/companies/cars/card')
const StoCarCreate             = () => import ('@/pages/sto/companies/cars/create')
const StoRepairRequests        = () => import ('@/pages/sto/requests/repair')
const StoRTActIndex            = () => import ('@/pages/sto/rt_act/index')
const StoRTAct                 = () => import ('@/pages/sto/rt_act/crud')
const StoCreateRTAct           = () => import ('@/pages/sto/rt_act/create')
const StoEditRTAct             = () => import ('@/pages/sto/rt_act/edit')
const StoCreateDefectAct       = () => import ('@/pages/sto/defect_act/create')
const StoDefectAct             = () => import ('@/pages/sto/defect_act/index')
const StoDoneAct               = () => import ('@/pages/sto/done_acts/index')
const StoRepairedCars          = () => import ('@/pages/sto/repaired_cars/index')
// Error pages
const NotFound                 = () => import ('@/pages/errors/404')
const NoAccess                 = () => import ('@/pages/errors/403')

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/403', name: 'NoAccess', component: NoAccess },
    { path: '/404', name: 'NotFound', component: NotFound },
    // Home layout
    { path: '/', component: HomeLayout, meta: { requiresAuth: true },
        children: [
            // Admin routes
            { path: '/a/acl', name: 'AdminACL', component: AdminACL, 
                meta: { 
                    requiresAuth: true,
                    permissions: ['read-acl'],
                    type: 'admin'
                }, 
            },
            { path: '/a/users', name: 'AdminUsers', component: AdminUsers, 
                meta: { 
                    requiresAuth: true,
                    permissions: ['read-users'],
                    type: 'admin'
                } 
            },
            { path: '/a/users/create', name: 'AdminUserCreate', component: AdminUserCreate, 
                meta: { 
                    requiresAuth: true,
                    permissions: ['create-users'],
                    type: 'admin'
                } 
            },
            { path: '/a/users/edit/:id', name: 'AdminUserEdit', component: AdminUserEdit, 
                meta: { 
                    requiresAuth: true,
                    permissions: ['update-users'],
                    type: 'admin'
                } 
            },            
            { path: '/a/companies', name: 'AdminCompanies', component: AdminCompanies, 
                meta: { 
                    requiresAuth: true,
                    permissions: ['read-companies'],
                    type: 'admin'
                } 
            },
            // Home routes
            { path: '/', name: 'Home', component: Home, meta: { requiresAuth: true, type: 'home' } },
            { path: '/companies', name: 'HomeCompanies', component: HomeCompanies, meta: { requiresAuth: true, type: 'home' } },
            { path: '/cars', name: 'HomeCars', component: HomeCars, meta: { requiresAuth: true, type: 'home' } },            
            { path: '/cars/reserved', name: 'HomeReservedCars', component: HomeReservedCars, meta: { requiresAuth: true, type: 'home' } },            
            { path: '/drivers', name: 'HomeDriversIndex', component: HomeDriversIndex, meta: { requiresAuth: true, type: 'home' } },            
            { path: '/drivers/queue', name: 'HomeDriversQueue', component: HomeDriversQueue, meta: { requiresAuth: true, type: 'home' } },            
        ],
    },
    // Company routes
    { path: '/c/:slug', name: 'CompanyHome', component: CompanyHome, meta: { requiresAuth: true, type: 'company' },
        children: [
            { path: 'cars', name: 'CompanyCars', component: CompanyCars, meta: { requiresAuth: true, type: 'company' } },
            { path: 'cars/:car/card', name: 'CompanyCarsCard', component: CompanyCarsCard, meta: { requiresAuth: true, type: 'company' } },
            { path: 'cars/sold', name: 'CompanySoldCars', component: CompanySoldCars, meta: { requiresAuth: true, type: 'company' } },
            { path: 'cars/create', name: 'CompanyCarsCreate', component: CompanyCarsCreate, meta: { requiresAuth: true, type: 'company' } },
            { path: 'cars/:car/edit', name: 'CompanyCarsEdit', component: CompanyCarsEdit, meta: { requiresAuth: true, type: 'company' } },
            { path: 'drivers', name: 'CompanyDrivers', component: CompanyDrivers, meta: { requiresAuth: true, type: 'company' } },
            { path: 'drivers/create', name: 'CompanyDriversCreate', component: CompanyDriversCreate, meta: { requiresAuth: true, type: 'company' } },
            { path: 'drivers/:driver/edit', name: 'CompanyDriversEdit', component: CompanyDriversEdit, meta: { requiresAuth: true, type: 'company' } },
            { path: 'sto-list', name: 'CompanyStoRequests', component: CompanyStoRequests, meta: { requiresAuth: true, type: 'company' } },
            { path: 'requests/repair', name: 'CompanyRepairRequests', component: CompanyRepairRequests, meta: { requiresAuth: true, type: 'company' } },
            { path: 'requests/archive', name: 'CompanyArchivedRequests', component: CompanyArchivedRequests, meta: { requiresAuth: true, type: 'company' } },
            { path: 'defect-acts/:act', name: 'CompanyDefectAct', component: CompanyDefectAct, meta: { requiresAuth: true, type: 'company' } },            
            { path: 'done-acts/:act', name: 'CompanyDoneAct', component: CompanyDoneAct, meta: { requiresAuth: true, type: 'company' } },            
        ]
    },
    // Sto routes
    { path: '/s', name: 'StoLayout', component: StoLayout, meta: { requiresAuth: true, type: 'sto' },
        children: [
            { path: ':slug', name: 'StoHome', component: StoHome, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/c-list', name: 'StoRequests', component: StoRequests, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/requests/repair', name: 'StoRepairRequests', component: StoRepairRequests, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/companies', name: 'StoCompanies', component: StoCompanies, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/companies/:company', name: 'StoCompany', component: StoCompany, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/companies/:company/cars/:car/card', name: 'StoCarCard', component: StoCarCard, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/companies/:company/cars/create', name: 'StoCarCreate', component: StoCarCreate, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/companies/:company/cars/:car/edit', name: 'StoCarsEdit', component: CompanyCarsEdit, meta: { requiresAuth: true, type: 'sto' } },
            
            { path: ':slug/pricelist', name: 'StoPricelist', component: StoPricelist, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/services', name: 'StoServices', component: StoServices, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/defects', name: 'StoDefects', component: StoDefects, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/rt-act/crud', name: 'StoRTAct', component: StoRTAct, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/rt-act/:act', name: 'StoRTActIndex', component: StoRTActIndex, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/companies/:company/cars/:car/card/rt-act/create', name: 'StoCreateRTAct', component: StoCreateRTAct, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/companies/:company/cars/:car/card/rt-act/:act/edit', name: 'StoEditRTAct', component: StoEditRTAct, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/companies/:company/cars/:car/card/defect-act/create', name: 'StoCreateDefectAct', component: StoCreateDefectAct, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/defect-act/:act', name: 'StoDefectAct', component: StoDefectAct, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/done-act/:act', name: 'StoDoneAct', component: StoDoneAct, meta: { requiresAuth: true, type: 'sto' } },
            { path: ':slug/repaired-cars', name: 'StoRepairedCars', component: StoRepairedCars, meta: { requiresAuth: true, type: 'sto' } },
        ]
    }
]

export default routes