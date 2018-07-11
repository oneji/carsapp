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
                        <CommentItem :key="comment.title" :item="comment" />
                    </template>
                </v-list>
            </v-card-text>
        </v-card>

        <AddComment @add="onCommentAdded" />
    </div>
</template>

<script>
import CommentItem from './CarCardCommentItem'
import AddComment from './AddCarCardComment'


export default {
    props: {
        items: Array,
    },
    components: {
        AddComment, CommentItem
    },
    data() {
        return {
            
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
