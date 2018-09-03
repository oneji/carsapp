<template>
    <div>
        <v-layout row wrap>
            <v-flex xs12 sm6 md3 lg3>
                <v-card>
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0">Аватар</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <v-card-title primary-title>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex>
                                    <img v-if="editedUser.avatar.url" class="avatar-preview" :src="`${assetsURL}/${editedUser.avatar.url}`" height="150" />                                    
                                    <img v-else class="avatar-preview" src="/static/images/no-photo.png" alt="Нет фото">
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-container class="pb-0 pt-3">
                            <v-layout row wrap>
                                <v-text-field label="Выберите аватар" @click="pickFile" v-model="editedUser.avatar.name" 
                                    prepend-icon="attach_file" append-icon="delete" :append-icon-cb="deleteAvatar"
                                ></v-text-field>
                                <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                            </v-layout>
                        </v-container>
                    </v-card-actions>
                </v-card>
            </v-flex>

            <v-flex xs12 sm6 md9 lg9>
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
                                    <v-flex xs12 sm6 md6 lg6 class="v-divider pr-4">                                        
                                        <v-container grid-list-xs>
                                            <v-alert v-model="alert" v-if="alert.active" type="error" dismissible>
                                                This is a success alert that is closable.
                                            </v-alert>
                                            <v-layout row wrap>
                                                <v-flex xs6>
                                                    <v-text-field type="text" v-model="editedUser.fullname" name="fullname" label="ФИО" prepend-icon="person"                 
                                                        v-validate="'required'" 
                                                        data-vv-name="fullname" data-vv-as='"ФИО"'
                                                        :error-messages="errors.collect('fullname')"
                                                    ></v-text-field>                                        
                                                </v-flex>
                                                
                                                <v-flex xs6>
                                                    <v-text-field v-model="editedUser.email" name="email" label="Email" type="text"
                                                        v-validate="'required|email'" 
                                                        :error-messages="errors.collect('email')"
                                                        data-vv-name="email" data-vv-as='"Email"'                                    
                                                    ></v-text-field>
                                                </v-flex>
                                            </v-layout>

                                            <v-text-field v-model="editedUser.password" name="password" label="Пароль" hint="Минимум 6 символов" prepend-icon="lock"
                                                :append-icon="showPassword ? 'visibility' : 'visibility_off'"
                                                :append-icon-cb="() => (showPassword = !showPassword)" 
                                                :type="showPassword ? 'password' : 'text'"
                                                v-validate="'required|min:6'"
                                                :error-messages="errors.collect('password')"
                                                data-vv-name="password" data-vv-as='"Пароль"' 
                                            ></v-text-field>   

                                            <v-text-field v-model="editedUser.passwordConfirmation" name="password_confimation" label="Подтвердите пароль" hint="Минимум 6 символов" prepend-icon="lock"
                                                :append-icon="showPasswordConfirmation ? 'visibility' : 'visibility_off'"
                                                :append-icon-cb="() => (showPasswordConfirmation = !showPasswordConfirmation)"
                                                :type="showPasswordConfirmation ? 'password' : 'text'"
                                                v-validate="'required|min:6|confirmed:password'"
                                                :error-messages="errors.collect('password_confirmation')" 
                                                data-vv-name="password_confirmation" data-vv-as='"Подтверждение пароля"' 
                                            ></v-text-field>    
                                        </v-container>
                                        
                                    </v-flex>

                                    <v-flex xs12 sm6 md6 lg6 class="pl-4">
                                        <form>
                                            <v-container grid-list-xs> 
                                                <v-layout>
                                                    <v-flex>
                                                        <v-select :items="roles" v-model="editedUser.roles" label="Выберите роли" multiple chips prepend-icon="people" persistent-hint></v-select>
                                                        <v-select :items="permissions" v-model="editedUser.permissions" label="Выберите права доступа" prepend-icon="category" multiple chips persistent-hint></v-select>
                                                        <v-select :items="userTypes" v-model="editedUser.type" label="Выберите тип пользователя" prepend-icon="supervised_user_circle" persistent-hint
                                                            name="type"
                                                            v-validate="'required'" 
                                                            :error-messages="errors.collect('type')"
                                                            data-vv-name="type" data-vv-as='"Тип пользователя"'
                                                        ></v-select>
                                                    </v-flex>     
                                                </v-layout>                                                                       
                                            </v-container>
                                        </form>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-container class="py-1">
                                <v-layout>
                                    <v-flex>
                                        <v-btn :loading="loading" color="success" type="submit">Изменить</v-btn>
                                        <v-btn color="info">Очистить</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
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

export default {
    $_veeValidate: {
        validator: 'new'
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
            loading: false,
            showPassword: false,
            showPasswordConfirmation: false,
            userTypes: [
                { text: 'Администратор', value: 0 },
                { text: 'Компания', value: 1 },
                { text: 'СТО', value: 2 },
            ],
            
            editedUser: {
                fullname: '',
                email: '',
                password: '',
                passwordConfirmation: '',
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
            const vm = this;
            axios.get(`admin/users/${id}`)
                .then(response => {
                    console.log(response);
                    vm.editedUser.fullname = response.data.fullname;
                    vm.editedUser.email = response.data.email;
                    vm.editedUser.type = Number(response.data.type);
                    
                    let rolesObj = response.data.roles;
                    let permissionsObj = response.data.permissions;

                    rolesObj.map(obj => {
                        vm.editedUser.roles.push(Number(obj.id));
                    });

                    permissionsObj.map(obj => {
                        vm.editedUser.permissions.push(Number(obj.id));
                    });
                })
                .catch(error => console.log(error));
        },

        editUser(event) {
            this.loading = true;
            const vm = this;
            this.$validator.validateAll()
                .then((result) => {
                    if(result) {
                        let formData = new FormData();
                        formData.append('fullname', vm.editedUser.fullname);
                        formData.append('email', vm.editedUser.email);
                        formData.append('password', vm.editedUser.password);
                        formData.append('password_confirmation', vm.editedUser.password_confirmation);
                        formData.append('avatar', vm.editedUser.avatar.file);
                        formData.append('type', vm.editedUser.type);

                        debugger;

                        axios.put(`admin/users/${this.$route.params.id}`, formData)
                            .then(response => {
                                console.log(JSON.parse(response.data.message));
                                vm.loading = false;
                                vm.snackbar.color = 'success';
                                vm.snackbar.text = response.data.message;
                                vm.snackbar.active = true;
                                event.target.reset();
                            });
                    } else {
                        vm.loading = false;
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
