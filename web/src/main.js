import Vue from 'vue'
import App from './App'
import router from '@/utils/perm' // permission control
import i18n from './lang'
import store from './store/'
import Antd from 'ant-design-vue'
import bootstrap from '@/utils/bootstrap'
// import '@/utils/storage'

// Vue.config.productionTip = false

Vue.use(Antd)

new Vue({
  router,
  store,
  i18n,
  created () {
    bootstrap()
  },
  render: h => h(App)
}).$mount('#app')
