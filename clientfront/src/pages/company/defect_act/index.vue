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
                                <td colspan="1">{{ act.created_at | moment('MMMM DD, YYYY') }}</td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Акт составил</strong></td>
                                <td colspan="1">{{ act.username }}</td>
                            </tr>
                            <tr>
                                <td colspan="1"><strong>Комментарий</strong></td>
                                <td colspan="1">{{ act.comment !== null ? act.comment : 'Комментария нет.' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="defect-act-table">
                        <tbody>
                            <template v-for="(item, index) in defectsData">
                                <tr class="defect-act-table-title" v-if="item.heading && headersCheck[item.id]" :key="item.uuid">
                                    <th colspan="7">{{ item.defect_type_name }}</th>
                                </tr>
                                <tr class="defect-act-table-section" v-if="item.heading && headersCheck[item.id]" :key="index">
                                    <th>#</th>
                                    <th>Деталь</th>
                                    <th>Статус</th>
                                    <th>Состояние</th>
                                    <th>Заключение</th>
                                    <th>Цена за ремонт</th>
                                    <th>Комментарий</th>
                                </tr>
                                <tr v-if="!item.heading && detailsInfo[item.id].toReport !== null" :key="item.uuid">
                                    <td>
                                        <v-checkbox v-model="toNewAct" :value="item.id" hide-details></v-checkbox>
                                    </td>
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
                                    <td>{{ actDefectConclusions.filter(conc => conc.defect_id === item.id)[0] !== undefined 
                                            ? actDefectConclusions.filter(conc => conc.defect_id === item.id)[0].service_price + ' сом.'
                                            : null }}</td>
                                    <td>{{ comments[item.id] !== undefined ? comments[item.id].body : 'Комментария нет.' }}</td>                                    
                                </tr>
                            </template>
                        </tbody>
                        <tbody>
                            <tr class="defect-act-table-title">
                                <th colspan="7">Итоговая цена ремонта: {{ totalPrice }} сом.</th>
                            </tr>
                            <tr class="defect-act-table-title">
                                <th colspan="7">Файлы</th>                                
                            </tr>
                            <tr>
                                <td colspan="7">
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
                        <tfoot>
                            <tr>
                                <th colspan="7" class="defect-act-table-checklist-title">Добавить файлы</th>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <FileUpload 
                                        @files-changed="onFileChanged" 
                                        types="image/jpeg, image/png, image/svg+xml" />
                                    <v-btn color="success" block :loading="loading.addMoreFiles" @click="addMoreFiles">Добавить файлы</v-btn>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <v-btn color="primary" block :loading="loading.newAct" @click="createNewAct">Подтверждаю</v-btn>
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
import Lightbox from 'vue-simple-lightbox'
import assetsURL from '@/components/mixins/assets-url'
import config from '@/config'

export default {
    mixins: [assetsURL],
    components: {
        Alert,
        Loading,
        FileUpload,
        Lightbox
    },
    computed: {
        totalPrice() {
            let totalPrice = 0;

            for(let i = 0; i < this.actDefectConclusions.length; i++) {
                let conclusion = this.actDefectConclusions[i];
                if(this.toNewAct.includes(conclusion.defect_id)) {
                    totalPrice += Number(conclusion.service_price);
                }
            }

            return totalPrice; 
        }
    },
    filters: {
        generateActNum(value) {
            return (value / 10000).toString().replace('.', '');
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
            headersCheck: []
        }
    },
    methods: {
        fetchDefectsInfo() {
            axios.get(`/sto/${this.$route.params.slug}/defect-acts/${this.$route.params.act}`)
                .then(response => {
                    let { 
                        act, 
                        equipment, 
                        defectsInfo, 
                        actDefectConditions, 
                        actDefectConclusions,
                        car 
                    } = response.data;
                    console.log(act);
                    this.$store.dispatch('setDefectAct', act);
                    this.act = act;
                    this.actDefectConclusions = actDefectConclusions;
                    this.car = car;
                    this.company = car.companies[0];
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
                link.href = `${config.apiURL}/defect-acts/files/download?id=${this.$route.params.act}&type=${type}`;
                link.setAttribute('download', 'download');
                document.body.appendChild(link);
                link.click();
            }).catch(error => console.log(error))
        },
        onFileChanged(file) {
            this.moreAttachments = file;
        },
        addMoreFiles() {
            if(this.moreAttachments.length > 0) {

                this.loading.addMoreFiles = true;
                let formData = new FormData();
                // Add files to a FormData
                for(let i = 0; i < this.moreAttachments.length; i++) {
                    formData.append('attachments[]', this.moreAttachments[i].file);
                }
                // Send files to the server
                axios.post(`/sto/${this.$route.params.slug}/defect-acts/${this.$route.params.act}/files/add`, formData)
                    .then(response => {
                        let { attachments } = response.data;
                        // Update defect act's attachments
                        this.attachments = [];                    
                        for(let i = 0; i < attachments.length; i++) {
                            this.attachments.push({
                                src: this.assetsURL + '/uploads/attachments/defect_acts/' + attachments[i].attachment,
                                title: attachments[i].attachment_name
                            });
                        }                        
                        this.loading.addMoreFiles = false;
                        this.$store.dispatch('showSnackbar', {
                            color: 'success',
                            active: true,
                            text: response.data.message
                        });
                    })
                    .catch(error => console.log(error));
            } else {
                this.$store.dispatch('showSnackbar', {
                    color: 'warning',
                    active: true,
                    text: 'Выберите хотя бы один файл.'
                });
            }
        },
        createNewAct() {
            if(this.toNewAct.length === 0) {
                this.$store.dispatch('showSnackbar', {
                    color: 'warning',
                    text: 'Выберите хотя бы одну деталь на ремонт.',
                    active: true
                });
            } else {
                this.loading.newAct = true;
                axios.post(`done-acts`, {
                    'defect_act_id': this.$route.params.act,
                    'car_card_id': this.$store.getters.defectAct.car_card_id,
                    'user_id': JSON.parse(window.localStorage.user).id,
                    'defects': this.toNewAct
                })
                .then(response => {
                    console.log(response)
                    this.loading.newAct = false;
                    // Notification
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        text: response.data.message,
                        active: true
                    });
                })
                .catch(error => console.log(error));
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
