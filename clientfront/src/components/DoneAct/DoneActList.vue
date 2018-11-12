<template>
    <v-card class="mb-3">
        <v-card-media>
            <v-container>
                <v-layout>
                    <v-flex>
                        <p class="subheading my-0">Акты выполненных работ</p>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card-media>
        <v-divider></v-divider>
        <v-card-text primary-title class="py-1 px-1">
            <v-alert outline transition="scale-transition" type="info" :value="true" v-if="items.length === 0">
                Актов выполненных работ нет.
            </v-alert>
            <v-list two-line v-else>                    
                <template v-for="item in items" >
                    <DoneActItem 
                        :item="item" 
                        :key="item.id"
                        :showAct="showAct" />
                </template>
            </v-list>
        </v-card-text>
    </v-card>
</template>

<script>
import DoneActItem from './DoneActItem'

export default {
    props: {
        items: Array,
        showAct: {
            type: Boolean,
            default: true
        }
    },
    components: {
        DoneActItem
    },
    data() {
        return {

        }
    }
}
</script>

<style>

</style>
