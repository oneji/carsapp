<template>
    <div>
        <v-card>
            <v-card-media :src="item.cover_image === null ? '/static/images/no-car-img.png' : assetsURL + '/' + item.cover_image" height="180px">
                <v-container fill-height fluid>
                    <v-layout fill-height>
                        <v-flex class="text-xs-right text-sm-right text-md-right text-lg-right" xs12 align-end flexbox justify-end>
                            <MyLabel :text="item.type === 0 ? 'Служебная' : 'Служебно-Личная'" :type="item.type === 0 ? 'success' : 'primary'" />
                            <!-- <skeleton-loading class="skeleton-loading">
                                <row class="skeleton-row">
                                    <square-skeleton 
                                        :boxProperties="{ bottom: '10px', height: '100%'}"    
                                    >
                                    </square-skeleton>
                                </row>
                            </skeleton-loading> -->
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-media> 
            <v-divider></v-divider>           
            <v-card-title primary-title class="pt-3 pb-0">
                <div>
                    <h3 class="title mb-0">{{ item.brand_name }} {{ item.model_name }} <span class="subheading red--text">{{ item.year   }}</span></h3>
                    <div v-if="item.drivers.length > 0"> 
                        <div v-for="driver in item.drivers" :key="driver.id">                                    
                            <span v-if="driver.pivot.active == 1"><strong>Водитель:</strong> {{ driver.fullname }}</span>
                        </div>
                    </div>
                    <div v-else><strong>Водитель:</strong> Водителя нет</div>
                </div>
            </v-card-title>
            <v-card-actions class="mt-2">
                <!-- Card link -->
                <v-btn block flat color="primary" v-if="item.card !== null && card && $route.name !== 'HomeCars' && $route.name !== 'HomeReservedCars'" :to="{ name: routeName, params: { car: item.id } }">Карточка</v-btn>
                <v-btn block flat color="primary" v-else-if="item.card !== null && $route.name === 'HomeCars'" :to="`/c/${item.companies[0].slug}/cars/${item.id}/card`">Карточка</v-btn>
                <v-btn block flat color="primary" v-else-if="item.card !== null && $route.name === 'HomeReservedCars'" :to="`/c/${item.companies[0].slug}/cars/${item.id}/card`">Карточка</v-btn>
                <!-- Create card -->
                <v-btn block flat color="success" v-else-if="item.card === null && card" @click="createCard(item.id)" :loading="loading.card === item.id">Создать карточку</v-btn>
                <!-- Back from sold -->
                <v-btn block flat color="primary" v-if="item.sold === 1 && forSale" @click="changeSoldStatus(item.id, 0)" :loading="loading.sale === item.id">Вернуть</v-btn>
                <v-tooltip bottom v-if="canReserve && item.reserved === 1">
                    <v-btn slot="activator" icon @click="showUnReserveModal(item.id)">
                        <v-icon>settings_backup_restore</v-icon>
                    </v-btn>
                    <span>Вернуть из резерва</span>
                </v-tooltip>
                <!-- Actions menu -->
                <v-tooltip bottom v-if="actions">   
                    <v-menu bottom left slot="activator">
                        <v-btn slot="activator" icon>
                            <v-icon>more_vert</v-icon>
                        </v-btn>
                        <v-list>
                            <!-- Reserve car -->
                            <v-list-tile @click="reserveCar(item.id)" v-if="canReserve && item.reserved === 0">
                                <v-list-tile-title>В резерв</v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile v-if="$route.name === 'StoCompany' && edit" :to="{ name: 'StoCarsEdit', params: { car: item.id } }">
                                <v-list-tile-title>Изменить</v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile v-if="$route.name !== 'StoCompany' && edit" :to="{ name: 'CompanyCarsEdit', params: { car: item.id } }">
                                <v-list-tile-title>Изменить</v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile 
                                v-if="item.sold === 0 && forSale" 
                                :disabled="item.drivers.length > 0 ? true : false"
                                @click="changeSoldStatus(item.id, 1)"
                            >
                                <v-list-tile-title>В список проданных</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                    <span>Действия</span>
                </v-tooltip>
                <!-- Car details -->
                <v-btn icon @click.native="showCarInfo = item.id" v-if="showCarInfo !== item.id && details">
                    <v-icon>keyboard_arrow_down</v-icon>
                </v-btn>
                <v-btn icon @click.native="showCarInfo = null" v-if="showCarInfo === item.id && details">
                    <v-icon>keyboard_arrow_up</v-icon>
                </v-btn>
            </v-card-actions>
            <v-slide-y-transition>
                <!-- Car info -->
                <v-card-text v-show="showCarInfo === item.id || expanded" class="pt-1 px-0">
                    <v-flex>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-car car-icon"></i>
                            <strong>Номер:</strong> {{ item.number }}
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-car car-icon"></i>
                            <strong>Цвет:</strong>
                            <span v-if="item.color !== null">{{ item.color }}</span>
                            <span v-else>Не установлен.</span>
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-car car-icon"></i>
                            <strong>Цена:</strong> 
                            <span v-if="item.price !== null">{{ item.price }} сом.</span>
                            <span v-else>Не установлена.</span>
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-speedometer car-icon"></i>
                            <strong>Пробег:</strong> 
                            <span v-if="item.milage !== null">{{ item.milage }} км.</span>
                            <span v-else>Не установлен.</span>
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-car car-icon"></i>
                            <strong>Vin код:</strong> {{ item.vin_code }}
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-wheel car-icon"></i>
                            <strong>Гос-номер:</strong> {{ item.number }}
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-engine car-icon"></i>
                            <strong>Объем двигателя:</strong> 
                            <span v-if="item.engine_capacity !== null">{{ item.engine_capacity }} л.</span>
                            <span v-else>Не установлен.</span>
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-fuel car-icon"></i>
                            <strong>Тип ДВС:</strong> {{ item.engine_type_name }}
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-transmission car-icon"></i>
                            <strong>Трансмиссия:</strong> {{ item.transmission_name }}
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-car car-icon"></i>
                            <strong>GPS:</strong> {{ item.has_gps === 1 ? 'Да' : 'Нет' }}
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-speedometer car-icon"></i>
                            <strong>Тех осмотр:</strong> 
                            <span v-if="item.teh_osmotr_end_date !== null">{{ item.teh_osmotr_end_date | moment('MMMM D, YYYY') }} </span>
                            <span v-else>Не установлен.</span>
                        </div>
                        <div class="car-details-block caption mb-2">
                            <i class="ic-speedometer car-icon"></i>
                            <strong>Тонировка до:</strong> 
                            <span v-if="item.tint_end_date !== null">{{ item.tint_end_date | moment('MMMM D, YYYY')}}</span>
                            <span v-else>Не установлена.</span>
                        </div>
                    </v-flex>
                </v-card-text>
            </v-slide-y-transition>
        </v-card>    
        <!-- Back from reservation -->
        <v-dialog v-model="backFromReserve.dialog" max-width="500">
            <form @submit.prevent="unReserve" data-vv-scope="back-from-reserve-form">
                <v-card>
                    <v-card-title class="headline">Вернуть автомобиль из резерва</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12> 
                                <v-select autocomplete :items="companies" v-model="backFromReserve.companyID" label="Выберите компанию" prepend-icon="list"
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
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import MyLabel from '@/components/Label'
import assetsURL from '@/components/mixins/assets-url'

