<template>
    <v-layout>
        <v-flex>
            <v-btn v-if="back" color="success" append @click="$router.back()">Назад</v-btn>    
            <v-btn v-if="forward" color="info" append @click="$router.forward()">Вперед</v-btn>                 
        </v-flex>
    </v-layout>
</template>

<script>
export default {
    props: {
        back: {
            type: Boolean,
            default: true
        },
        forward: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style>

</style>
