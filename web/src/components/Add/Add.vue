<template>
  <a-modal :bodyStyle="bodyStyle" :centered="true" :closable="false" :destroyOnClose="true" :width="width" :visible="visible" @ok="submit" @cancel="visible = false">
    <a-spin :spinning="false">
      <a-form :form="form"><Item v-for="(column, index) in columns" :key="index" v-bind="{ column: column, index: index }" @change="change" /></a-form>
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
    load () {
      http.add(this.controller).then(res => {
        for (const i in res.result.columns) {
          this.columns[i] && Object.assign(this.columns[i], res.result.columns[i])
        }
        this.form.setFieldsValue(util.pick(res.result.view, Object.keys(this.columns)))
      })
      this.visible = true
    },
    submit () {
      // 触发表单验证
      this.form.validateFields((err, values) => {
        // 验证表单没错误
        if (!err) {
          // 提交数据到服务端 必须为 Item 对象
          const data = this.convert(this.cols, values)
          http.save(this.controller, Object.assign(data, this.defaults)).then(res => {
            if (res.state === 200) {
              // Do something
              this.$message.success('保存成功')
              this.visible = false
              this.$parent.$refs.list && this.$parent.$refs.list.fresh(this.defaults)
              this.form.resetFields()
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
  },
  watch: {
    visible (newVal) {
      newVal && this.load()
    }
  }
}
</script>
<style scoped>
.ant-modal {
  min-width: 60vw;
  max-width: 80vw;
}
.ant-modal-body {
  max-height: 60vh;
  overflow-y: scroll;
}
</style>
