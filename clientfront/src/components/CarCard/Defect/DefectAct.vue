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
                    <v-flex xs12 sm12 md10 lg6>
                        <v-btn color="success" block @click="print()">Распечатать <v-icon right dark>print</v-icon></v-btn>
                    </v-flex>
                </v-layout>
                <v-layout row justify-center>
                    <v-flex xs12 sm12 md10 lg6>
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

                                        <div></div>
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
        }
    },
    data() {
        return {
            dialog: false
        }
    },
    methods: {
        print(elem) {
            printJS({
                printable: 'defect-act',
                type: 'html',
                scanStyles: true
            });
        }
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
