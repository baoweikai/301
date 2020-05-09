import http from '@/utils/http'
import filters from '@/utils/filters'

export default {
  data: {
    labelCol: {
      xs: { span: 24 },
      sm: { span: 5 }
    },
    wrapperCol: {
      xs: { span: 24 },
      sm: { span: 16 }
    },
    pk: false,
    data: {}
  },
  filters,
  computed: {
  },
  methods: {
    // 获取当前记录信息并开启编辑
    view (id) {
      this.pk = id
      http.view(this.controller, id)
        .then(res => {
          if (res.status === 200) {
            // 储存数据在store中
            this.data = res.result
          }
        })
    }
  }
}
