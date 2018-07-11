<template>
    <div>
        <v-card class="mb-3">
            <v-card-media>
                <v-container class="py-2">
                    <v-layout align-baseline>
                        <v-flex>
                            <p class="subheading my-0">Штрафы</p>
                        </v-flex>
                        <v-flex xs2 sm1 md1 lg1 class="px-0">                        
                            <v-tooltip bottom> 
                                <v-btn slot="activator" color="success" block flat small fab class="my-0" @click="newFine.dialog = true">
                                    <v-icon>add</v-icon>
                                </v-btn>
                                <span>Добавить штраф</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-media>
            <v-divider></v-divider>
            <v-card-text primary-title class="pt-1 pb-1">
                <v-list two-line>
                    <v-alert outline transition="scale-transition" type="info" :value="true" v-if="items.length === 0">
                        Штрафов нет.
                    </v-alert>
                    <template v-for="fine in items">
                        <FineItem :key="fine.id" :item="fine" />
                    </template>
                </v-list>
            </v-card-text>
        </v-card>
        
        <!-- Add fine modal -->
        <v-dialog v-model="newFine.dialog" max-width="500">
            <form @submit.prevent="addFine" data-vv-scope="add-fine-form">
                <v-card>
                    <v-card-title class="headline">Добавить штраф</v-card-title>
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12 lg12>
                                <v-text-field v-model="newFine.fine_amount" name="fine_amount" label="Сумма штрафа" type="text" prepend-icon="attach_money"
                                    v-validate="'required'" 
                                    :error-messages="errors.collect('fine_amount')"
                                    data-vv-name="fine_amount" data-vv-as='"Сумма штрафа"'                                    
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm12 md12 lg12>
                                <v-menu
                                    ref="date"
                                    :close-on-content-click="false"
                                    v-model="menu"
                                    :nudge-right="40"
                                    :return-value.sync="newFine.fine_date"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                    min-width="290px">
                                        <v-text-field 
                                            slot="activator" 
                                            v-model="newFine.fine_date" 
                                            label="Выберите месяц штрафа" 
                                            prepend-icon="event" 
                                            readonly
                                        ></v-text-field>
                                        <v-date-picker
                                            v-model="newFine.fine_date"
                                            scrollable 
                                            locale="ru" 
                                            @input="$refs.date.save(newFine.fine_date)"
                                            type="month"
                                        ></v-date-picker>
                                </v-menu>
                            </v-flex>
                            <v-flex xs12 sm12 md12 lg12>     
                                <FileUpload @files-changed="getFineAttachments" :remove-files="newFine.removeAll" />
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat="flat" @click.native="newFine.dialog = false">Закрыть</v-btn>
                        <v-btn color="green darken-1" :loading="newFine.loading" flat="flat" type="submit">Добавить</v-btn>
                    </v-card-actions>
                </v-card>
            </form>
        </v-dialog>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import axios from '@/axios'
import snackbar from '@/components/mixins/snackbar'

import FileUpload from '@/components/FileUpload'
import FineItem from './CarCardFineItem'

export default {
    mixins: [snackbar],
    props: {
        items: {
            type: Array,
            default: () => { return [] } 
        }
    },
    components: {
        FileUpload, FineItem
    },
    data() {
        return {
            menu: false,
            newFine: {
                fine_amount: '',
                fine_date: '',
                dialog: false,
                attachments: [],
                loading: false,
                removeAll: false
            }
        }
    },
    methods: {
        addFine() {
            this.newFine.loading = true;
            let formData = new FormData();

            let fileList = [];
            this.newFine.attachments.map(value => {
                fileList.push(value.file);
            });

            formData.append('fine_amount', this.newFine.fine_amount);
            formData.append('fine_date', this.newFine.fine_date);
            
            for(let i = 0; i < fileList.length; i++) {
                formData.append('attachments[]', fileList[i]);
            }            

            axios.post(`/company/${this.$route.params.slug}/cars/${this.$store.getters.car.id}/card/${this.$store.getters.car.card.id}/fines`, formData)
                .then(response => {
                    this.items.push(response.data.fine);
                    this.newFine.loading = false;
                    this.successSnackbar(response.data.message);
                })
                .catch(error => console.log(error));
        },

        getFineAttachments(file) {
            this.newFine.attachments = file;
        }
    }
}
</script>

<style>

</style>
