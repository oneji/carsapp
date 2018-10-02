<template>
    <div>
        <v-layout style="position: relative">
            <Loading :loading="loading.page" />
        </v-layout>

        <v-layout v-if="!loading.page" row wrap>
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
                            <td colspan="1"><strong>Водитель</strong></td>
                            <td colspan="2">                                    
                                <p>{{ act.driver_id === null ? 'Водителя нет' : act.driver_name }}</p>
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
                            <td colspan="3">
                                <v-btn block color="success" @click.native="$router.back()">Вернуться назад</v-btn>
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

export default {
    mixins: [assetsURL],
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        Loading,
        MoveButtons,
        MyLabel,
        Lightbox
    },
    data() {
        return {
            act: {},
            actChecklists: [],
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
            }
        }
    },
    methods: {
        makeRequests() {
            this.loading.page = true;
            
            Promise.all([this.fetchChecklistsAndChecklistItems(), this.fetchActInfo()])
                .then(values => {
                    const { checklists } =  values[0].data;
                    const { act } = values[1].data;
                    // Check lists
                    this.act = act;
                    let files = JSON.parse(act.files) || [];

                    files.map(file => {
                        this.files.push({
                            src: this.assetsURL + '/uploads/rt_act_files/' + file.file,
                            title: file.name
                        });
                    });

                    this.actChecklists = act.checklist_items.map(item => {
                        return {
                            id: item.id,
                            item_name: item.item_name,
                            status: item.pivot.status,
                            comment: item.pivot.comment,
                            rt_act_checklist: item.rt_act_checklist
                        }
                    });

                    this.checklists = this.actChecklists.map(item => {
                        return {
                            checklist_name: item.rt_act_checklist.checklist_name,
                            id: item.rt_act_checklist.id,
                            checklist_items: []
                        }
                    });
                    
                    for(let i = 0; i < this.checklists.length; i++) {
                        for(let j = 1; j < this.checklists.length; j++) {
                            if(this.checklists[i].id === this.checklists[j].id) {
                                this.checklists.splice(j, 1);
                            }
                        }
                    }

                    this.checklists.map(list => {
                        this.actChecklists.map(item => {
                            if(item.rt_act_checklist.id === list.id) {
                                list.checklist_items.push(item);
                            }
                        });
                    });

                    this.loading.page = false;
                });
        },
        fetchChecklistsAndChecklistItems() { return axios.get('rt-act/all') },
        fetchActInfo() {
            return axios.get(`rt-act/getById/${this.$route.params.act}`)
        },
        downloadFile(file) {
            axios({
                url: `rt-act/files/download?filename=${file}`,
                method: 'GET',
                responseType: 'blob'
            }).then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = `rt-act/files/download?filename=${file}`;
                link.setAttribute('download', 'download');
                document.body.appendChild(link);
                link.click();
            }).catch(error => console.log(error))
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
