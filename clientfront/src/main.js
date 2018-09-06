import Vue from 'vue'
import Vuex from 'vuex'
import App from './App'
import router from './router'
import store from './store'
import Vuetify from 'vuetify'
import ru from 'vee-validate/dist/locale/ru'
import VeeValidate, { Validator } from 'vee-validate'
import VueMoment from 'vue-moment'
import { Can } from '@/directives'

Vue.use(Vuetify)
Validator.localize('ru', ru);
Vue.use(VeeValidate)
Vue.use(VueMoment)

import 'material-design-icons-iconfont/dist/material-design-icons.css'
import 'vuetify/dist/vuetify.min.css'
import 'babel-polyfill'

Vue.config.productionTip = false

new Vue({
  directives: { Can },
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
