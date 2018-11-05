<template>
    <div>
        <!-- Page loading spinner -->
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>
        <!-- Content -->
        <v-layout v-if="!loading.page">
            <v-flex xs12 sm12 md12 lg12>
                <table class="prices-table">
                    <thead>
                        <tr class="prices-table-title">
                            <th class="py-1">Общий ч/ч</th>
                            <th class="py-1">
                                <v-layout row>
                                    <v-flex xs6>
                                        <v-btn block outline small color="primary">
                                            {{  prices.filter(price => price.human_hour_price !== null).length === 0 
                                                ? 'Текущая не установлена.' 
                                                : 'Текущая: ' + prices.filter(price => price.human_hour_price !== null)[0].human_hour_price + 'с.' }}
                                        </v-btn>
                                    </v-flex>
                                    <v-flex xs6>
                                        <v-btn block small color="success" @click="setHumanHourPriceDialog = true">Установить</v-btn>
                                    </v-flex>
                                </v-layout>
                            </th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="prices-table-title">
                            <th>Деталь</th>
                            <th>Заключение</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="defect in defects" >
                            <tr :key="defect.id">
                                <td>{{ defect.defect_name }}</td>
                                <td>
                                    <v-layout row wrap v-for="conclusion in defect.defect_conclusions" :key="conclusion.id">
                                        <v-flex xs6 class="py-0 px-1">
                                            <v-btn block flat small>{{ conclusion.conclusion_name }}</v-btn>
                                        </v-flex>
                                        <v-flex xs6 class="py-0 px-1" v-if="conclusion.price.price === 0">
                                            <v-btn block small color="success" @click="showSetupPriceDialog(conclusion.price.price_id, 'set')">Установить</v-btn>
                                        </v-flex>
                                        <v-flex xs3 class="py-0 px-1" v-if="conclusion.price.price > 0">
                                            <v-btn block outline small color="primary">{{ conclusion.price.price + 'с.' }}</v-btn>
                                        </v-flex>
                                        <v-flex xs3 class="py-0 px-1" v-if="conclusion.price.price > 0">
                                            <v-btn block small color="success" @click="showSetupPriceDialog(conclusion.price.price_id, 'edit')">Изменить</v-btn>                                            
                                        </v-flex>
                                    </v-layout>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </v-flex>
        </v-layout>

        <v-dialog v-model="setupPriceDialog" max-width="500">
            <form @submit.prevent="setupPrice" data-vv-scope="setup-price-form">
                <v-card>
                    <v-card-title class="headline">
                        {{ setupType === 'set' ? 'Установить цену ремонта' : 'Изменить цену ремонта' }}
                    </v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>
                                <v-text-field type="number" 
                                    v-model="price" 
                                    name="service_price" 
                                    label="Цена ремонта" 
                                    prepend-icon="attach_money"
                                    suffix="сом."
                                    v-validate="'required'" 
                                    data-vv-name="service_price" 
                                    data-vv-as='"Цена ремонта"'
                                    :error-messages="errors.collect('service_price')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="setupPriceDialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="setupPriceLoading" flat="flat" type="submit">{{ setupType === 'set' ? 'Создать' : 'Изменить' }}</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <v-dialog v-model="setHumanHourPriceDialog" max-width="500">
            <form @submit.prevent="setHumanHourPriceForAll" data-vv-scope="setup-hh-price-form">
                <v-card>
                    <v-card-title class="headline">Установить цену за ч/ч</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>
                                <v-text-field type="number" 
                                    v-model="human_hour_price" 
                                    name="service_hh_price" 
                                    label="Цена за ч/ч" 
                                    prepend-icon="attach_money"
                                    suffix="сом."
                                    v-validate="'required'" 
                                    data-vv-name="service_hh_price" 
                                    data-vv-as='"Цена за ч/ч"'
                                    :error-messages="errors.collect('service_hh_price')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="setHumanHourPriceDialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="setHumanHourPriceLoading" flat="flat" type="submit">Установить</v-btn>
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
            defects: [],
            setupPriceDialog: false,
            setupPriceLoading: false,
            setHumanHourPriceDialog: false,
            setHumanHourPriceLoading: false,
            defectId: null,
            conclusionId: null,
            priceId: null,
            setupType: '',
            price: '',
            human_hour_price: '',
            loading: {
                page: false
            }
        }
    },
    methods: {
        getPrices() {
            this.loading.page = true;
            axios.get(`/sto/${this.$route.params.slug}/prices`)
                .then(response => {
                    console.log(response.data);
                    this.defects = response.data.defects;
                    this.prices = response.data.prices;
                    this.loading.page = false;
                })
                .catch(error => console.log(error));
        },
        showSetupPriceDialog(priceId, setupType) {
            this.priceId = priceId;
            this.setupPriceDialog = true;
            this.setupType = setupType;
            if(setupType === 'edit') {
                this.price = this.prices.filter(price => price.price_id === priceId)[0].price;
            }
        },
        setupPrice() {
            this.$validator.validateAll('setup-price-form')
                .then(success => {
                    if(success) {
                        this.setupPriceLoading = true;
                        axios.post(`/sto/${this.$route.params.slug}/prices`, {
                            'price': this.price,
                            'priceId': this.priceId
                        })
                        .then(response => {
                            console.log(response.data);
                            
                            for(let i = 0; i < this.defects.length; i++) {
                                let defect = this.defects[i];
                                for(let j = 0; j < defect.defect_conclusions.length; j++) {
                                    let conclusion = defect.defect_conclusions[j];
                                    if(conclusion.price.price_id === this.priceId) {
                                        conclusion.price.price = this.price;
                                    }
                                }
                            }

                            this.setupPriceLoading = false;
                            this.setupPriceDialog = false;
                            this.price = null;
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
        setHumanHourPriceForAll() {
            
            this.$validator.validateAll('setup-hh-price-form')
                .then(success => {
                    if(success) {
                        this.setHumanHourPriceLoading = true;
                        axios.post(`/sto/${this.$route.params.slug}/hhprice`, { 'human_hour_price': this.human_hour_price })
                            .then(response => {
                                for(let i = 0; i < this.prices.length; i++) {
                                    this.prices[i].human_hour_price = this.human_hour_price;
                                }

                                this.setHumanHourPriceLoading = false;
                                this.$store.dispatch('showSnackbar', {
                                    color: 'success',
                                    text: response.data.message,
                                    active: true
                                });
                            })
                            .catch(error => console.log(error));
                    }
                })
        }
    },
    created() {
        this.getPrices();
    }
}
</script>

<style>
    .prices-table {
        width: 100%;
    }
    .prices-table td, .prices-table th {
        padding: 10px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        margin: 0; 
        width: 10%;
    }
    .prices-table td p {
        margin: 0;
    }
    .prices-table-title {
        background-color: rgba(0, 0, 0, .05);
        text-align: center;
    }
    .prices-table-checklist-title {
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
    }
</style>
