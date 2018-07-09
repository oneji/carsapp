<template>
    <v-card>
        <v-card-media>
            <v-container>
                <v-layout>
                    <v-flex>
                        <p class="subheading my-0">Вложения</p>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card-media>
        <v-divider></v-divider>
        <v-card-text primary-title>
            <v-alert outline transition="scale-transition" type="info" :value="true" v-if="files.length === 0">
                Вложений нет.
            </v-alert>
            
            <lightbox
                id="car_attachments"
                :images="files"
                :image_class="'card_attachment'"
                :album_class="'my-album-class'"
                :options="{ history: false }">
            </lightbox>
        </v-card-text>
        <v-card-actions>
            <!-- <v-btn color="success" block flat class="py-0" @click.native="newAttachments.dialog = true">Добавить вложения</v-btn> -->
        </v-card-actions>
    </v-card>
</template>

<script>
import Lightbox from 'vue-simple-lightbox'

export default {
    components: {
        Lightbox
    },
    props: {
        files: Array
    }
}
</script>

<style>

</style>
