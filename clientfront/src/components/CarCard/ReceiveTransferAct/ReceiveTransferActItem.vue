<template>
    <v-list-tile :key="item.title" avatar class="mb-3">
        <v-list-tile-avatar>
            <v-icon class="blue white--text">assignment</v-icon>
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title v-if="item.draft === 0">Акт {{ item.type === 0 ? 'приёма' : 'передачи' }}</v-list-tile-title>
            <v-list-tile-title v-else>Черновик</v-list-tile-title>
            <template v-if="item.draft === 0">
                <v-list-tile-sub-title>Ответственный: {{ item.responsible_employee }}</v-list-tile-sub-title>
                <v-list-tile-sub-title>От {{ item.company_from }} к {{ item.company_to }}</v-list-tile-sub-title>
            </template>
            <v-list-tile-sub-title>
                Дата создания: {{ typeof item.created_at === 'object' 
                    ? item.created_at.date 
                    : item.created_at | moment("MMMM D, YYYY") }}
            </v-list-tile-sub-title>                                
        </v-list-tile-content>
        <v-list-tile-action>
            <v-btn v-if="item.draft === 0" icon ripple :to="{ name: 'StoRTActIndex', params: { act: item.id } }">
                <v-icon color="grey lighten-1">remove_red_eye</v-icon>
            </v-btn>
            <v-btn v-else icon ripple :to="{ name: 'StoEditRTAct', params: { act: item.id } }">
                <v-icon color="grey lighten-1">edit</v-icon>
            </v-btn>
        </v-list-tile-action>
    </v-list-tile>
</template>

<script>
export default {
    props: {
        item: Object
    }
}
</script>

<style>

</style>
