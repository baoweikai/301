<template>
  <div>
    <a-form :form="form" layout="inline"  @submit="add">
      <a-form-item
        v-for="(column, index) in columns"
        :key="index"
        :wrapper-col="wrapperCol(column.type)"
      >
        <a-input-number v-if="column.type === 'number' "
          v-decorator="[column.dataIndex, { rules: [{ required: true, message: '请输入' + column.title }] }]"
          :placeholder="'请输入' + column.title"
        />
        <a-input v-else v-decorator="[column.dataIndex, { rules: [{ required: true, message: '请输入' + column.title }] }]" :placeholder="'请输入' + column.title"
        />
      </a-form-item>
      <a-form-item>
        <a-button
          type="default"
          html-type="submit"
        ><a-icon type="plus" /></a-button>
      </a-form-item>
    </a-form>
    <a-table size="small" class="mt-2" v-if="data && data.length > 0" :showHeader="false" rowKey="id" :dataSource="data" :columns="dataCols">
      <spam v-for="(col, i) in columns" :key="i" :slot="col.dataIndex" slot-scope="text, record, index">
        <a-input
          v-if="record.editable"
          style="margin: -5px 0"
          :value="text"
          @change="e => change(e.target.value, index, col.dataIndex)"
        />
        <template v-else>{{text}}</template>
      </spam>
      <template slot="operation" slot-scope="text, record, index">
        <div class='editable-row-operations'>
          <span v-if="record.editable">
            <a-icon type="save" @click="save(index)" />
            <a-icon type="close" @click="cancel(index)" />
          </span>
          <span v-else>
            <a-icon type="edit" @click="edit(index)" />
          </span>
        </div>
      </template>
    </a-table>
  </div>
</template>
<script>
// import IImage from './IImage'
// import IFile from './IFile'

const widthMap = { string: 16, number: 16 }

export default {
  name: 'IMult',
  // 自定义组件
  components: {
  },
  data () {
    return {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 5 }
      },
      wrapperCol: type => ({ xs: { span: 24 }, sm: { span: widthMap[type] || 16 } }),
      form: this.$form.createForm(this),
      columns: this.$attrs.columns,
      cacheData: [],
      dataCols: [...this.$attrs.columns.map(col => Object.assign(col, { scopedSlots: { customRender: col.dataIndex } })), {
        title: '操作',
        dataIndex: 'operation',
        scopedSlots: { customRender: 'operation' }
      }],
      typeMap: {
        string: 'AInput', number: 'AInputNumber', date: 'ADatePicker', month: 'ADatePicker', Range: 'ARangePicker', week: 'WeekPicker', time: 'ATimePicker', cascader: 'ACascader', select: 'ASelect', tree: 'ATreeSelect', switch: 'ASelect', radio: 'ASelect', state: 'ASelect', checkbox: 'ASelect', image: 'IImage', file: 'IFile'
      },
      tipsMap: {
        string: '填写', number: '填写', date: '选择', month: '选择', Range: '选择', week: '选择', time: '选择', cascader: '选择', select: '选择', tree: '选择', textarea: '填写', editor: '填写', image: '上传', file: '上传', slider: '选择', rate: '选择'
      }
    }
  },
  computed: {
    data: {
      get () {
        return this.$attrs['data-__field'].value
      },
      set (value) {
        this.$emit('change', value)
      }
    }
  },
  methods: {
    change (value, key, column) {
      const newData = [...this.data]
      const target = newData[key]
      if (target) {
        newData[key][column] = value
        this.data = newData
      }
    },
    edit (key) {
      const newData = [...this.data]
      const target = newData[key]
      if (target) {
        target.editable = true
        this.data = newData
      }
    },
    save (key) {
      const newData = [...this.data]
      const target = newData[key]
      if (target) {
        delete target.editable
        this.cacheData = newData.map(item => ({ ...item }))
        this.data = newData
      }
    },
    cancel (key) {
      const newData = [...this.data]
      const target = newData[key]
      if (target) {
        const target = newData[key]
        delete target.editable
        this.data = newData
      }
    },
    add (e) {
      // 触发表单验证
      e.preventDefault()
      this.form.validateFields((err, values) => {
        // 验证表单没错误
        if (!err) {
          this.data = this.data ? this.data.concat(values) : values
        }
      })
    }
  }
}
</script>
<style scoped>
  .editable-row-operations a {
    margin-right: 8px;
  }
  .mt-2 {
    margin-top: 1rem;
  }
</style>
