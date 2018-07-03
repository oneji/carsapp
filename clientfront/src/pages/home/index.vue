<template>
    <div>
        <v-layout style="position: relative;">
            <loading :loading="loading" />
        </v-layout>

        <transition class="row wrap" name="slide-x-transition" mode="out-in"> 
            <v-layout row wrap :key="11" v-if="!loading">           
                <v-flex xs12 sm6 mg3 lg4 :key="0" >
                    <tile-box :link="{ name: 'HomeCompanies' }" title="Компании" :value="companies.length" box-icon="business" />
                </v-flex>
                <v-flex xs12 sm6 mg3 lg4 :key="1">
                    <tile-box :link="{ name: 'HomeCars' }" title="Автомобили" :value="carsList.length" box-icon="directions_car" />
                </v-flex>
                <v-flex xs12 sm6 mg3 lg4 :key="2">
                    <tile-box :link="{ name: 'HomeReservedCars' }" title="Резервные автомобили" :value="reservedCarsList.length" box-icon="directions_car" />
                </v-flex>
                <v-flex xs12 sm6 mg3 lg4 :key="3">
                    <tile-box :link="{ name: 'HomeDriversQueue' }" title="Водители" :value="drivers.length" box-icon="people" />
                </v-flex>
                <v-flex xs12 sm6 mg3 lg4 :key="4">
                    <tile-box :link="{ name: 'HomeDriversQueue' }" title="Водители в очереди" :value="queue.length" box-icon="people" />
                </v-flex>
            </v-layout>
        </transition> 
    </div>
</template>

<script>
import axios from '@/axios'
import TileBox from '@/components/TileBox'
import Loading from '@/components/Loading'

export default {
    components: {
        TileBox, Loading
    },
    computed: {
        carsList() {
            return this.cars.filter(car => car.info.reserved === 0);
        },

        reservedCarsList() {
            return this.cars.filter(car => car.info.reserved === 1);
        },
        
        driverQueue() {
            return this.queue;
        },

        driverList() {
            return this.drivers;
        },

        companyList() {
            return this.companies;
        }
    },
    data() {
        return {
            companies: [],
            cars: [],
            loading: false,
            queue: [],
            drivers: []
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

                        company.drivers.map(driver => {                            
                            let driverInfo = {
                                'info': driver,
                                'company': company
                            };                    
                            this.drivers.push(driverInfo);   
                        });
                    })

                    this.loading = false;
                })
                .catch(error => console.error());
        },
        getQueue() {
            axios.get(`/company/${this.$route.params.slug}/drivers/queue`)
                .then(response => {
                    this.queue = response.data;                    
                })
                .catch(error => console.log(error));
        },
    },
    created() {
        this.fetchUserProjects();
        this.getQueue();
    }
}
</script>

<style>

</style>
