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
                        <v-alert transition="slide-y-reverse-transition" type="error" :value="alert.message">
                            {{ alert.message }}
                        </v-alert>
                        <v-form @submit.prevent="login">
                            <v-text-field v-model="email" name="email" label="Email" type="text"
                                v-validate="'required|email'" 
                                :error-messages="errors.collect('email')"
                                data-vv-name="email" required                                    
                            ></v-text-field>
                            <v-text-field v-model="password" name="password" label="Пароль" hint="Минимум 6 символов"
                                :append-icon="showPassword ? 'visibility' : 'visibility_off'"
                                :append-icon-cb="() => (showPassword = !showPassword)"
                                :type="showPassword ? 'password' : 'text'"
                                v-validate="'required|min:6'"
                                :error-messages="errors.collect('password')"
                                data-vv-name="password" data-vv-as="пароль" required
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
export default {
    head: {
        title: 'Авторизация'
    },
    layout: 'fullscreen',
    $_veeValidate: {
        validator: 'new'
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
        // login() {
        //     this.$validator.validateAll()
        //         .then((success) => {
        //             // Validation passed
        //             if(success) {      
        //                 this.loading = true;
        //                 this.$store.dispatch('auth/login', {
        //                     email: this.email,
        //                     password: this.password
        //                 }).then(result => {
        //                     console.log(result);
        //                     this.loading = false;
        //                     this.alert.show = false;
        //                     console.log(this.$store.state.user)
        //                     this.$router.push('/');
        //                 }).catch(error => {
        //                     this.loading = false;
        //                     if (error.response && error.response.data) {
        //                         this.alert.message = error.response.data.message || error.reponse.status;
        //                         this.alert.show = true;
        //                     }
        //                 });                   
        //             }
        //         });         
        // }
        login() {
            this.$validator.validateAll()
                .then((success) => {
                    if(success) {
                        this.loading = true;
                        this.$auth.loginWith('local', {
                            data: {
                                email: this.email,
                                password: this.password
                            }
                        }).then(() => {
                            this.$router.push('/');
                        });
                    }
                })
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
