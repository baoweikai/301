import { ACCESS_TOKEN, FRESH_TOKEN } from '@/store/mutation-types'
import { http, util, storage } from '@/utils'
import { asyncRouterMap } from '@/config/router'

const permList = storage.get('ACCESS_PERMS', [])
/*
* 过滤可见菜单
*/
function filterMenu (routerMap, k = '') {
  const menus = []
  routerMap.map((item, i) => {
    if (!item.hidden && (!item.meta || !item.meta.auth || permList.includes(item.meta.auth))) {
      const menu = {
        name: item.name,
        title: item.meta.title,
        icon: item.meta.icon,
        key: k + (k === '' ? '' : '.') + i
      }
      if (item.children && item.children.length > 0) {
        menu.children = filterMenu(item.children, menu.key)
      }
      menus.push(menu)
    }
  })
  return menus
}
/*
* 过滤可见路由
*/
function filterRouter (routerMap) {
  const routers = routerMap.filter(route => {
    if (!route.meta || !route.meta.auth) {
      return true
    }
    if (permList.includes(route.meta.auth)) {
      if (route.children && route.children.length > 0) {
        route.children = filterRouter(route.children)
      }
      return true
    }
    return false
  })
  return routers
}

export default {
  state: {
    access_token: storage.get(ACCESS_TOKEN, false),
    fresh_token: storage.get(FRESH_TOKEN, false),
    name: '',
    welcome: '',
    avatar: '',
    routers: filterRouter(asyncRouterMap),
    perms: storage.get('ACCESS_PERMS', []),
    menus: filterMenu(asyncRouterMap.find(item => item.path === '/').children),
    info: storage.get('ME', {})
  },
  mutations: {
    SET_INFO (state, res) {
      storage.set('ACCESS_PERMS', res.perms, 168 * 3600)
      storage.set('ME', { nickname: res.username }, 168 * 3600)
      Object.assign(state, { perms: res.perms, name: res.username })
    },
    SET_TOKEN (state, res) {
      storage.set(ACCESS_TOKEN, res.access_token, 168 * 3600)
      storage.set(FRESH_TOKEN, res.fresh_token, 720 * 3600)
      Object.assign(state, { access_token: res.access_token, fresh_token: res.fresh_token })
    }
  },
  actions: {
    // 登录
    Login ({ commit, dispatch }, res) {
      // 保存管理员权限
      commit('SET_TOKEN', res)
    },
    // 授权信息
    Auth ({ commit, state }, res) {
      if (!res.perms || res.perms.length === 0) {
        util.message('getInfo: roles must be a non-null array !', 'error')
        return false
      }
      // 重置权限
      commit('SET_INFO', res)
    },
    // 登出
    Logout ({ commit, state }) {
      storage.remove(ACCESS_TOKEN)
      console.log(storage.get(ACCESS_TOKEN))
      storage.remove('ACCESS_PERMS')

      http.get('identity/logout').then(() => {
        util.navto('/login')
      }).catch(() => {
      })
    }
  }
}
