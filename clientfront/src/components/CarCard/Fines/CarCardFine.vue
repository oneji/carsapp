<template>
    <v-card class="mb-3">
        <v-card-media>
            <v-container class="py-2">
                <v-layout align-baseline>
                    <v-flex>
                        <p class="subheading my-0">Штрафы</p>
                    </v-flex>
                    <v-flex xs2 sm1 md1 lg1 class="px-0">                        
                        <v-tooltip bottom> 
                            <v-btn slot="activator" color="success" block flat small fab class="my-0">
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
</template>

<script>
import FileUpload from '@/components/FileUpload'
import FineItem from './CarCardFineItem'

export default {
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
            
        }
    }
}
</script>

<style>

</style>
