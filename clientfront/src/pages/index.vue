<template>
    <div>
        <v-layout row>
            <v-flex xs12 sm12 md12 lg12>
                <h1 class="headline">Выберите проект:</h1>
            </v-flex>
        </v-layout>
        <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">               
            <v-flex xs12 sm6 md3 lg3 v-for="company in companies" :key="company.id" v-cloak>
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
                            <div v-if="company.contact">{{ company.contact }}</div>
                            <div v-else>Контакта нет</div>
                        </div>
                    </v-card-title>
                    <v-card-actions>
                        <v-btn flat color="success" :to="{ name: 'CompanyHome', params: { slug: company.slug } }">Войти</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>

            <v-flex xs12 sm6 md3 lg3 v-for="sto in stos" :key="sto.id" v-cloak>
                <v-card>
                    <v-card-media height="150px">
                        <v-layout row justify-center align-center>
                        <v-flex xs6 sm6 md6 lg6>
                            <img v-if="sto.logo" :src="`${assetURL}/${sto.logo}`" :alt="`Логотип ${sto.sto_name}`">
                            <img v-else src="/static/images/no-logo.png" alt="Нет логотипа">
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
                        <v-btn flat color="success">Войти</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </transition-group>

        <v-layout v-if="noProjects">
            <v-flex>
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Вы пока не привязаны ни к одному проекту.
                </v-alert>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import axios from "@/axios"
import config from '@/config'

export default {
    computed: {
        companies() {
            if(this.$store.getters.user === null)
                return {};
            else
                return this.$store.getters.user.companies
        },

        stos() {
            if(this.$store.getters.user === null)
                return {};
            else
                return this.$store.getters.user.stos
        },
        
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
            
        }
    },
    methods: {

    }
};
</script>
