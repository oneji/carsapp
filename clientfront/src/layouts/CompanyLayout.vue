<template>
  <v-app>
    <!-- Left sidebar -->
    <v-navigation-drawer :mini-variant="miniVariant" clipped v-model="drawer" fixed app>
      <v-list>
        <v-list-tile router :to="item.to" :key="i" v-for="(item, i) in items" exact>
          <v-list-tile-action>
            <v-icon v-html="item.icon"></v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title v-text="item.title"></v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>
    
    <!-- Toolbar -->
    <v-toolbar fixed app clipped-left dark color="blue darken-2">
      <v-toolbar-side-icon @click="drawer = !drawer"></v-toolbar-side-icon>
      <v-btn icon @click.stop="miniVariant = !miniVariant">
        <v-icon v-html="miniVariant ? 'chevron_right' : 'chevron_left'"></v-icon>
      </v-btn>
      <v-toolbar-title v-text="`Привет`"></v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon @click.stop="rightDrawer = !rightDrawer">
        <v-icon>menu</v-icon>
      </v-btn>
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
        { icon: 'home', title: 'Главная', to: '/' },
        { icon: 'directions_car', title: 'Машины', to: '/cars' },
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
