<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List
      ref="list"
      v-bind="{ columns: pick(columns, listItems), controller: controller, data: list, queryParams: queryParams }"
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
      description: '视频列表',
      controller: 'novel',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        title: { title: '标题', type: 'string', rules: [{ required: true }], ellipsis: true },
        cate_id: {
          title: '分类', type: 'radio', rules: [{ required: true }], options: {}
        },
        create_by: { title: '创建者', type: 'radio', options: {} },
        tagIds: {
          title: '标签', type: 'checkbox', rules: [{ required: true }], options: {}
        },
        keywords: { title: '关键字', type: 'string' },
        desc: { title: '描述', type: 'textarea', maxLength: 255, rules: [{ required: true }] },
        content: { title: '内容', type: 'editor', label: false, rules: [{ required: true }] },
        exame_status: {
          title: '审核状态', type: 'state', options: ['未审核', '审批中', '审核通过', '审核未通过'], rules: [{ required: true }]
        },
        status: {
          title: '状态', type: 'state', options: ['正常', '删除', '禁用'], rules: [{ required: true }]
        },
        cover: { title: '封面', type: 'image', rules: [{ required: true }] },
        creater_name: { title: '创建者', type: 'string' },
        create_at: { title: '创建日期', type: 'date', format: 'MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'MM-DD HH:mm' },
        action: { title: '操作', type: 'action', actions: ['edit', 'audit', 'del'] }
      },
      // 列表项
      listItems: ['id', 'title', 'cate_id', 'tagIds', 'status', 'cover', 'exame_status', 'creater_name', 'create_at', 'update_at', 'action'],
      // 表单
      formItems: ['title', 'cate_id', 'tagIds', 'desc', 'cover', 'content'],
      findItems: ['id', 'title', 'status', 'exame_status', 'create_by', 'cate_id']
    }
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
        okText: '通过',
        cancelText: '驳回',
        onOk () {
          this.$router.push('/picture/audit/' + id)
        },
        onCancel () {
          this.$router.push('/picture/reject/' + id)
        }
      })
    },
    update (res) {
      for (const i in res) {
        Object.assign(this.columns[i], res[i])
      }
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.queryParams = to.query
    next()
  }
}
</script>
