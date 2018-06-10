<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" :to="{ name: 'CompanyDriversCreate' }" append>Добавить водителя</v-btn>
            </v-flex>
        </v-layout>

        <v-layout v-if="drivers.length === 0">
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Ни одного водителя не зарегистрировано.
                </v-alert>
            </v-flex>
        </v-layout>

        <transition-group v-else tag="v-layout" class="row wrap" name="slide-x-transition">     
            <v-flex v-for="(driver, index) in drivers" :key="driver.id" xs12 sm6 md3 lg2>
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
                                            <v-list-tile-title>Email</v-list-tile-title>
                                            <v-list-tile-sub-title>{{ driver.email }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <v-divider></v-divider>
                                </v-list>
                            </v-flex>
                        </v-layout>    
                    </v-card-title>

                    <v-card-actions>
                        <v-btn flat color="red" @click="deleteDriver(drive.id, index)">Удалить</v-btn>
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
            drivers: []
        }
    },
    methods: {
        fetchDrivers() {
            axios.get(`/company/${this.$route.params.slug}/drivers`)
                .then(response => {
                    console.log(response);
                    this.drivers = response.data.drivers;
                })
                .catch(error => console.log(error));
        },

        deleteDriver(driver_id, index) {

        }
    },
    created() {
        this.fetchDrivers();
    }
}
</script>

<style>

</style>
