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
      description: '域名',
      controller: 'cited-domain',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        host: { title: '引流地址', type: 'string', rules: [{ required: true }] },
        group_id: {
          title: '分组', type: 'radio', options: {}, rules: [{ required: true }], customRender: (v, record) => record.group_name
        },
        weight: {
          title: '权重', type: 'string', rules: [{ required: true }]
        },
        status: {
          title: '状态', type: 'switch', options: ['关闭', '启用'], rules: [{ required: true }]
        },
        create_at: { title: '创建时间', type: 'date', format: 'MM-DD' },
        action: { title: '操作', type: 'action', actions: ['edit', 'audit', 'del'] }
      },
      // 列表项
      listItems: ['id', 'host', 'group_id', 'weight', 'status', 'create_at', 'action'],
      // 表单
      formItems: ['host', 'group_id', 'weight', 'status'],
      findItems: ['host', 'group_id', 'status']
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
