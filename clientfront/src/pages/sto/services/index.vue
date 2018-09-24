<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" @click.native="serviceCategory.dialog = true" append>Добавить категорию услуги</v-btn>
                <v-btn color="info" @click.native="serviceType.dialog = true" append>Добавить услугу</v-btn>
            </v-flex>
        </v-layout>

        <v-layout row wrap>
            <v-flex xs12 sm6 md8 lg8>
                <v-card>
                    <v-card-title class="py-1">
                        Услуги
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
                            <td>{{ props.item.service_type_name }}</td>
                            <td>{{ props.item.service_category_name }}</td>
                            <td>{{ props.item.service_price }} сом.</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showEditDialog(props.item, 'service-type')">
                                    <v-icon color="green">edit</v-icon>
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

            <v-flex xs12 sm6 md4 lg4>
                <v-card>
                    <v-card-title class="py-1">
                        Категории услуг
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="categories.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="categories.loading.table" :headers="categories.headers" :items="categories.items" :search="categories.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.service_category_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="showEditDialog(props.item, 'service-category')">
                                    <v-icon color="green">edit</v-icon>
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
                            Нет результатов для "{{ categories.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>
        </v-layout>

        <!-- Service category modal -->
        <v-dialog v-model="serviceCategory.dialog" max-width="500">
            <form @submit.prevent="addCategory" data-vv-scope="create-service-category-form">
                <v-card>
                    <v-card-title class="headline">Добавить категорию услуги</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="serviceCategory.name" name="service_category_name" label="Название категории" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="service_category_name" data-vv-as='"Название категории"' required
                                    :error-messages="errors.collect('service_category_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="serviceCategory.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="serviceCategory.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Service type modal -->
        <v-dialog v-model="serviceType.dialog" max-width="500">
            <form @submit.prevent="addService" data-vv-scope="create-service-type-form">
                <v-card>
                    <v-card-title class="headline">Добавить услугу</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="serviceType.name" name="service_type_name" label="Название услуги" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="service_type_name" data-vv-as='"Название услуги"' required
                                    :error-messages="errors.collect('service_type_name')"
                                ></v-text-field>

                                <v-text-field type="text" v-model="serviceType.price" name="service_price" label="Цена за услугу" 
                                    prepend-icon="attach_money"                                
                                    suffix="сом."
                                ></v-text-field>

                                <v-switch label="Приблизительная цена:" ripple v-model="serviceType.approximatePrice"></v-switch>

                                <v-select autocomplete :items="serviceCategory.selectItems" v-model="serviceType.categoryID" label="Выберите категорию" prepend-icon="build"
                                    name="service_type_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('service_type_id')"
                                    data-vv-name="service_type_id" data-vv-as='"Категория"'
                                ></v-select>

                                <v-select autocomplete :items="defectOptions.selectItems" v-model="serviceType.defectOptionID" label="Выберите дефект" prepend-icon="build"
                                    name="defect_option_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('defect_option_id')"
                                    data-vv-name="defect_option_id" data-vv-as='"Дефект"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="serviceType.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="serviceType.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Edit service category modal -->
        <v-dialog v-model="editServiceCategory.dialog" max-width="500">
            <form @submit.prevent="editCategory" data-vv-scope="edit-service-category-form">
                <v-card>
                    <v-card-title class="headline">Изменить категорию услуги</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="editServiceCategory.name" name="service_category_name" label="Название категории" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="service_category_name" data-vv-as='"Название категории"' required
                                    :error-messages="errors.collect('service_category_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="editServiceCategory.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="editServiceCategory.loading.button" flat="flat" type="submit">Изменить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
        <!-- Edit service type modal -->
        <v-dialog v-model="editServiceType.dialog" max-width="500">
            <form @submit.prevent="editService" data-vv-scope="edit-service-type-form">
                <v-card>
                    <v-card-title class="headline">Изменить услугу</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="editServiceType.name" name="service_type_name" label="Название услуги" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="service_type_name" data-vv-as='"Название услуги"' required
                                    :error-messages="errors.collect('service_type_name')"
                                ></v-text-field>

                                <v-text-field type="text" v-model="editServiceType.price" name="service_price" label="Цена за услугу" 
                                    prepend-icon="attach_money"                                
                                    suffix="сом."
                                ></v-text-field>

                                <v-switch label="Приблизительная цена:" ripple v-model="editServiceType.approximatePrice"></v-switch>

                                <v-select autocomplete :items="serviceCategory.selectItems" v-model="editServiceType.categoryID" label="Выберите категорию" prepend-icon="build"
                                    name="service_type_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('service_type_id')"
                                    data-vv-name="service_type_id" data-vv-as='"Категория"'
                                ></v-select>

                                <v-select autocomplete :items="defectOptions.selectItems" v-model="editServiceType.defectOptionID" label="Выберите дефект" prepend-icon="build"
                                    name="defect_option_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('defect_option_id')"
                                    data-vv-name="defect_option_id" data-vv-as='"Дефект"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="editServiceType.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="editServiceType.loading.button" flat="flat" type="submit">Изменить</v-btn>
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

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    data() {
        return {
            categories: {
                items: [],
                headers: [
                    { text: 'Категория услуги', value: 'service_category_name' },
                    { text: 'Действия', value: 'actions' },
                ],

                search: '',
                loading: {
                    table: false
                },
            },
            types: {
                items: [],
                headers: [
                    { text: 'Услуга', value: 'service_type_name' }, 
                    { text: 'Категория', value: 'service_category_name' }, 
                    { text: 'Цена', value: 'service_price' }, 
                    { text: 'Действия', value: 'actions' }, 
                ],

                search: '',
                loading: {
                    table: false
                }
            },
            editServiceCategory: {
                name: '',
                dialog: false,
                loading: {
                    button: false
                },
            },
            serviceCategory: {
                id: null,
                name: '',
                dialog: false,
                loading: {
                    button: false
                },
                selectItems: []
            },
            editServiceType: {
                dialog: false,
                id: null,
                name: '',
                price: '',
                categoryID: '',
                defectOptionID: '',
                approximatePrice: '',
                loading: {
                    button: false
                }
            },
            serviceType: {
                name: '',
                price: null,
                categoryID: '',
                defectOptionID: '',
                approximatePrice: '',
                dialog: false,                
                loading: {
                    button: false
                }
            },
            defectTypes: [],
            defects: [],
            defectOptions: {
                items: [],
                selectItems: []
            },
        }
    },
    methods: {
        addCategory() {
            this.serviceCategory.loading.button = true;
            this.$validator.validateAll('create-service-category-form')
                .then(result => {
                    if(result) {
                        axios.post(`/sto/${this.$route.params.slug}/services/categories`, { 'service_category_name': this.serviceCategory.name })
                        .then(response => {
                            this.categories.items.push(response.data.category);
                            this.serviceCategory.loading.button = false;
                            this.serviceCategory.selectItems.push({
                                text: response.data.category.service_category_name,
                                value: response.data.category.id
                            });
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                        })
                        .catch(error => console.log(error));
                    }
                });
        },

        getCategories() {
            this.categories.loading.table = true;
            axios.get(`/sto/${this.$route.params.slug}/services/categories`)
                .then(response => {
                    this.categories.items = response.data;
                    response.data.map(category => {
                        this.serviceCategory.selectItems.push({
                            text: category.service_category_name,
                            value: category.id
                        });
                    });
                    this.categories.loading.table = false;
                })
                .catch(error => console.log(error));
        },

        editCategory() {
            this.editServiceCategory.loading.button = true;
            axios.put(`/sto/${this.$route.params.slug}/services/categories/${this.editServiceCategory.id}`, { service_category_name: this.editServiceCategory.name })
                .then(response => {
                    this.getServices();
                    this.getCategories();
                    this.editServiceCategory.loading.button = false;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                    this.editServiceCategory.dialog = false;
                })
                .catch(error => console.log(error));
        },

        addService() {
            this.serviceType.loading.button = true;
            this.$validator.validateAll('create-service-types-form')
                .then(result => {
                    if(result) {
                        axios.post(`/sto/${this.$route.params.slug}/services/types`, {
                            'service_type_name': this.serviceType.name,
                            'service_price': this.serviceType.price,
                            'service_category_id': this.serviceType.categoryID,
                            'defect_option_id': this.serviceType.defectOptionID,
                            'approximate': this.serviceType.approximatePrice    
                        })
                        .then(response => {
                            this.getServices();
                            this.serviceType.loading.button = false;
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                        })
                        .catch(error => console.log(error));
                    }
                });
        },

        showEditDialog(serviceItem, editDialog) {
            if(editDialog === 'service-type') {
                let item = this.types.items.filter(it => it.id === serviceItem.id)[0];
                this.editServiceType.id = item.id;
                this.editServiceType.name = item.service_type_name;
                this.editServiceType.price = item.service_price;
                this.editServiceType.approximatePrice = item.service_price === null ? true : false;
                this.editServiceType.categoryID = item.service_category_id;
                this.editServiceType.defectOptionID = item.defect_option_id;
                this.editServiceType.dialog = true;
            } else if(editDialog === 'service-category') {
                this.editServiceCategory.dialog = true;
                this.editServiceCategory.id = serviceItem.id;
                this.editServiceCategory.name = serviceItem.service_category_name;
                console.log(serviceItem);
            }
        },

        editService(serviceId) {
            this.editServiceType.loading.button = true;
            axios.put(`/sto/${this.$route.params.slug}/services/types/${this.editServiceType.id}`, {
                'service_type_name': this.editServiceType.name,
                'service_price': this.editServiceType.price,
                'service_category_id': this.editServiceType.categoryID,
                'defect_option_id': this.editServiceType.defectOptionID,
                'approximate': this.editServiceType.approximatePrice    
            })
            .then(response => {
                this.getServices();
                this.editServiceType.loading.button = false;
                this.snackbar.color = 'success';
                this.snackbar.text = response.data.message;
                this.snackbar.active = true;
                this.editServiceType.dialog = false;
            })
            .catch(error => console.log(error));
        },

        getServices() {
            this.types.loading.table = true;
            axios.get(`/sto/${this.$route.params.slug}/services/types`)
                .then(response => {
                    this.types.items = response.data;
                    this.types.loading.table = false;   
                })
                .catch(error => console.log(error));
        },

        getFullDefectInfo() {
            axios.get(`/sto/${this.$route.params.slug}/defects/info`)
                .then(response => {
                    let defectInfo = response.data.defect_info;

                    defectInfo.map(type => {                        
                        this.defectTypes.push(type);

                        type.defects.map(defect => {
                            this.defects.push(defect);

                            defect.defect_options.map(option => {
                                this.defectOptions.selectItems.push({
                                    text: defect.defect_name + ': ' + option.defect_option_name,
                                    value: option.id
                                });
                            });
                        });
                    });
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.getCategories();
        this.getServices();
        this.getFullDefectInfo();
    }
}
</script>

<style>

</style>
