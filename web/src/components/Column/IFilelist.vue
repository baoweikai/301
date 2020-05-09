<template>
  <a-upload
    :action="action"
    :defaultFileList="fileList | galleryFilter"
    :beforeUpload="beforeUpload"
    @change="handleUpload"
  >
    <a-button>
      <a-icon type="upload" /> Upload
    </a-button>
  </a-upload>

</template>
<script>
import upload from '@/utils/mixin/upload'
import filters from '@/utils/filters'

export default {
  name: 'IFilelist',
  mixins: [upload],
  filters: {
    // 相册
    fileFilter (text) {
      return text.map((file, i) => {
        return { id: file ? file.id : null, uid: i, name: 'xxx.png', status: 'done', url: filters.image(file.url) }
      })
    }
  },
  // 如果你需要得到当前的editor对象来做一些事情，你可以像下面这样定义一个方法属性来获取当前的editor对象，实际上这里的$refs对应的是当前组件内所有关联了ref属性的组件元素对象
  computed: {
    fileList: {
      get () {
        return this.$attrs['data-__field'].value
      },
      set (value) {
        this.$emit('change', value)
      }
    }
  },
  methods: {
    handleUpload (info) {
      this.handleList(info, 'fileList')
    }
  }
}
</script>
<style scoped>
  .ant-upload.ant-upload-select-picture-card {
    display: inline-flex;
  }
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
