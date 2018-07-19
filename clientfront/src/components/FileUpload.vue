<template>
    <FilePond
        name="test"
        ref="pond"
        class-name="my-pond"
        label-idle="Кликните или перетащите файлы сюда..."
        allow-multiple="true"
        accepted-file-types="image/jpeg, image/png, image/svg+xml"
        allow-file-size-validation="true"
        max-file-size="1MB"
        label-max-file-size="Максимальный размер файла {filesize}"
        label-max-file-size-exceeded="Файл слишком большой"
        @addfile="filesChanged"
        @removefile="filesChanged" />
</template>

<script>
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js'
import FilepondPluginImagePreview from 'filepond-plugin-image-preview';
import FilepondPluginFileValidateSize from 'filepond-plugin-file-validate-size';

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilepondPluginImagePreview, FilepondPluginFileValidateSize)

// events: 'files-changed'

export default {
    props: {
        removeFiles: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {

        }
    },
    methods: {
        filesChanged() {
            this.$emit('files-changed', this.$refs.pond.getFiles());
        }
    },
    watch: {
        removeFiles() {
            if(this.removeFiles) {
                this.$refs.pond.removeFiles();
            }
        }
    }
}
</script>

<style>

</style>
