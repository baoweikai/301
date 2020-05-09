<template>
  <div class="ant-pro-multi-tab">
    <div class="ant-pro-multi-tab-wrapper">
      <a-tabs
        hideAdd
        type="editable-card"
        v-model="activeKey"
        :tabBarStyle="{ background: '#FFF', margin: 0, paddingLeft: '16px', paddingTop: '1px' }"
        @edit="onEdit">
        <a-tab-pane
          style="height: 0"
          v-for="page in panes"
          :key="page.fullPath"
          :closable="pages.length > 1"
        >
          <a-dropdown :trigger="['contextmenu']">
            <span :style="{ userSelect: 'none' }">{ page.meta.title }</span>
            <a-menu slot="overlay" @click="closeMenuClick">
              <a-menu-item key="close-that" :data-vkey="page.fullPath">关闭当前标签</a-menu-item>
              <a-menu-item key="close-right" :data-vkey="page.fullPath">关闭右侧</a-menu-item>
              <a-menu-item key="close-left" :data-vkey="page.fullPath">关闭左侧</a-menu-item>
              <a-menu-item key="close-all" :data-vkey="page.fullPath">关闭全部</a-menu-item>
            </a-menu>
          </a-dropdown>
        </a-tab-pane>
      </a-tabs>
    </div>
  </div>
</template>


<script>
export default {
  data () {
    return {
      fullPathList: [],
      pages: [],
      activeKey: '',
      newTabIndex: 0
    }
  },
  created () {
    this.pages.push(this.$route)
    this.fullPathList.push(this.$route.fullPath)
    this.selectedLastPath()
  },
  methods: {
    onEdit (targetKey, action) {
      this[action](targetKey)
    },
    remove (targetKey) {
      this.pages = this.pages.filter(page => page.fullPath !== targetKey)
      this.fullPathList = this.fullPathList.filter(path => path !== targetKey)
      // 判断当前标签是否关闭，若关闭则跳转到最后一个还存在的标签页
      if (!this.fullPathList.includes(this.activeKey)) {
        this.selectedLastPath()
      }
    },
    selectedLastPath () {
      this.activeKey = this.fullPathList[this.fullPathList.length - 1]
    },

    // content menu
    closeThat (e) {
      this.remove(e)
    },
    closeLeft (e) {
      const currentIndex = this.fullPathList.indexOf(e)
      if (currentIndex > 0) {
        this.fullPathList.forEach((item, index) => {
          if (index < currentIndex) {
            this.remove(item)
          }
        })
      } else {
        this.$message.info('左侧没有标签')
      }
    },
    closeRight (e) {
      const currentIndex = this.fullPathList.indexOf(e)
      if (currentIndex < (this.fullPathList.length - 1)) {
        this.fullPathList.forEach((item, index) => {
          if (index > currentIndex) {
            this.remove(item)
          }
        })
      } else {
        this.$message.info('右侧没有标签')
      }
    },
    closeAll (e) {
      const currentIndex = this.fullPathList.indexOf(e)
      this.fullPathList.forEach((item, index) => {
        if (index !== currentIndex) {
          this.remove(item)
        }
      })
    },
    closeMenuClick ({ key, item, domEvent }) {
      const vkey = domEvent.target.getAttribute('data-vkey')
      switch (key) {
        case 'close-right':
          this.closeRight(vkey)
          break
        case 'close-left':
          this.closeLeft(vkey)
          break
        case 'close-all':
          this.closeAll(vkey)
          break
        default:
        case 'close-that':
          this.closeThat(vkey)
          break
      }
    }
  },
  watch: {
    '$route': function (newVal) {
      this.activeKey = newVal.fullPath
      if (this.fullPathList.indexOf(newVal.fullPath) < 0) {
        this.fullPathList.push(newVal.fullPath)
        this.pages.push(newVal)
      }
    },
    activeKey: function (newPathKey) {
      this.$router.push({ path: newPathKey })
    }
  }
}
</script>

<style lang='less'>
@import 'index';

@multi-tab-prefix-cls: ~"@{ant-pro-prefix}-multi-tab";
@multi-tab-wrapper-prefix-cls: ~"@{ant-pro-prefix}-multi-tab-wrapper";

/*
.topmenu .@{multi-tab-prefix-cls} {
  max-width: 1200px;
  margin: -23px auto 24px auto;
}
*/
.@{multi-tab-prefix-cls} {
  margin: -23px -24px 24px -24px;
  background: #fff;
}

.topmenu .@{multi-tab-wrapper-prefix-cls} {
  max-width: 1200px;
  margin: 0 auto;
}

.topmenu.content-width-Fluid .@{multi-tab-wrapper-prefix-cls} {
  max-width: 100%;
  margin: 0 auto;
}

</style>
