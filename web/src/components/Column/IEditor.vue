<template>
  <div>
    <textarea class="editor" v-model="content" />
  </div>
</template>
<script>
import { http } from '@/utils'
// import { api } from '@/config'
import tinymce from 'tinymce/tinymce'
import 'tinymce/langs/zh_CN' // 语言文件
import 'tinymce/themes/silver/theme'
import 'tinymce/plugins/image'
import 'tinymce/plugins/axupimgs'
import 'tinymce/plugins/link'
import 'tinymce/plugins/lists'
import 'tinymce/plugins/code'
import 'tinymce/plugins/table'
import 'tinymce/plugins/wordcount'
import 'tinymce/plugins/paste' // 支持图片粘贴
import 'tinymce/plugins/indent2em' // 首航缩进

const options = {
  selector: 'textarea.editor',
  language: 'zh_CN', // 语言
  skin: 'oxide',
  skin_url: '/css',
  height: 600, // 编辑器高度
  branding: false, // 是否启用“Powered by TinyMCE”
  menubar: false, // 顶部菜单栏显示
  // inline: true,
  plugins: 'link lists image axupimgs code table wordcount paste indent2em',
  toolbar: 'indent2em bold italic underline strikethrough | fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | undo redo | link unlink axupimgs code | removeformat',
  a11y_advanced_options: true,
  a11ychecker_allow_decorative_images: true,
  images_upload_base_path: location.protocol + '//' + location.host,
  images_upload_credentials: false,
  typeahead_urls: false,
  images_upload_handler: (blobInfo, success, failure) => {
    http.upload('file/upload', { file: blobInfo.blob() }).then(res => {
      success(res)
    }).catch(res => {
      failure('error')
    })
  } // 图片上传回调
}

export default {
  data () {
    return {
    }
  },
  computed: {
    content () {
      if (this.$attrs['data-__field'].value) {
        tinymce.activeEditor.setContent(this.$attrs['data-__field'].value)
      }
      return this.$attrs['data-__field'].value
    }
  },
  mounted () {
    tinymce.init({
      ...options,
      setup: editor => editor.on('change', () => {
        // 更新表单内容文本
        this.$emit('change', editor.getContent())
      })
    })
  },
  beforeDestroy () {
    tinymce.remove()
  },
  // 如果需要手动控制数据同步，父组件需要显式地处理changed事件
  methods: {
  }
}
</script>
<style lang="less" scoped>
@import '~tinymce/skins/ui/oxide/skin.min.css';
@import '~tinymce/skins/ui/oxide/content.min.css';
</style>
