<template>
    <div>
        <v-card class="mb-3">
            <v-card-media>
                <v-container>
                    <v-layout>
                        <v-flex>
                            <p class="subheading my-0">Дефектные акты</p>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-media>
            <v-divider></v-divider>
            <v-card-text primary-title class="py-1 px-1">
                <v-alert outline transition="scale-transition" type="info" :value="true" v-if="items.length === 0">
                    Дефектных актов нет.
                </v-alert>
                <v-list two-line v-else>                    
                    <DefectActItem 
                        v-for="item in items" 
                        :key="item.id" 
                        :item="item"
                        :showDefect="showDefect" />
                </v-list>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import DefectActItem from './DefectActItem'

export default {
    props: {
        items: Array,
        showDefect: {
            type: Boolean,
            default: true
        }
    },
    components: {
        DefectActItem
    },
    data() {
        return {
            
        }
    }
}
</script>

<style>

</style>
