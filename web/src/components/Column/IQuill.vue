<template>
  <div>
    <div id="editor">{{value}}</div>
  </div>
</template>

<script>
import Quill from 'quill'

const toolbarOptions = [
  ['bold', 'italic', 'underline', 'strike'],
  // ['blockquote', 'code-block'],
  // [{ 'header': 1 }, { 'header': 2 }],
  // [{ 'list': 'ordered' }, { 'list': 'bullet' }],
  // [{ 'script': 'sub' }, { 'script': 'super' }],
  // [{ 'indent': '-1' }, { 'indent': '+1' }],
  [{ 'direction': 'rtl' }],
  // [{ 'size': ['small', false, 'large', 'huge'] }],
  // [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
  [{ 'color': [] }, { 'background': [] }],
  // [{ 'font': [] }],
  // ['clean'],
  // ['link', 'image', 'video']
  [{ 'align': [] }]
]

export default {
  name: 'IQuill',
  data  () {
    return {
      quill: null,
      options: {
        theme: 'snow',
        modules: {
          toolbar: toolbarOptions
        },
        placeholder: 'Insert text here ...'
      }
    }
  },
  // 如果你需要得到当前的editor对象来做一些事情，你可以像下面这样定义一个方法属性来获取当前的editor对象，实际上这里的$refs对应的是当前组件内所有关联了ref属性的组件元素对象
  computed: {
    value () {
      return this.$attrs['data-__field'].value
    }
  },
  mounted () {
    this.init()
  },
  // 如果需要手动控制数据同步，父组件需要显式地处理changed事件
  methods: {
    init () {
      this.quill = new Quill('#editor', this.options)
      /*
      this.quill.on('text-change', () => {
        this.$emit('change', this.quill.getContents())
      })
      */
    }
  },
  watch: {
    value (newVal) {
      this.quill.setHtml(newVal)
    }
  }
}
</script>
<style lang="less">
@import '~quill/dist/quill.snow.css';
.ql-container {
  max-height: 480px;
  overflow-y: scroll;
}
.ql-editor{
  height: auto;
}
</style>
