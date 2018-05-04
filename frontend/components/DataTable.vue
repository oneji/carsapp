<template>
    <v-card>
        <v-card-title>
            Список компаний
            <v-spacer></v-spacer>
            <v-text-field append-icon="search" label="Поиск" single-line hide-details v-model="search"></v-text-field>
        </v-card-title>

        <v-data-table :headers="headers" :items="items" :select-all="selectAll" :loading="loading" v-model="selected" :search="search" class="elevation-1">
            <!-- Main temlpate -->
            <template slot="items" slot-scope="props">
                <td v-if="selectAll"><v-checkbox primary hide-details v-model="props.selected"></v-checkbox></td>
                <td>
                    <v-avatar :tile="tile" :size="avatarSize">
                        <img :src="`${assetURL}/uploads/logos/companies/${props.item.logo}`" :alt="props.item.company_name">
                    </v-avatar>
                </td>
                <td>{{ props.item.company_name }}</td>
                <td>{{ props.item.contact }}</td>
            </template>

            <!-- No data template -->
            <template slot="no-data">
                <v-alert outline :value="true" color="info" icon="warning">
                    Нет доступных данных.
                </v-alert>
            </template>

            <!-- No results template -->
            <template slot="no-results">
                <v-alert outline :value="true" color="info" icon="warning">
                    Результатов "{{ search }}" не найдено.
                </v-alert>
            </template>
        </v-data-table>    
    </v-card>
</template>

<script>
export default {
    props: ['headers', 'items', 'selectAll', 'loading'],
    computed: {
        assetURL() {
            return process.env.assetURL || null;
        },
        avatarSize () {
            return `${this.slider}px`
        }
    },
    data() {
        return {
            search: '',
            selected: [],
            slider: 56,
            tile: true
        }
    }
};
</script>

<style>

</style>
