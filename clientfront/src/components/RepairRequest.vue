<template>
    <div>
        <v-card>
            <v-card-title primary-title class="py-0 px-0">
                <v-layout>
                    <v-flex>                                
                        <v-list two-line>
                            <v-list-tile avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title class="title mb-1">{{ item.sto_name }}</v-list-tile-title>
                                    <v-list-tile-sub-title v-if="item.status === 0">
                                        Статус: <MyLabel text="На рассмотрении" type="warning" />
                                    </v-list-tile-sub-title>
                                    <v-list-tile-sub-title v-else-if="item.status === 1">
                                        Статус: <MyLabel text="В очереди" type="primary" />
                                    </v-list-tile-sub-title>                                            
                                    <v-list-tile-sub-title v-else-if="item.status === 2">
                                        Статус: <MyLabel text="Принята" type="success" />
                                    </v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider></v-divider>
                            <v-list-tile avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Дата создания</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ item.created_at | moment('MMMM D, YYYY') }} в {{ item.created_at | moment('H:mm:ss') }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider></v-divider>
                            <v-list-tile avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Комментарий</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ item.comment }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider v-if="item.receive_date !== null && item.status === 1"></v-divider>
                            <v-list-tile avatar v-if="item.receive_date !== null && item.status === 1">
                                <v-list-tile-content>
                                    <v-list-tile-title>Автомобиль должен быть в СТО</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ item.receive_date | moment('MMMM D, YYYY') }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </v-flex>
                </v-layout>    
            </v-card-title>
            <v-divider v-if="hasActions"></v-divider>
            <v-card-actions v-if="hasActions">
                <v-btn color="warning" block flat v-if="queue" @click="dialog = true">В очередь</v-btn>
            </v-card-actions>
        </v-card>

        <v-dialog v-model="dialog" max-width="500" v-if="queue">
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
                                            @input="$refs.menu.save(receiveDate)"
                                        ></v-date-picker>
                                </v-menu>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="loading.queue" flat="flat" type="submit">В очередь</v-btn>
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
    props: {
        item: Object,
        queue: {
            type: Boolean,
            default: false
        },
         
    },
    computed: {
        hasActions() {
            if(this.queue)
                return true;
            else 
                return false;
        }
    },
    components: {
        MyLabel
    },
    data() {
        return {
            loading: {
                queue: false
            },
            dialog: false,
            menu: false,
            receiveDate: ''
        }
    },
    methods: {
        addToQueue() {
            this.loading.queue = true;
            axios.post(`/sto/${this.$route.params.slug}/requests/${this.item.id}/queue`, {
                'receive_date': this.receiveDate
            })
            .then(response => {
                this.$emit('queue', response.data.message);
                this.item.status = 1;
                this.loading.queue = false;
                this.dialog = false;
            })
            .catch(error => console.error());
        }
    }
}
</script>

<style>

</style>
