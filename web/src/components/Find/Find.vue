<template>
  <a-form layout="inline" :form="form" class="mb-3" @submit="handleSearch">
    <a-form-item v-for="(column, index) in columns" :key="index" :label="column.label || column.title">
      <component
        :is="typeMap[column.type]"
        v-bind="binds(column, index)"
        v-decorator="[index, decorators (column)]"
      >
      </component>
    </a-form-item>
    <a-form-item>
      <a-button type="primary" icon="search" html-type="submit"></a-button>
    </a-form-item>
  </a-form>

</template>
<script>
import filters from '@/utils/filters'
import { IRelate } from '@/components/Column'
import moment from 'moment'

export default {
  props: {
    columns: { type: Object, default: () => ({}) }
  },
  components: { IRelate },
  data () {
    return {
      form: this.$form.createForm(this),
      typeMap: {
        string: 'AInput', number: 'AInputNumber', date: 'ARangePicker', month: 'AMonthPicker', Range: 'ARangePicker', week: 'AWeekPicker', time: 'ARangePicker', switch: 'ASelect', radio: 'ASelect', state: 'ASelect', checkbox: 'ASelect', select: 'ASelect', cascader: 'ACascader', tree: 'ATreeSelect', tag: 'ITag', relate: 'IRelate'
      }
    }
  },
  mounted () {
    this.form.setFieldsValue(this.$route.query)
  },
  methods: {
    // 子组件绑定的参属性
    binds (column, index) {
      const binds = {}

      // 配置的输入项属性
      for (const i in column) {
        if (!['title', 'type', 'customRender', 'rules', 'label', 'defaultValue', 'format'].includes(i)) {
          binds[i] = column[i]
        }
      }
      // 选项格式化
      if (['radio', 'checkbox', 'select', 'state', 'switch'].includes(column.type)) {
        binds.options = filters.options(column.options, 0)
      }
      if (column.type === 'cascader') {
        binds.options = this.store[index] ? this.store[index].options : []
      }
      // 单选框多选框 不需要 placeholder
      if (!['time', 'date', 'datetime'].includes(column.type)) {
        binds.placeholder = binds.placeholder || column.title
      }

      return binds
    },
    handleParams (obj) {
      // 判断必须为obj
      if (!(Object.prototype.toString.call(obj) === '[object Object]')) {
        return {}
      }
      const tempObj = {}
      for (let [key, value] of Object.entries(obj)) {
        if (value === undefined || (Array.isArray(value) && value.length <= 0)) continue
        if (Object.prototype.toString.call(value) === '[object Function]') continue

        // 若是为true,则转为时间戳
        if (Object.prototype.toString.call(value) === '[object Object]' && value._isAMomentObject) {
          // 判断moment
          value = value.valueOf()
        }
        if (Array.isArray(value) && value[0]._isAMomentObject && value[1]._isAMomentObject) {
          // 判断moment
          value = value.map(item => item.format('YYYY-MM-DD'))
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
      this.form.validateFields((err, values) => {
        if (!err) {
          let queryParams
          if (this.$listeners.callBackFormat && typeof this.$listeners.callBackFormat === 'function') {
            queryParams = this.$listeners.callBackFormat(values)
          } else {
            queryParams = this.handleParams(values)
          }
          this.$router.push({ path: this.$route.path, query: queryParams }).catch(err => err)
        }
      })
    },
    resetSearch () {
      // 重置整个查询表单
      this.form.resetFields()
      this.change()
    },
    excel () {
      this.form.validateFields((err, values) => {
        if (!err) {
          const queryParams = this.handleParams(values)
          this.$router.push({ path: this.$attrs.controller + '/export', query: queryParams })
        }
      })
    },
    decorators (column) {
      const decorators = {}
      // 时间格式化
      if (['date', 'time', 'datetime'].includes(column.type)) {
        decorators.normalize = val => {
          const range = []
          val && val[0] && (range[0] = moment(Date.parse(val[0])))
          val && val[1] && (range[1] = moment(Date.parse(val[1])))
          return range
        }
      }
      // 时间格式化
      if (['range'].includes(column.type)) {
        decorators.normalize = val => [moment(val[0]), moment(val[1])]
      }
      return decorators
    }
  }
}
</script>
<style scoped>
form .ant-select{ min-width: 160px; }
</style>
