<template>
    <div>
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>                   
            </v-flex>
        </v-layout>

        <v-layout row wrap>
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
                        <v-text-field 
                            label="Выберите главное фото" 
                            prepend-icon="attach_file" 
                            append-icon="delete" 
                            :append-icon-cb="deleteCoverImage"
                            @click="pickFile" 
                            v-model="newCar.cover_image.name" 
                            hide-details
                        ></v-text-field>
                        <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                    </v-card-actions>
                </v-card>
                <!-- Company -->
                <v-card class="mt-3">
                    <v-card-media>
                        <v-container>
                            <v-layout>
                                <v-flex>
                                    <p class="subheading my-0">Компания</p>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-select 
                            autocomplete 
                            :items="companies" 
                            v-model="newCar.company_id" 
                            label="Выберите компанию" 
                            prepend-icon="business"
                            name="company_id"
                            v-validate="'required'" 
                            :error-messages="errors.collect('company_id')"
                            data-vv-name="company_id" data-vv-as='"Компания"'
                            hide-details
                        ></v-select>
                    </v-card-text>
                </v-card>
            </v-flex>

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
                            <v-layout row wrap>
                                <v-flex xs12 sm12 md12 lg12 class="v-divider pr-4">                                        
                                    <v-container grid-list-xs>
                                        <v-text-field type="text" v-model="newCar.year" name="year" label="Введите год" prepend-icon="event"                 
                                            v-validate="'required'" 
                                            data-vv-name="year" data-vv-as='"Год автомобиля"'
                                            :error-messages="errors.collect('year')"
                                        ></v-text-field>

                                        <v-text-field type="text" v-model="newCar.number" name="number" label="Гос номер машины" prepend-icon="filter_2"                 
                                            v-validate="'required'" 
                                            data-vv-name="number" data-vv-as='"Гос номер"'
                                            :error-messages="errors.collect('number')"
                                        ></v-text-field>

                                        <v-text-field type="text" 
                                            v-model="newCar.color" 
                                            name="color" 
                                            label="Цвет" 
                                            prepend-icon="color_lens"
                                            hint="Например: Мокрый асфальт"
                                        ></v-text-field>

                                        <v-text-field type="number" 
                                            v-model="newCar.price" 
                                            name="color" 
                                            label="Цена" 
                                            prepend-icon="attach_money"
                                            suffix="сом."
                                        ></v-text-field>  

                                        <v-select 
                                            autocomplete 
                                            :items="shapes" 
                                            v-model="newCar.shape_id" 
                                            label="Выберите кузов" 
                                            prepend-icon="directions_car"
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

                                        <v-select autocomplete :items="filteredModels" v-model="newCar.model_id" label="Выберите модель" prepend-icon="directions_car"
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

                                        <v-checkbox label="В резерв" v-model="newCar.reserved" hide-details></v-checkbox>
                                        <v-checkbox label="Есть GPS" v-model="newCar.has_gps" hide-details></v-checkbox>
                                        <v-radio-group v-model="newCar.type" row class="pt-0" hide-details>
                                            <v-radio label="Служебная" :value="0"></v-radio>
                                            <v-radio label="Служебно-Личная" :value="1"></v-radio>
                                        </v-radio-group>                                            
                                    </v-container>                                        
                                </v-flex>
                            </v-layout>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn :loading="loading" color="success" type="submit" block>Создать</v-btn>
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
                    <file-upload @files-changed="filesChanged" /> 
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
import axios from '@/axios'
import snackbar from '@/components/mixins/snackbar'
import FileUpload from '@/components/FileUpload'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    mixins: [ snackbar ],
    components: {
        FileUpload
    },
    computed: {
        filteredModels() {
            return this.models.filter(model => model.brand_id === this.newCar.brand_id)
        }
    },
    data() {
        return {
            attachments: [],
            newCar: {
                year: '',
                number: '',
                color: '',
                price: '',
                has_gps: false,
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
                company_id: Number(this.$route.params.company) ,
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
            transmissions: [],
            companies: []
        }
    },    
    methods: {
        filesChanged(file) {
            this.attachments = file;
            console.log(this.attachments);
        },

        createCar() {
            this.$validator.validateAll()
                .then(success => {
                    if(success) {                    
                        this.loading = true;
                        // this.attachments = this.$refs.pond.getFiles();
                        let fileList = [];
                        this.attachments.map(value => {
                            fileList.push(value.file);
                        });

                        let formData = new FormData();
                        formData.append('year', this.newCar.year);
                        formData.append('number', this.newCar.number);
                        formData.append('color', this.newCar.color);
                        formData.append('price', this.newCar.price);
                        formData.append('has_gps', this.newCar.has_gps);
                        formData.append('shape_id', this.newCar.shape_id);
                        formData.append('brand_id', this.newCar.brand_id);
                        formData.append('model_id', this.newCar.model_id);
                        formData.append('milage', this.newCar.milage);
                        formData.append('vin_code', this.newCar.vin_code);
                        formData.append('cover_image', this.newCar.cover_image.file);
                        formData.append('engine_capacity', this.newCar.engine_capacity);
                        formData.append('engine_type_id', this.newCar.engine_type_id);
                        formData.append('transmission_id', this.newCar.transmission_id);
                        formData.append('company_id', this.newCar.company_id);
                        formData.append('reserved', this.newCar.reserved);
                        formData.append('type', this.newCar.type);
                        formData.append('teh_osmotr_end_date', this.newCar.teh_osmotr_end_date);
                        formData.append('tint_end_date', this.newCar.tint_end_date);
                        
                        for(let i = 0; i < fileList.length; i++) {
                            formData.append('attachments[]', fileList[i]);
                        }

                        axios.post(`/sto/${this.$route.params.slug}/cars`, formData)
                            .then(response => {
                                console.log(response);
                                this.loading = false;
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
                            value: value.id,
                            brand_id: value.brand_id
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

        fetchCompanies() {
            axios.get(`/sto/${this.$route.params.slug}/companies`)
                .then(response => {
                    response.data.companies.map(company => {
                        this.companies.push({
                            text: company.company_name,
                            value: company.id
                        });                        
                    }); 
                }) 
                .catch(error => console.error());
        },        
    },
    created() {
        this.fetchCarBodyInfo();
        this.fetchCompanies();
    }
}
</script>

<style>

</style>
