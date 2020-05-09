<template>
  <div>
    <a-upload
      v-if="data"
      :action="action"
      class="flex"
      listType="picture-card"
      :fileList="fileList"
      @preview="handlePreview"
      @change="change"
    >
      <div v-if="!fileList || fileList.length < 5">
        <a-icon type="plus" />
        <div class="ant-upload-text">上传</div>
      </div>
    </a-upload>
    <a-modal :visible="previewVisible" :footer="null" @cancel="handleCancel">
      <img alt="example" style="width: 100%" :src="previewImage" />
    </a-modal>
  </div>
</template>
<script>
import upload from '@/utils/mixin/upload'
import filters from '@/utils/filters'

export default {
  name: 'IGallery',
  mixins: [upload],
  data () {
    return {
      fileList: []
    }
  },
  // 计算属性
  computed: {
    data () {
      return this.$attrs['data-__field'].value
    }
  },
  methods: {
    change ({ fileList }) {
      this.handleList(fileList)
    }
  },
  watch: {
    data (newVal, oldVal) {
      !oldVal && (this.fileList = (newVal || []).map((file, i) => {
        return { uid: -i, name: `${i}.png`, status: 'done', url: filters.image(file) }
      }))
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
