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
      description: '会员列表',
      controller: 'user',
      columns: {
        id: { title: 'ID', type: 'string' },
        username: { title: '用户名', type: 'string' },
        avatar: { title: '头像', type: 'image' },
        sex: {
          title: '性别',
          type: 'state',
          options: ['女', '男'],
          rules: [{ required: true }]
        },
        email: { title: '邮箱', type: 'string' },
        mobile: { title: '手机号', type: 'string' },
        state: {
          title: '状态',
          type: 'state',
          options: ['封禁', '正常'],
          rules: [{ required: true }]
        },
        create_at: {
          title: '注册时间',
          type: 'date',
          format: 'YYYY-MM-DD',
          rules: [{ required: true }]
        },
        action: {
          title: '操作',
          type: 'action',
          actions: ['state']
        }
      },
      // 列表项
      listItems: ['id', 'username', 'email', 'mobile', 'avatar', 'sex', 'state', 'create_at', 'action'],
      // 表单
      formItems: ['state'],
      findItems: ['username', 'mobile', 'state']
    }
  },
  methods: {}
}
</script>
