<template>
  <div>
    <div slot="pageMenu">
      <div class="page-menu-search" v-if="search">
        <a-input-search
          style="width: 80%; max-width: 522px;"
          placeholder="请输入..."
          size="large"
          enterButton="搜索"
        />
      </div>
      <div class="page-menu-tabs" v-if="tabs && tabs.items">
        <!-- @change="callback" :activeKey="activeKey" -->
        <a-tabs :tabBarStyle="{margin: 0}" :activeKey="tabs.active()" @change="tabs.callback">
          <a-tab-pane v-for="item in tabs.items" :tab="item.title" :key="item.key"></a-tab-pane>
        </a-tabs>
      </div>
    </div>

    <div class="f fjb bg-white py3">
      <breadcrumb />
      <div class="jfe">
      </div>
    </div>
    <slot>
      <!-- keep-alive  -->
      <keep-alive v-if="multiTab">
        <router-view ref="content" />
      </keep-alive>
      <router-view v-else ref="content" />
    </slot>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import Breadcrumb from './part/Breadcrumb'

export default {
  components: { Breadcrumb },
  props: {
    avatar: {
      type: String,
      default: null
    },
    title: {
      type: [String, Boolean],
      default: true
    },
    logo: {
      type: String,
      default: null
    }
  },
  data () {
    return {
      pageTitle: null,
      description: null,
      linkList: [],
      extraImage: '',
      search: false,
      tabs: {}
    }
  },
  computed: {
    ...mapState({
      multiTab: state => state.app.multiTab
    })
  },
  mounted () {
    this.getPageMeta()
  },
  updated () {
    this.getPageMeta()
  },
  methods: {
    getPageMeta () {
      // eslint-disable-next-line
      this.pageTitle = (typeof(this.title) === 'string' || !this.title) ? this.title : this.$route.meta.title

      const content = this.$refs.content
      if (content) {
        if (content.pageMeta) {
          Object.assign(this, content.pageMeta)
        } else {
          this.description = content.description
          this.linkList = content.linkList
          this.extraImage = content.extraImage
          this.search = content.search === true
          this.tabs = content.tabs
        }
      }
    }
  }
}
</script>

<style lang="less" scoped>
</style>
