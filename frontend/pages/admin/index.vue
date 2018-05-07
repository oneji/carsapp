<template>
    <v-container>
        <v-layout>
            <v-flex>
                <h1>Главная страница</h1>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
export default {
    head: {
        title: 'Главная'
    },
    methods: {
        parseJwt (token) {
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace('-', '+').replace('_', '/');
            return JSON.parse(window.atob(base64));
        }
    },
    created() {
        
    }
}
</script>

<style>

</style>

