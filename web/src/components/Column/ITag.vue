<template>
  <div>
    <a-input
      v-if="inputVisible"
      ref="input"
      type="text"
      :style="{ width: '78px' }"
      :value="inputValue"
      @change="change"
      @blur="confirm"
      @keyup.enter="confirm"
    />
    <a-tag v-else @click="add" style="background: #fff; borderStyle: dashed;">
      <a-icon type="plus" />添加
    </a-tag>
    <draggable v-model="data" draggable=".drag-item" :move="move" style="display: inline-block;">
      <span v-for="(tag, index) in data" :key="index" class="drag-item">
        <a-tooltip v-if="tag.name.length > 20" :title="tag.name">
          <a-tag @click="edit(index)">
            {{`${tag.name.slice(0, 20)}...`}}
          </a-tag>
        </a-tooltip>
        <a-tag @click="edit(index)" v-else>
          {{ tag.name }}
        </a-tag>
      </span>
    </draggable>
    <span class="inline-flex" style="width: 24px;"></span>
    <a-modal :centered="true" :closable="false" width="auto" cancelText="删除" okText="修改" :visible="visible" @cancel="close" @ok="update">
      <a-input v-model="value" style="width: 200px;" />
    </a-modal>
  </div>
</template>
<script>
import draggable from 'vuedraggable'

export default {
  name: 'ITag',
  data () {
    return {
      inputVisible: false,
      inputValue: '',
      visible: false, // 编辑窗可见
      index: 0, // 当前编辑项
      value: '' // 编辑窗的值
    }
  },
  components: {
    draggable
  },
  created () {
  },
  computed: {
    data: {
      get () {
        return this.$attrs['data-__field'].value || []
      },
      set (value) {
        this.$emit('change', value)
      }
    }
  },
  methods: {
    close () {
      this.visible = false
      this.data = this.data.filter((item, i) => this.index !== i) // 找出所有大于80的元素
    },
    add () {
      this.inputVisible = true
      this.$nextTick(() => {
        this.$refs.input.focus()
      })
    },
    update () {
      this.data[this.index].name = this.value
      this.visible = false
    },
    edit (i) {
      this.visible = true
      this.index = i
      this.value = this.data[i].name
    },
    confirm () {
      const inputValue = this.inputValue
      const data = typeof (this.data) === 'object' && this.data !== null ? this.data : []

      // 标签中是否已经包含当前值
      const include = data.some(row => row.name === inputValue)

      if (inputValue && !include) {
        data.push({ name: inputValue, serial: data.length + 1 })
      }

      this.data = data
      Object.assign(this, {
        inputVisible: false,
        inputValue: ''
      })
    },
    change (e) {
      this.inputValue = e.target.value
    },
    // 移动时调换序号
    move ({ relatedContext, draggedContext }) {
      const related = relatedContext.element
      const dragged = draggedContext.element
      this.data[relatedContext.index].serial = dragged.serial
      this.data[draggedContext.index].serial = related.serial
      this.data = this.data
    }
  }
}
</script>

<style lang="less" scoped>
  .ant-tag{ line-height: 30px; height: 32px; }
</style>
