import store from '@/store'

export function checkPermissions(perms) {
    let allow = false;
    let permissions = perms || [];
    let userPermissions = store.getters.permissions;

    
    if(permissions.length === 0) {
        return true;
    } else {
        permissions.map(permission => {
            userPermissions.map(userPermission => {
                if(permission === userPermission) {
                    allow = true;   
                }
            })
        }) 

        return allow;
    } 
}