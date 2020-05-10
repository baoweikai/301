<template>
  <div>
    <div class="py1 mt2">
      <a-button v-if="batch.includes('add')" type="primary" icon="plus" size="small" class="mr2" @click="$parent.$refs.add.visible = true">
        添加
      </a-button>
      <a-button v-if="batch.includes('del') && hasSelected" type="danger" size="small" class="mr2" @click="del(selectedRowKeys)">
        删除
      </a-button>
      <a-button v-if="batch.includes('audit')" type="primary" size="small" :disabled="!hasSelected" @click="audit(selectedRowKeys)">
        审核
      </a-button>
      <a-upload
          name="file"
          v-if="batch.includes('import')"
          accept="text/csv"
          :customRequest="customRequest"
          :fileList="[]"
          :action="controller + '/import'"
          @change="handleImport"
        >
          <a-button><a-icon type="upload" />导入</a-button>
      </a-upload>
      <span>
        <template v-if="hasSelected">
          {{`共选中 ${selectedRowKeys.length} 条`}}
        </template>
      </span>
    </div>
    <a-table
      :columns="cols()"
      :rowKey="record => record.id || record._id"
      :rowSelection="{selectedRowKeys: selectedRowKeys, onChange: onSelectChange}"
      :dataSource="list"
      :pagination="pagination"
      :loading="loading"
      size="small"
      @change="tableChange"
    >
      <span v-for="(column, index) in columns" :key="index" :slot="index" slot-scope="text, record">
        <a-badge v-if="column.type === 'state'" :status="text | state" :text="text | label(column.options)" />
        <a-tag v-else-if="column.type === 'radio'">{{ text | label(column.options) }}</a-tag>
        <template v-else-if="column.type === 'image'">
          <a-popover v-if="text !== ''">
            <template slot="content">
              <a-avatar shape="square" :size="64" :src="text | image" />
            </template>
            <a-icon type="eye" :style="{ color: 'green' }" @click="preview(text)" />
          </a-popover>
          <a-icon v-else type="fund" :style="{ color: 'gray' }" />
        </template>
        <template v-else-if="column.type === 'duration'">
          {{ text | duration(column.format) }}
        </template>
        <template v-else-if="column.type === 'date'">
          {{ text | date(column.format) }}
        </template>
        <template v-else-if="column.type === 'checkbox'">
          <a-tag v-for="(tag, i) in text.map(v=>column.options[v.tag_id])" :key="i">{{ tag }}</a-tag>
        </template>
        <template v-else-if="column.type === 'tag'">
          <a-tag  v-for="(tag, i) in text" :key="i">{{ tag.name }}</a-tag>
        </template>
        <template v-else-if="column.type === 'action'">
          <a-tag v-for="(act, i) in column.actions" :key="i" :color="colors[act] || ''" v-show="actShow(act, record)" @click="action(act, record.id || record._id, record)">{{ $t('action.' + act) }}</a-tag>
        </template>
      </span>
    </a-table>
  </div>
</template>

<script>
import { http, filters } from '@/utils'

