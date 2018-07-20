<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click.native="dialog = true">Создать заявку</v-btn>
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
                <RepairRequest :item="request" @cancel="onRequestCanceled" />
            </v-flex>
        </transition-group>
        

        <!-- Send request driver -->
        <v-dialog v-model="dialog" max-width="500">
            <form @submit.prevent="sendRequest" data-vv-scope="create-request-form">
                <v-card>
                    <v-card-title class="headline">Создать заявку</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12>   
                                <v-select :items="cars" v-model="car_id" label="Выберите автомобиль" prepend-icon="directions_car"
                                    autocomplete
                                    name="car_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('car_id')"
                                    data-vv-name="car_id" data-vv-as='"Автомобиль"'
                                ></v-select>

                                <v-select :items="stos" v-model="sto_id" label="Выберите СТО" prepend-icon="build"
                                    name="sto_id"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('sto_id')"
                                    data-vv-name="sto_id" data-vv-as='"СТО"'
                                ></v-select>    

                                <v-text-field
                                    v-validate="'required'"
                                    data-vv-name="comment" data-vv-as='"Комментарий"'
                                    :error-messages="errors.collect('comment')" 
                                    name="comment"
                                    label="Комментарий"
                                    multi-line 
                                    clearable
                                    no-resize
                                    v-model="comment"
                                    rows="3"
                                    prepend-icon="comment"
                                ></v-text-field>              
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
import RepairRequest from '@/components/RepairRequest'
import Loading from '@/components/Loading'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    components: {
        RepairRequest, Loading
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
            cars: [],
            car_id: null,
            company_id: null,
            sto_id: null,
            comment: '',
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

        fetchCars() {
            axios.get(`/company/${this.$route.params.slug}/cars`)
                .then(response => {   
                    response.data.company.cars.forEach(car => {
                        this.cars.push({
                            text: car.brand_name + ' ' + car.model_name + ' (' + car.number + ')',
                            value: car.id
                        });
                    });

                })
                .catch(error => console.log(error));
        },

        fetchRequests() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/requests/repair`)
                .then(response => {
                    this.requests = response.data;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.error());
        },

        sendRequest() {
            this.$validator.validateAll('create-request-form')
                .then(success => {
                    if(success) {
                        this.loading.create = true;
                        axios.post(`/company/${this.$route.params.slug}/requests/repair`, {
                            'car_id': this.car_id,
                            'sto_id': this.sto_id,
                            'comment': this.comment
                        })
                        .then(response => {
                            if(response.data.success) {
                                this.successSnackbar(response.data.message);
                                this.sto_id = null;
                                this.car_id = null;
                                this.comment = null;
                                this.dialog = false;                        
                            } else {
                                this.errorSnackbar(response.data.message);
                            }
                            
                            this.fetchRequests();
                            this.loading.create = false;
                        }) 
                        .catch(error => console.log(error));
                    }
                });   
        },

        onRequestCanceled(request_id) {
            this.requests = this.requests.filter(request => request.id !== request_id);
        }
    },
    created() {
        this.fetchRequests();
        this.fetchSTOs();
        this.fetchCars();
    }
}
</script>

<style>

</style>
