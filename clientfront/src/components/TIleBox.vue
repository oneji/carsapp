<template>
    <v-card>
        <v-card-text>
            <div class="tile-box">
                <div class="tile-box__icon">
                    <v-icon class="display-3">{{ boxIcon }}</v-icon>
                </div>
                <div class="tile-box__content">
                    <p class="title" title="Statistics">{{ title }}</p>
                    <h2 class="display-1">{{ value }}</h2>
                    <v-btn color="success" flat small :to="link">Просмотреть</v-btn>
                </div>
                <div class="clearfix"></div>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    props: {
        link: Object,
        title: String,
        value: Number,
        boxIcon: String
    }
}
</script>

<style>
    .tile-box {
        text-align: right;
    }
    .tile-box__icon {
        position: absolute;
        left: 30px;
        top: 30px;
        overflow: hidden;
        vertical-align: middle;
        height: 80px;
        width: 80px;
        text-align: center;
        line-height: 74px;
        border-radius: 50%;
        border: 4px double #4caf50;
    }
    .tile-box__content {
        position: relative;
    }
</style>
