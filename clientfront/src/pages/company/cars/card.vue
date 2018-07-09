<template>
    <div>
        <move-buttons />
        <v-layout row wrap style="position: relative">
            <loading :loading="loading.pageLoad" />
            
            <!-- Car info -->
            <transition name="slide-x-transition" mode="out-in">
                <v-flex xs12 sm12 md4 lg3 v-if="!loading.pageLoad" v-cloak>
                    <car :item="car" :expanded="true" />
                </v-flex>
            </transition>
            
            <!-- Defect act and attachments -->
            <transition name="slide-x-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg5 v-if="!loading.pageLoad" v-cloak>
                    <defect-act-list v-if="!loading.pageLoad" :items="defectActs" :show-defect="false" />

                    <attachments :files="lightboxImages" />
                    <!-- <v-card>
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Вложения</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>
                        <v-card-text primary-title>
                            <v-alert outline transition="scale-transition" type="info" :value="true" v-if="attachments.length === 0 && !loading.pageLoad">
                                Вложений нет.
                            </v-alert>
                            
                            <lightbox
                                id="car_attachments"
                                :images="lightboxImages"
                                :image_class="'card_attachment'"
                                :album_class="'my-album-class'"
                                :options="{ history: false }">
                            </lightbox>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn color="success" block flat class="py-0" @click.native="newAttachments.dialog = true">Добавить вложения</v-btn>
                        </v-card-actions>
                    </v-card> -->
                </v-flex>            
            </transition>

            <!-- Comments -->
            <transition name="slide-x-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg4 v-if="!loading.pageLoad">
                    <comments :items="comments" :card-id="car.card.id" @add="onCommentAdded" />
                </v-flex>
            </transition>
        </v-layout>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>

        <!-- Add attachment modal -->
        <v-dialog v-model="newAttachments.dialog" max-width="500">
            <form @submit.prevent="addAttachments" data-vv-scope="add-attachments-type-form">
                <v-card>
                    <v-card-title class="headline">Добавить вложения</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12 sm12 md12 lg12>     
                                <file-upload @files-changed="getAttachments" :remove-files="newAttachments.removeAll" />
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="newAttachments.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="newAttachments.loading" flat="flat" type="submit">Добавить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
    </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import snackbar from '@/components/mixins/snackbar'

import Loading from '@/components/Loading'
import FileUpload from '@/components/FileUpload'
import CreateDefectAct from '@/components/CarCard/Defect/CreateDefectAct'
import DefectAct from '@/components/CarCard/Defect/DefectAct'
import DefectActList from '@/components/CarCard/Defect/DefectActList'
import Car from '@/components/Car'
import MoveButtons from '@/components/MoveButtons'
import Comments from '@/components/CarCard/Comments/CarCardComments'
import Attachments from '@/components/CarCard/Attachments/CarCardAttachments'

export default {
    mixins: [ snackbar ],
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    components: {
        FileUpload, CreateDefectAct, DefectAct, Loading, DefectActList, Car, MoveButtons, Comments, Attachments
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
        
        getAttachments(file) {
            this.attachments.items = file;
        },

        addAttachments() {
            if(this.attachments.items !== undefined) {  
                this.newAttachments.loading = true;             
    
                let formData = new FormData();
                let fileList = [];
    
                this.attachments.items.map(value => {
                    fileList.push(value.file);
                });
                
                for(let i = 0; i < fileList.length; i++) {
                    formData.append('attachments[]', fileList[i]);
                }
                
                axios.post(`/sto/${this.$route.params.slug}/cars/${this.$route.params.car}/attachments`, formData)
                    .then(response => {
                        response.data.files.map(file => {
                            this.lightboxImages.push({
                                src: this.assetsURL + '/' + file.attachment,
                                title: file.attachment_name
                            });
                        });
                        this.newAttachments.loading = false;  
                        this.newAttachments.removeAll = true;                        

                        this.successSnackbar(response.data.message);       
                    })
                    .catch(error => console.log(error));
            } else {
                this.warningSnackbar('Выберите хотя бы одно вложение!');
            } 
            
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
    .car-icon {
        font-size: 150%;
        margin-right: 8px;
    }
    .car-details-block {
        display: flex;
        justify-content: start;
        align-items: center;
    }
    .car-details-block strong {
        margin-right: 5px;
    }
    .card_attachment {
        margin-right: 5px;
        border: 1px solid #ccc !important;
        padding: 3px;
        width: 80px !important;
        height: 80px !important;
        border-radius: 100%;
        float: none !important;
    }
</style>
