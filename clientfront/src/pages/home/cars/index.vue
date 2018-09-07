<template>
    <div>
        <div v-if="getTotalCarCount !== 0 && !loading">        
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
                    <v-select
                        :items="selectItems.brands"
                        label="Фильтр по марке машины"
                        overflow
                        item-value="value"
                        v-model="query.brand"
                        hide-details
                    ></v-select>
                </v-flex>      
                <v-flex xs12 sm6 md3 lg3>
                    <v-btn block color="primary" @click="clearFilter">Очистить фильтр</v-btn>
                </v-flex> 
                <v-flex xs12 sm6 md3 lg3>
                    <v-btn block outline color="primary">Количество машин: {{ getTotalCarCount }}</v-btn> 
                </v-flex>   
            </v-layout> 
            <v-divider class="mb-3"></v-divider>
        </div>

        <v-layout v-if="getTotalCarCount === 0 && !loading">
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true" >
                    Список автомобилей пуст.
                </v-alert>
            </v-flex>
        </v-layout>

        <v-layout style="position: relative;">
            <Loading :loading="loading" />
        </v-layout>

        <!-- <transition-group tag="v-layout" class="row wrap" name="slide-x-transition"> -->
            <v-layout row wrap>           
                <v-flex v-for="car in filterCars" :key="car.info.id" xs12 sm6 md4 lg3>
                    <Car 
                        :item="car.info"
                        :can-reserve="true"
                        :details="true"
                        :card="true"
                        @reserve="onReserveCar" />
                </v-flex>
            </v-layout> 
        <!-- </transition-group> -->

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>        
    </div>
</template>

<script>
import axios from '@/axios'
import snackbar from '@/components/mixins/snackbar'
import Loading from '@/components/Loading'
import Car from '@/components/Car'

export default {
    mixins: [snackbar],
    computed: {
        filterCars() {
            if(this.query.company === '' && this.query.brand === '') {
                return this.cars.filter(car => car.info.reserved === 0);
            } else if(this.query.company !== '' && this.query.brand === ''){
                return this.cars.filter(car => car.company.id === this.query.company && car.info.reserved === 0); 
            } else if(this.query.company === '' && this.query.brand !== '') {
                return this.cars.filter(car => car.info.brand_id === this.query.brand && car.info.reserved === 0); 
            } else if(this.query.company !== '' && this.query.brand !== '') {
                return this.cars.filter(car => car.company.id === this.query.company && car.info.brand_id === this.query.brand && car.info.reserved === 0); 
            }  
        },

        getTotalCarCount() {
            return this.filterCars.length;
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
                company: '',
                brand: ''
            },
            selectItems: {
                companies: [],
                brands: [],
                shapes: []
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
                        })

                        this.selectItems.companies.push({
                            text: company.company_name,
                            value: company.id
                        }) 
                    })

                    this.loading = false;
                })
                .catch(error => console.error());
        },

        fetchCarBodyInfo() {
            axios.get('/admin/cars/body/info')
                .then(response => { 
                    let brands = response.data.brands;

                    brands.map(value => {
                        this.selectItems.brands.push({
                            text: value.brand_name,
                            value: value.id
                        });
                    }); 

                })
                .catch(error => console.log(error));
        },

        clearFilter() {
            this.query.company = '';
            this.query.brand = '';
        },

        onReserveCar(params) {
            this.cars.map(car => {
                if(car.info.id === params.car_id)
                    car.info.reserved = 1;                    
            });

            this.successSnackbar(params.message);
        }
    },
    created() {
        this.fetchUserProjects();
        this.fetchCarBodyInfo();
    }
}
</script>

<style>
    
</style>
