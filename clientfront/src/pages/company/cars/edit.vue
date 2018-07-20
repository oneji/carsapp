<template>
    <div>
        <MoveButtons />
        
        <v-layout style="position: relative;">
            <Loading :loading="loading.pageLoad" />
        </v-layout>
        <v-layout row wrap v-if="!loading.pageLoad">
            <!-- Main photo -->
            <v-flex xs12 sm12 md4 lg3>
                <v-card class="mb-3">
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0">Главное фото</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <v-card-media :src="editCar.cover_image.url ? assetsURL + '/' + editCar.cover_image.url : '/static/images/no-photo.png'" height="200px"></v-card-media>
                </v-card>

                 <v-card>
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0">Новое главное фото</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <v-card-media :src="editCar.newCover_image.url ? editCar.newCover_image.url : '/static/images/no-photo.png'" height="200px"></v-card-media>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-container class="pb-0 pt-3">
                            <v-layout row wrap>
                                <v-text-field label="Выберите главное фото" @click="pickFile" v-model="editCar.newCover_image.name" 
                                    prepend-icon="attach_file" append-icon="delete" :append-icon-cb="deleteCoverImage"
                                ></v-text-field>
                                <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                            </v-layout>
                        </v-container>
                    </v-card-actions>
                </v-card>
            </v-flex>
            <!-- Info -->
            <v-flex xs12 sm12 md4 lg5>
                <v-card>
                    <v-form @submit.prevent="editCarInfo">
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
                                            <v-text-field type="text" v-model="editCar.year" name="year" label="Год машины" prepend-icon="event"                 
                                                v-validate="'required'" 
                                                data-vv-name="year" data-vv-as='"Гос номер"'
                                                :error-messages="errors.collect('year')"
                                            ></v-text-field>

                                            <v-text-field type="text" v-model="editCar.number" name="number" label="Гос-номер машины" prepend-icon="filter_2"                 
                                                v-validate="'required'" 
                                                data-vv-name="number" data-vv-as='"Гос номер"'
                                                :error-messages="errors.collect('number')"
                                            ></v-text-field>

                                            <v-select autocomplete :items="shapes" v-model="editCar.shape_id" label="Выберите кузов" prepend-icon="category"
                                                name="shape_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('shape_id')"
                                                data-vv-name="shape_id" data-vv-as='"Кузов"'
                                            ></v-select>

                                            <v-select autocomplete :items="brands" v-model="editCar.brand_id" label="Выберите марку" prepend-icon="directions_car"
                                                name="brand_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('brand_id')"
                                                data-vv-name="brand_id" data-vv-as='"Марка"'
                                            ></v-select>

                                            <v-select autocomplete :items="models" v-model="editCar.model_id" label="Выберите модель" prepend-icon="directions_car"
                                                name="model_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('model_id')"
                                                data-vv-name="model_id" data-vv-as='"Модель"'
                                            ></v-select>

                                            <v-text-field v-model="editCar.milage" name="milage" label="Пробег" type="text" prepend-icon="swap_calls" suffix="км."                                    
                                            ></v-text-field>

                                            <v-text-field v-model="editCar.vin_code" name="vin_code" label="Вин код" type="text" prepend-icon="polymer"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('vin_code')"
                                                data-vv-name="vin_code" data-vv-as='"Вин код"'                                    
                                            ></v-text-field>  

                                            <v-text-field v-model="editCar.engine_capacity" name="engine_capacity" label="Объём двигателя" type="text" prepend-icon="polymer"                                  
                                            ></v-text-field> 

                                            <v-select :items="engine_types" v-model="editCar.engine_type_id" label="Выберите тип двигателя" prepend-icon="directions_car"
                                                name="engine_type_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('engine_type_id')"
                                                data-vv-name="engine_type_id" data-vv-as='"Тип двигателя"'
                                            ></v-select>   

                                            <v-select :items="transmissions" v-model="editCar.transmission_id" label="Выберите КПП" prepend-icon="directions_car"
                                                name="transmission_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('transmission_id')"
                                                data-vv-name="transmission_id" data-vv-as='"Коробка передач"'
                                            ></v-select>  

                                            <v-menu
                                                ref="menu2"
                                                :close-on-content-click="false"
                                                v-model="menu2"
                                                :nudge-right="40"
                                                :return-value.sync="editCar.teh_osmotr_end_date"
                                                lazy
                                                transition="scale-transition"
                                                offset-y
                                                full-width
                                                min-width="290px">
                                                    <v-text-field slot="activator" v-model="editCar.teh_osmotr_end_date" label="Выберите дату окончания тех осмотра" prepend-icon="event" readonly></v-text-field>
                                                    <v-date-picker v-model="editCar.teh_osmotr_end_date" no-title scrollable locale="ru">
                                                        <v-btn flat color="primary" block @click="menu2 = false">Закрыть</v-btn>
                                                        <v-btn flat color="primary" block @click="$refs.menu2.save(editCar.teh_osmotr_end_date)">OK</v-btn>
                                                    </v-date-picker>
                                            </v-menu>

                                            <v-menu
                                                ref="menu3"
                                                :close-on-content-click="false"
                                                v-model="menu3"
                                                :nudge-right="40"
                                                :return-value.sync="editCar.tint_end_date"
                                                lazy
                                                transition="scale-transition"
                                                offset-y
                                                full-width
                                                min-width="290px">
                                                    <v-text-field slot="activator" v-model="editCar.tint_end_date" label="Выберите дату окончания тонировки" prepend-icon="event" readonly></v-text-field>
                                                    <v-date-picker v-model="editCar.tint_end_date" no-title scrollable locale="ru">
                                                        <v-btn flat color="primary" block @click="menu3 = false">Закрыть</v-btn>
                                                        <v-btn flat color="primary" block @click="$refs.menu3.save(editCar.tint_end_date)">OK</v-btn>
                                                    </v-date-picker>
                                            </v-menu>

                                            <v-checkbox label="В резерв" v-model="editCar.reserved"></v-checkbox>   
                                            <v-radio-group v-model="editCar.type" row class="pt-0">
                                                <v-radio label="Служебная" :value="0"></v-radio>
                                                <v-radio label="Служебно-Личная" :value="1"></v-radio>
                                            </v-radio-group>                                       
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
                                        <v-btn block :loading="loading.edit" color="success" type="submit">Изменить</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-actions>   
                    </v-form> 
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
import MoveButtons from '@/components/MoveButtons'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    components: {
        Loading, FilePond, MoveButtons
    },
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    data() {
        return {
            attachments: [],
            editCar: {
                year: null,
                number: '',
                shape_id: null,
                brand_id: null,
                model_id: null,
                milage: null,
                vin_code: '',
                cover_image: {
                    name: '',
                    file: '',
                    url: ''
                },
                newCover_image: {
                    name: '',
                    file: '',
                    url: ''
                },
                engine_capacity: '',
                engine_type_id: null,
                transmission_id: null,
                reserved: false,
                type: '',
                teh_osmotr_end_date: '',
                tint_end_date: ''
            },
            newCar: {
                year: null,
                number: '',
                shape_id: null,
                brand_id: null,
                model_id: null,
                milage: null,
                vin_code: '',
                cover_image: {
                    name: '',
                    file: '',
                    url: ''
                },
                engine_capacity: '',
                engine_type_id: null,
                transmission_id: null,
                reserved: false,
                type: 0 
            },
            loading: {
                edit: false,
                pageLoad: false
            },
            year: null,
            menu: false,
            menu2: false,
            menu3: false,
            shapes: [], 
            brands: [], 
            models: [],
            engine_types: [],
            transmissions: []
        }
    },
    methods: {
        editInfo() {
            this.loading.pageLoad = true;
            axios.get(`/company/${this.$route.params.company}/cars/${this.$route.params.car}/edit`)
                .then(response => {
                    this.editCar.year = response.data.year;
                    this.editCar.number = response.data.number;
                    this.editCar.shape_id = response.data.shape_id;
                    this.editCar.brand_id = response.data.brand_id;
                    this.editCar.model_id = response.data.model_id;
                    this.editCar.milage = response.data.milage;
                    this.editCar.vin_code = response.data.vin_code;
                    this.editCar.cover_image.name = response.data.cover_image;
                    this.editCar.cover_image.url = response.data.cover_image;
                    this.editCar.engine_capacity = response.data.engine_capacity;
                    this.editCar.engine_type_id = response.data.engine_type_id;
                    this.editCar.transmission_id = response.data.transmission_id;
                    this.editCar.reserved = !!+response.data.reserved;
                    this.editCar.type = response.data.type;
                    this.editCar.teh_osmotr_end_date = response.data.teh_osmotr_end_date;
                    this.editCar.tint_end_date = response.data.tint_end_date;
                    this.loading.pageLoad = false;  
                })
                .catch(error => console.log(error));
        },

        editCarInfo() {
            this.$validator.validateAll()
                .then(success => {
                    if(success) {                    
                        this.loading.edit = true;

                        let formData = new FormData();
                        formData.append('year', this.editCar.year);
                        formData.append('number', this.editCar.number);
                        formData.append('shape_id', this.editCar.shape_id);
                        formData.append('brand_id', this.editCar.brand_id);
                        formData.append('model_id', this.editCar.model_id);
                        formData.append('milage', this.editCar.milage);
                        formData.append('vin_code', this.editCar.vin_code);
                        formData.append('cover_image', this.editCar.newCover_image.file);
                        formData.append('engine_capacity', this.editCar.engine_capacity);
                        formData.append('engine_type_id', this.editCar.engine_type_id);
                        formData.append('transmission_id', this.editCar.transmission_id);
                        formData.append('reserved', this.editCar.reserved);
                        formData.append('type', this.editCar.type);
                        formData.append('teh_osmotr_end_date', this.editCar.teh_osmotr_end_date);
                        formData.append('tint_end_date', this.editCar.tint_end_date);

                        axios.post(`/company/${this.$route.params.slug}/cars/${this.$route.params.car}/update`, formData)
                            .then(response => {
                                this.loading.edit = false;
                                this.successSnackbar(response.data.message);
                            })
                            .catch(error => console.log(error));
                }
            });
        },

        fetchCarBodyInfo() {
            axios.get('/admin/cars/body/info')
                .then(response => {
                    response.data.shapes.map(value => {
                        this.shapes.push({
                            text: value.shape_name,
                            value: value.id
                        });
                    }); 

                    response.data.brands.map(value => {
                        this.brands.push({
                            text: value.brand_name,
                            value: value.id
                        });
                    }); 

                    response.data.models.map(value => {
                        this.models.push({
                            text: value.model_name,
                            value: value.id
                        });
                    }); 

                    response.data.engine_types.map(value => {
                        this.engine_types.push({
                            text: value.engine_type_name,
                            value: value.id
                        });
                    }); 

                    response.data.transmissions.map(value => {
                        this.transmissions.push({
                            text: value.transmission_name,
                            value: value.id
                        });
                    }); 
                })
                .catch(error => console.log(error));
        },
    
        pickFile() {
            this.$refs.image.click();
        },

        onFilePicked(e) {
            const files = e.target.files;

            if(files[0] !== undefined) {
                this.editCar.newCover_image.name = files[0].name;

                if(this.editCar.newCover_image.name.lastIndexOf('.') <= 0) {
                    return
                }

                const fr = new FileReader();
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.editCar.newCover_image.url = fr.result;
                    this.editCar.newCover_image.file = files[0];
                })
            } else {
                this.editCar.newCover_image.url = '';
                this.editCar.newCover_image.name = '';
                this.editCar.newCover_image.file = '';
            }
        },

        deleteCoverImage() {
            this.editCar.newCover_image.url = '';
            this.editCar.newCover_image.name = '';
            this.editCar.newCover_image.file = '';
        },
    },
    created() {
        this.editInfo();
        this.fetchCarBodyInfo();
    }
}
</script>

<style>

</style>
