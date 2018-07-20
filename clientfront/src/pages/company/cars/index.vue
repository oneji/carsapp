<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" :to="{ name: 'CompanyCarsCreate' }" append>Добавить машину</v-btn>
                <v-btn color="primary" append @click.native="driver.dialog = true">Привязать водителя</v-btn>
                <v-btn color="primary" append @click.native="deleteDriver.dialog = true">Отвязать водителя</v-btn>
            </v-flex>
        </v-layout> 

        <v-layout style="position: relative;">
            <v-flex v-if="cars.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Ни одной машины не зарегистрировано.
                </v-alert>
            </v-flex>

            <Loading :loading="loading.pageLoad" />
        </v-layout>            

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="car in cars" :key="car.id" v-cloak>
                <Car 
                    :item="car" 
                    :edit="true" 
                    :card="true" 
                    :for-sale="true" 
                    :details="true" 
                    :can-reserve="true"
                    @sale="onCarSale"
                    @card-created="onCarCardCreated"
                    @reserve="onCarReserved" />
            </v-flex>
        </transition-group>

        <!-- Bind driver -->
        <v-dialog v-model="driver.dialog" max-width="500">
            <form @submit.prevent="bindDriver" data-vv-scope="bind-driver-form">
                <v-card>
                    <v-card-title class="headline">Привязать водителя</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>   
                                <v-select autocomplete :items="driver.selectDriverItems" v-model="driver.driver_id" label="Выберите водителя" prepend-icon="category"
                                    name="driver_id" required
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('driver_id')"
                                    data-vv-name="driver_id" data-vv-as='"Водитель"'
                                ></v-select>       

                                <v-select autocomplete :items="driver.selectCarItems" v-model="driver.car_id" label="Выберите машину" prepend-icon="category"
                                    name="car_id" required
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

        <!-- Unbind driver -->
        <v-dialog v-model="deleteDriver.dialog" max-width="500">
            <form @submit.prevent="unbindDriver" data-vv-scope="unbind-driver-form">
                <v-card>
                    <v-card-title class="headline">Отвязать водителя</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12> 
                                <v-select autocomplete :items="deleteDriver.selectCarItems" v-model="deleteDriver.car_id" label="Выберите машину" prepend-icon="category"
                                    name="delete_car_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('delete_car_id')"
                                    data-vv-name="delete_car_id" data-vv-as='"Машина"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="deleteDriver.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="deleteDriver.loading" flat="flat" type="submit">Отвязать</v-btn>
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
import Loading from '@/components/Loading'
import Car from '@/components/Car'

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
        Loading, Car
    },
    data() {
        return {
            cars: [],
            showCarInfo: false,
            driver: {
                dialog: false,
                loading: false,
                driver_id: null,
                car_id: null,
                selectDriverItems: [],
                selectCarItems: [] 
            },
            deleteDriver: {
                dialog: false,
                loading: false,
                driver_id: null,
                car_id: null,
                selectDriverItems: [],
                selectCarItems: []
            },
            loading: {
                pageLoad: false,
            }
        }
    },
    methods: {
        fetchCars() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/cars`)
                .then(response => {   
                    this.cars = response.data.company.cars;                 
                    let carsSelect = response.data.company.cars;
                    let boundCars = response.data.unbindSelect.cars;
                    let notBoundCars = response.data.bindSelect.cars;

                    notBoundCars.map(car => {
                        this.driver.selectCarItems.push({
                            text: car.brand_name + ' ' + car.model_name + ' (' + car.number + ')',
                            value: car.id
                        });
                    });

                    boundCars.map(car => {
                        this.deleteDriver.selectCarItems.push({
                            text: car.brand_name + ' ' + car.model_name + ' (' + car.number + ')',
                            value: car.id
                        });
                    })

                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },

        fetchDrivers() {
            axios.get(`/company/${this.$route.params.slug}/drivers`)
                .then(response => {
                    let drivers = response.data.company.drivers;

                    drivers.map(driver => {
                        this.driver.selectDriverItems.push({
                            text: driver.fullname,
                            value: driver.id
                        });
                    });
                })
                .catch(error => console.log(error));
        },

        bindDriver() {  
            this.$validator.validateAll('bind-driver-form')
                .then(success => {
                    if(success) {
                        this.driver.loading = true;
                        axios.post(`/company/${this.$route.params.slug}/cars/drivers`, {
                            'driver_id': this.driver.driver_id,
                            'car_id': this.driver.car_id
                        })
                        .then(response => {
                            if(response.data.success) {
                                this.cars.map(car => {
                                    if(car.id === this.driver.car_id)
                                        car.drivers = response.data.car.drivers;
                                });

                                this.driver.selectDriverItems.map((driver, index) => {
                                    if(driver.value === this.driver.driver_id) {
                                        this.driver.selectDriverItems.splice(index, 1);
                                    }  
                                });

                                this.driver.selectCarItems.map((car, index) => {
                                    if(car.value === this.driver.car_id) {
                                        this.deleteDriver.selectCarItems.push({
                                            text: car.text,
                                            value: car.value
                                        });
                                        this.driver.selectCarItems.splice(index, 1);
                                    }
                                });
                                
                                this.driver.dialog = false;
                                this.driver.loading = false;
                                this.successSnackbar(response.data.message);
                            } else {
                                this.driver.loading = false;
                                this.errorSnackbar(response.data.message);
                            }
                        })
                        .catch(error => console.log(error));
                    }
                })
        },

        unbindDriver() {
            this.$validator.validateAll('unbind-driver-form')
                .then(success => {
                    if(success) {
                        this.deleteDriver.loading = true;
                        axios.put(`/company/${this.$route.params.slug}/cars/drivers`, {
                            car_id: this.deleteDriver.car_id
                        })
                        .then(response => {
                            this.cars.map((car, index) => {
                                if(car.id === this.deleteDriver.car_id) {
                                    car.drivers = [];
                                    this.cars.splice(index, 1);
                                }
                            });
                            
                            this.deleteDriver.selectCarItems.map((car, index) => {
                                if(car.value === this.deleteDriver.car_id) {
                                    this.driver.selectCarItems.push({
                                        text: car.text,
                                        value: car.value
                                    });
                                    this.deleteDriver.selectCarItems.splice(index, 1);
                                } 
                            });

                            this.fetchDrivers();                            
                            this.deleteDriver.dialog = false;
                            this.deleteDriver.loading = false;
                            this.successSnackbar(response.data.message);
                        })
                        .catch(error => console.log(error));
                    }
                }); 
        },

        onCarSale(params) {
            this.cars = this.cars.filter(car => car.id !== params.car_id);
            this.successSnackbar(params.message);
        },

        onCarCardCreated(params) {
            this.cars.map(car => {
                if(car.id === params.car.car_id)
                    car.card = params.car.card
            });

            this.successSnackbar(params.message);
        },

        onCarReserved(params) {
            this.cars.forEach((car, index) => {
                if(car.id === params.car_id)
                    this.cars.splice(index, 1);
            });

            this.successSnackbar(params.message);
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
