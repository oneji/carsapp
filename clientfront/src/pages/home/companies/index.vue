<template>
    <div>
        <v-layout>
            <v-flex v-if="noProjects && !loading">
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Вы пока не привязаны ни к одному проекту.
                </v-alert>
            </v-flex>
        </v-layout>
        <!-- Companies heading -->
        <v-layout row v-if="!noProjects && !loading && companies.length > 0">
            <v-flex xs12 sm12 md12 lg12>
                <v-btn block outline color="primary">Компании</v-btn>
            </v-flex>
        </v-layout>    
        <v-layout style="position: relative;">
            <Loading :loading="loading" />
        </v-layout>    
        <!-- A list of companies -->
        <transition-group tag="v-layout" class="row wrap" name="fade-transition">                        
            <v-flex xs12 sm6 md3 lg3 v-for="company in companies" :key="company.id" v-cloak>
                <v-card>
                    <v-card-media height="150px">
                        <v-layout row justify-center align-center>
                        <v-flex xs6 sm6 md6 lg6>
                            <img v-if="company.logo" :src="`${assetsURL}/${company.logo}`" :alt="`Логотип ${company.company_name}`">
                            <img v-else src="/static/images/no-photo.png" alt="Нет логотипа">
                        </v-flex>
                        </v-layout>
                    </v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title>
                        <div>
                            <h3 class="subheading mb-0">{{ company.company_name }}</h3>
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
        <!-- STOs heading -->        
        <v-layout row v-if="!noProjects && !loading && stos.length > 0">
            <v-flex xs12 sm12 md12 lg12>
                <v-btn block outline color="primary">СТО</v-btn>
            </v-flex>
        </v-layout>
        <!-- A list of STOs -->
        <transition-group tag="v-layout" class="row wrap" name="fade-transition">       
            <v-flex xs12 sm6 md3 lg3 v-for="sto in stos" :key="sto.id" v-cloak>
                <v-card>
                    <v-card-media height="150px">
                        <v-layout row justify-center align-center>
                        <v-flex xs6 sm6 md6 lg6>
                            <img v-if="sto.logo" :src="`${assetsURL}/${sto.logo}`" :alt="`Логотип ${sto.sto_name}`">
                            <img v-else src="/static/images/no-photo.png" alt="Нет логотипа">
                        </v-flex>
                        </v-layout>
                    </v-card-media> 
                    <v-divider></v-divider>           
                    <v-card-title primary-title>
                        <div>
                            <h3 class="subheading mb-0">{{ sto.sto_name }}</h3>
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
import assetsURL from '@/components/mixins/assets-url'

export default {
    mixins: [assetsURL],
    components: {
        Loading  
    },
    computed: {  
        noProjects() {
            if(Number(this.companies.length) + Number(this.stos.length) > 0)
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

</style>

