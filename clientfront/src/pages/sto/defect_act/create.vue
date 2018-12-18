<template>
    <div>
        <!-- Back button -->
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>                   
            </v-flex>
        </v-layout>
        <!-- Page loading spinner -->
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>
        <!-- Content -->
        <v-layout v-if="!loading.page">
            <v-flex xs12 sm12 md12 lg12>
                <form @submit.prevent="createDefectAct" data-vv-scope="create-defect-act-form" :style="{ fontSize: '14px' }">
                    <table class="defect-act-table" :style="{ paddingBottom: '20px', marginBottom: '20px', borderBottom: '1px solid #e6e6e6' }">
                        <thead>
                            <tr class="defect-act-table-title">
                                <th colspan="2"><h2>Дефектный акт</h2></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="1"><strong>Компания</strong></td>
                                <td colspan="1">{{ company.company_name }}</td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Марка автомобиля</strong></td>
                                <td colspan="1">{{ car.brand_name + ' ' + car.model_name }}</td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Номер автомобиля</strong></td>
                                <td colspan="1">{{ car.number }}</td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Водитель</strong></td>
                                <td colspan="1">
                                    <p v-if="car.drivers.length > 0">
                                        <span v-for="driver in car.drivers" :key="driver.id">                                    
                                            {{ driver.pivot.active === 1 ? driver.fullname : null }}
                                        </span>
                                    </p>
                                    <p v-else>Водителя нет</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Дата составления акта</strong></td>
                                <td colspan="1">{{ new Date() | moment('MMMM DD, YYYY') }}</td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Акт составил</strong></td>
                                <td colspan="1">{{ user.fullname }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="defect-act-table">
                        <tbody>
                            <template v-for="(item, index) in defectsData">
                                <tr class="defect-act-table-title" v-if="item.heading" :key="item.uuid">
                                    <th colspan="7" :style="{ position: 'relative' }">                                        
                                        <a class="black--text" @click="toggleSection(item.id)">{{ item.defect_type_name }}</a>
                                    </th>
                                </tr>
                                <tr class="defect-act-table-section" v-if="item.heading" :key="index" v-show="hiddenDefectType === item.id">
                                    <th>Деталь</th>
                                    <th>Статус</th>
                                    <th>Состояние</th>
                                    <th>Заключение</th>
                                    <th>Цена за ремонт</th>
                                    <th>Количество</th>
                                    <th>Комментарий</th>
                                </tr>
                                <tr v-if="!item.heading" v-show="hiddenDefectType === item.defect_type_id" :key="item.uuid">
                                    <!-- Detail -->
                                    <td colspan="1">{{ item.defect_name }}</td>
                                    <!-- Status -->
                                    <td colspan="1">
                                        <v-radio-group 
                                            v-model="detailsInfo[item.id].toReport"
                                            :error-messages="errors.collect(`radio_${item.id}`)"
                                            hide-details
                                            :style="{ padding: '0' }"
                                        >
                                            <v-radio label="Пройден" :value="1"></v-radio>
                                            <v-radio label="Не пройден" :value="0"></v-radio>
                                        </v-radio-group>
                                        <a @click="unCheckStatus(item.id)" :style="{ fontSize: '85%' }" class="grey--text">Убрать выбранное</a>
                                    </td>
                                    <!-- Condition -->
                                    <td colspan="1">
                                        <v-radio-group 
                                            v-model="selectedDetailConditions[item.id]"
                                            :error-messages="errors.collect(`condition_${item.id}`)"
                                            hide-details
                                            :style="{ padding: '0' }"
                                        >
                                            <v-radio
                                                v-for="condition in item.defect_options"
                                                :key="condition.id"
                                                :label="condition.defect_option_name" 
                                                :value="condition.id"
                                            ></v-radio>
                                        </v-radio-group>
                                        <a @click="unCheckCondition(item.id)" :style="{ fontSize: '85%' }" class="grey--text">Убрать выбранное</a>
                                    </td>    
                                    <!-- Conclusion -->
                                    <td colspan="1">
                                        <v-radio-group 
                                            v-model="selectedDetailConclusions[item.id]"
                                            :error-messages="errors.collect(`conclusion_${item.id}`)"
                                            hide-details
                                            :style="{ padding: '0' }"
                                            @change="setPrice(item.id)"
                                        >
                                            <v-radio
                                                v-for="conclusion in item.defect_conclusions"
                                                :key="conclusion.id"
                                                :label="conclusion.conclusion_name" 
                                                :value="conclusion.id"
                                            ></v-radio>
                                        </v-radio-group>
                                        <a @click="unCheckConclusion(item.id)" :style="{ fontSize: '85%' }" class="grey--text">Убрать выбранное</a>
                                    </td>
                                    <!-- Service price -->
                                    <td>
                                        <p>{{ servicePrices[item.id] !== null ? servicePrices[item.id] + ' сом.' : null }}</p>
                                        <v-text-field
                                            v-model="humanHours"
                                            label="Кол-во человеко часов"
                                            hide-details
                                            v-if="setHumanHours.includes(item.id)"
                                        ></v-text-field>
                                        <v-btn small block color="success" v-if="setHumanHours.includes(item.id)" @click="saveHumanHours(item.id)">Сохранить</v-btn>
                                        <v-checkbox
                                            label="Расчитать по ч/ч"
                                            v-model="setHumanHours"
                                            :value="item.id"
                                            v-if="!setHumanHours.includes(item.id)"
                                            hide-details></v-checkbox>
                                    </td>
                                    <td>
                                        <v-text-field
                                            v-model="detailQuantities[item.id]"
                                            label="Количество"
                                            hide-details
                                        ></v-text-field>
                                        <v-btn small block color="success" @click="setPrice(item.id)">Сохранить</v-btn>
                                    </td>
                                    <!-- Comment -->
                                    <td colspan="1">
                                        <v-text-field
                                            v-model="detailsInfo[item.id].comment"
                                            label="Введите комментарий"
                                            multi-line
                                            clearable
                                            no-resize
                                            rows="3"
                                            hide-details
                                        ></v-text-field>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody>
                            <tr class="defect-act-table-title">
                                <th colspan="7">Итоговая цена ремонта: {{ totalPrice }} сом.</th>
                            </tr>
                            <tr class="defect-act-table-title">
                                <th colspan="7">Файлы и комментарий</th>                                
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <v-text-field
                                        name="defect_act_comment"
                                        label="Введите комментарий"
                                        multi-line
                                        clearable
                                        no-resize
                                        v-model="comment"
                                        rows="3"
                                    ></v-text-field>
                                </td>
                            </tr>
                            <tr class="defect-act-table-title">
                                <th colspan="7">Скидка</th>                                
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <v-text-field
                                        name="discount_percent"
                                        label="Размер скидки, %"
                                        clearable
                                        no-resize
                                        v-model="discount_percent"
                                        hide-details></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <FileUpload
                                        @files-changed="onFileChanged"
                                        types="image/jpeg, image/png, image/svg+xml" />
                                </td>
                            </tr>
                        </tbody>                       
                        <tfoot>
                            <tr>
                                <td colspan="7">
                                    <v-btn block color="success" :loading="loading.saveBtn" type="submit">Сохранить и отравить на утверждение</v-btn>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </v-flex>
        </v-layout>        
        <!-- Partial info for pdf -->
        <v-layout row wrap v-show="false">
            <v-flex ref="partialReport">
                <table class="defect-act-table" style="padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid #e6e6e6; font-size: 11px !important">
                    <thead>
                        <tr class="defect-act-table-title">
                            <th colspan="2"><h2>Дефектный акт</h2></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="1"><strong>Компания</strong></td>
                            <td colspan="1">{{ company.company_name }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Марка автомобиля</strong></td>
                            <td colspan="1">{{ car.brand_name + ' ' + car.model_name }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Номер автомобиля</strong></td>
                            <td colspan="1">{{ car.number }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Водитель</strong></td>
                            <td colspan="1">
                                <p v-if="car.drivers.length > 0">
                                    <span v-for="driver in car.drivers" :key="driver.id">                                    
                                        {{ driver.pivot.active === 1 ? driver.fullname : null }}
                                    </span>
                                </p>
                                <p v-else>Водителя нет</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Дата составления акта</strong></td>
                            <td colspan="1">{{ new Date() | moment('MMMM DD, YYYY') }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Акт составил</strong></td>
                            <td colspan="1">{{ user.fullname }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="defect-act-table" style="font-size: 11px !important">
                    <tbody>
                        <template v-for="(item, index) in defectsData">
                            <tr class="defect-act-table-title" v-if="item.heading && forPDF.partialReportChecklistsHeaders[item.id]" :key="item.uuid">
                                <th colspan="7">{{ item.defect_type_name }}</th>
                            </tr>
                            <tr class="defect-act-table-section" v-if="item.heading && forPDF.partialReportChecklistsHeaders[item.id]" :key="index">
                                <th>Деталь</th>
                                <th>Статус</th>
                                <th>Состояние</th>
                                <th>Заключение</th>
                                <th>Цена за ремонт</th>
                                <th>Количество</th>
                                <th>Комментарий</th>
                            </tr>
                            <tr v-if="!item.heading && forPDF.partialReport[item.id].toReport === 0" :key="item.uuid">
                                <td>{{ item.defect_name }}</td>
                                <td>{{ forPDF.partialReport[item.id].toReport === 1 ? 'Пройден' : 'Не пройден' }}</td>                               
                                <td>
                                    <p v-for="condition in item.defect_options" :key="condition.id">
                                        {{ 
                                            selectedDetailConditions[item.id] === condition.id 
                                            ? condition.defect_option_name
                                            : ''
                                        }}
                                    </p>
                                </td>
                                <td>
                                    <p v-for="conclusion in item.defect_conclusions" :key="conclusion.id">
                                        {{ 
                                            selectedDetailConclusions[item.id] === conclusion.id
                                            ? conclusion.conclusion_name
                                            : ''
                                        }}
                                    </p>
                                </td>
                                <td>
                                    <p>{{ servicePrices[item.id] !== null ? servicePrices[item.id] + ' сом.' : null }}</p>
                                </td>
                                <td>{{ detailQuantities[item.id] }}</td>
                                <td>{{ forPDF.partialReport[item.id].comment }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </v-flex>
        </v-layout>
        <!-- Full info for pdf -->
        <v-layout row wrap v-show="false">
            <v-flex ref="fullReport">
                <table class="defect-act-table" style="padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid #e6e6e6; font-size: 11px !important">
                    <thead>
                        <tr class="defect-act-table-title">
                            <th colspan="2"><h2>Дефектный акт</h2></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="1"><strong>Компания</strong></td>
                            <td colspan="1">{{ company.company_name }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Марка автомобиля</strong></td>
                            <td colspan="1">{{ car.brand_name + ' ' + car.model_name }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Номер автомобиля</strong></td>
                            <td colspan="1">{{ car.number }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Водитель</strong></td>
                            <td colspan="1">
                                <p v-if="car.drivers.length > 0">
                                    <span v-for="driver in car.drivers" :key="driver.id">                                    
                                        {{ driver.pivot.active === 1 ? driver.fullname : null }}
                                    </span>
                                </p>
                                <p v-else>Водителя нет</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Дата составления акта</strong></td>
                            <td colspan="1">{{ new Date() | moment('MMMM DD, YYYY') }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Акт составил</strong></td>
                            <td colspan="1">{{ user.fullname }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="defect-act-table" style="font-size: 11px !important">
                    <tbody>
                        <template v-for="(item, index) in defectsData">
                            <tr class="defect-act-table-title" v-if="item.heading && forPDF.fullReportChecklistsHeaders[item.id]" :key="item.uuid">
                                <th colspan="7">{{ item.defect_type_name }}</th>
                            </tr>
                            <tr class="defect-act-table-section" v-if="item.heading && forPDF.fullReportChecklistsHeaders[item.id]" :key="index">
                                <th>Деталь</th>
                                <th>Статус</th>
                                <th>Состояние</th>
                                <th>Заключение</th>
                                <th>Цена за ремонт</th>
                                <th>Количество</th>
                                <th>Комментарий</th>
                            </tr>
                            <tr v-if="!item.heading && detailsInfo[item.id].toReport !== null" :key="item.uuid">
                                <td>{{ item.defect_name }}</td>
                                <td>{{ forPDF.fullReport[item.id].toReport === 1 ? 'Пройден' : 'Не пройден' }}</td>
                                <td>
                                    <p v-for="condition in item.defect_options" :key="condition.id">
                                        {{ 
                                            selectedDetailConditions[item.id] === condition.id
                                            ? condition.defect_option_name
                                            : ''
                                        }}
                                    </p>
                                </td>
                                <td>
                                    <p v-for="conclusion in item.defect_conclusions" :key="conclusion.id">
                                        {{ 
                                            selectedDetailConclusions[item.id] === conclusion.id 
                                            ? conclusion.conclusion_name
                                            : ''
                                        }}
                                    </p>
                                </td>
                                <td>
                                    <p>{{ servicePrices[item.id] !== null ? servicePrices[item.id] + ' сом.' : null }}</p>
                                </td>
                                <td>{{ detailQuantities[item.id] }}</td>
                                <td>{{ forPDF.fullReport[item.id].comment }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import axios from '@/axios'
import UUID from 'uuid-js'
import Alert from '@/components/Alert'
import Loading from '@/components/Loading'
import FileUpload from '@/components/FileUpload'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        Alert,
        Loading,
        FileUpload
    },
    computed: {
        user() {
            return JSON.parse(localStorage.getItem('user'));
        },
        totalPrice() {
            let totalPrice = 0;

            for(let i = 0; i < this.servicePrices.length; i++) {
                let price = this.servicePrices[i];
                if(price !== null && price !== undefined) {
                    totalPrice += Number(price);
                }
            }
            
            return totalPrice; 
        }
    },
    data() {
        return {
            car: {
                drivers: []
            },
            company: {},
            comment: '',
            discount_percent: null,
            attachments: [],
            cardId: null,
            defectsData: [],
            equipment: [],
            selectedDetailConditions: [],
            selectedDetailConclusions: [],
            servicePrices: [],
            detailQuantities: [],
            setHumanHours: [],
            humanHours: 1,
            selectedEquipment: [],
            detailsInfo: [],
            loading: {
                page: true,
                saveBtn: false,
            },
            hiddenDefectType: null,

            forPDF: {
                fullReport: [],
                partialReport: [],
                fullReportChecklistsHeaders: [],
                partialReportChecklistsHeaders: [],
            }
        }
    },
    methods: {
        fetchDefectsInfo() {
            axios.get(`/sto/${this.$route.params.slug}/cars/${this.$route.params.car}/card?company=${this.$route.params.company}`)
                .then(response => {
                    console.log(response.data);
                    let { car, defects_info, equipment, company } = response.data;
                    this.car = car;
                    this.company = company;
                    this.equipment = equipment;
                    this.cardId = car.card.id;
                    // Create an array with fake data to be filled later
                    for(let i = 0; i < defects_info.length; i++) {
                        let checklist = defects_info[i];
                        this.forPDF.partialReportChecklistsHeaders[checklist.id] = false;
                        for(let j = 0; j < checklist.defects.length; j++) {
                            let detail = checklist.defects[j];
                            this.$set(this.selectedDetailConditions, detail.id, null);
                            this.$set(this.selectedDetailConclusions, detail.id, null);
                            this.$set(this.detailsInfo, detail.id, { comment: '', toReport: null });
                            this.$set(this.servicePrices, detail.id, null);
                            this.$set(this.detailQuantities, detail.id, 1);
                            // Data for PDF files
                            this.forPDF.fullReport[detail.id] = { comment: '', toReport: null };
                            this.forPDF.partialReport[detail.id] = { comment: '', toReport: null };
                        }
                    }
                    // Parse defect checklists and merge all into one array
                    for(let i = 0; i < defects_info.length; i++) {
                        // If heading = true -> checklist, else -> detail
                        let checklist = defects_info[i];
                        checklist.heading = true;
                        checklist.uuid = UUID.create().toString();
                        this.defectsData.push(checklist);
                        // Parse defect details
                        for(let j = 0; j < checklist.defects.length; j++) {
                            let detail = checklist.defects[j];
                            detail.heading = false;
                            detail.uuid = UUID.create().toString();
                            this.defectsData.push(detail);
                        }
                    }
                    // Hide page loading spinner and show content
                    this.loading.page = false;
                })
                .catch(error => console.log(error));
        },
        createDefectAct() {
            this.$validator.errors.clear();
            this.$validator.validateAll('create-defect-act-form')
                .then(success => {
                    this.loading.saveBtn = true;
                    let allCheckboxesValidated = true;
                    // Validate conditions checkboxes
                    for(let i = 0; i < this.selectedDetailConditions.length; i++) {
                        let condition = this.selectedDetailConditions[i];
                        if(condition !== undefined) {
                            if(condition === null && this.detailsInfo[i].toReport === 0) {
                                this.$validator.errors.add(`condition_${i}`, 'Поле "Состояние" обязательно для заполнения.');
                                allCheckboxesValidated = false;
                            }
                        }                        
                    }
                    // Validate conclusion checkboxes
                    for(let i = 0; i < this.selectedDetailConclusions.length; i++) {
                        let conclusion = this.selectedDetailConclusions[i];
                        if(conclusion !== undefined) {
                            if(conclusion === null && this.detailsInfo[i].toReport === 0) {
                                this.$validator.errors.add(`conclusion_${i}`, 'Поле "Заключение" обязательно для заполнения.');
                                allCheckboxesValidated = false;
                            }
                        }
                        
                    }
                    // Validate to report status
                    // for(let i = 0; i < this.detailsInfo.length; i++) {
                    //     let infoItem = this.detailsInfo[i];
                    //     if(infoItem !== undefined) {
                    //         if(infoItem.toReport === 0) {
                    //             this.$validator.errors.add(`radio_${i}`, 'Поле "В отчет" обязательно для заполнения.');
                    //             allCheckboxesValidated = false;
                    //         }
                    //     }
                    // }
                    
                    // If validation succeed ...
                    if(allCheckboxesValidated) {
                        console.log('[Validation] OK!');
                        // Collecting data for PDF files
                        this.forPDF.partialReport = [...this.detailsInfo];
                        this.forPDF.fullReport = [...this.detailsInfo];
                        for(let i = 0; i < this.defectsData.length; i++) {
                            let item = this.defectsData[i];
                            if(item.heading) {
                                let flag = false;
                                let flag2 = false;
                                for(let j = 0; j < item.defects.length; j++) {
                                    let defect = item.defects[j];
                                    if(this.forPDF.partialReport[defect.id].toReport === 0) {
                                        flag = true;
                                    }

                                    if(this.forPDF.fullReport[defect.id].toReport !== null) {
                                        flag2 = true;
                                    }
                                }
                                this.forPDF.partialReportChecklistsHeaders[item.id] = flag;
                                this.forPDF.fullReportChecklistsHeaders[item.id] = flag2;
                            }
                        }

                        let formData = new FormData();
                        // Collecting all data into FormData
                        formData.append('comment', this.comment);
                        formData.append('user_id', this.user.id);
                        formData.append('discount_percent', this.discount_percent);
                        formData.append('detail_conditions', JSON.stringify(this.selectedDetailConditions));
                        formData.append('detail_info', JSON.stringify(this.detailsInfo));
                        formData.append('detail_conclusions', JSON.stringify(this.selectedDetailConclusions));
                        formData.append('service_prices', JSON.stringify(this.servicePrices));
                        formData.append('detail_quantities', JSON.stringify(this.detailQuantities));
                        formData.append('equipment', JSON.stringify(this.selectedEquipment));
                        // Collect files
                        for(let i = 0; i < this.attachments.length; i++) {
                            formData.append('attachments[]', this.attachments[i].file);
                        }
                        this.$nextTick(() => {
                            formData.append('partialReport', 
                                `<html>
                                    <head>
                                        <style>
                                            body { font-family: DejaVu Sans }
                                            .defect-act-table {
                                                width: 100%;
                                            }
                                            .defect-act-table td, .defect-act-table th {
                                               padding: 10px;
                                                vertical-align: middle;
                                                border: 1px solid #dee2e6;
                                                font-size: 11px !important;
                                                margin: 0; 
                                                width: 10%;
                                            }
                                            .defect-act-table td p {
                                                margin: 0;
                                            }
                                            .defect-act-table-title {
                                                background-color: rgba(0, 0, 0, .05);
                                                text-align: center;
                                            }
                                            .defect-act-table-checklist-title {
                                                text-transform: uppercase;
                                                font-weight: bold;
                                                text-align: center;
                                            }
                                        </style>
                                        <body>${this.$refs.partialReport.innerHTML}</body>
                                    </head>
                                </html>`);
                            formData.append('fullReport', 
                                `<html>
                                    <head>
                                        <style>
                                            body { font-family: DejaVu Sans }
                                            .defect-act-table {
                                                width: 100%;
                                            }
                                            .defect-act-table td, .defect-act-table th {
                                               padding: 10px;
                                                vertical-align: middle;
                                                border: 1px solid #dee2e6;
                                                font-size: 11px !important;
                                                margin: 0; 
                                                width: 10%;
                                            }
                                            .defect-act-table td p {
                                                margin: 0;
                                            }
                                            .defect-act-table-title {
                                                background-color: rgba(0, 0, 0, .05);
                                                text-align: center;
                                            }
                                            .defect-act-table-checklist-title {
                                                text-transform: uppercase;
                                                font-weight: bold;
                                                text-align: center;
                                            }
                                        </style>
                                        <body>${this.$refs.fullReport.innerHTML}</body>
                                    </head>
                                </html>`);

                            // Send all the data to the server
                            axios.post(`/sto/${this.$route.params.slug}/cards/${this.cardId}/defects/acts`, formData)
                                .then(response => {
                                    console.log(response.data);
                                    this.loading.saveBtn = false;
                                    // Notification
                                    this.$store.dispatch('showSnackbar', {
                                        color: 'success',
                                        text: response.data.message,
                                        active: true
                                    });

                                    // Sending an email to the driver
                                    axios.get(`/sto/${this.$route.params.slug}/defect-acts/${response.data.act.id}/sendActFile`)
                                        .then(response => {
                                            this.$store.dispatch('showSnackbar', {
                                                color: 'success',
                                                text: response.data.message,
                                                active: true
                                            });
                                        })
                                        .catch(error => console.log(error));
                                })
                                .catch(error => console.log(error));
                        });
                        
                    } else {
                        this.loading.saveBtn = false;
                        this.$store.dispatch('showSnackbar', {
                            color: 'warning',
                            text: 'Пожалуйста заполните все поля.',
                            active: true
                        });
                    }
                });
            
        },
        onFileChanged(file) {
            this.attachments = file;
        },
        unCheckStatus(itemId) {
            this.$set(this.detailsInfo[itemId], 'toReport', null);
            this.$set(this.selectedDetailConditions, itemId, null);
            this.$set(this.selectedDetailConclusions, itemId, null);
            this.$set(this.servicePrices, itemId, null);            
        },
        unCheckCondition(itemId) {
            this.$set(this.selectedDetailConditions, itemId, null);
        },
        unCheckConclusion(itemId) {
            this.$set(this.selectedDetailConclusions, itemId, null);
            this.$set(this.servicePrices, itemId, null);
        },
        setPrice(itemId) {
            console.log(this.humanHours);
            let item = this.defectsData.filter(it => it.id === itemId)[0];
            let price = item.service_prices.filter(price => price.defect_conclusion_id === this.selectedDetailConclusions[item.id])[0].price;
            this.$set(this.servicePrices, itemId, Number(price * this.detailQuantities[itemId]));
        },
        saveHumanHours(itemId) {
            this.setHumanHours = this.setHumanHours.filter(id => id !== itemId);
            // Set price according to human hours
            let item = this.defectsData.filter(it => it.id === itemId)[0];
            let price = Number(item.service_prices.filter(price => price.defect_conclusion_id === this.selectedDetailConclusions[item.id])[0].human_hour_price);
            if(price === 0) {
                this.$store.dispatch('showSnackbar', {
                    color: 'warning',
                    text: 'Не уставновлена цена за ч/ч в справочнике "Услуги".',
                    active: true
                });
            } else {
                this.$set(this.servicePrices, itemId, Number(price * this.humanHours * this.detailQuantities[itemId]));
            }
        },
        toggleSection(itemId) {
            if(this.hiddenDefectType !== null && this.hiddenDefectType === itemId) {
                this.hiddenDefectType = null;
            } else {
                this.hiddenDefectType = itemId;
            }
        }
    },
    created() {
        this.fetchDefectsInfo();
    }
}
</script>

<style>
    .defect-act-table {
        width: 100%;
    }
    .defect-act-table td, .defect-act-table th {
        padding: 10px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        margin: 0; 
        width: 10%;
    }
    .defect-act-table td p {
        margin: 0;
    }
    .defect-act-table-title, .defect-act-table-section {
        background-color: rgba(0, 0, 0, .05);
        text-align: center;
    }
    .defect-act-table-section th {
        padding: 3px 10px;
        text-align: left;
    }
    .defect-act-table-checklist-title {
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
    }
</style>
