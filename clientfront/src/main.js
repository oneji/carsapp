import Vue from 'vue'
import Vuex from 'vuex'
import App from './App'
import router from './router'
import store from './store'
import Vuetify from 'vuetify'
import VeeValidate from 'vee-validate'
import axios from './axios'
import cookie from 'js-cookie'
import VueMoment from 'vue-moment'
import { Can } from '@/directives'

Vue.use(Vuetify)
Vue.use(VeeValidate)
Vue.use(VueMoment)

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
