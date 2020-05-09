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
import Item from '@/components/Item'

export default {
  mixins: [page, form],
  components: { Item },
  data () {
    return {
      description: '编辑文章',
      controller: 'index/article',
      // 表单
      columns: {
        title: { title: '标题', type: 'string', rules: [{ required: true }] },
        keywords: { title: '关键词', type: 'string' },
        hits: { title: '点击量', type: 'string' },
        thumb: { title: '头像', type: 'image', rules: [{ message: '请上传图片' }] },
        publish_at: { title: '发布时间', type: 'date', format: 'YYYY-MM-DD', rules: [{ required: true }] },
        fromlink: { title: '来源', type: 'string' },
        desc: { title: '简介', type: 'textarea', label: false },
        content: { title: '内容', type: 'editor', label: false, rules: [{ required: true }] }
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
