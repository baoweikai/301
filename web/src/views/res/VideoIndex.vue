<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller, data: list, queryParams: queryParams, batch: [] }" />
    <Edit ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
    <Add ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller }" />
  </div>
</template>

<script>
import { Find, List, Edit, Add } from '@/components'
import { page, http } from '@/utils'

export default {
  mixins: [page],
  components: { List, Find, Edit, Add },
  data () {
    return {
      description: '视频列表',
      spinning: false,
      controller: 'video',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        title: { title: '标题', type: 'string', rules: [{ required: true }] },
        type: {
          title: '展示类型', type: 'radio', rules: [{ required: true }], options: ['普通', '精品', '热门', '推荐']
        },
        cate_id: {
          title: '分类', type: 'radio', rules: [{ required: true }], options: {}
        },
        route_id: {
          title: '线路', type: 'radio', rules: [{ required: true }], options: {}
        },
        tagIds: {
          title: '标签', type: 'checkbox', rules: [{ required: true }], options: {}
        },
        keywords: { title: '关键字', type: 'string' },
        desc: { title: '描述', type: 'textarea', maxLength: 255, rules: [{ required: true }] },
        flow_status: {
          title: '审核', type: 'state', options: ['未审核', '审核中', '审核通过', '审核驳回'], rules: [{ required: true }]
        },
        status: {
          title: '状态', type: 'state', options: ['正常', '删除', '禁用'], rules: [{ required: true }]
        },
        cover: { title: '封面', type: 'image', rules: [{ required: true }] },
        path: { title: '源视频路径', type: 'string', rules: [{ required: true }], customRender: (v, record) => record.play_host + v },
        create_at: { title: '创建日期', type: 'date', format: 'MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'MM-DD HH:mm' },
        action: { title: '操作', type: 'action', actions: ['edit', 'audit', 'del', 'preview'] }
      },
      // 列表项
      listItems: ['id', 'title', 'type', 'cover', 'cate_id', 'tagIds', 'status', 'path', 'create_at', 'update_at', 'action'],
      // 表单
      formItems: ['title', 'type', 'cate_id', 'tagIds', 'desc'],
      findItems: ['id', 'title', 'type', 'status', 'cate_id', 'route_id', 'create_at']
    }
  },
  created () {
  },
  methods: {
    preview (id, record) {
      window.open(record.play_host + '/share/' + record.shareid)
    },
    edit (id) {
      this.$refs.edit.load(id)
    },
    part (id) {
      this.vid = id
      this.modalName = 'list'
    },
    flow (id) {
      this.$confirm({
        title: '视频审批',
        content: '',
        centered: true,
        cancelText: '驳回',
        okText: '通过',
        onOk () {
          http.post('video/adopt/' + id).then(res => {
          })
        },
        onCancel () {
          http.post('video/reject/' + id).then(res => {
          })
        }
      })
    },
    change (index, val) {
      if (index === 'cate_id') {
        http.get('tag/options/' + val.target.value).then(res => {
          this.columns.tagIds.options = res
        })
      }
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.queryParams = to.query
    next()
  }
}
</script>
