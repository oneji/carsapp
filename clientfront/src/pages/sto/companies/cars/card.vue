<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>           
            </v-flex>
        </v-layout>

        <v-layout row wrap style="position: relative">
            <transition name="fade-transition" mode="out-in">
                <div class="loading-block" v-if="loading.pageLoad" v-cloak>
                    <v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
                </div>
            </transition>
            
            <!-- Car card -->
            <transition name="fade-transition" mode="out-in">
                <v-flex xs12 sm12 md4 lg3 v-if="!loading.pageLoad" v-cloak>
                    <v-card>
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Общая информация</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>
                        <v-card-media :src="car.cover_image !== null ? assetsURL + '/' + car.cover_image : '/static/images/no-photo.png'" height="200px"></v-card-media>
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
                        <v-card-actions class="pr-0 pl-0">
                            <v-container class="pt-2 pb-2">
                                <v-layout row wrap>
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
                                </v-layout>
                            </v-container>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </transition>
            
            <!-- Defect act -->
            <transition name="fade-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg5 v-if="!loading.pageLoad" v-cloak>
                    <v-card class="mb-3">
                        <v-tabs v-model="active" color="light-blue" dark slider-color="white" show-arrows>
                            <v-tab :key="1" ripple>Дефектный акт</v-tab>
                            <v-tab-item :key="1">
                                <v-card flat>
                                    <v-card-text class="px-0 py-0">
                                        <v-tabs v-model="active_2" color="light-blue" dark slider-color="white">
                                            <v-tab v-for="(type, index) in defects" :key="index" ripple>{{ type.defect_type_name }}</v-tab>
                                            <v-tab-item v-for="(type, index) in defects" :key="index">
                                                <v-card flat>
                                                    <v-card-text class="px-4 pb-0 pt-2">
                                                        <v-form>
                                                            <v-select
                                                                :items="selects.defects[index]"
                                                                v-model="selected.defects"
                                                                label="Вид дефекта"
                                                                single-line
                                                            ></v-select>

                                                            <v-select
                                                                :items="selects.defectOptions[selected.defects]"
                                                                v-model="selected.defectOptions"
                                                                label="Опция дефекта"
                                                                single-line
                                                                multiple                                                                
                                                            ></v-select>
                                                        </v-form>
                                                    </v-card-text>
                                                    <v-card-actions>
                                                        <v-btn color="success" block flat @click="saveDefects" :loading="loading.saveDefects">Сохранить</v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-tab-item>
                                        </v-tabs>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                        </v-tabs>
                    </v-card>

                    <v-card>
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Комментарии к автомобилю</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>
                        <v-card-text primary-title class="pt-3 pb-0">
                            <v-layout>
                                <v-flex class="px-3 py-0">
                                    <v-text-field
                                        name="car_card_comment"
                                        label="Введите комментарий"
                                        multi-line 
                                        clearable
                                        no-resize
                                        v-model="newComment"
                                    ></v-text-field>
                                </v-flex>
                            </v-layout>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn color="success" block flat @click="storeComment" :loading="loading.comments">Сохранить комментарий</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>            
            </transition>

            <v-flex v-if="comments.length === 0  && !loading.pageLoad"> 
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Комментариев к автомобилю нет.
                </v-alert>
            </v-flex>
        </v-layout>


        <!-- Services price -->
        <v-layout row wrap v-if="!loading.pageLoad">
            <v-flex xs12 sm12 md12 lg12>
                <v-btn color="success" append @click="getInvoice">Расчитать примерную цену и услуги</v-btn>      
            </v-flex>
        </v-layout>

        <v-layout row wrap style="position: relative;" v-if="!loading.pageLoad">
            <transition name="fade-transition" mode="out-in">
                <div class="loading-block" v-if="loading.invoice" v-cloak>
                    <v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
                </div>
            </transition>

            <v-flex v-if="!loading.pageLoad && totalSum === 0"> 
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Сумма и услуги расчитаны не были.
                </v-alert>
            </v-flex>
            
            <!-- Invoice list -->
            <v-flex xs12 sm12 md12 lg12 v-if="invoice.length > 0">         
                <v-card>
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0" style="float: left;">Примерная цена на услуги</p>
                                    <p class="title my-0" style="float: right;"><strong>Итого:</strong> <span class="red--text">{{ totalSum }} сомони</span></p>   
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <v-list two-line>
                        <template v-for="(item, index) in invoice">
                            <v-list-tile :key="item.title" avatar ripple @click="toggle(index)">
                                <v-list-tile-content>
                                    <v-list-tile-title>{{ item.service_type_name }}</v-list-tile-title>
                                    <v-list-tile-sub-title>Категория: {{ item.service_category_name }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                                <v-list-tile-action>
                                    <v-list-tile-action-text class="body-2">{{ item.service_price }} сом.</v-list-tile-action-text>
                                    <v-icon v-if="selectedServices.indexOf(index) < 0" color="grey lighten-1">star_border</v-icon>
                                    <v-icon v-else color="yellow darken-2">star</v-icon>
                                </v-list-tile-action>
                            </v-list-tile>
                            <v-divider v-if="index + 1 < invoice.length" :key="index"></v-divider>
                        </template>
                    </v-list>
                </v-card>
            </v-flex>
        </v-layout>

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
        }
    },
    data() {
        return {
            selectedServices: [],
            invoice: [],
            totalSum: 0,
            car: {
                cover_image: '/dasd/'
            },
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
            newComment: '',
            selects: {
                defects: [],
                defectOptions: []
            },
            selected: {
                defects: null,
                defectOptions: []
            },
        }
    },
    methods: {
        fetchCarCardInfo() {
            this.loading.pageLoad = true;
            axios.get(`/sto/${this.$route.params.slug}/cars/${this.$route.params.car}/card`)
                .then(response => {
                    console.log(response);
                    this.car = response.data.car;
                    this.defects = response.data.defects_info;
                    this.comments = this.car.card.comments;

                    console.log(this.comments);

                    let defect_options = response.data.car.card.defect_options;
                    defect_options.map(option => {
                        this.selected.defectOptions.push(option.id);
                    });

                    this.defects.map((item, index) => {                        
                        if(item.defects.length > 0) {
                            let defects = [];
                            
                            item.defects.map((defect, index) => {                      
                                defects.push({
                                    text: defect.defect_name,
                                    value: defect.id
                                });
                                let options = [];
                                defect.defect_options.map(option => {
                                    options.push({
                                        text: option.defect_option_name,
                                        value: option.id
                                    });                                                                       
                                });
                                this.selects.defectOptions[defect.id] = options;
                            });
                            this.selects.defects.push(defects);
                        }                        
                    });
                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },

        saveDefects() {
            this.loading.saveDefects = true;
            axios.post(`/sto/${this.$route.params.slug}/cards/${this.car.card.id}/defects`, { 
                'defect_options': this.selected.defectOptions 
            })
            .then(response => {
                this.loading.saveDefects = false;
                this.snackbar.color = 'success';
                this.snackbar.text = response.data.message;
                this.snackbar.active = true;
            })
            .catch(error => console.log(error));
        },

        getInvoice() {
            this.loading.invoice = true;
            axios.post(`/sto/${this.$route.params.slug}/services/invoice`, { 'defect_options': this.selected.defectOptions })
                .then(response => {
                    this.selectedServices = [];
                    this.invoice = response.data;
                    this.invoice.map((item, index) => {
                        this.selectedServices.push(index);
                        this.getTotal();
                    });
                    this.loading.invoice = false;
                })
                .catch(error => console.log(error));

            
        },

        toggle(index) {
            const i = this.selectedServices.indexOf(index)

            if (i > -1)
                this.selectedServices.splice(i, 1);
            else
                this.selectedServices.push(index);            

            this.getTotal();
        },

        getTotal() {
            this.totalSum = 0;
            this.selectedServices.map(serviceIndex => {
                this.invoice.map((item, index) => {
                    if(index === serviceIndex) {
                        this.totalSum += item.service_price;
                    }
                });
            });
        },

        storeComment() {
            this.loading.comments = true;
            axios.post(`/sto/${this.$route.params.slug}/cards/${this.car.card.id}/comments`, { comment: this.newComment })
                .then(response => {
                    this.comments.push(response.data.comment);
                    this.loading.comments = false;
                    this.newComment = '';
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                }) 
                .catch(error => console.log(error));
        }
    },
    created() {
        this.fetchCarCardInfo();
    }
}
</script>

<style>
    .loading-block {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
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
