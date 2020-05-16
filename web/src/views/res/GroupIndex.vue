<template>
  <div>
    <Find :columns="pick(columns, findItems)" @fresh="params => $refs.list.fresh(params)" />
    <List
      ref="list"
      v-bind="{ columns: pick(columns, listItems), controller: controller, data: list, queryParams: queryParams }"
      @edit="id => $refs.edit.load(id)"
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
      description: '分组',
      controller: 'group',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        name: {
          title: '名称', type: 'string', rules: [{ required: true }]
        },
        is_default: {
          title: '默认', type: 'switch', options: ['否', '是'], rules: [{ required: true }]
        },
        status: {
          title: '状态', type: 'switch', options: ['关闭', '开启'], rules: [{ required: true }]
        },
        create_at: { title: '创建日期', type: 'date', format: 'MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'MM-DD HH:mm' },
        action: { title: '操作', type: 'action', actions: ['edit', 'audit'] }
      },
      // 列表项
      listItems: ['id', 'name', 'is_default', 'status', 'create_at', 'action'],
      // 表单
      formItems: ['name', 'is_default', 'status'],
      findItems: ['name', 'is_default', 'status', 'create_at']
    }
  },
  created () {
    // this.load(this.controller + '/index')
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
        onOk () {
          this.$router.push('/picture/audit/' + id)
        },
        onCancel () {
          this.$router.push('/picture/audit/' + id)
        }
      })
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.queryParams = to.query
    next()
  }
}
</script>
