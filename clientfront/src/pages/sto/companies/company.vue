<template>
    <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
        <v-flex xs12 sm6 md3 lg3 v-for="car in cars" :key="car.id" v-cloak>
            <v-card>
                <v-card-media :src="car.cover_image === null ? '/static/images/no-car-img.png' : assetsURL + '/' + car.cover_image" height="150px"></v-card-media> 
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
                <v-card-actions class="mt-2">
                    <v-btn block flat color="success">Просмотреть</v-btn>
                    <v-btn block flat color="primary">Карточка</v-btn>
                </v-card-actions>
            </v-card>
        </v-flex>
    </transition-group>
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
        fetchCompanyCars() {
            axios.get(`/sto/${this.$route.params.slug}/companies/${this.$route.params.company}/cars`)
                .then(response => {
                    console.log(response);
                    this.cars = response.data.cars;
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.fetchCompanyCars();
    }
}
</script>

<style>

</style>