export default {
  props: {
    controller: { type: String, default: '' },
    columns: { type: Object, default: () => ({}) },
    data: { type: Array, default: () => [] },
    queryParams: { type: Object, default: () => ({}) },
    batch: { type: Array, default: () => ['add'] }
  },
  data () {
    return {
      list: [],
      loading: false,
      isPage: true, // 列表是否分页
      pagination: { showTotal: total => `共 ${total} 条`, defaultPageSize: 10, pageSizeOptions: ['10', '20', '50', '100'], showSizeChanger: true, total: 0 },
      selectedRowKeys: [],
      selectedRows: [],
      options: {
        alert: { show: true, clear: () => { this.selectedRowKeys = [] } },
        rowSelection: {
          selectedRowKeys: this.selectedRowKeys,
          onChange: this.onSelectChange
        }
      },
      colors: { del: 'red', preview: 'green', edit: 'blue' },
      optionAlertShow: false,
      uploading: null
    }
  },
  computed: {
    hasSelected () {
      return this.selectedRowKeys.length > 0
    }
  },
  filters,
  mounted () {
    this.tableOption()
    this.fetch()
  },
  methods: {
    actShow (act, record) {
      if (act === 'audit') {
        return record.exame_status < 1
      }
      return true
    },
    cols () {
      const res = []
      const cols = this.columns
      for (const i in cols) {
        const col = { title: cols[i].title, dataIndex: i }
        // 自定义渲染
        if (cols[i].options) {
          col.filters = filters.filters(cols[i].options)
        }
        if (cols[i].ellipsis) {
          col.ellipsis = cols[i].ellipsis
        }
        if (cols[i].customRender) {
          col.customRender = cols[i].customRender
        } else if (['image', 'date', 'state', 'radio', 'checkbox', 'select', 'duration', 'tag', 'action'].includes(cols[i].type)) {
          col.scopedSlots = { customRender: i }
        }
        res.push(col)
      }
      return res
    },
    fresh (params = {}) {
      // this.queryParams = params
      this.fetch(params)
    },
    fetch (params = {}) {
      this.loading = true

      http.index(this.controller, Object.assign(this.queryParams, params))
        .then(result => {
          this.loading = false
          for (const i in result.columns) {
            Object.assign(this.$parent.columns[i], result.columns[i])
          }
          this.isPage && (this.pagination.total = result.total)
          this.list = result.list.map(row => Object.assign(row, { key: `${row.id}` }))
        })
    },
    action (act, id, record) {
      if (['del', 'state', 'audit'].includes(act)) {
        this[act]([id])
      } else {
        this.$parent[act](id, record)
      }
    },
    del (ids) {
      const _this = this
      this.$confirm({
        title: this.$t('oprate.affirm', { act: this.$t('action.del') }),
        centered: true,
        okText: this.$t('action.ok'),
        okType: 'danger',
        cancelText: this.$t('action.cancel'),
        onOk () {
          http.del(_this.controller, ids).then(res => {
            if (res) {
              _this.$message.success('删除成功')
              _this.fetch(_this.queryParams)
            }
          })
        }
      })
    },
    audit (ids, post = {}) {
      const _this = this
      this.$confirm({
        title: this.$t('oprate.affirm', { act: this.$t('action.audit') }),
        maskClosable: true,
        centered: true,
        okText: this.$t('action.adopt'),
        okType: 'primary',
        cancelType: 'danger',
        cancelText: this.$t('action.reject'),
        onOk () {
          http.post(_this.controller + '/adopt', ids, post).then(res => {
            if (res) {
              _this.$message.success('修改成功')
              _this.fetch(_this.queryParams)
            }
          })
        },
        onCancel () {
          http.post(_this.controller + '/reject', ids, post).then(res => {
            if (res) {
              _this.$message.success('修改成功')
              _this.fetch(_this.queryParams)
            }
          })
        }
      })
    },
    state (ids, post = {}) {
      const _this = this
      this.$confirm({
        title: this.$t('oprate.affirm', { act: this.$t('action.state') }),
        // content: 'Are you sure you want to change the status?',
        okText: this.$t('action.ok'),
        okType: 'danger',
        centered: true,
        cancelText: this.$t('action.cancel'),
        onOk () {
          http.post(_this.controller + '/state', ids, post).then(res => {
            if (res) {
              _this.$message.success('修改成功')
              _this.fetch(_this.queryParams)
            }
          })
        }
      })
    },
    // 上传自定义请求
    customRequest (e) {
      http.upload(e.action, { file: e.file })
    },
    handleImport (info) {
      if (info.file.status === 'uploading') {
        this.$message.loading({ content: '导入中...', key: 'uploading' })
      } else {
        if (info.file.status === 'done') {
          this.$message.success({ content: '导入成功', key: 'uploading' })
          this.fresh()
        } else {
          this.$message.error({ content: info.file.message, key: 'uploading' })
        }
      }
    },
    tableChange (pagination, filter, sorter) {
      const pager = { ...this.pagination, current: pagination.current }
      this.pagination = pager
      this.fetch({
        ...filter,
        ...this.queryParams,
        pageSize: pagination.pageSize,
        page: pagination.current
        // sortField: sorter.field,
        // sortOrder: sorter.order
      })
    },
    tableOption () {
      if (!this.optionAlertShow) {
        this.options = {
          alert: { show: true, clear: () => { this.selectedRowKeys = [] } },
          rowSelection: {
            selectedRowKeys: this.selectedRowKeys,
            onChange: this.onSelectChange
          }
        }
        this.optionAlertShow = true
      } else {
        this.options = {
          alert: false,
          rowSelection: null
        }
        this.optionAlertShow = false
      }
    },
    onSelectChange (selectedRowKeys, selectedRows) {
      this.selectedRowKeys = selectedRowKeys
      this.selectedRows = selectedRows
    },
    preview (text) {
      text = filters.image(text)
      this.$info({
        title: '',
        centered: true,
        maskClosable: true,
        footer: null,
        width: '70vw',
        content: (
          <img src={text} style="width: 80vw;" />
        )
      })
    }
  },
  watch: {
    queryParams () {
      this.fetch()
    }
  }
}
</script>
<style>
.ant-table-column-title{ white-space: nowrap; }
</style>
