<template>
    <v-list-tile :key="item.title" avatar>
        <v-list-tile-avatar>
            <img v-if="item.user.avatar !== null" :src="assetsURL + '/' + item.user.avatar">
            <img v-else src="/static/images/user.png">
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title v-html="item.comment"></v-list-tile-title>
            <v-list-tile-sub-title>Автор: {{ item.user.fullname }}</v-list-tile-sub-title>
            <v-list-tile-sub-title>
                Дата: {{ item.created_at | moment('MMMM D, YYYY') }} <!-- в {{ item.created_at | moment('H:mm:ss') }} -->
            </v-list-tile-sub-title>
        </v-list-tile-content>
    </v-list-tile>
</template>

<script>
import assetsURL from '@/components/mixins/assets-url'

export default {
    mixins: [assetsURL],
    props: {
        item: Object
    },
}
</script>

<style>

</style>