export default {
    mixins: [assetsURL],
    props: {
        item: Object,
        actions: {
            type: Boolean,
            default: true
        },
        forSale: {
            type: Boolean,
            default: false
        },
        edit: {
            type: Boolean,
            default: false
        },
        card: {
            type: Boolean,
            default: false
        },
        canReserve: {
            type: Boolean,
            default: false
        },
        details: {
            type: Boolean,
            default: false
        },
        expanded: {
            type: Boolean,
            default: false
        },
        companies: {
            type: Array,
            default: () => { return [] },
        }
    },
    components: {
        MyLabel
    },
    computed: {
        routeName() {
            if(this.$route.name === 'StoCompany')
                return 'StoCarCard';
            if(this.$route.name === 'CompanyCars')
                return 'CompanyCarsCard';
            if(this.$route.name === 'HomeCars')
                return 'CompanyCarsCard';                           
        }
    },
    data() {
        return {
            showCarInfo: null,
            loading: {
                card: null,
                sale: null,
                reserve: null  
            },
            backFromReserve: {
                dialog: false,
                loading: false,
                companyID: null,
                carID: null,
                index: null
            },
        }
    },
    methods: {
        createCard(car_id) {
            this.loading.card = car_id;
            axios.post(`/company/${this.$route.params.slug}/cars/${car_id}/card`)
                .then(response => {
                    this.loading.card = null;
                    this.item.card = response.data.card;
                })
                .catch(error => console.log(error));
        },
        
        changeSoldStatus(car_id, status) {
            this.loading.sale = car_id;
            axios.put(`/company/${this.$route.params.company}/cars/${car_id}/sold/${status}`)
                .then(response => {
                    this.loading.sale = null;
                    this.$emit('sale', {
                        car_id: car_id, 
                        message: response.data.message
                    });
                })
                .catch(error => console.log(error));
        },

        reserveCar(car_id) {
            this.loading.reserve = car_id;
            axios.put(`/company/${this.$route.params.slug}/cars/reserve/put`, {
                car_id: car_id
            })
            .then(response => {
                this.loading.reserve = false;
                this.$emit('reserve', {
                    car_id: car_id,
                    message: response.data.message
                })
            })
            .catch(error => console.log(error));
        },
        
        showUnReserveModal(car_id) {
            this.backFromReserve.carID = car_id;
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
                            this.item.reserved = 0;
                            this.$emit('un-reserve', response);
                            this.backFromReserve.dialog = false;
                            this.backFromReserve.loading = false;

                            this.loading.card = false;
                        })
                        .catch(error => console.log(error));
                    }
                });           
        },
    },
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
    .vue-skeleton-loading, .skeleton-row {
        height: 100%;
    }
    .skeleton-loading {
        position: absolute;
        top: 0;
        right: 0;
    }
    .card__title {
        min-height: 80px !important;
    }
</style>
