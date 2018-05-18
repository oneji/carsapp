<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success">Создать роль</v-btn>
                <v-btn color="info">Создать право доступа</v-btn>
            </v-flex>
        </v-layout>

        <v-layout row wrap>
            <v-flex md6 lg6> 
                <v-expansion-panel>
                    <v-expansion-panel-content v-for="(item,i) in 5" :key="i">
                        <div slot="header">Item</div>
                        <v-card>
                        <v-card-text>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</v-card-text>
                        </v-card>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
export default {

}
</script>

<style>

</style>
