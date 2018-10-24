<template>
    <div>
        <!-- Back button -->
        <v-layout row wrap>
            <v-flex>
                <v-btn color="success" append @click="$router.back()">Назад</v-btn>
                <v-btn color="success" v-if="act.partial_file !== null" append @click="downloadFile('partial')">Скачать PDF клиента</v-btn>
                <v-btn color="success" v-if="act.full_file !== null" append @click="downloadFile('full')">Скачать PDF</v-btn>
            </v-flex>
        </v-layout>
        <!-- Page loading spinner -->
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>
        <!-- Content -->
        <v-layout row wrap v-if="!loading.page">
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
                                <td><strong>Номер акта</strong></td>
                                <td>#{{ act.id | generateActNum }}</td>
                            </tr>
                            <tr>
                                <td><strong>Дата создания</strong></td>
                                <td>{{ act.defect_act_date | moment("MMMM D, YYYY") }}</td>
                            </tr>
                            <tr>
                                <td><strong>Комментарий</strong></td>
                                <td>{{ act.comment !== null ? act.comment : 'Комментария нет.' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="defect-act-table">
                        <tbody>
                            <template v-for="(item, index) in defectsData">  
                                <tr class="defect-act-table-title" v-if="index === 0" :key="index">
                                    <th>Деталь</th>
                                    <th>Статус</th>
                                    <th>Состояние</th>
                                    <th>Заключение</th>
                                    <th>Комментарий</th>
                                </tr>
                                <tr class="defect-act-table-title" v-if="item.heading && headersCheck[item.id]" :key="item.uuid">
                                    <th colspan="5">{{ item.defect_type_name }}</th>
                                </tr>
                                <tr v-if="!item.heading && detailsInfo[item.id].toReport !== null" :key="item.uuid">
                                    <td>{{ item.defect_name }} | {{ item.id }}</td>
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
                                </tr>
                            </template>
                        </tbody>
                        <tbody>
                            <tr class="defect-act-table-title">
                                <th colspan="5">Наличие</th>
                            </tr>
                            <tr v-for="eq in equipment" :key="eq.id">
                                <td colspan="2">{{ eq.equipment_type_name }}</td>
                                <td colspan="3">{{ selectedEquipment.includes(eq.id) ? 'Есть' : 'Нет' }}</td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr class="defect-act-table-title">
                                <th colspan="5">Файлы</th>                                
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <Lightbox
                                        id="defect_act_files"
                                        :images="attachments"
                                        :image_class="'defect_act_file'"
                                        :album_class="'my-album-class'"
                                        :options="{ history: false }" 
                                        v-if="attachments.length > 0"/>
                                    <span v-else>Файлов нет.</span>                                    
                                </td>
                            </tr>
                        </tbody>
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
import Lightbox from 'vue-simple-lightbox'
import assetsURL from '@/components/mixins/assets-url'

export default {
    mixins: [assetsURL],
    components: {
        Alert,
        Loading,
        FileUpload,
        Lightbox
    },
    filters: {
        generateActNum(value) {
            return (value / 10000).toString().replace('.', '');
        }
    },
    data() {
        return {
            act: {},
            comment: '',
            attachments: [],
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
            },
            headersCheck: []
        }
    },
    methods: {
        fetchDefectsInfo() {
            axios.get(`/sto/${this.$route.params.slug}/defect-acts/${this.$route.params.act}`)
                .then(response => {
                    // console.log(response.data);
                    let { act, equipment, defectsInfo, actDefectConditions, actDefectConclusions } = response.data;
                    this.act = act;
                    this.equipment = equipment;
                    // Get defect act's equipement ids
                    this.selectedEquipment = act.equipment.map(eq => eq.id);
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
                        let tempComments = [];
                        tempComments.push(...detail.comments);
                        this.comments[detail.id] = tempComments.filter(comment => comment.commentable_id === detail.id)[0]
                        this.detailsInfo[detail.id].toReport = detail.pivot.to_report;
                    }

                    for(let i = 0; i < this.defectsData.length; i++) {
                        let item = this.defectsData[i];
                        if(item.heading) {
                            let flag = false;
                            for(let j = 0; j < item.defects.length; j++) {
                                let defect = item.defects[j];
                                if(this.detailsInfo[defect.id].toReport !== null) {
                                    flag = true;
                                }
                            }
                            this.headersCheck[item.id] = flag;
                        }
                    }
                    
                    // Get defect act's attachments
                    if(act.attachments.length > 0) {
                        for(let i = 0; i < act.attachments.length; i++) {
                            this.attachments.push({
                                src: this.assetsURL + '/uploads/attachments/defect_acts/' + act.attachments[i].attachment,
                                title: act.attachments[i].attachment_name
                            });
                        }                        
                    }
                    // Hide page loading spinner and show content
                    this.loading.page = false;
                })
                .catch(error => console.log(error));
        },
        createDefectAct() {
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
                    // Notification
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        text: response.data.message,
                        active: true
                    });
                })
                .catch(error => console.log(error));
        },
        downloadFile(type) {
            axios({
                url: `defect-acts/files/download?id=${this.$route.params.act}&type=${type}`,
                method: 'GET',
                responseType: 'blob'
            }).then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = `${this.assetsURL}/defect-acts/files/download?id=${this.$route.params.act}&type=${type}`;
                link.setAttribute('download', 'download');
                document.body.appendChild(link);
                link.click();
            }).catch(error => console.log(error))
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
    .defect_act_file {
        margin-right: 5px;
        border: 1px solid #ccc !important;
        padding: 3px;
        width: 75px !important;
        height: 70px !important;
        border-radius: 100%;
        float: none !important;
    }
</style>
