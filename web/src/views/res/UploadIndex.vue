<template>
  <div>
    <Find :columns="pick(columns, findItems)" />
    <List ref="list" v-bind="{ columns: pick(columns, listItems), controller: controller, queryParams: queryParams, batch: ['add', 'import'] }" />
    <Edit
      ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: queryParams }"
    />
    <Add
      ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: queryParams }"
    />
  </div>
</template>

<script>
import { List, Find, Edit, Add } from '@/components'
import { page, http } from '@/utils'

export default {
  mixins: [page],
  components: { List, Find, Edit, Add },
  data () {
    return {
      description: '视频列表',
      controller: 'video-upload',
      queryParams: {},
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        title: { title: '标题', type: 'string', rules: [{ required: true }], ellipsis: true },
        creater_name: { title: '创建者', type: 'string' },
        route_id: { title: '路由', type: 'radio', rules: [{ required: true }], options: {} },
        create_by: { title: '创建者', type: 'radio', options: {} },
        source_path: { title: '源视频路径', type: 'string', rules: [{ required: true }], customRender: (v, record) => record.play_host + v },
        cate_id: {
          title: '分类', type: 'radio', rules: [{ required: true }], options: {}
        },
        tagIds: {
          title: '标签', type: 'checkbox', rules: [{ required: true }], options: {}
        },
        keywords: { title: '关键字', type: 'string' },
        desc: { title: '描述', type: 'textarea', maxLength: 255, rules: [{ required: true }] },
        create_at: { title: '添加日期', type: 'date', format: 'MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'MM-DD HH:mm' },
        action: { title: '操作', type: 'action', actions: ['edit', 'del', 'preview'] }
      },
      // 列表项
      listItems: ['id', 'title', 'cate_id', 'tagIds', 'creater_name', 'title', 'source_path', 'create_at', 'action'],
      // 表单
      formItems: ['title', 'cate_id', 'tagIds', 'route_id', 'source_path', 'desc'],
      // 搜索项
      findItems: ['id', 'title', 'cate_id', 'create_by', 'create_at']
    }
  },
  created () {
  },
  methods: {
    edit (id) {
      this.$refs.edit.load(id)
    },
    preview (id, record) {
      window.open(record.play_host + record.source_path)
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
