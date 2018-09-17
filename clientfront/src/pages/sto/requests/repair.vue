<template>
    <div>
        <v-layout style="position: relative;">  
            <v-flex v-if="requests.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Заявок на ремонт не найдено.
                </v-alert>
            </v-flex>
            
            <Loading :loading="loading.pageLoad" />
        </v-layout>        

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="request in requests" :key="request.id">
                <RepairRequest 
                    :item="request" 
                    :queue="true" 
                    :for-sto="true" 
                    :repair="true" 
                    @queue="onRequestQueued" 
                    @bring="onCarBrought" 
                    @repair="onCarRepairDone" />
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
import RepairRequest from '@/components/RepairRequest'
import Loading from '@/components/Loading'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [snackbar],
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
            query: null,
            selectItems: {
                companies: []
            }
        }
    },
    methods: {
        fetchRequests() {
            this.loading.pageLoad = true;
            axios.get(`/sto/${this.$route.params.slug}/requests/repair`)
                .then(response => {
                    console.log(response.data);
                    this.requests = response.data;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.error());
        },
        onRequestQueued(message) {
            this.successSnackbar(message);
        },
        onCarBrought(message) {
            this.successSnackbar(message);
        },
        onCarRepairDone(message) {
            this.successSnackbar(message);
        },
        clearFilter() {
            this.query = null;
        }
    },
    created() {
        this.fetchRequests();
    }
}
</script>

<style>

</style>
