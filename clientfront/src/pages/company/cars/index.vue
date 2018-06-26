<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" :to="{ name: 'CompanyCarsCreate' }" append>Добавить машину</v-btn>
                <v-btn color="primary" append @click.native="driver.dialog = true">Привязать водителя</v-btn>
            </v-flex>
        </v-layout>
        
        <v-layout v-if="noCars">
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Ни одной машины не зарегистрировано.
                </v-alert>
            </v-flex>
        </v-layout>

        <transition-group v-else tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="car in cars" :key="car.id" v-cloak>
                <v-card>
                    <v-card-media :src="car.cover_image === null ? '/static/images/no-car-img.png' : assetsURL + '/' + car.cover_image" height="150px"></v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title class="pt-3 pb-0">
                        <div>
                            <h3 class="headline mb-0">{{ car.brand_name }} {{ car.model_name }}</h3>
                            <div v-if="car.drivers.length > 0"> 
                                <div v-for="driver in car.drivers" :key="driver.id"><strong>Водитель:</strong> {{ driver.fullname }}</div>
                            </div>
                            <div v-else>Водителя нет</div>
                        </div>
                    </v-card-title>
                    <v-card-actions class="mt-2">
                        <v-btn block flat color="success">Просмотреть</v-btn>
                        <v-btn block flat color="primary">Карточка</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </transition-group>

        <!-- Bind driver -->
        <v-dialog v-model="driver.dialog" max-width="500">
            <form @submit.prevent="bindDriver" data-vv-scope="create-model-form">
                <v-card>
                    <v-card-title class="headline">Привязать водителя</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>   
                                <v-select autocomplete :items="driver.selectDriverItems" v-model="driver.driver_id" label="Выберите водителя" prepend-icon="category"
                                    name="driver_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('driver_id')"
                                    data-vv-name="driver_id" data-vv-as='"Водитель"'
                                ></v-select>       

                                <v-select autocomplete :items="driver.selectCarItems" v-model="driver.car_id" label="Выберите машину" prepend-icon="category"
                                    name="car_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('car_id')"
                                    data-vv-name="car_id" data-vv-as='"Машина"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="driver.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="driver.loading" flat="flat" type="submit">Привязать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

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

export default {
    mixins: [ snackbar ],
    computed: {
        assetsURL() {
            return config.assetsURL;
        },

        noCars() {
            if(this.cars.length > 0)
                return false;
            else 
                return true;
        }
    },
    data() {
        return {
            cars: [],
            driver: {
                dialog: false,
                loading: false,
                driver_id: null,
                car_id: null,
                selectDriverItems: [],
                selectCarItems: [] 
            },
            boundCars: []
        }
    },
    methods: {
        fetchCars() {
            axios.get(`/company/${this.$route.params.slug}/cars`)
                .then(response => {                    
                    this.cars = response.data.cars;
                    this.cars.map(car => {
                        this.driver.selectCarItems.push({
                            text: car.brand_name + ' ' + car.model_name + ' (' + car.number + ')',
                            value: car.id
                        });
                    });
                })
                .catch(error => console.log(error));
        },

        fetchDrivers() {
            axios.get(`/company/${this.$route.params.slug}/drivers`)
                .then(response => {
                    console.log(response);
                    this.drivers = response.data.company.drivers;
                    this.drivers.map(driver => {
                        response.data.boundDrivers.map(boundDriver => {
                            if(boundDriver.driver_id !== driver.id) {
                                this.driver.selectDriverItems.push({
                                    text: driver.fullname,
                                    value: driver.id
                                });
                            }
                        });
                    });
                })
                .catch(error => console.log(error));
        },

        bindDriver() {
            this.driver.loading = true;
            axios.post(`/company/${this.$route.params.slug}/cars/drivers`, {
                'driver_id': this.driver.driver_id,
                'car_id': this.driver.car_id
            })
            .then(response => {
                if(response.data.success) {
                    this.cars.map(car => {
                        if(car.id === this.driver.car_id)
                            car.drivers.push(response.data.driver);
                    });
                    
                    this.driver.loading = false;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                } else {
                    this.driver.loading = false;
                    this.snackbar.color = 'error';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                }
            })
            .catch(error => console.log(error));
        }
    },
    created() {
        this.fetchCars();
        this.fetchDrivers();
    }
}
</script>

<style>

</style>
