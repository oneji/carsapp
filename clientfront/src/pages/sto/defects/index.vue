<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" @click.native="defectType.dialog = true" append>Добавить тип дефекта</v-btn>
                <v-btn color="primary" @click.native="defect.dialog = true" append>Добавить дефект</v-btn>
                <v-btn color="primary" @click.native="defectOption.dialog = true" append>Добавить вид дефекта</v-btn>
            </v-flex>
        </v-layout>

        <v-layout row wrap>
            <!-- Defect types -->
            <v-flex xs12 sm6 md4 lg4>
                <v-card>
                    <v-card-title class="py-1">
                        Типы дефектов
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
                            <td>{{ props.item.defect_type_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showEditTypeDialog(props.item)">
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
            </v-flex>
            <!-- Defects -->
            <v-flex xs12 sm6 md4 lg4>
                <v-card>
                    <v-card-title class="py-1">
                        Дефекты
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="defects.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="defects.loading.table" :headers="defects.headers" :items="defects.items" :search="defects.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.defect_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showEditDefectDialog(props.item)">
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
                            Нет результатов для "{{ defects.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>
            <!-- Defect options -->
            <v-flex xs12 sm6 md4 lg4>
                <v-card>
                    <v-card-title class="py-1">
                        Вид дефекта
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="options.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="options.loading.table" :headers="options.headers" :items="options.items" :search="options.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.defect_option_name }}</td>
                            <td>{{ props.item.defect_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showEditOptionDialog(props.item)">
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
                            Нет результатов для "{{ options.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>            
        </v-layout>

        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" @click.native="createEngineType = true" append>Добавить ДВС</v-btn>
                <v-btn color="primary" @click.native="createTransmission = true" append>Добавить трансмиссию</v-btn>
            </v-flex>
        </v-layout>

        <v-layout>
            <v-flex xs12 sm6 md4 lg4>
                <engine-type :create="createEngineType" @close-create-modal="createEngineType = false" />
            </v-flex>

            <v-flex xs12 sm6 md4 lg4>
                <engine-transmission :create="createTransmission" @close-create-modal="createTransmission = false" />
            </v-flex>
        </v-layout>

        <!-- Defect types modal -->
        <v-dialog v-model="defectType.dialog" max-width="500">
            <form @submit.prevent="addType" data-vv-scope="create-defect-type-form">
                <v-card>
                    <v-card-title class="headline">Добавить тип дефекта</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectType.defect_type_name" name="defect_type_name" label="Тип дефекта" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="defect_type_name" data-vv-as='"Тип дефекта"' required
                                    :error-messages="errors.collect('defect_type_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="defectType.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="defectType.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Defect types edit modal -->
        <v-dialog v-model="defectType.edit.dialog" max-width="500">
            <form @submit.prevent="editType" data-vv-scope="edit-defect-type-form">
                <v-card>
                    <v-card-title class="headline">Изменить тип дефекта</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectType.edit.defect_type_name" name="edit_defect_type_name" label="Тип дефекта" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_defect_type_name" data-vv-as='"Тип дефекта"' required
                                    :error-messages="errors.collect('edit_defect_type_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="defectType.edit.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="defectType.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <!-- Defects modal -->
        <v-dialog v-model="defect.dialog" max-width="500">
            <form @submit.prevent="addDefect" data-vv-scope="create-defect-form">
                <v-card>
                    <v-card-title class="headline">Добавить дефект</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defect.defect_name" name="defect_name" label="Дефект" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="defect_name" data-vv-as='"Дефект"' required
                                    :error-messages="errors.collect('defect_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="types.selectItems" v-model="defect.defect_type_id" label="Выберите тип дефекта" prepend-icon="list"
                                    name="defect_type_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('defect_type_id')"
                                    data-vv-name="defect_type_id" data-vv-as='"Тип дефекта"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="defect.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="defect.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Defects edit modal -->
        <v-dialog v-model="defect.edit.dialog" max-width="500">
            <form @submit.prevent="editDefect" data-vv-scope="edit-defect-form">
                <v-card>
                    <v-card-title class="headline">Изменить дефект</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defect.edit.defect_name" name="edit_defect_name" label="Дефект" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_defect_name" data-vv-as='"Дефект"' required
                                    :error-messages="errors.collect('edit_defect_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="types.selectItems" v-model="defect.edit.defect_type_id" label="Выберите тип дефекта" prepend-icon="list"
                                    name="edit_defect_type_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('edit_defect_type_id')"
                                    data-vv-name="edit_defect_type_id" data-vv-as='"Тип дефекта"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="defect.edit.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="defect.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        
        <!-- Defect options modal -->
        <v-dialog v-model="defectOption.dialog" max-width="500">
            <form @submit.prevent="addOption" data-vv-scope="create-defect-option-form">
                <v-card>
                    <v-card-title class="headline">Добавить вид дефекта</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectOption.defect_option_name" name="defect_option_name" label="Вид дефекта" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="defect_option_name" data-vv-as='"Вид дефекта"' required
                                    :error-messages="errors.collect('defect_option_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="defects.selectItems" v-model="defectOption.defect_id" label="Выберите дефект" prepend-icon="list"
                                    name="defect_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('defect_id')"
                                    data-vv-name="defect_id" data-vv-as='"Вид дефекта"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="defectOption.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="defectOption.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Defect options modal -->
        <v-dialog v-model="defectOption.edit.dialog" max-width="500">
            <form @submit.prevent="editOption" data-vv-scope="edit-defect-option-form">
                <v-card>
                    <v-card-title class="headline">Изменить вид дефекта</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectOption.edit.defect_option_name" name="edit_defect_option_name" label="Вид дефекта" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_defect_option_name" data-vv-as='"Вид дефекта"' required
                                    :error-messages="errors.collect('edit_defect_option_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="defects.selectItems" v-model="defectOption.edit.defect_id" label="Выберите дефект" prepend-icon="list"
                                    name="edit_defect_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('edit_defect_id')"
                                    data-vv-name="edit_defect_id" data-vv-as='"Вид дефекта"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="defectOption.edit.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="defectOption.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import axios from '@/axios'
import snackbar from '@/components/mixins/snackbar'

import EngineType from '@/components/Engine/EngineType'
import EngineTransmission from '@/components/Engine/EngineTransmission'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    components: {
        'engine-type': EngineType,
        'engine-transmission': EngineTransmission
    },
    data() {
        return {
            types: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Тип дефекта', value: 'defect_type_name' }, 
                    { text: 'Действия', value: 'action' },
                ],

                search: '',
                loading: {
                    table: false
                }
            },
            defects: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Дефект', value: 'defect_name' }, 
                    { text: 'Действия', value: 'action' },     
                ],

                search: '',
                loading: {
                    table: false
                }
            },
            options: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Вид дефекта', value: 'defect_option_name' }, 
                    { text: 'Дефект', value: 'defect_name' }, 
                    { text: 'Действия', value: 'action' },     
                ],

                search: '',
                loading: {
                    table: false
                }
            },

            defectType: {
                defect_type_name: '',
                dialog: false,
                loading: {
                    button: false
                },
                edit: {
                    dialog: false,
                    id: '',
                    defect_type_name: '',
                    index: ''
                }
            },
            defect: {
                defect_name: '',
                defect_type_id: '',
                dialog: false,
                loading: {
                    button: false
                },
                edit: {
                    dialog: false,
                    id: '',
                    defect_name: '',
                    defect_type_id: '',
                    index: ''
                }
            },
            defectOption: {
                defect_option_name: '',
                dialog: false,
                loading: {
                    button: false
                },
                edit: {
                    dialog: false,
                    id: '',
                    defect_option_name: '',
                    defect_id: '',
                    index: ''
                }
            },
            createEngineType: false,
            createTransmission: false
        }
    },
    methods: {
        addType() {            
            this.$validator.validateAll('create-defect-type-form')
                .then(result => {
                    if(result) {
                        this.defectType.loading.button = true;
                        axios.post(`/sto/${this.$route.params.slug}/defects/types`, { 'defect_type_name': this.defectType.defect_type_name })
                        .then(response => {
                            this.types.items.push(response.data.type);
                            this.types.selectItems.push({
                                text: response.data.type.defect_type_name,
                                value: response.data.type.id
                            });
                            this.defectType.loading.button = false;
                            this.successSnackbar(response.data.message);
                        })
                        .catch(error => console.log(error));
                    }
                });
        },

        showEditTypeDialog(type) {
            this.defectType.edit.id = type.id;
            this.defectType.edit.defect_type_name = type.defect_type_name;
            this.defectType.edit.index = this.types.items.indexOf(type);
            this.defectType.edit.dialog = true;
        },

        editType() {
            this.$validator.validateAll('edit-defect-type-form')
                .then(success => {
                    if(success) {
                        this.defectType.loading.button = true;
                        axios.put(`/sto/${this.$route.params.slug}/defects/types/${this.defectType.edit.id}`, {
                            'defect_type_name': this.defectType.edit.defect_type_name
                        })
                        .then(response => {
                            this.defectType.loading.button = false;
                            this.types.items[this.defectType.edit.index].defect_type_name = this.defectType.edit.defect_type_name;
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                        })
                        .catch(error => console.log(error));
                    }
                });
        },
        
        addDefect() {
            this.$validator.validateAll('create-defect-form')
                .then(success => {
                    if(success) {
                        this.defects.loading.button = true;
                        axios.post(`/sto/${this.$route.params.slug}/defects`, {
                            'defect_name': this.defect.defect_name,
                            'defect_type_id': this.defect.defect_type_id
                        })
                        .then(response => {
                            this.defects.items.push(response.data.defect);
                            this.defects.selectItems.push({
                                text: response.data.defect.defect_name,
                                value: response.data.defect.id
                            });
                            this.defect.loading.button = false; 
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                        })
                        .catch(error => console.log(error));
                    } else {
                        console.log('validation...')
                    }
            });
        },

        showEditDefectDialog(defect) {
            this.defect.edit.id = defect.id;
            this.defect.edit.defect_name = defect.defect_name;
            this.defect.edit.defect_type_id = defect.defect_type_id;
            this.defect.edit.index = this.defects.items.indexOf(defect);
            this.defect.edit.dialog = true;
        },

        editDefect() {
            this.$validator.validateAll('edit-defect-form')
                .then(success => {
                    if(success) {
                        this.defect.loading.button = true;
                        axios.put(`/sto/${this.$route.params.slug}/defects/${this.defect.edit.id}`, {
                            'defect_name': this.defect.edit.defect_name,
                            'defect_type_id': this.defect.edit.defect_type_id
                        })
                        .then(response => {
                            this.defect.loading.button = false;
                            this.defects.items[this.defect.edit.index].defect_name = this.defect.edit.defect_name;
                            this.defects.items[this.defect.edit.index].defect_type_id = this.defect.edit.defect_type_id;
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                        })
                        .catch(error => console.log(error));
                    }
                });
        },

        addOption() {
            this.$validator.validateAll('create-defect-option-form')
                .then(success => {
                    if(success) {
                        this.defectOption.loading.button = true;
                        axios.post(`/sto/${this.$route.params.slug}/defects/options`, {
                            'defect_option_name': this.defectOption.defect_option_name,
                            'defect_id': this.defectOption.defect_id
                        })
                        .then(response => {
                            this.options.items.push(response.data.option);
                            this.options.selectItems.push({
                                text: response.data.option.defect_option_name,
                                value: response.data.option.id
                            });
                            this.defectOption.loading.button = false;
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;

                        })
                        .catch(error => console.log(error));
                    } else {
                        console.log('options validation...');
                    }
                });
        },

        showEditOptionDialog(option) {
            this.defectOption.edit.id = option.id;
            this.defectOption.edit.defect_option_name = option.defect_option_name;
            this.defectOption.edit.defect_id = option.defect_id;
            this.defectOption.edit.index = this.options.items.indexOf(option);
            this.defectOption.edit.dialog = true;
        },

        editOption() {
            this.$validator.validateAll('edit-defect-option-form')
                .then(success => {
                    if(success) {
                        this.defectOption.loading.button = true;
                        axios.put(`/sto/${this.$route.params.slug}/defects/options/${this.defectOption.edit.id}`, {
                            'defect_option_name': this.defectOption.edit.defect_option_name,
                            'defect_id': this.defectOption.edit.defect_id
                        })
                        .then(response => {
                            this.defectOption.loading.button = false;
                            this.options.items[this.defectOption.edit.index].defect_option_name = this.defectOption.edit.defect_option_name;
                            this.options.items[this.defectOption.edit.index].defect_id = this.defectOption.edit.defect_id;
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                        })
                        .catch(error => console.log(error));
                    }
                });
        },

        getFullInfo() {
            this.types.loading.table = true;
            this.defects.loading.table = true;
            this.options.loading.table = true;

            axios.get(`/sto/${this.$route.params.slug}/defects/all`)
                .then(response => {
                    this.types.items = response.data.allDefects;
                    this.defects.items = [];
                    this.options.items = [];

                    response.data.allDefects.map(type => {
                        this.types.selectItems.push({
                            text: type.defect_type_name,
                            value: type.id
                        });

                        type.defects.map(defect => {
                            this.defects.items.push(defect);
                            this.defects.selectItems.push({
                                text: defect.defect_name,
                                value: defect.id
                            });

                            defect.defect_options.map(option => {
                                this.options.items.push(option);
                                this.options.selectItems.push({
                                    text: option.defect_option_name,
                                    value: option.id
                                });
                            });
                        });
                    });

                    this.types.loading.table = false;
                    this.defects.loading.table = false;
                    this.options.loading.table = false;
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.getFullInfo();
    }
}
</script>

<style>

</style>
