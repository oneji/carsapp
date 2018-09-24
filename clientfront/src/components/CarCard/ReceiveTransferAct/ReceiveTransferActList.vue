<template>
    <div>
        <v-card class="mb-3">
            <v-card-media>
                <v-container>
                    <v-layout>
                        <v-flex>
                            <p class="subheading my-0">Акты приема передачи</p>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-media>
            <v-divider></v-divider>
            <v-card-text primary-title class="py-1 px-1">
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="items.length === 0">
                    Актов нет.
                </v-alert>
                <v-list two-line v-else>                    
                    <template v-for="item in items" >
                        <v-list-tile :key="item.title" avatar class="mb-3">
                            <v-list-tile-avatar>
                                <v-icon class="blue white--text">assignment</v-icon>
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title>Акт {{ item.type === 0 ? 'приёма' : 'передачи' }}</v-list-tile-title>
                                <v-list-tile-sub-title>Ответственный: {{ item.responsible_employee }}</v-list-tile-sub-title>
                                <v-list-tile-sub-title>От {{ item.company_from }} к {{ item.company_to }}</v-list-tile-sub-title>
                                <v-list-tile-sub-title>
                                    Дата создания: {{ typeof item.created_at === 'object' 
                                        ? item.created_at.date 
                                        : item.created_at | moment("MMMM D, YYYY") }}
                                </v-list-tile-sub-title>                                
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-btn icon ripple :to="{ name: 'StoRTActIndex', params: { act: item.id } }">
                                    <v-icon color="grey lighten-1">remove_red_eye</v-icon>
                                </v-btn>
                            </v-list-tile-action>
                        </v-list-tile>
                    </template>
                </v-list>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>

export default {
    props: {
        items: Array,
    },
    filters: {
        generateActNum(value) {
            return (value / 10000).toString().replace('.', '');
        }
    },
    components: {
        
    },
    data() {
        return {
            
        }
    },
    methods: {
               
    },
    created() {
        console.log(this.items)
    }
}
</script>

<style>

</style>
