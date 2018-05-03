<template>
  <div>
    <v-layout>
      <v-flex xs12 sm12 md12 lg12>
        <v-btn color="success">Создать компанию</v-btn>
      </v-flex>
    </v-layout>
    
    <v-layout class="py-2">
      <v-flex xs12 sm12 md10 lg8>
        <data-table :headers="headers" :items="items"/>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
import DataTable from '~/components/DataTable'
export default {
  head: {
    title: 'Компании'
  },
  components: {
    DataTable
  },
  data() {
    return {
      selected: [],
      search: '',

      headers: [
        { text: 'Dessert (100g serving)', align: 'left', sortable: false, value: 'name' },
        { text: 'Calories', value: 'calories' },
        { text: 'Fat (g)', value: 'fat' },
        { text: 'Carbs (g)', value: 'carbs' },
        { text: 'Protein (g)', value: 'protein' },
        { text: 'Iron (%)', value: 'iron' }
      ],

      items: [
        { value: false, name: 'Timur Kamilov', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Timur Usmonov', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Хикмат Шайхаливе', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
        { value: false, name: 'Frozen Yogurt', calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: '1%' },
      ]
    }
  }
}
</script>

<style>

</style>
