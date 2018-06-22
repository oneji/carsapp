<template>
    <div>
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

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
            {{ snackbar.text }}
            <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import axios from '@/axios'
import snackbar from '@/components/mixins/snackbar'

export default {
    mixins: [snackbar],
    props: {
        selects: Object,
        defects: Array,
        cardId: [ Number, String ]
    },
    data() {
        return {
            loading: {
                saveDefects: false
            },
            selected: {
                defectOptions: []
            }
        }
    },
    methods: {
        saveDefects() {
            this.loading.saveDefects = true;
            axios.post(`/sto/${this.$route.params.slug}/cards/${this.cardId}/defects/acts`, { 
                'defect_options': this.selected.defectOptions 
            })
            .then(response => {
                console.log(response);
                this.$emit('added', response.data.act);
                this.loading.saveDefects = false;
                this.snackbar.color = 'success';
                this.snackbar.text = response.data.message;
                this.snackbar.active = true;
            })
            .catch(error => console.log(error));
        },
    },
    created() {
        console.log(this.selected.defectOptions);
        console.log(this.selects.defectOptions);
    }
}
</script>

<style>

</style>
