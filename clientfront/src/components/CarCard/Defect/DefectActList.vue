<template>
    <v-card class="mb-3">
        <v-card-media>
            <v-container>
                <v-layout>
                    <v-flex>
                        <p class="subheading my-0">Дефектные акты</p>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card-media>
        <v-divider></v-divider>
        <v-card-text primary-title class="pt-1 pb-0">
            <v-list two-line>
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="items.length === 0">
                    Дефектных актов нет.
                </v-alert>
                <template v-for="item in items" >
                    <v-list-tile :key="item.title" avatar>
                        <v-list-tile-avatar>
                            <v-icon class="blue white--text">assignment</v-icon>
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>Номер акта: #{{ item.id | generateActNum }}</v-list-tile-title>
                            <v-list-tile-sub-title>Дата создания: {{ item.defect_act_date }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-btn icon ripple>
                                <v-icon color="grey lighten-1">remove_red_eye</v-icon>
                            </v-btn>
                        </v-list-tile-action>
                    </v-list-tile>
                </template>
            </v-list>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    props: {
        items: Array
    },
    filters: {
        generateActNum(value) {
            return (value / 10000).toString().replace('.', '');
        }
    },
    data() {
        return {
            
        }
    }
}
</script>

<style>

</style>
