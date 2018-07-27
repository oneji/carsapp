<template>
    <div>
        <v-layout style="position: relative;">  
            <v-flex v-if="filteredRequests.length === 0 && !loading.pageLoad">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    В архиве заявок не найдено.
                </v-alert>
            </v-flex>
            
            <Loading :loading="loading.pageLoad" />
        </v-layout>        

        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="request in filteredRequests" :key="request.id">
                <RepairRequest :item="request" />
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
    computed: {
        filteredRequests() {
            return this.requests.filter(request => request.archived !== 0);
        }
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
            axios.get(`/company/${this.$route.params.slug}/requests/repair`)
                .then(response => {
                    this.requests = response.data;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.error());
        },
    },
    created() {
        this.fetchRequests();
    }
}
</script>

<style>

</style>
