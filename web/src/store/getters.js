const getters = {
  device: state => state.app.device,
  theme: state => state.app.theme,
  color: state => state.app.color,
  token: state => state.me.access_token,
  avatar: state => state.me.avatar,
  nickname: state => state.me.name,
  welcome: state => state.me.welcome,
  roles: state => state.me.roles,
  userInfo: state => state.me.info, // 用户信息
  routers: state => state.me.routers, // 路由
  perms: state => state.me.perms, // 权限
  menus: state => state.me.menus, // 菜单
  multiTab: state => state.app.multiTab
}

export default getters
