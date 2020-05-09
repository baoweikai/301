<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller }" />
    <Edit ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
    <Add ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: queryParams }" />
  </div>
</template>

<script>
import { List, Edit, Find, Add } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { Edit, List, Find, Add },
  data () {
    return {
      description: '站长列表',
      controller: 'master',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        username: { title: '名称', type: 'string' },
        email: { title: '电子邮箱', type: 'string' },
        site_count: { title: '站点数', type: 'number' },
        state: {
          title: '状态', type: 'state', options: ['禁用', '正常', '删除'], rules: [{ required: true }]
        },
        login_at: { title: '最后登录', type: 'datetime', format: 'YYYY-MM-DD HH:II' },
        create_at: {
          title: '注册时间', type: 'date', format: 'YYYY-MM-DD', rules: [{ required: true }]
        },
        action: {
          title: '操作', type: 'action', actions: ['edit', 'del']
        }
      },
      // 列表项
      listItems: ['id', 'username', 'email', 'site_count', 'state', 'create_at', 'login_at', 'action'],
      // 表单
      formItems: ['username', 'state', 'memo'],
      findItems: ['username', 'state']
    }
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    }
  }
}
</script>
