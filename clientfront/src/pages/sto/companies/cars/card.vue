<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>           
                <v-btn color="primary" append @click.native="createDefectActModal = true">Создать дефектный акт</v-btn>           
                <v-btn color="success" append :to="{ name: 'StoCreateRTAct' }">Создать акт приёма передачи</v-btn>           
            </v-flex>
        </v-layout>

        <v-layout row wrap style="position: relative">
            <Loading :loading="loading.pageLoad" />
            
            <!-- Car card -->
            <transition name="fade-transition" mode="out-in">
                <v-flex xs12 sm12 md4 lg3 v-if="!loading.pageLoad">
                    <Car 
                        :item="car" 
                        :expanded="true"
                        :actions="false" />
                </v-flex>
            </transition>
            
            <!-- Defect act -->
            <transition name="fade-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg5 v-if="!loading.pageLoad">
                    <ConsumablesList 
                        :items="consumables" 
                        :private-items="privateConsumables" 
                        @add="onConsumableAdded"
                        @change="onConsumableChanged" />                    
                    
                    <Attachments :files="lightboxImages" />

                    <DefectActList 
                        v-if="!loading.pageLoad" 
                        :items="defectActs" 
                        :car="car" class="mt-3" />
                </v-flex>            
            </transition>

            <transition name="fade-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg4 v-if="!loading.pageLoad">
                    <Comments :items="comments" :card-id="car.card.id" @add="onCommentAdded" />

                    <RTActsList :items="car.card.rt_acts" />
                </v-flex>
            </transition>
        </v-layout>       


        <!-- Services price -->
        <!-- <v-layout row wrap v-if="!loading.pageLoad">
            <v-flex xs12 sm12 md12 lg12>
                <v-btn color="success" append @click="getInvoice">Расчитать примерную цену и услуги</v-btn>      
            </v-flex>
        </v-layout>

        <v-layout row wrap style="position: relative;" v-if="!loading.pageLoad">
            <Loading :loading="loading.invoice" />

            <v-flex v-if="!loading.pageLoad && totalSum === 0"> 
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Сумма и услуги расчитаны не были.
                </v-alert>
            </v-flex>
            
            <v-flex xs12 sm12 md12 lg12 v-if="invoice.length > 0">         
                <v-card>
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0" style="float: left;">Примерная цена на услуги</p>
                                    <p class="title my-0" style="float: right;"><strong>Итого:</strong> <span class="red--text">{{ totalSum }} сомони</span></p>   
                                </v-flex>                                
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <v-list two-line>
                        <template v-for="(item, index) in invoice">
                            <v-list-tile :key="item.title" avatar ripple @click="toggle(index)">
                                <v-list-tile-content>
                                    <v-list-tile-title>{{ item.service_type_name }}</v-list-tile-title>
                                    <v-list-tile-sub-title>Категория: {{ item.service_category_name }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                                <v-list-tile-action>
                                    <v-list-tile-action-text class="body-2">{{ item.service_price }} сом.</v-list-tile-action-text>
                                    <v-icon v-if="selectedServices.indexOf(index) < 0" color="grey lighten-1">star_border</v-icon>
                                    <v-icon v-else color="yellow darken-2">star</v-icon>
                                </v-list-tile-action>
                            </v-list-tile>
                            <v-divider v-if="index + 1 < invoice.length" :key="index"></v-divider>
                        </template>
                    </v-list>
                </v-card>
            </v-flex>
        </v-layout> -->

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>

        <defect-act />

        <create-defect-act v-if="!loading.pageLoad"
            :open-defect-act-modal="createDefectActModal" 
            @close-defect-act-modal="createDefectActModal = false" 
            :selects="selects" 
            :defects="defects"  
            :card-id="car.card.id"
            :equipment="equipment"
            @act-created="onDefectActCreated" />
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import Loading from '@/components/Loading'
import snackbar from '@/components/mixins/snackbar'
import Lightbox from 'vue-simple-lightbox'
import FileUpload from '@/components/FileUpload'
import CreateDefectAct from '@/components/CarCard/Defect/CreateDefectAct'
import DefectAct from '@/components/CarCard/Defect/DefectAct'
import DefectActList from '@/components/CarCard/Defect/DefectActList'
import Comments from '@/components/CarCard/Comments/CarCardComments'
import Car from '@/components/Car'
import Attachments from '@/components/CarCard/Attachments/CarCardAttachments'
import ConsumablesList from '@/components/CarCard/Consumables/ConsumablesList'
import RTActsList from '@/components/CarCard/ReceiveTransferAct/ReceiveTransferActList'

export default {
    mixins: [ snackbar ],
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    components: {
        Lightbox, 
        FileUpload, 
        CreateDefectAct, 
        DefectAct, 
        Loading, 
        DefectActList, 
        Comments, 
        Car, 
        Attachments,
        ConsumablesList,
        RTActsList
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
            equipment: [],
            consumables: [],
            privateConsumables: [],
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
            axios.get(`/sto/${this.$route.params.slug}/cars/${this.$route.params.car}/card`)
                .then(response => {
                    this.car = response.data.car;
                    // Vuex actions
                    this.$store.dispatch('setCar', response.data.car);
                    this.$store.dispatch('setDefectTypes', response.data.defects_info);
                    this.$store.dispatch('setEquipment', response.data.equipment);
                    
                    this.defects = response.data.defects_info;
                    this.comments = this.car.card.comments;
                    this.attachments = this.car.attachments;
                    this.defectActs =  this.car.card.defect_acts;
                    this.equipment = response.data.equipment;

                    this.attachments.map(file => {
                        this.lightboxImages.push({
                            src: this.assetsURL + '/' + file.attachment,
                            title: file.attachment_name
                        });
                    });

                    this.defects.map(item => {            
                        let defects = [];
                        
                        item.defects.map(defect => {                      
                            defects.push({
                                text: defect.defect_name,
                                value: defect.id
                            });
                            
                            let options = [];
                            defect.defect_options.map(option => {
                                options.push({
                                    text: option.defect_option_name,
                                    value: option.id
                                });                                                                       
                            });
                            this.selects.defectOptions[defect.id] = options;
                        });
                        this.selects.defects.push(defects);                      
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
            this.createDefectActModal = false;
        },
        
        onCommentAdded(response) {
            this.comments.push(response.comment);
            this.successSnackbar(response.message);       
        },

        fetchConsumables() {
            axios.get(`/car/${this.$route.params.car}/consumables`)
                .then(response => {
                    this.consumables = response.data.consumables;
                    this.privateConsumables = response.data.data.consumables;
                })
                .catch(error => console.log(error));
        },
        
        onConsumableAdded(response) {
            this.consumables.push(response.consumable);
            this.successSnackbar(response.message);
        },

        onConsumableChanged(response) {
            this.successSnackbar(response.message);
            this.fetchConsumables();
        }
    },
    created() {
        this.fetchCarCardInfo();
        this.fetchConsumables();
    }
}
</script>

<style>

</style>
