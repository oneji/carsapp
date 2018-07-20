<template>
    <div>
        <v-layout style="position: relative;">
            <v-flex v-if="requests.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Заявок от компаний не найдено.
                </v-alert>
            </v-flex>
            
            <loading :loading="loading.pageLoad" />
        </v-layout>

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="(request, index) in requests" :key="request.id" v-cloak>
                <v-card>
                    <v-card-title primary-title class="py-0 px-0">
                        <v-layout>
                            <v-flex>                                
                                <v-list two-line>
                                    <v-list-tile avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title class="title mb-1">{{ request.company_name }}</v-list-tile-title>
                                            <v-list-tile-sub-title v-if="request.status === 1">
                                                Статус: <span class="label label-success">Принята</span>
                                            </v-list-tile-sub-title>
                                            <v-list-tile-sub-title v-else>
                                                Статус: <span class="label label-warning">Ждет принятия</span>
                                            </v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <v-divider></v-divider>
                                    <v-list-tile avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title>Дата создания</v-list-tile-title>
                                            <v-list-tile-sub-title>{{ request.created_at }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <v-divider v-if="request.status === 0"></v-divider>
                                </v-list>
                            </v-flex>
                        </v-layout>    
                    </v-card-title>

                    <v-card-actions v-if="request.status === 0">
                        <v-btn block flat color="success" :loading="loading.accept" @click="changeRequestStatus(request.id, index, 1)">Принять</v-btn>
                        <v-btn block flat color="error" :loading="loading.reject" @click="changeRequestStatus(request.id, index, 0)">Отклонить</v-btn>
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
    mixins: [ snackbar ],
    components: {
        Loading
    },
    computed: {
        assetsURL() {
            return config.assetsURL;
        },
    },
    data() {
        return {
            dialog: false,
            loading: {
                accept: false,
                reject: false,
                pageLoad: false
            },

            requests: [],
            items: [],
            stos: [],
            sto_id: null
        }
    },
    methods: {
        fetchRequests() {
            this.loading.pageLoad = true;
            axios.get(`/sto/${this.$route.params.slug}/requests`)
                .then(response => {
                    this.requests = response.data;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.error());           
        },

        changeRequestStatus(request_id, index, type) {
            if(type === 1)
                this.loading.accept = true;
            else if(type === 0) 
                this.loading.reject = true;

            axios.put(`/sto/${this.$route.params.slug}/requests/${request_id}`, { type })
                .then(response => {
                    this.loading.accept = false;
                    this.loading.reject = false;
                    this.requests.splice(index, 1);
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })
                .catch(error => console.log(error));
        },
    },
    created() {
        this.fetchRequests();
    }
}
</script>

<style>

</style>
