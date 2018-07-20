<template>
    <div>
        <v-layout row wrap>  
            <v-flex xs6 sm6 md3 lg3>     
                <v-select
                    :items="selectItems.companies"
                    label="Фильтр по компаниям"
                    overflow
                    item-value="value"
                    v-model="query.company"
                ></v-select>
            </v-flex>      
            <v-flex xs6 sm6 md5 lg2>
                <v-btn block color="primary" @click="clearFilter">Очистить фильтр</v-btn>
            </v-flex> 
            <v-flex>
                <v-btn outline color="primary">Количество машин: {{ getTotalCarCount }}</v-btn> 
            </v-flex>   
        </v-layout> 
        <v-divider class="mb-3"></v-divider>

        <v-layout style="position: relative;">
            <Loading :loading="loading" />
        </v-layout>      

        <!-- <transition-group tag="v-layout" class="row wrap" name="slide-x-transition"> -->
            <v-layout row wrap>           
                <v-flex v-for="car in getCarsByCompany" :key="car.info.id" xs12 sm6 md3 lg3 v-cloak>
                    <Car :item="car.info" :can-reserve="true" :details="true" @reserve="onReserveCar" />
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
        getCarsByCompany() {
            if(this.query.company === null)
                return this.cars.filter(car => car.info.reserved === 0);
            else
                return this.cars.filter(car => car.company.id === this.query.company && car.info.reserved === 0); 
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
                companies: []
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

        clearFilter() {
            this.query.company = null;
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
    }
}
</script>

<style>
    
</style>
