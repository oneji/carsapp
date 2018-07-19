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
                <RepairRequest :item="request" :queue="true" @queue="onRequestQueued" />
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
        fetchRequests() {
            this.loading.pageLoad = true;
            axios.get(`/sto/${this.$route.params.slug}/requests/repair`)
                .then(response => {
                    console.log(response);
                    this.requests = response.data;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.error());
        },

        onRequestQueued(message) {
            this.successSnackbar(message);
        }
    },
    created() {
        this.fetchRequests();
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
