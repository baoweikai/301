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
          <a-card title="总视频数">
            <router-link to="/res/video?flow_status=3" slot="extra">查看</router-link>
            <p class="fs34 fc-success tac">{{succCount}}</p>
          </a-card>
        </a-col>
        <a-col class="gutter-row" :span="6">
          <a-card title="失败视频数">
            <router-link to="/res/video?flow_status=5" slot="extra">查看</router-link>
            <p class="fs34 fc-success tac">{{failCount}}</p>
          </a-card>
        </a-col>
        <a-col class="gutter-row" :span="6">
          <a-card title="当日视频数">
            <router-link to="/res/video?flow_status=5" slot="extra">查看</router-link>
            <p class="fs34 fc-success tac">{{todayCount}}</p>
          </a-card>
        </a-col>
        <a-col class="gutter-row" :span="6">
          <a-card title="视频总大小">
            <router-link to="/res/video?flow_status=5" slot="extra"></router-link>
            <p class="fs34 fc-success tac">{{(totalSum / (1024 * 1024 * 1024)).toFixed(2)}}</p>
          </a-card>
        </a-col>
      </a-row>
      <a-row :gutter="16" class="mt4">
        <a-col class="gutter-row" :span="6" v-for="(item, i) in codeStat" :key="i">
          <a-card :title="'路由' + item.route_id + '视频数'">
            <router-link to="/res/video?route_id=2" slot="extra">查看</router-link>
            <p class="fs34 fc-success tac">{{item.num}}</p>
          </a-card>
        </a-col>
      </a-row>
    </div>
  </div>
</template>

<script>
import { util, page } from '@/utils'
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
      succCount: 0,
      failCount: 0,
      todayCount: 0,
      totalSum: 0
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
    ...mapGetters(['nickname', 'welcome'])
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
