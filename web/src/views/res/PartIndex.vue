<template>
  <a-modal :bodyStyle="bodyStyle" :centered="true" :closable="false" :width="800" :visible="modalName == 'list'" okText="添加" @ok="$refs.add.visible = true" @cancel="modalName = ''">
    <a-spin :spinning="spinning">
      <List
        ref="list"
        v-bind="{ columns: pick(columns, listItems), controller: controller, data: list, queryParams: params, batch: [] }"
        @edit="id => $refs.edit.load(id)"
        @update="update"
      />
      <Edit
        ref="edit" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: params }"
      />
      <Add
        ref="add" v-bind="{ columns: pick(columns, formItems), controller: controller, defaults: params }"
      />
    </a-spin>
  </a-modal>
</template>

<script>
import { List, Edit, Add } from '@/components'
import { page } from '@/utils'

export default {
  mixins: [page],
  components: { List, Edit, Add },
  props: {
    params: { type: Object, default: () => ({}) }
  },
  data () {
    return {
      bodyStyle: { maxHeight: '80vh', overflowY: 'scroll' },
      spinning: false,
      description: '视频列表',
      controller: 'video-upload',
      list: [],
      columns: {
        id: { title: 'ID', type: 'string' },
        chapter: { title: '第几集', type: 'string', ellipsis: true },
        source_path: { title: '源视频路径', type: 'string' },
        create_at: { title: '添加日期', type: 'date', format: 'MM-DD' },
        update_at: { title: '更新时间', type: 'date', format: 'MM-DD HH:mm' },
        action: { title: '操作', type: 'action', actions: ['edit', 'del'] }
      },
      // 列表项
      listItems: ['chapter', 'source_path', 'action'],
      // 表单
      formItems: ['chapter', 'source_path']
    }
  },
  created () {
  },
  methods: {
    edit (id) {
      this.$router.push('/video-upload/edit/' + id)
    },
    change (index, val) {
      if (index === 'type') {
        this.columns.source_path.type = 'number'
      }
    },
    update (res) {
      for (const i in res) {
        Object.assign(this.columns[i], res[i])
      }
    },
    fresh (params = {}) {
      console.log(params)
      this.$refs.list.fresh()
    }
  },
  wath: {
    video_id (val) {
      this.queryParams = { video_id: val }
    }
  }
}
</script>
