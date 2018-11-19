<template>
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
                <MyLabel type="success" text="Подтвержден" />
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

<script>
import MyLabel from '@/components/Label'

export default {
    props: {
        item: Object,
        showDefect: {
            type: Boolean,
            default: true
        }
    },
    components: {
        MyLabel
    },    
    filters: {
        generateActNum(value) {
            return (value / 10000).toString().replace('.', '');
        }
    },
}
</script>

<style>

</style>
