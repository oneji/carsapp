<template>
    <v-card>
        <v-card-title primary-title class="py-0 px-0">
            <v-layout>
                <v-flex>                                
                    <v-list two-line>
                        <v-list-tile avatar>
                            <v-list-tile-content>
                                <v-list-tile-title class="title mb-1">{{ item.sto_name }}</v-list-tile-title>
                                <v-list-tile-sub-title v-if="item.status === 0">
                                    Статус: <MyLabel text="Ждет принятия" type="warning" />
                                </v-list-tile-sub-title>
                                <v-list-tile-sub-title v-else-if="item.status === 1">
                                    Статус: <MyLabel text="Принята" type="success" />
                                </v-list-tile-sub-title>                                            
                                <v-list-tile-sub-title v-else-if="item.status === 2">
                                    Статус: <MyLabel text="Отклонена" type="error" />
                                </v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                        <v-divider></v-divider>
                        <v-list-tile avatar>
                            <v-list-tile-content>
                                <v-list-tile-title>Дата создания</v-list-tile-title>
                                <v-list-tile-sub-title>{{ item.created_at }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                        <v-divider v-if="item.status === 0"></v-divider>
                    </v-list>
                </v-flex>
            </v-layout>    
        </v-card-title>

        <v-card-actions v-if="item.status === 0">
            <v-btn block flat color="error" :loading="loading" @click="cancelRequest(item.id)">Отменить</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
// events: 'cancel'
import axios from '@/axios'
import MyLabel from '@/components/Label'

export default {
    props: {
        item: {
            type: Object
        } 
    },
    components: {
        MyLabel
    },
    data() {
        return {
            loading: false,
        }
    },
    methods: {
        cancelRequest(request_id) {
            this.loading = true;
            axios.delete(`/company/${this.$route.params.slug}/requests/${request_id}`)
                .then(response => {
                    this.loading = false;
                    this.$emit('cancel', request_id);
                })
                .catch(error => console.log(error));
        },
    }
}
</script>

<style>

</style>
