<template>
    <v-card>
        <v-card-title>
        Пользователи
        <v-spacer></v-spacer>
        <v-text-field v-model="search" append-icon="search" label="Поиск" single-line hide-details></v-text-field>
        </v-card-title>
        <v-data-table :search="search" v-model="selected" :headers="headers" :items="items" select-all item-key="id" class="elevation-1">
            <template slot="headers" slot-scope="props">
                <tr>
                    <th>
                        <v-checkbox :input-value="props.all" :indeterminate="props.indeterminate" primary hide-details @click.native="toggleAll"></v-checkbox>
                    </th>
                    <th v-for="header in props.headers" :key="header.text" :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '']" 
                        @click="changeSort(header.value)"
                    >
                        <v-icon small>arrow_upward</v-icon>
                        {{ header.text }}
                    </th>
                </tr>
            </template>
            <template slot="items" slot-scope="props">
            <tr :active="props.selected" @click="props.selected = !props.selected">
                <td>
                    <v-checkbox :input-value="props.selected" primary hide-details></v-checkbox>
                </td>
                <td class="text-xs-left">{{ props.item.fullname }}</td>
                <td class="text-xs-left">{{ props.item.email }}</td>

                <td class="justify-center layout px-0">
                    <v-btn icon class="mx-0" @click="editItem(props.item)">
                        <v-icon color="teal">edit</v-icon>
                    </v-btn>
                    <v-btn icon class="mx-0" @click="deleteItem(props.item)">
                        <v-icon color="pink">delete</v-icon>
                    </v-btn>
                </td>
            </tr>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    props: ['items', 'headers'],
    data() {
        return {
            search: '',
            pagination: {
                sortBy: 'name'
            },
            selected: []
        }
    },
    methods: {
        toggleAll () {
        if (this.selected.length) this.selected = []
            else this.selected = this.items.slice()
        },
        changeSort (column) {
            if (this.pagination.sortBy === column) {
                this.pagination.descending = !this.pagination.descending
            } else {
                this.pagination.sortBy = column
                this.pagination.descending = false
            }
        }
    }
}
</script>

<style>

</style>
