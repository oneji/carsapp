<template>
    <v-snackbar :timeout="3000" :color="$store.getters.snackbar.color" v-model="$store.getters.snackbar.active">
        {{ $store.getters.snackbar.text }}
        <v-btn dark flat @click.native="$store.dispatch('hideSnackbar')">Закрыть</v-btn>
    </v-snackbar>
</template>

<script>
export default {

}
</script>

<style>

</style>
