<template>
    <div>
        <v-layout style="position: relative">
            <v-flex v-if="drivers.length === 0 && !loading">
                <v-alert outline transition="scale-transition" type="info" :value="true"> 
                    Ни одного водителя не зарегистрировано.
                </v-alert>
            </v-flex>

            <Loading :loading="loading" />
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">     
            <v-flex v-for="(driver) in drivers" :key="driver.info.id" xs12 sm4 md3 lg2>
                <v-card>
                    <v-card-media>                        
                        <v-container>
                            <v-layout wrap justify-center align-center>
                                <v-avatar class="mt-3" size="100" color="grey lighten-4">
                                    <img v-if="driver.info.photo" :src="`${assetsURL}/${driver.info.photo}`" alt="">
                                    <img v-else src="/static/images/user.png" alt="Нет аватара">
                                </v-avatar>

                                <v-flex xs12 sm12 md12 lg12>
                                    <p class="subheading text-xs-center mb-0">{{ driver.info.fullname }}</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    
                    <v-card-title primary-title class="py-0 px-0">
                        <v-layout>
                            <v-flex>                                
                                <v-list two-line>
                                    <v-list-tile avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title>Телефон</v-list-tile-title>
                                            <v-list-tile-sub-title v-if="driver.info.phone !== null">{{ driver.info.phone }}</v-list-tile-sub-title>
                                            <v-list-tile-sub-title v-else>Нет</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <!-- <v-divider></v-divider> -->
                                </v-list>
                            </v-flex>
                        </v-layout>    
                    </v-card-title>

                    <!-- <v-card-actions>
                        <transition name="fade-transition" mode="out-in" v-if="driver.queue === null">
                            <v-btn flat block color="primary" :loading="loading.queue === driver.id" @click="addToQueue(driver.id, index)" >В очередь</v-btn>
                        </transition>
                        <transition name="fade-transition" mode="out-in" v-else>
                            <v-btn flat block color="warning" :loading="loading.queue === driver.id" @click="backFromQueue(driver.id, index)" >Убрать из очереди</v-btn>
                        </transition>
                        <v-btn icon :to="{ name: 'CompanyDriversEdit', params: { driver: driver.id } }">
                            <v-icon>edit</v-icon>
                        </v-btn>
                    </v-card-actions> -->
                </v-card>
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
import assetsURL from '@/components/mixins/assets-url'
import snackbar from '@/components/mixins/snackbar'
import Loading from '@/components/Loading'

export default {
    mixins: [assetsURL, snackbar],
    components: {
        Loading
    },
    data() {
        return {
            loading: false,
            companies: [],
            drivers: []
        }
    },
    methods: {
        fetchUserProjects() {
            this.loading = true;
            axios.get('/all-cars')
                .then(response => {
                    this.companies = response.data.companies;

                    this.companies.map(company => {
                        company.drivers.map(driver => {                            
                            let driverInfo = {
                                'info': driver,
                                'company': company
                            };                    
                            this.drivers.push(driverInfo);   
                        })
                    })

                    this.loading = false;
                })
                .catch(error => console.error());
        },
    },
    created() {
      this.fetchUserProjects();  
    },
}
</script>

<style>

</style>
