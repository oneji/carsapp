<template>
    <span :class="`label label-${type}`">{{ text }}</span>
</template>

<script>
export default {
    props: ['text', 'type']
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
    .label-primary {
        background: #3498db;
    }
    .label-success {
        background-color: #4caf50;
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
