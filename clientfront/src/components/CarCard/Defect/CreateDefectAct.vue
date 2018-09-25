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
                        <v-btn dark flat @click="saveDefects" :loading="loading.saveDefects">Сохранить</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                
                <v-container grid-list-lg>
                    <v-layout row wrap>
                        <!-- Defect options -->
                        <!-- <v-flex xs12 sm12 md5 lg4>
                            <v-card v-for="defectType in defects" :key="defectType.id" :class="['mb-1', 'defect-type', { 'active': activeDefectType === defectType.id }]">
                                <v-card-text @click="getDefectsFromDefectType(defectType.id)">
                                    {{ defectType.defect_type_name }}
                                </v-card-text>
                            </v-card>

                            <v-card class="mt-3">                            
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

                        <v-flex xs12 sm12 md5 lg4>
                            <v-card>
                                <v-card-text class="py-2 px-2">
                                    <v-form v-if="currentDefectType.defects.length > 0">
                                        <v-list three-line>
                                            <v-list-tile v-for="defect in currentDefectType.defects" :key="defect.id">                                                
                                                <v-list-tile-content >
                                                    <v-list-tile-title>{{ defect.defect_name }}</v-list-tile-title>
                                                    <v-list-tile-sub-title>
                                                        <v-select
                                                            :items="selects.defectOptions[defect.id]"
                                                            v-model="selected.defectOptions"
                                                            label="Опция дефекта"
                                                            single-line
                                                            multiple 
                                                            hide-details                                                               
                                                        ></v-select>
                                                    </v-list-tile-sub-title>
                                                </v-list-tile-content>
                                            </v-list-tile>
                                        </v-list>
                                    </v-form>

                                    <Alert v-else type="info" message="Здесь пусто." />
                                </v-card-text>
                            </v-card>
                        </v-flex> -->
                        
                        <!-- Defect act img -->
                        <v-flex xs12 sm12 lg4>
                            <v-card>
                                <v-card-media>
                                    <v-container>
                                        <v-layout>
                                            <v-flex>
                                                <p class="subheading my-0">Фото</p>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-card-media>
                                <v-divider></v-divider>
                                <v-card-media :src="img.url ? img.url : '/static/images/no-photo.png'" height="200px"></v-card-media>
                                <v-divider></v-divider>
                                <v-card-actions>
                                    <v-container class="pb-0 pt-3">
                                        <v-layout row wrap>
                                            <v-flex xs12 class="py-0 px-0">
                                                <v-text-field 
                                                    label="Выберите фото" 
                                                    @click="pickFile" 
                                                    v-model="img.name" 
                                                    prepend-icon="attach_file" 
                                                    append-icon="delete" 
                                                    :append-icon-cb="deleteImage"
                                                    hide-details
                                                ></v-text-field>
                                                <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                                            </v-flex>
                                            
                                            <v-flex xs12 class="py-0 px-0">                                                
                                                <v-text-field
                                                    name="defect_act_comment"
                                                    label="Введите комментарий"
                                                    multi-line 
                                                    clearable
                                                    no-resize
                                                    v-model="comment"
                                                    rows="3"
                                                    prepend-icon="comment"
                                                ></v-text-field>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-card-actions>
                            </v-card>
                        </v-flex>

                        <v-flex xs12 sm12 md5 lg4>
                            <v-expansion-panel>
                                <v-expansion-panel-content v-for="defectType in defects" :key="defectType.id">
                                    <div slot="header">{{ defectType.defect_type_name }}</div>
                                    <v-card>
                                        <v-card-text class="py-2 px-2">
                                            <v-form v-if="defectType.defects.length > 0">
                                                <v-list three-line>
                                                    <v-list-tile v-for="defect in defectType.defects" :key="defect.id">                                                
                                                        <v-list-tile-content >
                                                            <v-list-tile-title>{{ defect.defect_name }}</v-list-tile-title>
                                                            <v-list-tile-sub-title>
                                                                <v-select
                                                                    :items="selects.defectOptions[defect.id]"
                                                                    v-model="selected.defectOptions"
                                                                    label="Опция дефекта"
                                                                    single-line
                                                                    multiple 
                                                                    hide-details                                                               
                                                                ></v-select>
                                                            </v-list-tile-sub-title>
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                </v-list>
                                            </v-form>

                                            <Alert v-else type="info" message="Здесь пусто." />
                                        </v-card-text>
                                    </v-card>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
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
import Alert from '@/components/Alert'

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
    components: {
        Alert
    },
    data() {
        return {
            dialog: false,
            card_id: '',
            comment: '',
            img: {
                    name: '',
                    file: '',
                    url: ''
                },
            selected: {
                defectOptions: [],
                equipment: []
            },
            loading: {
                saveDefects: false
            },
            currentDefectType: this.defects[0],
            activeDefectType: this.defects[0].id
        }
    },
    methods: {
        saveDefects() {
            this.loading.saveDefects = true;
            let formData = new FormData();
            formData.append('defect_act_img', this.img.file);
            
            for(let i = 0; i < this.selected.defectOptions.length; i++)
                formData.append('defect_options[]', this.selected.defectOptions[i]);
            
            for(let i = 0; i < this.selected.equipment.length; i++)
                formData.append('equipment[]', this.selected.equipment[i]);

            axios.post(`/sto/${this.$route.params.slug}/cards/${this.cardId}/defects/acts`, formData)
                .then(response => {
                    this.$emit('act-created', response.data.act);
                    this.loading.saveDefects = false;
                    this.snackbar.color = 'success';
                    this.snackbar.text = response.data.message;
                    this.snackbar.active = true;
                })
                .catch(error => console.log(error));
        },   

        getDefectsFromDefectType(defectTypeId) {
            this.activeDefectType = defectTypeId;
            this.currentDefectType = this.defects.filter(type => type.id === defectTypeId)[0];
        },

        pickFile () {
            this.$refs.image.click();
        },

        onFilePicked (e) {
            const files = e.target.files;

            if(files[0] !== undefined) {
                this.img.name = files[0].name;

                if(this.img.name.lastIndexOf('.') <= 0) {
                    return
                }

                const fr = new FileReader();
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.img.url = fr.result;
                    this.img.file = files[0];
                })
            } else {
                this.img.url = '';
                this.img.name = '';
                this.img.file = '';
            }
        },

        deleteImage() {
            this.img.url = '';
            this.img.name = '';
            this.img.file = '';
        },    
    }
};
</script>

<style>
    .defect-type {
        cursor: pointer;
        transition: .3s all ease;
        border: 1px solid transparent;
    }

    .defect-type.active {
        border: 1px solid rgb(24, 144, 255);        
    }

    .defect-type:hover {
        border: 1px solid rgb(24, 144, 255);
    }
</style>
