<template>
    <div>
        <div>        
            <v-layout row wrap>
                <v-flex xs12 sm6 md3 lg3>      
                    <v-select
                        :items="selectItems.companies"
                        label="Фильтр по компаниям"
                        overflow
                        item-value="value"
                        v-model="query.company"
                        hide-details
                    ></v-select>
                </v-flex>      
                <v-flex xs12 sm6 md3 lg3>
                    <v-btn block color="primary" @click="clearFilter">Очистить фильтр</v-btn>
                </v-flex> 
                <v-flex xs12 sm6 md3 lg3>
                    <v-btn block color="success" @click="addToReserve.dialog = true">Добавить в резерв</v-btn>
                </v-flex>
                <v-flex xs12 sm6 md3 lg3> 
                    <v-btn block outline color="primary">Количество машин: {{ getTotalCarCount }}</v-btn> 
                </v-flex>   
            </v-layout>  
            <v-divider class="mb-3"></v-divider>  
        </div>  

        <v-layout v-if="getTotalCarCount === 0 && !loading">
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Список резервных автомобилей пуст.
                </v-alert>
            </v-flex>
        </v-layout>

        <v-layout style="position: relative;" v-if="loading">
            <Loading :loading="loading" />
        </v-layout>       

        <v-layout row wrap>
            <v-flex v-for="car in getCarsByCompany" :key="car.info.id" xs12 sm6 md4 lg3 v-cloak>
                <Car 
                    :item="car.info" 
                    :can-reserve="true" 
                    :details="true"
                    :card="true"
                    :actions="false"
                    :companies="selectItems.companies" />
            </v-flex>         
        </v-layout> 
        

        <!-- Add to reserve reservation -->
        <v-dialog v-model="addToReserve.dialog" max-width="500">
            <form @submit.prevent="reserve" data-vv-scope="reserve-form">
                <v-card>
                    <v-card-title class="headline">Добавить автомобиль в резерв</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12> 
                                <v-select autocomplete :items="selectItems.cars" v-model="addToReserve.carID" label="Выберите автомобиль" prepend-icon="directions_car"
                                    name="car_id" required
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('car_id')"
                                    data-vv-name="car_id" data-vv-as='"Автомобиль"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="addToReserve.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="addToReserve.loading" flat="flat" type="submit">В резерв</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
    </div>
</template>

<script>
import axios from '@/axios'
import Loading from '@/components/Loading'
import Car from '@/components/Car'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    computed: {
        getCarsByCompany() {
            if(this.query.company === null)
                return this.cars.filter(car => car.info.reserved === 1);
            else
                return this.cars.filter(car => car.company.id === this.query.company && car.info.reserved === 1); 
        },

        getTotalCarCount() {
            return this.getCarsByCompany.length;
        } 
    },
    components: {
        Loading, Car
    },
    data() {
        return {
            cars: [],
            companies: [],
            stos: [],
            loading: false,
            card: {
                loading: false,
                showInfo: false
            },
            query: {
                company: null
            },
            selectItems: {
                companies: [],
                cars: []
            },
            backFromReserve: {
                dialog: false,
                loading: false,
                companyID: null,
                carID: null,
                index: null
            },
            addToReserve: {
                dialog: false,
                loading: false,
                carID: null,
                index: null,
            } 
        }
    },
    methods: {
        fetchUserProjects() {
            this.loading = true;
            axios.get('/all-cars')
                .then(response => {
                    this.companies = response.data.companies;

                    this.companies.map(company => {
                        company.cars.map(car => {                            
                            let carInfo = {
                                'info': car,
                                'company': company
                            };                    
                            this.cars.push(carInfo);   
                        });

                        this.selectItems.companies.push({
                            text: company.company_name,
                            value: company.id
                        }) 
                    })

                    this.cars.map(car => {
                        if(car.info.reserved == 0) {
                            this.selectItems.cars.push({
                                text: car.info.brand_name + ' ' + car.info.model_name + ' (' + car.info.number + ')',
                                value: car.info.id
                            })
                        }
                    });

                    this.loading = false;
                })
                .catch(error => console.error());
        },

        clearFilter() {
            this.query.company = null;
        },

        reserve() {
            this.$validator.validateAll('reserve-form')
                .then(success => {
                    if(success) {
                        this.addToReserve.loading = true;
                        axios.put(`/company/${this.$route.params.slug}/cars/reserve/put`, {
                            car_id: this.addToReserve.carID
                        })
                        .then(response => {
                            this.cars.map(car => {
                                if(car.info.id === this.addToReserve.carID) {
                                    car.info.reserved = 1;
                                }
                            });

                            this.selectItems.cars = this.selectItems.cars.filter(car => car.value !== this.addToReserve.carID);

                            this.addToReserve.dialog = false;
                            this.addToReserve.loading = false;
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
    },
    created() {
        this.fetchUserProjects();
    }
}
</script>

<style>

</style>
