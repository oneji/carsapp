<template>
    <div>
        <v-layout row wrap>
            <v-flex xs12 sm12 md12 lg12>
                <v-btn @click.native.stop="dialog = true" color="success">Создать пользователя</v-btn>
                <v-dialog v-model="dialog" max-width="500">
                    <v-card>
                        <v-card-title class="headline">Создать пользователя</v-card-title>
                        <v-card-text>
                        <v-layout>
                            <v-flex xs12>
                                <form>
                                    <v-container grid-list-xs>
                                        <v-layout row>
                                            <v-flex xs12>
                                                <v-alert :value="true" :type="alert.type">
                                                    <ul>
                                                        <li v-for="message in alert.messages" :key="message">{{ message }}</li>
                                                    </ul>
                                                </v-alert>
                                            </v-flex>
                                        </v-layout>
                                        <v-layout row wrap>
                                            <v-flex xs6>
                                                <v-text-field type="text" v-model="newUser.fullname" name="fullname" label="ФИО" prepend-icon="person"                 
                                                    v-validate="'required'" 
                                                    data-vv-name="fullname" data-vv-as='"ФИО"' required
                                                    :error-messages="errors.collect('fullname')"
                                                ></v-text-field>                                        
                                            </v-flex>
                                            
                                            <v-flex xs6>
                                                <v-text-field v-model="newUser.email" name="email" label="Email" type="text"
                                                    v-validate="'required|email'" 
                                                    :error-messages="errors.collect('email')"
                                                    data-vv-name="email" data-vv-as='"Email"' required                                    
                                                ></v-text-field>
                                            </v-flex>
                                        </v-layout>

                                        <v-text-field v-model="newUser.password" name="password" label="Пароль" hint="Минимум 6 символов" prepend-icon="vpn_key"
                                            :append-icon="showPassword ? 'visibility' : 'visibility_off'"
                                            :append-icon-cb="() => (showPassword = !showPassword)"
                                            :type="showPassword ? 'password' : 'text'"
                                            v-validate="'required|min:6'"
                                            :error-messages="errors.collect('password')"
                                            data-vv-name="password" data-vv-as='"Пароль"' required 
                                        ></v-text-field>    
                                        
                                        <v-select :items="roles" v-model="newUser.roles" label="Выберите роли" multiple chips prepend-icon="people" persistent-hint></v-select>
                                        <v-select :items="permissions" v-model="newUser.permissions" label="Выберите права доступа" prepend-icon="people" multiple chips persistent-hint></v-select>
                                        <v-select :items="userTypes" v-model="newUser.type" label="Выберите тип пользователя" prepend-icon="people" persistent-hint></v-select>

                                        <img class="avatar-preview" :src="newUser.avatar.url" height="150" v-if="newUser.avatar.url" />
                                        <v-text-field label="Выберите аватар" @click="pickFile" v-model="newUser.avatar.name" prepend-icon="attach_file"></v-text-field>
                                        <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">

                                    </v-container>
                                </form>
                            </v-flex>
                        </v-layout>
                        </v-card-text>
                        
                        <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="loading.button" flat="flat" @click.native="createUser">Создать</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-flex>
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">     
            <v-flex v-for="(user, index) in users" :key="user.id" xs12 sm6 md2 lg2>
                <v-card>
                    <v-card-media height="150px">                        
                        <v-layout row justify-center align-center>
                            <v-avatar size="100" color="grey lighten-4">
                                <img v-if="user.avatar" :src="`${assetsURL}/user.avatar`" alt="">
                                <img v-else src="/static/images/user.png" alt="Нет аватара">
                            </v-avatar>
                        </v-layout>
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
                    <v-btn flat color="green">Изменить</v-btn>
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
