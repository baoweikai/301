import { filters } from '@/utils'

export default {
  data () {
    return {
      search: this.$form.createForm(this)
    }
  },
  filters,
  methods: {
    handleParams (obj) {
      // 判断必须为obj
      if (!(Object.prototype.toString.call(obj) === '[object Object]')) {
        return {}
      }
      const tempObj = {}
      for (let [key, value] of Object.entries(obj)) {
        if (Array.isArray(value) && value.length <= 0) continue
        if (Object.prototype.toString.call(value) === '[object Function]') continue

        if (this.datetimeTotimeStamp) {
          // 若是为true,则转为时间戳
          if (Object.prototype.toString.call(value) === '[object Object]' && value._isAMomentObject) {
            // 判断moment
            value = value.valueOf()
          }
          if (Array.isArray(value) && value[0]._isAMomentObject && value[1]._isAMomentObject) {
            // 判断moment
            value = value.map(item => item.valueOf())
          }
        }
        // 若是为字符串则清除两边空格
        if (value && typeof value === 'string') {
          value = value.trim()
        }
        tempObj[key] = value
      }

      return tempObj
    },
    handleSearch (e) {
      // 触发表单提交
      e.preventDefault()
      this.search.validateFields((err, values) => {
        if (err) {

        }
        if (this.$listeners.callBackFormat && typeof this.$listeners.callBackFormat === 'function') {
          const formatData = this.$listeners.callBackFormat(values)
          this.$emit('refresh', formatData)
        } else {
          const queryParams = this.handleParams(values)
          this.$parent.fetch(queryParams)
        }
      })
    },
    resetSearch () {
      // 重置整个查询表单
      this.search.resetFields()
      this.change()
    },
    excel () {
      this.search.validateFields((err, values) => {
        if (err) {

        }
        const queryParams = this.handleParams(values)
        const urlParm = []
        for (const i in queryParams) {
          if (queryParams[i]) {
            urlParm.push(i + '=' + queryParams[i])
          }
        }
        location.href = '/api/' + this.$attrs.controller + '/export?' + urlParm.join('&')
      })
    }
  }
}
