<template>
    <v-app>
        <!-- Left sidebar -->
        <v-navigation-drawer :mini-variant="miniVariant" clipped v-model="drawer" fixed app>
            <v-list>
                <template v-for="(item, index) in items">
                    <v-layout v-if="item.heading" :key="index" row align-center>
                        <v-flex xs6>
                            <v-subheader v-if="item.heading">
                                {{ item.heading }}
                            </v-subheader>
                        </v-flex>
                        <v-flex xs6 class="text-xs-right">
                            <v-btn small flat>Добавить</v-btn>
                        </v-flex>
                    </v-layout>
                    <v-divider v-else-if="item.divider" :key="index" dark class="my-1"></v-divider>
                    <v-list-tile v-else :key="index" :to="item.to">
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
        <v-toolbar fixed app clipped-left dark color="light-blue">
            <v-toolbar-side-icon @click="drawer = !drawer"></v-toolbar-side-icon>
            <v-btn icon @click.stop="miniVariant = !miniVariant">
                <v-icon v-html="miniVariant ? 'chevron_right' : 'chevron_left'"></v-icon>
            </v-btn>
            <v-toolbar-title></v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click.stop="rightDrawer = !rightDrawer">
                <v-icon>notifications</v-icon>
            </v-btn>
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
                    <router-view/>
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
            <span class="pl-3">IT Nova &copy; 2018</span>
        </v-footer>
  </v-app>
</template>

<script>
import axios from '@/axios'
import cookies from 'js-cookie'
import config from '@/config'
import user from '@/components/mixins/user'
import assetsURL from '@/components/mixins/assets-url'

export default {
    mixins: [user, assetsURL],
    data() {
        return {
            drawer: true,
            fixed: false,
            items: [
                { icon: 'dashboard', title: 'Все проекты', to: { name: 'Home' } },
                { icon: 'home', title: 'Главная', to: { name: 'CompanyHome' } },
                { icon: 'assignment', title: 'Список СТО', to: { name: 'CompanyStoRequests' } },
                { divider: true },        
                { icon: 'directions_car', title: 'Автомобили', to: { name: 'CompanyCars' } },
                { icon: 'directions_car', title: 'Проданные автомобили', to: { name: 'CompanySoldCars' } },
                { divider: true },
                { icon: 'supervised_user_circle', title: 'Водители', to: { name: 'CompanyDrivers' } },
            ],
            miniVariant: false,
            rightDrawer: false,
        };
    },
    methods: {
        logout() {
            this.$store.dispatch('logout');
        }
    }, 
};
</script>
