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
                    <table class="defect-act-table">
                        <tbody>
                            <template v-for="(item, index) in defectsData">
                                <tr class="defect-act-table-title" :style="{ fontSize: '20px' }" v-if="index === 0" :key="index">
                                    <th colspan="5">Новый дефектный акт</th>
                                </tr>
                                <tr class="defect-act-table-title" v-if="index === 0" :key="index + 1">
                                    <th>Деталь</th>
                                    <th>Статус</th>
                                    <th>Состояние</th>
                                    <th>Заключение</th>
                                    <th>Комментарий</th>
                                </tr>
                                <tr class="defect-act-table-title" v-if="item.heading" :key="item.uuid">
                                    <th colspan="5">{{ item.defect_type_name }}</th>
                                </tr>
                                <tr v-else :key="item.uuid">
                                    <td>{{ item.defect_name }}</td>
                                    <td>
                                        <v-radio-group 
                                            v-model="detailsInfo[item.id].toReport"
                                            :error-messages="errors.collect(`radio_${item.id}`)"
                                            hide-details
                                            :style="{ padding: '0' }"
                                        >
                                            <v-radio label="Пройден" :value="1"></v-radio>
                                            <v-radio label="Не пройден" :value="0"></v-radio>
                                        </v-radio-group>
                                    </td>
                                    <td>
                                        <!-- <v-checkbox
                                            :error-messages="errors.collect(`condition_${item.id}`)"
                                            v-model="selectedDetailConditions[item.id]" 
                                            v-for="condition in item.defect_options"
                                            :key="condition.id"
                                            :label="condition.defect_option_name" 
                                            :value="condition.id"
                                            hide-details></v-checkbox> -->
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
                                    </td>                                    
                                    <td>
                                        <!-- <v-checkbox
                                            :error-messages="errors.collect(`conclusion_${item.id}`)"
                                            v-model="selectedDetailConclusions[item.id]" 
                                            v-for="conclusion in item.defect_conclusions"
                                            :key="conclusion.id"
                                            :label="conclusion.conclusion_name" 
                                            :value="conclusion.id"
                                            hide-details></v-checkbox> -->
                                            <v-radio-group 
                                                v-model="selectedDetailConclusions[item.id]"
                                                :error-messages="errors.collect(`conclusion_${item.id}`)"
                                                hide-details
                                                :style="{ padding: '0' }"
                                            >
                                                <v-radio
                                                    v-for="conclusion in item.defect_conclusions"
                                                    :key="conclusion.id"
                                                    :label="conclusion.conclusion_name" 
                                                    :value="conclusion.id"
                                                ></v-radio>
                                            </v-radio-group>
                                    </td>
                                    
                                    <td>
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
                        <!-- <tbody>
                            <tr class="defect-act-table-title">
                                <th colspan="5">Наличие</th>
                            </tr>
                            <tr v-for="eq in equipment" :key="eq.id">
                                <td colspan="2">{{ eq.equipment_type_name }}</td>
                                <td colspan="3">
                                    <v-checkbox 
                                        v-model="selectedEquipment" 
                                        :value="eq.id"
                                        label="Есть"
                                        hide-details></v-checkbox>
                                </td>
                            </tr>
                        </tbody> -->
                        <tbody>
                            <tr class="defect-act-table-title">
                                <th colspan="5">Файлы и комментарий</th>                                
                            </tr>
                            <tr>
                                <td colspan="5">
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
                            <tr>
                                <td colspan="5">
                                    <FileUpload
                                        @files-changed="onFileChanged"
                                        types="image/jpeg, image/png, image/svg+xml" />
                                </td>
                            </tr>
                        </tbody>                       
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <v-btn block color="success" :loading="loading.saveBtn" type="submit">Создать дефектный акт</v-btn>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </v-flex>
        </v-layout>        

        <v-layout row wrap v-show="false">
            <v-flex ref="partialReport">
                <table class="defect-act-table" style="font-size: 11px !important">
                    <tbody>
                        <template v-for="(item, index) in defectsData">
                            <tr class="defect-act-table-title" v-if="index === 0" :key="index">
                                <th>Деталь</th>
                                <th>Cтатус</th>
                                <th>Состояние</th>
                                <th>Заключение</th>
                                <th>Комментарий</th>
                            </tr>
                            <tr class="defect-act-table-title" v-if="item.heading && forPDF.partialReportChecklistsHeaders[item.id]" :key="item.uuid">
                                <th colspan="5">{{ item.defect_type_name }}</th>
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
                                <td>{{ forPDF.partialReport[item.id].comment }}</td>
                            </tr>
                        </template>
                    </tbody>
                    <!-- <tbody>
                        <tr class="defect-act-table-title">
                            <th colspan="5">Наличие</th>
                        </tr>
                        <tr v-for="eq in equipment" :key="eq.id">
                            <td colspan="2">{{ eq.equipment_type_name }}</td>
                            <td colspan="3">{{ selectedEquipment.includes(eq.id) ? 'Есть' : 'Нет' }}</td>
                        </tr>
                    </tbody> -->
                </table>
            </v-flex>
        </v-layout>

        <v-layout row wrap v-show="false">
            <v-flex ref="fullReport">
                <table class="defect-act-table" style="font-size: 11px !important">
                    <tbody>
                        <template v-for="(item, index) in defectsData">
                            <tr class="defect-act-table-title" v-if="index === 0" :key="index">
                                <th>Деталь</th>
                                <th>Cтатус</th>                                
                                <th>Состояние</th>
                                <th>Заключение</th>
                                <th>Комментарий</th>
                            </tr>
                            <tr class="defect-act-table-title" v-if="item.heading && forPDF.fullReportChecklistsHeaders[item.id]" :key="item.uuid">
                                <th colspan="5">{{ item.defect_type_name }}</th>
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
                                <td>{{ forPDF.fullReport[item.id].comment }}</td>
                            </tr>
                        </template>
                    </tbody>
                    <!-- <tbody>
                        <tr class="defect-act-table-title">
                            <th colspan="5">Наличие</th>
                        </tr>
                        <tr v-for="eq in equipment" :key="eq.id">
                            <td colspan="2">{{ eq.equipment_type_name }}</td>
                            <td colspan="3">{{ selectedEquipment.includes(eq.id) ? 'Есть' : 'Нет' }}</td>
                        </tr>
                    </tbody> -->
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
    data() {
        return {
            comment: '',
            attachments: [],
            cardId: null,
            defectsData: [],
            equipment: [],
            selectedDetailConditions: [],
            selectedDetailConclusions: [],
            selectedEquipment: [],
            detailsInfo: [],
            loading: {
                page: true,
                saveBtn: false,
            },

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
            axios.get(`/sto/${this.$route.params.slug}/cars/${this.$route.params.car}/card`)
                .then(response => {
                    let { car, defects_info, equipment } = response.data;
                    this.equipment = equipment;
                    this.cardId = car.card.id;
                    // Create an array with fake data to be filled later
                    for(let i = 0; i < defects_info.length; i++) {
                        let checklist = defects_info[i];
                        this.forPDF.partialReportChecklistsHeaders[checklist.id] = false;
                        for(let j = 0; j < checklist.defects.length; j++) {
                            let detail = checklist.defects[j];
                            this.selectedDetailConditions[detail.id] = null;
                            this.selectedDetailConclusions[detail.id] = null;
                            this.detailsInfo[detail.id] = {
                                comment: '',
                                toReport: null
                            };
                            // Data for PDF files
                            this.forPDF.fullReport[detail.id] = {
                                comment: '',
                                toReport: null
                            };
                            this.forPDF.partialReport[detail.id] = {
                                comment: '',
                                toReport: null
                            };
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
                        formData.append('detail_conditions', JSON.stringify(this.selectedDetailConditions));
                        formData.append('detail_info', JSON.stringify(this.detailsInfo));
                        formData.append('detail_conclusions', JSON.stringify(this.selectedDetailConclusions));
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
