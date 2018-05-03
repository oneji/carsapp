<template>
    <v-app>
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
                            <v-form @submit.prevent="login">
                                <v-text-field v-model="email" name="email" label="Email" type="text"
                                    v-validate="'required|email'" 
                                    :error-messages="errors.collect('email')"
                                    data-vv-name="email" required                                    
                                ></v-text-field>

                                <v-text-field v-model="password" name="password" label="Пароль" type="password"
                                    v-validate="'required|min:6'"
                                    :error-messages="errors.collect('password')"
                                    data-vv-name="password" data-vv-as="пароль" required
                                ></v-text-field>
                                <v-btn color="info" block large type="submit">Войти</v-btn>
                            </v-form>
                        </v-card-text> 
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </v-app>
</template>

<script>
import auth from '~/services/auth'
import OrbitSpinner from '~/components/OrbitSpinner'

export default {
    layout: 'main',
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        OrbitSpinner
    },
    data: () => ({
        email: '',
        password: '',
    }),
    methods: {
        login() {
            this.$validator.validateAll()
                .then((success) => {
                    // Validation passed
                    if(success) {      
                                                
                    }
                });         
        }
    }
};
</script>

<style>
    .login-page-container {
        background-color: #ECECEC;
        background-image: url('/images/login_bg.jpg');
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
        background-image: url('/images/user.png');
        background-repeat: no-repeat;
        background-color: rgba(0, 0, 0, .085);
        background-position: 50% 1px;
        margin: 0 auto;
    }
</style>
