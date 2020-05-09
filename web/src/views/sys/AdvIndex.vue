<template>
  <div>
    <Find :columns="pick(columns, findItems)" @fresh="params => $refs.list.fresh(params)" />
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
      description: '推广页管理',
      controller: 'index/adv',
      columns: {
        id: { title: 'ID', type: 'string' },
        serial: { title: '排序', type: 'number' },
        thumb: { title: '缩略图', type: 'image' },
        title: { title: '标题', type: 'string', rules: [{ required: true }] },
        url: { title: '链接', type: 'string' },
        desc: { title: '备注', type: 'textarea' },
        block_id: {
          title: '位置', type: 'radio', options: ['推广页顶图', '首页顶图', '首页通栏'], rules: [{ required: true }]
        },
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
      listItems: ['serial', 'title', 'block_id', 'thumb', 'image', 'state', 'create_at', 'action'],
      // 表单
      formItems: ['title', 'subtitle', 'serial', 'block_id', 'url', 'state', 'image', 'thumb', 'desc'],
      findItems: ['title', 'block_id', 'state']
    }
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    }
  }
}
</script>
