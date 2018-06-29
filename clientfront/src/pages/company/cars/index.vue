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
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="cars.length === 0 && !loading.pageLoad">
                    Ни одной машины не зарегистрировано.
                </v-alert>
            </v-flex>

            <loading :loading="loading.pageLoad" />
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="car in cars" :key="car.id" v-cloak>
                <v-card>
                    <v-card-media :src="car.cover_image === null ? '/static/images/no-car-img.png' : assetsURL + '/' + car.cover_image" height="150px"></v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title class="pt-3 pb-0">
                        <div>
                            <h3 class="headline mb-0">{{ car.brand_name }} {{ car.model_name }}</h3>
                            <div v-if="car.drivers.length > 0"> 
                                <div v-for="driver in car.drivers" :key="driver.id">                                    
                                    <span v-if="driver.pivot.active == 1"><strong>Водитель:</strong> {{ driver.fullname }}</span>
                                    <span v-else>dsadas</span>
                                </div>
                            </div>
                            <div v-else><strong>Водитель:</strong> Водителя нет</div>
                            <div v-if="car.type === 0"><my-label text="Служебная" color="#32c861" /></div>
                            <div v-if="car.type === 1"><my-label text="Служебно-Личная" color="#3498db" /></div>
                        </div>
                    </v-card-title>
                    <v-card-actions class="mt-2">
                        <v-btn block flat color="primary">Карточка</v-btn>
                        <v-btn icon :to="{ name: 'CompanyCarsEdit', params: { car: car.id } }">
                            <v-icon>edit</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
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
                pageLoad: false
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
                            this.cars.map(car => {
                                if(car.id === this.deleteDriver.car_id) {
                                    car.drivers = [];
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
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                        })
                        .catch(error => console.log(error));
                    }
                }); 
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
