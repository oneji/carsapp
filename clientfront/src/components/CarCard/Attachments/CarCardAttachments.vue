<template>
    <div>
        <v-card class="mb-3">
            <v-card-media>
                <v-container class="py-2">
                    <v-layout align-baseline>
                        <v-flex>
                            <p class="subheading my-0">Вложения</p>
                        </v-flex>
                        <v-flex xs1 sm1 md1 lg1 class="px-0">                        
                            <v-tooltip bottom> 
                                <v-btn slot="activator" color="success" block flat small fab class="my-0" @click.native="newAttachments.dialog = true">
                                    <v-icon>add</v-icon>
                                </v-btn>
                                <span>Добавить вложения</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-media>
            <v-divider></v-divider>
            <v-card-text primary-title>
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="files.length === 0">
                    Вложений нет.
                </v-alert>
                
                <lightbox
                    id="car_attachments"
                    :images="files"
                    :image_class="'card_attachment'"
                    :album_class="'my-album-class'"
                    :options="{ history: false }">
                </lightbox>
            </v-card-text>
        </v-card>

        <!-- Add attachment modal -->
        <v-dialog v-model="newAttachments.dialog" max-width="500">
            <form @submit.prevent="addAttachments" data-vv-scope="add-attachments-type-form">
                <v-card>
                    <v-card-title class="headline">Добавить вложения</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12 sm12 md12 lg12>     
                                <FileUpload @files-changed="getAttachments" :remove-files="newAttachments.removeAll" />
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

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import axios from '@/axios'
import Lightbox from 'vue-simple-lightbox'
import FileUpload from '@/components/FileUpload'
import assetsURL from '@/components/mixins/assets-url'
import snackbar from '@/components/mixins/snackbar'

export default {
    mixins: [snackbar, assetsURL],
    components: {
        Lightbox, FileUpload
    },
    props: {
        files: Array
    },
    data() {
        return {
            attachments: [],
            newAttachments: {
                dialog: false,
                loading: false,
                items: [],
                removeAll: false
            },
        }
    },
    methods: {
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
                            this.files.push({
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
    }
}
</script>

<style>
    .card_attachment {
        margin-right: 5px;
        border: 1px solid #ccc !important;
        padding: 3px;
        width: 75px !important;
        height: 70px !important;
        border-radius: 100%;
        float: none !important;
    }
</style>
