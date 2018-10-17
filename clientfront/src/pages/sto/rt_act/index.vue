<template>
    <div>
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>

        <v-layout v-if="!loading.page" row wrap>
            <v-flex xs12 sm12 md12 lg12>
                <v-btn color="success" @click.native="$router.back()">Вернуться назад</v-btn>
                <a v-if="act.rt_act_file !== null" class="btn success" :href="`${assetsURL}/api/rt-act/files/download?id=${$route.params.act}`" download="download">
                    <div class="btn__content">Скачать PDF</div>
                </a>
                <!-- <v-btn color="primary" @click="addMoreFilesDialog = true">Добавить вложение</v-btn> -->
            </v-flex>
            
            <v-flex xs12 sm12 md12 lg12>   
                <table class="rt-act" :style="{ paddingBottom: '20px', marginBottom: '20px', borderBottom: '1px solid #e6e6e6' }">
                    <thead>
                        <tr class="rt-act-title">
                            <th colspan="3"><h2>Акт приёма передачи автомобиля</h2></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="1"><strong>Тип акта</strong></td>
                            <td colspan="2">{{ act.type === 0 ? 'Приём' : 'Передача' }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>От</strong></td>
                            <td colspan="2">{{ act.company_from }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Ответственный сотрудник</strong></td>
                            <td colspan="2">{{ act.responsible_employee }}</td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Кому</strong></td>
                            <td colspan="2">{{ act.company_to }}</td>
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
                                <p>{{ act.driver_id === null ? 'Водителя нет' : act.driver_name }}</p>
                            </td>
                        </tr>                        
                        <tr>
                            <td colspan="1"><strong>Акт создал</strong></td>
                            <td colspan="2">                                    
                                <p>{{ act.created_by_name }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1"><strong>Дата создания акта</strong></td>
                            <td colspan="2">                                    
                                <p>{{ act.created_at | moment("MMMM D, YYYY") }}</p>
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
                                            <th>Наименование части осмотра</th>
                                            <th>Чек лист</th>
                                            <th>Комментарии</th>
                                        </tr>
                                        <tr>
                                            <th class="rt-act-checklist-title" colspan="3">{{ checklist.checklist_name }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in checklist.checklist_items" :key="item.id">
                                            <td colspan="1"><strong>{{ item.item_name }}</strong></td>
                                            <td>{{ item.status === 0 ? 'Пройден' : 'Не пройден' }}</td>
                                            <td>{{ item.comment !== '' ? item.comment : 'Комментария нет.' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr v-if="files.length > 0">
                            <th colspan="3" class="rt-act-checklist-title">Файлы</th>
                        </tr>
                        <tr v-if="files.length > 0">
                            <td colspan="3">
                                <Lightbox
                                    id="rt_act_files"
                                    :images="files"
                                    :image_class="'rt_act_file'"
                                    :album_class="'my-album-class'"
                                    :options="{ history: false }" />
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3" class="rt-act-checklist-title">Добавить файлы</th>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <FileUpload 
                                    @files-changed="onFileChanged" 
                                    types="image/jpeg, image/png, image/svg+xml" />
                                <v-btn color="success" block @click="addMoreFiles">Добавить файлы</v-btn>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </v-flex>
        </v-layout>
    </div>    
</template>

<script>
import axios from '@/axios'
import assetsURL from '@/components/mixins/assets-url'
import Loading from '@/components/Loading'
import MoveButtons from '@/components/MoveButtons'
import MyLabel from '@/components/Label'
import Lightbox from 'vue-simple-lightbox'
import FileUpload from '@/components/FileUpload'

export default {
    mixins: [assetsURL],
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        Loading,
        MoveButtons,
        MyLabel,
        Lightbox,
        FileUpload
    },
    data() {
        return {
            act: {
                rt_act_file: null
            },
            car: {},
            type: 0,
            companyFromSelect: '',
            companyFromText: '',
            responsibleEmployee: '',
            companyTo: '',
            driver: null,
            comments: [],
            files: [],
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
            },
            addMoreFilesDialog: false,
            addMoreFilesLoading: false,
            moreFiles: []
        }
    },
    methods: {
        makeRequests() {
            this.loading.page = true;
            
            Promise.all([this.fetchChecklistsAndChecklistItems(), this.fetchActInfo()])
                .then(values => {
                    const { checklists } =  values[0].data;
                    const { act, car } = values[1].data;
                    // Check lists
                    this.act = act;
                    this.car = car;
                    let files = JSON.parse(act.files) || [];

                    if(files.length > 0) {
                        for(let i = 0; i < files.length; i++) {
                            this.files.push({
                                src: this.assetsURL + '/uploads/rt_act_files/' + files[i].file,
                                title: files[i].name
                            });
                        }                        
                    }
                    
                    this.checklists = act.checklist_items.map(item => {
                        return {
                            checklist_name: item.rt_act_checklist.checklist_name,
                            id: item.rt_act_checklist.id,
                            checklist_items: []
                        }
                    });

                    for(let i = 0; i < this.checklists.length; i++) {
                        for(let j = 1; j < this.checklists.length; j++) {
                            if(this.checklists[i].id === this.checklists[j].id && i !== j) {
                                this.checklists.splice(j, 1);
                            }
                        }
                    }

                    for(let i = 0; i < this.checklists.length; i++) {
                        let list = this.checklists[i];
                        for(let j = 0; j < act.checklist_items.length; j++) {
                            let item = act.checklist_items[j];
                            if(item.rt_act_checklist.id === list.id) {
                                list.checklist_items.push({
                                    id: item.id,
                                    item_name: item.item_name,
                                    status: item.pivot.status,
                                    comment: item.pivot.comment,
                                });
                            }
                        }
                    }

                    this.loading.page = false;
                });
        },
        fetchChecklistsAndChecklistItems() { return axios.get('rt-act/all') },
        fetchActInfo() {
            return axios.get(`rt-act/getById/${this.$route.params.act}`)
        },
        downloadFile(file) {
            axios({
                url: `rt-act/files/download?id=${this.$route.params.act}`,
                method: 'GET',
                responseType: 'blob'
            }).then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = `${this.assetsURL}/rt-act/files/download?id=${this.$route.params.act}`;
                link.setAttribute('download', 'download');
                document.body.appendChild(link);
                link.click();
            }).catch(error => console.log(error))
        },
        onFileChanged(file) {
            this.moreFiles = file;
        },
        addMoreFiles() {
            let formData = new FormData();
            // Add files to a FormData
            for(let i = 0; i < this.moreFiles.length; i++) {
                formData.append('attachments[]', this.moreFiles[i].file);
            }
            // Send files to the server
            axios.post(`rt-act/${this.$route.params.act}/files/add`, formData)
                .then(response => {
                    console.log(response)
                    this.$store.dispatch('showSnackbar', {
                        color: 'success',
                        active: true,
                        text: response.data.message
                    });
                })
                .catch(error => console.log(error));
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
    .rt_act_file {
        margin-right: 5px;
        border: 1px solid #ccc !important;
        padding: 3px;
        width: 75px !important;
        height: 70px !important;
        border-radius: 100%;
        float: none !important;
    }
</style>
