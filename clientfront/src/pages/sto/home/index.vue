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
            axios.get(`/sto/${this.$route.params.slug}/statistics`)
                .then(response => {
                    console.log(response.data);
                    let { repairedCarsCount } = response.data;

                    this.statistics = [
                        { title: 'Автомобили в ремонте', link: { name: 'StoRepairedCars' }, value: repairedCarsCount, boxIcon: 'directions_car' },
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
