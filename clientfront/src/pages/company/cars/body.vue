<template>
    <div>
        <v-layout row wrap>
            <v-flex xs12 sm12 md12 lg12>
                <v-btn color="success" @click.native="shape.dialog = true">Добавить кузов</v-btn>
                <v-btn color="info" @click.native="brand.dialog = true">Добавить марку</v-btn>
                <v-btn color="warning" @click.native="model.dialog = true">Добавить модель</v-btn>
            </v-flex>
        </v-layout>

        <v-layout row wrap>
            <!-- Car shapes -->
            <v-flex xs12 sm6 md6 lg6>
                <v-card>
                    <v-card-title class="py-1">
                        Кузова
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="shape.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="shape.loading.table" :headers="shape.headers" :items="shape.items" :search="shape.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.shape_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="deleteCarShape(props.item.id)">
                                    <v-icon color="pink">delete</v-icon>
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
                            Нет результатов для "{{ shape.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>
            <!-- Car brands -->
            <v-flex xs12 sm6 md6 lg6>
                <v-card>
                    <v-card-title class="py-1">
                        Кузова
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="brand.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="brand.loading.table" :headers="brand.headers" :items="brand.items" :search="brand.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.brand_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="deleteCarBrand(props.item.id)">
                                    <v-icon color="pink">delete</v-icon>
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
                            Нет результатов для "{{ brand.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>
            <!-- Car models -->
            <v-flex xs12 sm6 md6 lg6>
                <v-card>
                    <v-card-title class="py-1">
                        Модели
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="model.search"
                            append-icon="search"
                            label="Поиск"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-data-table :loading="model.loading.table" :headers="model.headers" :items="model.items" :search="model.search">
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.model_name }}</td>
                            <td>{{ props.item.brand_name }}</td>
                            <td class="justify-center">
                                <v-btn icon class="mx-0" @click="deleteCarModel(props.item.id)">
                                    <v-icon color="pink">delete</v-icon>
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
                            Нет результатов для "{{ model.search }}".
                        </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>
        </v-layout>

        <!-- Create new shape modal -->
        <v-dialog v-model="shape.dialog" max-width="500">
            <form @submit.prevent="createCarShape" data-vv-scope="create-shape-form">
                <v-card>
                    <v-card-title class="headline">Создать кузов</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="shape.shape_name" name="shape_name" label="Название кузова" prepend-icon="commute"                 
                                    v-validate="'required'" 
                                    data-vv-name="shape_name" data-vv-as='"Название кузова"' required
                                    :error-messages="errors.collect('shape_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="shape.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="shape.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <!-- Create new brand modal -->
        <v-dialog v-model="brand.dialog" max-width="500">
            <form @submit.prevent="createCarBrand" data-vv-scope="create-brand-form">
                <v-card>
                    <v-card-title class="headline">Создать марку</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="brand.brand_name" name="brand_name" label="Название марки" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="brand_name" data-vv-as='"Название марки"' required
                                    :error-messages="errors.collect('brand_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="brand.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="brand.loading.button" flat="flat" type="submit">Создать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <!-- Create new model modal -->
        <v-dialog v-model="model.dialog" max-width="500">
            <form @submit.prevent="createCarModel" data-vv-scope="create-model-form">
                <v-card>
                    <v-card-title class="headline">Создать марку</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>                    
                                <v-text-field type="text" v-model="model.model_name" name="model_name" label="Название модели" prepend-icon="directions_car"                 
                                    v-validate="'required'" 
                                    data-vv-name="model_name" data-vv-as='"Название модели"' required
                                    :error-messages="errors.collect('model_name')"
                                ></v-text-field>

                                <v-select autocomplete :items="brand.selectItems" v-model="model.brand_id" label="Выберите марку" prepend-icon="category"
                                    name="company_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('company_id')"
                                    data-vv-name="company_id" data-vv-as='"Компания"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="model.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="model.loading.button" flat="flat" type="submit">Создать</v-btn>
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

