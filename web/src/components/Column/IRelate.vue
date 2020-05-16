<template>
  <div>
    <a-input placeholder="请点击获取数据" style="width: 200px" v-model="label" @click="visible=true" :readOnly="true" />

    <a-modal
      :closable="false"
      :centered="true"
      :visible="visible"
      @cancel="visible=false"
      :footer="null"
    >
      <a-form layout="inline" :form="form" class="mb-3" @submit="handleSearch">
        <a-form-item v-for="(attr, index) in find" :key="index" :label="'名称'">
          <a-input v-decorator="[attr]" />
        </a-form-item>
        <a-form-item>
          <a-button type="primary" icon="search" html-type="submit"></a-button>
        </a-form-item>
      </a-form>

      <a-list
        itemLayout="vertical"
        size="small"
        :pagination="pagination"
        :dataSource="list"
        :bordered="true"
      >
        <a-list-item slot="renderItem" slot-scope="item" @click="handelChoose(item)">
          <div class="fjb">
            <div>{{item.name}}</div>
            <a-icon v-show="value == item.id" type="check" class="fc-success" />
          </div>
        </a-list-item>
      </a-list>
    </a-modal>
  </div>
</template>

<script>
import { http, filters } from '@/utils'

export default {
  name: 'IRelate',
  props: ['column', 'find', 'controller', 'text'],
  data () {
    return {
      visible: false,
      list: [],
      pagination: {
        onChange: (page) => {
          this.fetch({
            pageSize: this.pagination.pageSize,
            page: page,
            ...this.queryParams
          })
        },
        size: 'small',
        pageSize: 10,
        total: 0
      },
      label: '',
      form: this.$form.createForm(this)
    }
  },
  computed: {
    // 从store读取数据
    value: {
      get () {
        return this.$attrs['data-__field'].value
      },
      set (value) {
        this.$emit('change', value)
        // this.$emit('update:data-__field', { value: value, name: this.$attrs['data-__field'].value })
      }
    }
  },
  filters,
  methods: {
    fresh (params = {}) {
      this.queryParams = params
      this.fetch(params)
    },
    fetch (params = {}) {
      this.loading = true
      http.get(this.controller + '/select', params).then(result => {
        this.loading = false
        this.pagination.total = result.total
        this.list = result.list
      })
    },
    handelChoose (item) {
      this.value = item.id
      this.label = item.name
      this.visible = false
    },
    handleSearch (e) {
      // 触发表单提交
      e.preventDefault()
      this.form.validateFields((err, values) => {
        if (!err) {
          http.get(this.controller + '/select', values).then(res => {
            this.list = res.list
          })
        }
      })
    }
  },
  watch: {
    visible: {
      handler (newVal, oldVal) {
        if (newVal) {
          this.fetch()
        }
      }
    },
    text: {
      handler (newVal, oldVal) {
        if (newVal) {
          this.label = newVal
        }
      }
    }
  }
}
</script>
<style scoped>
.ant-list-item-content { position: relative; }
.ant-list-item-content i{
  position: absolute;
  right: 10px;
}
</style>
