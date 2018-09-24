<template>
    <v-flex xs4 sm2 md2 lg2>
        <v-card class="consumable__item" @click.native="consumableDetails">
            <v-tooltip bottom>
                <v-card-text slot="activator" style="height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column">
                    <img :src="item.icon" class="consumable__icon">
                </v-card-text>
                <span>{{ item.consumable_name }}</span>
            </v-tooltip>
        </v-card>
    </v-flex>
</template>

<script>
export default {
    props: {
        item: Object
    },
    data() {
        return {
            showDetails: false
        }
    },
    methods: {
        consumableDetails() {
            this.$emit('show', this.item);
        }
    }
}
</script>

<style>
    .consumable__item {
        transition: .1s all ease;
        text-align: center;
        height: 70px !important;
    }
    .consumable__item:hover {
        cursor: pointer;
        box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 3px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
    }
    .consumable__icon {
        width: 40px;
        height: auto;
    }
    .consumable__name {
        font-size: 14px;
    }
</style>
