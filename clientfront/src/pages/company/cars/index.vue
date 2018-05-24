<template>
    <v-layout row wrap>
        <v-flex>
            <v-btn color="success" :to="{ name: 'CompanyCarsCreate' }" append>Добавить машину</v-btn>
        </v-flex>
    </v-layout>
</template>

<script>
export default {
    mounted() {
        console.log(this.$router.matched);
    }
}
</script>

<style>

</style>
