<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>           
                <v-btn color="primary" append @click.native="createDefectActModal = true">Создать дефектный акт</v-btn>           
            </v-flex>
        </v-layout>

        <v-layout row wrap style="position: relative">
            <loading :loading="loading.pageLoad" />
            
            <!-- Car card -->
            <transition name="fade-transition" mode="out-in">
                <v-flex xs12 sm12 md4 lg3 v-if="!loading.pageLoad" v-cloak>
                    <v-card>
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Общая информация</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>
                        <v-card-media :src="car.cover_image !== null ? assetsURL + '/' + car.cover_image : '/static/images/no-photo.png'" height="200px"></v-card-media>
                        <v-divider></v-divider>
                        <v-card-title primary-title class="pt-3 pb-0">
                            <div>
                                <h3 class="headline mb-0">{{ car.brand_name }} {{ car.model_name }}</h3>
                                <div v-if="car.drivers.length > 0"> 
                                    <div v-for="driver in car.drivers" :key="driver.id">                                    
                                        <span v-if="driver.pivot.active == 1"><strong>Водитель:</strong> {{ driver.fullname }}</span>
                                        <span v-else>dsadas</span>
                                    </div>
                                </div>
                                <div v-else><strong>Водитель:</strong> Водителя нет</div>
                            </div>
                        </v-card-title>
                        <v-card-actions class="pr-0 pl-0">
                            <v-container class="pt-2 pb-2">
                                <v-layout row wrap>
                                    <v-flex>
                                        <div class="car-details-block subheading mb-2">
                                            <i class="ic-speedometer car-icon"></i>
                                            <strong>Пробег:</strong> 
                                            <span v-if="car.milage !== null">{{ car.milage }} км.</span>
                                            <span v-else>Не установлен.</span>
                                        </div>
                                        <div class="car-details-block subheading mb-2">
                                            <i class="ic-car car-icon"></i>
                                            <strong>Vin код:</strong> {{ car.vin_code }}
                                        </div>
                                        <div class="car-details-block subheading mb-2">
                                            <i class="ic-wheel car-icon"></i>
                                            <strong>Гос-номер:</strong> {{ car.number }}
                                        </div>
                                        <div class="car-details-block subheading mb-2">
                                            <i class="ic-engine car-icon"></i>
                                            <strong>Объем двигателя:</strong> 
                                            <span v-if="car.engine_capacity !== null">{{ car.engine_capacity }} л.</span>
                                            <span v-else>Не установлен.</span>
                                        </div>
                                        <div class="car-details-block subheading mb-2">
                                            <i class="ic-fuel car-icon"></i>
                                            <strong>Тип ДВС:</strong> {{ car.engine_type_name }}
                                        </div>
                                        <div class="car-details-block subheading">
                                            <i class="ic-transmission car-icon"></i>
                                            <strong>Трансмиссия:</strong> {{ car.transmission_name }}
                                        </div>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </transition>
            
            <!-- Defect act -->
            <transition name="fade-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg5 v-if="!loading.pageLoad" v-cloak>
                    <defect-act-list v-if="!loading.pageLoad" :items="defectActs" :car="car" />

                    <v-card>
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
                    </v-card>
                </v-flex>            
            </transition>

            <transition name="fade-transition" mode="out-in"> 
                <v-flex xs12 sm12 md6 lg4 v-if="!loading.pageLoad">
                    <v-card>
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Комментарии</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>
                        <v-card-text primary-title class="pt-1 pb-0">
                            <v-list two-line>
                                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="comments.length === 0  && !loading.pageLoad">
                                    Комментариев к автомобилю нет.
                                </v-alert>
                                <template v-for="comment in comments">
                                    <v-list-tile :key="comment.title" avatar>
                                        <v-list-tile-avatar>
                                            <img v-if="comment.user.avatar !== null" :src="assetsURL + '/' + comment.user.avatar">
                                            <img v-else src="/static/images/user.png">
                                        </v-list-tile-avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title v-html="comment.comment"></v-list-tile-title>
                                            <v-list-tile-sub-title v-html="comment.user.fullname + ': ' + comment.created_at"></v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>
                            </v-list>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-container class="py-0 pb-2">
                                <v-layout row wrap>
                                    <v-flex xs12 sm12 md12 lg12>
                                        <v-text-field
                                            name="car_card_comment"
                                            label="Введите комментарий"
                                            multi-line 
                                            clearable
                                            no-resize
                                            v-model="newComment"
                                            rows="3"
                                        ></v-text-field>                                
                                    </v-flex>
                                    <v-flex xs12 sm12 md12 lg12>
                                        <v-btn color="success" block flat @click="storeComment" :loading="loading.comments" class="py-0">Сохранить комментарий</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </transition>
        </v-layout>       


        <!-- Services price -->
        <v-layout row wrap v-if="!loading.pageLoad">
            <v-flex xs12 sm12 md12 lg12>
                <v-btn color="success" append @click="getInvoice">Расчитать примерную цену и услуги</v-btn>      
            </v-flex>
        </v-layout>

        <v-layout row wrap style="position: relative;" v-if="!loading.pageLoad">
            <transition name="fade-transition" mode="out-in">
                <div class="loading-block" v-if="loading.invoice" v-cloak>
                    <v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
                </div>
            </transition>

            <v-flex v-if="!loading.pageLoad && totalSum === 0"> 
                <v-alert outline transition="scale-transition" type="info" :value="true">
                    Сумма и услуги расчитаны не были.
                </v-alert>
            </v-flex>
            
            <!-- Invoice list -->
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

export default {
    mixins: [ snackbar ],
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    components: {
        Lightbox, FileUpload, CreateDefectAct, DefectAct, Loading, DefectActList
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
            newComment: '',
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
                    console.log(response)
                    this.car = response.data.car;
                    this.$store.dispatch('setCar', response.data.car);
                    this.defects = response.data.defects_info;
                    this.$store.dispatch('setDefectTypes', response.data.defects_info);
                    this.comments = this.car.card.comments;
                    this.attachments = this.car.attachments;
                    this.defectActs =  this.car.card.defect_acts;
                    this.equipment = response.data.equipment;
                    this.$store.dispatch('setEquipment', response.data.equipment);

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

        storeComment() {
            this.loading.comments = true;
            axios.post(`/sto/${this.$route.params.slug}/cards/${this.car.card.id}/comments`, { comment: this.newComment })
                .then(response => {
                    this.comments.push(response.data.comment);
                    this.loading.comments = false;
                    this.newComment = '';
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                }) 
                .catch(error => console.log(error));
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
                        console.log(response);
                        response.data.files.map(file => {
                            this.lightboxImages.push({
                                src: this.assetsURL + '/' + file.attachment,
                                title: file.attachment_name
                            });
                        });
                        this.newAttachments.removeAll = true;
                        console.log(this.newAttachments.removeAll)

                        this.newAttachments.loading = false;   
                        this.snackbar.color = 'success';
                        this.snackbar.text = response.data.message;
                        this.snackbar.active = true;                 
                    })
                    .catch(error => console.log(error));
            } else {
                this.snackbar.color = 'success';
                this.snackbar.text = 'Выберите хотя бы одно вложение.';
                this.snackbar.active = true;
            } 
        },

        onDefectActCreated(act) {
            this.defectActs.push(act);
        },
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
        width: 19% !important;
        float: none !important;
    }
</style>
