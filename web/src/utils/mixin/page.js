// import Vue from 'vue'
import { mapState } from 'vuex'
import pick from 'lodash.pick'
import http from '../http'

// const mixinsComputed = Vue.config.optionMergeStrategies.computed
// const mixinsMethods = Vue.config.optionMergeStrategies.methods

export default {
  data () {
    return {
      modalName: '',
      loading: false,
      loadPath: ''
    }
  },
  computed: {
    ...mapState(['pageState'])
  },
  methods: {
    pick,
    async load (path = false, parameter = {}) {
      this.loading = true
      const result = await http.get(path || this.loadPath, parameter)

      if (result) {
        this.loadData(result)
        this.$store.commit('UPDATE', { pageState: 1 })
        this.loading = true
        return result
      }
      return false
    },
    loadData (result) {
      Object.assign(this, result)
    }
  }
}
