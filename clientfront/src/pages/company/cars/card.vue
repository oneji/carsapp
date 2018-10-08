<template>
    <div>
        <v-layout row wrap v-if="!loading.pageLoad"> 
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>
                <v-btn color="primary" append @click.native="driver.dialog = true" v-if="car.drivers.length === 0">Привязать водителя</v-btn>
                <v-btn color="primary" append @click.native="unbindDriver" :loading="deleteDriver.loading" v-else>Отвязать водителя</v-btn>
            </v-flex>
        </v-layout> 

        <v-layout row wrap style="position: relative">
            <Loading :loading="loading.pageLoad" />
            
            <!-- Car info and defect act list -->
            <transition name="slide-x-transition" mode="out-in">
                <v-flex xs12 sm12 md4 lg3 v-if="!loading.pageLoad" v-cloak>
                    <Car 
                        :item="car" 
                        :expanded="true"
                        :actions="false" />
                </v-flex>
            </transition>
            
            <!-- Consumables, attachments and fines -->
            <transition name="slide-x-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg5 v-if="!loading.pageLoad" v-cloak>
                    <ConsumablesList 
                        :items="consumables" 
                        :private-items="privateConsumables" 
                        @add="onConsumableAdded"
                        @change="onConsumableChanged" />

                    <Attachments :files="lightboxImages" />  

                    <Fines :items="fines" />

                    <DefectActList 
                        :items="defectActs"
                        class="mt-3" />
                </v-flex>            
            </transition>

            <!-- Comments -->
            <transition name="slide-x-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg4 v-if="!loading.pageLoad">
                    <Comments :items="comments" @add="onCommentAdded" />

                    <RTActsList :items="car.card.rt_acts" />
                </v-flex>
            </transition>
        </v-layout>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>

        <defect-act />

        <!-- Bind driver -->
        <v-dialog v-model="driver.dialog" max-width="500">
            <form @submit.prevent="bindDriver" data-vv-scope="bind-driver-form">
                <v-card>
                    <v-card-title class="headline">Привязать водителя</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>   
                                <v-select 
                                    autocomplete 
                                    :items="driver.selectDriverItems" 
                                    v-model="driver.driver_id" 
                                    label="Выберите водителя" 
                                    prepend-icon="person"
                                    name="driver_id" required
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('driver_id')"
                                    data-vv-name="driver_id" data-vv-as='"Водитель"'
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
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import snackbar from '@/components/mixins/snackbar'
import assetsURL from '@/components/mixins/assets-url'

import Loading from '@/components/Loading'
import FileUpload from '@/components/FileUpload'
import CreateDefectAct from '@/components/CarCard/Defect/CreateDefectAct'
import DefectAct from '@/components/CarCard/Defect/DefectAct'
import DefectActList from '@/components/CarCard/Defect/DefectActList'
import Car from '@/components/Car'
import Comments from '@/components/CarCard/Comments/CarCardComments'
import Attachments from '@/components/CarCard/Attachments/CarCardAttachments'
import Fines from '@/components/CarCard/Fines/CarCardFine'
import ConsumablesList from '@/components/CarCard/Consumables/ConsumablesList'
import RTActsList from '@/components/CarCard/ReceiveTransferAct/ReceiveTransferActList'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar, assetsURL ],
    components: {
        FileUpload, 
        CreateDefectAct, 
        DefectAct, 
        Loading, 
        DefectActList, 
        Car, 
        Comments, 
        Attachments, 
        Fines,
        ConsumablesList,
        RTActsList
    },
    data() {
        return {
            selectedServices: [],
            invoice: [],
            totalSum: 0,
            car: {},
            loading: {
                pageLoad: false,
                saveDefects: false,
                invoice: false,
                comments: false
            },
            active: null,
            active_2: null,
            tabs: [
                { text: 'Дефектный акт' },
                { text: 'Ремонтные работы' },
            ],
            defects: [],
            comments: [],
            defectActs: [],
            attachments: [],
            fines: [],
            equipment: [],
            consumables: [],
            privateConsumables: [],
            selects: {
                defects: [],
                defectOptions: []
            },
            selected: {
                defects: null,
                defectOptions: []
            },

            lightboxImages: [],
            newAttachments: {
                dialog: false,
                loading: false,
                items: [],
                removeAll: false
            },
            createDefectActModal: false,
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
        }
    },
    methods: {
        bindDriver() {
            this.$validator.validateAll('bind-driver-form')
                .then(success => {
                    if(success) {
                        this.driver.loading = true;
                        axios.post(`/company/${this.$route.params.slug}/cars/drivers`, {
                            'driver_id': this.driver.driver_id,
                            'car_id': this.$store.getters.car.id
                        })
                        .then(response => {
                            if(response.data.success) {
                                let driver = {
                                    ...response.data.driver,
                                    pivot: {
                                        active: 1
                                    }
                                }

                                this.car.drivers.push(driver);                                
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
            this.deleteDriver.loading = true;
            axios.put(`/company/${this.$route.params.slug}/cars/drivers`, {
                car_id: this.$store.getters.car.id
            })
            .then(response => {
                this.car.drivers = [];
                this.fetchDrivers();
                this.deleteDriver.dialog = false;
                this.deleteDriver.loading = false;
                this.successSnackbar(response.data.message);
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

        fetchCarCardInfo() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/cars/${this.$route.params.car}/card`)
                .then(response => {
                    console.log(response.data);
                    this.car = response.data.car;
                    // Vuex actions
                    this.$store.dispatch('setCar', response.data.car);
                    this.$store.dispatch('setDefectTypes', response.data.defects_info);
                    this.$store.dispatch('setEquipment', response.data.equipment);
                    
                    this.comments = this.car.card.comments;
                    this.attachments = this.car.attachments;
                    this.fines = this.car.card.fines;
                    this.equipment = response.data.equipment;
                    this.defectActs =  this.car.card.defect_acts;

                    this.attachments.map(file => {
                        this.lightboxImages.push({
                            src: this.assetsURL + '/' + file.attachment,
                            title: file.attachment_name
                        });
                    });
                    
                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },  

        fetchConsumables() {
            axios.get(`/car/${this.$route.params.car}/consumables`)
                .then(response => {
                    this.consumables = response.data.consumables;
                    this.privateConsumables = response.data.data.consumables;
                })
                .catch(error => console.log(error));
        },

        onDefectActCreated(act) {
            this.defectActs.push(act);
        },

        onCommentAdded(response) {
            this.comments.push(response.comment);
            this.successSnackbar(response.message);       
        },
        
        onConsumableAdded(response) {
            this.consumables.push(response.consumable);
            this.successSnackbar(response.message);
        },

        onConsumableChanged(response) {
            this.successSnackbar(response.message);
            this.fetchConsumables();
        }
    },
    created() {
        this.fetchCarCardInfo();
        this.fetchConsumables();
        this.fetchDrivers();
    }
}
</script>

<style>
    
</style>
