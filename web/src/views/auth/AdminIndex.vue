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
        email: { title: '邮箱', type: 'string' },
        mobile: { title: '手机号', type: 'string' },
        qq: { title: 'QQ', type: 'string' },
        state: {
          title: '状态', type: 'state', options: ['禁用', '正常', '删除'], rules: [{ required: true }]
        },
        role_id: {
          title: '角色', type: 'radio', options: {}
        },
        desc: { title: '备注', type: 'string' },
        create_at: {
          title: '注册时间', type: 'date', format: 'YYYY-MM-DD', rules: [{ required: true }]
        },
        action: {
          title: '操作', type: 'action', actions: ['edit', 'state', 'password']
        }
      },
      // 列表项
      listItems: ['id', 'username', 'email', 'role_id', 'qq', 'state', 'create_at', 'desc', 'action'],
      // 表单
      formItems: ['username', 'email', 'role_id', 'mobile', 'qq', 'state', 'desc'],
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
