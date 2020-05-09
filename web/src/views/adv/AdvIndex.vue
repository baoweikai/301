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
  components: { Edit, Add, List },
  data () {
    return {
      description: '分类列表',
      controller: 'adv',
      columns: {
        id: { title: 'ID', type: 'string' },
        title: { title: '名称', type: 'string', rules: [{ required: true }] },
        status: {
          title: '状态', type: 'state', options: ['关闭', '启用'], rules: [{ required: true }]
        },
        picture: { title: '图片链接', type: 'string', rules: [{ required: true }] },
        link: { title: '跳转地址', type: 'string', rules: [{ required: true }] },
        put_volume: { title: '投放量', type: 'number' },
        width: { title: '宽度', type: 'number' },
        height: { title: '高度', type: 'number' },
        serial: { title: '排序', type: 'number' },
        create_at: { title: '创建时间', type: 'date', format: 'YYYY-MM-DD' },
        action: { title: '操作', type: 'action', actions: ['edit', 'del'] }
      },
      // 列表项
      listItems: ['id', 'title', 'picture', 'status', 'link', 'put_volume', 'width', 'height', 'action'],
      // 表单
      formItems: ['title', 'picture', 'link', 'put_volume', 'serial', 'status', 'width', 'height'],
      findItems: ['title']
    }
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    }
  }
}
</script>
