<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
    <Edit ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
  </div>
</template>

<script>
import { List, Edit, Find } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { Edit, List, Find },
  data () {
    return {
      description: '管理员列表',
      controller: 'tag',
      columns: {
        id: { title: 'ID', type: 'string' },
        name: { title: '标题', type: 'string', rules: [{ required: true }] },
        status: { title: '状态', type: 'radio', options: ['禁用', '启用'], rules: [{ required: true }] },
        serial: { title: '排序', type: 'number' },
        create_at: { title: '添加时间', type: 'date', format: 'YYYY-MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'YYYY-MM-DD' },
        action: { title: '操作', type: 'action', actions: ['edit', 'state'] }
      },
      // 列表项
      listItems: ['id', 'name', 'serial', 'status', 'create_at', 'action'],
      // 表单
      formItems: ['name', 'serial'],
      findItems: ['title', 'state', 'create_at']
    }
  },
  methods: {
  }
}
</script>
