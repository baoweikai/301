<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
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
      description: '广告列表',
      controller: 'adv-stat',
      columns: {
        id: { title: 'ID', type: 'string' },
        siter_id: { title: '站点', type: 'relate', rules: [{ required: true }] },
        adv_id: { title: '广告', type: 'relate', options: ['禁用', '启用'], rules: [{ required: true }] },
        impression: { title: '展示次数', type: 'number' },
        date: { title: '日期', type: 'date', format: 'YYYY-MM-DD' },
        action: { title: '操作', type: 'action', actions: ['edit', 'state'] }
      },
      // 列表项
      listItems: ['id', 'siter_id', 'adv_id', 'impression', 'date', 'action'],
      // 搜索
      findItems: ['siter_id', 'adv_id', 'date']
    }
  },
  methods: {
  }
}
</script>
