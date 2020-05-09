<template>
  <div>
		<List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
  </div>
</template>

<script>
import { page } from '@/utils'

export default {
  mixins: [page],
  components: {},
  data () {
    return {
      description: '用户列表',
      pk: 0,
      controller: 'me',
      columns: {
        id: { title: 'ID', type: 'string' },
        username: { title: '用户名', type: 'string' },
        mail: { title: '邮箱', type: 'string' },
        mobile: { title: '手机号', type: 'string' },
        state: {
          title: '状态', type: 'state', options: ['正常', '封禁'],
          rules: [{ required: true }]
        },
        login_at: {
          title: '登录时间', type: 'date', format: 'YYYY-MM-DD',
          rules: [{ required: true }]
        },
        create_at: {
          title: '注册时间', type: 'date', format: 'YYYY-MM-DD',
          rules: [{ required: true }]
        },
        action: {
          title: '操作', type: 'action', actions: ['edit', 'del']
        }
      },
      // 列表项
      listItems: ['id', 'username', 'mail', 'mobile', 'state', 'login_at', 'create_at', 'action'],
      // 表单
      formItems: ['username', 'mail', 'mobile', 'state'],
      findItems: []
    }
  },
  methods: {
  }
}
</script>
