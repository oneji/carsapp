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
    this.$moment.updateLocale('ru', {
      months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    });

    this.$moment.locale('ru');
  }
}
</script>

<style>
  @import url('./assets/css/style.css'); 
  body {
    font-family: 'Open Sans', sans-serif;
  }
</style>
