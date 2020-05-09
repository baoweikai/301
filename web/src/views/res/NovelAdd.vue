<template>
  <a-form :form="form">
    <item v-for="(column, index) in columns" :key="index" v-bind="{ column: column, index: index }" />
    <div class="submit-item">
      <a-row type="flex" justify="center">
        <a-button @click="submit()" type="primary">提交</a-button>
        <a-button style="margin-left: 8px" @click="$router.go(-1)">返回</a-button>
      </a-row>
    </div>
  </a-form>
</template>

<script>
import { page, form } from '@/utils'
import Item from '@/components/Item/Item'

export default {
  mixins: [page, form],
  components: { Item },
  data () {
    return {
      description: '编辑文章',
      controller: 'novel',
      columns: {
        title: { title: '标题', type: 'string' },
        cate_id: {
          title: '分类', type: 'cascader', rules: [{ required: true }], customRender: (text, record) => record.cate_name
        },
        tagIds: {
          title: '标签', type: 'select', rules: [{ required: true }], mode: 'multiple', showSearch: true
        },
        keywords: { title: '关键字', type: 'string' },
        desc: { title: '描述', type: 'textarea', rules: [{ required: false }] },
        content: { title: '内容', type: 'editor', label: false, rules: [{ required: true }] },
        exame_status: {
          title: '审核状态', type: 'state', options: ['未审核', '审核通过', '审核未通过'], rules: [{ required: true }]
        },
        status: {
          title: '状态', type: 'state', options: ['正常', '删除', '禁用'], rules: [{ required: true }]
        },
        cover: { title: '封面', type: 'image', rules: [{ required: true }] }
      }
    }
  },
  mounted () {
    if (this.$route.params.id) {
      this.edit(this.$route.params.id)
    }
    // this.edit(this.$route.params.id)
  },
  methods: {
  }
}
</script>
