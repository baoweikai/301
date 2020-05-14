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
import { page, http } from '@/utils'

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
        domain_id: { title: '跳转地址', type: 'string', customRender: (v, record) => record.jump_url },
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
      findItems: ['id', 'date', 'domain_id', 'create_at']
    }
  },
  created () {
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    },
    preview (id, record) {
      window.open(record.play_host + record.source_path)
    },
    change (index, val) {
      if (index === 'cate_id') {
        http.get('tag/options/' + val.target.value).then(res => {
          this.columns.tagIds.options = res
        })
      }
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.queryParams = to.query
    next()
  }
}
</script>
