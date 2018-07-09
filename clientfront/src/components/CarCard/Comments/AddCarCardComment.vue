<template>
    <v-card class="mt-2"> 
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs12 sm12 md12 lg12>
                    <v-text-field
                        name="car_card_comment"
                        label="Введите комментарий"
                        multi-line 
                        clearable
                        no-resize
                        v-model="newComment"
                        rows="3"
                    ></v-text-field>                                
                </v-flex>
                <v-flex xs12 sm12 md12 lg12>
                    <v-btn color="success" block flat @click="storeComment" :loading="loading" class="py-0">Сохранить комментарий</v-btn>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<script>
import axios from '@/axios'

export default {
    props: {
        cardId: [Number, String]
    },
    data() {
        return {
            newComment: '',
            loading: false
        }
    },
    methods: {
        storeComment() {
            this.loading = true;
            axios.post(`/sto/${this.$route.params.slug}/cards/${this.cardId}/comments`, { comment: this.newComment })
                .then(response => {
                    this.loading = false;
                    this.newComment = '';
                    this.$emit('add', {
                        comment: response.data.comment,
                        message: response.data.message
                    })
                }) 
                .catch(error => console.log(error));
        },
    },
}
</script>

<style>

</style>
