<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
    <Edit ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
    <Add ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
    <Password ref="password" />
  </div>
</template>

<script>
import { List, Add, Edit, Find } from '@/components'
import Password from './_password'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { Add, Edit, List, Find, Password },
  data () {
    return {
      description: '管理员列表',
      pk: 0,
      controller: 'admin',
      columns: {
        id: { title: 'ID', type: 'string' },
        username: { title: '用户名', type: 'string' },
        status: {
          title: '状态', type: 'state', options: ['禁用', '正常', '删除'], rules: [{ required: true }]
        },
        role_id: {
          title: '角色', type: 'radio', options: {}
        },
        last_at: { title: '最后登录', type: 'datetime', format: 'MM-DD' },
        create_at: {
          title: '注册时间', type: 'date', format: 'YYYY-MM-DD', rules: [{ required: true }]
        },
        action: {
          title: '操作', type: 'action', actions: ['edit', 'state', 'password']
        }
      },
      // 列表项
      listItems: ['id', 'username', 'status', 'last_at', 'create_at', 'action'],
      // 表单
      formItems: ['username', 'role_id', 'state', 'desc'],
      findItems: ['username', 'mobile', 'state']
    }
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    },
    password (id) {
      this.$refs.password.pk = id
      this.$refs.password.visible = true
    }
  }
}
</script>
