<template>
    <v-list-tile avatar>
        <v-list-tile-avatar>
            <v-icon class="blue white--text">assignment</v-icon>
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title>Номер акта: #{{ item.id | generateActNum }}</v-list-tile-title>
            <v-list-tile-sub-title>
                Дата создания: {{ typeof item.created_at === 'object' 
                    ? item.created_at.date 
                    : item.created_at | moment("MMMM D, YYYY") }}
            </v-list-tile-sub-title>
            <v-list-tile-sub-title v-if="item.sent === 0">
                <MyLabel type="warning" text="Ждет отправки" />
            </v-list-tile-sub-title>
            <v-list-tile-sub-title v-if="item.sent === 1 && item.confirmed === 0 && item.closed === 0">
                <MyLabel type="warning" text="Ждет подтверждения и закрытия" />
            </v-list-tile-sub-title>
            <v-list-tile-sub-title v-if="item.sent === 1 && item.confirmed === 1 && item.closed === 1">
                <MyLabel type="success" text="Подтвержден и закрыт" />
            </v-list-tile-sub-title>
        </v-list-tile-content>
        <v-list-tile-action>
            <v-btn v-if="showAct && $route.name !== 'CompanyCarsCard'" icon ripple :to="{ name: 'StoDoneAct', params: { act: item.id } }">
                <v-icon color="grey lighten-1">remove_red_eye</v-icon>
            </v-btn>
            <v-btn v-if="showAct && $route.name !== 'StoCarCard' && item.sent === 1" icon ripple :to="{ name: 'CompanyDoneAct', params: { act: item.id } }">
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
        showAct: {
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
    }
}
</script>

<style>

</style>
