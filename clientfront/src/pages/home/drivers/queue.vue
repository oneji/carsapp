<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success">Добавить в очередь</v-btn>
            </v-flex>
        </v-layout>
        <v-layout style="position: relative;">
            <loading :loading="loading.pageLoad" />

            <v-flex v-if="queue.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true"> 
                    Ни одного водителя в очереди не найдено.
                </v-alert>
            </v-flex>
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">     
            <v-flex v-for="(q, index) in queue" :key="q.driver.id" xs12 sm6 md3 lg2>
                <v-card>
                    <v-card-media>                        
                        <v-container>
                            <v-layout wrap justify-center align-center>
                                <v-avatar class="mt-3" size="100" color="grey lighten-4">
                                    <img v-if="q.driver.photo" :src="`${assetsURL}/${q.driver.photo}`" alt="">
                                    <img v-else src="/static/images/user.png" alt="Нет аватара">
                                </v-avatar>

                                <v-flex xs12 sm12 md12 lg12>
                                    <p class="subheading text-xs-center mb-0">{{ q.driver.fullname }}</p>
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
                                            <v-list-tile-title>Дата добавления</v-list-tile-title>
                                            <v-list-tile-sub-title>{{ q.created_at | moment('MMMM Do, YYYY') }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <v-divider></v-divider>
                                    <v-list-tile avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title>Компания</v-list-tile-title>
                                            <v-list-tile-sub-title>{{ q.driver.companies[0].company_name }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <v-divider></v-divider>
                                </v-list>
                            </v-flex>
                        </v-layout>    
                    </v-card-title>

                    <v-card-actions>
                        <v-btn flat block color="warning" :loading="loading.queue === q.driver.id" @click="backFromQueue(q.driver.id, index)">Убрать из очереди</v-btn>
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
import snackbar from '@/components/mixins/snackbar'
import Loading from '@/components/Loading'

export default {
    mixins: [snackbar],
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    components: {
        Loading
    },
    data() {
        return {
            loading: {
                pageLoad: true,
                queue: null
            },
            queue: [] 
        }
    },
    methods: {
        getQueue() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/drivers/queue`)
                .then(response => {
                    console.log(response);
                    this.queue = response.data;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },

        backFromQueue(driver_id, index) {
            this.loading.queue = driver_id;
            axios.delete(`/company/${this.$route.params.slug}/drivers/${driver_id}/queue`)
                .then(response => {
                    if(response.data.success) {
                        this.snackbar.color = 'success';
                        this.queue.splice(index, 1);
                    } else
                        this.snackbar.color = 'error';
                        
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                    this.loading.queue = null;
                })
        }
    },
    created() {
        this.getQueue();
    }
}
</script>

<style>

</style>
