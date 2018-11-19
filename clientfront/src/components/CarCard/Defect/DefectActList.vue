<template>
    <div>
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
            <v-card-text primary-title class="py-1 px-1">
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="items.length === 0">
                    Дефектных актов нет.
                </v-alert>
                <v-list two-line v-else>                    
                    <template v-for="item in items" >
                        <v-list-tile :key="item.title" avatar>
                            <v-list-tile-avatar>
                                <v-icon class="blue white--text">assignment</v-icon>
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title>Номер акта: #{{ item.id | generateActNum }}</v-list-tile-title>
                                <v-list-tile-sub-title>
                                    Дата составления: {{ typeof item.defect_act_date === 'object' 
                                        ? item.defect_act_date.date 
                                        : item.defect_act_date | moment("MMMM D, YYYY") }}
                                </v-list-tile-sub-title>
                                <v-list-tile-sub-title v-if="item.confirmed === 0">
                                    <MyLabel type="success" text="Ждет подтверждения" />
                                </v-list-tile-sub-title>
                                <v-list-tile-sub-title v-if="item.confirmed === 1">
                                    <MyLabel type="success" text="Подтвержден и закрыт" />
                                </v-list-tile-sub-title>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-btn v-if="showDefect && $route.name === 'CompanyCarsCard'" icon ripple :to="{ name: 'CompanyDefectAct', params: { act: item.id } }">
                                    <v-icon color="grey lighten-1">remove_red_eye</v-icon>
                                </v-btn>
                                <v-btn v-if="showDefect && $route.name !== 'CompanyCarsCard'" icon ripple :to="{ name: 'StoDefectAct', params: { act: item.id } }">
                                    <v-icon color="grey lighten-1">remove_red_eye</v-icon>
                                </v-btn>
                            </v-list-tile-action>
                        </v-list-tile>
                    </template>
                </v-list>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import MyLabel from '@/components/Label'

export default {
    props: {
        items: Array,
        showDefect: {
            type: Boolean,
            default: true
        }
    },
    filters: {
        generateActNum(value) {
            return (value / 10000).toString().replace('.', '');
        }
    },
    components: {
        MyLabel
    },
    data() {
        return {
            
        }
    }
}
</script>

<style>

</style>
