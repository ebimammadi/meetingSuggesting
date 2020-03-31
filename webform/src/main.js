import Vue from 'vue'
import App from './App.vue'
import store from './store'
import { BootstrapVue } from 'bootstrap-vue'
import Multiselect from "vue-multiselect"

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'vue-multiselect/dist/vue-multiselect.min.css'

Vue.config.productionTip = false
Vue.use(BootstrapVue)
Vue.component('multiselect', Multiselect)


new Vue({
  store,
  render: h => h(App),
}).$mount('#app')
