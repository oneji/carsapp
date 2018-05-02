<template>
    <v-app>
        <v-container fluid fill-height>
            <v-layout align-center justify-center>
                <v-flex xs12 sm6 md4 lg3>
                    <v-card class="elevation-2">
                        <v-toolbar dark color="blue darken-2">
                            <v-toolbar-title>Авторизация</v-toolbar-title>
                        </v-toolbar>

                        <v-card-text>
                            <v-form>
                                <v-text-field prepend-icon="person" name="email" label="Email" type="text"></v-text-field>
                                <v-text-field prepend-icon="lock" name="password" label="Пароль" type="password"></v-text-field>
                            </v-form>
                        </v-card-text>
                        
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="success">Войти</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </v-app>
</template>

<script>
export default {
    layout: 'main'
};
</script>

<style>

</style>
