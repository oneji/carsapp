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
                    <template v-for="item in items">
                        <RtActItem :item="item" :key="item.id" />
                    </template>
                </v-list>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import RtActItem from './ReceiveTransferActItem'

export default {
    props: {
        items: Array,
    },
    components: {
        RtActItem
    }
}
</script>

<style>

</style>
