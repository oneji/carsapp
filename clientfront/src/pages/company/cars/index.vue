<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" :to="{ name: 'CompanyCarsCreate' }" append>Добавить машину</v-btn>
            </v-flex>
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg2 v-for="car in cars" :key="car.id" v-cloak>
                <v-card>
                    <v-card-media src="/static/images/no-car-img.png" height="150px">
                        
                    </v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title class="pt-3 pb-0">
                        <div>
                            <h3 class="headline mb-0">{{ car.brand_name }} {{ car.model_name }}</h3>
                            <div>Контакта нет</div>
                        </div>
                    </v-card-title>
                    <v-card-actions>
                        <v-btn flat color="success">Просмотреть</v-btn>
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
        }
    },
    data() {
        return {
            cars: []
        }
    },
    methods: {
        fetchCars() {
            axios.get('/company/cars')
                .then(response => {
                    this.cars = response.data;
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
