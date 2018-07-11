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
                
                <v-container>
                    <v-layout row justify-center>
                        <v-flex xs12 sm12 md6 lg5>
                            <DefectAct :selects="selects" :defects="defects" :selected="selected" :card-id="cardId" />
                        </v-flex>
                    </v-layout>
                </v-container>                
                <v-divider></v-divider>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import DefectAct from '@/components/CarCard/Defect/DefectAct'
import snackbar from '@/components/mixins/snackbar'

export default {
    props: {
        openDefectActModal: Boolean,
        selects: Object,
        defects: Array,
        selected: Object,
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
    components: {
        DefectAct
    },
    data() {
        return {
            dialog: false,
            notifications: false,
            sound: true,
            widgets: false,
            card_id: ''
        }
    },
    methods: {
        
    }
};
</script>

<style>
</style>
