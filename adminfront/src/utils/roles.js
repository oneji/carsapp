import store from '@/store'

export function hasRole(userRoles, routeRoles) {
    for(let i = 0; i < routeRoles.length; i++) {
        for(let j = 0; j < userRoles.length; j++) {
            if(userRoles[j].name === routeRoles[i]) {
                return true
            }
        }
    }
    
    return false
}