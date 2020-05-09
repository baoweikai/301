import { http, filters } from '@/utils'
import pick from 'lodash.pick'

export default {
  data () {
    return {
      list: [],
      tableBinds: () => {
        return {
          columns: this.cols(),
          rowKey: record => record.id,
          dataSource: this.list,
          pagination: this.pagination,
          loading: this.loading
        }
      },
      modalName: '',
      isPage: true, // 列表是否分页
      pagination: { pageSize: 10, pageSizeOptions: ['10', '20', '50', '100'], total: 0 },
      selectedRowKeys: [],
      queryParams: {},
      selectedRows: [],
      options: {
        alert: { show: true, clear: () => { this.selectedRowKeys = [] } },
        rowSelection: {
          selectedRowKeys: this.selectedRowKeys,
          onChange: this.onSelectChange
        }
      },
      optionAlertShow: false
    }
  },
  filters,
  mounted () {
    this.tableOption()
    this.fetch()
  },
  updated () {
    this.loading = false
  },
  methods: {
    pick,
    cols () {
      const res = []
      const cols = pick(this.columns, this.listItems)
      for (const i in cols) {
        const col = { title: cols[i].title, dataIndex: i }
        // 自定义渲染
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
      this.queryParams = params
      this.fetch(params)
    },
    fetch (params = {}) {
      this.loading = true

      http.index(this.controller, params)
        .then(result => {
          this.loading = false
          this.isPage && (this.pagination.total = result.total)
          this.list = result.list
        })
    },
    action (act, id) {
      if (act === 'edit') {
        this.modalName = 'editModal'
      } else if (act === 'view') {
        this.modalName = 'viewModal'
        // this.$router.push('/' + this.controller + '/' + act + '/' + id)
      } else {
        this[act](id)
      }
    },
    del (id) {
      this.$confirm({
        title: '你确认要删除这条记录?',
        content: 'Are you sure you want to delete this record?',
        okText: '确定',
        okType: 'danger',
        cancelText: 'No',
        onOk () {
          http.delete(this.controller, id).then(res => {
            if (res.status === 200) {
              this.fetch(this.queryParams)
            } else {
              this.$message.error(res.message)
            }
          })
        }
      })
    },
    state (id, post = {}) {
      this.$confirm({
        title: '你确认要修改状态吗?',
        content: 'Are you sure you want to change the status?',
        okText: '确定',
        okType: 'danger',
        cancelText: 'No',
        onOk () {
          http.patch(this.controller, id, post)
            .then(res => {
              if (res.status === 200) {
                this.fetch(this.queryParams)
              } else {
                this.$message.error(res.message)
              }
            })
        }
      })
    },
    tableChange (pagination, filter, sorter) {
      const pager = { ...this.pagination }
      pager.current = pagination.current
      this.pagination = pager

      this.fetch({
        pageSize: pagination.pageSize,
        page: pagination.current,
        // sortField: sorter.field,
        // sortOrder: sorter.order,
        ...filter,
        ...this.queryParams
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
    }
  }
}
