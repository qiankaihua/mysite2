/* eslint-disable no-multiple-empty-lines */
// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
// import Vuex from 'vuex'
import App from './App.vue'
import router from './router/index'
import store from './store'
import iView from 'iview'
import 'iview/dist/styles/iview.css'
import State from './components/State.vue'
import axios from 'axios'
import lodash from 'lodash'
import Router from 'vue-router'

Vue.use(iView)
// Vue.use(Vuex)
// Vue.use(Router)

Vue.config.productionTip = false

axios.defaults.baseURL = '/api/'
Vue.$http = Vue.prototype.$http = axios
Vue._ = Vue.prototype._ = window._ = Window.prototype._ = lodash

Vue.prototype.GLOBAL = State
window.title_suf = '|To Be Continue'
Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
}).$mount('#app')
