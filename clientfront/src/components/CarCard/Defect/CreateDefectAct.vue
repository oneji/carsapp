<template>
    <div>
        <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition" @input="$emit('close-defect-act-modal')">
            <v-card>
                <v-toolbar dark color="primary">
                    <v-btn icon dark @click.native="$emit('close-defect-act-modal')">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Создать дефектный акт</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat>Сохранить</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                
                <v-container grid-list-lg>
                    <v-layout row>
                        <v-flex xs12 sm12 md5 lg4 offset-lg4>
                            <v-card>
                                <v-card-text class="px-0 py-0">
                                    <v-tabs color="light-blue" dark show-arrows>
                                        <v-tabs-slider color="white"></v-tabs-slider>
                                        <v-tab v-for="(type, index) in defects" :key="index" ripple>{{ type.defect_type_name }}</v-tab>
                                        <v-tab-item v-for="(type, index) in defects" :key="index">
                                            <v-card flat>
                                                <v-card-text class="px-4 pb-0 pt-2">
                                                    <v-form>
                                                        <v-list three-line>
                                                            <v-list-tile v-for="defect in type.defects" :key="defect.id">
                                                                <v-list-tile-content>
                                                                    <v-list-tile-title>{{ defect.defect_name }}   </v-list-tile-title>
                                                                    <v-list-tile-sub-title>
                                                                        <v-select
                                                                            :items="selects.defectOptions[defect.id]"
                                                                            v-model="selected.defectOptions"
                                                                            label="Опция дефекта"
                                                                            single-line
                                                                            multiple                                                                
                                                                        ></v-select>
                                                                    </v-list-tile-sub-title>
                                                                </v-list-tile-content>
                                                            </v-list-tile>
                                                        </v-list>
                                                    </v-form>
                                                </v-card-text>
                                                <v-card-actions>
                                                    <v-btn color="success" block flat @click="saveDefects" :loading="loading.saveDefects">Создать</v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </v-tab-item>
                                    </v-tabs>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                        <v-flex xs12 sm12 md5 lg4>
                            <v-card>                            
                                <v-list subheader>
                                    <v-subheader>Наличие:</v-subheader>
                                    <v-list-tile avatar v-for="eq in equipment" :key="eq.id">
                                        <v-list-tile-action>
                                            <v-checkbox v-model="selected.equipment" :value="eq.id"></v-checkbox>
                                        </v-list-tile-action>
                                        <v-list-tile-content>
                                            <v-list-tile-title>{{ eq.equipment_type_name }}</v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </v-list>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-container>                
                <v-divider></v-divider>
            </v-card>
        </v-dialog>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import snackbar from '@/components/mixins/snackbar'
import axios from '@/axios'

export default {
    mixins: [snackbar],
    props: {
        openDefectActModal: Boolean,
        selects: Object,
        defects: Array,
        equipment: Array,
        cardId: [ Number, String ]
    },
    watch: {
        openDefectActModal() {
            if(this.openDefectActModal)
                this.dialog = true;
            else 
                this.dialog = false;
        },
    },
    data() {
        return {
            dialog: false,
            card_id: '',
            selected: {
                defectOptions: [],
                equipment: []
            },
            loading: {
                saveDefects: false
            },
        }
    },
    methods: {
        saveDefects() {
            this.loading.saveDefects = true;
            axios.post(`/sto/${this.$route.params.slug}/cards/${this.cardId}/defects/acts`, { 
                'defect_options': this.selected.defectOptions,
                'equipment': this.selected.equipment 
            })
            .then(response => {
                this.$emit('act-created', response.data.act);
                this.loading.saveDefects = false;
                this.snackbar.color = 'success';
                this.snackbar.text = response.data.message;
                this.snackbar.active = true;
            })
            .catch(error => console.log(error));
        },       
    },
    created() {
        
    }
};
</script>

<style>

</style>
