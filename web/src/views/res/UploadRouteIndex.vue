<template>
  <div>
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
    <Edit ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: queryParams }" />
    <Add
      ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: queryParams }"
    />
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
      bodyStyle: { maxHeight: '80vh', overflowY: 'scroll' },
      description: '线路列表',
      controller: 'video-upload-route',
      list: [],
      queryParams: this.$route.query,
      columns: {
        id: { title: 'ID', type: 'string' },
        title: { title: '名称', type: 'string', rules: [{ required: true }] },
        host: { title: '服务器地址', type: 'string', rules: [{ required: true }] },
        create_at: { title: '添加日期', type: 'date', format: 'MM-DD' },
        action: { title: '操作', type: 'action', actions: ['edit', 'del'] }
      },
      // 列表项
      listItems: ['id', 'title', 'host', 'action'],
      // 表单
      formItems: ['title', 'host']
    }
  },
  created () {
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    }
  }
}
</script>
