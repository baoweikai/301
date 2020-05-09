<template>
  <a-menu
    mode="horizontal"
    theme="dark"
  >
    <a-menu-item key="0">
      <router-link :to="{ name: 'HelpIndex' }">
        <a-icon type="question-circle-o"/>
      </router-link>
    </a-menu-item>
    <a-sub-menu>
      <span slot="title" class="submenu-title-wrapper">
        <a-avatar class="avatar" /><span>{{ nickname() }}</span>
      </span>
      <a-menu-item key="0">
        <router-link to="/profile">
          <a-icon type="user"/>
          <span>个人设置</span>
        </router-link>
      </a-menu-item>
      <a-menu-item key="1">
        <router-link to="/password">
          <a-icon type="setting"/>
          <span>重置密码</span>
        </router-link>
      </a-menu-item>
      <a-menu-divider/>
      <a-menu-item key="3" @click="handleLogout">
        <a href="javascript:;">
          <a-icon type="logout"/>
          <span>退出登录</span>
        </a>
      </a-menu-item>
    </a-sub-menu>
  </a-menu>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  methods: {
    ...mapActions(['Logout']),
    ...mapGetters(['nickname', 'avatar']),
    handleLogout () {
      const that = this

      this.$confirm({
        title: '提示',
        content: '真的要注销登录吗 ?',
        onOk () {
          return that.Logout({}).then(() => {
            window.location.reload()
          }).catch(err => {
            that.$message.error({
              title: '错误',
              description: err.message
            })
          })
        },
        onCancel () {
        }
      })
    }
  }
}
</script>
