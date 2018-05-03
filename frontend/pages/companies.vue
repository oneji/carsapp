<template>
  <div>
    <v-layout>
      <v-flex xs12 sm12 md12 lg12>
        <v-btn color="success">Создать компанию</v-btn>
      </v-flex>
    </v-layout>
    
    <v-layout>
      <v-flex xs12 sm12 md10 lg8>
        <v-card>
          <v-card-title>
            Nutrition
            <v-spacer></v-spacer>
            <v-text-field append-icon="search" label="Поиск" single-line hide-details v-model="search"></v-text-field>
          </v-card-title>
          <v-data-table :headers="headers" select-all v-model="selected" :items="items" :search="search" class="elevation-1">
            <!-- Main temlpate -->
            <template slot="items" slot-scope="props">
              <td><v-checkbox primary hide-details v-model="props.selected"></v-checkbox></td>
              <td>{{ props.item.name }}</td>
              <td class="text-xs-right">{{ props.item.calories }}</td>
              <td class="text-xs-right">{{ props.item.fat }}</td>
              <td class="text-xs-right">{{ props.item.carbs }}</td>
              <td class="text-xs-right">{{ props.item.protein }}</td>
              <td class="text-xs-right">{{ props.item.iron }}</td>
            </template>

            <!-- No data template -->
            <template slot="no-data">
              <v-alert :value="true" color="error" icon="warning">
                Нет доступных данных.
              </v-alert>
            </template>
          </v-data-table>
          
          <!-- No results template -->
          <v-alert slot="no-results" :value="true" color="error" icon="warning">
            Реультатов "{{ search }}" не найдено.
          </v-alert>
        </v-card>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
export default {
  head: {
    title: 'Компании'
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
