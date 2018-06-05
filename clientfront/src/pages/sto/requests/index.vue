<template>
    <div>
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

export default {
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
                reject: false
            },
            snackbar: {
                active: false,
                text: '',
                timeout: 5000,
                color: ''
            },

            requests: [],
            items: [],
            stos: [],
            sto_id: null
        }
    },
    methods: {
        fetchRequests() {
            axios.get(`/sto/${this.$route.params.slug}/requests`)
                .then(response => {
                    console.log(response);
                    this.requests = response.data;
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
                    console.log(response);
                    this.loading.accept = false;
                    this.loading.reject = false;
                    this.requests.splice(index, 1);
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })
                .catch(error => console.log(error));
        },

        rejectRequest(request_id, index) {
            console.log('...');
        }
    },
    created() {
        this.fetchRequests();
    }
}
</script>

<style>
    .label {
        display: inline;
        padding: .2em .6em .3em;
        font-size: 80%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }
    .label-warning {
        background-color: #ffa91c;
        padding: 0.3em .6em;
    }
    .label-success {
        background-color: #32c861;
    }
</style>
