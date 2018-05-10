<template>
  <v-app>
    <transition name="slide-x-reverse-transition" mode="out-in">
      <router-view/>
    </transition>
  </v-app>
</template>

<script>
import axios from './axios'
import cookies from 'js-cookie'

export default {
  name: 'App',
  created() {
    // const token = cookies.get('auth.token');

    // if(token !== undefined) {
    //   this.$store.dispatch('fetch');
    // } else {
    //   this.$store.dispatch('resetUser');
    //   this.$router.push('/login');
    // }
  }
}
</script>

<style>
  body {
    font-family: 'Open Sans', sans-serif;
  }
</style>