export default {
    $_veeValidate: {
        validator: 'new'
    },
    data() {
        return {     
            shape: {
                shape_name: '',
                loading: {
                    button: false,
                    table: false
                },
                dialog: false,
                items: [],
                headers: [ 
                    { text: 'Кузов', value: 'shape_name' },
                    { text: 'Действия', value: 'actions' },
                ],
                search: ''
            },

            brand: {
                brand_name: '',
                loading: {
                    button: false,
                    table: false
                },
                dialog: false,
                items: [],
                headers: [ 
                    { text: 'Марка', value: 'brand_name' },
                    { text: 'Действия', value: 'actions' },
                ],
                search: '',
                selectItems: []
            },

            model: {
                model_name: '',
                brand_id: null,
                loading: {
                    button: false,
                    table: false
                },
                dialog: false,
                items: [],
                headers: [ 
                    { text: 'Модель', value: 'model_name' },
                    { text: 'Марка', value: 'brand_name' },
                    { text: 'Действия', value: 'actions' },
                ],
                search: ''
            },

            snackbar: {
                active: false,
                text: '',
                timeout: 5000,
                color: ''
            },
        }
    },
    methods: {
        fetchCarBodyInfo() {
            this.shape.loading.table = true;
            this.brand.loading.table = true;
            this.model.loading.table = true;
            axios.get('/company/cars/body/info')
                .then(response => {
                    this.shape.items = response.data.shapes;
                    this.shape.loading.table = false;

                    this.brand.items = response.data.brands;
                    this.brand.loading.table = false;

                    this.brand.items.map(value => {
                        this.brand.selectItems.push({
                            text: value.brand_name,
                            value: value.id
                        });
                    }); 

                    this.model.items = response.data.models;
                    this.model.loading.table = false;
                })
                .catch(error => console.log(error));
        },

        createCarShape() {
            this.shape.loading.button = true;
            this.$validator.validateAll('create-shape-form')
                .then(success => {
                    if(success) {
                        axios.post('/company/cars/shapes', { 'shape_name': this.shape.shape_name })
                            .then(response => {
                                this.shape.loading.button = false;
                                this.shape.items.push(response.data.shape);
                                this.shape.shape_name = '';

                                this.snackbar.color = 'success';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
                            })
                            .catch(error => {
                                console.log(error);
                                this.shape.loading.button = false;
                            });
                    }
                })
        },
        
        deleteCarShape(shape_id) {
            axios.delete('/company/cars/shapes/' + shape_id)
                .then(response => {
                    this.fetchCarBodyInfo();

                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })  
                .catch(error => console.log(error));
        },

        createCarBrand() {
            this.brand.loading.button = true;
            this.$validator.validateAll('create-brand-form')
                .then(success => {
                    if(success) {
                        axios.post('/company/cars/brands', { 'brand_name': this.brand.brand_name })
                            .then(response => {
                                this.brand.loading.button = false;
                                this.brand.items.push(response.data.brand);
                                this.brand.brand_name = '';

                                this.snackbar.color = 'success';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
                            })
                            .catch(error => {
                                console.log(error);
                                this.brand.loading.button = false;
                            });
                    }
                })
        },

        deleteCarBrand(brand_id) {
            axios.delete('/company/cars/brands/' + brand_id)
                .then(response => {
                    this.fetchCarBodyInfo();

                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })  
                .catch(error => console.log(error));
        },

        createCarModel() {
            this.model.loading.button = true;
            this.$validator.validateAll('create-model-form')
                .then(success => {
                    if(success) {
                        axios.post('/company/cars/models', {
                            'model_name': this.model.model_name,
                            'brand_id': this.model.brand_id
                        })
                        .then(response => {
                            this.model.loading.button = false;
                            this.model.model_name = '';

                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                        })
                        .catch(error => {
                            console.log(error);
                            this.model.loading.button = false;
                        });
                    }                    
                });
        },

        deleteCarModel(model_id) {
            axios.delete('/company/cars/models/' + model_id)
                .then(response => {
                    this.fetchCarBodyInfo();

                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })  
                .catch(error => console.log(error));
        }
    },
    created() {
        this.fetchCarBodyInfo();
    }
}
</script>

<style>

</style>
