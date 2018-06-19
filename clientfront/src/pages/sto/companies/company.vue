<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>  
                <v-btn color="info" append @click="$router.forward()">Вперед</v-btn>               
            </v-flex>
        </v-layout>

        <v-layout style="position: relative;">
            <loading :loading="loading" />

            <v-flex v-if="cars.length === 0 && !loading">
                <alert message="Автомобилей в компании не зарегистрировано." type="info" />
            </v-flex>
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="(car, index) in cars" :key="car.id" v-cloak>
                <v-card>
                    <v-card-media :src="car.cover_image === null ? '/static/images/no-car-img.png' : assetsURL + '/' + car.cover_image" height="150px"></v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title class="pt-3 pb-0">
                        <div>
                            <h3 class="headline mb-0">{{ car.brand_name }} {{ car.model_name }} <span class="red--text">{{ car.year }}</span></h3>
                            <div v-if="car.drivers.length > 0"> 
                                <div v-for="driver in car.drivers" :key="driver.id"><strong>Водитель:</strong> {{ driver.fullname }}</div>
                            </div>
                            <div v-else>Водителя нет</div>
                        </div>
                    </v-card-title>
                    <v-card-actions class="mt-2">
                        <v-btn flat block color="success" v-if="car.card === null" @click="createCard(car.id, index)" :loading="card.loading === car.id">Создать карточку</v-btn>
                        <v-btn flat block color="primary" v-else :to="{ name: 'StoCarCard', params: { car: car.id,  } }">Карточка</v-btn>
                        <v-btn icon @click.native="card.showInfo = car.id" v-if="card.showInfo !== car.id">
                            <v-icon>keyboard_arrow_down</v-icon>
                        </v-btn>
                        <v-btn icon @click.native="card.showInfo = null" v-if="card.showInfo === car.id">
                            <v-icon>keyboard_arrow_up</v-icon>
                        </v-btn>
                    </v-card-actions>
                    <v-slide-y-transition>
                        <!-- Car info -->
                        <v-card-text v-show="card.showInfo === car.id" class="pt-1">
                            <v-flex>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-speedometer car-icon"></i>
                                    <strong>Пробег:</strong> {{ car.milage }} км.
                                </div>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-car car-icon"></i>
                                    <strong>Vin код:</strong> {{ car.vin_code }}
                                </div>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-wheel car-icon"></i>
                                    <strong>Гос номер:</strong> {{ car.number }}
                                </div>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-engine car-icon"></i>
                                    <strong>Объем двигателя:</strong> {{ car.engine_capacity }} л.
                                </div>
                                <div class="car-details-block subheading mb-2">
                                    <i class="ic-fuel car-icon"></i>
                                    <strong>Тип ДВС:</strong> {{ car.engine_type_name }}
                                </div>
                                <div class="car-details-block subheading">
                                    <i class="ic-transmission car-icon"></i>
                                    <strong>Трансмиссия:</strong> {{ car.transmission_name }}
                                </div>
                            </v-flex>
                        </v-card-text>
                    </v-slide-y-transition>
                </v-card>
            </v-flex>
        </transition-group>
        
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
import Alert from '@/components/Alert'

export default {
    mixins: [ snackbar ],
    components: {
        Loading, Alert
    },
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    data() {
        return {
            cars: [],
            card: {
                loading: null,
                showInfo: null
            },
            loading: false
        }
    },
    methods: {
        fetchCompanyCars() {
            this.loading = true;
            axios.get(`/sto/${this.$route.params.slug}/companies/${this.$route.params.company}/cars`)
                .then(response => {
                    this.cars = response.data.cars;
                    this.loading = false;
                })
                .catch(error => console.log(error));
        },

        createCard(car_id, index) {
            this.card.loading = car_id;
            axios.post(`/sto/${this.$route.params.slug}/cars/${car_id}/card`)
                .then(response => {
                    this.fetchCompanyCars();
                    this.card.loading = false;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.fetchCompanyCars();
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
