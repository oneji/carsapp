<template>
    <div>
        <v-layout>
            <v-flex>
                <v-btn color="success" @click="$router.back()">Назад</v-btn>
            </v-flex>
        </v-layout>
        <v-layout row wrap>
            <!-- Main photo -->
            <v-flex xs12 sm12 md4 lg3>
                <v-card>
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
                    <v-card-media :src="newCar.cover_image.url ? newCar.cover_image.url : '/static/images/no-photo.png'" height="200px"></v-card-media>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-container class="pb-0 pt-3">
                            <v-layout row wrap>
                                <v-text-field label="Выберите главное фото" @click="pickFile" v-model="newCar.cover_image.name" 
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
                    <v-form @submit.prevent="createCar">
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
                                            <v-text-field type="text" v-model="newCar.year" name="year" label="Выберите год" prepend-icon="event"                 
                                                v-validate="'required'" 
                                                data-vv-name="year" data-vv-as='"Год автомобиля"'
                                                :error-messages="errors.collect('year')"
                                            ></v-text-field>

                                            <v-text-field type="text" v-model="newCar.number" name="number" label="Гос номер машины" prepend-icon="filter_2"                 
                                                v-validate="'required'" 
                                                data-vv-name="number" data-vv-as='"Гос номер"'
                                                :error-messages="errors.collect('number')"
                                            ></v-text-field>

                                            <v-select autocomplete :items="shapes" v-model="newCar.shape_id" label="Выберите кузов" prepend-icon="category"
                                                name="shape_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('shape_id')"
                                                data-vv-name="shape_id" data-vv-as='"Кузов"'
                                            ></v-select>

                                            <v-select autocomplete :items="brands" v-model="newCar.brand_id" label="Выберите марку" prepend-icon="directions_car"
                                                name="brand_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('brand_id')"
                                                data-vv-name="brand_id" data-vv-as='"Марка"'
                                            ></v-select>

                                            <v-select autocomplete :items="models" v-model="newCar.model_id" label="Выберите модель" prepend-icon="directions_car"
                                                name="model_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('model_id')"
                                                data-vv-name="model_id" data-vv-as='"Модель"'
                                            ></v-select>

                                            <v-text-field v-model="newCar.milage" name="milage" label="Пробег" type="text" prepend-icon="swap_calls" suffix="км."                                    
                                            ></v-text-field>

                                            <v-text-field v-model="newCar.vin_code" name="vin_code" label="Вин код" type="text" prepend-icon="polymer"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('vin_code')"
                                                data-vv-name="vin_code" data-vv-as='"Вин код"'                                    
                                            ></v-text-field>  

                                            <v-text-field v-model="newCar.engine_capacity" name="engine_capacity" label="Объём двигателя" type="text" prepend-icon="polymer"                                  
                                            ></v-text-field> 

                                            <v-select :items="engine_types" v-model="newCar.engine_type_id" label="Выберите тип двигателя" prepend-icon="directions_car"
                                                name="engine_type_id"
                                                v-validate="'required'" 
                                                :error-messages="errors.collect('engine_type_id')"
                                                data-vv-name="engine_type_id" data-vv-as='"Тип двигателя"'
                                            ></v-select>   

                                            <v-select :items="transmissions" v-model="newCar.transmission_id" label="Выберите КПП" prepend-icon="directions_car"
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
                                                :return-value.sync="newCar.teh_osmotr_end_date"
                                                lazy
                                                transition="scale-transition"
                                                offset-y
                                                full-width
                                                min-width="290px">
                                                    <v-text-field 
                                                        slot="activator" 
                                                        v-model="newCar.teh_osmotr_end_date" 
                                                        label="Выберите дату окончания тех осмотра" 
                                                        prepend-icon="event" 
                                                        readonly
                                                    ></v-text-field>
                                                    <v-date-picker 
                                                        v-model="newCar.teh_osmotr_end_date" 
                                                        scrollable 
                                                        locale="ru" 
                                                        @input="$refs.menu2.save(newCar.teh_osmotr_end_date)"
                                                    ></v-date-picker>
                                            </v-menu>

                                            <v-menu
                                                ref="menu3"
                                                :close-on-content-click="false"
                                                v-model="menu3"
                                                :nudge-right="40"
                                                :return-value.sync="newCar.tint_end_date"
                                                lazy
                                                transition="scale-transition"
                                                offset-y
                                                full-width
                                                min-width="290px">
                                                    <v-text-field 
                                                        slot="activator" 
                                                        v-model="newCar.tint_end_date" 
                                                        label="Выберите дату окончания тонировки" 
                                                        prepend-icon="event" 
                                                        readonly
                                                    ></v-text-field>
                                                    <v-date-picker 
                                                        v-model="newCar.tint_end_date"
                                                        scrollable 
                                                        locale="ru" 
                                                        @input="$refs.menu3.save(newCar.tint_end_date)"
                                                    ></v-date-picker>
                                            </v-menu> 

                                            <v-checkbox label="В резерв" v-model="newCar.reserved"></v-checkbox>   
                                            <v-radio-group v-model="newCar.type" row class="pt-0">
                                                <v-radio label="Служебная" :value="0"></v-radio>
                                                <v-radio label="Служебная-Личная" :value="1"></v-radio>
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
                                        <v-btn block :loading="loading" color="success" type="submit">Создать</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-actions>   
                    </v-form> 
                </v-card>
            </v-flex>
            <!-- Attachments -->
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

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    data() {
        return {
            attachments: [],
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
                type: 0,
                teh_osmotr_end_date: null,
                tint_end_date: null 
            },
            loading: false,
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
    components: {
        FilePond
    },
    methods: {
        createCar() {
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
                        formData.append('year', this.newCar.year);
                        formData.append('number', this.newCar.number);
                        formData.append('shape_id', this.newCar.shape_id);
                        formData.append('brand_id', this.newCar.brand_id);
                        formData.append('model_id', this.newCar.model_id);
                        formData.append('milage', this.newCar.milage);
                        formData.append('vin_code', this.newCar.vin_code);
                        formData.append('cover_image', this.newCar.cover_image.file);
                        formData.append('engine_capacity', this.newCar.engine_capacity);
                        formData.append('engine_type_id', this.newCar.engine_type_id);
                        formData.append('transmission_id', this.newCar.transmission_id);
                        formData.append('reserved', this.newCar.reserved);
                        formData.append('type', this.newCar.type);
                        formData.append('teh_osmotr_end_date', this.newCar.teh_osmotr_end_date);
                        formData.append('tint_end_date', this.newCar.tint_end_date);
                        
                        for(let i = 0; i < fileList.length; i++) {
                            formData.append('attachments[]', fileList[i]);
                        }

                        axios.post(`/company/${this.$route.params.slug}/cars`, formData)
                            .then(response => {
                                this.loading = false;
                                this.snackbar.color = 'success';
                                this.snackbar.text = response.data.message;
                                this.snackbar.active = true;
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
    
        pickFile () {
            this.$refs.image.click();
        },

        onFilePicked (e) {
            const files = e.target.files;

            if(files[0] !== undefined) {
                this.newCar.cover_image.name = files[0].name;

                if(this.newCar.cover_image.name.lastIndexOf('.') <= 0) {
                    return
                }

                const fr = new FileReader();
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.newCar.cover_image.url = fr.result;
                    this.newCar.cover_image.file = files[0];
                })
            } else {
                this.newCar.cover_image.url = '';
                this.newCar.cover_image.name = '';
                this.newCar.cover_image.file = '';
            }
        },

        deleteCoverImage() {
            this.newCar.cover_image.url = '';
            this.newCar.cover_image.name = '';
            this.newCar.cover_image.file = '';
        },
    },
    created() {
        this.fetchCarBodyInfo();
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
