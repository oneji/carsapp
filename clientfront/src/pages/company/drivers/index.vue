<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" :to="{ name: 'CompanyDriversCreate' }" append>Добавить водителя</v-btn>
            </v-flex>
        </v-layout>

        <v-layout style="position: relative">
            <v-flex v-if="drivers.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true"> 
                    Ни одного водителя не зарегистрировано.
                </v-alert>
            </v-flex>

            <Loading :loading="loading.pageLoad" />
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">     
            <v-flex v-for="(driver, index) in drivers" :key="driver.id" xs12 sm4 md3 lg2>
                <v-card>
                    <v-card-media>                        
                        <v-container>
                            <v-layout wrap justify-center align-center>
                                <v-avatar class="mt-3" size="100" color="grey lighten-4">
                                    <img v-if="driver.photo" :src="`${assetsURL}/${driver.photo}`" alt="">
                                    <img v-else src="/static/images/user.png" alt="Нет аватара">
                                </v-avatar>

                                <v-flex xs12 sm12 md12 lg12>
                                    <p class="subheading text-xs-center mb-0">{{ driver.fullname }}</p>
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
                                            <v-list-tile-sub-title v-if="driver.phone !== null">{{ driver.phone }}</v-list-tile-sub-title>
                                            <v-list-tile-sub-title v-else>Нет</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <v-divider></v-divider>
                                </v-list>
                            </v-flex>
                        </v-layout>    
                    </v-card-title>

                    <v-card-actions>
                        <transition name="fade-transition" mode="out-in" v-if="driver.queue === null">
                            <v-btn flat block color="primary" :loading="loading.queue === driver.id" @click="addToQueue(driver.id, index)" >В очередь</v-btn>
                        </transition>
                        <transition name="fade-transition" mode="out-in" v-else>
                            <v-btn flat block color="warning" :loading="loading.queue === driver.id" @click="backFromQueue(driver.id, index)" >Убрать из очереди</v-btn>
                        </transition>
                        <v-btn icon :to="{ name: 'CompanyDriversEdit', params: { driver: driver.id } }">
                            <v-icon>edit</v-icon>
                        </v-btn>
                    </v-card-actions>                    
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
import config from '@/config'
import Loading from '@/components/Loading'
import snackbar from '@/components/mixins/snackbar'
import assetsURL from '@/components/mixins/assets-url'

export default {
    mixins: [snackbar, assetsURL],    
    components: {
        Loading
    },
    data() {
        return {
            drivers: [],
            loading: {
                pageLoad: false, 
                queue: null
            },
        }
    },    
    methods: {
        fetchDrivers() {            
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/drivers`)
                .then(response => {
                    this.drivers = response.data.allDrivers.drivers;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },

        deleteDriver(driver_id, index) {

        },

        addToQueue(driver_id, index) {
            this.loading.queue = driver_id;
            axios.post(`/company/${this.$route.params.slug}/drivers/${driver_id}/queue`)
                .then(response => {
                    console.log(response);
                    if(response.data.success) {
                        this.successSnackbar(response.data.message);
                        this.drivers[index].queue = response.data.queue;
                    } else {
                        this.errorSnackbar(response.data.message);
                    }
                    this.loading.queue = null;
                })
                .catch(error => console.log(error));
        },

        backFromQueue(driver_id, index) {
            this.loading.queue = driver_id;
            axios.delete(`/company/${this.$route.params.slug}/drivers/${driver_id}/queue`)
                .then(response => {
                    if(response.data.success) {
                        this.snackbar.color = 'success';
                        this.drivers[index].queue = null;
                    } else
                        this.snackbar.color = 'error';
                        
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                    this.loading.queue = null;
                })
        }
    },
    created() {
        this.fetchDrivers();
    }
}
</script>

<style>

</style>
