<template>
    <div>
        <MoveButtons />
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>

        <v-layout v-if="!loading.page" row wrap>
            <v-flex xs12 sm12 md12 lg12>
                <form @submit.prevent="createRT" data-vv-scope="create-rt-act-form">                
                    <table class="rt-act" :style="{ paddingBottom: '20px', marginBottom: '20px', borderBottom: '1px solid #e6e6e6' }">
                        <thead>
                            <tr class="rt-act-title">
                                <th colspan="3"><h2>Акт приёма передачи автомобиля</h2></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="1">Тип акта</td>
                                <td colspan="2">
                                    <v-radio-group v-model="type" hide-details :style="{ padding: '0' }">
                                        <v-radio label="Приём" :value="0"></v-radio>
                                        <v-radio label="Передача" :value="1"></v-radio>
                                    </v-radio-group>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1">От</td>
                                <td colspan="2">
                                    <v-select autocomplete label="Выберите компанию"
                                        name="company_name"
                                        v-validate="'required'" 
                                        :error-messages="errors.collect('company_name_select')"
                                        data-vv-name="company_name_select" data-vv-as='"Компания"'
                                        v-model="companyFromSelect"
                                        :items="companies"
                                        :disabled="companyFromText.length > 0"></v-select>
                                    или
                                    <v-text-field 
                                        v-model="companyFromText" 
                                        label="Введите компанию" 
                                        hide-details
                                        v-validate="'required'" 
                                        :error-messages="errors.collect('company_name_text')"
                                        data-vv-name="company_name_text" data-vv-as='"Компания"'
                                        :disabled="companyFromSelect.length > 0"></v-text-field>                                
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1">Ответственный сотрудник</td>
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
                                <td colspan="1">Кому</td>
                                <td colspan="2">
                                    <v-text-field 
                                        v-model="companyTo" 
                                        label="Введите название компании"
                                        v-validate="'required'" 
                                        :error-messages="errors.collect('company_to')"
                                        data-vv-name="company_to" data-vv-as='"Компания"' 
                                        hide-details></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1">Водитель</td>
                                <td colspan="2">
                                    <p v-if="car.drivers.length > 0">
                                        <span v-for="driver in car.drivers" :key="driver.id">                                    
                                            {{ driver.pivot.active === 1 ? driver.fullname : null }}
                                        </span>
                                    </p>
                                    <p v-else>Водителя нет</p>
                                </td>
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
                                                        v-model="result.values[item.id]" 
                                                        hide-details 
                                                        :style="{ padding: '0' }"
                                                    >
                                                        <v-radio label="Пройден" :value="0"></v-radio>
                                                        <v-radio label="Не пройден" :value="1"></v-radio>
                                                    </v-radio-group>
                                                </td>
                                                <td>
                                                    <v-text-field
                                                        v-model="result.comments[item.id]"
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
                                    <v-btn block color="success" :loading="loading.saveBtn" type="submit">Сохранить</v-btn>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </v-flex>
        </v-layout>
    </div>    
</template>

<script>
import axios from '@/axios'
import Loading from '@/components/Loading'
import MoveButtons from '@/components/MoveButtons'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        Loading,
        MoveButtons
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
            companyTo: '',
            driver: null,
            comments: [],
            values: [],
            result: {
                values: [],
                comments: []
            },
            companies: [],
            checklists: [],
            loading: {
                page: false,
                saveBtn: false
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
                    this.checklists = checklists;
                    for(let i = 0; i < checklists.length; i++) {
                        for(let j = 0; j < checklists[i].checklist_items.length; j++) {
                            let item = checklists[i].checklist_items[j];
                            this.result.values[item.id] = 0;
                        }
                    }
                    
                    this.car = car;
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
            this.$validator.validateAll('create-rt-act-form')
                .then(success => {
                    if(success) {
                        console.log('[Validation] OK!')

                        this.loading.saveBtn = true;
                        let formData = new FormData();

                        formData.append('type', this.type);
                        formData.append('car_card_id', this.car.card.id);
                        formData.append('company_from', this.companyFromSelect || this.companyFromText);
                        formData.append('responsible_employee', this.responsibleEmployee);
                        formData.append('company_to', this.companyTo);
                        if(this.driver !== undefined) {
                            formData.append('driver_id', this.driver.id);                            
                        }

                        let newArr = this.result.values.map((value, index) => {
                            return {
                                id: index,
                                status: value,
                                comment: '' 
                            }
                        });
                        this.result.comments.map((comment, index) => newArr[index].comment = comment);

                        formData.append('values', JSON.stringify(newArr));

                        axios.post('rt-act', formData)
                            .then(response => {
                                this.loading.saveBtn = false;

                                this.$store.dispatch('showSnackbar', {
                                    color: 'success',
                                    active: true,
                                    text: response.data.message
                                });
                            })
                            .catch(error => console.log(error));
                    } else {
                        console.log('[Validation] Failed!')
                    }
                });

            
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
    .rt-act tbody tr:nth-of-type(odd) {        
        /* background-color: rgba(0, 0, 0, .05); */
    }
    .rt-act td, .rt-act th {
        padding: .75rem;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        margin: 0;
        font-size: 14px;
        width: 250px;
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
