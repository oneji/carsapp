<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn small color="primary" @click.native="defectType.dialog = true" append>Добавить чек лист</v-btn>
                <v-btn small color="primary" @click.native="defect.dialog = true" append>Добавить деталь</v-btn>
                <v-btn small color="primary" @click.native="defectOption.dialog = true" append>Добавить состояние</v-btn>
                <v-btn small color="primary" @click.native="defectConclusion.dialog = true" append>Добавить заключение</v-btn>
            </v-flex>
        </v-layout>

        <v-layout row wrap>
            <!-- Defect types -->
            <v-flex xs12 sm6 md6 lg6>
                <v-card>
                    <v-card-title class="py-1">
                        Чек листы
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
                            <td :class="{ 'active-line': types.active === props.item.id }">{{ props.item.defect_type_name }}</td>
                            <td :class="{ 'active-line': types.active === props.item.id, 'justify-center': true }">
                                <v-btn icon class="mx-0" @click="showEditTypeDialog(props.item)">
                                    <v-icon color="teal">edit</v-icon>
                                </v-btn>
                                <v-btn icon class="mx-0" @click="showDefects(props.item)">
                                    <v-icon color="grey">info</v-icon>
                                </v-btn>
                                <v-btn icon class="mx-0" :loading="defectType.loading.delete === props.item.id" @click="deleteType(props.item)">
                                    <v-icon color="red">delete</v-icon>
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
            <v-flex xs12 sm6 md6 lg6 v-if="showDefectsTrigger">
                <v-card>
                    <v-card-title class="py-1">
                        Детали
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
                            <td :class="{ 'active-line': defects.active === props.item.id }">{{ props.item.defect_name }}</td>
                            <td :class="{ 'active-line': defects.active === props.item.id, 'justify-content': true }">
                                <v-btn icon class="mx-0" @click="showEditDefectDialog(props.item)">
                                    <v-icon color="teal">edit</v-icon>
                                </v-btn>
                                <v-btn icon class="mx-0" @click="showDefectOptions(props.item)">
                                    <v-icon color="grey">info</v-icon>
                                </v-btn>
                                <v-btn icon class="mx-0" :loading="defect.loading.delete === props.item.id" @click="deleteDefect(props.item)">
                                    <v-icon color="red">delete</v-icon>
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
            <v-flex xs12 sm6 md6 lg6 v-if="showDefectOptionsTrigger">
                <v-card>
                    <v-card-title class="py-1">
                        Состояния
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
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showEditOptionDialog(props.item)">
                                    <v-icon color="teal">edit</v-icon>
                                </v-btn>
                                <v-btn icon class="mx-0" :loading="defectOption.loading.delete === props.item.id" @click="deleteOption(props.item)">
                                    <v-icon color="red">delete</v-icon>
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
            <!-- Defect conclusions -->
            <v-flex xs12 sm6 md6 lg6 v-if="showDefectConclusionsTrigger">
                <v-card>
                    <v-card-title class="py-1">
                        Заключения
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="conclusions.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="conclusions.loading.table" :headers="conclusions.headers" :items="conclusions.items" :search="conclusions.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.conclusion_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showEditConclusionDialog(props.item)">
                                    <v-icon color="teal">edit</v-icon>
                                </v-btn>
                                <v-btn icon class="mx-0" :loading="defectConclusion.loading.delete === props.item.id" @click="deleteConclusion(props.item)">
                                    <v-icon color="red">delete</v-icon>
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
                            Нет результатов для "{{ conclusions.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>            
        </v-layout>

        <v-layout row wrap>
            <v-flex>
                <v-btn small color="success" @click.native="createEngineType = true" append>Добавить ДВС</v-btn>
                <v-btn small color="primary" @click.native="createTransmission = true" append>Добавить трансмиссию</v-btn>
            </v-flex>
        </v-layout>

        <v-layout row wrap>
            <v-flex xs12 sm6 md6 lg6>
                <EngineType :create="createEngineType" @close-create-modal="createEngineType = false" />
            </v-flex>

            <v-flex xs12 sm6 md6 lg6>
                <EngineTransmission :create="createTransmission" @close-create-modal="createTransmission = false" />
            </v-flex>
        </v-layout>

        <!-- Defect types modal -->
        <v-dialog v-model="defectType.dialog" max-width="500">
            <form @submit.prevent="addType" data-vv-scope="create-defect-type-form">
                <v-card>
                    <v-card-title class="headline">Добавить чек лист</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectType.defect_type_name" name="defect_type_name" label="Чек лист" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="defect_type_name" data-vv-as='"Чек лист"' required
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
                    <v-card-title class="headline">Изменить чек лист</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectType.edit.defect_type_name" name="edit_defect_type_name" label="Чек лист" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_defect_type_name" data-vv-as='"Чек лист"' required
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
                    <v-card-title class="headline">Добавить деталь</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defect.defect_name" name="defect_name" label="Деталь" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="defect_name" data-vv-as='"Деталь"' required
                                    :error-messages="errors.collect('defect_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="types.selectItems" v-model="defect.defect_type_id" label="Выберите чек лист" prepend-icon="list"
                                    name="defect_type_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('defect_type_id')"
                                    data-vv-name="defect_type_id" data-vv-as='"Чек лист"'
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
                    <v-card-title class="headline">Изменить деталь</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defect.edit.defect_name" name="edit_defect_name" label="Деталь" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_defect_name" data-vv-as='"Деталь"' required
                                    :error-messages="errors.collect('edit_defect_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="types.selectItems" v-model="defect.edit.defect_type_id" label="Выберите чек лист" prepend-icon="list"
                                    name="edit_defect_type_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('edit_defect_type_id')"
                                    data-vv-name="edit_defect_type_id" data-vv-as='"Чек лист"'
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
                    <v-card-title class="headline">Добавить состояние</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectOption.defect_option_name" name="defect_option_name" label="Состояние" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="defect_option_name" data-vv-as='"Состояние"' required
                                    :error-messages="errors.collect('defect_option_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="defects.selectItems" v-model="defectOption.defect_id" label="Выберите деталь" prepend-icon="list"
                                    name="defect_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('defect_id')"
                                    data-vv-name="defect_id" data-vv-as='"Деталь"'
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
        <!-- Defect options edit modal -->
        <v-dialog v-model="defectOption.edit.dialog" max-width="500">
            <form @submit.prevent="editOption" data-vv-scope="edit-defect-option-form">
                <v-card>
                    <v-card-title class="headline">Изменить состояние</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectOption.edit.defect_option_name" name="edit_defect_option_name" label="Состояние" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_defect_option_name" data-vv-as='"Состояние"' required
                                    :error-messages="errors.collect('edit_defect_option_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="defects.selectItems" v-model="defectOption.edit.defect_id" label="Выберите деталь" prepend-icon="list"
                                    name="edit_defect_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('edit_defect_id')"
                                    data-vv-name="edit_defect_id" data-vv-as='"Деталь"'
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

        <!-- Defect conclusions modal -->
        <v-dialog v-model="defectConclusion.dialog" max-width="500">
            <form @submit.prevent="addConclusion" data-vv-scope="create-defect-conclusion-form">
                <v-card>
                    <v-card-title class="headline">Добавить заключение</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectConclusion.conclusion_name" name="conclusion_name" label="Заключение" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="conclusion_name" data-vv-as='"Заключение"' required
                                    :error-messages="errors.collect('conclusion_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="defects.selectItems" v-model="defectConclusion.defect_id" label="Выберите деталь" prepend-icon="list"
                                    name="defect_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('defect_id')"
                                    data-vv-name="defect_id" data-vv-as='"Деталь"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="defectConclusion.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="defectConclusion.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Defect conclusions edit modal -->
        <v-dialog v-model="defectConclusion.edit.dialog" max-width="500">
            <form @submit.prevent="editConclusion" data-vv-scope="edit-defect-conclusion-form">
                <v-card>
                    <v-card-title class="headline">Изменить заключение</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="defectConclusion.edit.conclusion_name" name="edit_defect_conclusion_name" label="Заключение" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="edit_defect_conclusion" data-vv-as='"Заключение"' required
                                    :error-messages="errors.collect('edit_defect_conclusion')"
                                ></v-text-field>

                                <v-select autocomplete :items="defects.selectItems" v-model="defectConclusion.edit.defect_id" label="Выберите деталь" prepend-icon="list"
                                    name="edit_defect_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('edit_defect_id')"
                                    data-vv-name="edit_defect_id" data-vv-as='"Деталь"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="defectConclusion.edit.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="defectConclusion.loading.button" flat="flat" type="submit">Изменить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
    </div>
