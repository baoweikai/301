<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List
      ref="list"
      v-bind="{ columns: pick(columns, listItems), controller: controller, data: list, queryParams: queryParams }"
    />
    <Edit
      ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }"
    />
    <Add
      ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller }"
    />
  </div>
</template>

<script>
import { Find, List, Edit, Add } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Find, Edit, Add },
  data () {
    return {
      description: '别名',
      controller: 'cname',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        name: { title: '名称', type: 'string', rules: [{ required: true }] },
        cate_id: {
          title: '分类', type: 'radio', rules: [{ required: true }], options: {}, customRender: (v, record) => record.cate_name
        },
        is_use: {
          title: '是否使用', type: 'switch', options: ['未使用', '已使用'], rules: [{ required: true }]
        },
        status: {
          title: '状态', type: 'switch', options: ['正常', '关闭'], rules: [{ required: true }]
        },
        create_at: { title: '创建日期', type: 'date', format: 'MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'MM-DD HH:mm' },
        action: { title: '操作', type: 'action', actions: ['edit', 'audit', 'del'] }
      },
      // 列表项
      listItems: ['id', 'name', 'cate_id', 'status', 'is_use', 'create_at', 'update_at', 'action'],
      // 表单
      formItems: ['name', 'cate_id', 'status', 'is_use'],
      findItems: ['name', 'cate_id', 'status', 'is_use', 'create_at']
    }
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    },
    audit (id) {
      this.$confirm({
        title: '图片审核',
        content: '',
        centered: true,
        okText: '通过',
        cancelText: '驳回',
        onOk () {
          this.$router.push('/picture/audit/' + id)
        },
        onCancel () {
          this.$router.push('/picture/reject/' + id)
        }
      })
    },
    update (res) {
      for (const i in res) {
        Object.assign(this.columns[i], res[i])
      }
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.queryParams = to.query
    next()
  }
}
</script>
