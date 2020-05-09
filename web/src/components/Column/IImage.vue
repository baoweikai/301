<template>
  <div>
    <a-upload
      listType="picture-card"
      class="avatar-uploader inline-flex"
      :showUploadList="false"
      :action="action"
      :customRequest="customRequest"
      :beforeUpload="beforeUpload"
      @change="handleUpload"
    >
      <img v-if="file" :src="file | image" alt="avatar" />
      <div v-else>
        <a-icon :type="loading ? 'loading' : 'plus'" />
        <div class="ant-upload-text">上传</div>
      </div>
    </a-upload>
    <div style="width: 24px; display: inline-flex;"></div>
  </div>
</template>
<script>
import upload from '@/utils/mixin/upload'
import { http, filters } from '@/utils'

export default {
  name: 'IImage',
  mixins: [upload],
  // 如果你需要得到当前的editor对象来做一些事情，你可以像下面这样定义一个方法属性来获取当前的editor对象，实际上这里的$refs对应的是当前组件内所有关联了ref属性的组件元素对象
  computed: {
    file: {
      get () {
        return this.$attrs['data-__field'].value
      },
      set (value) {
        this.$emit('change', value)
      }
    }
  },
  filters,
  methods: {
    handleUpload (info) {
      this.handleFile(info, 'image')
    },
    customRequest (e) {
      const _this = this
      http.upload(e.action, { file: e.file }).then(res => {
        _this.loading = false
        // console.log(res)
        _this.file = res
      })
    }
  }
}
</script>
<style scoped>
  .avatar-uploader .ant-upload {
    width: 104px;
    height: 104px;
  }
  .avatar-uploader .ant-upload img {
    width: 100%;
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
