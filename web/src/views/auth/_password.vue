<template>
  <a-modal :bodyStyle="bodyStyle" :centered="true" :closable="false" :width="500" :visible="visible" @ok="submit" @cancel="visible = false">
    <a-spin :spinning="spinning">
      <a-form :form="form">
        <Item v-for="(column, index) in columns" :key="index" v-bind="{ column: column, index: index }" @change="() => {}" />
      </a-form>
    </a-spin>
  </a-modal>
</template>

<script>
import { Item } from '@/components'
import { page, http } from '@/utils'
import md5 from 'md5'

export default {
  mixins: [page],
  components: { Item },
  data () {
    return {
      pd: 0,
      controller: 'admin',
      columns: {
        password: { title: '密码', type: 'string', rules: [{ required: true }] },
        confirm: { title: '确认密码', type: 'string', rules: [{ required: true }] }
      },
      labelCol: {
        xs: { span: 24 },
        sm: { span: 5 }
      },
      bodyStyle: { maxHeight: '80vh', overflowY: 'scroll' },
      visible: false,
      form: this.$form.createForm(this),
      spinning: false
    }
  },
  methods: {
    submit () {
      this.form.validateFields((err, values) => {
        if (!err) {
          const params = { password: md5(values.password), confirm: md5(values.confirm) }
          http.post('admin/pass/' + this.pk, params)
            .then(res => {
              res.state === 200 && this.$message.success('修改成功')
            }).catch(e => {})
        } else {
          // this.state.loginBtn = false
        }
      })
    }
  }
}
</script>
