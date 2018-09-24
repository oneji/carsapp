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
                            <td colspan="1">Тип акта</td>
                            <td colspan="2">{{ act.type === 0 ? 'Приём' : 'Передача' }}</td>
                        </tr>
                        <tr>
                            <td colspan="1">От</td>
                            <td colspan="2">{{ act.company_from }}</td>
                        </tr>
                        <tr>
                            <td colspan="1">Ответственный сотрудник</td>
                            <td colspan="2">{{ act.responsible_employee }}</td>
                        </tr>
                        <tr>
                            <td colspan="1">Кому</td>
                            <td colspan="2">{{ act.company_to }}</td>
                        </tr>
                        <tr>
                            <td colspan="1">Водитель</td>
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
                                                {{ 
                                                    item.status === 0 ? 'Пройден' : 'Не пройден' 
                                                }}
                                            </td>
                                            <td>
                                                {{
                                                    item.comment                                                         
                                                }}
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
            act: {},
            actChecklists: [],
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
            
            Promise.all([this.fetchChecklistsAndChecklistItems(), this.fetchActInfo()])
                .then(values => {
                    console.log(values);
                    const { checklists } =  values[0].data;
                    const { act } = values[1].data;
                    // Check lists
                    this.act = act;
                    this.actChecklists = act.checklist_items.map(item => {
                        return {
                            id: item.id,
                            item_name: item.item_name,
                            status: item.pivot.status,
                            comment: item.pivot.comment,
                            rt_act_checklist: item.rt_act_checklist
                        }
                    });

                    this.checklists = act.checklist_items.map(item => { 
                        return {
                            checklist_name: item.rt_act_checklist.checklist_name,
                            id: item.rt_act_checklist.id,
                            checklist_items: []
                        }
                    });

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
