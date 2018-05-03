<template>
    <v-container>
        <v-layout>
            <v-flex>
                <v-btn @click="logout" color="success">Выйти</v-btn>
                <v-btn @click="showUser" color="success">Пользователь</v-btn>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
export default {
    methods: {
        async logout() {
            await this.$auth.logout();
            this.$router.push('/login');
        },

        showUser() {  
                          
        },

        parseJwt (token) {
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace('-', '+').replace('_', '/');
            return JSON.parse(window.atob(base64));
        }
    },
    created() {
        console.log(this.$auth.user.email);
    }
}
</script>

<style>

</style>

