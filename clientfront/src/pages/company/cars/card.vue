<template>
    <div>
        <MoveButtons />
        <v-layout row wrap style="position: relative">
            <Loading :loading="loading.pageLoad" />
            
            <!-- Car info -->
            <transition name="slide-x-transition" mode="out-in">
                <v-flex xs12 sm12 md4 lg3 v-if="!loading.pageLoad" v-cloak>
                    <Car :item="car" :expanded="true" />
                </v-flex>
            </transition>
            
            <!-- Defect act, attachments and fines -->
            <transition name="slide-x-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg5 v-if="!loading.pageLoad" v-cloak>
                    <DefectActList v-if="!loading.pageLoad" :items="defectActs" :show-defect="false" />

                    <Attachments :files="lightboxImages" />  

                    <Fines :items="fines" />
                </v-flex>            
            </transition>

            <!-- Comments -->
            <transition name="slide-x-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg4 v-if="!loading.pageLoad">
                    <Comments :items="comments" @add="onCommentAdded" />
                </v-flex>
            </transition>
        </v-layout>

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
import assetsURL from '@/components/mixins/assets-url'

import Loading from '@/components/Loading'
import FileUpload from '@/components/FileUpload'
import CreateDefectAct from '@/components/CarCard/Defect/CreateDefectAct'
import DefectAct from '@/components/CarCard/Defect/DefectAct'
import DefectActList from '@/components/CarCard/Defect/DefectActList'
import Car from '@/components/Car'
import MoveButtons from '@/components/MoveButtons'
import Comments from '@/components/CarCard/Comments/CarCardComments'
import Attachments from '@/components/CarCard/Attachments/CarCardAttachments'
import Fines from '@/components/CarCard/Fines/CarCardFine'

export default {
    mixins: [ snackbar, assetsURL ],
    components: {
        FileUpload, CreateDefectAct, DefectAct, Loading, DefectActList, Car, MoveButtons, Comments, Attachments, Fines
    },
    data() {
        return {
            selectedServices: [],
            invoice: [],
            totalSum: 0,
            car: {},
            loading: {
                pageLoad: false,
                saveDefects: false,
                invoice: false,
                comments: false
            },
            active: null,
            active_2: null,
            tabs: [
                { text: 'Дефектный акт' },
                { text: 'Ремонтные работы' },
            ],
            defects: [],
            comments: [],
            defectActs: [],
            attachments: [],
            fines: [],
            equipment: [],
            selects: {
                defects: [],
                defectOptions: []
            },
            selected: {
                defects: null,
                defectOptions: []
            },

            lightboxImages: [],
            newAttachments: {
                dialog: false,
                loading: false,
                items: [],
                removeAll: false
            },
            createDefectActModal: false
        }
    },
    methods: {
        fetchCarCardInfo() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.slug}/cars/${this.$route.params.car}/card`)
                .then(response => {
                    this.car = response.data.car;
                    this.$store.dispatch('setCar', response.data.car);
                    this.$store.dispatch('setDefectTypes', response.data.defects_info);
                    this.comments = this.car.card.comments;
                    this.attachments = this.car.attachments;
                    this.fines = this.car.card.fines;
                    this.defectActs =  this.car.card.defect_acts;

                    this.attachments.map(file => {
                        this.lightboxImages.push({
                            src: this.assetsURL + '/' + file.attachment,
                            title: file.attachment_name
                        });
                    });
                    
                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },        

        getInvoice() {
            this.loading.invoice = true;
            axios.post(`/sto/${this.$route.params.slug}/services/invoice`, { 'defect_options': this.selected.defectOptions })
                .then(response => {
                    this.selectedServices = [];
                    this.invoice = response.data;
                    this.invoice.map((item, index) => {
                        this.selectedServices.push(index);
                        this.getTotal();
                    });
                    this.loading.invoice = false;
                })
                .catch(error => console.log(error));

            
        },

        toggle(index) {
            const i = this.selectedServices.indexOf(index)

            if (i > -1)
                this.selectedServices.splice(i, 1);
            else
                this.selectedServices.push(index);            

            this.getTotal();
        },

        getTotal() {
            this.totalSum = 0;
            this.selectedServices.map(serviceIndex => {
                this.invoice.map((item, index) => {
                    if(index === serviceIndex) {
                        this.totalSum += item.service_price;
                    }
                });
            });
        },

        onDefectActCreated(act) {
            this.defectActs.push(act);
        },

        onCommentAdded(response) {
            this.comments.push(response.comment);
            this.successSnackbar(response.message);       
        } 
    },
    created() {
        this.fetchCarCardInfo();
    }
}
</script>

<style>
    
</style>
