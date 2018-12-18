<template>
    <div>
        <v-layout style="position: relative;">
            <Loading :loading="loading" />
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">
            <v-flex v-for="car in cars" :key="car.id" xs12 sm6 md4 lg3>
                <Car 
                    :item="car"
                    :details="true"
                    :card="false"
                    :actions="false" />
            </v-flex>
        </transition-group>
    </div>
</template>

<script>
import axios from '@/axios'
import Car from '@/components/Car'
import Loading from '@/components/Loading'

export default {
    components: {
        Car,
        Loading
    },
    data() {
        return {
            loading: true,
            cars: []
        }
    },
    methods: {
        getCars() {
            axios.get(`/sto/${this.$route.params.slug}/cars/repaired`)
                .then(response => {
                    console.log(response.data)
                    let { repairedRequests } = response.data;
                    this.cars = repairedRequests.map(req => req.car);

                    this.loading = false;
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.getCars();
    }
}
</script>

<style>

</style>
