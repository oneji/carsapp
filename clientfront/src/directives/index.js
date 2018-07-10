import Vue from 'vue'
import store from '@/store'

export const Can = {
    bind(el, binding) {
        // Parse permissions from directive
        let permissions = binding.arg.split(':');
        
        let userPermissions = store.getters.permissions;
        let block = true;
        // Check if a user has required permission
        permissions.map(permission => {
            userPermissions.map(userPermission => {
                if(permission === userPermission) {
                    block = false;
                }
            })
        }) 
        // if not then delete an element 
        if(block)
            el.remove();
    }
}

Vue.directive('can', Can)