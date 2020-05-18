<template>
  <div style="background-color: rgb(236, 236, 236); padding: 20px; margin: 0 -15px;">
    <div slot="headerContent">
      <div class="title py4">{{ timeFix }}<span class="welcome-text">，{{ welcome() }}</span></div>
    </div>
    <div slot="extra">
      <a-row class="more-info">
      </a-row>
    </div>
    <div>
      <a-row :gutter="16">
        <a-col class="gutter-row" :span="6">
          <a-card title="今日">
            <a slot="extra" @click="stat()">更新</a>
            <div class="fs16 tac">
              <a-row>
                <a-col :span="12">IP</a-col><a-col class="fc-success" :span="12">{{today[0]}}</a-col>
              </a-row>
              <a-row>
                <a-col :span="12">跳转</a-col><a-col class="fc-success" :span="12">{{today[1]}}</a-col>
              </a-row>
              <a-row>
                <a-col :span="12">引量</a-col><a-col class="fc-success" :span="12">{{today[2]}}</a-col>
              </a-row>
            </div>
          </a-card>
        </a-col>
        <a-col class="gutter-row" :span="6">
          <a-card title="昨日">
            <a @click="stat(1)" slot="extra">更新</a>
            <div class="fs16 tac">
              <a-row>
                <a-col :span="12">IP</a-col><a-col class="fc-success" :span="12">{{yesterday[0]}}</a-col>
              </a-row>
              <a-row>
                <a-col :span="12">跳转</a-col><a-col class="fc-success" :span="12">{{yesterday[1]}}</a-col>
              </a-row>
              <a-row>
                <a-col :span="12">引量</a-col><a-col class="fc-success" :span="12">{{yesterday[2]}}</a-col>
              </a-row>
            </div>
          </a-card>
        </a-col>
        <a-col class="gutter-row" :span="6">
          <a-card title="一周">
            <div class="fs16 tac">
              <a-row>
                <a-col :span="12">IP</a-col><a-col class="fc-success" :span="12">{{sevenday[0]}}</a-col>
              </a-row>
              <a-row>
                <a-col :span="12">跳转</a-col><a-col class="fc-success" :span="12">{{sevenday[1]}}</a-col>
              </a-row>
              <a-row>
                <a-col :span="12">引量</a-col><a-col class="fc-success" :span="12">{{sevenday[2]}}</a-col>
              </a-row>
            </div>
          </a-card>
        </a-col>
        <a-col class="gutter-row" :span="6">
          <a-card title="域名数">
            <div to="/res/video?flow_status=5" slot="extra"></div>
            <p class="fs34 fc-success tac">{{domainCount}}</p>
          </a-card>
        </a-col>
      </a-row>
    </div>
  </div>
</template>

<script>
import { util, page, http } from '@/utils'
import { mapGetters } from 'vuex'

export default {
  mixins: [page],
  components: {
  },
  data () {
    return {
      timeFix: util.timeFix(),
      avatar: '',
      user: {},

      codeStat: [],
      today: [0, 0, 0],
      yesterday: [0, 0, 0],
      sevenday: [0, 0, 0],
      domainCount: 0
    }
  },
  computed: {
    userInfo () {
      return this.$store.getters.userInfo
    }
  },
  created () {
    this.user = this.userInfo
    this.avatar = this.userInfo.avatar
  },
  mounted () {
    this.load('index/workplace')
  },
  methods: {
    ...mapGetters(['nickname', 'welcome']),
    stat (type = 0) {
      http.post('stat/frush', { type: type }).then(res => {
        if (res) {
          this.$message.success('刷新成功')
          Object.assign(this, res)
        }
      })
    }
  }
}
</script>

<style lang="less" scoped>
  .project-list {

    .card-title {
      font-size: 0;

      a {
        color: rgba(0, 0, 0, 0.85);
        margin-left: 12px;
        line-height: 24px;
        height: 24px;
        display: inline-block;
        vertical-align: top;
        font-size: 14px;

        &:hover {
          color: #1890ff;
        }
      }
    }
    .card-description {
      color: rgba(0, 0, 0, 0.45);
      height: 44px;
      line-height: 22px;
      overflow: hidden;
    }
    .project-item {
      display: flex;
      margin-top: 8px;
      overflow: hidden;
      font-size: 12px;
      height: 20px;
      line-height: 20px;
      a {
        color: rgba(0, 0, 0, 0.45);
        display: inline-block;
        flex: 1 1 0;

        &:hover {
          color: #1890ff;
        }
      }
      .datetime {
        color: rgba(0, 0, 0, 0.25);
        flex: 0 0 auto;
        float: right;
      }
    }
    .ant-card-meta-description {
      color: rgba(0, 0, 0, 0.45);
      height: 44px;
      line-height: 22px;
      overflow: hidden;
    }
  }

  .item-group {
    padding: 20px 0 8px 24px;
    font-size: 0;
    a {
      color: rgba(0, 0, 0, 0.65);
      display: inline-block;
      font-size: 14px;
      margin-bottom: 13px;
      width: 25%;
    }
  }

  .members {
    a {
      display: block;
      margin: 12px 0;
      line-height: 24px;
      height: 24px;
      .member {
        font-size: 14px;
        color: rgba(0, 0, 0, .65);
        line-height: 24px;
        max-width: 100px;
        vertical-align: top;
        margin-left: 12px;
        transition: all 0.3s;
        display: inline-block;
      }
      &:hover {
        span {
          color: #1890ff;
        }
      }
    }
  }

  .mobile {

    .project-list {

      .project-card-grid {
        width: 100%;
      }
    }

    .more-info {
      border: 0;
      padding-top: 16px;
      margin: 16px 0 16px;
    }

    .headerContent .title .welcome-text {
      display: none;
    }
  }

</style>
