<template>
    <v-app>
        <!-- Left sidebar -->
        <v-navigation-drawer :mini-variant="miniVariant" clipped v-model="drawer" fixed app>
            <v-list>
                <template v-for="(item, index) in items">
                    <v-layout v-if="item.heading" :key="index" row align-center>
                        <v-flex xs12>
                            <v-subheader v-if="item.heading">
                                {{ item.heading }}
                            </v-subheader>
                        </v-flex>
                    </v-layout>
                    <v-divider v-else-if="item.divider" :key="index" dark class="my-1"></v-divider>
                    <v-list-tile v-else :key="index" :to="item.to" v-can="item.permissions">
                        <v-list-tile-action>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{ item.title }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    </template>
            </v-list>
        </v-navigation-drawer>
        
        <!-- Toolbar -->
        <v-toolbar fixed app clipped-left dark color="blue darken-2">
            <v-toolbar-side-icon @click="drawer = !drawer"></v-toolbar-side-icon>
            <v-btn icon @click.stop="miniVariant = !miniVariant">
                <v-icon v-html="miniVariant ? 'chevron_right' : 'chevron_left'"></v-icon>
            </v-btn>
            <v-toolbar-title></v-toolbar-title>
            <v-spacer></v-spacer>
            <!-- User menu -->
            <v-menu :close-on-content-click="false" offset-x>
                <v-btn icon large slot="activator">
                    <v-avatar size="36px" tile>
                        <img :src="user.avatar !== null ? assetsURL + '/' + user.avatar : '/static/images/default_user.png'" alt="User avatar" class="user-avatar" />
                    </v-avatar>
                </v-btn>
                <v-card>
                    <v-list>
                        <v-list-tile avatar>
                            <v-list-tile-avatar>
                                <img :src="user.avatar !== null ? assetsURL + '/' + user.avatar : '/static/images/default_user.png'" alt="User avatar" class="user-avatar" />
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title>{{ user.fullname }}</v-list-tile-title>
                                <v-list-tile-sub-title>{{ user.email }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list>
                    <v-divider></v-divider>
                    <v-list>
                        <v-list-tile @click="passwords.dialog = true">
                            <v-list-tile-action>
                                <v-icon light>lock_open</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>Изменить пароль</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile @click="logout">
                            <v-list-tile-action>
                                <v-icon light>power_settings_new</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>Выйти</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list>
                </v-card>
            </v-menu>   
        </v-toolbar>

        <!-- Main content -->
        <v-content>
            <v-container grid-list-xs grid-list-sm grid-list-md grid-list-lg>
                <transition name="slide-x-reverse-transition" mode="out-in">
                    <router-view />
                </transition>
            </v-container>
        </v-content>

        <!-- Right sidebar -->
        <v-navigation-drawer temporary right v-model="rightDrawer" fixed>
            <v-list>
                <v-list-tile @click="logout">
                    <v-list-tile-action>
                        <v-icon light>power_settings_new</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Выйти</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-navigation-drawer>

        <v-footer fixed app inset>
            <span class="px-3">IT Nova &copy; 2018</span>
        </v-footer>

        <!-- Change password modal -->
        <v-dialog v-model="passwords.dialog" max-width="500">
            <form @submit.prevent="changePassword" data-vv-scope="change-password-form">
                <v-card>
                    <v-card-title class="headline">Изменить пароль</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field v-model="passwords.old" name="old_password" label="Старый пароль" prepend-icon="lock_open"                 
                                    :append-icon="passwords.showOldPassword ? 'visibility_off' : 'visibility'"
                                    :append-icon-cb="() => (passwords.showOldPassword = !passwords.showOldPassword)"
                                    :type="passwords.showOldPassword ? 'text' : 'password'"
                                    v-validate="'required'" 
                                    data-vv-name="old_password" data-vv-as='"Старый пароль"' required
                                    :error-messages="errors.collect('old_password')"
                                ></v-text-field>

                                <v-text-field v-model="passwords.new" name="new_password" label="Новый пароль" hint="Минимум 6 символов" prepend-icon="lock_open"  
                                    :append-icon="passwords.showNewPassword ? 'visibility_off' : 'visibility'"
                                    :append-icon-cb="() => (passwords.showNewPassword = !passwords.showNewPassword)"
                                    :type="passwords.showNewPassword ? 'text' : 'password'"
                                    v-validate="'required|min:6'"
                                    :error-messages="errors.collect('new_password')"
                                    data-vv-name="new_password" data-vv-as='"Пароль"' required 
                                ></v-text-field> 
                                
                                <v-text-field v-model="passwords.new_confirmation" name="new_confimation_password" label="Повторите новый пароль" hint="Минимум 6 символов" prepend-icon="lock_open"  
                                    :append-icon="passwords.showNewConfPassword ? 'visibility_off' : 'visibility'"
                                    :append-icon-cb="() => (passwords.showNewConfPassword = !passwords.showNewConfPassword)"
                                    :type="passwords.showNewConfPassword ? 'text' : 'password'"
                                    v-validate="{ required: true, confirmed: passwords.new }"
                                    :error-messages="errors.collect('new_confimation_password')"
                                    data-vv-name="new_confimation_password" data-vv-as='"Пароль"' required 
                                ></v-text-field> 
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="passwords.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="passwords.loading" flat="flat" type="submit">Изменить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
     </v-app>
</template>

<script>
import axios from '@/axios'
import cookies from 'js-cookie'
import config from '@/config'
import snackbar from '@/components/mixins/snackbar'
import user from '@/components/mixins/user'
import assetsURL from '@/components/mixins/assets-url'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar, user, assetsURL ],
    data() {
        return {
            drawer: true,
            fixed: false,
            items: [
                { icon: 'home', title: 'Главная', to: '/' },
                { divider: true },                               
                { icon: 'list', title: 'Список компаний', to: '/companies' },
                { icon: 'directions_car', title: 'Все автомобили', to: '/cars' },
                { icon: 'directions_car', title: 'Резервные автомобили', to: '/cars/reserved' },
                { icon: 'people', title: 'Водители', to: '/drivers' },
                { icon: 'people', title: 'Очередь водителей', to: '/drivers/queue' },
                { divider: true }, 
                { heading: 'Админка' },
                { icon: 'people', title: 'Пользователи', to: { name: 'AdminUsers' }, permissions: ['read-users'] },
                { icon: 'list', title: 'Компании', to: { name: 'AdminCompanies' }, permissions: ['read-companies'] },                
                { icon: 'lock', title: 'Конструктор прав доступа', to: { name: 'AdminACL' }, permissions: ['read-acl'] },                
            ],
            miniVariant: false,
            rightDrawer: false,

            passwords: {
                old: '',
                new: '',
                new_confirmation: '',
                dialog: false,
                loading: false,
                showOldPassword: false,
                showNewPassword: false, 
                showNewConfPassword: false, 
            }
        };
    },
    methods: {
        logout() {
            this.$store.dispatch('logout');
        },

        changePassword() {
            this.passwords.loading = true;
            this.$validator.validateAll('change-password-form')
                .then(success => {
                    if(success) {
                        axios.put('/password/change', {
                            'old_password': this.passwords.old,
                            'new_password': this.passwords.new,
                            'new_password_confirmation': this.passwords.new_confirmation                
                        })
                        .then(response => {
                            if(response.data.success) {
                                this.passwords.old = this.passwords.new = this.passwords.new_confirmation = ''                             
                                this.$validator.errors.clear();
                                this.snackbar.color = 'success';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
                                this.passwords.loading = false;
                            } else {
                                this.snackbar.color = 'error';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
                                this.passwords.loading = false;                                
                            }
                        })
                        .catch(error => console.error());
                    }
                });            
        }
    },
};
</script>

<style>
    
</style>

