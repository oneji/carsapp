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
                <v-btn color="success" @click="addToReserve.dialog = true">Добавить в резерв</v-btn> 
                <v-btn outline color="primary">Количество машин: {{ getTotalCarCount }}</v-btn> 
            </v-flex>   
        </v-layout>  
        <v-divider class="mb-3"></v-divider>    

        <v-layout style="position: relative;">
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="getTotalCarCount === 0 && !loading">
                    Список резервных машин пуст.
                </v-alert>
            </v-flex>

            <loading :loading="loading" />
        </v-layout>       

        <v-layout row wrap>
            <v-flex v-for="(car, index) in getCarsByCompany" :key="car.info.id" xs12 sm6 md3 lg3 v-cloak>
                <v-card>
                    <v-card-media :src="car.info.cover_image === null ? '/static/images/no-photo.png' : assetsURL + '/' + car.info.cover_image" height="150px">
                        <v-container fill-height fluid>
                            <v-layout fill-height>
                                <v-flex class="text-xs-right text-sm-right text-md-right text-lg-right" xs12 align-end flexbox justify-end>
                                    <my-label :text="car.info.type === 0 ? 'Служебная' : 'Служебно-Личная'" :type="car.info.type === 0 ? 'success' : 'primary'" />
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media> 
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
                            <div v-if="car.info.type === 1"><my-label text="Служебно-Личная" color="#3498db" /></div>
                        </div>
                    </v-card-title>
                    <v-card-actions class="mt-2">
                        <v-btn flat block color="success" @click="showUnReserveModal(car.info.id, index)">Вернуть</v-btn>
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

        <!-- Back from reservation -->
        <v-dialog v-model="backFromReserve.dialog" max-width="500">
            <form @submit.prevent="unReserve" data-vv-scope="back-from-reserve-form">
                <v-card>
                    <v-card-title class="headline">Вернуть автомобиль из резерва</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12> 
                                <v-select autocomplete :items="selectItems.companies" v-model="backFromReserve.companyID" label="Выберите компанию" prepend-icon="category"
                                    name="company_id" required
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('company_id')"
                                    data-vv-name="company_id" data-vv-as='"Компания"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="backFromReserve.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="backFromReserve.loading" flat="flat" type="submit">Вернуть из резерва</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <!-- Add to reserve reservation -->
        <v-dialog v-model="addToReserve.dialog" max-width="500">
            <form @submit.prevent="reserve" data-vv-scope="reserve-form">
                <v-card>
                    <v-card-title class="headline">Добавить автомобиль в резерв</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12> 
                                <v-select autocomplete :items="selectItems.cars" v-model="addToReserve.carID" label="Выберите автомобиль" prepend-icon="category"
                                    name="car_id" required
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('car_id')"
                                    data-vv-name="car_id" data-vv-as='"Автомобиль"'
                                ></v-select>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="addToReserve.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="addToReserve.loading" flat="flat" type="submit">В резерв</v-btn>
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
    mixins: [snackbar],
    computed: {
        assetsURL() {
            return config.assetsURL;
        },

        getCarsByCompany() {
            if(this.query.company === null)
                return this.cars.filter(car => car.info.reserved === 1);
            else
                return this.cars.filter(car => car.company.id === this.query.company && car.info.reserved === 1); 
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
                companies: [],
                cars: []
            },
            backFromReserve: {
                dialog: false,
                loading: false,
                companyID: null,
                carID: null,
                index: null
            },
            addToReserve: {
                dialog: false,
                loading: false,
                carID: null,
                index: null,
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

                    this.cars.map(car => {
                        if(car.info.reserved == 0) {
                            this.selectItems.cars.push({
                                text: car.info.brand_name + ' ' + car.info.model_name + ' (' + car.info.number + ')',
                                value: car.info.id
                            })
                        }
                    });

                    this.loading = false;
                })
                .catch(error => console.error());
        },

        clearFilter() {
            this.query.company = null;
        },

        showUnReserveModal(car_id, index) {
            this.backFromReserve.carID = car_id;
            this.backFromReserve.index = index;
            this.backFromReserve.dialog = true;
        },

        unReserve() {            
            this.$validator.validateAll('back-from-reserve-form')
                .then(success => {
                    if(success) {
                        this.backFromReserve.loading = true;
                        axios.put(`/company/${this.$route.params.slug}/cars/reserve/get`, {
                            car_id: this.backFromReserve.carID,
                            company_id: this.backFromReserve.companyID
                        })
                        .then(response => {
                            this.cars.map(car => {
                                if(car.info.id === this.backFromReserve.carID) {
                                    car.info.reserved = 0;
                                }
                            });
                            this.backFromReserve.dialog = false;
                            this.backFromReserve.loading = false;

                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                            this.card.loading = false;
                        })
                        .catch(error => console.log(error));
                    }
                });
            
        },

        reserve() {
            this.$validator.validateAll('reserve-form')
                .then(success => {
                    if(success) {
                        axios.put(`/company/${this.$route.params.slug}/cars/reserve/put`, {
                            car_id: this.addToReserve.carID
                        })
                        .then(response => {
                            this.cars.map(car => {
                                if(car.info.id === this.addToReserve.carID) {
                                    car.info.reserved = 1;
                                }
                            });

                            this.selectItems.cars = this.selectItems.cars.filter(car => car.value !== this.addToReserve.carID);

                            this.addToReserve.dialog = true;
                            this.snackbar.color = 'success';
                            this.snackbar.text = response.data.message;
                            this.snackbar.active = true;
                            this.card.loading = false;
                        })
                        .catch(error => console.log(error));
                    }
                });
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
