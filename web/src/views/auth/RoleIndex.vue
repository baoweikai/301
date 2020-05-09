<template>
  <div>
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" @edit="id => $refs.edit.load(id)" />
    <Edit
      ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }"
      @fresh="params => $refs.list.fresh(params)"
    />
  </div>
</template>

<script>
import { List, Edit } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Edit },
  data () {
    return {
      description: '角色列表',
      controller: 'role',
      data: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        name: { title: '权限名称', type: 'string' },
        state: {
          title: '状态',
          type: 'state',
          options: ['禁用', '启用'],
          rules: [{ required: true }]
        },
        desc: { title: '备注', type: 'string' },
        create_at: {
          title: '创建时间',
          type: 'date',
          format: 'YYYY-MM-DD',
          rules: [{ required: true }]
        },
        action: {
          title: '操作',
          type: 'action',
          actions: ['edit', 'state', 'view']
        }
      },
      // 列表项
      listItems: ['id', 'name', 'state', 'create_at', 'desc', 'action'],
      // 表单
      formItems: ['name', 'state', 'desc'],
      findItems: []
    }
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    }
  }
}
</script>
