<template>
    <v-card>
        <v-card-title>
            Список компаний
            <v-spacer></v-spacer>
            <v-text-field append-icon="search" label="Поиск" single-line hide-details v-model="search"></v-text-field>
        </v-card-title>

        <v-data-table :headers="headers" :items="items" select-all v-model="selected" :search="search" class="elevation-1">
            <!-- Main temlpate -->
            <template slot="items" slot-scope="props">
                <td><v-checkbox primary hide-details v-model="props.selected"></v-checkbox></td>
                <td>{{ props.item.name }}</td>
                <td class="text-xs-right">{{ props.item.calories }}</td>
                <td class="text-xs-right">{{ props.item.fat }}</td>
                <td class="text-xs-right">{{ props.item.carbs }}</td>
                <td class="text-xs-right">{{ props.item.protein }}</td>
                <td class="text-xs-right">{{ props.item.iron }}</td>
            </template>

            <!-- No data template -->
            <template slot="no-data">
                <v-alert :value="true" color="error" icon="warning">
                    Нет доступных данных.
                </v-alert>
            </template>

            <!-- No results template -->
            <v-alert slot="no-results" :value="true" color="error" icon="warning">
                Реультатов "{{ search }}" не найдено.
            </v-alert>
        </v-data-table>    
    </v-card>
</template>

<script>
export default {
    props: ['headers', 'items'],
    data() {
        return {
            search: '',
            selected: []
        }
    }
};
</script>

<style>

</style>
