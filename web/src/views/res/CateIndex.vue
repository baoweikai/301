<template>
  <div>
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
    <Edit ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
    <Add ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
  </div>
</template>

<script>
import { List, Edit, Add } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Edit, Add },
  data () {
    return {
      description: '分类',
      controller: 'cate',
      columns: {
        id: { title: 'ID', type: 'string' },
        name: { title: '名称', type: 'string', rules: [{ required: true }] },
        status: {
          title: '状态', type: 'switch', options: ['关闭', '启用'], rules: [{ required: true }]
        },
        create_at: {
          title: '创建时间', type: 'date', format: 'YYYY-MM-DD'
        },
        update_at: {
          title: '创建时间', type: 'date', format: 'YYYY-MM-DD'
        },
        action: {
          title: '操作', type: 'action', actions: ['edit']
        }
      },
      // 列表项
      listItems: ['name', 'status', 'create_at', 'action'],
      // 表单
      formItems: ['name', 'desc'],
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
