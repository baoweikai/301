import Vue from 'vue'

const form = {
  state: {
    cate_id: {
      options: []
    }
  },
  getters: {
  },
  mutations: {
    UPDATE: (state, values) => {
      for (const index in values) {
        Vue.set(state, index, values[index])
      }
    }
  },
  actions: {
    // 获取用户信息
    info ({ commit }, store) {
    }
  }
}

export default form
