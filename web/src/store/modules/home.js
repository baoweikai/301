import Vue from 'vue'
import http from '@/utils/http'

const home = {
  state: {
    form: {},
    controller: '', // 控制器
    pk: 0, //  主键值
    default: {}, //  创建时初始默认值
    items: [], // 表单项
    columns: [], // 列配置

    options: {
      gender: ['男', '女', '保密'],
      switch: ['关闭', '开启']
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
    // 获取单条信息
    load (id) {
      http.view(this.controller, id)
        .then(res => {
          if (res.status === 200) {
            // 非表单提交数据储存在store
            // this.commit(res.result.store)
          }
        })
    }
  }
}

export default home
