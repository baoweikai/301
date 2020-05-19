import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import {
  util,
  http
} from '@/utils'
import {
  constRouterMap,
  defaults,
  api
} from '@/config'

Vue.use(VueRouter)

// console.log(constRouterMap.concat(store.getters.routers))
const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior: () => ({
    y: 0
  }),
  routes: constRouterMap.concat(store.getters.routers)
})

NProgress.configure({
  showSpinner: false
}) // NProgress Configuration

const whiteList = ['Login', 'Signup', 'Forget', 'Result'] // no redirect whitelist

router.beforeEach((to, from, next) => {
  NProgress.start() // start progress bar
  to.meta && to.meta.title && util.setTitle(`${to.meta.title} - ${defaults.appName}`)

  if (whiteList.includes(to.name)) {
    // 在免登录白名单，直接进入
    next()
  } else if (store.getters.token) {
    /* has token */
    if (to.path === '/login') {
      next({
        path: '/dashboard/workplace'
      })
      NProgress.done()
      return
    }
    // 如果管理员权限没保存
    if (store.getters.perms.length === 0) {
      http.get(api.UserInfo).then(res => {
        if (res.state === 200) {
          store.dispatch('Auth', res.result)
          const redirect = decodeURIComponent(from.query.redirect || to.path)
          if (to.path === redirect) {
            // hack方法 确保addRoutes已完成 ,set the replace: true so the navigation will not leave a history record
            next({ ...to,
              replace: true
            })
          } else {
            // 跳转到目的路由
            next({
              path: redirect
            })
          }
        } else {
          // 跳转到目的路由
          next({
            path: '/login'
          })
        }
      })
    } else {
      // router.addRoutes(store.getters.routers)
      next()
    }
  } else {
    next({
      path: '/login',
      query: {
        redirect: to.fullPath
      }
    })
    NProgress.done() // if current page is login will not trigger afterEach hook, so manually handle it
  }
})

router.afterEach(() => {
  NProgress.done() // finish progress bar
})

export default router
