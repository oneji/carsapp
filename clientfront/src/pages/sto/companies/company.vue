<template>
    <div>        
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>  
                <v-btn color="info" append @click="$router.forward()">Вперед</v-btn>               
                <v-btn color="info" append :to="{ name: 'StoCarCreate' }">Добавить автомобиль</v-btn>               
            </v-flex>
        </v-layout>

        <v-layout style="position: relative;">
            <Loading :loading="loading" />

            <v-flex v-if="cars.length === 0 && !loading">
                <Alert message="Автомобилей в компании не зарегистрировано." type="info" />
            </v-flex>
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="car in cars" :key="car.id">
                <Car 
                    :item="car" 
                    :card="true" 
                    :details="true" 
                    :edit="true"
                    @card-created="onCarCardCreated" />
            </v-flex>
        </transition-group>
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import Loading from '@/components/Loading'
import Alert from '@/components/Alert'
import MoveButtons from '@/components/MoveButtons'
import Car from '@/components/Car'

export default {
    components: {
        Loading, Alert, MoveButtons, Car
    },
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    data() {
        return {
            cars: [],
            card: {
                loading: null,
                showInfo: null
            },
            loading: false
        }
    },
    methods: {
        fetchCompanyCars() {
            this.loading = true;
            axios.get(`/sto/${this.$route.params.slug}/companies/${this.$route.params.company}/cars`)
                .then(response => {
                    this.cars = response.data.cars;
                    this.loading = false;
                })
                .catch(error => console.log(error));
        },
        
        onCarCardCreated(params) {
            this.cars.map(car => {
                if(car.id === params.car.car_id)
                    car.card = params.car.card
            });

            this.$store.dispatch('showSnackbar', {
                color: 'success',
                text: params.message,
                active: true
            });
        }
    },
    created() {
        this.fetchCompanyCars();
    }
}
</script>

<style>

</style>
