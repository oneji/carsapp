<template>
    <div>
        <MoveButtons />
        <v-layout row wrap>
            <v-flex xs12 sm12 md4 lg3>
                <v-card>
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0">Фото</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <v-card-title primary-title>
                        <v-container>
                            <v-layout row wrap justify-center>
                                <v-flex xs6>
                                    <img v-if="newDriver.photo.url" class="avatar-preview" :src="newDriver.photo.url" height="150" />                                    
                                    <img v-else class="avatar-preview" src="/static/images/no-photo.png" alt="Нет фото">
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-container class="pb-0 pt-3">
                            <v-layout row wrap>
                                <v-text-field label="Выберите фото" @click="pickFile" v-model="newDriver.photo.name" 
                                    prepend-icon="attach_file" append-icon="delete" :append-icon-cb="deletePhoto"
                                ></v-text-field>
                                <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                            </v-layout>
                        </v-container>
                    </v-card-actions>
                </v-card>
            </v-flex>

            <v-flex xs12 sm12 md4 lg5>
                <v-card>
                    <v-form @submit.prevent="createDriver">
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
                                            <v-text-field type="text" v-model="newDriver.fullname" name="fullname" label="ФИО" prepend-icon="person"                 
                                                v-validate="'required'" 
                                                data-vv-name="fullname" data-vv-as='"ФИО"'
                                                :error-messages="errors.collect('fullname')"
                                            ></v-text-field>

                                            <v-text-field v-model="newDriver.address" name="address" label="Адрес" type="text" prepend-icon="home"></v-text-field>

                                            <v-text-field v-model="newDriver.email" name="email" label="Email" type="text" prepend-icon="email"
                                                v-validate="'email'" 
                                                :error-messages="errors.collect('email')"
                                                data-vv-name="email" data-vv-as='"Email"'                                    
                                            ></v-text-field> 

                                            <v-text-field v-model="newDriver.phone" name="phone" label="Телефон" type="text" prepend-icon="phone"
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

                                            <v-text-field 
                                                v-model="newDriver.driver_license_category" 
                                                name="driver_license_category" label="Введите категории прав через запятую" type="text" 
                                                prepend-icon="spellcheck"
                                                hint="Например: B, C"
                                            ></v-text-field> 
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
                                        <v-btn :loading="loading" block color="success" type="submit">Создать</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-actions>   
                    </v-form> 
                </v-card>
            </v-flex>

            <v-flex xs12 sm12 md4 lg4>
                <v-card>
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0">Файлы | Прикрепления</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <file-pond
                        name="test"
                        ref="pond"
                        class-name="my-pond"
                        label-idle="Кликните или перетащите файлы сюда..."
                        allow-multiple="true"
                        accepted-file-types="image/jpeg, image/png, image/svg+xml"/>
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
import snackbar from '@/components/mixins/snackbar'
import MoveButtons from '@/components/MoveButtons'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    data() {
        return {
            attachments: [],
            newDriver: {
                fullname: '',
                address: '',
                email: '',
                driver_license_date: null,
                driver_license_category: '',
                photo: {
                    name: '',
                    file: '',
                    url: ''
                },
            },
            loading: false,
            date: null,
            menu: false,
        }
    },
    components: {
        FilePond, MoveButtons
    },
    methods: {
        createDriver() {
            this.$validator.validateAll()
                .then(success => {
                    if(success) {                    
                        this.loading = true;
                        this.attachments = this.$refs.pond.getFiles();
                        let fileList = [];
                        this.attachments.map(value => {
                            fileList.push(value.file);
                        });

                        let formData = new FormData();
                        formData.append('fullname', this.newDriver.fullname);
                        formData.append('address', this.newDriver.address);
                        formData.append('email', this.newDriver.email);
                        formData.append('phone', this.newDriver.phone);
                        formData.append('driver_license_date', this.date);
                        formData.append('driver_license_category', this.newDriver.driver_license_category);
                        formData.append('photo', this.newDriver.photo.file);
                        
                        for(let i = 0; i < fileList.length; i++) {
                            formData.append('attachments[]', fileList[i]);
                        }

                        axios.post(`/company/${this.$route.params.slug}/drivers`, formData)
                            .then(response => {
                                console.log(response);
                                this.newDriver.fullname = '';
                                this.newDriver.address = '';
                                this.newDriver.email = '';
                                this.newDriver.phone = '';
                                this.date = '';
                                this.newDriver.photo.url = '';
                                this.newDriver.photo.file = '';
                                this.newDriver.photo.name = '';

                                this.loading = false;
                                this.successSnackbar(response.data.message);
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
                this.newDriver.photo.name = files[0].name;

                if(this.newDriver.photo.name.lastIndexOf('.') <= 0) {
                    return
                }

                const fr = new FileReader();
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.newDriver.photo.url = fr.result;
                    this.newDriver.photo.file = files[0];
                })
            } else {
                this.newDriver.photo.url = '';
                this.newDriver.photo.name = '';
                this.newDriver.photo.file = '';
            }
        },

        deletePhoto() {
            this.newDriver.photo.url = '';
            this.newDriver.photo.name = '';
            this.newDriver.photo.file = '';
        },
    },
}
</script>

<style>
    
</style>
