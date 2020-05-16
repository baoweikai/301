<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller, queryParams: queryParams, batch: [] }" />
    <Edit
      ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: queryParams }"
    />
    <Add
      ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: queryParams }"
    />
  </div>
</template>

<script>
import { List, Find, Edit, Add } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Find, Edit, Add },
  data () {
    return {
      description: '日报',
      controller: 'stat',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        date: { title: '日期', type: 'date' },
        domain_id: { title: '域名', type: 'string', customRender: (v, record) => v === 0 ? '未入库域名' : record.shield_host },
        ip_count: { title: 'ip量', type: 'number' },
        jump_count: { title: '跳转', type: 'number' },
        cited_count: { title: '引量', type: 'number' },
        create_at: { title: '添加日期', type: 'date', format: 'MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'MM-DD HH:mm' },
        action: { title: '操作', type: 'action', actions: ['edit', 'del', 'preview'] }
      },
      // 列表项
      listItems: ['id', 'date', 'domain_id', 'ip_count', 'jump_count', 'cited_count'],
      // 表单
      formItems: ['date', 'domain_id', 'ip_count', 'jump_count', 'cited_count'],
      // 搜索项
      findItems: ['date', 'domain_id']
    }
  },
  created () {
  },
  methods: {
  },
  beforeRouteUpdate (to, from, next) {
    this.queryParams = to.query
    next()
  }
}
</script>
