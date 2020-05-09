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
  provide () {
    return { queryParams: {} }
  },
  data () {
    return {
      description: '会员积分列表',
      controller: 'index/user_score',
      columns: {
        user_id: { title: '用户ID', type: 'string' },
        score: { title: '积分', type: 'string' },
        type: {
          title: '类型',
          type: 'state',
          options: ['系统操作', '购买商品', '签到', '退款'],
          rules: [{ required: true }]
        },
        desc: { title: '描述', type: 'string' },
        create_at: {
          title: '时间',
          type: 'date',
          format: 'YYYY-MM-DD',
          rules: [{ required: true }]
        }
      },
      // 列表项
      listItems: ['user_id', 'score', 'desc', 'type', 'create_at'],
      // 表单
      formItems: ['user_id', 'score', 'desc', 'type', 'create_at'],
      findItems: ['user_id', 'score', 'type']
    }
  },
  methods: {}
}
</script>
