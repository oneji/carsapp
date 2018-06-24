<template>
    <div>
        <v-layout style="position: relative;">
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="companies.length === 0 && !loading">
                    Компаний не найдено.
                </v-alert>
            </v-flex>
            
            <loading :loading="loading" />
        </v-layout>
        <!-- A list of companies -->
        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">     
            <v-flex xs12 sm4 md3 lg3 v-for="company in companies" :key="company.id">
                <v-card>
                    <v-card-media height="150px">
                        <v-layout row justify-center align-center>
                            <v-flex xs6 sm6 md6 lg6>
                                <img v-if="company.logo" :src="`${assetURL}/${company.logo}`" :alt="`Логотип ${company.company_name}`">
                                <img v-else src="/static/images/no-logo.png" alt="Нет логотипа">
                            </v-flex>
                        </v-layout>
                    </v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title>
                        <div>
                            <h3 class="headline mb-0">{{ company.company_name }}</h3>
                            <div v-if="company.contact">Контакт: {{ company.contact }}</div>
                            <div v-else>Контакта нет</div>
                            <div><strong>Количество машин:</strong> {{ company.cars.length }}</div>
                        </div>
                    </v-card-title>
                    <v-card-actions class="pt-1">
                        <v-btn flat block color="orange" :to="{ name: 'StoCompany', params: { company: company.id } }">Панель</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>      
        </transition-group>
    </div>    
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import Loading from '@/components/Loading'

export default {
    components: {
        Loading
    },
    computed: {
        assetURL() {
            return config.assetsURL;
        }
    },
    data() {
        return {
            companies: [],
            loading: false
        }
    },
    methods: {
        fetchCompanies() {
            this.loading = true;
            axios.get(`/sto/${this.$route.params.slug}/companies`)
                .then(response => {
                    this.companies = response.data.companies;
                    this.loading = false;
                }) 
                .catch(error => console.error());
        }
    },
    created() {
        this.fetchCompanies();
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
