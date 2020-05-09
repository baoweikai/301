<template>
  <a-popover
    v-model="visible"
    trigger="click"
    placement="bottomRight"
    overlayClassName="header-notice-wrapper"
    :autoAdjustOverflow="true"
    :arrowPointAtCenter="true"
    :overlayStyle="{ width: '300px', top: '50px' }"
  >
    <template slot="content">
      <a-spin :spinning="loadding">
        <a-tabs v-if="data" defaultActiveKey="0" @change="callback">
          <a-tab-pane tab="通知" key="0" >
            <a-list>
              <a-list-item v-for="item in data['data']">
                <a-list-item-meta :title="item.title" :description="item.create_at">
                  <a-avatar style="background-color: white" slot="avatar" src="https://gw.alipayobjects.com/zos/rmsportal/ThXAXghbEsBCCSDihZxY.png"/>
                </a-list-item-meta>
              </a-list-item>
            </a-list>
          </a-tab-pane>
          <a-tab-pane tab="消息" key="1">
            <a-list>
              <a-list-item v-for="item in data['data']">
                <a-list-item-meta :title="item.title" :description="item.addtime">
                  <a-avatar style="background-color: white" slot="avatar" src="https://gw.alipayobjects.com/zos/rmsportal/ThXAXghbEsBCCSDihZxY.png"/>
                </a-list-item-meta>
              </a-list-item>
            </a-list>
          </a-tab-pane>
          <a-tab-pane tab="待办" key="2">
            <a-list>
              <a-list-item v-for="item in data['data']">
                <a-list-item-meta :title="item.title" :description="item.addtime">
                  <a-avatar style="background-color: white" slot="avatar" src="https://gw.alipayobjects.com/zos/rmsportal/ThXAXghbEsBCCSDihZxY.png"/>
                </a-list-item-meta>
              </a-list-item>
            </a-list>
          </a-tab-pane>
        </a-tabs>
      </a-spin>
    </template>
    <span @click="fetch" class="header-notice">
      <a-badge :count="noticeCount">
        <a-icon style="font-size: 16px; padding: 4px" type="bell" />
      </a-badge>
    </span>
  </a-popover>
</template>

<script>
import { mapState } from 'vuex'
import manage from '@/utils/http'
export default {
  name: 'HeaderNotice',

  data () {
    return {
      loadding: false,
      visible: false,
      controller: 'message',
      noticeCount: {},
      data: []
    }
  },
  created () {
    this.noticeCount = this.userInfo.noticeCount
  },
  computed: {
    ...mapState({
      userInfo: state => {
        return state.user.info
      }
    })
  },
  methods: {
    callback(key){
      this.fetch(key)
    },
    fetch (key) {
      if (!this.visible || key) {
        this.loadding = true
        const k = typeof (key) === 'string' ? key : 0
        const path = '/index/message/index?type=' + k
        manage.get (path, {})
          .then(res => {
            if (res.status === 200) {
              this.data = res.result
            } else {}
          }).catch (() => {
            // Do something
          })
        this.loadding = false
      } else {
        this.loadding = false
      }
    }
  }
}
</script>

<style lang="css">
  .header-notice-wrapper {
    top: 50px !important;
  }
</style>
<style lang="less" scoped>
  .header-notice{
    display: inline-block;
    transition: all 0.3s;

    span {
      vertical-align: initial;
    }
  }
</style>
