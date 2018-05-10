<template>
    <v-container class="login-page-container" fluid fill-height>
        <v-layout row justify-center>
            <v-flex xs12 sm8 md4 lg3 class="pt-5">                    
                <v-card class="login-card elevation-2 px-4 py-5">                    
                    <v-card-media class="mt-4">
                        <v-layout row justify-center>
                            <v-flex>
                                <div class="user-avatar"></div>
                            </v-flex>
                        </v-layout>
                    </v-card-media> 
                                     
                    <v-card-text>
                        <v-alert transition="scale-transition" type="error" :value="alert.message">
                            {{ alert.message }}
                        </v-alert>
                        <v-form @submit.prevent="login">
                            <v-text-field v-model="email" name="email" label="Email" type="text"
                                v-validate="'required|email'" 
                                :error-messages="errors.collect('email')"
                                data-vv-name="email" data-vv-as='"Email"' required                                    
                            ></v-text-field>
                            <v-text-field v-model="password" name="password" label="Пароль" hint="Минимум 6 символов"
                                :append-icon="showPassword ? 'visibility' : 'visibility_off'"
                                :append-icon-cb="() => (showPassword = !showPassword)"
                                :type="showPassword ? 'password' : 'text'"
                                v-validate="'required|min:6'"
                                :error-messages="errors.collect('password')"
                                data-vv-name="password" data-vv-as='"Пароль"' required 
                            ></v-text-field>                            
                            <v-btn :loading="loading" color="info" block large type="submit">Войти</v-btn>
                        </v-form>
                    </v-card-text> 
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import axios from '../axios'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    computed: {
        user() {
            return this.$store.getters.user;
        },
        isLogged() {
            return this.$store.getters.isLogged;
        }
    },
    data: () => ({        
        showPassword: true,
        email: '',
        password: '',
        alert: {
            show: false,
            message: ''
        },
        loading: false
    }),
    methods: {
        login() {
            this.loading = true;
            let credentials = {
                email: this.email,
                password: this.password
            }
            this.$store.dispatch('login', credentials).then(response => {
                this.loading = false;
                this.$router.push('/');
            })
            .catch(error => {
                this.loading = false;
                this.alert.message = error.data.message;
                this.alert.show = true;
            })        
        }
    }, 
};
</script>

<style>
    .login-page-container {
        background-color: #ECECEC;
        background-image: url('/static/images/login_bg.jpg');
        background-position: center;
        background-size: cover;
    }
    .login-card {
        background: rgba(255, 255, 255, 0.8) !important;
        margin-top: 70px;
    }
    .user-avatar {
        width: 70px;
        height: 70px;
        display: block;
        text-align: center;
        border-radius: 50%;
        background-image: url('/static/images/user.png');
        background-repeat: no-repeat;
        background-color: rgba(0, 0, 0, .085);
        background-position: 50% 1px;
        margin: 0 auto;
    }
</style>
