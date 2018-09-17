<template>
    <div>
        <v-card>
            <v-card-title primary-title class="py-0 px-0">
                <v-layout>
                    <v-flex>                                
                        <v-list two-line>
                            <!-- Title and status -->
                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title class="title mb-1">
                                        {{ forSto ? item.company_name : item.sto_name }} 
                                        <span class="body-2 red--text">#{{ item.id | generateRequestNum }}</span>
                                    </v-list-tile-title>
                                    <v-list-tile-sub-title v-if="item.status === 0">
                                        Статус: <MyLabel text="На рассмотрении" type="warning" />
                                    </v-list-tile-sub-title>
                                    <v-list-tile-sub-title v-else-if="item.status === 1">
                                        Статус: <MyLabel text="В очереди" type="primary" />
                                    </v-list-tile-sub-title>                                            
                                    <v-list-tile-sub-title v-else-if="item.status === 2">
                                        Статус: <MyLabel text="Принята" type="success" />
                                    </v-list-tile-sub-title>
                                    <v-list-tile-sub-title v-else-if="item.status === 3">
                                        Статус: <MyLabel text="Ремонт окончен" type="success" />
                                    </v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider></v-divider>
                            <!-- Car model -->
                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title>Автомобиль</v-list-tile-title>
                                    <v-list-tile-sub-title>
                                        <MyLabel :text="`${item.car.brand_name} ${item.car.model_name} (${item.car.number})`" type="success" />
                                    </v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider></v-divider>
                            <!-- Date -->
                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title>Дата создания</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ item.created_at | moment('MMMM D, YYYY') }} в {{ item.created_at | moment('H:mm:ss') }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider></v-divider>
                            <!-- Comment -->
                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title>Комментарий</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ item.comment }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider v-if="item.status === 1"></v-divider>
                            <!-- Income date -->
                            <v-list-tile v-if="item.status === 1">
                                <v-list-tile-content>
                                    <v-list-tile-title>Автомобиль должен быть в СТО</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ item.receive_date | moment('MMMM D, YYYY') }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider v-if="item.status === 3"></v-divider>
                            <!-- Repair over date -->
                            <v-list-tile v-if="item.status === 3">
                                <v-list-tile-content>
                                    <v-list-tile-title>Дата окончания ремонта</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ item.repair_date | moment('MMMM D, YYYY') }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>                            
                            <v-divider v-if="item.status === 3"></v-divider>
                            <!-- Comment -->
                            <v-list-tile v-if="item.status === 3">
                                <v-list-tile-content>
                                    <v-list-tile-title>Комментарий к ремонту</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ item.repair_comment !== null ? item.repair_comment : 'Комментария нет.' }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>                            
                        </v-list>
                    </v-flex>
                </v-layout>    
            </v-card-title>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn block flat color="primary"
                    v-if="item.car.card !== null 
                        && $route.name === 'StoRepairRequests'"
                    :to="{ name: 'StoCarCard', params: { company: item.company_id, car: item.car.id } }"
                >
                    Карточка
                </v-btn>
            </v-card-actions>
            <v-divider></v-divider>
            <v-card-actions v-if="hasActions">
                <!-- <div v-if="forSto" style="width: 100%"> -->
                    <v-btn color="warning" block flat v-if="queue && item.status === 0 && forSto" @click="dialog.queue = true">В очередь</v-btn>
                    <v-btn color="primary" block flat v-if="item.status === 1 && forSto" @click="carBrought" :loading="loading.brought">Заехал</v-btn>
                    <v-btn color="success" block flat v-if="item.status === 2 && forSto" @click="dialog.repair = true" :loading="loading.repair">Ремонт окончен</v-btn>
                <!-- </div>                 -->
                <v-btn color="primary" block flat v-if="archive && item.status === 3" @click="archiveRequest">В архив</v-btn>
                <v-tooltip bottom v-if="item.status > 0">
                    <v-btn icon slot="activator" @click="printDialog = true">
                        <v-icon>print</v-icon>
                    </v-btn>
                    <span>Распечатать</span>
                </v-tooltip>
                
            </v-card-actions>  
        </v-card>

        <!-- Add car to STO queue -->
        <v-dialog v-model="dialog.queue" max-width="500" v-if="queue">
            <form @submit.prevent="addToQueue" data-vv-scope="queue-form">
                <v-card>
                    <v-card-title class="headline">Добавить в очередь</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12 sm12 md12 lg12>     
                                <v-menu
                                    ref="menu"
                                    :close-on-content-click="false"
                                    v-model="menu"
                                    :nudge-right="40"
                                    :return-value.sync="receiveDate"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                    min-width="290px">
                                        <v-text-field 
                                            slot="activator" 
                                            v-model="receiveDate" 
                                            label="Выберите дату привоза автомобиля" 
                                            prepend-icon="event" 
                                            readonly
                                        ></v-text-field>
                                        <v-date-picker 
                                            v-model="receiveDate"
                                            scrollable 
                                            locale="ru" 
                                            :min="currentDate"
                                            @input="$refs.menu.save(receiveDate)"
                                        ></v-date-picker>
                                </v-menu>                                
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="dialog.queue = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="loading.queue" flat="flat" type="submit">В очередь</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <!-- Repair done -->
        <v-dialog v-model="dialog.repair" max-width="500" v-if="repair">
            <form @submit.prevent="repairDone" data-vv-scope="repair-form">
                <v-card>
                    <v-card-title class="headline">Завершить ремонт</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12 sm12 md12 lg12>     
                                <v-menu
                                    ref="menu2"
                                    :close-on-content-click="false"
                                    v-model="menu2"
                                    :nudge-right="40"
                                    :return-value.sync="repairDate"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                    min-width="290px">
                                        <v-text-field 
                                            slot="activator" 
                                            v-model="repairDate" 
                                            label="Выберите дату окончания ремонта" 
                                            prepend-icon="event" 
                                            readonly
                                        ></v-text-field>
                                        <v-date-picker 
                                            v-model="repairDate"
                                            scrollable 
                                            locale="ru" 
                                            :min="item.receive_date"
                                            @input="$refs.menu2.save(repairDate)"
                                        ></v-date-picker>
                                </v-menu>

                                <v-text-field type="text" v-model="currentMilage" name="current_milage" label="Текущий пробег" prepend-icon="comment"
                                ></v-text-field>
                                
                                <v-text-field type="text" v-model="repairComment" name="repair_comment" label="Комментарий" prepend-icon="comment"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="dialog.repair = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="loading.repair" flat="flat" type="submit">Завершить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <!-- Print dialog -->
        <v-dialog v-model="printDialog" max-width="500">
            <form @submit.prevent="print" data-vv-scope="repair-form">
                <v-card>
                    <v-card-title class="headline">Печать заявки</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12 sm12 md12 lg12>
                                <div id="print-block">
                                    <h3>Заявка на ремонт</h3>
                                    <p>
                                        <strong>{{ forSto ? item.company_name : item.sto_name }}</strong>
                                        <span class="body-2 red--text">#{{ item.id | generateRequestNum }}</span>
                                    </p>
                                    <p><strong>Автомобиль:</strong> {{ item.car.brand_name + '' + item.car.model_name + ' (' + item.car.number + ')' }}</p>
                                    <p><strong>Дата создания:</strong> {{ item.created_at | moment('MMMM D, YYYY') }} в {{ item.created_at | moment('H:mm:ss') }}</p>
                                    <p><strong>Комментарий:</strong> {{ item.comment }}</p>
                                    <p v-if="item.status === 1"><strong>Автомобиль должен быть в СТО:</strong> {{ item.receive_date | moment('MMMM D, YYYY') }}</p>
                                    <p v-if="item.repair_date !== null"><strong>Дата окончания ремонта:</strong> {{ item.repair_date | moment('MMMM D, YYYY') }}</p>
                                    <p v-if="drivers.length > 0"><strong>Владелец авто:</strong> <span v-for="(driver, index) in item.car.drivers" :key="driver.id + index">{{ driver.pivot.active === 1 ? driver.fullname : '' }}</span> </p>
                                    <br>
                                    <p>Заявка составлена в составе комиссии:</p>
                                    <p><strong>Механик СТО:</strong> ______________________</p>
                                    <p><strong>Специалист по дефектовке: </strong> ______________________</p>
                                    <div class="comment-table">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Комментарий</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>   
                                </div>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="printDialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="loading.repair" flat="flat" type="submit">Распечатать</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
    </div>
