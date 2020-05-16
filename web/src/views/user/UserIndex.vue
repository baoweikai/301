<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
    <Edit
      ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }"
    />
    <Add
      ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller }"
    />
  </div>
</template>

<script>
import { List, Find, Add, Edit } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Find, Add, Edit },
  provide () {
    return { queryParams: {} }
  },
  data () {
    return {
      description: '会员列表',
      controller: 'user',
      columns: {
        id: { title: 'ID', type: 'string' },
        account: { title: '用户名', type: 'string', rules: [{ required: true }] },
        phone: { title: '手机号', type: 'string' },
        status: { title: '状态', type: 'state', options: ['封禁', '正常'] },
        create_at: { title: '注册时间', type: 'date', format: 'YYYY-MM-DD' },
        action: { title: '操作', type: 'action', actions: ['edit'] }
      },
      // 列表项
      listItems: ['id', 'account', 'email', 'phone', 'state', 'create_at', 'action'],
      // 表单
      formItems: ['account', 'phone'],
      findItems: ['account', 'phone', 'status']
    }
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    }
  }
}
</script>
