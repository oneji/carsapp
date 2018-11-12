<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>           
                <!-- <v-btn color="primary" append @click.native="createDefectActModal = true">Создать дефектный акт</v-btn>            -->
                <v-btn color="primary" append :to="{ name: 'StoCreateDefectAct' }">Создать дефектный акт</v-btn>           
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

                    <DoneActList 
                        :items="doneActs"
                        :showAct="true" />
                </v-flex>            
            </transition>

            <transition name="fade-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg4 v-if="!loading.pageLoad">
                    <Comments :items="comments" :card-id="car.card.id" @add="onCommentAdded" />

                    <RTActsList :items="car.card.rt_acts" />
                </v-flex>
            </transition>
        </v-layout>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>

        <!-- <defect-act />

        <create-defect-act v-if="!loading.pageLoad"
            :open-defect-act-modal="createDefectActModal" 
            @close-defect-act-modal="createDefectActModal = false" 
            :selects="selects" 
            :defects="defects"  
            :card-id="car.card.id"
            :equipment="equipment"
            @act-created="onDefectActCreated" /> -->
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
import DoneActList from '@/components/DoneAct/DoneActList'

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
        RTActsList,
        DoneActList
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
            doneActs: [],
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
                    this.doneActs =  this.car.card.done_acts;
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
                            // defect.defect_options.map(option => {
                            //     options.push({
                            //         text: option.defect_option_name,
                            //         value: option.id
                            //     });                                                                       
                            // });
                            // this.selects.defectOptions[defect.id] = options;
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
