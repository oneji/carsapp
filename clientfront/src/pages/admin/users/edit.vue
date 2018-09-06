<template>
    <div>
        <MoveButtons />
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>
        <v-layout row wrap v-if="!loading.page">
            <v-flex xs12 sm12 md8 lg8>
                <v-card>
                    <v-form @submit.prevent="editUser">
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Информация</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>                        
                        <v-card-title>
                            <v-container>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12 md12 lg12>
                                        <v-text-field type="text" v-model="editedUser.fullname" name="fullname" label="ФИО" prepend-icon="person"
                                            v-validate="'required'" 
                                            data-vv-name="fullname" data-vv-as='"ФИО"'
                                            :error-messages="errors.collect('fullname')"
                                        ></v-text-field>

                                        <v-text-field v-model="editedUser.email" name="email" label="Email" type="text" prepend-icon="email"
                                            v-validate="'required|email'" 
                                            :error-messages="errors.collect('email')"
                                            data-vv-name="email" data-vv-as='"Email"'                                    
                                        ></v-text-field>

                                        <v-select :items="roles" v-model="editedUser.roles" label="Выберите роли" multiple chips prepend-icon="people" persistent-hint></v-select>
                                        <v-select :items="permissions" v-model="editedUser.permissions" label="Выберите права доступа" prepend-icon="people" multiple chips persistent-hint></v-select>
                                        <v-select :items="userTypes" v-model="editedUser.type" label="Выберите тип пользователя" prepend-icon="person" persistent-hint
                                            name="type"
                                            v-validate="'required'" 
                                            :error-messages="errors.collect('type')"
                                            data-vv-name="type" data-vv-as='"Тип пользователя"'
                                        ></v-select>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn block :loading="loading.button" color="success" type="submit">Изменить</v-btn>
                        </v-card-actions>   
                    </v-form> 
                </v-card>
            </v-flex>
        </v-layout>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import axios from '@/axios' 
import config from '@/config'
import Loading from '@/components/Loading'
import MoveButtons from '@/components/MoveButtons'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        Loading, MoveButtons
    },
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    data() {
        return {
            alert: {
              show: false,
              message: '' 
            },
            snackbar: {
                active: false,
                text: '',
                timeout: 5000,
                color: ''
            },
            loading: {
                button: false,
                page: false
            },
            showPassword: false,
            showPasswordConfirmation: false,
            userTypes: [
                { text: 'Администратор', value: 0 },
                { text: 'Клиент', value: 1 },
            ],
            
            editedUser: {
                fullname: '',
                email: '',
                avatar: {
                    name: '',
                    file: '',
                    url: ''
                },
                type: '',
                roles: [],
                permissions: []
            },   
            acl: {},
            roles: [],
            permissions: [] 
        }
    },
    methods: {
        getUserByID(id) {
            this.loading.page = true;
            axios.get(`admin/users/${id}`)
                .then(response => {
                    this.editedUser.fullname = response.data.fullname;
                    this.editedUser.email = response.data.email;
                    this.editedUser.type = Number(response.data.type);
                    
                    let rolesObj = response.data.roles;
                    let permissionsObj = response.data.permissions;

                    rolesObj.map(obj => {
                        this.editedUser.roles.push(Number(obj.id));
                    });

                    permissionsObj.map(obj => {
                        this.editedUser.permissions.push(Number(obj.id));
                    });

                    this.loading.page = false;
                })
                .catch(error => console.log(error));
        },

        editUser() {   
            this.loading.button = true;                     
            this.$validator.errors.clear();
            this.$validator.validateAll()
                .then((result) => {
                    if(result) {
                        axios.put(`admin/users/${this.$route.params.id}`, this.editedUser)
                            .then(response => {
                                if(response.data.success) {
                                    this.loading.button = false;
                                    this.snackbar.color = 'success';
                                    this.snackbar.text = response.data.message;
                                    this.snackbar.active = true;
                                } else {
                                    this.loading.button = false;
                                    let errorMessages = JSON.parse(response.data.message);
                                    for(let errorKey in errorMessages) {
                                        this.$validator.errors.add(errorKey, errorMessages[errorKey]);
                                    }
                                }
                            });
                    } else {
                        this.loading.button = false;
                    }
                });           
        },

        // Helpers
        getAcl() {
            axios.get('admin/acl')   
                .then(response => {
                    this.acl = response.data.acl;

                    for(let role in this.acl.roles) {
                        this.roles.push({
                            text: this.acl.roles[role].display_name,
                            value: this.acl.roles[role].id
                        });
                    }      
                    
                    for(let permission in this.acl.permissions) {
                        this.permissions.push({
                            text: this.acl.permissions[permission].display_name,
                            value: this.acl.permissions[permission].id
                        });
                    }
                })
                .catch(error => console.log(error));
        },

        deleteAvatar() {
            this.editedUser.avatar.url = '';
            this.editedUser.avatar.name = '';
            this.editedUser.avatar.file = '';
        },

        pickFile () {
            this.$refs.image.click();
        },

        onFilePicked (e) {
            const files = e.target.files;

            if(files[0] !== undefined) {
                this.editedUser.avatar.name = files[0].name;

                if(this.editedUser.avatar.name.lastIndexOf('.') <= 0) {
                    return
                }

                const fr = new FileReader();
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.editedUser.avatar.url = fr.result;
                    this.editedUser.avatar.file = files[0];
                })
            } else {
                this.editedUser.avatar.url = '';
                this.editedUser.avatar.name = '';
                this.editedUser.avatar.file = '';
            }
        },
    },

    created() {
        this.getAcl();
        this.getUserByID(this.$route.params.id);
    }
}
</script>

<style>
    .avatar-preview {
        display: block;
        margin: 0 auto;
        width: 100%;
    }
    .v-divider {
        border-right: 1px solid #ccc; 
    }
</style>
