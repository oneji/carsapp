<template>
    <figure v-lazyload class="image__wrapper">
        <div :data-url="source"></div>
    </figure>
</template>

<script>
export default {
    props: {
        source: {
            type: String,
            required: true
        }
    }
}
</script>

<style>
    .image__wrapper {
        width: 100%;
        height: 100%;
    }
    .image__wrapper > div {
        background-position: center;
        background-size: cover;
        height: 100%;
        width: 100%;
    }
</style>
