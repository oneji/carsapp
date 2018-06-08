<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success" append @click="$router.go(-1)">Назад</v-btn>           
            </v-flex>
        </v-layout>

        <v-layout row wrap style="position: relative">
            <transition name="fade-transition" mode="out-in">
                <div class="loading-block" v-if="loading.pageLoad" v-cloak>
                    <v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
                </div>
            </transition>
            
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
                        <v-card-media :src="car.cover_image !== '' ? assetsURL + '/' + car.cover_image : '/static/images/no-photo.png'" height="200px"></v-card-media>
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
                        <v-card-actions class="pr-0 pl-0">
                            <v-container class="pt-0">
                                <v-layout row wrap>
                                    <v-flex>
                                        <div><strong>Пробег:</strong> {{ car.milage }} км.</div>
                                        <div><strong>Год выпуска:</strong> {{ car.year }}</div>
                                        <div><strong>Vin код:</strong> {{ car.vin_code }}</div>
                                        <div><strong>Гос номер:</strong> {{ car.number }}</div>
                                        <div><strong>Объем двигателя:</strong> {{ car.engine_capacity }}</div>
                                        <div><strong>Тип ДВС:</strong> {{ car.engine_type_name }}</div>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </transition>

            <transition name="fade-transition" mode="out-in"> 
                <v-flex xs12 sm12 md5 lg5 v-if="!loading.pageLoad" v-cloak>
                    <v-card>
                        <v-tabs v-model="active" color="light-blue" dark slider-color="white" show-arrows>
                            <v-tab :key="1" ripple>Дефектный акт</v-tab>
                            <v-tab-item :key="1">
                                <v-card flat>
                                    <v-card-text class="px-0 py-0">
                                        <v-tabs v-model="active_2" color="light-blue" dark slider-color="white">
                                            <v-tab v-for="(type, index) in defects" :key="index" ripple>
                                                {{ type.defect_type_name }}
                                            </v-tab>
                                            <v-tab-item v-for="(type, index) in defects" :key="index">
                                                <v-card flat>
                                                    <v-card-text>
                                                        <v-form>
                                                            <v-select
                                                                :items="selects.defects[index]"
                                                                v-model="selected.defects"
                                                                label="Вид дефекта"
                                                                single-line
                                                            ></v-select>

                                                            <v-select
                                                                :items="selects.defectOptions[selected.defects - 1]"
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
                </v-flex>            
            </transition>
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

export default {
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    data() {
        return {
            car: {
                cover_image: '/dasd/'
            },
            loading: {
                pageLoad: false,
                saveDefects: false
            },
            active: null,
            active_2: null,
            tabs: [
                { text: 'Дефектный акт' },
                { text: 'Ремонтные работы' },
            ],
            defects: [],
            selects: {
                defects: [],
                defectOptions: []
            },
            selected: {
                defects: null,
                defectOptions: []
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
        fetchCarCardInfo() {
            this.loading.pageLoad = true;
            axios.get(`/sto/${this.$route.params.slug}/cars/${this.$route.params.car}/card`)
                .then(response => {
                    console.log(response);
                    this.car = response.data.car;
                    this.defects = response.data.defects_info;

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
                                this.selects.defectOptions.push(options);
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
                console.log(response);
                this.loading.saveDefects = false;
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
</style>
