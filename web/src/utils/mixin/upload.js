import api from '@/config/api'

export default {
  data () {
    return {
      upload: api.Upload,
      previewVisible: false,
      previewImage: '',
      loading: 0
    }
  },
  props: {
    type: { type: String, 'default': 'image' },
    format: { type: String, default: 'jpg, png, gif, jpeg' },
    size: { type: Number, default: 2000 * 1024 },
    action: { type: String, default: api.Upload }
  },
  methods: {
    getBase64 (img, callback) {
      const reader = new FileReader()
      reader.addEventListener('load', () => callback(reader.result))
      reader.readAsDataURL(img)
    },
    handleFile (info, type) {
      if (info.file.status === 'uploading') {
        this.loading = true
      }
    },
    handleList (fileList) {
      this.fileList = fileList
      const data = []
      const cpmplete = fileList.every(v => {
        if (v.status === 'done') {
          data.push(v.response ? v.response.result : v.url)
          return true
        }
        return false
      })
      cpmplete && this.$emit('change', data)
    },
    beforeUpload (file) {
      if (['video/mp4', 'image/png', 'image/jpeg', 'image/gif'].indexOf(file.type) < 0) {
        this.$message.error('You can only upload ' + this.format + ' file!')
        return false
      }

      if (file.size / 1024 > this.size) {
        this.$message.error('File must smaller than ' + this.size + 'MB!')
        return false
      }
      return true
    },
    handlePreview (file) {
      this.previewImage = file.url || file.thumbUrl
      this.previewVisible = true
    },
    handleCancel () {
      this.previewVisible = false
    }
  }
}
