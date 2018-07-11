<template>
    <transition name="fade-transition" mode="out-in">
        <div class="loading-block" v-if="loading" v-cloak>
            <v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
        </div>
    </transition>
</template>

<script>
export default {
    props: {
        loading: Boolean
    }
}
</script>

<style>
    .loading-block {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
