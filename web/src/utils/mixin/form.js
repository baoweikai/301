import { http, url } from '@/utils'
import pick from 'lodash.pick'

const widthMap = { string: 8, cascader: 8, editor: 24 }

export default {
  data () {
    return {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 5 }
      },
      pk: false,
      form: this.$form.createForm(this),
      default: {}, // 创建时初始值
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: widthMap[this.type] ? widthMap[this.type] : 16 }
      },
      spinning: false
    }
  },
  methods: {
    pick,
    add (params) {
      this.default = params
      http.add(this.controller)
        .then(result => {
          // 非表单提交数据储存在store，以便其他地方调用
          this.$store.commit('form/UPDATE', result.store)
        })
    },
    // 获取当前记录信息并开启编辑
    edit (id) {
      this.pk = id
      http.edit(this.controller, id)
        .then(result => {
          // 非表单提交数据储存在store，以便其他地方调用
          this.$store.commit('form/UPDATE', result.store)
          this.form.setFieldsValue(pick(result.view, this.Items || Object.keys(this.columns)))
        })
    },
    submit () {
      // 触发表单验证
      this.form.validateFields((err, values) => {
        // 验证表单没错误
        if (!err) {
          // 提交数据到服务端 必须为 Item 对象
          const data = this.convert(this.cols, pick(values, this.Items || Object.keys(this.columns)))
          const savePath = this.pk ? url.update(this.controller, this.pk) : url.save(this.controller)
          http.post(savePath, Object.assign(data, this.default))
            .then(res => {
              if (res) {
                // Do something
                this.$message.success('保存成功')
                this.modalName = ''
              }
            })
        }
      })
    },
    // 转化为表单可用数据
    convert (columns, values) {
      return values
    }
  }
}
