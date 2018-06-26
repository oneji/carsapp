<template>
    <div> 
        <v-layout style="position: relative;">
            <loading :loading="loading" />
        </v-layout>       

        <v-layout row wrap>  
            <v-flex xs12 sm12 md12 lg12>      
                <v-card>
                    <v-card-text class="py-0">                        
                        <v-container>
                            <v-layout justify-center>
                                <v-flex fill-height>
                                    <v-select
                                        :items="selectItems.companies"
                                        v-model="query.company"
                                        label="Фильтр по компаниям"
                                        autocomplete
                                    ></v-select>                                                                         
                                </v-flex>
                                <v-flex fill-height>
                                    <v-btn color="primary" @click="clearFilter">Очистить фильтр</v-btn>   
                                </v-flex>
                            </v-layout>
                        </v-container>          
                    </v-card-text>
                </v-card> 
            </v-flex>          
        </v-layout>  

        <v-layout>
            <v-flex>
                <p class="headline">Количество машин: {{ getTotalCarCount }}</p>  
            </v-flex>
        </v-layout>      

        <!-- <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">    -->
        <v-layout row wrap>
            <v-flex v-for="car in getCarsByCompany" :key="car.info.id" xs12 sm6 md3 lg3 v-cloak>
                <v-card>
                    <v-card-media :src="car.info.cover_image === null ? '/static/images/no-photo.png' : assetsURL + '/' + car.info.cover_image" height="150px"></v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title class="pt-3 pb-0">
                        <div>
                            <h3 class="headline mb-0">{{ car.info.brand_name }} {{ car.info.model_name }} <span class="red--text">{{ car.info.year }}</span></h3>
                            <div v-if="car.info.drivers.length > 0"> 
                                <div v-for="driver in car.info.drivers" :key="driver.id"><strong>Водитель:</strong> {{ driver.fullname }}</div>
                            </div>
                            <div v-else>Водителя нет</div>
                            <div>Компания: {{ car.company.company_name }}</div>
                        </div>
                    </v-card-title>
                    <v-card-actions class="mt-2">
                        <v-btn flat block color="primary">Карточка</v-btn>
                        <v-btn icon @click.native="card.showInfo = car.info.id" v-if="card.showInfo !== car.info.id">
                            <v-icon>keyboard_arrow_down</v-icon>
                        </v-btn>
                        <v-btn icon @click.native="card.showInfo = null" v-if="card.showInfo === car.info.id">
                            <v-icon>keyboard_arrow_up</v-icon>
                        </v-btn>
                    </v-card-actions>
                    <v-slide-y-transition>
                        <!-- Car info -->
                        <v-card-text v-show="card.showInfo === car.info.id" class="pt-1">
                            <v-flex>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-speedometer car-icon"></i>
                                    <strong>Пробег:</strong> 
                                    <span v-if="car.info.milage !== null">{{ car.info.milage }} км.</span>
                                    <span v-else>Не установлен.</span>
                                </div>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-car car-icon"></i>
                                    <strong>Vin код:</strong> {{ car.info.vin_code }}
                                </div>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-wheel car-icon"></i>
                                    <strong>Гос-номер:</strong> {{ car.info.number }}
                                </div>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-engine car-icon"></i>
                                    <strong>Объем двигателя:</strong> 
                                    <span v-if="car.info.engine_capacity !== null">{{ car.info.engine_capacity }} л.</span>
                                    <span v-else>Не установлен.</span>
                                </div>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-fuel car-icon"></i>
                                    <strong>Тип ДВС:</strong> {{ car.info.engine_type_name }}
                                </div>
                                <div class="car-details-block subheading">
                                    <i class="ic-transmission car-icon"></i>
                                    <strong>Трансмиссия:</strong> {{ car.info.transmission_name }}
                                </div>
                            </v-flex>
                        </v-card-text>
                    </v-slide-y-transition>
                </v-card>
            </v-flex>         
        </v-layout>  
        <!-- </transition-group> -->
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import Loading from '@/components/Loading'

export default {
    computed: {
        assetsURL() {
            return config.assetsURL;
        },

        getCarsByCompany() {
            if(this.query.company === null)
                return this.cars;
            else
                return this.cars.filter(car => car.company.id === this.query.company); 
        },

        getTotalCarCount() {
            return this.getCarsByCompany.length;
        } 
    },
    components: {
        Loading
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
                        });
                          
                        this.selectItems.companies.push({
                            text: company.company_name,
                            value: company.id
                        }) 
                    })
                    
                    console.log(this.cars);

                    this.loading = false;
                })
                .catch(error => console.error());
        },

        clearFilter() {
            this.query.company = null;
        }
    },
    created() {
        this.fetchUserProjects();
    }
}
</script>

<style>
    .car-icon {
        font-size: 150%;
        margin-right: 8px;
    }
    .car-details-block {
        display: flex;
        justify-content: start;
        align-items: center;
    }
    .car-details-block strong {
        margin-right: 5px;
    }
</style>
