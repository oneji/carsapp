<template>
    <div>        
        <v-layout style="position: relative;">
            <v-flex v-if="cars.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Проданных машин не найдено.
                </v-alert>
            </v-flex>

            <loading :loading="loading.pageLoad" />
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="(car, index) in cars" :key="car.id" v-cloak>
                <v-card>
                    <v-card-media class="white--text" :src="car.cover_image === null ? '/static/images/no-car-img.png' : assetsURL + '/' + car.cover_image" height="150px">
                        <v-container fill-height fluid>
                            <v-layout fill-height>
                                <v-flex class="text-xs-right text-sm-right text-md-right text-lg-right" xs12 align-end flexbox justify-end>
                                    <my-label :text="car.type === 0 ? 'Служебная' : 'Служебно-Личная'" :type="car.type === 0 ? 'success' : 'primary'" />
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title class="pt-3 pb-0">
                        <div>
                            <h3 class="headline mb-0">{{ car.brand_name }} {{ car.model_name }} <span class="subheading">{{ car.number }}</span> </h3>
                            <div v-if="car.drivers.length > 0"> 
                                <div v-for="driver in car.drivers" :key="driver.id">                                    
                                    <span v-if="driver.pivot.active == 1"><strong>Водитель:</strong> {{ driver.fullname }}</span>
                                </div>
                            </div>
                            <div v-else><strong>Водитель:</strong> Водителя нет</div>                            
                        </div>
                    </v-card-title>
                    <v-card-actions class="mt-2">
                        <v-btn block flat color="primary" @click="changeSoldStatus(car.id, index, 0)" :loading="loading.sale === car.id">Вернуть</v-btn>
                    </v-card-actions>
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
import MyLabel from '@/components/Label'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    computed: {
        assetsURL() {
            return config.assetsURL;
        },
    },
    components: {
        Loading, MyLabel
    },
    data() {
        return {
            cars: [],
            loading: {
                pageLoad: false,
                sale: null
            }
        }
    },
    methods: {
        fetchCars() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/cars/sold`)
                .then(response => {  
                    console.log(response); 
                    this.cars = response.data.cars;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },

        changeSoldStatus(car_id, index, status) {
            this.loading.sale = car_id;
            axios.put(`/company/${this.$route.params.company}/cars/${car_id}/sold/${status}`)
                .then(response => {
                    this.cars.splice(index, 1);
                    this.loading.sale = null;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.fetchCars();
    }
}
</script>

<style>

</style>
