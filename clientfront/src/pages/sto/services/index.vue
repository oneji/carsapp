<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" @click.native="serviceCategory.dialog = true" append>Добавить категорию услуги</v-btn>
                <v-btn color="info" @click.native="serviceType.dialog = true" append>Добавить услугу</v-btn>
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

                                <v-select autocomplete :items="serviceCategory.selectItems" v-model="serviceType.categoryID" label="Выберите категорию" prepend-icon="category"
                                    name="service_type_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('service_type_id')"
                                    data-vv-name="service_type_id" data-vv-as='"Категория"'
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
            serviceCategory: {
                name: '',
                dialog: false,
                loading: {
                    button: false
                },
                selectItems: []
            },
            serviceType: {
                name: '',
                categoryID: '',
                price: null,
                approximatePrice: '',
                dialog: false,
                loading: {
                    button: false
                }
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
        addCategory() {
            this.serviceCategory.loading.button = true;
            this.$validator.validateAll('create-service-category-form')
                .then(result => {
                    if(result) {
                        axios.post(`/sto/${this.$route.params.slug}/services/categories`, { 'service_category_name': this.serviceCategory.name })
                        .then(response => {
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
            axios.get(`/sto/${this.$route.params.slug}/services/categories`)
                .then(response => {
                    console.log(response);
                    response.data.map(category => {
                        this.serviceCategory.selectItems.push({
                            text: category.service_category_name,
                            value: category.id
                        });
                    });
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
                            'approximate': this.serviceType.approximatePrice    
                        })
                        .then(response => {
                            console.log(response);
                            this.serviceType.loading.button = false;
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
        this.getCategories();
    }
}
</script>

<style>

</style>
