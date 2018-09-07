import Vue from 'vue'
import store from '@/store'

export const Can = {
    bind(el, binding) {
        // Parse permissions from directive
        let permissions = binding.value || [];
        
        let userPermissions = store.getters.permissions;
        let block = true;       

        if(permissions.length === 0)
            block = false;
        // Check if a user has the required permission
        for(let i = 0; i < permissions.length; i++) {
            let permission = permissions[i];
            for(let j = 0; j < userPermissions.length; j++) {
                let userPermission = userPermissions[j];
                if(permission === userPermission) {
                    block = false;
                }
            }
        }

        // if not then delete an element 
        if(block) {
            el.remove();
        }
    }
}

Vue.directive('can', Can)