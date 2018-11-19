<template>
    <div>
        <v-layout style="position: relative;">
            <Loading :loading="loading" />
        </v-layout>

        <transition class="row wrap" name="slide-x-transition" mode="out-in"> 
            <v-layout row wrap v-if="!loading">           
                <v-flex v-for="item in statistics" :key="item.link.name" xs12 sm6 mg3 lg4>
                    <TileBox 
                        :title="item.title" 
                        :link="item.link" 
                        :value="item.value" 
                        :box-icon="item.boxIcon" />
                </v-flex>
            </v-layout>
        </transition> 
    </div>
</template>

<script>
import axios from '@/axios'
import TileBox from '@/components/TileBox'
import Loading from '@/components/Loading'

export default {
    components: {
        TileBox,
        Loading
    },
    data() {
        return {
            loading: true,
            statistics: []
        }
    },
    methods: {
        getStatistics() {
            axios.get('/statistics')
                .then(response => {
                    let { companiesCount, carsCount, reservedCarsCount, driversCount, queuedDriversCount } = response.data;

                    this.statistics = [
                        { title: 'Компании', link: { name: 'HomeCompanies' }, value: companiesCount, boxIcon: 'business' },
                        { title: 'Автомобили', link: { name: 'HomeCars' }, value: carsCount, boxIcon: 'directions_car' },
                        { title: 'Резервные автомобили', link: { name: 'HomeReservedCars' }, value: reservedCarsCount, boxIcon: 'directions_car' },
                        { title: 'Водители', link: { name: 'HomeDriversIndex' }, value: driversCount, boxIcon: 'people' },
                        { title: 'Водители в очереди', link: { name: 'HomeDriversQueue' }, value: queuedDriversCount, boxIcon: 'people' },
                    ];

                    this.loading = false;
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.getStatistics();
    }
}
</script>

<style>

</style>
