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
              <v-btn small flat>Создать</v-btn>
            </v-flex>
          </v-layout>
          <v-divider v-else-if="item.divider" :key="index" dark class="my-1"></v-divider>
          <v-list-tile v-else :key="index" :to="item.to">
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title class="grey--text">
                {{ item.title }}
              </v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </template>
      </v-list>
    </v-navigation-drawer>
    
    <!-- Toolbar -->
    <v-toolbar fixed app clipped-left dark color="green accent-3">
      <v-toolbar-side-icon @click="drawer = !drawer"></v-toolbar-side-icon>
      <v-btn icon @click.stop="miniVariant = !miniVariant">
        <v-icon v-html="miniVariant ? 'chevron_right' : 'chevron_left'"></v-icon>
      </v-btn>
      <v-toolbar-title></v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon @click.stop="rightDrawer = !rightDrawer">
        <v-icon>menu</v-icon>
      </v-btn>
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

    <v-footer fixed app>
      <span>IT Nova &copy; 2018</span>
    </v-footer>
  </v-app>
</template>

<script>
import axios from '../axios'

export default {
  computed: {

  },
  data() {
    return {
      drawer: true,
      fixed: false,
      items: [
        { icon: 'home', title: 'Все проекты', to: { name: 'Home' } },
        { divider: true },
        { icon: 'home', title: 'Главная', to: { name: 'Home' } },
        { divider: true },
        { heading: 'Машины' },
        { icon: 'directions_car', title: 'Все', to: { name: 'CompanyCars' } },
        { icon: 'directions_car', title: 'Кузова', to: { name: 'Home' } },
        { icon: 'directions_car', title: 'Модели', to: { name: 'Home' } },
        { icon: 'directions_car', title: 'Марки', to: { name: 'Home' } },
        { divider: true },
        { heading: 'Водители' },
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
