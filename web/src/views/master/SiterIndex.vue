<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller, batch: [] }" />
    <Edit ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
  </div>
</template>

<script>
import { List, Find, Edit } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Find, Edit },
  data () {
    return {
      description: '站点列表',
      controller: 'siter',
      columns: {
        id: { title: '站点ID', type: 'string' },
        name: { title: '名称', type: 'string' },
        master_id: {
          title: '站长', type: 'relate', controller: 'master', rules: [{ required: true }], customRender: (text, record) => record.master_name
        },
        agent_id: {
          title: '代理', type: 'relate', controller: 'master', rules: [{ required: true }], customRender: (text, record) => record.agent_name
        },
        domain: { title: '域名', type: 'string', customRender: (text, record) => record.protocol + '://' + text },
        ip: { title: 'IP', type: 'string' },
        version: { title: '程序版本', type: 'number' },
        push_date: { title: '数据推送日期', type: 'string' },
        create_at: { title: '创建时间', type: 'date', format: 'MM-DD HH:mm', rules: [{ required: true }] },
        action: { title: '操作', type: 'action', actions: ['edit'] }
      },
      // 列表项
      listItems: ['id', 'master_id', 'agent_id', 'domain', 'ip', 'version', 'push_date', 'create_at', 'action'],
      // 表单
      formItems: ['name', 'domain', 'ip', 'master_id', 'version', 'push_date'],
      findItems: ['name', 'master_id', 'domain', 'push_date']
    }
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    }
  }
}
</script>
