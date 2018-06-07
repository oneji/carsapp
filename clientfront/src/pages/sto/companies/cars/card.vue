<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click="$router.go(-1)">Назад</v-btn>
            </v-flex>
        </v-layout>

        <v-layout row wrap style="position: relative">
            <transition name="fade-transition" mode="out-in">
                <div class="loading-block" v-if="loading" v-cloak>
                    <v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
                </div>
            </transition>
            
            <transition name="fade-transition" mode="out-in">
                <v-flex xs12 sm12 md4 lg3 v-if="!loading" v-cloak>
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
                        <v-card-actions>
                            <v-container>
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
        </v-layout>
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
            loading: false
        }
    },
    methods: {
        fetchCarCardInfo() {
            this.loading = true;
            axios.get(`/sto/${this.$route.params.slug}/cars/${this.$route.params.car}/card`)
                .then(response => {
                    console.log(response);
                    this.car = response.data;
                    this.loading = false;
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
