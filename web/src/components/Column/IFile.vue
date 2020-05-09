<template>
  <a-upload
    listType="picture-card"
    class="avatar-uploader"
    accept="video/*,image/*"
    :showUploadList="false"
    :action="action"
    :customRequest="customRequest"
    :beforeUpload="beforeUpload"
    @change="handleUpload"
  >
    <span v-if="file">{{ file }}</span>
    <div v-else>
      <a-icon :type="loading ? 'loading' : 'plus'" />
      <div class="ant-upload-text">Upload</div>
    </div>
  </a-upload>
</template>
<script>
import upload from '@/utils/mixin/upload'
import { http } from '@/utils'

export default {
  name: 'IFile',
  mixins: [upload],
  // 如果你需要得到当前的editor对象来做一些事情，你可以像下面这样定义一个方法属性来获取当前的editor对象，实际上这里的$refs对应的是当前组件内所有关联了ref属性的组件元素对象
  computed: {
    file: {
      get () {
        return this.$attrs['data-__field'].value
      },
      set (value) {
        this.$emit('change', { value: value, type: 'file', name: this.$attrs['data-__field'].name })
      }
    }
  },
  methods: {
    handleUpload (info) {
      this.handleFile(info, 'file')
    },
    customRequest (e) {
      http.upload(e.action, { file: e.file })
    }
  }
}
</script>
<style scoped>
  .avatar-uploader > .ant-upload, .avatar-uploader > .ant-upload img {
    width: 100%;
    height: 100%;
  }
  .ant-upload-select-picture-card i {
    font-size: 32px;
    color: #999;
  }

  .ant-upload-select-picture-card .ant-upload-text {
    margin-top: 8px;
    color: #666;
  }
</style>
