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
                            <template v-for="item in defectsData">
                                <tr class="defect-act-table-title" v-if="item.heading" :key="item.uuid">
                                    <th colspan="5">{{ item.defect_type_name }}</th>
                                </tr>
                                <tr v-else :key="item.uuid">
                                    <td>{{ item.defect_name }}</td>
                                    <td>
                                        <v-checkbox
                                            :error-messages="errors.collect(`condition_${item.id}`)"
                                            v-model="selectedDetailConditions[item.id]" 
                                            v-for="condition in item.defect_options"
                                            :key="condition.id"
                                            :label="condition.defect_option_name" 
                                            :value="condition.id"
                                            hide-details></v-checkbox>
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
                                    <td>
                                        <v-checkbox
                                            :error-messages="errors.collect(`conclusion_${item.id}`)"
                                            v-model="selectedDetailConclusions[item.id]" 
                                            v-for="conclusion in item.defect_conclusions"
                                            :key="conclusion.id"
                                            :label="conclusion.conclusion_name" 
                                            :value="conclusion.id"
                                            hide-details></v-checkbox>
                                    </td>
                                    <td>
                                        <v-radio-group 
                                            v-model="detailsInfo[item.id].toReport"
                                            :error-messages="errors.collect(`radio_${item.id}`)"
                                            hide-details
                                            :style="{ padding: '0' }"
                                        >
                                            <v-radio label="Выводить в отчет" :value="1"></v-radio>
                                            <v-radio label="Не выводить в отчет" :value="0"></v-radio>
                                        </v-radio-group>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody>
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
                        </tbody>
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
            }
        }
    },
    methods: {
        fetchDefectsInfo() {
            axios.get(`/sto/${this.$route.params.slug}/cars/${this.$route.params.car}/card`)
                .then(response => {
                    console.log(response);
                    let { car, defects_info, equipment } = response.data;
                    this.equipment = equipment;
                    this.cardId = car.card.id;
                    // Create an array with fake data to be filled later
                    for(let i = 0; i < defects_info.length; i++) {
                        let checklist = defects_info[i];
                        for(let j = 0; j < checklist.defects.length; j++) {
                            let detail = checklist.defects[j];
                            this.selectedDetailConditions[detail.id] = [];
                            this.selectedDetailConclusions[detail.id] = [];
                            this.detailsInfo[detail.id] = {
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
                            if(condition.length === 0) {
                                this.$validator.errors.add(`condition_${i}`, 'Поле "Состояние" обязательно для заполнения.');
                                allCheckboxesValidated = false;
                            }
                        }                        
                    }
                    // Validate conclusion checkboxes
                    for(let i = 0; i < this.selectedDetailConclusions.length; i++) {
                        let conclusion = this.selectedDetailConclusions[i];
                        if(conclusion !== undefined) {
                            if(conclusion.length === 0) {
                                this.$validator.errors.add(`conclusion_${i}`, 'Поле "Заключение" обязательно для заполнения.');
                                allCheckboxesValidated = false;
                            }
                        }
                        
                    }
                    // Validate to report status
                    for(let i = 0; i < this.detailsInfo.length; i++) {
                        let infoItem = this.detailsInfo[i];
                        if(infoItem !== undefined) {
                            if(infoItem.toReport === null) {
                                this.$validator.errors.add(`radio_${i}`, 'Поле "В отчет" обязательно для заполнения.');
                                allCheckboxesValidated = false;
                            }
                        }
                    }
                    // If validation succeed ...
                    if(success && allCheckboxesValidated) {
                        console.log('[Validation] OK!');
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
                        // Send all the data to the server
                        axios.post(`/sto/${this.$route.params.slug}/cards/${this.cardId}/defects/acts`, formData)
                            .then(response => {
                                console.log(response)
                                this.loading.saveBtn = false;
                                // Notification
                                this.$store.dispatch('showSnackbar', {
                                    color: 'success',
                                    text: response.data.message,
                                    active: true
                                });
                            })
                            .catch(error => console.log(error));
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
