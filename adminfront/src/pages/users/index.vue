<template>
    <div>
        <v-layout row wrap>
            <v-flex xs12 sm12 md12 lg12>
                <v-btn to="/users/create" color="success">Создать пользователя</v-btn>
            </v-flex>
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">     
            <v-flex v-for="(user, index) in users" :key="user.id" xs12 sm6 md3 lg2>
                <v-card>
                    <v-card-media>                        
                        <v-container>
                            <v-layout wrap justify-center align-center>
                                <v-avatar class="mt-3" size="100" color="grey lighten-4">
                                    <img v-if="user.avatar" :src="`${assetsURL}/${user.avatar}`" alt="">
                                    <img v-else src="/static/images/user.png" alt="Нет аватара">
                                </v-avatar>

                                <v-flex xs12 sm12 md12 lg12>
                                    <p class="subheading text-xs-center mb-0">{{ user.fullname }}</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    
                    <v-card-title primary-title class="py-0 px-0">
                        <v-layout>
                            <v-flex>                                
                                <v-list two-line>
                                    <v-list-tile avatar>
                                        <v-list-tile-content>
                                        <v-list-tile-title>Email</v-list-tile-title>
                                        <v-list-tile-sub-title>{{ user.email }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <v-divider></v-divider>
                                </v-list>
                            </v-flex>
                        </v-layout>    
                    </v-card-title>

                    <v-card-actions>
                        <v-btn flat color="green" :to="`/users/edit/${user.id}`">Изменить</v-btn>
                        <v-btn flat color="red" @click="deleteUser(user.id, index)">Удалить</v-btn>
                    </v-card-actions>                    
                </v-card>
            </v-flex>
        </transition-group>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import axios from '@/axios'
import DataTable from '@/components/DataTable'
import config from '@/config'

export default {
    data() {
        return {            
            showPassword: false,
            dialog: false,
            snackbar: {
                active: false,
                text: '',
                timeout: 5000,
                color: ''
            },
            alert: {
                active: false,
                text: {},
                type: ''
            },
            loading: {
                button: false
            },
            alert: {
              show: false,
              message: '' 
            },

            userTypes: [
                { text: 'Администратор', value: 0 },
                { text: 'Компания', value: 1 },
                { text: 'СТО', value: 2 },
            ],
            usersArray: [],
            newUser: {
                fullname: '',
                email: '',
                password: '',
                passwordConfirmation: '',
                avatar: {
                    name: '',
                    file: '',
                    url: ''
                },
                type: null,
                roles: [],
                permissions: []
            },    
            acl: {},
            roles: [],
            permissions: []
        }
    },
    computed: {
        currentUser() {
            return this.$store.getters.user;
        },

        users() {
            return this.usersArray;
        },

        assetsURL() {
            return config.assetsURL;
        },
    },
    components: {
        DataTable
    },
    methods: {
        getAllUsers() {
            axios.get('admin/users')
                .then(response => {
                    this.usersArray = response.data;
                })
                .catch(error => console.log(error));
        },

        createUser() {
            let formData = new FormData();
            formData.append('fullname', this.newUser.fullname);
            formData.append('email', this.newUser.email);
            formData.append('password', this.newUser.password);
            formData.append('avatar', this.newUser.avatar.file);
            formData.append('type', this.newUser.type);
            formData.append('roles', this.newUser.roles);
            formData.append('permissions', this.newUser.permissions);

            axios.post('admin/users', formData)
                .then(response => {
                    if(response.data.success) {

                    } else {
                        console.log(JSON.parse(response.data.message));
                        this.alert.type = 'error';
                        this.alert.text = JSON.parse(response.data.message);
                        this.alert.active = true;
                    }
                });
        },

        editUser(user) {
            const vm = this;
            this.$store.dispatch('userToEdit', user)
                .then(() => {
                    vm.$router.push({ name: 'UserEdit' });
                });
        },
        
        deleteUser(user_id, index) {
            axios.delete(`admin/users/${user_id}`)
                .then(response => {
                    this.usersArray.splice(index, 1);
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })
                .catch(error => console.log(error));
        },

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
    
        pickFile () {
            this.$refs.image.click();
        },

        onFilePicked (e) {
            const files = e.target.files;

            if(files[0] !== undefined) {
                this.newUser.avatar.name = files[0].name;

                if(this.newUser.avatar.name.lastIndexOf('.') <= 0) {
                    return
                }

                const fr = new FileReader();
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.newUser.avatar.url = fr.result;
                    this.newUser.avatar.file = files[0];
                })
            } else {
                this.newUser.avatar.url = '';
                this.newUser.avatar.name = '';
                this.newUser.avatar.file = '';
            }
        }
    },
    created() {
        this.getAllUsers();
        this.getAcl();
    }
}
</script>

<style>
    .avatar-preview {
        display: block;
        margin: 0 auto;
    }
</style>
