import Vue from 'vue'

const form = {
  namespaced: true,
  state () {
    return {
      controller: '', // 控制器
      pk: 0, //  主键值
      defaults: {}, //  创建时初始默认值
      items: [], // 表单项
      columns: [], // 列配置
      options: {
        gender: ['男', '女', '保密'],
        switch: ['关闭', '开启']
      }
    }
  },
  mutations: {
    UPDATE: (state, values) => {
      for (const index in values) {
        Vue.set(state, index, values[index])
      }
    }
  },
  actions: {
    commit ({ commit }, values) {
      commit('UPDATE', values)
    }
  }
}

export default form
