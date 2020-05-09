import Vue from 'vue'
import { http } from '@/utils'

const list = {
  namespaced: true,
  state () {
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
      acts: ['edit', 'del'],
      pagination: { pageSize: 10, pageSizeOptions: ['10', '20', '50', '100'], total: 0 },
      selectedRowKeys: [],
      queryParams: {},
      selectedRows: [],
      options: {
        // alert: { show: true, clear: () => { this.selectedRowKeys = [] } },
        rowSelection: {
          // selectedRowKeys: this.selectedRowKeys,
          // onChange: this.onSelectChange
        }
      },
      optionAlertShow: false
    }
  },
  mutations: {
    UPDATE: (state, values) => {
      for (const index in values) {
        Vue.set(state, index, values[index])
      }
    }
  },
  actions: {
    fresh ({ commit, state }, params = {}) {
      state.queryParams = params
      this.fetch(params)
    },
    fetch ({ commit, state }, params = {}) {
      this.loading = true
      console.log(this.controller)
      http.index(this.controller, params)
        .then(({ result }) => {
          this.loading = false
          state.pagination.total = result.total
          state.list = result.list
        })
    },
    action ({ commit, state }, act, id) {
      if (act === 'edit') {
        this.modalName = 'editModal'
      } else if (act === 'view') {
        this.modalName = 'viewModal'
        // state.$router.push('/' + this.controller + '/' + act + '/' + id)
      }
      this[act](id)
    },
    delete ({ commit, state }, id) {
      state.$confirm({
        title: '你确认要删除这条记录?',
        content: 'Are you sure you want to delete this record?',
        okText: '确定',
        okType: 'danger',
        cancelText: 'No',
        onOk () {
          http.delete(this.controller, id).then(res => {
            if (res.status === 200) {
              this.fetch(state.queryParams)
            } else {
              this.$message.error(res.message)
            }
          })
        }
      })
    },
    state ({ commit, state }, id, post = {}) {
      state.$confirm({
        title: '你确认要修改状态吗?',
        content: 'Are you sure you want to change the status?',
        okText: '确定',
        okType: 'danger',
        cancelText: 'No',
        onOk () {
          http.patch(this.controller, id, post)
            .then(res => {
              if (res.status === 200) {
                this.fetch(state.queryParams)
              } else {
                this.$message.error(res.message)
              }
            })
        }
      })
    },
    tableChange ({ commit, state }, pagination, filter, sorter) {
      const pager = { ...state.pagination }
      pager.current = pagination.current
      state.pagination = pager

      this.fetch({
        pageSize: pagination.pageSize,
        page: pagination.current,
        // sortField: sorter.field,
        // sortOrder: sorter.order,
        ...filter,
        ...state.queryParams
      })
    },
    tableOption ({ commit, state }) {
      if (!state.optionAlertShow) {
        state.options = {
          alert: { show: true, clear: () => { state.selectedRowKeys = [] } },
          rowSelection: {
            selectedRowKeys: state.selectedRowKeys,
            onChange: state.onSelectChange
          }
        }
        state.optionAlertShow = true
      } else {
        state.options = {
          alert: false,
          rowSelection: null
        }
        state.optionAlertShow = false
      }
    },
    onSelectChange ({ commit, state }, selectedRowKeys, selectedRows) {
      state.selectedRowKeys = selectedRowKeys
      state.selectedRows = selectedRows
    }
  }
}

export default list
