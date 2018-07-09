<template>
    <div>
        <v-card>
            <v-card-media>
                <v-container>
                    <v-layout>
                        <v-flex>
                            <p class="subheading my-0">Комментарии</p>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-media>
            <v-divider></v-divider>
            <v-card-text primary-title class="pt-1">
                <v-list two-line>
                    <v-alert outline transition="scale-transition" type="info" :value="true" v-if="items.length === 0">
                        Комментариев к автомобилю нет.
                    </v-alert>
                    <template v-for="comment in items">
                        <v-list-tile :key="comment.title" avatar>
                            <v-list-tile-avatar>
                                <img v-if="comment.user.avatar !== null" :src="assetsURL + '/' + comment.user.avatar">
                                <img v-else src="/static/images/user.png">
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title v-html="comment.comment"></v-list-tile-title>
                                <v-list-tile-sub-title v-html="comment.user.fullname + ': ' + comment.created_at"></v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </template>
                </v-list>
            </v-card-text>
        </v-card>

        <add-comment :card-id="cardId" @add="onCommentAdded" />
    </div>
</template>

<script>
import config from '@/config'
import AddComment from './AddCarCardComment'

export default {
    props: {
        items: Array,
        cardId: [Number, String]
    },
    components: {
        AddComment
    },
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    },
    methods: {
        onCommentAdded(response) {
            this.$emit('add', response);
        }
    }
    
}
</script>

<style>

</style>
