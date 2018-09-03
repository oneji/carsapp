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
        // Check if a user has a required permission
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