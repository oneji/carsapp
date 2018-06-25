<template>
    <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition" @input="$emit('close')">
        <v-card>
            <v-toolbar dark color="primary">
                <v-btn icon dark @click.native="$emit('close')">
                    <v-icon>close</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-toolbar-items>
                    <v-btn dark flat>Сохранить</v-btn>
                </v-toolbar-items>
            </v-toolbar>
            
            <v-container grid-list-lg>
                <v-layout row justify-center>
                    <v-flex xs12 sm12 md10 lg8>
                        <v-btn color="success" block @click="print()">Распечатать <v-icon right dark>print</v-icon></v-btn>
                    </v-flex>
                </v-layout>
                <v-layout row justify-center>
                    <v-flex xs12 sm12 md10 lg8>
                            <v-card>
                                <v-card-text>
                                    <div class="defect-act" id="defect-act" ref="defectAct">
                                        <p class="display-1 text-lg-center defect-act__title">Дефектный акт автомобиля</p> 
                                        <p><strong>Номер акта:</strong> №{{ act.id | generateActNum }}</p>
                                        <p><strong>Дата осмотра автомобиля:</strong> {{ act.defect_act_date | moment("MMMM D, YYYY") }}</p> 
                                        <p> 
                                            <strong>Марка автомобиля:</strong> {{ car.brand_name }} 
                                            <strong>Модель:</strong> {{ car.model_name }}
                                            <strong>Пробег:</strong> {{ car.milage }} км.
                                        </p> 
                                        <p>
                                            <strong>Год выпуска автомобиля:</strong> {{ car.year }}
                                            <strong>VIN-CODE:</strong> {{ car.vin_code }}    
                                        </p> 
                                        <p>
                                            <strong>Гос-номер:</strong> {{ car.number }}
                                            <strong>Владелец:</strong> <span v-for="driver in car.drivers" :key="driver.id">{{ driver.fullname }},</span>
                                        </p>
                                        <p>
                                            <strong>Объем ДВС:</strong> {{ car.engine_capacity }}
                                            <strong>Тип ДВС:</strong> {{ car.engine_type_name }}
                                        </p>
                                        <p><strong>Трансмиссия:</strong> {{ car.transmission_name }}</p>
                                        <p>
                                            <strong>Наличие:
                                                <span v-for="(eq, index) in equipmentStatuses" :key="eq.id">
                                                    {{ `${index + 1}) ${eq.name} - ${eq.status}` }}
                                                </span>
                                            </strong>
                                        </p>

                                        <div v-for="(defectType, index) in defects" :key="index">
                                            <p><strong>{{ defectType.type }}</strong></p>
                                            <div v-if="defectType.options.length > 0">
                                                <p v-for="(option, i) in defectType.options" :key="option.id">
                                                    {{ i + 1 }}. {{ option.defect.defect_name }}: {{ option.defect_option_name }}
                                                </p>
                                            </div>
                                            <div v-else>
                                                <p>Нареканий нет.</p>
                                            </div>
                                        </div>
                                    </div>
                                </v-card-text>
                            </v-card>
                    </v-flex>
                </v-layout>
            </v-container>           
        </v-card>
    </v-dialog>
</template>

<script>
import printJS from 'print-js'

export default {
    props: {
        defectAct: Array,
        open: Boolean
    },
    watch: {
        open() {
            if(this.open)
                this.dialog = true;                    
            else 
                this.dialog = false;
        },        
    },
    filters: {
        generateActNum(value) {
            return (value / 10000).toString().replace('.', '');
        }
    },
    computed: {
        act() {
            return this.$store.getters.defectAct;
        },
            
        car() {
            return this.$store.getters.car;
        },

        defectTypes() {
            return this.$store.getters.defectTypes;
        },
        
        equipment() {
            return this.$store.getters.equipment;
        },

        equipmentStatuses() {
            let statuses = [];

            for(let j = 0; j < this.equipment.length; j++) {
                let status = '';
                if(this.act.equipment.length === 0) {
                    status = 'нет';        
                } else {
                    for(let i = 0; i < this.act.equipment.length; i++) {             
                        if(this.equipment[j].id === this.act.equipment[i].id) {
                            status = 'да';
                            break;
                        } else
                            status = 'нет';                
                    }
                }
                
                statuses.push({
                    'id': this.equipment[j].id,
                    'name': this.equipment[j].equipment_type_name,
                    'status': status
                });
            }

            return statuses;
        },

        defects() {
            let defects = [];               

            this.defectTypes.map(type => {
                let defectOptions = this.getDefectOptionsForDefectTypes(type.id, this.act.defect_options);
                defects.push({
                    'type': type.defect_type_name,
                    'options': defectOptions
                });
            });

            console.log(defects)

            return defects;
        },        
    },
    data() {
        return {
            dialog: false,
            equipmentIDs: []
        }
    },
    methods: {
        print(elem) {
            printJS({
                printable: 'defect-act',
                type: 'html',
                scanStyles: true
            });
        },

        getDefectOptionsForDefectTypes(defectTypeId, defectOptions) {
            let lala = defectOptions.filter(option => option.defect.defect_type_id === defectTypeId);
            return lala;
        }
    }, 
    created() {   
        
    }
}
</script>

<style>
    .defect-act {
        padding: 20px 40px;
    }
    .defect-act__title {
        margin-bottom: 40px;
    }
</style>
