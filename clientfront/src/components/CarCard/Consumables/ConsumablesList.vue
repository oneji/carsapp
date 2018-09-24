<template>
    <div>
        <v-card class="mb-3">
            <v-card-media>
                <v-container class="py-2">
                    <v-layout align-baseline>
                        <v-flex>
                            <p class="subheading my-0">Расходные материалы</p>
                        </v-flex>
                        <v-flex xs1 sm1 md1 lg1 class="px-0">                        
                            <v-tooltip bottom> 
                                <v-btn slot="activator" color="success" block flat small fab class="my-0" @click.native="dialog = true">
                                    <v-icon>add</v-icon>
                                </v-btn>
                                <span>Добавить расходный материал</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-media>
            <v-divider></v-divider>
            <v-card-text>
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="items.length === 0">
                    Расходных материалов нет.
                </v-alert>
                <v-layout row wrap>
                    <template v-for="(item, index) in items">                        
                        <ConsumablesListItem :item="item" :key="index" @show="onConsumableShowed" />                            
                    </template>
                </v-layout>
            </v-card-text>
        </v-card>

        <!-- Change consumable modal -->
        <v-dialog v-model="showDetails.dialog" max-width="500">
            <form @submit.prevent="saveConsumable" data-vv-scope="add-attachments-type-form">
                <v-card>
                    <v-card-title class="headline">{{ consumableValues.consumable_name }}</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12 sm12 md12 lg12>     
                                <v-menu
                                    ref="menu"
                                    :close-on-content-click="false"
                                    v-model="menu"
                                    :nudge-right="40"
                                    :return-value.sync="consumableValues.pivot.change_date"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width :disabled="!editConsumables"
                                    min-width="290px">
                                        <v-text-field 
                                            slot="activator" 
                                            v-model="consumableValues.pivot.change_date" 
                                            label="Дата последней замены" 
                                            prepend-icon="event" 
                                            readonly :disabled="!editConsumables"
                                        ></v-text-field>
                                        <v-date-picker 
                                            v-model="consumableValues.pivot.change_date"
                                            scrollable 
                                            locale="ru" 
                                            @input="$refs.menu.save(consumableValues.pivot.change_date)"
                                        ></v-date-picker>
                                </v-menu> 

                                <v-text-field type="text" v-model="consumableValues.pivot.change_date_milage" name="change_date_milage" label="Пробег при замене" prepend-icon="event"                 
                                    v-validate="'required'" 
                                    data-vv-name="change_date_milage" data-vv-as='"Пробег при замене"'
                                    :error-messages="errors.collect('change_date_milage')" :disabled="!editConsumables"
                                ></v-text-field>
                                
                                <v-text-field type="text" v-model="consumableValues.pivot.recommended_change_milage" name="recommended_change_milage" label="Рекомендуемая следующая замена" prepend-icon="event"                 
                                    v-validate="'required'" 
                                    data-vv-name="recommended_change_milage" data-vv-as='"Рекомендуемая следующая замена"'
                                    :error-messages="errors.collect('recommended_change_milage')" :disabled="!editConsumables"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="showDetails.dialog = false" v-if="!editConsumables">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="showDetails.loading" flat="flat" type="submit" v-if="editConsumables">Сохранить</v-btn>
                        <v-btn color="blue darken-1" flat="flat" @click.native="editConsumables = false" v-if="editConsumables">Отмена</v-btn>
                        <v-btn color="green darken-1" :loading="showDetails.loading" flat="flat" @click="editConsumables = true" v-if="!editConsumables">Изменить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <v-dialog v-model="dialog" max-width="500">
            <form @submit.prevent="addConsumable" data-vv-scope="add-attachments-type-form">
                <v-card>
                    <v-card-title class="headline">Добавить расходный материал</v-card-title>
                    <v-card-text>
                        <v-layout>
                            <v-flex xs12 sm12 md12 lg12>
                                
                                <v-text-field type="text" v-model="consumable_name" name="consumable_name" label="Название расходного матерала" prepend-icon="event"                 
                                    v-validate="'required'" 
                                    data-vv-name="consumable_name" data-vv-as='"Название расходного матерала"'
                                    :error-messages="errors.collect('consumable_name')"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="showDetails.loading" flat="flat" type="submit">Сохранить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>
    </div>
</template>

<script>
import axios from '@/axios'
import ConsumablesListItem from './ConsumablesListItem'

export default {

    props: {
        items: Array,
        privateItems: Array
    },
    components: {
        ConsumablesListItem
    },
    computed: {
        consumableValues() {
            return this.$store.getters.consumable;
        }
    },
    data() {
        return {
            showDetails: {
                dialog: false,
                loading: false
            },
            dialog: false,
            consumable_name: '',
            menu: false,
            editConsumables: false
        }
    },
    methods: {
        addConsumable() {
            axios.post(`/car/${this.$route.params.car}/consumables/create`, { consumable_name: this.consumable_name })
                .then(response => {
                    this.$emit('add', response.data);
                    this.dialog = false;
                })
                .catch(error => console.log(error));
        },

        saveConsumable() {
            this.showDetails.loading = true;
            axios.post(`/car/${this.$route.params.car}/consumables`, {
                'consumable_name': this.consumableValues.consumable_name,
                'change_date': this.consumableValues.pivot.change_date,
                'change_date_milage': this.consumableValues.pivot.change_date_milage,
                'recommended_change_milage': this.consumableValues.pivot.recommended_change_milage,
                'car_card_id': '',
                'consumable_id': this.consumableValues.id
            })
            .then(response => {
                this.$emit('change', response.data);
                this.showDetails.dialog = false;
                this.showDetails.loading = false;
                this.editConsumables = !this.editConsumables;                
            })
            .catch(error => console.log(error));
        },
        onConsumableShowed(item) {
            let hasInfo = false;

            this.privateItems.map(privateItem => {
                if(item.id === privateItem.id) {
                    hasInfo = true;
                    this.$store.dispatch('setConsumable', {
                        id: privateItem.id,
                        consumable_name: privateItem.consumable_name,
                        pivot: {
                            change_date: privateItem.pivot.change_date,
                            change_date_milage: privateItem.pivot.change_date_milage,
                            recommended_change_milage: privateItem.pivot.recommended_change_milage,
                            car_card_id: privateItem.pivot.car_card_id,
                            consumable_id: privateItem.pivot.consumable_id,
                        }
                    });
                }
            })

            if(!hasInfo) {
                this.$store.dispatch('setConsumable', {
                    id: item.id,
                    consumable_name: item.consumable_name,
                    pivot: {
                        change_date: '',
                        change_date_milage: '',
                        recommended_change_milage: '',
                        car_card_id: '',
                        consumable_id: '',
                    }
                });
            }

            this.showDetails.dialog = true;
        }
    }
}
</script>

<style>

</style>
