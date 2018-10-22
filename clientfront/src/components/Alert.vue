<template>
    <v-alert outline transition="scale-transition" :type="`${type}`" :value="true">
        {{ message }}
    </v-alert>
</template>

<script>
export default {
    props: {
        type: String,
        message: String
    }
}
</script>

<style>

</style>
