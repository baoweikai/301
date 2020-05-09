/**
 * 项目默认配置项
 * primaryColor - 默认主题色
 * navTheme - sidebar theme ['dark', 'light'] 两种主题
 * colorWeak - 色盲模式
 * layout - 整体布局方式 ['sidemenu', 'topmenu'] 两种布局
 * fixedHeader - 固定 Header : boolean
 * fixSiderbar - 固定左侧菜单栏 ： boolean
 * autoHideHeader - 向下滚动时，隐藏 Header : boolean
 * contentWidth - 内容区布局： 流式 |  固定
 *
 * storageOptions: {} - Vue-ls 插件配置项 (localStorage/sessionStorage)
 *
 */

export default {
  Login: '/identity/login',
  Logout: '/me/logout',
  ForgePassword: '/me/forge-password',
  Signup: '/identity/signup',
  TwoStepCode: '/identity/2step-code',
  SendSms: '/account/sms',
  SendSmsErr: '/account/sms_err',
  // get my info
  UserInfo: '/me/info',
  User: '/user',
  Role: '/role',
  Service: '/service',
  Permission: '/permission',
  PermissionNoPager: '/permission/no-pager',
  OrgTree: '/org/tree',
  Upload: 'file/upload',
  Image: location.protocol + '//' + location.host
}
