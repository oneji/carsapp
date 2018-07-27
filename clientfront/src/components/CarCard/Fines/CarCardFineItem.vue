<template>
    <v-list-tile avatar>
        <v-list-tile-avatar>
            <v-icon class="blue white--text">assignment</v-icon>
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title>
                Штраф от: {{ typeof item.fine_date === 'object' ? item.fine_date.date.substr(0, 10) : item.fine_date | moment('MMMM DD, YYYY') }}
            </v-list-tile-title>
            <v-list-tile-sub-title>
                <MyLabel :text="item.fine_amount + 'с.'" type="primary" />
                <MyLabel :text="item.paid === 1 ? 'Оплачен' : 'Не оплачен'" :type="item.paid === 1 ? 'success' : 'warning'" />
            </v-list-tile-sub-title>
        </v-list-tile-content>
        <v-list-tile-action>
            <v-btn slot="activator" icon ripple>
                <v-icon color="grey lighten-1">remove_red_eye</v-icon>
            </v-btn>             

            <v-tooltip bottom v-if="item.paid === 0">
                <v-btn slot="activator" icon ripple @click="action(item.id)" :loading="actionLoading === item.id">
                    <v-icon color="grey lighten-1">attach_money</v-icon>
                </v-btn>     
                <span>Оплатить</span>
            </v-tooltip>   

            <v-tooltip bottom v-if="item.paid === 1">
                <v-btn slot="activator" icon ripple @click="action(item.id)" :loading="actionLoading === item.id">
                    <v-icon color="grey lighten-1">money_off</v-icon>
                </v-btn>     
                <span>Не оплачен</span>
            </v-tooltip>                    
        </v-list-tile-action>
    </v-list-tile>
</template>

<script>
import axios from '@/axios'
import MyLabel from '@/components/Label'

export default {
    props: {
        item: Object
    },
    components: {
        MyLabel
    },
    data() {
        return {
            actionLoading: null
        }
    },
    methods: {
        action(fine_id) {
            this.actionLoading = fine_id;
            let status = this.item.paid === 0 ? 1 : 0;
            axios.put(`/company/${this.$route.params.slug}/cars/${this.$store.getters.car.id}/fines/${fine_id}/${status}`)
                .then(response => {
                    this.item.paid = status;
                    this.actionLoading = null;
                    
                })
                .catch(error => console.error());
        }
    }
}
</script>

<style>

</style>
