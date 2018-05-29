<template>
    <div>
        <v-layout row wrap>
            <v-flex xs12 sm6 md4 lg4>
                <v-card>
                    <v-form @submit.prevent="createUser">
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

                                            <v-text-field v-model="newDriver.address" name="address" label="Адрес" type="text" prepend-icon="home"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('address')"
                                                data-vv-name="address" data-vv-as='"Адрес"'                                    
                                            ></v-text-field>

                                            <v-text-field v-model="newDriver.email" name="email" label="Email" type="text" prepend-icon="email"
                                                v-validate="'required|email'" 
                                                :error-messages="errors.collect('email')"
                                                data-vv-name="email" data-vv-as='"Email"'                                    
                                            ></v-text-field> 

                                            <v-text-field v-model="newDriver.email" name="phone" label="Телефон" type="text" prepend-icon="phone"
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
                                        <v-btn :loading="loading" color="success" type="submit">Создать</v-btn>
                                        <v-btn color="info">Очистить</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-actions>   
                    </v-form> 
                </v-card>
            </v-flex>

            <v-flex xs12 sm6 md4 lg4>
                <file-pond
                    name="test"
                    ref="pond"
                    class-name="my-pond"
                    label-idle="Кликните или перетащите файлы сюда..."
                    allow-multiple="true"
                    accepted-file-types="image/jpeg, image/png, image/svg+xml"/>
            </v-flex>
        </v-layout>
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

export default {
    $_veeValidate: {
        validator: 'new'
    },
    data() {
        return {
            attachments: [],
            newDriver: {
                fullname: '',
                address: '',
                email: '',
                driver_licence_date: ''
            },
            loading: false,
            date: null,
            menu: false,
        }
    },
    components: {
        FilePond
    },
    methods: {
        getFiles() {
            this.attachments = this.$refs.pond.getFiles();
            let fileList = [];
            this.attachments.map(value => {
                fileList.push(value.file);
            });

            console.log(fileList);

            let formData = new FormData();
            formData.append('test', 'This is a test string!');
            
            for(let i = 0; i < fileList.length; i++) {
                formData.append('attachments[]', fileList[i]);
            }

            axios.post('/company/drivers', formData)
                .then(response => {
                    console.log(response);
                })
                .catch(error => console.log(error));
        },
    },
}
</script>

<style>

</style>
