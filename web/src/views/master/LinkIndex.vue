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
      description: '推广链接列表',
      controller: 'index/link',
      columns: {
        id: { title: 'ID', type: 'string' },
        title: { title: '标题', type: 'string' },
        url: { title: '链接地址', type: 'string' },
        image: { title: '图片', type: 'string' },
        dist_id: { title: '视频位', type: 'string' }
      },
      // 列表项
      listItems: ['id', 'title', 'image', 'content', 'adv_id', 'create_at'],
      // 表单
      formItems: ['title', 'image', 'content', 'adv_id', 'create_at'],
      findItems: ['title', 'create_at']
    }
  },
  methods: {
  }
}
</script>
