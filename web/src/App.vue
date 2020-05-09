<template>
  <a-config-provider :locale="locale" id="app">
    <router-view/>
  </a-config-provider>
</template>

<script>
import zhCN from 'ant-design-vue/lib/locale/zh_CN'
import moment from 'moment'
import 'moment/locale/zh-cn'
import { AppDeviceEnquire } from '@/utils/mixin/app'

moment.locale('zh-cn')

export default {
  name: 'app',
  mixins: [AppDeviceEnquire],
  components: { },
  data () {
    return {
      locale: zhCN
    }
  }
}
</script>

<style lang="less">
@import '~ant-design-vue/dist/antd.css';
@import './index.less';

#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
</style>
