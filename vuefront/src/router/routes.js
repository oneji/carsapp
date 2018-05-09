import AdminLayout from '@/layouts/AdminLayout'
import Login from '@/pages/login'
import Home from '@/pages/index'
import Companies from '@/pages/companies'
import Stos from '@/pages/stos'

const routes = [        
    { path: '/login', name: 'Login', component: Login },
    { path: '/', component: AdminLayout, 
        children: [
            { path: '/', name: 'Index', component: Home },
            { path: '/companies', name: 'Companies', component: Companies },
            { path: '/stos', name: 'Stos', component: Stos },
        ]
    },
]

export default routes