<template>
    <div>
        <!-- Control buttons -->
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" @click.native="showChecklistDialog(null, 'create')" append>Добавить чек-лист</v-btn>
                <v-btn color="primary" @click.native="showChecklistItemDialog(null, 'create')" append>Добавить элемент чек листа</v-btn>
            </v-flex>
        </v-layout>
        <!-- Loading -->
        <v-layout style="position: relative;">
            <Loading :loading="loading.page" />
        </v-layout>
        
        <v-layout row wrap v-if="!loading.page">
            <!-- Checklists -->
            <v-flex xs12 sm6 md6 lg6>
                <v-card>
                    <v-card-title class="py-1">
                        Чек листы
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="checklists.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="loading.tables" :headers="checklists.tableHeaders" :items="checklists.items" :search="checklists.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.checklist_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showChecklistDialog(props.item, 'edit')">
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
                            Нет результатов для "{{ checklists.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>
            <!-- Checklist items -->
            <v-flex xs12 sm6 md6 lg6>
                <v-card>
                    <v-card-title class="py-1">
                        Элементы чек листа
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="checklistItems.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="loading.tables" :headers="checklistItems.tableHeaders" :items="checklistItems.items" :search="checklistItems.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.item_name }}</td>
                            <td>{{ props.item.checklist_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showChecklistItemDialog(props.item, 'edit')">
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
                            Нет результатов для "{{ checklistItems.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>
        </v-layout>

        <!-- Checklist modal -->
        <v-dialog v-model="checklists.dialog" max-width="500">
            <form @submit.prevent="executeChecklistAction" data-vv-scope="checklist-form">
                <v-card>
                    <v-card-title class="headline">
                        {{ checklists.dialogType === 'create' ? 'Добавить' : 'Изменить' }} чек лист
                    </v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="checklists.checklistName" name="checklist_name" label="Название чек листа" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="checklist_name" data-vv-as='"Название чек листа"' required
                                    :error-messages="errors.collect('checklist_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="checklists.dialog = false">Закрыть</v-btn>
                        <v-btn v-if="checklists.dialogType === 'create'" color="green darken-1" :loading="loading.checklistBtn" flat="flat" type="submit">Создать</v-btn>
                        <v-btn v-else-if="checklists.dialogType === 'edit'" color="green darken-1" :loading="loading.checklistBtn" flat="flat" type="submit">Изменить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Checklist item modal -->
        <v-dialog v-model="checklistItems.dialog" max-width="500">
            <form @submit.prevent="executeChecklistItemAction" data-vv-scope="checklist-item-form">
                <v-card>
                    <v-card-title class="headline">
                        {{ checklistItems.dialogType === 'create' ? 'Добавить' : 'Изменить' }} элемент чек листа
                    </v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="checklistItems.itemName" name="checklist_item_name" label="Элемент чек листа" prepend-icon="list"                 
                                    v-validate="'required'" 
                                    data-vv-name="checklist_item_name" data-vv-as='"Элемент чек листа"' required
                                    :error-messages="errors.collect('checklist_item_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="checklists.selectItems" v-model="checklistItems.checklistId" label="Выберите чек лист" prepend-icon="list"
                                    name="checklist_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('checklist_id')"
                                    data-vv-name="checklist_id" data-vv-as='"Чек лист"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="checklistItems.dialog = false">Закрыть</v-btn>
                        <v-btn v-if="checklistItems.dialogType === 'create'" color="green darken-1" :loading="loading.checklistItemBtn" flat="flat" type="submit">Создать</v-btn>
                        <v-btn v-else-if="checklistItems.dialogType === 'edit'" color="green darken-1" :loading="loading.checklistItemBtn" flat="flat" type="submit">Изменить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
    </div>
</template>

<script>
import axios from '@/axios'
import Loading from '@/components/Loading'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        Loading
    },
    data() {
        return {
            checklists: {
                items: [],
                selectItems: [],                
                checklistName: '',
                // Controls
                tableHeaders: [
                    { text: 'Чек лист', value: 'checklist_name' },
                    { text: 'Действия', value: 'actions' },
                ],
                search: '',
                dialog: false,
                dialogType: '',
                editObject: {}
            },
            checklistItems: {
                items: [],                
                itemName: '',
                checklistId: null,
                // Controls
                tableHeaders: [
                    { text: 'Элемент чек листа', value: 'item_name' },
                    { text: 'Чек лист', value: 'checklist_name' },
                    { text: 'Действия', value: 'actions' },
                ],
                search: '',
                dialog: false,
                dialogType: '',
                editObject: {}
            },
            loading: {
                page: false,
                tables: false,
                checklistBtn: false,
                checklistItemBtn: false
            }
        }
    },
    methods: {
        fetchChecklistsAndChecklistItems() {
            this.loading.page = true;
            axios.get('rt-act/all')
                .then(response => {
                    this.checklists.items = response.data.checklists;
                    this.checklistItems.items = response.data.checklist_items;
                    // Fill in items' select 
                    this.checklists.selectItems = response.data.checklists.map(list => {
                            return { text: list.checklist_name, value: list.id }
                        }
                    );
                    this.loading.page = false;
                })
                .catch(error => console.log(error));
        },
        addChecklist() {
            this.$validator.validateAll('checklist-form')
                .then(success => {
                    if(success) {
                        this.loading.checklistBtn = true;

                        axios.post('rt-act/checklists', { 'checklist_name': this.checklists.checklistName })
                            .then(response => {
                                const { checklist, message } = response.data;
                                
                                this.checklists.items.push(checklist);
                                this.checklists.selectItems.push({
                                    text: checklist.checklist_name,
                                    value: checklist.id
                                });
                                this.checklists.checklist_name = '';

                                this.loading.checklistBtn = false;
                                this.checklists.dialog = false;
                                this.$store.dispatch('showSnackbar', {
                                    color: 'success',
                                    active: true,
                                    text: message
                                });
                            })
                            .catch(error => console.log(error));
                    }
                });
        },
        editChecklist() {
            this.$validator.validateAll('checklist-form')
                .then(success => {
                    if(success) {
                        this.loading.checklistBtn = true;

                        axios.put(`rt-act/checklists/${this.checklists.editObject.id}`, { 'checklist_name': this.checklists.checklistName })
                            .then(response => {
                                let item = this.checklists.items.filter(it => it.id === this.checklists.editObject.id)[0];
                                item.checklist_name = this.checklists.checklistName;
                                this.checklists.checklist_name = '';
                                this.loading.checklistBtn = false;
                                this.checklists.dialog = false;
                                this.$store.dispatch('showSnackbar', {
                                    color: 'success',
                                    active: true,
                                    text: response.data.message
                                });
                            })
                            .catch(error => console.log(error));
                    }
                });
        },
        addChecklistItem() {
            this.$validator.validateAll('checklist-item-form')
                .then(success => {
                    if(success) {
                        this.loading.checklistItemBtn = true;
                        console.log('[Validation] OK!');

                        axios.post('rt-act/checklists/items', {
                            'item_name': this.checklistItems.itemName,
                            'rt_act_checklist_id': this.checklistItems.checklistId
                        })
                        .then(response => {
                            this.checklistItems.items.push(response.data.checklist_item);
                            this.loading.checklistItemBtn = false;
                            this.checklistItems.dialog = false;
                            this.checklistItems.itemName = '';
                            this.checklistItems.checklistId = null;

                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                active: true,
                                text: response.data.message
                            });

                        })
                        .catch(error => console.log(error));
                    }
                });
        },
        editChecklistItem() {
            console.log(this.checklistItems.editObject)

            this.$validator.validateAll('checklist-item-form')
                .then(success => {
                    if(success) {
                        this.loading.checklistItemBtn = true;
                        console.log('[Validation] OK!');

                        axios.put(`rt-act/checklists/items/${this.checklistItems.editObject.id}`, {
                            'item_name': this.checklistItems.itemName,
                            'rt_act_checklist_id': this.checklistItems.checklistId
                        })
                        .then(response => {
                            this.fetchChecklistsAndChecklistItems();
                            this.loading.checklistItemBtn = false;
                            this.checklistItems.dialog = false;
                            this.checklistItems.itemName = '';
                            this.checklistItems.checklistId = null;

                            this.$store.dispatch('showSnackbar', {
                                color: 'success',
                                active: true,
                                text: response.data.message
                            });
                        })
                        .catch(error => console.log(error));
                    }
                });
        },
        showChecklistDialog(item, type) {
            this.checklists.dialogType = type;
            this.checklists.dialog = true;
            if(type === 'edit') {
                this.checklists.editObject = item;
                this.checklists.checklistName = item.checklist_name;
            }
        },
        showChecklistItemDialog(item, type) {
            this.checklistItems.dialogType = type;            
            if(type === 'edit') {
                this.checklistItems.editObject = item;
                this.checklistItems.itemName = item.item_name;
                this.checklistItems.checklistId = item.rt_act_checklist_id;
            } 
            this.checklistItems.dialog = true;
        },
        executeChecklistAction() {
            if(this.checklists.dialogType === 'create')
                this.addChecklist();
            else
                this.editChecklist();
        },
        executeChecklistItemAction() {
            if(this.checklistItems.dialogType === 'create')
                this.addChecklistItem();
            else
                this.editChecklistItem();
        }
    },
    created() {
        this.fetchChecklistsAndChecklistItems();
    }
}
</script>

<style>

</style>
