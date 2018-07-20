<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click.native="dialog = true">Добавить СТО</v-btn>
            </v-flex>
        </v-layout>

        <v-layout style="position: relative;">  
            <v-flex v-if="requests.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Вы пока не отправляли ни одной заявки.
                </v-alert>
            </v-flex>
            
            <Loading :loading="loading.pageLoad" />
        </v-layout>        

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="request in requests" :key="request.id">
                <StoRequest :item="request" @cancel="onRequestCanceled" />
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
import StoRequest from '@/components/StoRequest'
import Loading from '@/components/Loading'

export default {
    mixins: [ snackbar ],
    components: {
        StoRequest, Loading
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
            axios.get(`/company/${this.$route.params.slug}/sto-list`)
                .then(response => {
                    this.requests = response.data;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.error());           
        },

        sendRequest() {
            this.loading.create = true;
            axios.post(`/company/${this.$route.params.slug}/sto-list/${this.sto_id}`)
                .then(response => {
                    if(response.data.success) {
                        this.successSnackbar(response.data.message);                        
                    } else {
                        this.errorSnackbar(response.data.message);
                    }
                    
                    this.fetchRequests();
                    this.loading.create = false;
                }) 
                .catch(error => console.log(error));
        },

        onRequestCanceled(request_id) {
            this.requests = this.requests.filter(request => request.id !== request_id);
        }
    },
    created() {
        this.fetchRequests();
        this.fetchSTOs();
    }
}
</script>

<style>

</style>
