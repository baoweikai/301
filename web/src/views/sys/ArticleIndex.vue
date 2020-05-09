<template>
  <div>
    <Find :columns="pick(columns, findItems)" @fresh="params => $refs.list.fresh(params)" />
    <List
      ref="list"
      v-bind="{ columns: pick(columns, listItems), controller: controller }"
      @edit="id => $router.push({ path: '/article/edit/' + id })"
    />
  </div>
</template>

<script>
import { List, Find } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Find },
  data () {
    return {
      description: '公告咨询',
      controller: 'index/article',
      columns: {
        id: { title: 'ID', type: 'string' },
        title: { title: '标题', type: 'string' },
        thumb: { title: '缩略图', type: 'image' },
        hits: { title: '点击量', type: 'string' },
        state: {
          title: '状态', type: 'state', options: ['隐藏', '展示'], rules: [{ required: true }]
        },
        publish_at: { title: '发布时间', type: 'date', format: 'YYYY-MM-DD' },
        action: {
          title: '操作', type: 'action', actions: ['edit', 'state']
        }
      },
      // 列表项
      listItems: ['id', 'title', 'thumb', 'hits', 'publish_at', 'state', 'action'],
      // 表单
      formItems: ['title', 'thumb', 'hits', 'state', 'publish_at'],
      findItems: ['title', 'publish_at', 'state']
    }
  },
  methods: {
    edit (id) {
      this.$router.push('/article/edit/' + id)
    }
  }
}
</script>