</template>

<script>
// events: 'cancel'
import axios from '@/axios'
import MyLabel from '@/components/Label'

export default {
    $_veeValidate: {
        validator: 'new'
    },
    props: {
        item: Object,
        queue: {
            type: Boolean,
            default: false
        },
        repair: {
            type: Boolean,
            default: false
        },
        forSto: {
            type: Boolean,
            default: false
        },
        archive: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        hasActions() {
            if(this.item.status < 3 && this.queue || this.archive)
                return true;
            else 
                return false;
        },
        drivers() {
            return this.item.car.drivers.filter(driver => driver.pivot.active === 1);
        }
    },
    filters: {
        generateRequestNum(value) {
            return (value / 10000).toString().replace('.', '');
        }
    },
    components: {
        MyLabel
    },
    data() {
        return {
            loading: {
                queue: false,
                brought: false,
                repair: false,
                archive: false
            },
            dialog: {
                queue: false,
                repair: false
            },
            menu: false,
            menu2: false,
            receiveDate: '',
            repairDate: '',
            currentMilage: '',
            repairComment: '',
            printDialog: false,
            currentDate: this.$moment().format('YYYY-MM-DD')
        }
    },
    methods: {
        addToQueue() {
            this.$validator.validateAll('queue-form')
                .then(success => {
                    if(success) {
                        this.loading.queue = true;
                        axios.post(`/sto/${this.$route.params.slug}/requests/${this.item.id}/queue`, {
                            'receive_date': this.receiveDate
                        })
                        .then(response => {
                            this.$emit('queue', response.data.message);
                            this.item.status = 1;
                            this.item.receive_date = response.data.request.receive_date.date;
                            this.loading.queue = false;
                            this.dialog.queue = false;
                        })
                        .catch(error => console.error());
                    }
                })
        },
        carBrought() {
            this.loading.brought = true;
            axios.post(`/sto/${this.$route.params.slug}/requests/${this.item.id}/sto`)
            .then(response => {
                this.$emit('bring', response.data.message);
                this.item.status = 2;
                this.loading.brought = false;
            })
            .catch(error => console.error());
        },
        repairDone() {
            this.$validator.validateAll('repair-form')
                .then(success => {
                    this.loading.repair = true;
                    axios.post(`/sto/${this.$route.params.slug}/requests/${this.item.id}/repair-done`, {
                        'repair_date': this.repairDate,
                        'repair_comment': this.repairComment,
                        'current_milage': this.currentMilage
                    })
                    .then(response => {
                        this.$emit('repair', response.data.message);
                        this.item.status = 3;
                        this.item.repair_date = response.data.request.repair_date.date;
                        this.loading.repair = false;
                        this.dialog.repair = false;
                    })
                    .catch(error => console.error());
                })
        },
        archiveRequest() {
            this.loading.archive = true;
            axios.post(`/company/${this.$route.params.slug}/requests/${this.item.id}/archive`)
            .then(response => {
                this.$emit('archive', response.data);
                this.item.archived = 1;
                this.loading.repair = false;
            })
            .catch(error => console.error());
        },
        print() {
            printJS({
                printable: 'print-block',
                type: 'html',
                scanStyles: true
            });
        }
    }
}
</script>

<style>
    .table {
        border: 1px solid rgba(0,0,0,.12);
    }

    .table tr {
        height: 200px;
    }
</style>
