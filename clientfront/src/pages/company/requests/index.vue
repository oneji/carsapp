<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click.native="dialog = true">Отправить заявку СТО</v-btn>
            </v-flex>
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
                                            <v-list-tile-title class="title mb-1">{{ request.sto_name }}</v-list-tile-title>
                                            <v-list-tile-sub-title v-if="request.status === 0">
                                                Статус: <span class="label label-warning">Ждет принятия</span>
                                            </v-list-tile-sub-title>
                                            <v-list-tile-sub-title v-else-if="request.status === 1">
                                                Статус: <span class="label label-success">Принята</span>
                                            </v-list-tile-sub-title>                                            
                                            <v-list-tile-sub-title v-else-if="request.status === 2">
                                                Статус: <span class="label label-danger">Отклонена</span>
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
                        <v-btn block flat color="error" :loading="loading.cancel" @click="cancelRequest(request.id, index)">Отменить</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </transition-group>

        <!-- Bind driver -->
        <v-dialog v-model="dialog" max-width="500">
            <form @submit.prevent="sendRequest" data-vv-scope="create-request-form">
                <v-card>
                    <v-card-title class="headline">Отправить заявку</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>   
                                <v-select :items="stos" v-model="sto_id" label="Выберите СТО" prepend-icon="category"
                                    name="sto_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('sto_id')"
                                    data-vv-name="sto_id" data-vv-as='"СТО"'
                                ></v-select>     
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="loading.create" flat="flat" type="submit">Отправить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

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
                create: false,
                cancel: false
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
            axios.get(`/company/${this.$route.params.slug}/requests`)
                .then(response => {
                    console.log(response);
                    this.requests = response.data;
                })
                .catch(error => console.error());           
        },

        sendRequest() {
            this.loading.create = true;
            axios.post(`/company/${this.$route.params.slug}/requests/${this.sto_id}`)
                .then(response => {
                    this.fetchRequests();
                    this.loading.create = false;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                }) 
                .catch(error => console.log(error));
        },

        cancelRequest(request_id, index) {
            this.loading.cancel = true;
            axios.delete(`/company/${this.$route.params.slug}/requests/${request_id}`)
                .then(response => {
                    this.loading.cancel = false;
                    this.requests.splice(index, 1);
                })
                .catch(error => console.log(error));
        },

        fetchSTOs() {
            axios.get('/admin/stos')
                .then(response => {                    
                    this.items = response.data;
                    this.items.map(sto => {
                        this.stos.push({
                            text: sto.sto_name,
                            value: sto.id
                        });                
                    });
                })
                .catch(error => {
                    console.log(error);
                });
        },
    },
    created() {
        this.fetchRequests();
        this.fetchSTOs();
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
    .label-danger {
        background-color: #f96a74;
    }
</style>
