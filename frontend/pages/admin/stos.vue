<template>
  <div>
    <v-layout row wrap>
      <v-flex xs12 sm12 md12 lg12>
        <v-btn @click.native.stop="dialog = true" color="success">Создать СТО</v-btn>
      </v-flex>
  </v-layout>
  </div>
</template>

<script>
export default {
  head: {
    title: 'Список СТО'
  }
}
</script>

<style>

</style>
