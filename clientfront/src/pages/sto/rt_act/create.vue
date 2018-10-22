<template>
    <div>
        <v-layout row>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn> 
                <v-btn color="primary" :loading="loading.draft" @click="saveToDraft">В черновик</v-btn>        
            </v-flex>
        </v-layout>
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>

        <v-layout v-if="!loading.page" row wrap>
            <v-flex xs12 sm12 md12 lg12>
                <form @submit.prevent="createRT" data-vv-scope="create-rt-act-form" :style="{ fontSize: '14px' }">
                    <table class="rt-act" :style="{ paddingBottom: '20px', marginBottom: '20px', borderBottom: '1px solid #e6e6e6' }">
                        <thead>
                            <tr class="rt-act-title">
                                <th colspan="3"><h2>Акт приёма передачи автомобиля</h2></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="1"><strong>Тип акта</strong></td>
                                <td colspan="2">
                                    <v-radio-group v-model="type" hide-details :style="{ padding: '0' }">
                                        <v-radio label="Приём" :value="0"></v-radio>
                                        <v-radio label="Передача" :value="1"></v-radio>
                                    </v-radio-group>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>От</strong></td>
                                <td colspan="2">
                                    <v-text-field 
                                        v-model="companyFromText" 
                                        label="Введите название компании" 
                                        hide-details
                                        v-validate="'required'" 
                                        :error-messages="errors.collect('company_from')"
                                        data-vv-name="company_from" data-vv-as='"Компания"'
                                        :disabled="companyFromSelect.length > 0"></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Ответственный сотрудник</strong></td>
                                <td colspan="2">
                                    <v-text-field 
                                        v-model="responsibleEmployee" 
                                        label="Введите ФИО" 
                                        hide-details
                                        v-validate="'required'" 
                                        :error-messages="errors.collect('responsible_employee')"
                                        data-vv-name="responsible_employee" data-vv-as='"Ответственный сотрудник"'
                                    ></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Кому</strong></td>
                                <td colspan="2">
                                    <v-select autocomplete label="Выберите компанию"
                                        name="company_name"
                                        v-validate="'required'" 
                                        :error-messages="errors.collect('company_to_select')"
                                        data-vv-name="company_to_select" data-vv-as='"Компания"'
                                        v-model="companyToSelect"
                                        :items="companies"
                                        :disabled="companyToText.length > 0"></v-select>
                                    или
                                    <v-text-field 
                                        v-model="companyToText" 
                                        label="Введите название компании"
                                        v-validate="'required'" 
                                        :error-messages="errors.collect('company_to')"
                                        data-vv-name="company_to" data-vv-as='"Компания"'
                                        hide-details
                                        :disabled="companyToSelect.length > 0"></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Марка автомобиля</strong></td>
                                <td colspan="2">{{ car.brand_name + ' ' + car.model_name }}</td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Номер автомобиля</strong></td>
                                <td colspan="2">{{ car.number }}</td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Водитель</strong></td>
                                <td colspan="2">
                                    <p v-if="car.drivers.length > 0">
                                        <span v-for="driver in car.drivers" :key="driver.id">                                    
                                            {{ driver.pivot.active === 1 ? driver.fullname : null }}
                                        </span>
                                    </p>
                                    <p v-else>Водителя нет</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Акт создал</strong></td>
                                <td colspan="2">{{ user.fullname }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- RT act checklists -->
                    <table class="rt-act">
                        <tbody>
                            <tr v-for="(checklist, index) in checklists" :key="checklist.id">
                                <td colspan="3" v-if="checklist.checklist_items.length > 0" :style="{ padding: '0px', border: '0px' }">
                                    <table class="rt-act">
                                        <thead>
                                            <tr class="rt-act-title" v-if="index === 0">
                                                <td><strong>Наименование части осмотра</strong></td>
                                                <td><strong>Чек лист</strong></td>
                                                <td><strong>Комментарии</strong></td>
                                            </tr>
                                            <tr>
                                                <th class="rt-act-checklist-title" colspan="3">{{ checklist.checklist_name }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in checklist.checklist_items" :key="item.id">
                                                <td colspan="1">{{ item.item_name }}</td>
                                                <td>
                                                    <v-radio-group 
                                                        v-model="values[item.id].status"
                                                        :error-messages="errors.collect(`radio_${item.id}`)"
                                                        hide-details
                                                        :style="{ padding: '0' }"
                                                    >
                                                        <v-radio label="Пройден" :value="0"></v-radio>
                                                        <v-radio label="Не пройден" :value="1"></v-radio>
                                                    </v-radio-group>
                                                </td>
                                                <td>
                                                    <v-text-field
                                                        v-model="values[item.id].comment"
                                                        label="Введите комментарий"
                                                        multi-line 
                                                        clearable
                                                        no-resize
                                                        rows="3"
                                                        hide-details
                                                    ></v-text-field>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">
                                    <FileUpload 
                                        @files-changed="onFileChanged" 
                                        types="image/jpeg, image/png, image/svg+xml" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <v-btn block color="success" :loading="loading.saveBtn" type="submit">Сохранить и отправить на почту</v-btn>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </v-flex>
        </v-layout>
        <!-- HTML for pdf -->
        <v-layout v-show="false" row wrap>
            <v-flex ref="htmlForm" :style="{ fontSize: '11px !important' }">
                <table class="rt-act">
                    <thead>
                        <tr class="rt-act-title">
                            <th colspan="3"><h2>Акт приёма передачи автомобиля</h2></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="1"><strong>Тип акта</strong></td>
                            <td colspan="2">{{ forPDF.type }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>От</strong></td>
                            <td colspan="2">{{ forPDF.companyFrom }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Ответственный сотрудник</strong></td>
                            <td colspan="2">{{ forPDF.responsibleEmployee }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Кому</strong></td>
                            <td colspan="2">{{ forPDF.companyTo }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Марка автомобиля</strong></td>
                            <td colspan="2">{{ forPDF.car }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Номер автомобиля</strong></td>
                            <td colspan="2">{{ forPDF.carNumber }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Водитель</strong></td>
                            <td colspan="2">{{ forPDF.driver }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Акт создал</strong></td>
                            <td colspan="2">{{ forPDF.createdBy }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="rt-act">
                    <tbody>
                        <template 
                            v-for="(item, index) in forPDF.filteredChecklists"
                            :class="{ 'rt-act-title': index === 0 }"
                        >
                            <tr class="rt-act-title" :key="index" v-if="index === 0">
                                <th>Наименование части осмотра</th>
                                <th>Чек лист</th>
                                <th>Комментарии</th>
                            </tr>
                            <tr v-if="item.heading" :key="item.uuid">
                                <th class="rt-act-checklist-title" colspan="3">{{ item.checklist_name }}</th>                            
                            </tr>
                            <tr :key="item.uuid" v-else>
                                <td colspan="1"><strong>{{ item.item_name }}</strong></td>
                                <td>{{ item.status }}</td>
                                <td>{{ item.comment }}</td>
                            </tr>
                            <!-- <tr :key="item.uuid" v-else>
                                <td colspan="1"><strong>{{ item.item_name }}</strong></td>
                                <td>{{ item.status }}</td>
                                <td>{{ item.comment }}</td>
                            </tr> -->
                        </template>
                    </tbody>
                </table>
            </v-flex>
        </v-layout>
    </div>    
</template>

<script>
import axios from '@/axios'
import Loading from '@/components/Loading'
import MoveButtons from '@/components/MoveButtons'
import FileUpload from '@/components/FileUpload'
import UUID from 'uuid-js'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        Loading,
        MoveButtons,
        FileUpload
    },
    computed: {
        user() {
            return JSON.parse(localStorage.getItem('user'));
        }
    },
    data() {
        return {
            car: {
                drivers: []
            },
            type: 0,
            companyFromSelect: '',
            companyFromText: '',
            responsibleEmployee: '',
            companyToSelect: '',
            companyToText: '',
            driver: null,
            comments: [],
            values: [],
            files: [],
            values: [],
            companies: [],
            checklists: [],
            loading: {
                page: false,
                saveBtn: false
            },
            forPDF: {
                type: null,
                companyFrom: '',
                responsibleEmployee: '',
                companyTo: '',
                car: '',
                carNumber: '',
                driver: null,
                createdBy: '',
                checklists: [],
                filteredChecklists: []
            }
        }
    },
    methods: {
        makeRequests() {
            this.loading.page = true;
            
            Promise.all([this.fetchChecklistsAndChecklistItems(), this.fetchFullInfo()])
                .then(values => {
                    const { checklists } =  values[0].data;
                    const { car, companies } =  values[1].data;
                    // Check lists
                    this.forPDF.checklists = checklists;
                    this.checklists = checklists;
                    this.values = [];
                    for(let i = 0; i < checklists.length; i++) {
                        for(let j = 0; j < checklists[i].checklist_items.length; j++) {
                            let item = checklists[i].checklist_items[j];
                            this.values[item.id] = {
                                id: item.id,
                                status: null,
                                comment: ''
                            };
                        }
                    }
                    
                    this.car = car;
                    this.companyFromText = car.companies[0].company_name;
                    this.companies = companies.map(company => company.company_name);
                    this.driver = this.car.drivers.filter(driver => driver.pivot.active === 1)[0];
                    this.loading.page = false;
                });
        },
        fetchChecklistsAndChecklistItems() { return axios.get('rt-act/all') },
        fetchFullInfo() {
            return axios.get(`rt-act/info?sto_slug=${this.$route.params.slug}&car_id=${this.$route.params.car}`);
        },
        createRT() {
            this.$validator.errors.clear();
            this.$validator.validateAll('create-rt-act-form')
                .then(success => {
                    // Validate radio buttons
                    let radiosValidated = true;
                    for(let i = 0; i < this.values.length; i++) {
                        if(this.values[i] !== undefined) {
                            let val = this.values[i];
                            if(val.status === null) {
                                radiosValidated = false;
                                this.$validator.errors.add(`radio_${i}`, 'Поле "Статус" обязательно для заполнения.');
                            }                            
                        }
                    }

                    if(success && radiosValidated) {                      
                        console.log('[Validation] OK!')
                        this.loading.saveBtn = true;

                        let formData = new FormData();
                        let fileList = [];
                        this.files.map(value => {
                            fileList.push(value.file);
                        });                      

                        formData.append('type', this.type);
                        formData.append('car_card_id', this.car.card.id);
                        formData.append('company_from', this.companyFromText);
                        formData.append('responsible_employee', this.responsibleEmployee);
                        formData.append('company_to', this.companyToSelect || this.companyToText);
                        formData.append('company_to_type', this.companyToSelect ? 'inner' : 'outer');
                        formData.append('created_by', this.user.id);
                        
                        if(this.driver !== undefined) {
                            formData.append('driver_id', this.driver.id);                            
                        }
                        for(let i = 0; i < fileList.length; i++) {
                            formData.append('attachments[]', fileList[i]);
                        }

                        // Generating data for PDF file
                        this.forPDF.filteredChecklists = [];
                        this.forPDF.type = this.type === 0 ? 'Приём' : 'Передача';
                        this.forPDF.companyFrom = this.companyFromText;
                        this.forPDF.responsibleEmployee = this.responsibleEmployee;
                        this.forPDF.companyTo = this.companyToSelect || this.companyToText;
                        this.forPDF.car = this.car.brand_name + ' ' + this.car.model_name;
                        this.forPDF.carNumber = this.car.number;
                        this.forPDF.driver = this.driver !== undefined ? this.driver.fullname : 'Водителя нет.'
                        this.forPDF.createdBy = this.user.fullname;
                        for(let i = 0; i < this.forPDF.checklists.length; i++) {
                            let checklist = this.forPDF.checklists[i];
                            checklist.heading = true;
                            checklist.uuid = UUID.create().toString();
                            this.forPDF.filteredChecklists.push(checklist);
                            for(let j = 0; j < checklist.checklist_items.length; j++) {
                                let checklistItem = checklist.checklist_items[j];
                                checklistItem.heading = false;
                                this.forPDF.filteredChecklists.push(checklistItem);
                                for(let k = 0; k < this.values.length; k++) {
                                    if(this.values[k]) {
                                        if(this.values[k].id === checklistItem.id) {
                                            checklistItem.status = this.values[k].status === 0 ? 'Пройден' : 'Не пройден';
                                            checklistItem.comment = this.values[k].comment;
                                            checklistItem.uuid = UUID.create().toString();
                                        }
                                    }                                    
                                }
                            }
                        }

                        this.$nextTick(() => {
                            formData.append('values', JSON.stringify(this.values));
                            formData.append('htmlToPdf', 
                                `<html>
                                    <head>
                                        <style>
                                            body { font-family: DejaVu Sans }
                                            .rt-act {
                                                width: 100%;
                                            }
                                            .rt-act td, .rt-act th {
                                                padding: 5px 10px;
                                                vertical-align: middle;
                                                border: 1px solid #dee2e6;
                                                margin: 0;
                                                font-size: 11px !important;
                                                width: 33.3%;
                                            }
                                            .rt-act td p {
                                                margin: 0;
                                            }
                                            .rt-act-title {
                                                background-color: rgba(0, 0, 0, .05);
                                                text-align: center;
                                            }
                                            .rt-act-checklist-title {
                                                text-transform: uppercase;
                                                font-weight: bold;
                                                text-align: center;
                                            }
                                        </style>
                                        <body>${this.$refs.htmlForm.innerHTML}</body>
                                    </head>
                                </html>`
                            );

                            axios.post('rt-act', formData)
                            .then(response => {
                                this.loading.saveBtn = false;

                                this.$store.dispatch('showSnackbar', {
                                    color: 'success',
                                    active: true,
                                    text: response.data.message
                                });

                                axios.get(`rt-act/${response.data.act.id}/sendActFile`)
                                    .then(response => {
                                        this.$store.dispatch('showSnackbar', {
                                            color: 'success',
                                            active: true,
                                            text: response.data.message
                                        });
                                    })
                                    .catch(error => console.log(error));
                            })
                            .catch(error => console.log(error));
                        });
                    } else {
                        this.$store.dispatch('showSnackbar', {
                            color: 'warning',
                            active: true,
                            text: 'Пожалуйста заполните все поля.'
                        });
                    }
                });

            
        },
        saveToDraft() {
            console.log('[DRAFT]');
            this.loading.draft = true;
            // Collect act data
            let formData = new FormData();
            formData.append('type', this.type);
            formData.append('car_card_id', this.car.card.id);
            formData.append('company_from', this.companyFromText || '');
            formData.append('responsible_employee', this.responsibleEmployee || '');
            formData.append('company_to', this.companyToSelect || this.companyToText || '');
            formData.append('company_to_type', this.companyToSelect ? 'inner' : 'outer');
            formData.append('created_by', this.user.id);
            formData.append('driver_id', this.driver !== undefined ? this.driver.id : null);
            formData.append('values', JSON.stringify(this.values));
            // Collecting act attachments
            for(let i = 0; i < this.files.length; i++) {
                formData.append('attachments[]', this.files[i].file);
            }

            // Sending the data to the server
            axios.post('rt-act/draft', formData)
                .then(response => {
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        active: true,
                        text: response.data.message
                    });
                })
                .catch(error => console.log(error));

            this.loading.draft = false;
        },
        onFileChanged(file) {
            this.files = file;
        }
    },
    created() {
        this.makeRequests();
    }
}
</script>

<style>
    .rt-act {
        width: 100%;
    }
    .rt-act td, .rt-act th {
        padding: 10px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        margin: 0; 
        width: 33%;
    }
    .rt-act td p {
        margin: 0;
    }
    .rt-act-title {
        background-color: rgba(0, 0, 0, .05);
        text-align: center;
    }
    .rt-act-checklist-title {
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
    }
</style>
