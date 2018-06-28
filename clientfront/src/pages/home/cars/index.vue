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
            <loading :loading="loading" />
        </v-layout>      

        <!-- <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">    -->
            <v-layout row wrap>           
                <v-flex v-for="(car, index) in getCarsByCompany" :key="car.info.id" xs12 sm6 md3 lg3 v-cloak>
                    <v-card>
                        <v-card-media :src="car.info.cover_image === null ? '/static/images/no-photo.png' : assetsURL + '/' + car.info.cover_image" height="150px"></v-card-media> 
                        <v-divider></v-divider>           
                        <v-card-title primary-title class="pt-3 pb-0">
                            <div>
                                <h3 class="headline mb-0">{{ car.info.brand_name }} {{ car.info.model_name }}</h3>
                                <div v-if="car.info.drivers.length > 0"> 
                                    <div v-for="driver in car.info.drivers" :key="driver.id">                                    
                                        <span v-if="driver.pivot.active == 1"><strong>Водитель:</strong> {{ driver.fullname }}</span>
                                        <span v-else>dsadas</span>
                                    </div>
                                </div>
                                <div v-else><strong>Водитель:</strong> Водителя нет</div>
                                <div v-if="car.info.type === 0"><my-label text="Служебная" color="#32c861" /></div>
                                <div v-if="car.info.type === 1"><my-label text="Служебная-Личная" color="#f96a74" /></div>
                            </div>
                        </v-card-title>
                        <v-card-actions class="mt-2">
                            <v-btn flat block color="warning" :loading="card.loading === car.info.id" @click="reserveCar(car.info.id, index)">В резерв</v-btn>
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

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
        
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import snackbar from '@/components/mixins/snackbar'
import Loading from '@/components/Loading'
import MyLabel from '@/components/Label'

export default {
    mixins: [snackbar],
    computed: {
        assetsURL() {
            return config.assetsURL;
        },

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
        Loading, MyLabel
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
                    console.log(response);                    
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

                    this.loading = false;
                })
                .catch(error => console.error());
        },

        clearFilter() {
            this.query.company = null;
        },

        reserveCar(car_id, index) {
            this.card.loading = car_id;
            axios.put(`/company/${this.$route.params.slug}/cars/reserve/put`, {
                car_id: car_id
            })
            .then(response => {
                this.cars.map(car => {
                    if(car.info.id === car_id) {
                        car.info.reserved = 1;
                    }
                });
                this.snackbar.color = 'success';
                this.snackbar.text = response.data.message;
                this.snackbar.active = true;
                this.card.loading = false;
            })
            .catch(error => console.log(error));
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
