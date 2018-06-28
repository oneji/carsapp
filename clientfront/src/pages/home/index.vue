<template>
    <div>
        <v-layout style="position: relative;">
            <loading :loading="loading" />

            <v-flex v-if="noProjects && !loading">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Вы пока не привязаны ни к одному проекту.
                </v-alert>
            </v-flex>
        </v-layout>

        <v-layout row>
            <v-flex xs12 sm12 md12 lg12>
                <h1 class="headline">Компании:</h1>
            </v-flex>
        </v-layout>
        <v-divider></v-divider>       

        <transition-group tag="v-layout" class="row wrap" name="fade-transition">                        
            <v-flex xs12 sm6 md3 lg3 v-for="company in companies" :key="company.id" v-cloak>
                <v-card>
                    <v-card-media height="150px">
                        <v-layout row justify-center align-center>
                        <v-flex xs6 sm6 md6 lg6>
                            <img v-if="company.logo" :src="`${assetURL}/${company.logo}`" :alt="`Логотип ${company.company_name}`">
                            <img v-else src="/static/images/no-photo.png" alt="Нет логотипа">
                        </v-flex>
                        </v-layout>
                    </v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title>
                        <div>
                            <h3 class="headline mb-0">{{ company.company_name }}</h3>
                            <div v-if="company.contact">{{ company.contact }}</div>
                            <div v-else>Контакта нет</div>
                        </div>
                    </v-card-title>
                    <v-card-actions>
                        <v-btn flat block color="success" :to="{ name: 'CompanyHome', params: { slug: company.slug } }">Войти</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>            
        </transition-group>

        <v-layout row v-if="!loading">
            <v-flex xs12 sm12 md12 lg12>
                <h1 class="headline">СТО:</h1>
            </v-flex>
        </v-layout>
        <v-divider></v-divider>

        <transition-group tag="v-layout" class="row wrap" name="fade-transition">       
            <v-flex xs12 sm6 md3 lg3 v-for="sto in stos" :key="sto.id" v-cloak>
                <v-card>
                    <v-card-media height="150px">
                        <v-layout row justify-center align-center>
                        <v-flex xs6 sm6 md6 lg6>
                            <img v-if="sto.logo" :src="`${assetURL}/${sto.logo}`" :alt="`Логотип ${sto.sto_name}`">
                            <img v-else src="/static/images/no-photo.png" alt="Нет логотипа">
                        </v-flex>
                        </v-layout>
                    </v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title>
                        <div>
                            <h3 class="headline mb-0">{{ sto.sto_name }}</h3>
                            <div v-if="sto.contact">{{ sto.contact }}</div>
                            <div v-else>Контакта нет</div>
                        </div>
                    </v-card-title>
                    <v-card-actions>
                        <v-btn flat block color="success" :to="{ name: 'StoHome', params: { slug: sto.slug } }">Войти</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex> 
        </transition-group>    
    </div>
</template>

<script>
import axios from "@/axios"
import config from '@/config'
import Loading from '@/components/Loading'

export default {
    components: {
        Loading  
    },
    computed: {        
        assetURL() {
            return config.assetsURL;
        },

        noProjects() {
            let companies = this.companies;
            let stos = this.stos;
            if(Number(companies.length) + Number(stos.length) > 0)
                return false;
            else             
                return true;
        }
    },
    data() {
        return {
            companies: [],
            stos: [],
            loading: false
        }
    },
    methods: {
        fetchUserProjects() {
            this.loading = true;
            axios.get('/projects')
                .then(response => {
                    this.companies = response.data.companies;
                    this.stos = response.data.stos;
                    this.loading = false;
                })
                .catch(error => console.error());
        }
    },
    created() {
        this.fetchUserProjects();
    }
};
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

