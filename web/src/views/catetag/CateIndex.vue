<template>
  <div>
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
    <Edit ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
  </div>
</template>

<script>
import { List, Edit } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { Edit, List },
  data () {
    return {
      description: '分类列表',
      controller: 'cate',
      columns: {
        id: { title: 'ID', type: 'string' },
        name: { title: '名称', type: 'string', rules: [{ required: true }] },
        status: {
          title: '状态', type: 'state', options: ['关闭', '启用'], rules: [{ required: true }]
        },
        image: {
          title: '图片', type: 'image'
        },
        type: { title: '类型', type: 'radio', options: ['视频', '图片', '小说'] },
        desc: { title: '内容', type: 'textarea' },
        create_at: {
          title: '创建时间', type: 'date', format: 'YYYY-MM-DD'
        },
        action: {
          title: '操作', type: 'action', actions: ['edit', 'del']
        }
      },
      // 列表项
      listItems: ['name', 'type', 'status', 'create_at', 'action'],
      // 表单
      formItems: ['name', 'image', 'status', 'desc'],
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
