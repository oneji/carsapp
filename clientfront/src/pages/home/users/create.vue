<template>
    <div>
        <MoveButtons />
        <v-layout row wrap>
            <v-flex xs12 sm6 md4 lg4>
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
                                    <img v-if="newUser.avatar.url" class="avatar-preview" :src="newUser.avatar.url" height="150" />                                    
                                    <img v-else class="avatar-preview" src="/static/images/no-photo.png" alt="Нет фото">
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-container class="pb-0 pt-3">
                            <v-layout row wrap>
                                <v-text-field label="Выберите аватар" @click="pickFile" v-model="newUser.avatar.name" 
                                    prepend-icon="attach_file" append-icon="delete" :append-icon-cb="deleteAvatar"
                                ></v-text-field>
                                <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                            </v-layout>
                        </v-container>
                    </v-card-actions>
                </v-card>
            </v-flex>

            <v-flex xs12 sm6 md8 lg8>
                <v-card>
                    <v-form @submit.prevent="createUser">
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
                                    <v-flex xs12 sm12 md12 lg12 v-if="alert.show"> 
                                        <v-alert v-model="alert" type="error" dismissible>
                                            {{ alert.message }}
                                        </v-alert>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12 lg12>
                                        <v-text-field type="text" v-model="newUser.fullname" name="fullname" label="ФИО" prepend-icon="person"                 
                                            v-validate="'required'" 
                                            data-vv-name="fullname" data-vv-as='"ФИО"'
                                            :error-messages="errors.collect('fullname')"
                                        ></v-text-field>  
                                    
                                        <v-text-field v-model="newUser.email" name="email" label="Email" type="text" prepend-icon="email" 
                                            v-validate="'required|email'" 
                                            :error-messages="errors.collect('email')"
                                            data-vv-name="email" data-vv-as='"Email"'
                                        ></v-text-field>

                                        <v-text-field v-model="newUser.password" name="password" label="Пароль" hint="Минимум 6 символов" prepend-icon="lock"
                                            :append-icon="showPassword ? 'visibility_off' : 'visibility'"
                                            :append-icon-cb="() => (showPassword = !showPassword)" 
                                            :type="showPassword ? 'text' : 'password'"
                                            v-validate="'required|min:6'"
                                            :error-messages="errors.collect('password')"
                                            data-vv-name="password" data-vv-as='"Пароль"' 
                                        ></v-text-field>

                                        <v-text-field v-model="newUser.passwordConfirmation" name="password_confimation" label="Подтвердите пароль" hint="Минимум 6 символов" prepend-icon="lock"
                                            :append-icon="showPasswordConfirmation ? 'visibility_off' : 'visibility'"
                                            :append-icon-cb="() => (showPasswordConfirmation = !showPasswordConfirmation)"
                                            :type="showPasswordConfirmation ? 'text' : 'password'"
                                            v-validate="'required|min:6|confirmed:password'"
                                            :error-messages="errors.collect('password_confirmation')" 
                                            data-vv-name="password_confirmation" data-vv-as='"Подтверждение пароля"' 
                                        ></v-text-field>  

                                        <v-select :items="roles" v-model="newUser.roles" label="Выберите роли" multiple chips prepend-icon="people" persistent-hint></v-select>
                                        <v-select :items="permissions" v-model="newUser.permissions" label="Выберите права доступа" prepend-icon="category" multiple chips persistent-hint></v-select>
                                        <v-select :items="userTypes" v-model="newUser.type" label="Выберите тип пользователя" prepend-icon="supervised_user_circle" persistent-hint
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
                            <v-container class="py-1">
                                <v-layout>
                                    <v-flex>
                                        <v-btn :loading="loading" color="success" type="submit">Создать</v-btn>
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
import MoveButtons from '@/components/MoveButtons'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        MoveButtons
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
                { text: 'Клиент', value: 1 },
            ],

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
    methods: {
        createUser() {
            this.loading = true;
            this.$validator.errors.clear();
            this.$validator.validateAll()
                .then((result) => {
                    if(result) {
                        let formData = new FormData();
                        formData.append('fullname', this.newUser.fullname);
                        formData.append('email', this.newUser.email);
                        formData.append('password', this.newUser.password);
                        formData.append('password_confirmation', this.newUser.passwordConfirmation);
                        formData.append('avatar', this.newUser.avatar.file);
                        formData.append('type', this.newUser.type);
                        formData.append('roles', this.newUser.roles);
                        formData.append('permissions', this.newUser.permissions);

                        axios.post('admin/users', formData)
                            .then(response => {
                                if(response.data.success) {
                                    this.loading = false;
                                    this.snackbar.color = 'success';
                                    this.snackbar.text = response.data.message;
                                    this.snackbar.active = true;
                                    this.$validator.errors.clear();
                                } else {
                                    this.loading = false;
                                    let errorMessages = JSON.parse(response.data.message);
                                    console.log(errorMessages);
                                    for(let errorKey in errorMessages) {
                                        this.$validator.errors.add(errorKey, errorMessages[errorKey]);
                                    }
                                }
                            });
                    } else {
                        this.loading = false;
                    }
                });           
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

        deleteAvatar() {
            this.newUser.avatar.url = '';
            this.newUser.avatar.name = '';
            this.newUser.avatar.file = '';
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
        },
    },

    created() {
        this.getAcl();
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
