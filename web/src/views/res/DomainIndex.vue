<template>
  <div>
    <Find :columns="pick(columns, findItems)" @fresh="params => $refs.list.fresh(params)" />
    <List
      ref="list"
      v-bind="{ columns: pick(columns, listItems), controller: controller, data: list, queryParams: queryParams }"
      @edit="id => $refs.edit.load(id)"
    />
    <Edit
      ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }"
    />
    <Add
      ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller }"
    />
  </div>
</template>

<script>
import { Find, List, Edit, Add } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Find, Edit, Add },
  data () {
    return {
      description: '域名',
      controller: 'domain',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        user_id: {
          title: '用户', type: 'relate', rules: [{ required: true }], customRender: (v, record) => record.account
        },
        shield_host: { title: '域名', type: 'string', rules: [{ required: true }] },
        jump_host: { title: '跳转地址', type: 'string', rules: [{ required: true }] },
        expire_at: {
          title: '过期日期', type: 'date', format: 'YYYY-MM-DD', rules: [{ required: true }]
        },
        is_param: {
          title: '带参数', type: 'switch', options: ['否', '是'], rules: [{ required: true }]
        },
        is_open: {
          title: '引流', type: 'switch', options: ['关闭', '开启'], rules: [{ required: true }]
        },
        percent: {
          title: '引流概率', type: 'number', rules: [{ required: true }], customRender: v => v + '%'
        },
        group_id: {
          title: '引流分组', type: 'radio', options: {}, rules: [{ required: true }], customRender: (v, record) => record.group_name
        },
        status: {
          title: '状态', type: 'switch', options: ['失效', '正常'], rules: [{ required: true }]
        },
        cited_range: {
          title: '引流时段', type: 'checkbox', options: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23']
        },
        create_at: { title: '创建日期', type: 'date', format: 'MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'MM-DD HH:mm' },
        action: { title: '操作', type: 'action', actions: ['edit', 'audit', 'del'] }
      },
      // 列表项
      listItems: ['id', 'user_id', 'shield_host', 'jump_host', 'expire_at', 'is_open', 'percent', 'group_id', 'status', 'create_at', 'update_at', 'action'],
      // 表单
      formItems: ['shield_host', 'jump_host', 'expire_at', 'is_param', 'is_open', 'percent', 'group_id', 'cited_range', 'status'],
      findItems: ['id', 'shield_host', 'jump_host', 'is_open', 'status', 'exame_status']
    }
  },
  created () {
    // this.load(this.controller + '/index')
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    },
    audit (id) {
      this.$confirm({
        title: '图片审核',
        content: '',
        centered: true,
        onOk () {
          this.$router.push('/picture/audit/' + id)
        },
        onCancel () {
          this.$router.push('/picture/audit/' + id)
        }
      })
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.queryParams = to.query
    next()
  }
}
</script>
