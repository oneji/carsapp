<template>
    <span class="label" :style="`background-color: ${color}`">{{ text }}</span>
</template>

<script>
export default {
    props: ['text', 'color']
}
</script>

<style>
    .label {
        display: inline;
        padding: 0.3em .6em;
        font-size: 95%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }
    .label-success {
        background-color: #32c861;
    }
    .label-error {
        background: #f96a74;
    }
    .label-muted {
        background-color: #98a6ad;
    }
    .label-warning {
        background: #ffa91c;
    }
</style>
