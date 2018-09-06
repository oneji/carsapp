import store from '@/store'

export function checkPermissions(perms) {
    let allow = false;
    const permissions = perms || [];
    const userPermissions = store.getters.permissions;

    if(permissions.length === 0) {
        return true;
    } else {
        for(let i = 0; i < permissions.length; i++) {
            let permission = permissions[i];
            for(let j = 0; j < userPermissions.length; j++) {
                let userPermission = userPermissions[j];

                if(permission === userPermission) {
                    allow = true;
                }
            }
        }

        return allow;
    } 
}