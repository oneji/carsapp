<template>
    <div>    
        <!-- List of transmissions -->
        <v-card>
            <v-card-title class="py-1">
                Трансмиссия
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="transmissions.search"
                    append-icon="search"
                    label="Поиск"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table :loading="transmissions.loading.table" :headers="transmissions.headers" :items="transmissions.items" :search="transmissions.search">
                <template slot="items" slot-scope="props">
                    <td>{{ props.item.transmission_name }}</td>
                    <td class="justify-center">
                        <v-btn icon class="mx-0" @click="showEditTransmissionModal(props.item)">
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
                    Нет результатов для "{{ transmissions.search }}".
                </v-alert>
            </v-data-table>
        </v-card>
        
        <!-- Transmission modal -->
        <v-dialog v-model="createTransmissionModal" max-width="500" @input="$emit('close-create-modal')">
            <form @submit.prevent="addTransmission" data-vv-scope="create-transmission-form">
                <v-card>
                    <v-card-title class="headline">Добавить трансмиссию</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="transmission_name" name="transmission_name" label="Трансмиссия" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="transmission_name" data-vv-as='"Трансмиссия"' required
                                    :error-messages="errors.collect('transmission_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="$emit('close-create-modal')">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="transmissions.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Transmissions edit modal -->
        <v-dialog v-model="edit.dialog" max-width="500">
            <form @submit.prevent="editTransmission" data-vv-scope="edit-engine-type-form">
                <v-card>
                    <v-card-title class="headline">Изменить трансмиссию</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="edit.transmission_name" name="edit_transmission_name" label="Трансмиссия" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_transmission_name" data-vv-as='"Трансмиссия"' required
                                    :error-messages="errors.collect('edit_transmission_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="edit.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="transmissions.loading.button" flat="flat" type="submit">Изменить</v-btn>
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
                this.createTransmissionModal = true;                
            else
                this.createTransmissionModal = false;
        },
    },    
    data() {
        return {
            transmissions: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Трансмиссия', value: 'transmission_name' }, 
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
                transmission_name: '',
                index: '',
                dialog: '',
            },
            transmission_name: '',
            createTransmissionModal: false
        }
    },
    methods: {
        getTransmissions() {
            this.transmissions.loading.table = true;
            axios.get(`/sto/${this.$route.params.slug}/engine/transmissions`)
                .then(response => {
                    this.transmissions.items = response.data;
                    this.transmissions.loading.table = false;
                })
                .catch(error => console.log(error));
        },
        addTransmission() {
            this.$validator.validateAll('create-transmission-form')
                .then(success => {
                    if(success) {
                        this.transmissions.loading.button = true;
                        axios.post(`/sto/${this.$route.params.slug}/engine/transmissions`, { 'transmission_name': this.transmission_name })
                            .then(response => {
                                this.transmissions.items.push(response.data.transmission);

                                this.transmissions.loading.button = false;
                                this.snackbar.color = 'success';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
                            })
                            .catch(error => console.log(error))
                    }
                });
        },
        showEditTransmissionModal(type) {
            this.edit.id = type.id;
            this.edit.transmission_name = type.transmission_name;
            this.edit.index = this.transmissions.items.indexOf(type);
            this.edit.dialog = true;
        },
        editTransmission() {
            this.$validator.validateAll() 
                .then(success => {
                    if(success) {
                        this.transmissions.loading.button = true;
                        axios.put(`/sto/${this.$route.params.slug}/engine/transmissions/${this.edit.id}`, { 'transmission_name': this.edit.transmission_name })
                            .then(response => {
                                this.transmissions.items[this.edit.index].transmission_name = this.edit.transmission_name;

                                this.transmissions.loading.button = false;
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
        this.getTransmissions();
    }
}
</script>

<style>

</style>
