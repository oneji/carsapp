<template>
    <v-card class="mb-3"> 
        <v-card-text class="py-1">
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
                        hide-details
                    ></v-text-field>                                
                    <v-btn color="success" block flat @click="storeComment" :loading="loading" class="py-0">Сохранить</v-btn>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<script>
import axios from '@/axios'

export default {
    data() {
        return {
            newComment: '',
            loading: false
        }
    },
    methods: {
        storeComment() {
            this.loading = true;
            axios.post(`/sto/${this.$route.params.slug}/cards/${this.$store.getters.car.card.id}/comments`, { comment: this.newComment })
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
