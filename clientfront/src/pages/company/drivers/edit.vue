<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success" @click="$router.back()">Назад</v-btn>
            </v-flex>
        </v-layout>
        <v-layout style="position: relative">
            <loading :loading="loading.pageLoad" />
        </v-layout>
        <v-layout row wrap v-if="!loading.pageLoad">
            <!-- Old driver photo -->
            <v-flex xs12 sm12 md4 lg3>
                <v-card>
                    <v-card-media 
                        :src="editDriver.photo.url ? assetsURL + '/' + editDriver.photo.url : '/static/images/no-photo.png'" 
                        height="250px">
                    </v-card-media>
                </v-card>                
            </v-flex>
            
            <!-- Driver info -->
            <v-flex xs12 sm12 md4 lg5>
                <v-card>
                    <v-form @submit.prevent="updateDriver">
                        <v-card-media>
                            <v-container>
                                <v-layout>
                                    <v-flex>
                                        <p class="subheading my-0">Информация</p>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                        <v-divider></v-divider>                        
                        <v-card-title>
                            <v-container>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12 md12 lg12 class="v-divider pr-4">                                        
                                        <v-container grid-list-xs>
                                            <v-text-field type="text" v-model="editDriver.fullname" name="fullname" label="ФИО" prepend-icon="person"                 
                                                v-validate="'required'" 
                                                data-vv-name="fullname" data-vv-as='"ФИО"'
                                                :error-messages="errors.collect('fullname')"
                                            ></v-text-field>

                                            <v-text-field v-model="editDriver.address" name="address" label="Адрес" type="text" prepend-icon="home"></v-text-field>

                                            <v-text-field v-model="editDriver.email" name="email" label="Email" type="text" prepend-icon="email"
                                                v-validate="'email'" 
                                                :error-messages="errors.collect('email')"
                                                data-vv-name="email" data-vv-as='"Email"'                                    
                                            ></v-text-field> 

                                            <v-text-field v-model="editDriver.phone" name="phone" label="Телефон" type="text" prepend-icon="phone"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('phone')"
                                                data-vv-name="phone" data-vv-as='"Телефон"'                                    
                                            ></v-text-field> 

                                            <v-menu
                                                ref="menu"
                                                :close-on-content-click="false"
                                                v-model="menu"
                                                :nudge-right="40"
                                                :return-value.sync="date"
                                                lazy
                                                transition="scale-transition"
                                                offset-y
                                                full-width
                                                min-width="290px">
                                                    <v-text-field slot="activator" v-model="date" label="Выберите дату получения прав" prepend-icon="event" readonly></v-text-field>
                                                    <v-date-picker v-model="date" no-title scrollable locale="ru">
                                                        <v-btn flat color="primary" block @click="menu = false">Закрыть</v-btn>
                                                        <v-btn flat color="primary" block @click="$refs.menu.save(date)">OK</v-btn>
                                                    </v-date-picker>
                                            </v-menu>
                                        </v-container>                                        
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-container class="py-1">
                                <v-layout>
                                    <v-flex>
                                        <v-btn :loading="loading.edit" block color="success" type="submit">Изменить</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-actions>   
                    </v-form> 
                </v-card>
            </v-flex>

            <!-- New driver photo -->
            <v-flex xs12 sm12 md4 lg4>
                <v-card>
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0">Новое фото</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <v-card-title primary-title>
                        <v-container>
                            <v-layout row wrap>
                                <v-flex offset-xs2 xs8>
                                    <img v-if="editDriver.newPhoto.url" class="avatar-preview" :src="editDriver.newPhoto.url" height="150" />                                    
                                    <img v-else class="avatar-preview" src="/static/images/no-photo.png" alt="Нет фото">
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-container class="pb-0 pt-3">
                            <v-layout row wrap>
                                <v-text-field label="Выберите фото" @click="pickFile" v-model="editDriver.newPhoto.name" 
                                    prepend-icon="attach_file" append-icon="delete" :append-icon-cb="deletePhoto"
                                ></v-text-field>
                                <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                            </v-layout>
                        </v-container>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js'
import FilepondPluginImagePreview from 'filepond-plugin-image-preview';

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilepondPluginImagePreview)

import axios from '@/axios'
import config from '@/config'
import snackbar from '@/components/mixins/snackbar'
import Loading from '@/components/Loading'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },    
    components: {
        FilePond, Loading
    },
    mixins: [ snackbar ],
    data() {
        return {
            attachments: [],
            editDriver: {
                fullname: '',
                address: '',
                email: '',
                driver_license_date: null,
                photo: {
                    name: '',
                    file: '',
                    url: ''
                },
                newPhoto: {
                    name: '',
                    file: '',
                    url: ''
                },
            },
            newDriver: {
                fullname: '',
                address: '',
                email: '',
                driver_license_date: null,
                photo: {
                    name: '',
                    file: '',
                    url: ''
                },
            },
            loading: {
                edit: false,
                pageLoad: false
            },
            date: null,
            menu: false,
        }
    },
    methods: {
        editInfo() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.company}/drivers/${this.$route.params.driver}/edit`)
                .then(response => {
                    this.editDriver.fullname = response.data.fullname;
                    this.editDriver.address = response.data.address;
                    this.editDriver.email = response.data.email;
                    this.editDriver.phone = response.data.phone;
                    this.editDriver.driver_license_date = response.data.driver_license_date;
                    this.date = response.data.driver_license_date;
                    this.editDriver.photo.name = response.data.photo;
                    this.editDriver.photo.url = response.data.photo;
                    this.loading.pageLoad = false;
                })
                .catch(error => console.log(error));
        },
        
        updateDriver() {
            this.$validator.validateAll()
                .then(success => {
                    if(success) {                    
                        this.loading.edit = true;

                        let formData = new FormData();
                        formData.append('fullname', this.editDriver.fullname);
                        formData.append('address', this.editDriver.address);
                        formData.append('email', this.editDriver.email);
                        formData.append('phone', this.editDriver.phone);
                        formData.append('driver_license_date', this.date);
                        formData.append('photo', this.editDriver.newPhoto.file);

                        axios.post(`/company/${this.$route.params.slug}/drivers/${this.$route.params.driver}/update`, formData)
                            .then(response => {
                                console.log(response);
                                this.loading.edit = false;
                                this.snackbar.color = 'success';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
                            })
                            .catch(error => console.log(error));
                }
            });
        },

        pickFile () {
            this.$refs.image.click();
        },

        onFilePicked (e) {
            const files = e.target.files;

            if(files[0] !== undefined) {
                this.editDriver.newPhoto.name = files[0].name;

                if(this.editDriver.newPhoto.name.lastIndexOf('.') <= 0) {
                    return
                }

                const fr = new FileReader();
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.editDriver.newPhoto.url = fr.result;
                    this.editDriver.newPhoto.file = files[0];
                })
            } else {
                this.editDriver.newPhoto.url = '';
                this.editDriver.newPhoto.name = '';
                this.editDriver.newPhoto.file = '';
            }
        },

        deletePhoto() {
            this.editDriver.newPhoto.url = '';
            this.editDriver.newPhoto.name = '';
            this.editDriver.newPhoto.file = '';
        },
    },
    created() {
        this.editInfo();
    }
}
</script>

<style>
    .avatar-preview {
        display: block;
        margin: 0 auto;
        width: 100%;
    }
</style>
