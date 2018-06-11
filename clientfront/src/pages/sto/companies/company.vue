<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>  
                <v-btn color="info" append @click="$router.forward()">Вперед</v-btn>               
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
                        <v-btn flat block color="success" v-if="car.card === null" @click="createCard(car.id, index)" :loading="card.loading">Создать карточку</v-btn>
                        <v-btn flat block color="primary" v-else :to="{ name: 'StoCarCard', params: { car: car.id,  } }">Карточка</v-btn>
                        <v-btn icon @click.native="card.showInfo = !card.showInfo">
                            <v-icon>{{ card.showInfo ? 'keyboard_arrow_down' : 'keyboard_arrow_up' }}</v-icon>
                        </v-btn>
                    </v-card-actions>
                    <v-slide-y-transition>
                        <v-card-text v-show="card.showInfo" class="pt-1">
                            <div><strong>Пробег:</strong> {{ car.milage }} км.</div>
                            <div><strong>Год выпуска:</strong> {{ car.year }}</div>
                            <div><strong>Vin код:</strong> {{ car.vin_code }}</div>
                            <div><strong>Гос номер:</strong> {{ car.number }}</div>
                            <div><strong>Объем двигателя:</strong> {{ car.engine_capacity }}</div>
                            <div><strong>Тип ДВС:</strong> {{ car.engine_type_name }}</div>
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

export default {
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    data() {
        return {
            cars: [],
            card: {
                loading: false,
                showInfo: false
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
        fetchCompanyCars() {
            axios.get(`/sto/${this.$route.params.slug}/companies/${this.$route.params.company}/cars`)
                .then(response => {
                    this.cars = response.data.cars;
                })
                .catch(error => console.log(error));
        },

        createCard(car_id, index) {
            this.card.loading = true;
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
        console.log(this.$router);
    }
}
</script>

<style>

</style>
