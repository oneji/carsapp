<template>
    <div>    
        <!-- List of engine types -->
        <v-card>
            <v-card-title class="py-1">
                Типы двигателя
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="types.search"
                    append-icon="search"
                    label="Поиск"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table :loading="types.loading.table" :headers="types.headers" :items="types.items" :search="types.search">
                <template slot="items" slot-scope="props">
                    <td>{{ props.item.engine_type_name }}</td>
                    <td class="justify-center">
                        <v-btn icon class="mx-0" @click="showEditTypeModal(props.item)">
                            <v-icon color="teal">edit</v-icon>
                        </v-btn>
                    </td>
                </template>
                <!-- No data slot -->
                <template slot="no-data">
                    <v-alert :value="true" outline color="info" icon="warning">
                        Нет данных для отображения.
                    </v-alert>
                </template>
                <!-- No results slot -->
                <v-alert slot="no-results" :value="true" color="error" icon="warning">
                    Нет результатов для "{{ types.search }}".
                </v-alert>
            </v-data-table>
        </v-card>

        <!-- Engine types modal -->
        <v-dialog v-model="createTypeModal" max-width="500" @input="$emit('close-create-modal')">
            <form @submit.prevent="addType" data-vv-scope="create-engine-type-form">
                <v-card>
                    <v-card-title class="headline">Добавить тип ДВС</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="engine_type_name" name="engine_type_name" label="Тип ДВС" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="engine_type_name" data-vv-as='"Тип ДВС"' required
                                    :error-messages="errors.collect('engine_type_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="$emit('close-create-modal')">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="types.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Engine types edit modal -->
        <v-dialog v-model="edit.dialog" max-width="500">
            <form @submit.prevent="editType" data-vv-scope="edit-engine-type-form">
                <v-card>
                    <v-card-title class="headline">Изменить тип ДВС</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="edit.engine_type_name" name="edit_engine_type_name" label="Тип ДВС" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_engine_type_name" data-vv-as='"Тип ДВС"' required
                                    :error-messages="errors.collect('edit_engine_type_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="edit.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="types.loading.button" flat="flat" type="submit">Изменить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <!-- Snackbar -->
        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import axios from '@/axios'
import snackbar from '@/components/mixins/snackbar'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    props: {
        create: {
            type: Boolean,
            default: false
        }
    },
    watch: {
        create() {
            if(this.create) 
                this.createTypeModal = true;                
            else
                this.createTypeModal = false;
        },
    },
    data() {
        return {
            types: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Тип двигателя', value: 'engine_type_name' }, 
                    { text: 'Действия', value: 'action' },                     
                ],

                search: '',
                loading: {
                    table: false,
                    button: false
                },
            },
            edit: {
                id: '',
                engine_type_name: '',
                index: '',
                dialog: '',
            },
            engine_type_name: '',
            createTypeModal: false
        }
    },
    methods: {
        getTypes() {
            this.types.loading.table = true;
            axios.get(`/sto/${this.$route.params.slug}/engine/types`)
                .then(response => {
                    this.types.items = response.data;
                    this.types.loading.table = false;
                })
                .catch(error => console.log(error));
        },
        addType() {
            this.$validator.validateAll('create-engine-type-form')
                .then(success => {
                    if(success) {
                        this.types.loading.button = true;
                        axios.post(`/sto/${this.$route.params.slug}/engine/types`, { 'engine_type_name': this.engine_type_name })
                            .then(response => {
                                this.types.items.push(response.data.type);

                                this.types.loading.button = false;
                                this.snackbar.color = 'success';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
                            })
                            .catch(error => console.log(error))
                    }
                });
        },
        showEditTypeModal(type) {
            this.edit.id = type.id;
            this.edit.engine_type_name = type.engine_type_name;
            this.edit.index = this.types.items.indexOf(type);
            this.edit.dialog = true;
        },
        editType() {
            this.$validator.validateAll() 
                .then(success => {
                    if(success) {
                        this.types.loading.button = true;
                        axios.put(`/sto/${this.$route.params.slug}/engine/types/${this.edit.id}`, { 'engine_type_name': this.edit.engine_type_name })
                            .then(response => {
                                this.types.items[this.edit.index].engine_type_name = this.edit.engine_type_name;

                                this.types.loading.button = false;
                                this.snackbar.color = 'success';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
                            })
                            .catch(error => console.log(error));
                    }
                });
        }
    },
    created() {
        this.getTypes();
    }
}
</script>

<style>

</style>
