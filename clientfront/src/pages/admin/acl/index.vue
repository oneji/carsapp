<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="primary" @click.native="createRoleDialog = true" v-can="['create-role']">Создать роль</v-btn>
            </v-flex>
        </v-layout>
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>
        <v-layout row wrap v-if="!loading.page">
            <!-- Assign permissions to role -->
            <v-flex xs12 sm12 md12 lg12> 
                <v-form @submit.prevent="editRoles">
                    <v-card>                    
                        <v-tabs v-model="active" color="cyan" dark slider-color="yellow" show-arrows>
                            <v-tab v-for="role in roles" :key="role.id" ripple>
                                {{ role.display_name }}
                            </v-tab>
                            <v-tab-item v-for="role in roles" :key="role.id">
                                <v-card flat>
                                    <v-card-text>
                                        <div  >
                                            <v-checkbox :value="permission.id" 
                                                v-for="permission in acl.permissions" :key="permission.id" :label="permission.display_name" 
                                                v-model="role.permissions"
                                            ></v-checkbox>
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                        </v-tabs>

                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn block :loading="loading.editRole" color="success" @click="editRoles">Сохранить</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-form>
            </v-flex>

            <!-- Create a role -->
            <!-- <v-flex xs12 sm6 mg4 lg4>
                <v-card v-can="['create-role']">
                    <v-form @submit="createRole">
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Роль</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>
                        <v-card-title>
                            <v-container class="py-0 px-0">
                                <v-layout row wrap>
                                    <v-flex>
                                        <v-text-field v-model="newData.role.name" name="role_name" label="Название роли" type="text"
                                            v-validate="'required'" 
                                            :error-messages="errors.collect('role_name')"
                                            data-vv-name="role_name" data-vv-as='"Название роли"'                                    
                                        ></v-text-field>

                                        <v-text-field v-model="newData.role.display_name" name="role_diplay_name" label="Отображаемое имя роли" type="text"></v-text-field>
                                        <v-text-field v-model="newData.role.description" name="role_description" label="Описание роли" type="text"></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn block outline :loading="newData.role.loading" color="success" @click="createRole">Создать роль</v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-flex> -->

            <!-- <v-flex xs12 sm6 mg4 lg4>
                <v-card>
                    <v-form @submit="createRole">
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Право доступа</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>
                        <v-card-title>
                            <v-container class="py-0 px-0">
                                <v-layout row wrap>
                                    <v-flex>
                                        <v-text-field v-model="newData.permission.name" name="permission_name" label="Название права доступа" type="text"
                                            v-validate="'required'" 
                                            :error-messages="errors.collect('permission_name')"
                                            data-vv-name="permission_name" data-vv-as='"Название права доступа"'                                    
                                        ></v-text-field>

                                        <v-text-field v-model="newData.permission.display_name" name="permission_diplay_name" label="Отображаемое имя права доступа" type="text"></v-text-field>
                                        <v-text-field v-model="newData.permission.description" name="permission_description" label="Описание права доступа" type="text"></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn block outline :loading="newData.permission.loading" color="success" @click="createPermission">Создать право доступа</v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-flex> -->
        </v-layout>  

        <v-dialog v-model="createRoleDialog" max-width="500" >
            <form @submit.prevent="createRole" data-vv-scope="create-role-form" v-can="['create-role']">
                <v-card>
                    <v-card-title class="headline">Создание роли</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>
                                <v-text-field v-model="newData.role.name" name="role_name" label="Название" type="text"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('role_name')"
                                    data-vv-name="role_name" data-vv-as='"Название"'
                                    hint="Например: superadministrator"
                                ></v-text-field>

                                <v-text-field 
                                    type="text"
                                    name="role_diplay_name" 
                                    label="Отображаемое имя роли" 
                                    v-model="newData.role.display_name"
                                    hint="Например: Суперадминистратор" 
                                ></v-text-field>
                                <v-text-field v-model="newData.role.description" name="role_description" label="Описание" type="text"></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="createRoleDialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="newData.role.loading" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
            </v-dialog>      

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import axios from '@/axios'
import Loading from '@/components/Loading'

export default {
    components: {
        Loading
    },
    data() {
        return {
            active: null,
            snackbar: {
                active: false,
                text: '',
                timeout: 5000,
                color: ''
            },
            createRoleDialog: false,
            loading: {
                page: false,
                editRole: false
            },
            acl: {
                roles: [],
                permissions: []
            },
            roles: [],
            permissions: [],
            newData: {
                role: {
                    name: '',
                    display_name: '',
                    description: '',
                    loading: false
                },
                permission: {
                    name: '',
                    display_name: '',
                    description: '',
                    loading: false
                }
            }
        }
    },
    methods: {
        next() {
            const active = parseInt(this.active)
            this.active = (active < 2 ? active + 1 : 0).toString()
        },

        getACL() {
            this.loading.page = true;
            axios.get('admin/acl')   
                .then(response => {
                    this.acl = response.data.acl;
                    this.roles = [];
                    this.permissions = [];

                    this.acl.permissions.map(permission => {
                        this.permissions.push(Number(permission.id))
                    });

                    this.acl.roles.map(role => {
                        let temp = [];
                        role.permissions.map(permission => {
                            temp.push(permission.id);
                        });

                        this.roles.push({
                            'id': role.id,
                            'display_name': role.display_name,
                            'permissions': temp
                        });                        
                    });

                    this.loading.page = false;
                })
                .catch(error => console.log(error));
        },

        editRoles() {
            this.loading.editRole = true;
            axios.put(`admin/acl`, { roles: this.roles })
                .then(response => {
                    this.loading.editRole = false;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                    this.getACL();
                });
        },

        createRole() {
            this.newData.role.loading = true
            axios.post('admin/acl/roles', this.newData.role)
                .then(response => {
                    this.newData.role.loading = false;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                    this.getACL();
                })
                .catch(error => console.log(error));
        },

        createPermission() {
            this.newData.permission.loading = true
            axios.post('admin/acl/permissions', this.newData.permission)
                .then(response => {
                    this.newData.permission.loading = false;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.getACL();
    }
}
</script>

<style>

</style>
