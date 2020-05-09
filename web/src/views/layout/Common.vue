<template>
  <div class="page">
    <div class="fac">
      <router-link class="sider tac" :class="{ fold: fold }" :to="{ name:'Dashboard' }">
        <logo alt="logo" class="w4 h4" />
      </router-link>
      <div class="f1 tal">
        <a-icon :type="fold ? 'menu-unfold' : 'menu-fold'" class="fc-white" @click="fold = !fold" />
      </div>
      <user-menu />
    </div>
    <div class="f">
      <!-- 侧边栏 -->
      <div class="w26 ps t0 oh" :class="{ fold: fold }">
        <a-menu
          :mode="fold ? 'vertical' : 'inline'"
          theme="dark"
          :openKeys="openKeys"
          @openChange="onOpenChange"
        >
          <template v-for="item in menus">
            <a-menu-item v-if="!item.children" :key="item.key">
              <router-link :to="{ name: item.name }">
                <a-icon :type="item.icon" /><span>{{item.title}}</span>
              </router-link>
            </a-menu-item>
            <sub-menu v-else :menu="item" :key="item.key"/>
          </template>
        </a-menu>
      </div>
      <!-- 主视图区 -->
      <div class="f1 bg-white" style="min-height: 92vh; padding: 0 15px;">
        <transition name="page-transition">
          <router-view />
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
import util from '@/utils/util'
import { mapState, mapGetters, mapActions } from 'vuex'
import { mixin, mixinDevice } from '@/utils/mixin/app'
import config from '@/config/default'
import SubMenu from '@/components/SubMenu'
import { UserMenu } from './part'
import Logo from '@/assets/logo.svg?inline'

export default {
  mixins: [mixin, mixinDevice],
  components: { SubMenu, UserMenu, Logo },
  data () {
    return {
      title: config.appName,
      production: config.production,
      fold: false,
      collapsed: false,
      menus: [],
      rootSubmenuKeys: ['0', '1', '2', '3', '4', '5', '6', '7', '8'],
      openKeys: ['0']
    }
  },
  computed: {
    ...mapState({
      // 动态主路由
      mainMenu: state => state.me.menus
    }),
    contentPaddingLeft () {
      if (!this.fixSidebar || this.isMobile()) {
        return '0'
      }
      if (this.sidebarOpened) {
        return '256px'
      }
      return '80px'
    }
  },
  watch: {
    sidebarOpened (val) {
      this.collapsed = !val
    }
  },
  created () {
    this.menus = this.mainMenu
    this.collapsed = !this.sidebarOpened
  },
  mounted () {
    const userAgent = navigator.userAgent
    if (userAgent.indexOf('Edge') > -1) {
      this.$nextTick(() => {
        this.collapsed = !this.collapsed
        setTimeout(() => {
          this.collapsed = !this.collapsed
        }, 16)
      })
    }
  },
  methods: {
    ...mapActions(['Logout', 'setSidebar']),
    ...mapGetters(['nickname', 'avatar']),
    toggle () {
      this.collapsed = !this.collapsed
      this.setSidebar(!this.collapsed)
      util.triggerWindowResizeEvent()
    },
    paddingCalc () {
      let left = ''
      if (this.sidebarOpened) {
        left = this.isDesktop() ? '256px' : '80px'
      } else {
        left = (this.isMobile() && '0') || ((this.fixSidebar && '80px') || '0')
      }
      return left
    },
    menuSelect () {
      if (!this.isDesktop()) {
        this.collapsed = false
      }
    },
    drawerClose () {
      this.collapsed = false
    },
    handleLogout () {

    },
    onOpenChange (openKeys) {
      const latestOpenKey = openKeys.find(key => this.openKeys.indexOf(key) === -1)

      if (this.rootSubmenuKeys.includes(latestOpenKey)) {
        this.openKeys = latestOpenKey ? [latestOpenKey] : []
      } else {
        this.openKeys = openKeys
      }
    }
  }
}
</script>

<style lang="less" scoped>
  .page{ background-color: #001529; }
  .sider{ width: 260px; overflow: hidden;}
  .sider.fold{ width: 46px;}

  // 框架透明
  .ant-layout, .ant-layout-sider{ background-color: transparent; }
  .ant-layout-content{ background-color: #FFF; }
</style>
