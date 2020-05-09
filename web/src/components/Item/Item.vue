<script>
import { IEditor, ITag, IRelate, IImage, IFile, IGallery, IFilelist, IMult } from '@/components/Column'
import filters from '@/utils/filters'
import { mapState } from 'vuex'
import moment from 'moment'

const widthMap = { string: 8, cascader: 8, editor: 24 }
const typeMap = {
  string: 'AInput',
  number: 'AInputNumber',
  date: 'ADatePicker',
  month: 'ADatePicker',
  Range: 'ARangePicker',
  week: 'AWeekPicker',
  time: 'ATimePicker',
  cascader: 'ACascader',
  select: 'ASelect',
  tree: 'ATreeSelect',
  tag: 'ITag',
  relate: 'IRelate',
  switch: 'ASwitch',
  radio: 'ARadioGroup',
  state: 'ARadioGroup',
  checkbox: 'ACheckboxGroup',
  textarea: 'ATextarea',
  editor: 'IEditor',
  image: 'IImage',
  file: 'IFile',
  gallery: 'IGallery',
  fileList: 'IFilelist',
  slider: 'ASlider',
  rate: 'ARate',
  attr: 'IAttr',
  mult: 'IMult'
}
export default {
  components: {
    IEditor, ITag, IRelate, IImage, IFile, IGallery, IFilelist, IMult
  },
  data () {
    return {
      type: this.$attrs.column.type,
      index: this.$attrs.index,
      column: this.$attrs.column,
      labelCol: {
        xs: { span: 24 },
        sm: { span: 5 }
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: widthMap[this.$attrs.column.type] || 16 }
      },
      typeMap: typeMap,
      tipsMap: {
        string: '填写',
        number: '填写',
        date: '选择',
        month: '选择',
        Range: '选择',
        week: '选择',
        time: '选择',
        cascader: '选择',
        select: '选择',
        tree: '选择',
        relate: '选择',
        switch: '选择',
        radio: '选择',
        checkbox: '选择',
        textarea: '填写',
        editor: '填写',
        image: '上传',
        file: '上传',
        gallery: '上传',
        fileList: '上传',
        slider: '选择',
        rate: '选择'
      }
    }
  },
  computed: {
    ...mapState({
      // ...
      store: state => state.form
    })
  },
  methods: {
    // 子组件绑定的参属性
    binds () {
      const binds = {}
      const column = this.column

      // 配置的输入项属性
      for (const i in column) {
        if (!['title', 'type', 'customRender', 'rules', 'label', 'defaultValue'].includes(i)) {
          binds[i] = column[i]
        }
      }
      // 选项格式化
      if (['radio', 'state', 'checkbox'].includes(this.type)) {
        binds.options = filters.options(column.options, this.type === 'select' ? 1 : -1)
      }
      if (this.type === 'cascader' || this.type === 'select') {
        // binds.options = this.store[this.index] ? this.store[this.index].options : []
      }
      if (this.type === 'relate') {
        binds.text = column.text
      }
      // 单选框多选框 不需要 placeholder
      binds.placeholder = binds.placeholder || column.title

      return binds
    },
    itemBinds () {
      const column = this.column
      return {
        label: column.type === 'editor' ? '' : column.label || column.title,
        hasFeedback: !['radio', 'checkbox', 'state', 'editor'].includes(column.type),
        labelCol: column.labelCol || this.labelCol,
        wrapperCol: column.wrapperCol || this.wrapperCol
      }
    },
    decorators () {
      const column = this.column
      const decorators = {
        rules: (column.rules || []).map((rule) => {
          rule.required && (rule.message = column.title + '不能为空')
          return rule
        })
      }
      // 时间格式化
      if (['date', 'time', 'datetime'].includes(this.type)) {
        decorators.normalize = v => moment(v)
      }
      // 编辑器内容初始化
      if (column.type === 'editor') {
        decorators.normalize = v => v ? v.replace(new RegExp(/src="(\/)/g), 'src="' + filters.image('/')) : ''
      }
      return decorators
    }
  },
  render (h) {
    return h(
      'a-form-item', { props: this.itemBinds() }, [
        h(typeMap[this.type], {
          props: this.binds(),
          directives: [
            {
              name: 'decorator',
              value: [this.index, this.decorators()]
            }
          ],
          on: {
            change: curr => this.$emit('change', this.index, curr)
          }
        })
      ]
    )
  }
}
</script>
<style scoped>
form .ant-select{ min-width: 400px; }
</style>
