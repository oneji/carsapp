<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" :to="{ name: 'CompanyCarsCreate' }" append>Добавить машину</v-btn>
                <v-btn color="primary" append>Привязать водителя</v-btn>
            </v-flex>
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="car in cars" :key="car.id" v-cloak>
                <v-card>
                    <v-card-media :src="car.cover_image === null ? '/static/images/no-car-img.png' : assetsURL + '/' + car.cover_image" height="150px"></v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title class="pt-3 pb-0">
                        <div>
                            <h3 class="headline mb-0">{{ car.brand_name }} {{ car.model_name }}</h3>
                            <div>Водителя нет</div>
                        </div>
                    </v-card-title>
                    <v-card-actions class="mt-2">
                        <v-btn block flat color="success">Просмотреть</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </transition-group>
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'

export default {
    computed: {
        assetsURL() {
            return config.assetsURL;
        },
    },
    data() {
        return {
            cars: []
        }
    },
    methods: {
        fetchCars() {
            axios.get(`/company/${this.$route.params.slug}/cars`)
                .then(response => {
                    console.log(response.data);
                    this.cars = response.data.cars;
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
