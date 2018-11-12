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
        <v-layout row wrap v-if="!loading.page">
            <v-flex xs12 sm12 md12 lg12>
                <form @submit.prevent="createDefectAct" data-vv-scope="create-done-act-form" :style="{ fontSize: '14px' }">
                    <table class="done-act-table">
                        <thead>
                            <tr class="done-act-table-title">
                                <th colspan="2"><h2>Акт выполненных работ</h2></th>
                            </tr>
                        </thead>
                    </table>
                    <table class="done-act-table">
                        <tbody>
                            <template v-for="(item, index) in defectsData">  
                                <tr class="done-act-table-title" v-if="index === 0" :key="index">
                                    <th>Деталь</th>
                                    <th>Статус</th>
                                    <th>Состояние</th>
                                    <th>Заключение</th>
                                    <th>Комментарий</th>
                                    <th></th>
                                </tr>
                                <tr class="done-act-table-title" v-if="item.heading && headersCheck[item.id]" :key="item.uuid">
                                    <th colspan="7">{{ item.defect_type_name }}</th>
                                </tr>
                                <tr v-if="!item.heading && detailsInfo[item.id].toReport !== null" :key="item.uuid">
                                    <td>{{ item.defect_name }}</td>
                                    <td>
                                        {{ detailsInfo[item.id].toReport === 1 ? 'Пройден' : 'Не пройден' }}
                                    </td>
                                    <td>
                                        <p v-for="condition in item.defect_options" :key="condition.id">
                                            {{ 
                                                selectedDetailConditions[item.id].includes(condition.id) 
                                                ? condition.defect_option_name
                                                : ''
                                            }}
                                        </p>
                                    </td>
                                    <td>
                                        <p v-for="conclusion in item.defect_conclusions" :key="conclusion.id">
                                            {{ 
                                                selectedDetailConclusions[item.id].includes(conclusion.id) 
                                                ? conclusion.conclusion_name
                                                : ''
                                            }}
                                        </p>
                                    </td>
                                    <td>{{ comments[item.id] !== undefined ? comments[item.id].body : 'Комментария нет.' }}</td>                                    
                                    <td>
                                        <MyLabel 
                                            :type="markedDetails.includes(item.id) ? 'success' : 'error'" 
                                            :text="markedDetails.includes(item.id) ? 'Выполнено' : 'Не выполнено'" />
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody>
                            <tr class="defect-act-table-title">
                                <th colspan="7">Итоговая цена ремонта: {{ totalPrice }} сом.</th>
                            </tr>
                        </tbody>
                        <tfoot v-if="act.sent === 1 && act.confirmed === 0 && act.closed === 0">
                            <!-- <tr>
                                <th colspan="6" class="done-act-table-checklist-title">Добавить файлы</th>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <FileUpload 
                                        @files-changed="onFileChanged" 
                                        types="image/jpeg, image/png, image/svg+xml" />
                                </td>
                            </tr> -->
                            <tr>
                                <td colspan="6">
                                    <v-btn block color="success" :loading="loading.saveBtn" @click="confirmAndCloseDoneAct">Подтвердить и закрыть</v-btn>
                                </td>
                            </tr>
                        </tfoot>
                        <tfoot v-else>
                            <tr class="done-act-table-title">
                                <th colspan="6"><h3>Акт подтвержден и закрыт.</h3></th>
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
import Loading from '@/components/Loading'
import FileUpload from '@/components/FileUpload'
import MyLabel from '@/components/Label'

export default {
    components: {
        Loading,
        FileUpload,
        MyLabel
    },
    computed: {
        totalPrice() {
            let totalPrice = 0;
            let defects = this.act.defects.map(defect => defect.id);

            for(let i = 0; i < this.actDefectConclusions.length; i++) {
                let conclusion = this.actDefectConclusions[i];
                if(defects.includes(conclusion.defect_id)) {
                    totalPrice += Number(conclusion.service_price);
                }
            }

            return totalPrice;
        }
    },
    data() {
        return {
            actDefectConclusions: [],
            act: {},
            car: {
                drivers: []
            },
            toNewAct: [],
            company: {},
            comment: '',
            attachments: [],
            moreAttachments: [],
            cardId: null,
            defectsData: [],
            equipment: [],
            equipmentIds: [],
            detailConditionsIds: [],
            selectedDetailConditions: [],
            selectedDetailConclusions: [],
            selectedEquipment: [],
            detailsInfo: [],
            comments: [],
            loading: {
                page: true,
                saveBtn: false,
                addMoreFiles: false,
                newAct: false
            },
            headersCheck: [],
            markedDetails: []
        }
    },
    methods: {
        fetchActInfo() {
            axios.get(`done-acts/${this.$route.params.act}`)
                .then(response => {
                    let { act, defectsInfo, actDefectConditions, actDefectConclusions } = response.data;
                    this.act = act;
                    this.actDefectConclusions = actDefectConclusions;
                    // Determine marked details
                    this.markedDetails = act.defects.map(defect => {
                        if(defect.pivot.done === 1) {
                            return defect.id;
                        }
                    });
                    // Parse defect checklists and merge all into one array
                    for(let i = 0; i < defectsInfo.length; i++) {
                        // If heading = true -> checklist, else -> detail
                        let checklist = defectsInfo[i];
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
                    // Create an array with fake data to be filled later
                    for(let i = 0; i < defectsInfo.length; i++) {
                        let checklist = defectsInfo[i];
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
                    // Get defect act's defect conditions and conslusions
                    for(let i = 0; i < this.defectsData.length; i++) {
                        let detail = this.defectsData[i];
                        if(!detail.heading) {
                            this.selectedDetailConditions[detail.id] = actDefectConditions.map(condition => {
                                if(condition.defect_id === detail.id) return condition.id;
                            });
                            this.selectedDetailConclusions[detail.id] = actDefectConclusions.map(conclusion => {
                                if(conclusion.defect_id === detail.id) return conclusion.id;
                            });
                        }
                    }
                    
                    // Get comments and to report status
                    for(let i = 0; i < act.defects.length; i++) {
                        let detail = act.defects[i];
                        // let tempComments = [];
                        // tempComments.push(...detail.comments);
                        // this.comments[detail.id] = tempComments.filter(comment => comment.commentable_id === detail.id)[0]
                        this.detailsInfo[detail.id].toReport = detail.pivot.to_report;
                    }
                    
                    this.loading.page = false;
                })
                .catch(error => console.log(error));
        },
        onFileChanged(file) {
            this.attachments = file;
        },
        confirmAndCloseDoneAct() {
            this.loading.saveBtn = true;

            axios.put(`done-acts/${this.$route.params.act}/confirmAndClose`)
                .then(response => {
                    this.act.confirmed = 1;
                    this.act.closed = 1;
                    this.loading.saveBtn = false;
                    // Notification
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        text: response.data.message,
                        active: true
                    });
                })
                .catch(error => console.log(error));
        }
    },
    created() {
        this.fetchActInfo();
    },
}
</script>

<style>
    .done-act-table {
        width: 100%;
    }
    .done-act-table td, .done-act-table th {
        padding: 10px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        margin: 0; 
        width: 10%;
    }
    .done-act-table td p {
        margin: 0;
    }
    .done-act-table-title {
        background-color: rgba(0, 0, 0, .05);
        text-align: center;
    }
    .done-act-table-checklist-title {
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
    }
    .done_act_file {
        margin-right: 5px;
        border: 1px solid #ccc !important;
        padding: 3px;
        width: 75px !important;
        height: 70px !important;
        border-radius: 100%;
        float: none !important;
    }
</style>
