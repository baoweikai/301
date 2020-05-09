import Vue from 'vue'
import Vuex from 'vuex'

import app from './modules/app'
import me from './modules/me'
import form from './modules/form'
import list from './modules/list'
import prod from './modules/prod'
import home from './modules/home'
import getters from './getters'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    app,
    me,
    form,
    prod,
    list,
    home
  },
  state: {
    pageState: 0
  },
  mutations: {

  },
  actions: {

  },
  getters
})