</template>

<script>
import axios from '@/axios'

import EngineType from '@/components/Engine/EngineType'
import EngineTransmission from '@/components/Engine/EngineTransmission'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        EngineType,
        EngineTransmission
    },
    data() {
        return {
            types: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Чек лист', value: 'defect_type_name' }, 
                    { text: 'Действия', value: 'action' },
                ],

                search: '',
                loading: {
                    table: false
                },
                active: null
            },
            defects: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Деталь', value: 'defect_name' }, 
                    { text: 'Действия', value: 'action' },     
                ],

                search: '',
                loading: {
                    table: false
                },
                active: null
            },
            options: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Состояние', value: 'defect_option_name' },
                    { text: 'Действия', value: 'action' },     
                ],

                search: '',
                loading: {
                    table: false
                }
            },
            conclusions: {
                items: [],
                selectItems: [],
                headers: [
                    { text: 'Заключение', value: 'conclusion_name' },
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
                    button: false,
                    delete: false
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
                    button: false,
                    delete: false
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
                    button: false,
                    delete: false
                },
                edit: {
                    dialog: false,
                    id: '',
                    defect_option_name: '',
                    defect_id: '',
                    index: ''
                }
            },
            defectConclusion: {
                conclusion_name: '',
                dialog: false,
                loading: {
                    button: false,
                    delete: null
                },
                edit: {
                    dialog: false,
                    id: '',
                    conclusion_name: '',
                    defect_id: '',
                    index: ''
                }
            },
            createEngineType: false,
            createTransmission: false,

            showDefectsTrigger: false,
            showDefectOptionsTrigger: false,
            showDefectConclusionsTrigger: false,
        }
    },
    methods: {
        showDefects(type) {
            this.options.items = [];
            this.showDefectOptionsTrigger = false;
            this.defects.items = [...type.defects];
            this.showDefectsTrigger = true;
            this.types.active = type.id;
        },

        showDefectOptions(defect) {
            this.options.items = [...defect.defect_options];
            this.conclusions.items = [...defect.defect_conclusions];
            this.showDefectOptionsTrigger = true;
            this.showDefectConclusionsTrigger = true;
            this.defects.active = defect.id;
        },

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
                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                text: response.data.message,
                                active: true
                            });
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
                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                text: response.data.message,
                                active: true
                            });
                        })
                        .catch(error => console.log(error));
                    }
                });
        },
        
        addDefect() {
            this.$validator.validateAll('create-defect-form')
                .then(success => {
                    if(success) {
                        this.defect.loading.button = true;
                        axios.post(`/sto/${this.$route.params.slug}/defects`, {
                            'defect_name': this.defect.defect_name,
                            'defect_type_id': this.defect.defect_type_id
                        })
                        .then(response => {
                            if(!response.data.success) {
                                this.$store.dispatch('showSnackbar', {
                                    color: 'error',
                                    text: response.data.message,
                                    active: true
                                });    
                            } else {
                                this.defects.items.push(response.data.defect);
                                this.defects.selectItems.push({
                                    text: response.data.defect.defect_name,
                                    value: response.data.defect.id
                                });
                                this.$store.dispatch('showSnackbar', {
                                    color: 'success',
                                    text: response.data.message,
                                    active: true
                                });
                            }
                            this.defect.loading.button = false; 
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
                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                text: response.data.message,
                                active: true
                            });
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
                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                text: response.data.message,
                                active: true
                            });
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
                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                text: response.data.message,
                                active: true
                            });
                        })
                        .catch(error => console.log(error));
                    }
                });
        },

        addConclusion() {
            this.$validator.validateAll('create-defect-conclusion-form')
                .then(success => {
                    if(success) {
                        this.defectConclusion.loading.button = true;
                        axios.post(`/sto/${this.$route.params.slug}/defects/conclusions`, {
                            'conclusion_name': this.defectConclusion.conclusion_name,
                            'defect_id': this.defectConclusion.defect_id
                        })
                        .then(response => {
                            this.conclusions.items.push(response.data.conclusion);
                            this.conclusions.selectItems.push({
                                text: response.data.conclusion.conclusion_name,
                                value: response.data.conclusion.id
                            });
                            this.defectConclusion.loading.button = false;
                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                text: response.data.message,
                                active: true
                            });
                        })
                        .catch(error => console.log(error));
                    } else {
                        console.log('options validation...');
                    }
                });
        },

        showEditConclusionDialog(conclusion) {
            this.defectConclusion.edit.id = conclusion.id;
            this.defectConclusion.edit.conclusion_name = conclusion.conclusion_name;
            this.defectConclusion.edit.defect_id = conclusion.defect_id;
            this.defectConclusion.edit.index = this.conclusions.items.indexOf(conclusion);
            this.defectConclusion.edit.dialog = true;
        },

        editConclusion() {
            this.$validator.validateAll('edit-defect-conclusions-form')
                .then(success => {
                    if(success) {
                        this.defectConclusion.loading.button = true;
                        axios.put(`/sto/${this.$route.params.slug}/defects/conclusions/${this.defectConclusion.edit.id}`, {
                            'conclusion_name': this.defectConclusion.edit.conclusion_name,
                            'defect_id': this.defectConclusion.edit.defect_id
                        })
                        .then(response => {
                            this.defectConclusion.loading.button = false;
                            this.conclusions.items[this.defectConclusion.edit.index].conclusion_name = this.defectConclusion.edit.conclusion_name;
                            this.conclusions.items[this.defectConclusion.edit.index].defect_id = this.defectConclusion.edit.defect_id;
                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                text: response.data.message,
                                active: true
                            });
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
                    console.log(response.data);
                    this.types.items = response.data.allDefects;
                    this.defects.items = [];
                    this.options.items = [];
                    this.conclusions.items = [];

                    response.data.allDefects.map(type => {
                        this.types.selectItems.push({
                            text: type.defect_type_name,
                            value: type.id
                        });

                        type.defects.map(defect => {
                            // this.defects.items.push(defect);
                            this.defects.selectItems.push({
                                text: defect.defect_name,
                                value: defect.id
                            });

                            defect.defect_options.map(option => {
                                // this.options.items.push(option);
                                this.options.selectItems.push({
                                    text: option.defect_option_name,
                                    value: option.id
                                });
                            });

                            defect.defect_conclusions.map(conclusion => {
                                this.conclusions.selectItems.push({
                                    text: conclusion.conclusion_name,
                                    value: conclusion.id
                                });
                            });
                        });
                    });

                    this.types.loading.table = false;
                    this.defects.loading.table = false;
                    this.options.loading.table = false;
                })
                .catch(error => console.log(error));
        },

        // Delete methods
        deleteType(defectType) {
            this.defectType.loading.delete = defectType.id;
            axios.delete(`/sto/${this.$route.params.slug}/defects/types/${defectType.id}`)
                .then(response => {
                    this.types.items = this.types.items.filter(def => def.id !== defectType.id);
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        text: response.data.message,
                        active: true
                    });
                })
                .catch(error => console.log(error));
        },

        deleteDefect(defect) {
            this.defect.loading.delete = defect.id;
            axios.delete(`/sto/${this.$route.params.slug}/defects/${defect.id}`)
                .then(response => {
                    this.defects.items = this.defects.items.filter(def => def.id !== defect.id);
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        text: response.data.message,
                        active: true
                    });
                })
                .catch(error => console.log(error));
        },

        deleteOption(option) {
            this.defectOption.loading.delete = option.id;
            axios.delete(`/sto/${this.$route.params.slug}/defects/options/${option.id}`)
                .then(response => {
                    this.options.items = this.options.items.filter(opt => opt.id !== option.id);
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        text: response.data.message,
                        active: true 
                    });
                })
                .catch(error => console.log(error));
        },

        deleteConclusion(conclusion) {
            this.defectConclusion.loading.delete = conclusion.id;
            axios.delete(`/sto/${this.$route.params.slug}/defects/conclusions/${conclusion.id}`)
                .then(response => {
                    this.conclusions.items = this.conclusions.items.filter(conc => conc.id !== conclusion.id);
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        text: response.data.message,
                        active: true 
                    });
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
    .active-line {
        background-color: #eee;
    }
</style>
