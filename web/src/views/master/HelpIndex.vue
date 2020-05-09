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
      description: '帮助文档',
      controller: 'index/help',
      columns: {
        id: { title: 'ID', type: 'string' },
        serial: { title: '排序', type: 'number' },
        title: { title: '标题', type: 'string' },
        content: { title: '备注', type: 'textarea' },
        state: {
          title: '状态', type: 'state', options: ['禁用', '正常', '删除'], rules: [{ required: true }]
        },
        create_at: {
          title: '添加时间', type: 'date', format: 'YYYY-MM-DD', rules: [{ required: true }]
        },
        action: {
          title: '操作', type: 'action', actions: ['edit', 'del']
        }
      },
      // 列表项
      listItems: ['serial', 'title', 'subtitle', 'icon', 'state', 'create_at', 'action'],
      // 表单
      formItems: ['title', 'serial', 'content', 'state'],
      findItems: ['title']
    }
  },
  methods: {
  }
}
</script>
