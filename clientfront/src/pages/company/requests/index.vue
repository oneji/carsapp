<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click.native="dialog = true">Отправить заявку СТО</v-btn>
            </v-flex>
        </v-layout>

        <v-layout style="position: relative;">  
            <v-flex v-if="requests.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Вы пока не отправляли ни одной заявки.
                </v-alert>
            </v-flex>
            
            <transition name="fade-transition" mode="out-in">
                <div class="loading-block" v-if="loading.pageLoad" v-cloak>
                    <v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
                </div>
            </transition>
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
                                                Статус: <my-label text="Ждет принятия" color="#ffa91c" />
                                            </v-list-tile-sub-title>
                                            <v-list-tile-sub-title v-else-if="request.status === 1">
                                                Статус: <my-label text="Принята" color="#32c861" />
                                            </v-list-tile-sub-title>                                            
                                            <v-list-tile-sub-title v-else-if="request.status === 2">
                                                Статус: <my-label text="Отклонена" color="#f96a74" />
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
import snackbar from '@/components/mixins/snackbar'
import MyLabel from '@/components/Label'

export default {
    mixins: [ snackbar ],
    computed: {
        assetsURL() {
            return config.assetsURL;
        },
    },
    components: {
        MyLabel
    },
    data() {
        return {
            dialog: false,
            loading: {
                create: false,
                cancel: false,
                pageLoad: false
            },

            requests: [],
            items: [],
            stos: [],
            sto_id: null
        }
    },
    methods: {
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

        fetchRequests() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/requests`)
                .then(response => {
                    this.requests = response.data;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.error());           
        },

        sendRequest() {
            this.loading.create = true;
            axios.post(`/company/${this.$route.params.slug}/requests/${this.sto_id}`)
                .then(response => {
                    console.log(response);
                    if(response.data.success) {
                        this.snackbar.color = 'success';                        
                    } else {
                        this.snackbar.color = 'error';
                    }
                    
                    this.fetchRequests();
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                    this.loading.create = false;
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

        
    },
    created() {
        this.fetchRequests();
        this.fetchSTOs();
    }
}
</script>

<style>
    .loading-block {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
