<template>
    <div>        
        <v-layout style="position: relative;">
            <v-flex v-if="cars.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Проданных машин не найдено.
                </v-alert>
            </v-flex>

            <loading :loading="loading.pageLoad" />
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="car in cars" :key="car.id" v-cloak>
                <car :item="car" :for-sale="true" :details="true" @sale="onCarSale" />
            </v-flex>
        </transition-group>

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
import Car from '@/components/Car'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    computed: {
        assetsURL() {
            return config.assetsURL;
        },
    },
    components: {
        Loading, MyLabel, Car
    },
    data() {
        return {
            cars: [],
            loading: {
                pageLoad: false,
                sale: null
            }
        }
    },
    methods: {
        fetchCars() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/cars/sold`)
                .then(response => {  
                    this.cars = response.data.cars;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },

        onCarSale(params) {
            this.cars = this.cars.filter(car => car.id !== params.car_id);
            this.successSnackbar(params.message);
        },
    },
    created() {
        this.fetchCars();
    }
}
</script>

<style>

</style>
