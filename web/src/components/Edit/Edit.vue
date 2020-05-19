<template>
  <a-modal :bodyStyle="bodyStyle" :centered="true" :closable="false" :width="width" :visible="visible" @ok="update" @cancel="visible = false">
    <a-spin :spinning="spinning">
      <a-form :form="form">
        <Item v-for="(column, index) in columns" :key="index" v-bind="{ column: column, index: index }" @change="change" />
      </a-form>
    </a-spin>
  </a-modal>
</template>

<script>
import { http, util } from '@/utils'
import Item from '../Item/Item'

const widthMap = { string: 8, cascader: 8, editor: 24 }

export default {
  components: { Item },
  props: {
    controller: { type: String, default: '' },
    defaults: { type: Object, default: () => ({}) },
    width: { type: Number, default: 1200 }
  },
  data () {
    return {
      columns: this.cols(),
      labelCol: {
        xs: { span: 24 },
        sm: { span: 5 }
      },
      bodyStyle: { maxHeight: '80vh', overflowY: 'scroll' },
      pk: false,
      visible: false,
      form: this.$form.createForm(this),
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: widthMap[this.type] || 16 }
      },
      spinning: false
    }
  },
  methods: {
    cols () {
      const cols = {}
      for (const i in this.$attrs.columns) {
        cols[i] = Object.assign({}, this.$attrs.columns[i])
      }
      return cols
    },
    // 获取当前记录信息并开启编辑
    load (id) {
      this.pk = id
      this.visible = true
      this.spinning = true
      http.edit(this.controller, id)
        .then(res => {
          // 非表单提交数据储存在store，以便其他地方调用
          for (const i in res.result.columns) {
            this.columns[i] && Object.assign(this.columns[i], res.result.columns[i])
          }
          this.form.setFieldsValue(util.pick(res.result.view, Object.keys(this.columns)))
          this.spinning = false
        })
    },
    update () {
      // 触发表单验证
      this.form.validateFields((err, values) => {
        // 验证表单没错误
        if (!err) {
          // 提交数据到服务端 必须为 Item 对象
          const data = this.convert(this.cols, values)
          http.update(this.controller, this.pk, Object.assign(data, this.defaults))
            .then(res => {
              if (res.state === 200) {
                // Do something
                this.$message.success('保存成功')
                this.visible = false
                this.$parent.$refs.list && this.$parent.$refs.list.fresh(this.defaults)
              }
            })
        }
      })
    },
    // 转化为表单可用数据
    convert (columns, values) {
      return values
    },
    change (index, v) {
      this.$parent.change && this.$parent.change(index, v)
    }
  }
}
</script>
<style>
</style>
