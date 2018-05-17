<template>
  <v-app>
    <transition name="slide-x-reverse-transition" mode="out-in">
      <router-view/>
    </transition>
  </v-app>
</template>

<script>
export default {
  name: 'App',
}
</script>

<style>
  body {
    font-family: 'Open Sans', sans-serif;
  }
</style>
